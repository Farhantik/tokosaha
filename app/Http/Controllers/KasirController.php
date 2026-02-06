<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class KasirController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Cek kasir aktif untuk user ini
        $kasirAktif = DB::table('kasir')
            ->where('id_user', $user->id_user)
            ->whereNull('waktu_close')
            ->first();
        
        // Convert string ke Carbon untuk kasir aktif
        if ($kasirAktif) {
            $kasirAktif->waktu_open = Carbon::parse($kasirAktif->waktu_open);
        }
        
        // Riwayat kasir dengan pagination
        $riwayatKasir = DB::table('kasir')
            ->where('id_user', $user->id_user)
            ->orderBy('waktu_open', 'desc')
            ->paginate(10);
        
        // Convert string ke Carbon dan tambah status
        $riwayatKasir->getCollection()->transform(function ($kasir) {
            $kasir->waktu_open = Carbon::parse($kasir->waktu_open);
            $kasir->waktu_close = $kasir->waktu_close ? Carbon::parse($kasir->waktu_close) : null;
            $kasir->status = $kasir->waktu_close ? 'close' : 'open';
            return $kasir;
        });
        
        return view('kasir.index', compact('kasirAktif', 'riwayatKasir'));
    }
    
    public function open(Request $request)
    {
        $request->validate([
            'modal_awal' => 'required|numeric|min:1'
        ], [
            'modal_awal.required' => 'Modal awal harus diisi',
            'modal_awal.numeric' => 'Modal awal harus berupa angka',
            'modal_awal.min' => 'Modal awal minimal Rp 1'
        ]);
        
        $user = Auth::user();
        
        // Cek apakah sudah ada kasir yang aktif
        $kasirAktif = DB::table('kasir')
            ->where('id_user', $user->id_user)
            ->whereNull('waktu_close')
            ->first();
        
        if ($kasirAktif) {
            // Jika request AJAX, return JSON
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
            'id_user' => $user->id_user,
            'modal_awal' => $request->modal_awal,
            'waktu_open' => now(),
            'saldo_akhir' => null,
            'waktu_close' => null
        ]);
        
        // Jika request AJAX, return JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Kasir berhasil dibuka',
                'data' => [
                    'id_kasir' => $idKasir,
                    'modal_awal' => $request->modal_awal
                ]
            ], 200);
        }
        
        return redirect()->route('kasir.index')
            ->with('success', 'Kasir berhasil dibuka dengan modal Rp ' . number_format($request->modal_awal, 0, ',', '.'));
    }
    
    public function close(Request $request, $id)
    {
        try {
            // Validasi input saldo_akhir jika ada
            if ($request->has('saldo_akhir')) {
                $request->validate([
                    'saldo_akhir' => 'required|numeric|min:1'
                ], [
                    'saldo_akhir.required' => 'Saldo akhir harus diisi',
                    'saldo_akhir.numeric' => 'Saldo akhir harus berupa angka',
                    'saldo_akhir.min' => 'Saldo akhir minimal Rp 1'
                ]);
            }
            
            $user = Auth::user();
            
            // Cari kasir
            $kasir = DB::table('kasir')
                ->where('id_kasir', $id)
                ->where('id_user', $user->id_user)
                ->whereNull('waktu_close')
                ->first();
            
            if (!$kasir) {
                // Jika request AJAX, return JSON
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Kasir tidak ditemukan atau sudah ditutup.'
                    ], 404);
                }
                
                return redirect()->route('kasir.index')
                    ->with('error', 'Kasir tidak ditemukan atau sudah ditutup.');
            }
            
            // Hitung total penjualan dari kasir ini
            $totalPenjualan = DB::table('penjualan')
                ->where('id_kasir', $id)
                ->sum('total_pembayaran');
            
            // Hitung saldo akhir
            // Jika user input saldo_akhir, gunakan itu. Jika tidak, hitung otomatis
            $saldoAkhir = $request->has('saldo_akhir') 
                ? $request->saldo_akhir 
                : ($kasir->modal_awal + $totalPenjualan);
            
            // Update kasir - tutup
            DB::table('kasir')
                ->where('id_kasir', $id)
                ->update([
                    'saldo_akhir' => $saldoAkhir,
                    'waktu_close' => now()
                ]);
            
            // Hitung selisih
            $saldoSeharusnya = $kasir->modal_awal + $totalPenjualan;
            $selisih = $saldoAkhir - $kasir->modal_awal;
            $selisihFisik = $saldoAkhir - $saldoSeharusnya; // Selisih antara fisik dan sistem
            
            // Jika request AJAX, return JSON
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kasir berhasil ditutup',
                    'modal_awal' => $kasir->modal_awal,
                    'total_penjualan' => $totalPenjualan,
                    'saldo_seharusnya' => $saldoSeharusnya,
                    'saldo_akhir' => $saldoAkhir,
                    'selisih' => $selisih, // Total keuntungan dari modal awal
                    'selisih_fisik' => $selisihFisik // Selisih fisik vs sistem (untuk cek kesesuaian)
                ], 200);
            }
            
            return redirect()->route('kasir.index')
                ->with('success', 'Kasir berhasil ditutup. Saldo akhir: Rp ' . number_format($saldoAkhir, 0, ',', '.'));
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal: ' . collect($e->errors())->flatten()->first()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error closing kasir: ' . $e->getMessage());
            
            // Jika request AJAX, return JSON
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
}