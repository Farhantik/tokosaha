<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukLog extends Model
{
    use HasFactory;

    protected $table = 'produk_logs';
    protected $primaryKey = 'id_log';

    protected $fillable = [
        'id_produk',
        'jenis_aktivitas',
        'stok_sebelum',
        'stok_sesudah',
        'jumlah_perubahan',
        'harga_saat_itu',
        'keterangan',
        'id_penjualan',
        'id_penerimaan',
        'user_nama'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    // Scopes
    public function scopePenjualan($query)
    {
        return $query->where('jenis_aktivitas', 'penjualan');
    }

    public function scopePenerimaan($query)
    {
        return $query->where('jenis_aktivitas', 'penerimaan');
    }

    public function scopeStokKeluar($query)
    {
        return $query->where('jenis_aktivitas', 'kurang_stok');
    }

    // Helper untuk icon aktivitas
    public function getIconAttribute()
    {
        $icons = [
            'tambah_stok' => 'fa-arrow-up',
            'kurang_stok' => 'fa-arrow-down',
            'penjualan' => 'fa-shopping-cart',
            'edit' => 'fa-edit',
            'tambah' => 'fa-plus-circle',
            'penerimaan' => 'fa-box'
        ];
        
        return $icons[$this->jenis_aktivitas] ?? 'fa-info-circle';
    }

    // Helper untuk warna badge
    public function getBadgeColorAttribute()
    {
        $colors = [
            'tambah_stok' => 'green',
            'tambah' => 'green',
            'penerimaan' => 'green',
            'kurang_stok' => 'orange',
            'penjualan' => 'blue',
            'edit' => 'yellow'
        ];
        
        return $colors[$this->jenis_aktivitas] ?? 'gray';
    }
}