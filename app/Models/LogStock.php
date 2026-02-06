<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogStock extends Model
{
    /**
     * Nama tabel di database
     */
    protected $table = 'log_stock';

    /**
     * Primary key tabel
     */
    protected $primaryKey = 'id_log';

    /**
     * Timestamps
     */
    public $timestamps = false;

    /**
     * Field yang bisa diisi
     */
    protected $fillable = [
        'id_aktivitas',
        'id_produk',
        'jenis_aktivitas',
        'jumlah_aktivitas',
        'jumlah_awal',
        'jumlah_akhir'
    ];

    /**
     * Cast attributes
     */
    protected $casts = [
        'jumlah_aktivitas' => 'integer',
        'jumlah_awal' => 'integer',
        'jumlah_akhir' => 'integer'
    ];

    /**
     * Relasi ke produk (Many to One)
     */
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    /**
     * Relasi polymorphic ke penjualan atau penerimaan
     * Karena id_aktivitas bisa merujuk ke id_penjualan atau id_penerimaan
     */
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'id_aktivitas', 'id_penjualan')
            ->where('jenis_aktivitas', 'PENJUALAN');
    }

    public function penerimaan()
    {
        return $this->belongsTo(Penerimaan::class, 'id_aktivitas', 'id_penerimaan')
            ->where('jenis_aktivitas', 'PENERIMAAN');
    }

    /**
     * Accessor untuk jenis aktivitas label
     */
    public function getJenisLabelAttribute()
    {
        return $this->jenis_aktivitas === 'PENJUALAN' ? 'Penjualan' : 'Penerimaan Barang';
    }

    /**
     * Accessor untuk status perubahan (in/out)
     */
    public function getStatusPerubahanAttribute()
    {
        return $this->jenis_aktivitas === 'PENJUALAN' ? 'out' : 'in';
    }

    /**
     * Accessor untuk warna badge
     */
    public function getBadgeColorAttribute()
    {
        return $this->jenis_aktivitas === 'PENJUALAN' ? 'red' : 'green';
    }

    /**
     * Scope untuk log penjualan
     */
    public function scopePenjualan($query)
    {
        return $query->where('jenis_aktivitas', 'PENJUALAN');
    }

    /**
     * Scope untuk log penerimaan
     */
    public function scopePenerimaan($query)
    {
        return $query->where('jenis_aktivitas', 'PENERIMAAN');
    }

    /**
     * Scope untuk log by produk
     */
    public function scopeByProduk($query, $idProduk)
    {
        return $query->where('id_produk', $idProduk);
    }

    /**
     * Scope untuk log hari ini
     */
    public function scopeHariIni($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Static method untuk catat log stok
     */
    public static function catat($idProduk, $jenisAktivitas, $idAktivitas, $jumlah, $jumlahAwal, $jumlahAkhir)
    {
        return self::create([
            'id_produk' => $idProduk,
            'jenis_aktivitas' => $jenisAktivitas,
            'id_aktivitas' => $idAktivitas,
            'jumlah_aktivitas' => $jumlah,
            'jumlah_awal' => $jumlahAwal,
            'jumlah_akhir' => $jumlahAkhir
        ]);
    }
}
