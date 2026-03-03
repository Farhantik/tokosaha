<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KategoriController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('produk_kategori');

        if ($request->has('search') && $request->search != '') {
            $query->where('nama_kategori', 'like', "%{$request->search}%");
        }

        $categories = $query->orderBy('nama_kategori', 'asc')->paginate(10);

        foreach ($categories as $category) {
            $category->total_produk = DB::table('produk')
                ->where('id_produk_kategori', $category->id_produk_kategori)
                ->count();
        }

        return view('kategori.index', compact('categories'));
    }

    private function smartRedirect(string $type, string $message)
    {
        $referer    = request()->headers->get('referer', '');
        $fromProduk = str_contains($referer, '/produk');
        $route      = $fromProduk ? 'produk.index' : 'kategori.index';
        return redirect()->route($route)->with($type, $message);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:produk_kategori,nama_kategori',
        ], [
            'nama_kategori.required' => 'Nama kategori harus diisi',
            'nama_kategori.max'      => 'Nama kategori maksimal 100 karakter',
            'nama_kategori.unique'   => 'Nama kategori sudah ada',
        ]);

        DB::table('produk_kategori')->insert([
            'nama_kategori' => $validated['nama_kategori'],
        ]);

        return $this->smartRedirect('success', 'Kategori "' . $validated['nama_kategori'] . '" berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $category = DB::table('produk_kategori')
            ->where('id_produk_kategori', $id)->first();

        if (!$category) abort(404, 'Kategori tidak ditemukan');

        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:produk_kategori,nama_kategori,' . $id . ',id_produk_kategori',
        ], [
            'nama_kategori.required' => 'Nama kategori harus diisi',
            'nama_kategori.max'      => 'Nama kategori maksimal 100 karakter',
            'nama_kategori.unique'   => 'Nama kategori sudah ada',
        ]);

        DB::table('produk_kategori')
            ->where('id_produk_kategori', $id)
            ->update(['nama_kategori' => $validated['nama_kategori']]);

        return $this->smartRedirect('success', 'Kategori "' . $validated['nama_kategori'] . '" berhasil diperbarui');
    }

    public function destroy($id)
    {
        $category = DB::table('produk_kategori')
            ->where('id_produk_kategori', $id)->first();

        if (!$category) abort(404, 'Kategori tidak ditemukan');

        $hasProducts = DB::table('produk')
            ->where('id_produk_kategori', $id)->exists();

        if ($hasProducts) {
            return $this->smartRedirect('error', 'Tidak dapat menghapus kategori yang masih memiliki produk');
        }

        DB::table('produk_kategori')
            ->where('id_produk_kategori', $id)->delete();

        return $this->smartRedirect('success', 'Kategori berhasil dihapus');
    }

    public function list(Request $request)
    {
        $search     = $request->get('q', '');
        $categories = DB::table('produk_kategori')
            ->when($search, fn($q) => $q->where('nama_kategori', 'like', "%{$search}%"))
            ->orderBy('nama_kategori', 'asc')
            ->get(['id_produk_kategori', 'nama_kategori']);

        return response()->json([
            'results' => $categories->map(fn($c) => [
                'id'   => $c->id_produk_kategori,
                'text' => $c->nama_kategori,
            ])
        ]);
    }

    public function statistics($id)
    {
        $category = DB::table('produk_kategori')
            ->where('id_produk_kategori', $id)->first();

        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Kategori tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'total_produk'  => DB::table('produk')->where('id_produk_kategori', $id)->count(),
                'total_stock'   => DB::table('produk')->where('id_produk_kategori', $id)->sum('stock_produk') ?? 0,
                'produk_kosong' => DB::table('produk')->where('id_produk_kategori', $id)->where('stock_produk', '<=', 0)->count(),
            ]
        ]);
    }
}
