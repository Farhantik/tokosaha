<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penerimaan extends Model
{
    /**
     * Nama tabel di database
     */
    protected $table = 'penerimaan';

    /**
     * Primary key tabel
     */
    protected $primaryKey = 'id_penerimaan';

    /**
     * Timestamps
     */
    public $timestamps = false;

    /**
     * Field yang bisa diisi
     */
    protected $fillable = [
        'id_supplier',
        'tanggal_penerimaan',
        'total_harga',
        'id_metode_pembayaran'
    ];

    /**
     * Cast attributes
     */
    protected $casts = [
        'tanggal_penerimaan' => 'datetime',
        'total_harga' => 'decimal:2'
    ];

    /**
     * Relasi ke supplier (Many to One)
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id_supplier');
    }

    /**
     * Relasi ke detail penerimaan (One to Many)
     */
    public function detail()
    {
        return $this->hasMany(PenerimaanDetail::class, 'id_penerimaan', 'id_penerimaan');
    }

    /**
     * Relasi ke keuangan (One to Many)
     */
    public function keuangan()
    {
        return $this->hasMany(Keuangan::class, 'id_penerimaan', 'id_penerimaan');
    }

    /**
     * Relasi ke log stock (One to Many)
     */
    public function logStock()
    {
        return $this->hasMany(LogStock::class, 'id_aktivitas', 'id_penerimaan')
            ->where('jenis_aktivitas', 'PENERIMAAN');
    }

    /**
     * Accessor untuk format total harga
     */
    public function getTotalFormatAttribute()
    {
        return 'Rp ' . number_format($this->total_harga, 0, ',', '.');
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
     * Accessor untuk nomor penerimaan
     */
    public function getNomorPenerimaanAttribute()
    {
        return 'PNM-' . str_pad($this->id_penerimaan, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Scope untuk penerimaan hari ini
     */
    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal_penerimaan', today());
    }

    /**
     * Scope untuk penerimaan bulan ini
     */
    public function scopeBulanIni($query)
    {
        return $query->whereMonth('tanggal_penerimaan', now()->month)
            ->whereYear('tanggal_penerimaan', now()->year);
    }

    /**
     * Scope untuk penerimaan by supplier
     */
    public function scopeBySupplier($query, $idSupplier)
    {
        return $query->where('id_supplier', $idSupplier);
    }

    /**
     * Scope untuk penerimaan dengan range tanggal
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_penerimaan', [$startDate, $endDate]);
    }

    /**
     * Static method untuk hitung total penerimaan
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
     * Boot method untuk auto calculate total
     */
    protected static function boot()
    {
        parent::boot();

        // Auto calculate total saat ada detail baru
        static::saved(function ($penerimaan) {
            if ($penerimaan->detail()->exists()) {
                $total = $penerimaan->detail()
                    ->selectRaw('SUM(subtotal_harga) as total')
                    ->value('total');

                if ($total && $total != $penerimaan->total_harga) {
                    $penerimaan->updateQuietly(['total_harga' => $total]);
                }
            }
        });
    }
}
