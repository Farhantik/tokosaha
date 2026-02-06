<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    /**
     * Nama tabel di database
     */
    protected $table = 'penjualan';

    /**
     * Primary key tabel
     */
    protected $primaryKey = 'id_penjualan';

    /**
     * Timestamps
     */
    public $timestamps = false;

    /**
     * Field yang bisa diisi
     */
    protected $fillable = [
        'id_kasir',
        'tanggal_penjualan',
        'total_bayar',
        'total_pembayaran',
        'kembalian_pembayaran'
    ];

    /**
     * Cast attributes
     */
    protected $casts = [
        'tanggal_penjualan' => 'datetime',
        'total_bayar' => 'decimal:2',
        'total_pembayaran' => 'decimal:2',
        'kembalian_pembayaran' => 'decimal:2'
    ];

    /**
     * Relasi ke kasir (Many to One)
     */
    public function kasir()
    {
        return $this->belongsTo(Kasir::class, 'id_kasir', 'id_kasir');
    }

    /**
     * Relasi ke detail penjualan (One to Many)
     */
    public function detail()
    {
        return $this->hasMany(PenjualanDetail::class, 'id_penjualan', 'id_penjualan');
    }

    /**
     * Relasi ke keuangan (One to Many)
     */
    public function keuangan()
    {
        return $this->hasMany(Keuangan::class, 'id_penjualan', 'id_penjualan');
    }

    /**
     * Relasi ke log stock (One to Many)
     */
    public function logStock()
    {
        return $this->hasMany(LogStock::class, 'id_aktivitas', 'id_penjualan')
            ->where('jenis_aktivitas', 'PENJUALAN');
    }

    /**
     * Accessor untuk nomor transaksi
     */
    public function getNomorTransaksiAttribute()
    {
        return 'TRX-' . str_pad($this->id_penjualan, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Accessor untuk format total pembayaran
     */
    public function getTotalFormatAttribute()
    {
        return 'Rp ' . number_format($this->total_pembayaran, 0, ',', '.');
    }

    /**
     * Accessor untuk format total bayar
     */
    public function getTotalBayarFormatAttribute()
    {
        return 'Rp ' . number_format($this->total_bayar, 0, ',', '.');
    }

    /**
     * Accessor untuk format kembalian
     */
    public function getKembalianFormatAttribute()
    {
        return 'Rp ' . number_format($this->kembalian_pembayaran, 0, ',', '.');
    }

    /**
     * Accessor untuk jumlah item
     */
    public function getJumlahItemAttribute()
    {
        return $this->detail()->count();
    }

    /**
     * Accessor untuk total qty
     */
    public function getTotalQtyAttribute()
    {
        return $this->detail()->sum('qty_produk');
    }

    /**
     * Accessor untuk nama kasir
     */
    public function getNamaKasirAttribute()
    {
        return $this->kasir?->user?->nama_user ?? '-';
    }

    /**
     * Scope untuk penjualan hari ini
     */
    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal_penjualan', today());
    }

    /**
     * Scope untuk penjualan bulan ini
     */
    public function scopeBulanIni($query)
    {
        return $query->whereMonth('tanggal_penjualan', now()->month)
            ->whereYear('tanggal_penjualan', now()->year);
    }

    /**
     * Scope untuk penjualan by kasir
     */
    public function scopeByKasir($query, $idKasir)
    {
        return $query->where('id_kasir', $idKasir);
    }

    /**
     * Scope untuk penjualan dengan range tanggal
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_penjualan', [$startDate, $endDate]);
    }

    /**
     * Static method untuk hitung total dari detail
     */
    public static function hitungTotal($detail)
    {
        $total = 0;
        foreach ($detail as $item) {
            $total += ($item['harga_produk'] * $item['qty_produk']);
        }
        return $total;
    }

    /**
     * Boot method untuk auto events
     */
    protected static function boot()
    {
        parent::boot();

        // Auto calculate kembalian sebelum save
        static::saving(function ($penjualan) {
            if ($penjualan->total_bayar && $penjualan->total_pembayaran) {
                $penjualan->kembalian_pembayaran = $penjualan->total_bayar - $penjualan->total_pembayaran;
            }
        });

        // Auto update total_pembayaran saat ada detail baru
        static::saved(function ($penjualan) {
            if ($penjualan->detail()->exists()) {
                $total = $penjualan->detail()
                    ->selectRaw('SUM(subtotal_harga) as total')
                    ->value('total');

                if ($total && $total != $penjualan->total_pembayaran) {
                    $penjualan->updateQuietly(['total_pembayaran' => $total]);
                }
            }
        });
    }
}
