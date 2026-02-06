<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    /**
     * Nama tabel di database
     */
    protected $table = 'produk';

    /**
     * Primary key tabel
     */
    protected $primaryKey = 'id_produk';

    /**
     * Timestamps
     */
    public $timestamps = false;

    /**
     * Field yang bisa diisi
     */
    protected $fillable = [
        'id_produk_kategori',
        'nama_produk',
        'harga_produk',
        'stock_produk',
        'code_produk',
        'gambar_produk'
    ];

    /**
     * Cast attributes
     */
    protected $casts = [
        'harga_produk' => 'decimal:2',
        'stock_produk' => 'integer'
    ];

    /**
     * Relasi ke kategori produk (Many to One)
     */
    public function kategori()
    {
        return $this->belongsTo(ProdukKategori::class, 'id_produk_kategori', 'id_produk_kategori');
    }

    /**
     * Relasi ke penjualan detail (One to Many)
     */
    public function detailPenjualan()
    {
        return $this->hasMany(PenjualanDetail::class, 'id_produk', 'id_produk');
    }

    /**
     * Relasi ke penerimaan detail (One to Many)
     */
    public function detailPenerimaan()
    {
        return $this->hasMany(PenerimaanDetail::class, 'id_produk', 'id_produk');
    }

    /**
     * Relasi ke log stock (One to Many)
     */
    public function logStock()
    {
        return $this->hasMany(LogStock::class, 'id_produk', 'id_produk');
    }

    /**
     * Relasi ke produk logs (One to Many) - NEW
     * Untuk tracking aktivitas produk dari aplikasi
     */
    public function logs()
    {
        return $this->hasMany(ProdukLog::class, 'id_produk', 'id_produk')
                    ->orderBy('created_at', 'desc');
    }

    /**
     * Get latest logs - NEW
     * Helper untuk mengambil log terbaru
     */
    public function latestLogs($limit = 5)
    {
        return $this->logs()->limit($limit)->get();
    }

    /**
     * Accessor untuk format harga
     */
    public function getHargaFormatAttribute()
    {
        return 'Rp ' . number_format($this->harga_produk, 0, ',', '.');
    }

    /**
     * Accessor untuk status stok
     */
    public function getStatusStokAttribute()
    {
        if ($this->stock_produk == 0) {
            return 'habis';
        } elseif ($this->stock_produk < 10) {
            return 'menipis';
        } else {
            return 'aman';
        }
    }

    /**
     * Accessor untuk badge color status stok - NEW
     */
    public function getBadgeStokColorAttribute()
    {
        return match($this->status_stok) {
            'habis' => 'red',
            'menipis' => 'orange',
            'aman' => 'green',
            default => 'gray'
        };
    }

    /**
     * Accessor untuk icon status stok - NEW
     */
    public function getIconStokAttribute()
    {
        return match($this->status_stok) {
            'habis' => 'fa-times-circle',
            'menipis' => 'fa-exclamation-triangle',
            'aman' => 'fa-check-circle',
            default => 'fa-info-circle'
        };
    }

    /**
     * Scope untuk produk dengan stok menipis
     */
    public function scopeStokMenupis($query)
    {
        return $query->where('stock_produk', '<', 10)->where('stock_produk', '>', 0);
    }

    /**
     * Scope untuk produk habis
     */
    public function scopeStokHabis($query)
    {
        return $query->where('stock_produk', '=', 0);
    }

    /**
     * Scope untuk produk berdasarkan kategori
     */
    public function scopeByKategori($query, $idKategori)
    {
        return $query->where('id_produk_kategori', $idKategori);
    }

    /**
     * Scope untuk produk dengan logs - NEW
     */
    public function scopeWithLogs($query, $limit = 10)
    {
        return $query->with(['logs' => function($q) use ($limit) {
            $q->orderBy('created_at', 'desc')->limit($limit);
        }]);
    }

    /**
     * Get total aktivitas produk - NEW
     */
    public function getTotalAktivitasAttribute()
    {
        return $this->logs()->count();
    }

    /**
     * Get aktivitas terakhir - NEW
     */
    public function getAktivitasTerakhirAttribute()
    {
        $log = $this->logs()->first();
        return $log ? $log->created_at->diffForHumans() : 'Belum ada aktivitas';
    }
}