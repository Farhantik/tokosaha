<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Piutang extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'piutang';
    protected $primaryKey = 'id_piutang';
    public $timestamps = true;

    protected $fillable = [
        'id_pelanggan',
        'id_user',
        'tanggal_piutang',
        'total_piutang',
        'sisa_piutang',
        'jatuh_tempo',
        'status_piutang',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_piutang' => 'datetime',
        'jatuh_tempo' => 'date',
        'total_piutang' => 'decimal:2',
        'sisa_piutang' => 'decimal:2',
    ];

    protected $attributes = [
        'status_piutang' => 'belum_lunas',
    ];

    /**
     * Relationship: Piutang belongs to Pelanggan
     */
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    /**
     * Relationship: Piutang belongs to User (created by)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Relationship: Piutang has many Pembayaran
     */
    public function pembayaran()
    {
        return $this->hasMany(PembayaranPiutang::class, 'id_piutang', 'id_piutang');
    }

    /**
     * Scope: Piutang belum lunas
     */
    public function scopeBelumLunas($query)
    {
        return $query->where('status_piutang', 'belum_lunas');
    }

    /**
     * Scope: Piutang cicilan
     */
    public function scopeCicilan($query)
    {
        return $query->where('status_piutang', 'cicilan');
    }

    /**
     * Scope: Piutang lunas
     */
    public function scopeLunas($query)
    {
        return $query->where('status_piutang', 'lunas');
    }

    /**
     * Scope: Piutang jatuh tempo
     */
    public function scopeJatuhTempo($query)
    {
        return $query->whereNotNull('jatuh_tempo')
                    ->whereDate('jatuh_tempo', '<=', now())
                    ->whereIn('status_piutang', ['belum_lunas', 'cicilan']);
    }

    /**
     * Accessor: Total yang sudah terbayar
     */
    public function getTotalTerbayarAttribute()
    {
        return $this->total_piutang - $this->sisa_piutang;
    }

    /**
     * Accessor: Persentase terbayar
     */
    public function getPersentaseTerbayarAttribute()
    {
        if ($this->total_piutang == 0) return 0;
        return ($this->total_terbayar / $this->total_piutang) * 100;
    }

    /**
     * Accessor: Format total piutang
     */
    public function getTotalPiutangFormatAttribute()
    {
        return 'Rp ' . number_format($this->total_piutang, 0, ',', '.');
    }

    /**
     * Accessor: Format sisa piutang
     */
    public function getSisaPiutangFormatAttribute()
    {
        return 'Rp ' . number_format($this->sisa_piutang, 0, ',', '.');
    }

    /**
     * Accessor: Format total terbayar
     */
    public function getTotalTerbayarFormatAttribute()
    {
        return 'Rp ' . number_format($this->total_terbayar, 0, ',', '.');
    }

    /**
     * Accessor: Status badge color
     */
    public function getStatusBadgeAttribute()
    {
        switch ($this->status_piutang) {
            case 'lunas':
                return 'green';
            case 'cicilan':
                return 'blue';
            case 'belum_lunas':
                return 'yellow';
            default:
                return 'gray';
        }
    }

    /**
     * Accessor: Status label
     */
    public function getStatusLabelAttribute()
    {
        switch ($this->status_piutang) {
            case 'lunas':
                return 'Lunas';
            case 'cicilan':
                return 'Cicilan';
            case 'belum_lunas':
                return 'Belum Lunas';
            default:
                return '-';
        }
    }

    /**
     * Accessor: Tanggal piutang format Indonesia
     */
    public function getTanggalPiutangFormatAttribute()
    {
        return $this->tanggal_piutang->format('d/m/Y H:i');
    }

    /**
     * Accessor: Jatuh tempo format Indonesia
     */
    public function getJatuhTempoFormatAttribute()
    {
        return $this->jatuh_tempo ? $this->jatuh_tempo->format('d/m/Y') : '-';
    }

    /**
     * Check if jatuh tempo
     */
    public function isJatuhTempo()
    {
        if (!$this->jatuh_tempo) return false;
        return Carbon::parse($this->jatuh_tempo)->isPast() && 
               in_array($this->status_piutang, ['belum_lunas', 'cicilan']);
    }

    /**
     * Get hari tersisa jatuh tempo
     */
    public function getHariTersisaAttribute()
    {
        if (!$this->jatuh_tempo) return null;
        $now = Carbon::now()->startOfDay();
        $jatuhTempo = Carbon::parse($this->jatuh_tempo)->startOfDay();
        return $now->diffInDays($jatuhTempo, false);
    }

    /**
     * Get status jatuh tempo text
     */
    public function getStatusJatuhTempoAttribute()
    {
        if (!$this->jatuh_tempo) return null;
        
        $hariTersisa = $this->hari_tersisa;
        
        if ($hariTersisa < 0) {
            return 'Terlambat ' . abs($hariTersisa) . ' hari';
        } elseif ($hariTersisa == 0) {
            return 'Jatuh tempo hari ini';
        } elseif ($hariTersisa <= 7) {
            return 'Segera jatuh tempo (' . $hariTersisa . ' hari)';
        } else {
            return $hariTersisa . ' hari lagi';
        }
    }

    /**
     * Get badge color untuk status jatuh tempo
     */
    public function getJatuhTempoBadgeAttribute()
    {
        if (!$this->jatuh_tempo) return 'gray';
        
        $hariTersisa = $this->hari_tersisa;
        
        if ($hariTersisa < 0) {
            return 'red'; // Terlambat
        } elseif ($hariTersisa <= 7) {
            return 'orange'; // Segera jatuh tempo
        } else {
            return 'green'; // Masih aman
        }
    }
}