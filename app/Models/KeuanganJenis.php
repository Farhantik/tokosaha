<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeuanganJenis extends Model
{
    /**
     * Nama tabel di database
     */
    protected $table = 'keuangan_jenis';

    /**
     * Primary key tabel
     */
    protected $primaryKey = 'id_keuangan_jenis';

    /**
     * Timestamps
     */
    public $timestamps = false;

    /**
     * Field yang bisa diisi
     */
    protected $fillable = [
        'jenis_keuangan'
    ];

    /**
     * Relasi ke keuangan (One to Many)
     */
    public function keuangan()
    {
        return $this->hasMany(Keuangan::class, 'id_keuangan_jenis', 'id_keuangan_jenis');
    }

    /**
     * Accessor untuk jumlah transaksi
     */
    public function getJumlahTransaksiAttribute()
    {
        return $this->keuangan()->count();
    }

    /**
     * Accessor untuk total transaksi
     */
    public function getTotalTransaksiAttribute()
    {
        return $this->keuangan()->sum('total_keuangan');
    }
}
