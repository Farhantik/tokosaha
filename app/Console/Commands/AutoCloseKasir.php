<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutoCloseKasir extends Command
{
    protected $signature = 'kasir:auto-close';
    protected $description = 'Tutup kasir aktif secara otomatis sesuai jadwal';

    public function handle()
    {
        $setting = DB::table('settings')->first();

        // Batalkan jika fitur tidak aktif
        if (!$setting || !$setting->auto_close_kasir) {
            $this->info('Auto-close tidak aktif.');
            return;
        }

        // Cek apakah jam sekarang sudah mencapai jam auto-close
        $jamSekarang = now()->timezone('Asia/Jakarta')->format('H:i');
        $jamAutoClose = $setting->auto_close_time; // contoh: "23:00"

        if ($jamSekarang < $jamAutoClose) {
            $this->info("Belum waktunya. Sekarang: {$jamSekarang}, Jadwal: {$jamAutoClose}");
            return;
        }

        // Ambil semua kasir yang masih aktif
        $sesiAktif = DB::table('kasir')
            ->whereNull('waktu_close')
            ->get();

        if ($sesiAktif->isEmpty()) {
            $this->info('Tidak ada kasir aktif.');
            return;
        }

        foreach ($sesiAktif as $kasir) {
            $totalPenjualan = DB::table('penjualan')
                ->where('id_kasir', $kasir->id_kasir)
                ->whereNull('deleted_at')
                ->sum('total_pembayaran');

            $saldoAkhir = $kasir->modal_awal + $totalPenjualan;

            DB::table('kasir')
                ->where('id_kasir', $kasir->id_kasir)
                ->update([
                    'saldo_akhir' => $saldoAkhir,
                    'waktu_close' => now(),
                    'is_auto_closed' => true,
                ]);

            $this->info("Kasir ID {$kasir->id_kasir} berhasil ditutup. Saldo: {$saldoAkhir}");
        }

        $this->info(count($sesiAktif) . ' kasir berhasil ditutup otomatis.');
    }
}
