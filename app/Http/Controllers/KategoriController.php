<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KategoriController extends Controller
{
    /**
     * Display a listing of categories
     */
    public function index(Request $request)
    {
        $query = DB::table('produk_kategori');

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('nama_kategori', 'like', "%{$search}%");
        }

        $categories = $query->orderBy('nama_kategori', 'asc')->paginate(10);

        // Get product count for each category
        foreach ($categories as $category) {
            $category->total_produk = DB::table('produk')
                ->where('id_produk_kategori', $category->id_produk_kategori)
                ->count();
        }

        return view('kategori.index', compact('categories'));
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:produk_kategori,nama_kategori',
        ], [
            'nama_kategori.required' => 'Nama kategori harus diisi',
            'nama_kategori.max' => 'Nama kategori maksimal 100 karakter',
            'nama_kategori.unique' => 'Nama kategori sudah ada',
        ]);

        DB::table('produk_kategori')->insert([
            'nama_kategori' => $validated['nama_kategori'],
        ]);

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori berhasil ditambahkan');
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, $id)
    {
        $category = DB::table('produk_kategori')
            ->where('id_produk_kategori', $id)
            ->first();

        if (!$category) {
            abort(404, 'Kategori tidak ditemukan');
        }

        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:produk_kategori,nama_kategori,' . $id . ',id_produk_kategori',
        ], [
            'nama_kategori.required' => 'Nama kategori harus diisi',
            'nama_kategori.max' => 'Nama kategori maksimal 100 karakter',
            'nama_kategori.unique' => 'Nama kategori sudah ada',
        ]);

        DB::table('produk_kategori')
            ->where('id_produk_kategori', $id)
            ->update([
                'nama_kategori' => $validated['nama_kategori'],
            ]);

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori berhasil diperbarui');
    }

    /**
     * Remove the specified category
     */
    public function destroy($id)
    {
        $category = DB::table('produk_kategori')
            ->where('id_produk_kategori', $id)
            ->first();

        if (!$category) {
            abort(404, 'Kategori tidak ditemukan');
        }

        // Check if category has products
        $hasProducts = DB::table('produk')
            ->where('id_produk_kategori', $id)
            ->exists();

        if ($hasProducts) {
            return redirect()->route('kategori.index')
                ->with('error', 'Tidak dapat menghapus kategori yang masih memiliki produk');
        }

        DB::table('produk_kategori')
            ->where('id_produk_kategori', $id)
            ->delete();

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori berhasil dihapus');
    }

    /**
     * Get categories list for API/Select
     */
    public function list(Request $request)
    {
        $search = $request->get('q', '');

        $categories = DB::table('produk_kategori')
            ->when($search, function ($query) use ($search) {
                $query->where('nama_kategori', 'like', "%{$search}%");
            })
            ->orderBy('nama_kategori', 'asc')
            ->get(['id_produk_kategori', 'nama_kategori']);

        return response()->json([
            'results' => $categories->map(function ($category) {
                return [
                    'id' => $category->id_produk_kategori,
                    'text' => $category->nama_kategori,
                ];
            })
        ]);
    }

    /**
     * Get category statistics
     */
    public function statistics($id)
    {
        $category = DB::table('produk_kategori')
            ->where('id_produk_kategori', $id)
            ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan'
            ], 404);
        }

        $stats = [
            'total_produk' => DB::table('produk')
                ->where('id_produk_kategori', $id)
                ->count(),
            'total_stock' => DB::table('produk')
                ->where('id_produk_kategori', $id)
                ->sum('stock_produk') ?? 0,
            'produk_kosong' => DB::table('produk')
                ->where('id_produk_kategori', $id)
                ->where('stock_produk', '<=', 0)
                ->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
