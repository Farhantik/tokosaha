<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kasir extends Model
{
    /**
     * Nama tabel di database
     */
    protected $table = 'kasir';

    /**
     * Primary key tabel
     */
    protected $primaryKey = 'id_kasir';

    /**
     * Timestamps
     */
    public $timestamps = false;

    /**
     * Field yang bisa diisi
     */
    protected $fillable = [
        'id_user',
        'modal_awal',
        'waktu_open',
        'saldo_akhir',
        'waktu_close'
    ];

    /**
     * Cast attributes
     */
    protected $casts = [
        'waktu_open' => 'datetime',
        'waktu_close' => 'datetime',
        'modal_awal' => 'decimal:2',
        'saldo_akhir' => 'decimal:2'
    ];

    /**
     * Relasi ke user (Many to One)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Relasi ke penjualan (One to Many)
     */
    public function penjualan()
    {
        return $this->hasMany(Penjualan::class, 'id_kasir', 'id_kasir');
    }

    /**
     * Relasi ke keuangan (One to Many)
     */
    public function keuangan()
    {
        return $this->hasMany(Keuangan::class, 'id_kasir', 'id_kasir');
    }

    /**
     * Accessor untuk status kasir
     */
    public function getStatusAttribute()
    {
        return $this->waktu_close === null ? 'open' : 'close';
    }

    /**
     * Accessor untuk total penjualan
     */
    public function getTotalPenjualanAttribute()
    {
        return $this->penjualan()->sum('total_pembayaran');
    }

    /**
     * Accessor untuk jumlah transaksi
     */
    public function getJumlahTransaksiAttribute()
    {
        return $this->penjualan()->count();
    }

    /**
     * Accessor untuk durasi operasional
     */
    public function getDurasiAttribute()
    {
        if (!$this->waktu_open) {
            return null;
        }

        $end = $this->waktu_close ?? now();
        return $this->waktu_open->diffForHumans($end, true);
    }

    /**
     * Accessor untuk selisih (untung/rugi)
     */
    public function getSelisihAttribute()
    {
        if (!$this->saldo_akhir) {
            return null;
        }

        return $this->saldo_akhir - $this->modal_awal;
    }

    /**
     * Scope untuk kasir aktif (belum ditutup)
     */
    public function scopeAktif($query)
    {
        return $query->whereNull('waktu_close');
    }

    /**
     * Scope untuk kasir sudah ditutup
     */
    public function scopeTutup($query)
    {
        return $query->whereNotNull('waktu_close');
    }

    /**
     * Scope untuk kasir berdasarkan user
     */
    public function scopeByUser($query, $idUser)
    {
        return $query->where('id_user', $idUser);
    }

    /**
     * Scope untuk kasir hari ini
     */
    public function scopeHariIni($query)
    {
        return $query->whereDate('waktu_open', today());
    }

    /**
     * Method untuk tutup kasir
     */
    public function tutup()
    {
        $totalPenjualan = $this->penjualan()->sum('total_pembayaran');
        $saldoAkhir = $this->modal_awal + $totalPenjualan;

        $this->update([
            'saldo_akhir' => $saldoAkhir,
            'waktu_close' => now()
        ]);

        return $this;
    }
}
