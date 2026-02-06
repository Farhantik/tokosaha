<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    /**
     * Display a listing of suppliers
     */
    public function index(Request $request)
    {
        $query = DB::table('supplier');

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_supplier', 'like', "%{$search}%")
                    ->orWhere('telp_supplier', 'like', "%{$search}%")
                    ->orWhere('alamat_supplier', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->orderBy('nama_supplier', 'asc')->paginate(10);

        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new supplier
     */
    public function create()
    {
        return view('suppliers.create');
    }

    /**
     * Store a newly created supplier
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_supplier' => 'required|string|max:100',
            'telp_supplier' => 'nullable|string|max:20',
            'alamat_supplier' => 'nullable|string',
        ], [
            'nama_supplier.required' => 'Nama supplier harus diisi',
            'nama_supplier.max' => 'Nama supplier maksimal 100 karakter',
            'telp_supplier.max' => 'Nomor telepon maksimal 20 karakter',
        ]);

        DB::table('supplier')->insert([
            'nama_supplier' => $validated['nama_supplier'],
            'telp_supplier' => $validated['telp_supplier'] ?? null,
            'alamat_supplier' => $validated['alamat_supplier'] ?? null,
        ]);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier berhasil ditambahkan');
    }

    /**
     * Display the specified supplier
     */
    public function show($id)
    {
        $supplier = DB::table('supplier')->where('id_supplier', $id)->first();

        if (!$supplier) {
            abort(404, 'Supplier tidak ditemukan');
        }

        // Get statistics
        $stats = [
            'total_penerimaan' => DB::table('penerimaan')
                ->where('id_supplier', $id)
                ->count(),
            'total_nilai' => DB::table('penerimaan')
                ->where('id_supplier', $id)
                ->sum('total_harga') ?? 0,
            'penerimaan_terakhir' => DB::table('penerimaan')
                ->where('id_supplier', $id)
                ->orderBy('tanggal_penerimaan', 'desc')
                ->first(),
        ];

        // Get recent penerimaan
        $recentPenerimaan = DB::table('penerimaan')
            ->where('id_supplier', $id)
            ->orderBy('tanggal_penerimaan', 'desc')
            ->limit(10)
            ->get();

        return view('suppliers.show', compact('supplier', 'stats', 'recentPenerimaan'));
    }

    /**
     * Show the form for editing supplier
     */
    public function edit($id)
    {
        $supplier = DB::table('supplier')->where('id_supplier', $id)->first();

        if (!$supplier) {
            abort(404, 'Supplier tidak ditemukan');
        }

        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified supplier
     */
    public function update(Request $request, $id)
    {
        $supplier = DB::table('supplier')->where('id_supplier', $id)->first();

        if (!$supplier) {
            abort(404, 'Supplier tidak ditemukan');
        }

        $validated = $request->validate([
            'nama_supplier' => 'required|string|max:100',
            'telp_supplier' => 'nullable|string|max:20',
            'alamat_supplier' => 'nullable|string',
        ], [
            'nama_supplier.required' => 'Nama supplier harus diisi',
            'nama_supplier.max' => 'Nama supplier maksimal 100 karakter',
            'telp_supplier.max' => 'Nomor telepon maksimal 20 karakter',
        ]);

        DB::table('supplier')->where('id_supplier', $id)->update([
            'nama_supplier' => $validated['nama_supplier'],
            'telp_supplier' => $validated['telp_supplier'] ?? null,
            'alamat_supplier' => $validated['alamat_supplier'] ?? null,
        ]);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier berhasil diperbarui');
    }

    /**
     * Remove the specified supplier
     */
    public function destroy($id)
    {
        $supplier = DB::table('supplier')->where('id_supplier', $id)->first();

        if (!$supplier) {
            abort(404, 'Supplier tidak ditemukan');
        }

        // Check if supplier has penerimaan
        $hasPenerimaan = DB::table('penerimaan')
            ->where('id_supplier', $id)
            ->exists();

        if ($hasPenerimaan) {
            return redirect()->route('suppliers.index')
                ->with('error', 'Tidak dapat menghapus supplier yang sudah memiliki riwayat penerimaan');
        }

        DB::table('supplier')->where('id_supplier', $id)->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier berhasil dihapus');
    }

    /**
     * Get supplier data for select2/ajax
     */
    public function search(Request $request)
    {
        $search = $request->get('q', '');

        $suppliers = DB::table('supplier')
            ->where('nama_supplier', 'like', "%{$search}%")
            ->orWhere('telp_supplier', 'like', "%{$search}%")
            ->limit(10)
            ->get(['id_supplier', 'nama_supplier', 'telp_supplier']);

        return response()->json([
            'results' => $suppliers->map(function ($supplier) {
                return [
                    'id' => $supplier->id_supplier,
                    'text' => $supplier->nama_supplier . ' - ' . ($supplier->telp_supplier ?? 'No Phone'),
                    'nama' => $supplier->nama_supplier,
                    'telp' => $supplier->telp_supplier,
                ];
            })
        ]);
    }

    /**
     * Get supplier detail for form auto-fill
     */
    public function getDetail($id)
    {
        $supplier = DB::table('supplier')->where('id_supplier', $id)->first();

        if (!$supplier) {
            return response()->json([
                'success' => false,
                'message' => 'Supplier tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id_supplier' => $supplier->id_supplier,
                'nama_supplier' => $supplier->nama_supplier,
                'telp_supplier' => $supplier->telp_supplier,
                'alamat_supplier' => $supplier->alamat_supplier,
            ]
        ]);
    }

    /**
     * Get supplier statistics
     */
    public function statistics($id)
    {
        $supplier = DB::table('supplier')->where('id_supplier', $id)->first();

        if (!$supplier) {
            return response()->json([
                'success' => false,
                'message' => 'Supplier tidak ditemukan'
            ], 404);
        }

        $stats = [
            'total_penerimaan' => DB::table('penerimaan')
                ->where('id_supplier', $id)
                ->count(),
            'total_nilai' => DB::table('penerimaan')
                ->where('id_supplier', $id)
                ->sum('total_harga') ?? 0,
            'total_item' => DB::table('penerimaan_detail')
                ->join('penerimaan', 'penerimaan_detail.id_penerimaan', '=', 'penerimaan.id_penerimaan')
                ->where('penerimaan.id_supplier', $id)
                ->sum('penerimaan_detail.qty_produk') ?? 0,
            'penerimaan_bulan_ini' => DB::table('penerimaan')
                ->where('id_supplier', $id)
                ->whereMonth('tanggal_penerimaan', date('m'))
                ->whereYear('tanggal_penerimaan', date('Y'))
                ->count(),
            'nilai_bulan_ini' => DB::table('penerimaan')
                ->where('id_supplier', $id)
                ->whereMonth('tanggal_penerimaan', date('m'))
                ->whereYear('tanggal_penerimaan', date('Y'))
                ->sum('total_harga') ?? 0,
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Export suppliers to CSV
     */
    public function export()
    {
        $suppliers = DB::table('supplier')
            ->orderBy('nama_supplier', 'asc')
            ->get();

        $filename = 'suppliers_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($suppliers) {
            $file = fopen('php://output', 'w');

            // Header CSV
            fputcsv($file, ['ID', 'Nama Supplier', 'Telepon', 'Alamat']);

            // Data
            foreach ($suppliers as $supplier) {
                fputcsv($file, [
                    $supplier->id_supplier,
                    $supplier->nama_supplier,
                    $supplier->telp_supplier ?? '-',
                    $supplier->alamat_supplier ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
