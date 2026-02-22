<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class KasirController extends Controller
{
    // ═══════════════════════════════════════════════════════════
    // INDEX
    // ═══════════════════════════════════════════════════════════

    public function index()
    {
        $user = Auth::user();

        // Kasir aktif = belum ada waktu_close (milik user ini)
        $kasirAktif = DB::table('kasir')
            ->where('id_user', $user->id_user)
            ->whereNull('waktu_close')
            ->first();

        if ($kasirAktif) {
            $kasirAktif->waktu_open = Carbon::parse($kasirAktif->waktu_open);
        }

        // Riwayat kasir dengan pagination
        $riwayatKasir = DB::table('kasir')
            ->where('id_user', $user->id_user)
            ->orderBy('waktu_open', 'desc')
            ->paginate(10);

        $riwayatKasir->getCollection()->transform(function ($kasir) {
            $kasir->waktu_open  = Carbon::parse($kasir->waktu_open);
            $kasir->waktu_close = $kasir->waktu_close ? Carbon::parse($kasir->waktu_close) : null;
            $kasir->status      = is_null($kasir->waktu_close) ? 'open' : 'closed';
            return $kasir;
        });

        return view('kasir.index', compact('kasirAktif', 'riwayatKasir'));
    }

    // ═══════════════════════════════════════════════════════════
    // BUKA KASIR — hanya input modal_awal
    // ═══════════════════════════════════════════════════════════

    public function open(Request $request)
    {
        $request->validate([
            'modal_awal' => 'required|numeric|min:1'
        ], [
            'modal_awal.required' => 'Modal awal harus diisi',
            'modal_awal.numeric'  => 'Modal awal harus berupa angka',
            'modal_awal.min'      => 'Modal awal minimal Rp 1'
        ]);

        $user = Auth::user();

        // Cek kasir aktif milik user ini
        $kasirAktif = DB::table('kasir')
            ->where('id_user', $user->id_user)
            ->whereNull('waktu_close')
            ->first();

        if ($kasirAktif) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kasir sudah aktif. Tutup kasir terlebih dahulu.'
                ], 400);
            }
            return redirect()->route('kasir.index')
                ->with('error', 'Kasir sudah aktif. Tutup kasir terlebih dahulu.');
        }

        // Buka kasir baru
        $idKasir = DB::table('kasir')->insertGetId([
            'id_user'        => $user->id_user,
            'modal_awal'     => $request->modal_awal,
            'waktu_open'     => now(),
            'saldo_akhir'    => null,
            'waktu_close'    => null,
            'is_auto_closed' => false,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Kasir berhasil dibuka',
                'data'    => [
                    'id_kasir'   => $idKasir,
                    'modal_awal' => $request->modal_awal
                ]
            ], 200);
        }

        return redirect()->route('kasir.index')
            ->with('success', 'Kasir berhasil dibuka dengan modal Rp ' . number_format($request->modal_awal, 0, ',', '.'));
    }

    // ═══════════════════════════════════════════════════════════
    // TUTUP KASIR — saldo akhir OTOMATIS (modal + total penjualan)
    // Tidak perlu input saldo dari user
    // is_auto = false → tutup manual | is_auto = true → tutup otomatis
    // ═══════════════════════════════════════════════════════════

    public function close(Request $request, $id)
    {
        try {
            $user   = Auth::user();
            $isAuto = (bool) $request->input('is_auto', false);

            // Owner bisa tutup kasir siapapun, kasir hanya milik sendiri
            $query = DB::table('kasir')
                ->where('id_kasir', $id)
                ->whereNull('waktu_close');

            if ($user->role_user !== 'owner') {
                $query->where('id_user', $user->id_user);
            }

            $kasir = $query->first();

            if (!$kasir) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Kasir tidak ditemukan atau sudah ditutup.'
                    ], 404);
                }
                return redirect()->route('kasir.index')
                    ->with('error', 'Kasir tidak ditemukan atau sudah ditutup.');
            }

            // Hitung total penjualan dari sesi ini (exclude soft-deleted)
            $totalPenjualan = DB::table('penjualan')
                ->where('id_kasir', $id)
                ->whereNull('deleted_at')
                ->sum('total_pembayaran');

            // Saldo akhir dihitung OTOMATIS — tidak perlu input manual
            $saldoAkhir = $kasir->modal_awal + $totalPenjualan;
            $selisih    = $saldoAkhir - $kasir->modal_awal;

            // Update kasir
            DB::table('kasir')
                ->where('id_kasir', $id)
                ->update([
                    'saldo_akhir'    => $saldoAkhir,
                    'waktu_close'    => now(),
                    'is_auto_closed' => $isAuto,
                ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success'         => true,
                    'message'         => $isAuto
                        ? 'Kasir ditutup otomatis oleh sistem.'
                        : 'Kasir berhasil ditutup.',
                    'modal_awal'      => $kasir->modal_awal,
                    'total_penjualan' => $totalPenjualan,
                    'saldo_akhir'     => $saldoAkhir,
                    'selisih'         => $selisih,
                ], 200);
            }

            return redirect()->route('kasir.index')
                ->with('success', 'Kasir berhasil ditutup. Saldo akhir: Rp ' . number_format($saldoAkhir, 0, ',', '.'));
        } catch (\Exception $e) {
            \Log::error('Error closing kasir: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('kasir.index')
                ->with('error', 'Terjadi kesalahan saat menutup kasir.');
        }
    }

    // ═══════════════════════════════════════════════════════════
    // AUTO-CLOSE ALL — tutup semua kasir aktif sekaligus (Owner)
    // ═══════════════════════════════════════════════════════════

    public function autoCloseAll(Request $request)
    {
        try {
            $sesiAktif = DB::table('kasir')
                ->whereNull('waktu_close')
                ->get();

            if ($sesiAktif->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tidak ada sesi kasir yang perlu ditutup.',
                    'closed'  => 0,
                ]);
            }

            $results = [];

            foreach ($sesiAktif as $kasir) {
                $totalPenjualan = DB::table('penjualan')
                    ->where('id_kasir', $kasir->id_kasir)
                    ->whereNull('deleted_at')
                    ->sum('total_pembayaran');

                $saldoAkhir = $kasir->modal_awal + $totalPenjualan;

                DB::table('kasir')
                    ->where('id_kasir', $kasir->id_kasir)
                    ->update([
                        'saldo_akhir'    => $saldoAkhir,
                        'waktu_close'    => now(),
                        'is_auto_closed' => true,
                    ]);

                $results[] = [
                    'id_kasir'        => $kasir->id_kasir,
                    'modal_awal'      => $kasir->modal_awal,
                    'total_penjualan' => $totalPenjualan,
                    'saldo_akhir'     => $saldoAkhir,
                ];
            }

            return response()->json([
                'success' => true,
                'message' => count($results) . ' sesi kasir berhasil ditutup otomatis.',
                'closed'  => count($results),
                'data'    => $results,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error auto-close all kasir: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // ═══════════════════════════════════════════════════════════
    // UPDATE SETTING AUTO-CLOSE — simpan waktu & status (Owner)
    // ═══════════════════════════════════════════════════════════

    public function updateAutoCloseSetting(Request $request)
    {
        try {
            $request->validate([
                'auto_close_kasir' => 'required|in:0,1',
                'auto_close_time'  => [
                    'required',
                    'regex:/^([01]\d|2[0-3]):([0-5]\d)$/',
                ],
            ], [
                'auto_close_time.regex' => 'Format waktu harus HH:MM, contoh: 23:59',
            ]);

            DB::table('settings')
                ->where('id', 1)
                ->update([
                    'auto_close_kasir' => (bool) $request->auto_close_kasir,
                    'auto_close_time'  => $request->auto_close_time,
                    'updated_at'       => now(),
                ]);

            $aktif  = (bool) $request->auto_close_kasir;
            $status = $aktif
                ? "Aktif — kasir akan ditutup otomatis pukul {$request->auto_close_time} setiap hari."
                : "Nonaktif — kasir tidak akan ditutup otomatis.";

            return response()->json([
                'success' => true,
                'message' => "Pengaturan berhasil disimpan. {$status}",
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error updating auto-close setting: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
