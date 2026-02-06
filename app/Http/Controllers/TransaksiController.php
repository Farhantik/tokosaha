<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    public function index()
    {
        // Ambil produk dengan kategori
        $produk = DB::table('produk')
            ->leftJoin('produk_kategori', 'produk.id_produk_kategori', '=', 'produk_kategori.id_produk_kategori')
            ->select(
                'produk.*', 
                'produk_kategori.nama_kategori as kategori_produk'
            )
            ->orderBy('produk.nama_produk', 'asc')
            ->get();

        return view('transaksi.index', compact('produk'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'items' => 'required|array',
                'items.*.id_produk' => 'required|exists:produk,id_produk',
                'items.*.qty' => 'required|integer|min:1',
                'items.*.harga' => 'required|numeric|min:0',
                'total_bayar' => 'required|numeric|min:0',
                'total_pembayaran' => 'required|numeric|min:0',
                'status_pembayaran' => 'required|in:lunas,belum_bayar'
            ]);

            $user = Auth::user();

            // Cek kasir aktif untuk user ini
            $kasirAktif = DB::table('kasir')
                ->where('id_user', $user->id_user)
                ->whereNull('waktu_close')
                ->first();

            if (!$kasirAktif) {
                throw new \Exception('Kasir belum dibuka. Silakan buka kasir terlebih dahulu.');
            }

            // Cek stok produk
            foreach ($validated['items'] as $item) {
                $produk = DB::table('produk')->where('id_produk', $item['id_produk'])->first();
                
                if (!$produk) {
                    throw new \Exception("Produk dengan ID {$item['id_produk']} tidak ditemukan");
                }
                
                if ($produk->stock_produk < $item['qty']) {
                    throw new \Exception("Stok {$produk->nama_produk} tidak mencukupi. Tersedia: {$produk->stock_produk}");
                }
            }

            // Simpan penjualan menggunakan DB query builder
            $idPenjualan = DB::table('penjualan')->insertGetId([
                'id_kasir' => $kasirAktif->id_kasir,
                'tanggal_penjualan' => now(),
                'total_pembayaran' => $validated['total_pembayaran'],
                'total_bayar' => $validated['total_bayar'],
                'kembalian_pembayaran' => $validated['total_bayar'] - $validated['total_pembayaran'],
                'status_pembayaran' => $validated['status_pembayaran']
            ]);

            // Simpan detail penjualan dan kurangi stok
            foreach ($validated['items'] as $item) {
                // Get stok sebelum update
                $produk = DB::table('produk')->where('id_produk', $item['id_produk'])->first();
                $stokSebelum = $produk->stock_produk;
                
                // Insert detail penjualan
                DB::table('penjualan_detail')->insert([
                    'id_penjualan' => $idPenjualan,
                    'id_produk' => $item['id_produk'],
                    'qty_produk' => $item['qty'],
                    'harga_produk' => $item['harga'],
                    'subtotal_harga' => $item['qty'] * $item['harga']
                ]);

                // Kurangi stok produk
                DB::table('produk')
                    ->where('id_produk', $item['id_produk'])
                    ->decrement('stock_produk', $item['qty']);
                
                // Get stok setelah update
                $produkUpdated = DB::table('produk')->where('id_produk', $item['id_produk'])->first();
                $stokSesudah = $produkUpdated->stock_produk;

                // CATAT LOG PENJUALAN KE PRODUK_LOGS
                DB::table('produk_logs')->insert([
                    'id_produk' => $item['id_produk'],
                    'jenis_aktivitas' => 'penjualan',
                    'stok_sebelum' => $stokSebelum,
                    'stok_sesudah' => $stokSesudah,
                    'jumlah_perubahan' => -$item['qty'],
                    'harga_saat_itu' => $item['harga'],
                    'keterangan' => $validated['status_pembayaran'] === 'lunas' 
                        ? 'Penjualan produk via transaksi kasir (Tunai)'
                        : 'Penjualan produk via transaksi kasir (Piutang)',
                    'id_penjualan' => $idPenjualan,
                    'id_penerimaan' => null,
                    'user_nama' => $user->nama_user ?? $user->name,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();

            // ✅ AMBIL SETTINGS DARI DATABASE
            $settings = DB::table('settings')->first();
            
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan',
                'data' => [
                    'id_penjualan' => $idPenjualan,
                    'total_pembayaran' => $validated['total_pembayaran'],
                    'total_bayar' => $validated['total_bayar'],
                    'kembalian_pembayaran' => $validated['total_bayar'] - $validated['total_pembayaran'],
                    'status_pembayaran' => $validated['status_pembayaran'],
                    // ✅ DATA AUTO PRINT
                    'auto_print' => $settings->auto_print ?? false,
                    'printer_name' => $settings->printer_name ?? '',
                    'print_url' => route('transaksi.struk.printer', $idPenjualan)
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Get Daftar Piutang
    public function getPiutang()
    {
        try {
            $piutang = DB::table('penjualan')
                ->join('kasir', 'penjualan.id_kasir', '=', 'kasir.id_kasir')
                ->join('user', 'kasir.id_user', '=', 'user.id_user')
                ->select(
                    'penjualan.id_penjualan',
                    'penjualan.tanggal_penjualan',
                    'penjualan.total_pembayaran',
                    'penjualan.total_bayar',
                    'penjualan.status_pembayaran',
                    'user.nama_user as kasir'
                )
                ->orderBy('penjualan.tanggal_penjualan', 'desc')
                ->get()
                ->map(function($item) {
                    return [
                        'id_penjualan' => $item->id_penjualan,
                        'tanggal_penjualan' => Carbon::parse($item->tanggal_penjualan)->format('d/m/Y H:i'),
                        'total_pembayaran' => $item->total_pembayaran,
                        'total_bayar' => $item->total_bayar,
                        'status_pembayaran' => $item->status_pembayaran,
                        'kasir' => $item->kasir
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $piutang
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Get Detail Transaksi
    public function getDetail($id)
    {
        try {
            $penjualan = DB::table('penjualan')
                ->join('kasir', 'penjualan.id_kasir', '=', 'kasir.id_kasir')
                ->join('user', 'kasir.id_user', '=', 'user.id_user')
                ->where('penjualan.id_penjualan', $id)
                ->select(
                    'penjualan.*',
                    'user.nama_user as kasir'
                )
                ->first();

            if (!$penjualan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi tidak ditemukan'
                ], 404);
            }

            $detail = DB::table('penjualan_detail')
                ->join('produk', 'penjualan_detail.id_produk', '=', 'produk.id_produk')
                ->where('penjualan_detail.id_penjualan', $id)
                ->select(
                    'penjualan_detail.*',
                    'produk.nama_produk'
                )
                ->get()
                ->map(function($item) {
                    return [
                        'nama_produk' => $item->nama_produk,
                        'qty' => $item->qty_produk,
                        'harga' => $item->harga_produk,
                        'subtotal' => $item->subtotal_harga
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'id_penjualan' => $penjualan->id_penjualan,
                    'tanggal_penjualan' => Carbon::parse($penjualan->tanggal_penjualan)->format('d/m/Y H:i'),
                    'total_pembayaran' => $penjualan->total_pembayaran,
                    'total_bayar' => $penjualan->total_bayar,
                    'kembalian_pembayaran' => $penjualan->kembalian_pembayaran,
                    'status_pembayaran' => $penjualan->status_pembayaran,
                    'kasir' => $penjualan->kasir,
                    'items' => $detail
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Bayar Piutang
    public function bayarPiutang(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'total_bayar' => 'required|numeric|min:0'
            ]);

            $penjualan = DB::table('penjualan')->where('id_penjualan', $id)->first();

            if (!$penjualan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi tidak ditemukan'
                ], 404);
            }

            if ($penjualan->status_pembayaran === 'lunas') {
                return response()->json([
                    'success' => false,
                    'message' => 'Piutang sudah lunas'
                ], 400);
            }

            if ($validated['total_bayar'] < $penjualan->total_pembayaran) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pembayaran kurang dari total tagihan'
                ], 400);
            }

            // Update status pembayaran
            DB::table('penjualan')
                ->where('id_penjualan', $id)
                ->update([
                    'total_bayar' => $validated['total_bayar'],
                    'kembalian_pembayaran' => $validated['total_bayar'] - $penjualan->total_pembayaran,
                    'status_pembayaran' => 'lunas'
                ]);

            DB::commit();

            // ✅ AMBIL SETTINGS DARI DATABASE
            $settings = DB::table('settings')->first();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran piutang berhasil',
                'data' => [
                    'id_penjualan' => $id,
                    'total_bayar' => $validated['total_bayar'],
                    'kembalian_pembayaran' => $validated['total_bayar'] - $penjualan->total_pembayaran,
                    // ✅ DATA AUTO PRINT
                    'auto_print' => $settings->auto_print ?? false,
                    'printer_name' => $settings->printer_name ?? '',
                    'print_url' => route('transaksi.struk.printer', $id)
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Struk untuk browser (PDF/HTML)
    public function struk($id)
    {
        $penjualan = DB::table('penjualan')
            ->join('kasir', 'penjualan.id_kasir', '=', 'kasir.id_kasir')
            ->join('user', 'kasir.id_user', '=', 'user.id_user')
            ->where('penjualan.id_penjualan', $id)
            ->select(
                'penjualan.*',
                'user.nama_user as kasir'
            )
            ->first();

        if (!$penjualan) {
            abort(404, 'Transaksi tidak ditemukan');
        }

        $detail = DB::table('penjualan_detail')
            ->join('produk', 'penjualan_detail.id_produk', '=', 'produk.id_produk')
            ->where('penjualan_detail.id_penjualan', $id)
            ->select(
                'penjualan_detail.*',
                'produk.nama_produk'
            )
            ->get();

        return view('transaksi.struk', compact('penjualan', 'detail'));
    }

    // Struk untuk thermal printer (auto print)
    public function strukPrinter($id)
    {
        $penjualan = DB::table('penjualan')
            ->join('kasir', 'penjualan.id_kasir', '=', 'kasir.id_kasir')
            ->join('user', 'kasir.id_user', '=', 'user.id_user')
            ->where('penjualan.id_penjualan', $id)
            ->select(
                'penjualan.*',
                'user.nama_user as kasir'
            )
            ->first();

        if (!$penjualan) {
            abort(404, 'Transaksi tidak ditemukan');
        }

        $detail = DB::table('penjualan_detail')
            ->join('produk', 'penjualan_detail.id_produk', '=', 'produk.id_produk')
            ->where('penjualan_detail.id_penjualan', $id)
            ->select(
                'penjualan_detail.*',
                'produk.nama_produk'
            )
            ->get();

        // ✅ AMBIL SETTINGS DARI DATABASE
        $settings = DB::table('settings')->first();

        // Format data untuk thermal printer
        $receiptData = [
            'id_penjualan' => $penjualan->id_penjualan,
            'no_transaksi' => '#' . str_pad($penjualan->id_penjualan, 6, '0', STR_PAD_LEFT),
            'tanggal_penjualan' => Carbon::parse($penjualan->tanggal_penjualan)->format('d/m/Y H:i'),
            'kasir' => $penjualan->kasir,
            'total_bayar' => (float) $penjualan->total_bayar,
            'total_pembayaran' => (float) $penjualan->total_pembayaran,
            'kembalian_pembayaran' => (float) $penjualan->kembalian_pembayaran,
            'status_pembayaran' => $penjualan->status_pembayaran ?? 'lunas',
            'items' => $detail->map(function($item) {
                return [
                    'nama_produk' => $item->nama_produk,
                    'qty_produk' => (int) $item->qty_produk,
                    'harga_produk' => (float) $item->harga_produk,
                    'subtotal_harga' => (float) $item->subtotal_harga
                ];
            })->toArray()
        ];

        return view('transaksi.printer', compact('receiptData', 'settings'));
    }
}