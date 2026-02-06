<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * Nama tabel di database
     */
    protected $table = 'user';

    /**
     * Primary key tabel
     */
    protected $primaryKey = 'id_user';

    /**
     * Timestamps
     */
    public $timestamps = false;

    /**
     * Field yang bisa diisi
     */
    protected $fillable = [
        'nama_user',
        'username_user',
        'password_user',
        'role_user',
        'gambar_user',
    ];

    /**
     * Field yang disembunyikan
     */
    protected $hidden = [
        'password_user',
    ];

    /**
     * Cast attributes
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Override method untuk mendapatkan password
     */
    public function getAuthPassword()
    {
        return $this->password_user;
    }

    /**
     * Override method untuk mendapatkan username sebagai identifier
     */
    public function getAuthIdentifierName()
    {
        return 'id_user';
    }

    /**
     * Accessor untuk role
     */
    public function getRoleAttribute()
    {
        return $this->attributes['role_user'] ?? null;
    }

    /**
     * Accessor untuk nama
     */
    public function getNameAttribute()
    {
        return $this->attributes['nama_user'] ?? null;
    }

    /**
     * Accessor untuk email (untuk compatibility dengan Laravel Auth)
     */
    public function getEmailAttribute()
    {
        return $this->attributes['username_user'] ?? null;
    }

    /**
     * Accessor untuk username
     */
    public function getUsernameAttribute()
    {
        return $this->attributes['username_user'] ?? null;
    }

    /**
     * Accessor untuk password (untuk compatibility)
     */
    public function getPasswordAttribute()
    {
        return $this->attributes['password_user'] ?? null;
    }

    /**
     * Get photo URL or default
     */
    public function getPhotoUrlAttribute()
    {
        if ($this->gambar_user && file_exists(public_path('uploads/users/' . $this->gambar_user))) {
            return asset('uploads/users/' . $this->gambar_user);
        }
        return null;
    }

    /**
     * Get avatar (photo or initial)
     */
    public function getAvatarAttribute()
    {
        return $this->photo_url ?? strtoupper(substr($this->nama_user, 0, 1));
    }

    /**
     * Check if user has photo
     */
    public function hasPhoto()
    {
        return $this->gambar_user && file_exists(public_path('uploads/users/' . $this->gambar_user));
    }

    /**
     * Check if user is owner
     */
    public function isOwner()
    {
        return $this->role_user === 'owner';
    }

    /**
     * Check if user is kasir
     */
    public function isKasir()
    {
        return $this->role_user === 'kasir';
    }

    /**
     * Get role badge color
     */
    public function getRoleBadgeColorAttribute()
    {
        return $this->isOwner() ? 'purple' : 'blue';
    }

    /**
     * Get role icon
     */
    public function getRoleIconAttribute()
    {
        return $this->isOwner() ? 'fa-crown' : 'fa-user';
    }

    /**
     * Get formatted created date
     */
    public function getCreatedDateAttribute()
    {
        return $this->created_at ? $this->created_at->format('d/m/Y H:i') : '-';
    }

    /**
     * Relasi ke Kasir
     * Satu user bisa punya banyak sesi kasir
     */
    public function kasir()
    {
        return $this->hasMany(Kasir::class, 'id_user', 'id_user');
    }

    /**
     * Relasi ke Kasir Aktif (yang sedang buka)
     * Untuk mendapatkan sesi kasir yang sedang aktif
     */
    public function kasirAktif()
    {
        return $this->hasOne(Kasir::class, 'id_user', 'id_user')
            ->whereNotNull('waktu_open')
            ->whereNull('waktu_close')
            ->latest('waktu_open');
    }

    /**
     * Get total kasir sessions
     */
    public function getTotalKasirAttribute()
    {
        return $this->kasir()->count();
    }

    /**
     * Get total transaksi
     */
    public function getTotalTransaksiAttribute()
    {
        return DB::table('penjualan')
            ->join('kasir', 'penjualan.id_kasir', '=', 'kasir.id_kasir')
            ->where('kasir.id_user', $this->id_user)
            ->count();
    }

    /**
     * Get total omzet
     */
    public function getTotalOmzetAttribute()
    {
        return DB::table('penjualan')
            ->join('kasir', 'penjualan.id_kasir', '=', 'kasir.id_kasir')
            ->where('kasir.id_user', $this->id_user)
            ->sum('total_pembayaran') ?? 0;
    }

    /**
     * Get formatted total omzet
     */
    public function getTotalOmzetFormatAttribute()
    {
        return 'Rp ' . number_format($this->total_omzet, 0, ',', '.');
    }

    /**
     * Check if user has active kasir
     */
    public function hasActiveKasir()
    {
        return $this->kasirAktif()->exists();
    }

    /**
     * Get active kasir session
     */
    public function getActiveKasir()
    {
        return $this->kasirAktif()->first();
    }

    /**
     * Query scope untuk filter by role
     */
    public function scopeOwner($query)
    {
        return $query->where('role_user', 'owner');
    }

    /**
     * Query scope untuk filter kasir
     */
    public function scopeKasirOnly($query)
    {
        return $query->where('role_user', 'kasir');
    }

    /**
     * Query scope untuk search
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('nama_user', 'like', "%{$search}%")
                    ->orWhere('username_user', 'like', "%{$search}%");
            });
        }
        return $query;
    }

    /**
     * Get user statistics
     */
    public function getStatistics()
    {
        return [
            'total_kasir' => $this->total_kasir,
            'total_transaksi' => $this->total_transaksi,
            'total_omzet' => $this->total_omzet,
            'total_omzet_format' => $this->total_omzet_format,
            'has_active_kasir' => $this->hasActiveKasir(),
            'last_login' => $this->created_at,
        ];
    }

    /**
     * Get user activity summary
     */
    public function getActivitySummary()
    {
        $kasirSessions = $this->kasir()->get();

        return [
            'total_sessions' => $kasirSessions->count(),
            'total_hours' => $kasirSessions->sum(function ($kasir) {
                if ($kasir->waktu_close && $kasir->waktu_open) {
                    $open = strtotime($kasir->waktu_open);
                    $close = strtotime($kasir->waktu_close);
                    return ($close - $open) / 3600; // Convert to hours
                }
                return 0;
            }),
            'last_session' => $kasirSessions->sortByDesc('waktu_open')->first(),
        ];
    }
}
