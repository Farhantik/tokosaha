<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    protected $table = 'pelanggan';
    protected $primaryKey = 'id_pelanggan';
    
    protected $fillable = [
        'nama_pelanggan',
        'no_telp',
        'alamat',
        'email',
        'total_piutang',
        'status'
    ];
    
    protected $casts = [
        'total_piutang' => 'decimal:2',
    ];
    
    // Relationships
    public function piutang()
    {
        return $this->hasMany(Piutang::class, 'id_pelanggan', 'id_pelanggan');
    }
    
    public function piutangAktif()
    {
        return $this->hasMany(Piutang::class, 'id_pelanggan', 'id_pelanggan')
                    ->whereIn('status_piutang', ['belum_lunas', 'cicilan']);
    }
    
    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
    
    public function scopePunyaPiutang($query)
    {
        return $query->where('total_piutang', '>', 0);
    }
    
    // Accessors
    public function getTotalPiutangFormatAttribute()
    {
        return 'Rp ' . number_format($this->total_piutang, 0, ',', '.');
    }
    
    public function getStatusBadgeAttribute()
    {
        return $this->status === 'aktif' ? 'green' : 'gray';
    }
    
    public function getHasPiutangAttribute()
    {
        return $this->total_piutang > 0;
    }
    
    public function getJumlahPiutangAktifAttribute()
    {
        return $this->piutangAktif()->count();
    }
}