<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RunScheduler
{
    public function handle(Request $request, Closure $next)
    {
        \Log::info('RunScheduler hit', [
            'url'       => $request->url(),
            'ajax'      => $request->ajax(),
            'wantsJson' => $request->wantsJson(),
            'header_x'  => $request->header('X-Requested-With'),
            'accept'    => $request->header('Accept'),
        ]);

        if (!$request->ajax() && !$request->wantsJson()) {
            $this->checkAutoCloseKasir();
        }

        return $next($request);
    }

    private function checkAutoCloseKasir()
    {
        try {
            $setting = DB::table('settings')->first();

            if (!$setting || !$setting->auto_close_kasir) {
                return;
            }

            $jamSekarang  = Carbon::now('Asia/Jakarta')->format('H:i');
            $jamAutoClose = substr($setting->auto_close_time, 0, 5);

            if ($jamSekarang < $jamAutoClose) {
                return;
            }

            $sesiAktif = DB::table('kasir')
                ->whereNull('waktu_close')
                ->get();

            if ($sesiAktif->isEmpty()) {
                return;
            }

            foreach ($sesiAktif as $kasir) {
                $waktuBukaJkt = Carbon::parse($kasir->waktu_open)
                    ->setTimezone('Asia/Jakarta')
                    ->format('H:i');

                if ($waktuBukaJkt >= $jamAutoClose) {
                    continue;
                }

                $totalPenjualan = DB::table('penjualan')
                    ->where('id_kasir', $kasir->id_kasir)
                    ->whereNull('deleted_at')
                    ->sum('total_pembayaran');

                $saldoAkhir = $kasir->modal_awal + $totalPenjualan;

                DB::table('kasir')
                    ->where('id_kasir', $kasir->id_kasir)
                    ->update([
                        'saldo_akhir'    => $saldoAkhir,
                        'waktu_close'    => Carbon::now('Asia/Jakarta'),
                        'is_auto_closed' => true,
                    ]);

                \Log::info("RunScheduler auto-close kasir id={$kasir->id_kasir}, saldo={$saldoAkhir}");
            }
        } catch (\Exception $e) {
            \Log::error('RunScheduler auto-close error: ' . $e->getMessage());
        }
    }
}
