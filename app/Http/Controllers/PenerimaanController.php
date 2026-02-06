<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PenerimaanController extends Controller
{
    /**
     * Display a listing of penerimaan
     */
    public function index(Request $request)
    {
        $query = DB::table('penerimaan')
            ->leftJoin('supplier', 'penerimaan.id_supplier', '=', 'supplier.id_supplier')
            ->select(
                'penerimaan.*',
                'supplier.nama_supplier'
            );

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('supplier.nama_supplier', 'like', "%{$search}%")
                    ->orWhere('penerimaan.id_penerimaan', 'like', "%{$search}%");
            });
        }

        // Filter by supplier
        if ($request->has('supplier') && $request->supplier != '') {
            $query->where('penerimaan.id_supplier', $request->supplier);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('penerimaan.tanggal_penerimaan', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('penerimaan.tanggal_penerimaan', '<=', $request->end_date);
        }

        $penerimaan = $query->orderBy('penerimaan.tanggal_penerimaan', 'desc')
            ->paginate(10);

        // Get suppliers for filter
        $suppliers = DB::table('supplier')
            ->orderBy('nama_supplier', 'asc')
            ->get();

        return view('penerimaan.index', compact('penerimaan', 'suppliers'));
    }

    /**
     * Show the form for creating a new penerimaan
     */
    public function create()
    {
        // Get suppliers
        $suppliers = DB::table('supplier')
            ->orderBy('nama_supplier', 'asc')
            ->get();

        // Get products
        $products = DB::table('produk')
            ->leftJoin('produk_kategori', 'produk.id_produk_kategori', '=', 'produk_kategori.id_produk_kategori')
            ->select(
                'produk.*',
                'produk_kategori.nama_kategori'
            )
            ->orderBy('produk.nama_produk', 'asc')
            ->get();

        return view('penerimaan.create', compact('suppliers', 'products'));
    }

    /**
     * Store a newly created penerimaan
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_supplier' => 'required|exists:supplier,id_supplier',
            'tanggal_penerimaan' => 'required|date',
            'id_metode_pembayaran' => 'nullable|integer',
            'detail' => 'required|array|min:1',
            'detail.*.id_produk' => 'required|exists:produk,id_produk',
            'detail.*.harga_produk' => 'required|numeric|min:0',
            'detail.*.qty_produk' => 'required|integer|min:1',
        ], [
            'id_supplier.required' => 'Supplier harus dipilih',
            'id_supplier.exists' => 'Supplier tidak valid',
            'tanggal_penerimaan.required' => 'Tanggal penerimaan harus diisi',
            'detail.required' => 'Detail penerimaan harus diisi',
            'detail.min' => 'Minimal harus ada 1 produk',
            'detail.*.id_produk.required' => 'Produk harus dipilih',
            'detail.*.harga_produk.required' => 'Harga produk harus diisi',
            'detail.*.qty_produk.required' => 'Jumlah produk harus diisi',
            'detail.*.qty_produk.min' => 'Jumlah produk minimal 1',
        ]);

        DB::beginTransaction();
        try {
            // Calculate total
            $total_harga = 0;
            foreach ($validated['detail'] as $item) {
                $total_harga += ($item['harga_produk'] * $item['qty_produk']);
            }

            // Insert penerimaan
            $id_penerimaan = DB::table('penerimaan')->insertGetId([
                'id_supplier' => $validated['id_supplier'],
                'tanggal_penerimaan' => $validated['tanggal_penerimaan'],
                'total_harga' => $total_harga,
                'id_metode_pembayaran' => $validated['id_metode_pembayaran'] ?? null,
            ]);

            // Insert penerimaan detail & Update stock & LOG
            foreach ($validated['detail'] as $item) {
                $subtotal = $item['harga_produk'] * $item['qty_produk'];

                // Get stock sebelum update
                $produk = DB::table('produk')
                    ->where('id_produk', $item['id_produk'])
                    ->first();

                $stock_sebelum = $produk->stock_produk ?? 0;

                // Insert detail
                DB::table('penerimaan_detail')->insert([
                    'id_penerimaan' => $id_penerimaan,
                    'id_produk' => $item['id_produk'],
                    'harga_produk' => $item['harga_produk'],
                    'qty_produk' => $item['qty_produk'],
                    'subtotal_harga' => $subtotal,
                ]);

                // UPDATE STOCK PRODUK (TAMBAH STOK)
                DB::table('produk')
                    ->where('id_produk', $item['id_produk'])
                    ->increment('stock_produk', $item['qty_produk']);

                // Get stock setelah update
                $stock_sesudah = $stock_sebelum + $item['qty_produk'];

                // ✅ CATAT LOG STOCK - HANYA SATU INSERT (Konsisten dengan nama kolom)
                DB::table('log_stock')->insert([
                    'id_aktivitas' => $id_penerimaan,
                    'id_produk' => $item['id_produk'],
                    'jenis_aktivitas' => 'PENERIMAAN',
                    'jumlah_aktivitas' => $item['qty_produk'],
                    'jumlah_awal' => $stock_sebelum,
                    'jumlah_akhir' => $stock_sesudah,
                ]);
                
                // ✅ CATAT LOG KE PRODUK_LOGS (Optional - jika tabel ada)
                if (DB::getSchemaBuilder()->hasTable('produk_logs')) {
                    DB::table('produk_logs')->insert([
                        'id_produk' => $item['id_produk'],
                        'jenis_aktivitas' => 'penerimaan',
                        'stok_sebelum' => $stock_sebelum,
                        'stok_sesudah' => $stock_sesudah,
                        'jumlah_perubahan' => $item['qty_produk'],
                        'harga_saat_itu' => $item['harga_produk'],
                        'keterangan' => 'Penerimaan barang dari supplier',
                        'id_penjualan' => null,
                        'id_penerimaan' => $id_penerimaan,
                        'user_nama' => auth()->check() ? (auth()->user()->nama_user ?? auth()->user()->name) : 'System',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('penerimaan.index')
                ->with('success', 'Penerimaan barang berhasil ditambahkan dan stok telah diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified penerimaan
     * 🔧 FIXED: Menghindari duplikasi log stock
     */
    public function show($id)
    {
        // Get penerimaan with supplier
        $penerimaan = DB::table('penerimaan')
            ->leftJoin('supplier', 'penerimaan.id_supplier', '=', 'supplier.id_supplier')
            ->select(
                'penerimaan.*',
                'supplier.nama_supplier',
                'supplier.telp_supplier',
                'supplier.alamat_supplier'
            )
            ->where('penerimaan.id_penerimaan', $id)
            ->first();

        if (!$penerimaan) {
            abort(404, 'Penerimaan tidak ditemukan');
        }

        // Get penerimaan detail
        $detail = DB::table('penerimaan_detail')
            ->leftJoin('produk', 'penerimaan_detail.id_produk', '=', 'produk.id_produk')
            ->leftJoin('produk_kategori', 'produk.id_produk_kategori', '=', 'produk_kategori.id_produk_kategori')
            ->select(
                'penerimaan_detail.*',
                'produk.nama_produk',
                'produk.code_produk',
                'produk_kategori.nama_kategori'
            )
            ->where('penerimaan_detail.id_penerimaan', $id)
            ->get();

        // Calculate statistics
        $stats = [
            'total_item' => $detail->count(),
            'total_qty' => $detail->sum('qty_produk'),
            'total_harga' => $penerimaan->total_harga,
        ];

        // 🔧 FIXED: Get log stock dengan GROUP BY untuk menghindari duplikasi
        $logStock = DB::table('log_stock')
            ->leftJoin('produk', 'log_stock.id_produk', '=', 'produk.id_produk')
            ->select(
                'log_stock.id_produk',
                'produk.nama_produk',
                'produk.code_produk',
                DB::raw('SUM(log_stock.jumlah_aktivitas) as jumlah_aktivitas'),
                DB::raw('MIN(log_stock.jumlah_awal) as jumlah_awal'),
                DB::raw('MAX(log_stock.jumlah_akhir) as jumlah_akhir')
            )
            ->where('log_stock.id_aktivitas', $id)
            ->where('log_stock.jenis_aktivitas', 'PENERIMAAN')
            ->groupBy('log_stock.id_produk', 'produk.nama_produk', 'produk.code_produk')
            ->get();

        return view('penerimaan.show', compact('penerimaan', 'detail', 'stats', 'logStock'));
    }

    /**
     * Remove the specified penerimaan
     * 🔧 FIXED: Menghindari duplikasi log saat delete
     */
    public function destroy($id)
    {
        $penerimaan = DB::table('penerimaan')->where('id_penerimaan', $id)->first();

        if (!$penerimaan) {
            abort(404, 'Penerimaan tidak ditemukan');
        }

        DB::beginTransaction();
        try {
            // Get all detail before delete
            $details = DB::table('penerimaan_detail')
                ->where('id_penerimaan', $id)
                ->get();

            // Reduce stock for each product (ROLLBACK STOK)
            foreach ($details as $detail) {
                // Get stock sebelum update
                $produk = DB::table('produk')
                    ->where('id_produk', $detail->id_produk)
                    ->first();

                $stock_sebelum = $produk->stock_produk ?? 0;

                // Kurangi stok
                DB::table('produk')
                    ->where('id_produk', $detail->id_produk)
                    ->decrement('stock_produk', $detail->qty_produk);

                $stock_sesudah = $stock_sebelum - $detail->qty_produk;

                // ✅ CATAT LOG STOCK PENGURANGAN (untuk audit trail)
                DB::table('log_stock')->insert([
                    'id_aktivitas' => $id,
                    'id_produk' => $detail->id_produk,
                    'jenis_aktivitas' => 'HAPUS_PENERIMAAN', // Ubah jenis untuk membedakan
                    'jumlah_aktivitas' => -$detail->qty_produk, // negatif karena dikurangi
                    'jumlah_awal' => $stock_sebelum,
                    'jumlah_akhir' => $stock_sesudah,
                ]);
            }

            // 🔧 FIXED: Hapus log stock lama SETELAH insert log baru
            // Jangan hapus semua, hanya yang jenis_aktivitas = 'PENERIMAAN'
            DB::table('log_stock')
                ->where('id_aktivitas', $id)
                ->where('jenis_aktivitas', 'PENERIMAAN')
                ->delete();

            // Delete penerimaan detail
            DB::table('penerimaan_detail')
                ->where('id_penerimaan', $id)
                ->delete();

            // Delete penerimaan
            DB::table('penerimaan')
                ->where('id_penerimaan', $id)
                ->delete();

            DB::commit();

            return redirect()->route('penerimaan.index')
                ->with('success', 'Penerimaan berhasil dihapus dan stok telah dikembalikan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get penerimaan statistics
     */
    public function statistics(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $stats = [
            'total_penerimaan' => DB::table('penerimaan')
                ->whereBetween('tanggal_penerimaan', [$startDate, $endDate])
                ->count(),
            'total_nilai' => DB::table('penerimaan')
                ->whereBetween('tanggal_penerimaan', [$startDate, $endDate])
                ->sum('total_harga') ?? 0,
            'total_item' => DB::table('penerimaan_detail')
                ->join('penerimaan', 'penerimaan_detail.id_penerimaan', '=', 'penerimaan.id_penerimaan')
                ->whereBetween('penerimaan.tanggal_penerimaan', [$startDate, $endDate])
                ->sum('penerimaan_detail.qty_produk') ?? 0,
            'supplier_terbanyak' => DB::table('penerimaan')
                ->join('supplier', 'penerimaan.id_supplier', '=', 'supplier.id_supplier')
                ->whereBetween('penerimaan.tanggal_penerimaan', [$startDate, $endDate])
                ->select('supplier.nama_supplier', DB::raw('COUNT(*) as total'))
                ->groupBy('supplier.id_supplier', 'supplier.nama_supplier')
                ->orderBy('total', 'desc')
                ->first(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Print penerimaan receipt
     */
    public function print($id)
    {
        // Get penerimaan with supplier
        $penerimaan = DB::table('penerimaan')
            ->leftJoin('supplier', 'penerimaan.id_supplier', '=', 'supplier.id_supplier')
            ->select(
                'penerimaan.*',
                'supplier.nama_supplier',
                'supplier.telp_supplier',
                'supplier.alamat_supplier'
            )
            ->where('penerimaan.id_penerimaan', $id)
            ->first();

        if (!$penerimaan) {
            abort(404, 'Penerimaan tidak ditemukan');
        }

        // Get penerimaan detail
        $detail = DB::table('penerimaan_detail')
            ->leftJoin('produk', 'penerimaan_detail.id_produk', '=', 'produk.id_produk')
            ->select(
                'penerimaan_detail.*',
                'produk.nama_produk',
                'produk.code_produk'
            )
            ->where('penerimaan_detail.id_penerimaan', $id)
            ->get();

        return view('penerimaan.print', compact('penerimaan', 'detail'));
    }

    /**
     * Export penerimaan to CSV
     */
    public function export(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $penerimaan = DB::table('penerimaan')
            ->leftJoin('supplier', 'penerimaan.id_supplier', '=', 'supplier.id_supplier')
            ->select(
                'penerimaan.*',
                'supplier.nama_supplier'
            )
            ->whereBetween('penerimaan.tanggal_penerimaan', [$startDate, $endDate])
            ->orderBy('penerimaan.tanggal_penerimaan', 'desc')
            ->get();

        $filename = 'penerimaan_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($penerimaan) {
            $file = fopen('php://output', 'w');

            // Header CSV
            fputcsv($file, ['No. Penerimaan', 'Tanggal', 'Supplier', 'Total Harga']);

            // Data
            foreach ($penerimaan as $item) {
                fputcsv($file, [
                    'PNM-' . str_pad($item->id_penerimaan, 6, '0', STR_PAD_LEFT),
                    date('d/m/Y H:i', strtotime($item->tanggal_penerimaan)),
                    $item->nama_supplier,
                    $item->total_harga,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}