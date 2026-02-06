<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranPiutang extends Model
{
    protected $table = 'pembayaran_piutang';
    protected $primaryKey = 'id_pembayaran';
    public $timestamps = false;

    protected $fillable = [
        'id_piutang',
        'id_user',
        'tanggal_bayar',
        'jumlah_bayar',
        'metode_pembayaran',
        'bukti_pembayaran',
        'keterangan'
    ];

    protected $casts = [
        'jumlah_bayar' => 'decimal:2',
        'tanggal_bayar' => 'datetime',
    ];

    // Relationships
    public function piutang()
    {
        return $this->belongsTo(Piutang::class, 'id_piutang', 'id_piutang');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    // Accessors
    public function getJumlahBayarFormatAttribute()
    {
        return 'Rp ' . number_format($this->jumlah_bayar, 0, ',', '.');
    }

    public function getMetodeBadgeAttribute()
    {
        return match($this->metode_pembayaran) {
            'tunai' => 'green',
            'transfer' => 'blue',
            'e-wallet' => 'purple',
            default => 'gray'
        };
    }

    public function getMetodeLabelAttribute()
    {
        return match($this->metode_pembayaran) {
            'tunai' => 'Tunai',
            'transfer' => 'Transfer Bank',
            'e-wallet' => 'E-Wallet',
            default => $this->metode_pembayaran
        };
    }
}