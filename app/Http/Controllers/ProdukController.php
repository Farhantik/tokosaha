<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\ProdukKategori;
use App\Models\ProdukLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProdukController extends Controller
{
    /**
     * Copy file ke public_html (karena web server menggunakan public_html)
     */
    private function copyToPublicHtml($filename)
    {
        $sourcePath = public_path('uploads/produk/' . $filename);
        $destPath = '/home/irryvkri/public_html/uploads/produk/' . $filename;

        Log::info("=== Copy Produk Image ===");
        Log::info("Source: " . $sourcePath);
        Log::info("Dest: " . $destPath);

        if (!file_exists('/home/irryvkri/public_html/uploads/produk')) {
            mkdir('/home/irryvkri/public_html/uploads/produk', 0755, true);
            Log::info("Created directory: /home/irryvkri/public_html/uploads/produk");
        }

        if (file_exists($sourcePath)) {
            try {
                copy($sourcePath, $destPath);
                Log::info("✅ Successfully copied produk image: " . $filename);
            } catch (\Exception $e) {
                Log::error("❌ Failed to copy produk image: " . $e->getMessage());
            }
        } else {
            Log::error("❌ Source produk image not found: " . $sourcePath);
        }
    }

    /**
     * Hapus file dari kedua lokasi
     */
    private function deleteFromBothLocations($filename)
    {
        Log::info("=== Delete Produk Image ===");
        Log::info("Deleting: " . $filename);

        // Hapus dari public Laravel
        $publicPath = public_path('uploads/produk/' . $filename);
        if (file_exists($publicPath)) {
            unlink($publicPath);
            Log::info("Deleted from public: " . $filename);
        }

        // Hapus dari public_html
        $htmlPath = '/home/irryvkri/public_html/uploads/produk/' . $filename;
        if (file_exists($htmlPath)) {
            unlink($htmlPath);
            Log::info("✅ Deleted from public_html: " . $filename);
        }
    }

    /**
     * Catat log aktivitas produk
     */
    private function catatLog($idProduk, $jenisAktivitas, $data = [])
    {
        try {
            $produk = Produk::find($idProduk);

            if (!$produk) {
                Log::warning("Produk tidak ditemukan untuk logging: ID {$idProduk}");
                return false;
            }

            ProdukLog::create([
                'id_produk'        => $idProduk,
                'jenis_aktivitas'  => $jenisAktivitas,
                'stok_sebelum'     => $data['stok_sebelum']     ?? $produk->stock_produk,
                'stok_sesudah'     => $data['stok_sesudah']     ?? $produk->stock_produk,
                'jumlah_perubahan' => $data['jumlah_perubahan'] ?? null,
                'harga_saat_itu'   => $data['harga_saat_itu']   ?? $produk->harga_produk,
                'keterangan'       => $data['keterangan']       ?? null,
                'id_penjualan'     => $data['id_penjualan']     ?? null,
                'id_penerimaan'    => $data['id_penerimaan']    ?? null,
                'user_nama'        => auth()->check() ? auth()->user()->nama_user : 'System'
            ]);

            Log::info("✅ Log aktivitas produk berhasil dicatat: {$jenisAktivitas} - Produk ID: {$idProduk}");
            return true;
        } catch (\Exception $e) {
            Log::error("❌ Gagal mencatat log produk: " . $e->getMessage());
            return false;
        }
    }

    public function index()
    {
        $produk = Produk::with('kategori')
            ->orderBy('nama_produk', 'asc')
            ->paginate(15);
        $kategori = ProdukKategori::orderBy('nama_kategori', 'asc')->get();
        return view('produk.index', compact('produk', 'kategori'));
    }

    public function store(Request $request)
    {
        Log::info("=== Store New Product ===");
        Log::info("Request has file: " . ($request->hasFile('gambar_produk') ? 'YES' : 'NO'));

        $request->validate([
            'nama_produk'        => 'required|string|max:150',
            'code_produk'        => 'nullable|string|max:50',
            'id_produk_kategori' => 'nullable|exists:produk_kategori,id_produk_kategori',
            'harga_produk'       => 'required|numeric|min:0',
            'stock_produk'       => 'required|integer|min:0',
            'gambar_produk'      => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'deskripsi_produk'   => 'nullable|string|max:500', // ✅ VALIDASI DESKRIPSI
        ], [
            'nama_produk.required'  => 'Nama produk wajib diisi.',
            'harga_produk.required' => 'Harga produk wajib diisi.',
            'harga_produk.min'      => 'Harga produk tidak boleh negatif.',
            'stock_produk.required' => 'Stok produk wajib diisi.',
            'stock_produk.min'      => 'Stok produk tidak boleh negatif.',
            'stock_produk.integer'  => 'Stok produk harus berupa angka bulat.',
            'deskripsi_produk.max'  => 'Deskripsi produk maksimal 500 karakter.', // ✅ PESAN ERROR
        ]);

        // ✅ Extra guard: pastikan stok tidak negatif
        if ((int) $request->stock_produk < 0) {
            return redirect()->back()
                ->with('error', 'Stok produk tidak boleh negatif!')
                ->withInput();
        }

        // ✅ Extra guard: pastikan harga tidak negatif
        if ((float) $request->harga_produk < 0) {
            return redirect()->back()
                ->with('error', 'Harga produk tidak boleh negatif!')
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $data = $request->except('gambar_produk');

            // Handle upload gambar
            if ($request->hasFile('gambar_produk')) {
                $file     = $request->file('gambar_produk');
                $filename = time() . '_' . $file->getClientOriginalName();

                Log::info("Uploading product image: " . $filename);

                $destinationPath = public_path('uploads/produk');

                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                    Log::info("Created directory: " . $destinationPath);
                }

                $file->move($destinationPath, $filename);
                Log::info("File moved to public: " . $filename);

                // Copy ke public_html
                $this->copyToPublicHtml($filename);

                $data['gambar_produk'] = $filename;
            }

            $produk = Produk::create($data);

            // ✅ Log deskripsi jika ada
            $keterangan = "Produk baru ditambahkan: {$produk->nama_produk}";
            if (!empty($produk->deskripsi_produk)) {
                $deskripsiShort = strlen($produk->deskripsi_produk) > 50
                    ? substr($produk->deskripsi_produk, 0, 50) . '...'
                    : $produk->deskripsi_produk;
                $keterangan .= " | Deskripsi: {$deskripsiShort}";
            }

            // Catat log produk baru
            $this->catatLog($produk->id_produk, 'tambah', [
                'stok_sebelum'     => 0,
                'stok_sesudah'     => $produk->stock_produk,
                'jumlah_perubahan' => $produk->stock_produk,
                'keterangan'       => $keterangan
            ]);

            DB::commit();
            Log::info("✅ Product created successfully with ID: {$produk->id_produk}");

            return redirect()->route('produk.index')
                ->with('success', 'Produk berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("❌ Failed to create product: " . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Gagal menambahkan produk: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        Log::info("=== Update Product ID: $id ===");
        Log::info("Request has file: " . ($request->hasFile('gambar_produk') ? 'YES' : 'NO'));
        Log::info("Hapus gambar: " . ($request->input('hapus_gambar') == '1' ? 'YES' : 'NO'));

        $request->validate([
            'nama_produk'        => 'required|string|max:150',
            'code_produk'        => 'nullable|string|max:50',
            'id_produk_kategori' => 'nullable|exists:produk_kategori,id_produk_kategori',
            'harga_produk'       => 'required|numeric|min:0',
            'stock_produk'       => 'required|integer|min:0',
            'gambar_produk'      => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'hapus_gambar'       => 'nullable|boolean',
            'deskripsi_produk'   => 'nullable|string|max:500', // ✅ VALIDASI DESKRIPSI
        ], [
            'nama_produk.required'  => 'Nama produk wajib diisi.',
            'harga_produk.required' => 'Harga produk wajib diisi.',
            'harga_produk.min'      => 'Harga produk tidak boleh negatif.',
            'stock_produk.required' => 'Stok produk wajib diisi.',
            'stock_produk.min'      => 'Stok produk tidak boleh negatif.',
            'stock_produk.integer'  => 'Stok produk harus berupa angka bulat.',
            'deskripsi_produk.max'  => 'Deskripsi produk maksimal 500 karakter.', // ✅ PESAN ERROR
        ]);

        // ✅ Extra guard: pastikan stok tidak negatif
        if ((int) $request->stock_produk < 0) {
            return redirect()->back()
                ->with('error', 'Stok produk tidak boleh negatif!')
                ->withInput();
        }

        // ✅ Extra guard: pastikan harga tidak negatif
        if ((float) $request->harga_produk < 0) {
            return redirect()->back()
                ->with('error', 'Harga produk tidak boleh negatif!')
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $produk = Produk::findOrFail($id);

            // Simpan data lama untuk logging
            $stokLama      = $produk->stock_produk;
            $hargaLama     = $produk->harga_produk;
            $deskripsiLama = $produk->deskripsi_produk; // ✅ SIMPAN DESKRIPSI LAMA

            $data = $request->except(['gambar_produk', 'hapus_gambar']);

            // Handle hapus gambar
            if ($request->input('hapus_gambar') == '1') {
                Log::info("Deleting old product image: " . $produk->gambar_produk);
                if ($produk->gambar_produk) {
                    $this->deleteFromBothLocations($produk->gambar_produk);
                }
                $data['gambar_produk'] = null;
            }
            // Handle upload gambar baru
            elseif ($request->hasFile('gambar_produk')) {
                // Hapus gambar lama dari kedua lokasi
                if ($produk->gambar_produk) {
                    Log::info("Deleting old product image before upload: " . $produk->gambar_produk);
                    $this->deleteFromBothLocations($produk->gambar_produk);
                }

                // Upload gambar baru
                $file     = $request->file('gambar_produk');
                $filename = time() . '_' . $file->getClientOriginalName();

                Log::info("Uploading new product image: " . $filename);

                $destinationPath = public_path('uploads/produk');

                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                    Log::info("Created directory: " . $destinationPath);
                }

                $file->move($destinationPath, $filename);
                Log::info("File moved to public: " . $filename);

                // Copy ke public_html
                $this->copyToPublicHtml($filename);

                $data['gambar_produk'] = $filename;
            }

            $produk->update($data);

            // Catat log perubahan
            $stokBaru      = $produk->stock_produk;
            $hargaBaru     = $produk->harga_produk;
            $deskripsiBaru = $produk->deskripsi_produk; // ✅ AMBIL DESKRIPSI BARU

            // Jika ada perubahan stok
            if ($stokLama != $stokBaru) {
                $selisih = $stokBaru - $stokLama;
                $jenis   = $selisih > 0 ? 'tambah_stok' : 'kurang_stok';

                $this->catatLog($produk->id_produk, $jenis, [
                    'stok_sebelum'     => $stokLama,
                    'stok_sesudah'     => $stokBaru,
                    'jumlah_perubahan' => abs($selisih),
                    'keterangan'       => "Update manual stok: " . ($selisih > 0 ? '+' : '') . $selisih . " unit"
                ]);

                Log::info("📊 Stok updated: {$stokLama} → {$stokBaru} (Selisih: {$selisih})");
            }
            // Jika hanya edit data tanpa perubahan stok
            else {
                $perubahanDetail = [];

                if ($hargaLama != $hargaBaru) {
                    $perubahanDetail[] = "Harga: Rp " . number_format($hargaLama, 0, ',', '.') .
                        " → Rp " . number_format($hargaBaru, 0, ',', '.');
                }

                // ✅ LOG PERUBAHAN DESKRIPSI
                if ($deskripsiLama != $deskripsiBaru) {
                    if (empty($deskripsiLama) && !empty($deskripsiBaru)) {
                        $perubahanDetail[] = "Deskripsi ditambahkan";
                    } elseif (!empty($deskripsiLama) && empty($deskripsiBaru)) {
                        $perubahanDetail[] = "Deskripsi dihapus";
                    } else {
                        $perubahanDetail[] = "Deskripsi diubah";
                    }
                }

                $keterangan = !empty($perubahanDetail)
                    ? "Update data produk: " . implode(', ', $perubahanDetail)
                    : "Update data produk";

                $this->catatLog($produk->id_produk, 'edit', [
                    'stok_sebelum' => $stokBaru,
                    'stok_sesudah' => $stokBaru,
                    'keterangan'   => $keterangan
                ]);

                Log::info("✏️ Product data updated (no stock change)");
            }

            DB::commit();
            Log::info("✅ Product updated successfully");

            return redirect()->route('produk.index')
                ->with('success', 'Produk berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("❌ Failed to update product: " . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Gagal memperbarui produk: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $produk     = Produk::findOrFail($id);
            $namaProduk = $produk->nama_produk;

            // Hapus gambar dari kedua lokasi jika ada
            if ($produk->gambar_produk) {
                $this->deleteFromBothLocations($produk->gambar_produk);
            }

            // Hapus produk (log akan terhapus otomatis karena cascade)
            $produk->delete();

            DB::commit();
            Log::info("✅ Product deleted successfully: {$namaProduk}");

            return redirect()->route('produk.index')
                ->with('success', 'Produk berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("❌ Failed to delete product: " . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan halaman log aktivitas produk
     */
    public function logs($id)
    {
        try {
            $produk = Produk::with('kategori')->findOrFail($id);

            $logs = ProdukLog::where('id_produk', $id)
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            $totalLogs       = ProdukLog::where('id_produk', $id)->count();
            $totalPenjualan  = ProdukLog::where('id_produk', $id)->where('jenis_aktivitas', 'penjualan')->count();
            $totalPenerimaan = ProdukLog::where('id_produk', $id)->whereIn('jenis_aktivitas', ['penerimaan', 'tambah_stok'])->count();
            $totalStokKeluar = ProdukLog::where('id_produk', $id)->where('jenis_aktivitas', 'kurang_stok')->count();

            Log::info("📋 Viewing logs for product: {$produk->nama_produk} (ID: {$id})");
            Log::info("📊 Total logs found: {$totalLogs}");

            return view('produk.logs', compact(
                'produk',
                'logs',
                'totalLogs',
                'totalPenjualan',
                'totalPenerimaan',
                'totalStokKeluar'
            ));
        } catch (\Exception $e) {
            Log::error("❌ Failed to load product logs: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());

            return redirect()->route('produk.index')
                ->with('error', 'Gagal memuat log produk: ' . $e->getMessage());
        }
    }

    public function showLog($id)
    {
        $produk = \App\Models\Produk::with('kategori')->findOrFail($id);

        $logs = \App\Models\ProdukLog::where('id_produk', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalLogs       = $logs->count();
        $totalPenjualan  = $logs->where('jenis_aktivitas', 'penjualan')->count();
        $totalPenerimaan = $logs->whereIn('jenis_aktivitas', ['penerimaan', 'tambah_stok'])->count();
        $totalStokKeluar = $logs->where('jenis_aktivitas', 'penjualan')->count();

        return view('produk.log', compact('produk', 'logs', 'totalLogs', 'totalPenjualan', 'totalPenerimaan', 'totalStokKeluar'));
    }
}
