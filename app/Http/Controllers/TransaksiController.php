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
                'items'                        => 'required|array',
                'items.*.id_produk'            => 'required|exists:produk,id_produk',
                'items.*.qty'                  => 'required|integer|min:1',
                'items.*.harga'                => 'required|numeric|min:0',
                'total_bayar'                  => 'required|numeric|min:0',
                'total_pembayaran'             => 'required|numeric|min:0',
                // ✅ FIX: tambahkan bayar_sebagian ke daftar nilai yang diizinkan
                'status_pembayaran'            => 'required|in:lunas,belum_bayar,bayar_sebagian',
            ]);

            $user = Auth::user();

            $kasirAktif = DB::table('kasir')
                ->where('id_user', $user->id_user)
                ->whereNull('waktu_close')
                ->first();

            if (!$kasirAktif) {
                throw new \Exception('Kasir belum dibuka. Silakan buka kasir terlebih dahulu.');
            }

            // Cek stok semua item sebelum proses
            foreach ($validated['items'] as $item) {
                $produk = DB::table('produk')
                    ->where('id_produk', $item['id_produk'])
                    ->lockForUpdate()
                    ->first();

                if (!$produk) {
                    throw new \Exception("Produk dengan ID {$item['id_produk']} tidak ditemukan.");
                }

                $stokTersedia = max(0, $produk->stock_produk);

                if ($stokTersedia < $item['qty']) {
                    throw new \Exception(
                        "Stok \"{$produk->nama_produk}\" tidak mencukupi! " .
                            "Stok tersedia: {$stokTersedia}, diminta: {$item['qty']}."
                    );
                }
            }

            // ✅ FIX: Hitung sisa_tagihan berdasarkan status pembayaran
            $totalPembayaran = $validated['total_pembayaran'];
            $totalBayar      = $validated['total_bayar'];
            $status          = $validated['status_pembayaran'];

            if ($status === 'lunas') {
                $sisaTagihan   = 0;
                $kembalian     = $totalBayar - $totalPembayaran;
            } elseif ($status === 'bayar_sebagian') {
                $sisaTagihan   = $totalPembayaran - $totalBayar;
                $kembalian     = 0;
            } else {
                // belum_bayar
                $sisaTagihan   = $totalPembayaran;
                $kembalian     = 0;
            }

            $idPenjualan = DB::table('penjualan')->insertGetId([
                'id_kasir'             => $kasirAktif->id_kasir,
                'tanggal_penjualan'    => now(),
                'total_pembayaran'     => $totalPembayaran,
                'total_bayar'          => $totalBayar,
                'kembalian_pembayaran' => $kembalian,
                'status_pembayaran'    => $status,
                'sisa_tagihan'         => $sisaTagihan,
            ]);

            foreach ($validated['items'] as $item) {
                $produk = DB::table('produk')
                    ->where('id_produk', $item['id_produk'])
                    ->lockForUpdate()
                    ->first();

                $stokSebelum  = $produk->stock_produk;
                $stokTersedia = max(0, $stokSebelum);

                if ($stokTersedia < $item['qty']) {
                    throw new \Exception(
                        "Stok \"{$produk->nama_produk}\" tidak mencukupi saat proses! " .
                            "Stok tersedia: {$stokTersedia}, diminta: {$item['qty']}."
                    );
                }

                DB::table('penjualan_detail')->insert([
                    'id_penjualan'   => $idPenjualan,
                    'id_produk'      => $item['id_produk'],
                    'qty_produk'     => $item['qty'],
                    'harga_produk'   => $item['harga'],
                    'subtotal_harga' => $item['qty'] * $item['harga']
                ]);

                $stokBaru = max(0, $stokSebelum - $item['qty']);
                DB::table('produk')
                    ->where('id_produk', $item['id_produk'])
                    ->update(['stock_produk' => $stokBaru]);

                // ✅ FIX: keterangan log lebih deskriptif sesuai status
                $keteranganLog = match ($status) {
                    'lunas'         => 'Penjualan produk via transaksi kasir (Lunas)',
                    'bayar_sebagian' => 'Penjualan produk via transaksi kasir (Bayar Sebagian)',
                    default         => 'Penjualan produk via transaksi kasir (Piutang)',
                };

                DB::table('produk_logs')->insert([
                    'id_produk'        => $item['id_produk'],
                    'jenis_aktivitas'  => 'penjualan',
                    'stok_sebelum'     => $stokSebelum,
                    'stok_sesudah'     => $stokBaru,
                    'jumlah_perubahan' => -$item['qty'],
                    'harga_saat_itu'   => $item['harga'],
                    'keterangan'       => $keteranganLog,
                    'id_penjualan'     => $idPenjualan,
                    'id_penerimaan'    => null,
                    'user_nama'        => $user->nama_user ?? $user->name,
                    'created_at'       => now(),
                    'updated_at'       => now()
                ]);
            }

            DB::commit();

            $settings = DB::table('settings')->first();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan',
                'data'    => [
                    'id_penjualan'         => $idPenjualan,
                    'total_pembayaran'     => $totalPembayaran,
                    'total_bayar'          => $totalBayar,
                    'kembalian_pembayaran' => $kembalian,
                    'sisa_tagihan'         => $sisaTagihan,
                    'status_pembayaran'    => $status,
                    'auto_print'           => $settings->auto_print ?? false,
                    'printer_name'         => $settings->printer_name ?? '',
                    'print_url'            => route('transaksi.struk.printer', $idPenjualan)
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $e->errors()
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
                ->whereIn('penjualan.status_pembayaran', ['belum_bayar', 'bayar_sebagian', 'lunas'])
                ->select(
                    'penjualan.id_penjualan',
                    'penjualan.tanggal_penjualan',
                    'penjualan.total_pembayaran',
                    'penjualan.total_bayar',
                    'penjualan.status_pembayaran',
                    'penjualan.sisa_tagihan',
                    'user.nama_user as kasir'
                )
                ->orderBy('penjualan.tanggal_penjualan', 'desc')
                ->get()
                ->map(function ($item) {
                    $sisaTagihan = isset($item->sisa_tagihan) && $item->sisa_tagihan !== null
                        ? (float) $item->sisa_tagihan
                        : (float) $item->total_pembayaran;

                    return [
                        'id_penjualan'      => $item->id_penjualan,
                        'tanggal_penjualan' => Carbon::parse($item->tanggal_penjualan)->format('d/m/Y H:i'),
                        'total_pembayaran'  => (float) $item->total_pembayaran,
                        'total_bayar'       => (float) $item->total_bayar,
                        'status_pembayaran' => $item->status_pembayaran,
                        'sisa_tagihan'      => $item->status_pembayaran === 'lunas' ? 0 : $sisaTagihan,
                        'kasir'             => $item->kasir
                    ];
                });

            return response()->json([
                'success' => true,
                'data'    => $piutang
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
                ->select('penjualan.*', 'user.nama_user as kasir')
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
                ->select('penjualan_detail.*', 'produk.nama_produk')
                ->get()
                ->map(function ($item) {
                    return [
                        'nama_produk' => $item->nama_produk,
                        'qty'         => $item->qty_produk,
                        'harga'       => $item->harga_produk,
                        'subtotal'    => $item->subtotal_harga
                    ];
                });

            $sisaTagihan = isset($penjualan->sisa_tagihan) && $penjualan->sisa_tagihan !== null
                ? (float) $penjualan->sisa_tagihan
                : (float) $penjualan->total_pembayaran;

            return response()->json([
                'success' => true,
                'data'    => [
                    'id_penjualan'         => $penjualan->id_penjualan,
                    'tanggal_penjualan'    => Carbon::parse($penjualan->tanggal_penjualan)->format('d/m/Y H:i'),
                    'total_pembayaran'     => (float) $penjualan->total_pembayaran,
                    'total_bayar'          => (float) $penjualan->total_bayar,
                    'kembalian_pembayaran' => (float) $penjualan->kembalian_pembayaran,
                    'status_pembayaran'    => $penjualan->status_pembayaran,
                    'sisa_tagihan'         => $penjualan->status_pembayaran === 'lunas' ? 0 : $sisaTagihan,
                    'kasir'                => $penjualan->kasir,
                    'items'                => $detail
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Bayar Piutang — support bayar sebagian
    public function bayarPiutang(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'total_bayar'       => 'required|numeric|min:1',
                'status_pembayaran' => 'nullable|in:lunas,bayar_sebagian',
                'payment_methods'   => 'nullable|array',
            ]);

            $penjualan = DB::table('penjualan')->where('id_penjualan', $id)->lockForUpdate()->first();

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

            $sisaSekarang    = isset($penjualan->sisa_tagihan) && $penjualan->sisa_tagihan !== null
                ? (float) $penjualan->sisa_tagihan
                : (float) $penjualan->total_pembayaran;

            $totalBayarBaru  = (float) $validated['total_bayar'];
            $sisaBaru        = $sisaSekarang - $totalBayarBaru;

            if ($sisaBaru <= 0) {
                $statusBaru = 'lunas';
                $sisaBaru   = 0;
                $kembalian  = $totalBayarBaru - $sisaSekarang;
            } else {
                $statusBaru = 'bayar_sebagian';
                $kembalian  = 0;
            }

            $totalBayarAkumulasi = (float) $penjualan->total_bayar + $totalBayarBaru;

            DB::table('penjualan')
                ->where('id_penjualan', $id)
                ->update([
                    'total_bayar'          => $totalBayarAkumulasi,
                    'kembalian_pembayaran' => $kembalian,
                    'status_pembayaran'    => $statusBaru,
                    'sisa_tagihan'         => $sisaBaru,
                ]);

            DB::commit();

            $settings = DB::table('settings')->first();

            return response()->json([
                'success' => true,
                'message' => $statusBaru === 'lunas' ? 'Pembayaran piutang lunas!' : 'Bayar sebagian berhasil dicatat',
                'data'    => [
                    'id_penjualan'          => $id,
                    'total_bayar'           => $totalBayarBaru,
                    'total_bayar_akumulasi' => $totalBayarAkumulasi,
                    'kembalian_pembayaran'  => $kembalian,
                    'sisa_tagihan'          => $sisaBaru,
                    'status_pembayaran'     => $statusBaru,
                    'auto_print'            => $settings->auto_print ?? false,
                    'printer_name'          => $settings->printer_name ?? '',
                    'print_url'             => route('transaksi.struk.printer', $id)
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $e->errors()
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
            ->select('penjualan.*', 'user.nama_user as kasir')
            ->first();

        if (!$penjualan) {
            abort(404, 'Transaksi tidak ditemukan');
        }

        $detail = DB::table('penjualan_detail')
            ->join('produk', 'penjualan_detail.id_produk', '=', 'produk.id_produk')
            ->where('penjualan_detail.id_penjualan', $id)
            ->select('penjualan_detail.*', 'produk.nama_produk')
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
            ->select('penjualan.*', 'user.nama_user as kasir')
            ->first();

        if (!$penjualan) {
            abort(404, 'Transaksi tidak ditemukan');
        }

        $detail = DB::table('penjualan_detail')
            ->join('produk', 'penjualan_detail.id_produk', '=', 'produk.id_produk')
            ->where('penjualan_detail.id_penjualan', $id)
            ->select('penjualan_detail.*', 'produk.nama_produk')
            ->get();

        $settings = DB::table('settings')->first();

        $receiptData = [
            'id_penjualan'         => $penjualan->id_penjualan,
            'no_transaksi'         => '#' . str_pad($penjualan->id_penjualan, 6, '0', STR_PAD_LEFT),
            'tanggal_penjualan'    => Carbon::parse($penjualan->tanggal_penjualan)->format('d/m/Y H:i'),
            'kasir'                => $penjualan->kasir,
            'total_bayar'          => (float) $penjualan->total_bayar,
            'total_pembayaran'     => (float) $penjualan->total_pembayaran,
            'kembalian_pembayaran' => (float) $penjualan->kembalian_pembayaran,
            'sisa_tagihan'         => (float) ($penjualan->sisa_tagihan ?? 0),
            'status_pembayaran'    => $penjualan->status_pembayaran ?? 'lunas',
            'items'                => $detail->map(function ($item) {
                return [
                    'nama_produk'    => $item->nama_produk,
                    'qty_produk'     => (int) $item->qty_produk,
                    'harga_produk'   => (float) $item->harga_produk,
                    'subtotal_harga' => (float) $item->subtotal_harga
                ];
            })->toArray()
        ];

        return view('transaksi.printer', compact('receiptData', 'settings'));
    }
}
