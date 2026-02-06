<?php

namespace App\Observers;

use App\Models\Piutang;
use App\Models\Pelanggan;
use Illuminate\Support\Facades\Log;

class PiutangObserver
{
    public function created(Piutang $piutang)
    {
        $this->updatePelangganTotalPiutang($piutang->id_pelanggan);
    }

    public function updated(Piutang $piutang)
    {
        $this->updatePelangganTotalPiutang($piutang->id_pelanggan);
    }

    public function deleted(Piutang $piutang)
    {
        $this->updatePelangganTotalPiutang($piutang->id_pelanggan);
    }

    private function updatePelangganTotalPiutang($idPelanggan)
    {
        try {
            $pelanggan = Pelanggan::find($idPelanggan);
            
            if ($pelanggan) {
                $totalPiutang = Piutang::where('id_pelanggan', $idPelanggan)
                    ->whereIn('status_piutang', ['belum_lunas', 'cicilan'])
                    ->sum('sisa_piutang');
                
                $pelanggan->total_piutang = $totalPiutang;
                $pelanggan->saveQuietly();
                
                Log::info("✅ Updated total piutang for pelanggan #{$idPelanggan}: Rp " . number_format($totalPiutang, 0, ',', '.'));
            }
        } catch (\Exception $e) {
            Log::error("❌ Failed to update total piutang: " . $e->getMessage());
        }
    }
}