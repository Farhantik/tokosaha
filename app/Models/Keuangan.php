<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keuangan extends Model
{
    /**
     * Nama tabel di database
     */
    protected $table = 'keuangan';

    /**
     * Primary key tabel
     */
    protected $primaryKey = 'id_keuangan';

    /**
     * Timestamps
     */
    public $timestamps = false;

    /**
     * Field yang bisa diisi
     */
    protected $fillable = [
        'id_penjualan',
        'id_penerimaan',
        'id_kasir',
        'id_keuangan_jenis',
        'total_keuangan'
    ];

    /**
     * Cast attributes
     */
    protected $casts = [
        'total_keuangan' => 'decimal:2'
    ];

    /**
     * Relasi ke penjualan (Many to One)
     */
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'id_penjualan', 'id_penjualan');
    }

    /**
     * Relasi ke penerimaan (Many to One)
     */
    public function penerimaan()
    {
        return $this->belongsTo(Penerimaan::class, 'id_penerimaan', 'id_penerimaan');
    }

    /**
     * Relasi ke kasir (Many to One)
     */
    public function kasir()
    {
        return $this->belongsTo(Kasir::class, 'id_kasir', 'id_kasir');
    }

    /**
     * Relasi ke jenis keuangan (Many to One)
     */
    public function jenis()
    {
        return $this->belongsTo(KeuanganJenis::class, 'id_keuangan_jenis', 'id_keuangan_jenis');
    }

    /**
     * Accessor untuk format total
     */
    public function getTotalFormatAttribute()
    {
        return 'Rp ' . number_format($this->total_keuangan, 0, ',', '.');
    }

    /**
     * Accessor untuk tipe transaksi (masuk/keluar)
     */
    public function getTipeTransaksiAttribute()
    {
        // Penjualan = uang masuk, Penerimaan = uang keluar
        if ($this->id_penjualan) {
            return 'masuk';
        } elseif ($this->id_penerimaan) {
            return 'keluar';
        }
        return 'lainnya';
    }

    /**
     * Accessor untuk badge color
     */
    public function getBadgeColorAttribute()
    {
        return $this->tipe_transaksi === 'masuk' ? 'green' : 'red';
    }

    /**
     * Accessor untuk icon
     */
    public function getIconAttribute()
    {
        return $this->tipe_transaksi === 'masuk' ? 'arrow-down' : 'arrow-up';
    }

    /**
     * Accessor untuk keterangan
     */
    public function getKeteranganAttribute()
    {
        if ($this->id_penjualan) {
            return 'Penjualan #' . $this->id_penjualan;
        } elseif ($this->id_penerimaan) {
            return 'Pembelian #' . $this->id_penerimaan;
        }
        return $this->jenis?->jenis_keuangan ?? '-';
    }

    /**
     * Scope untuk transaksi masuk
     */
    public function scopeMasuk($query)
    {
        return $query->whereNotNull('id_penjualan');
    }

    /**
     * Scope untuk transaksi keluar
     */
    public function scopeKeluar($query)
    {
        return $query->whereNotNull('id_penerimaan');
    }

    /**
     * Scope untuk by kasir
     */
    public function scopeByKasir($query, $idKasir)
    {
        return $query->where('id_kasir', $idKasir);
    }

    /**
     * Scope untuk by jenis
     */
    public function scopeByJenis($query, $idJenis)
    {
        return $query->where('id_keuangan_jenis', $idJenis);
    }

    /**
     * Scope untuk hari ini
     */
    public function scopeHariIni($query)
    {
        return $query->whereHas('penjualan', function ($q) {
            $q->whereDate('tanggal_penjualan', today());
        })->orWhereHas('penerimaan', function ($q) {
            $q->whereDate('tanggal_penerimaan', today());
        });
    }

    /**
     * Scope untuk bulan ini
     */
    public function scopeBulanIni($query)
    {
        return $query->whereHas('penjualan', function ($q) {
            $q->whereMonth('tanggal_penjualan', now()->month)
                ->whereYear('tanggal_penjualan', now()->year);
        })->orWhereHas('penerimaan', function ($q) {
            $q->whereMonth('tanggal_penerimaan', now()->month)
                ->whereYear('tanggal_penerimaan', now()->year);
        });
    }

    /**
     * Static method untuk catat keuangan dari penjualan
     */
    public static function catatPenjualan($penjualan)
    {
        return self::create([
            'id_penjualan' => $penjualan->id_penjualan,
            'id_kasir' => $penjualan->id_kasir,
            'id_keuangan_jenis' => 1, // ID untuk "Penjualan"
            'total_keuangan' => $penjualan->total_pembayaran
        ]);
    }

    /**
     * Static method untuk catat keuangan dari penerimaan
     */
    public static function catatPenerimaan($penerimaan, $idKasir = null)
    {
        return self::create([
            'id_penerimaan' => $penerimaan->id_penerimaan,
            'id_kasir' => $idKasir,
            'id_keuangan_jenis' => 2, // ID untuk "Pembelian"
            'total_keuangan' => $penerimaan->total_harga
        ]);
    }

    /**
     * Static method untuk hitung saldo
     */
    public static function hitungSaldo($idKasir = null)
    {
        $query = self::query();

        if ($idKasir) {
            $query->where('id_kasir', $idKasir);
        }

        $masuk = $query->clone()->masuk()->sum('total_keuangan');
        $keluar = $query->clone()->keluar()->sum('total_keuangan');

        return [
            'masuk' => $masuk,
            'keluar' => $keluar,
            'saldo' => $masuk - $keluar
        ];
    }
}
