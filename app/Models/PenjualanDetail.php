<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenjualanDetail extends Model
{
    /**
     * Nama tabel di database
     */
    protected $table = 'penjualan_detail';

    /**
     * Primary key tabel
     */
    protected $primaryKey = 'id_detail_penjualan';

    /**
     * Timestamps
     */
    public $timestamps = false;

    /**
     * Field yang bisa diisi
     */
    protected $fillable = [
        'id_penjualan',
        'id_produk',
        'harga_produk',
        'qty_produk',
        'subtotal_harga'
    ];

    /**
     * Cast attributes
     */
    protected $casts = [
        'harga_produk' => 'decimal:2',
        'subtotal_harga' => 'decimal:2',
        'qty_produk' => 'integer'
    ];

    /**
     * Relasi ke penjualan (Many to One)
     */
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'id_penjualan', 'id_penjualan');
    }

    /**
     * Relasi ke produk (Many to One)
     */
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    /**
     * Accessor untuk format harga
     */
    public function getHargaFormatAttribute()
    {
        return 'Rp ' . number_format($this->harga_produk, 0, ',', '.');
    }

    /**
     * Accessor untuk format subtotal
     */
    public function getSubtotalFormatAttribute()
    {
        return 'Rp ' . number_format($this->subtotal_harga, 0, ',', '.');
    }

    /**
     * Accessor untuk total harga per item
     */
    public function getTotalHargaAttribute()
    {
        return $this->harga_produk * $this->qty_produk;
    }

    /**
     * Scope untuk detail by penjualan
     */
    public function scopeByPenjualan($query, $idPenjualan)
    {
        return $query->where('id_penjualan', $idPenjualan);
    }

    /**
     * Scope untuk detail by produk
     */
    public function scopeByProduk($query, $idProduk)
    {
        return $query->where('id_produk', $idProduk);
    }

    /**
     * Boot method untuk auto events
     */
    protected static function boot()
    {
        parent::boot();

        // Auto calculate subtotal sebelum save
        static::saving(function ($detail) {
            if ($detail->harga_produk && $detail->qty_produk) {
                $detail->subtotal_harga = $detail->harga_produk * $detail->qty_produk;
            }
        });

        // Kurangi stok setelah created (jika trigger database tidak aktif)
        static::created(function ($detail) {
            // Uncomment jika trigger database tidak aktif
            // $produk = $detail->produk;
            // $produk->decrement('stock_produk', $detail->qty_produk);

            // Log stock
            // LogStock::catat(
            //     idProduk: $detail->id_produk,
            //     jenisAktivitas: 'PENJUALAN',
            //     idAktivitas: $detail->id_penjualan,
            //     jumlah: $detail->qty_produk,
            //     jumlahAwal: $produk->stock_produk + $detail->qty_produk,
            //     jumlahAkhir: $produk->stock_produk
            // );
        });

        // Kembalikan stok setelah deleted (opsional)
        static::deleted(function ($detail) {
            // Uncomment jika ingin auto return stok saat hapus detail
            // $detail->produk->increment('stock_produk', $detail->qty_produk);
        });
    }
}
