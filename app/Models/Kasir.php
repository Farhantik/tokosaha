<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kasir extends Model
{
    protected $table      = 'kasir';
    protected $primaryKey = 'id_kasir';
    public    $timestamps = false;

    protected $fillable = [
        'id_user',
        'modal_awal',
        'waktu_open',
        'saldo_akhir',
        'waktu_close',
        'is_auto_closed', // ← kolom baru
    ];

    protected $casts = [
        'waktu_open'     => 'datetime',
        'waktu_close'    => 'datetime',
        'modal_awal'     => 'decimal:2',
        'saldo_akhir'    => 'decimal:2',
        'is_auto_closed' => 'boolean',  // ← cast baru
    ];

    // ═══════════════════════════════════════════
    // RELASI
    // ═══════════════════════════════════════════

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class, 'id_kasir', 'id_kasir')
            ->whereNull('deleted_at');
    }

    public function keuangan()
    {
        return $this->hasMany(Keuangan::class, 'id_kasir', 'id_kasir');
    }

    // ═══════════════════════════════════════════
    // ACCESSORS
    // ═══════════════════════════════════════════

    /** Status kasir: 'open' atau 'closed' */
    public function getStatusAttribute(): string
    {
        return is_null($this->waktu_close) ? 'open' : 'closed';
    }

    /** Apakah kasir masih aktif */
    public function getIsActiveAttribute(): bool
    {
        return is_null($this->waktu_close);
    }

    /** Total penjualan selama sesi (tidak termasuk yang dihapus) */
    public function getTotalPenjualanAttribute()
    {
        return $this->penjualan()->sum('total_pembayaran');
    }

    /** Jumlah transaksi selama sesi */
    public function getJumlahTransaksiAttribute(): int
    {
        return $this->penjualan()->count();
    }

    /** Estimasi saldo akhir = modal_awal + total_penjualan */
    public function getEstimasiSaldoAttribute()
    {
        return $this->modal_awal + $this->total_penjualan;
    }

    /** Durasi sesi kasir */
    public function getDurasiAttribute(): ?string
    {
        if (!$this->waktu_open) {
            return null;
        }

        $end = $this->waktu_close ?? now();
        return $this->waktu_open->diffForHumans($end, true);
    }

    /** Selisih saldo akhir vs modal awal */
    public function getSelisihAttribute()
    {
        if (!$this->saldo_akhir) {
            return null;
        }

        return $this->saldo_akhir - $this->modal_awal;
    }

    // ═══════════════════════════════════════════
    // SCOPES
    // ═══════════════════════════════════════════

    /** Kasir yang masih aktif (belum ditutup) */
    public function scopeAktif($query)
    {
        return $query->whereNull('waktu_close');
    }

    /** Kasir yang sudah ditutup */
    public function scopeTutup($query)
    {
        return $query->whereNotNull('waktu_close');
    }

    /** Filter berdasarkan user */
    public function scopeByUser($query, int $idUser)
    {
        return $query->where('id_user', $idUser);
    }

    /** Kasir yang dibuka hari ini */
    public function scopeHariIni($query)
    {
        return $query->whereDate('waktu_open', today());
    }

    /** Kasir yang ditutup otomatis */
    public function scopeAutoClose($query)
    {
        return $query->where('is_auto_closed', true);
    }

    // ═══════════════════════════════════════════
    // METHODS
    // ═══════════════════════════════════════════

    /**
     * Tutup kasir secara MANUAL.
     * Saldo akhir dihitung otomatis: modal_awal + total_penjualan.
     */
    public function tutup(): static
    {
        $totalPenjualan = $this->penjualan()->sum('total_pembayaran');

        $this->update([
            'saldo_akhir'    => $this->modal_awal + $totalPenjualan,
            'waktu_close'    => now(),
            'is_auto_closed' => false,
        ]);

        return $this;
    }

    /**
     * Tutup kasir secara OTOMATIS oleh sistem / scheduler.
     * Saldo akhir dihitung otomatis: modal_awal + total_penjualan.
     */
    public function tutupOtomatis(): static
    {
        $totalPenjualan = $this->penjualan()->sum('total_pembayaran');

        $this->update([
            'saldo_akhir'    => $this->modal_awal + $totalPenjualan,
            'waktu_close'    => now(),
            'is_auto_closed' => true,
        ]);

        return $this;
    }
}
