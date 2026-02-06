<?php

namespace App\Observers;

use App\Models\Produk;
use App\Models\ProdukLog;

class ProdukObserver
{
    /**
     * Handle the Produk "created" event.
     */
    public function created(Produk $produk): void
    {
        ProdukLog::create([
            'id_produk' => $produk->id_produk,
            'jenis_aktivitas' => 'tambah',
            'stok_sebelum' => 0,
            'stok_sesudah' => $produk->stock_produk,
            'jumlah_perubahan' => $produk->stock_produk,
            'harga_saat_itu' => $produk->harga_produk,
            'keterangan' => 'Produk baru ditambahkan: ' . $produk->nama_produk,
            'user_nama' => auth()->user()->nama_user ?? 'System'
        ]);
    }

    /**
     * Handle the Produk "updated" event.
     */
    public function updated(Produk $produk): void
    {
        // Cek apakah ada perubahan stok
        if ($produk->isDirty('stock_produk')) {
            $stokLama = $produk->getOriginal('stock_produk');
            $stokBaru = $produk->stock_produk;
            $selisih = $stokBaru - $stokLama;
            
            // Tentukan jenis aktivitas
            $jenis = $selisih > 0 ? 'tambah_stok' : 'kurang_stok';
            
            ProdukLog::create([
                'id_produk' => $produk->id_produk,
                'jenis_aktivitas' => $jenis,
                'stok_sebelum' => $stokLama,
                'stok_sesudah' => $stokBaru,
                'jumlah_perubahan' => abs($selisih),
                'harga_saat_itu' => $produk->harga_produk,
                'keterangan' => 'Update stok produk: ' . ($selisih > 0 ? '+' : '') . $selisih,
                'user_nama' => auth()->user()->nama_user ?? 'System'
            ]);
        } 
        // Jika ada perubahan selain stok
        elseif ($produk->isDirty()) {
            ProdukLog::create([
                'id_produk' => $produk->id_produk,
                'jenis_aktivitas' => 'edit',
                'stok_sebelum' => $produk->stock_produk,
                'stok_sesudah' => $produk->stock_produk,
                'harga_saat_itu' => $produk->harga_produk,
                'keterangan' => 'Update data produk',
                'user_nama' => auth()->user()->nama_user ?? 'System'
            ]);
        }
    }

    /**
     * Handle the Produk "deleted" event.
     */
    public function deleted(Produk $produk): void
    {
        // Log akan otomatis terhapus karena foreign key cascade
    }
}