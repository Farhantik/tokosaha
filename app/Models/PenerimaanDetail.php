<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenerimaanDetail extends Model
{
    /**
     * Nama tabel di database
     */
    protected $table = 'penerimaan_detail';

    /**
     * Primary key tabel
     */
    protected $primaryKey = 'id_penerimaan_detail';

    /**
     * Timestamps
     */
    public $timestamps = false;

    /**
     * Field yang bisa diisi
     */
    protected $fillable = [
        'id_penerimaan',
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
        'qty_produk' => 'integer',
        'subtotal_harga' => 'decimal:2'
    ];

    /**
     * Relasi ke penerimaan (Many to One)
     */
    public function penerimaan()
    {
        return $this->belongsTo(Penerimaan::class, 'id_penerimaan', 'id_penerimaan');
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
     * Scope untuk detail by penerimaan
     */
    public function scopeByPenerimaan($query, $idPenerimaan)
    {
        return $query->where('id_penerimaan', $idPenerimaan);
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

        // Tambah stok setelah created (jika trigger database tidak aktif)
        static::created(function ($detail) {
            // Uncomment jika trigger database tidak aktif
            // $produk = $detail->produk;
            // $produk->increment('stock_produk', $detail->qty_produk);

            // Log stock
            // LogStock::catat(
            //     idProduk: $detail->id_produk,
            //     jenisAktivitas: 'PENERIMAAN',
            //     idAktivitas: $detail->id_penerimaan,
            //     jumlah: $detail->qty_produk,
            //     jumlahAwal: $produk->stock_produk - $detail->qty_produk,
            //     jumlahAkhir: $produk->stock_produk
            // );
        });

        // Kurangi stok setelah deleted (opsional - untuk rollback)
        static::deleted(function ($detail) {
            // Uncomment jika ingin auto kurangi stok saat hapus detail
            // $detail->produk->decrement('stock_produk', $detail->qty_produk);
        });
    }
}
