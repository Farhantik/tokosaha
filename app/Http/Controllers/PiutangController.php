<?php

namespace App\Http\Controllers;

use App\Models\Piutang;
use App\Models\Pelanggan;
use App\Models\PembayaranPiutang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PiutangController extends Controller
{
    /**
     * Display listing of piutang
     */
    public function index(Request $request)
    {
        $query = Piutang::with(['pelanggan', 'user'])
                       ->orderBy('tanggal_piutang', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status_piutang', $request->status);
        }

        // Filter by jatuh tempo
        if ($request->has('jatuh_tempo')) {
            $query->jatuhTempo();
        }

        $piutang = $query->paginate(15);
        
        // Statistics
        $stats = [
            'total_piutang' => Piutang::whereIn('status_piutang', ['belum_lunas', 'cicilan'])->sum('sisa_piutang'),
            'total_belum_lunas' => Piutang::belumLunas()->count(),
            'total_cicilan' => Piutang::cicilan()->count(),
            'total_jatuh_tempo' => Piutang::jatuhTempo()->count(),
        ];

        return view('piutang.index', compact('piutang', 'stats'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $pelanggan = Pelanggan::aktif()->orderBy('nama_pelanggan')->get();
        return view('piutang.create', compact('pelanggan'));
    }

    /**
     * Store new piutang
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_pelanggan' => 'required|exists:pelanggan,id_pelanggan',
            'total_piutang' => 'required|numeric|min:1',
            'jatuh_tempo' => 'nullable|date',
            'keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $piutang = Piutang::create([
                'id_pelanggan' => $request->id_pelanggan,
                'id_user' => auth()->user()->id_user,
                'tanggal_piutang' => now(),
                'total_piutang' => $request->total_piutang,
                'sisa_piutang' => $request->total_piutang,
                'jatuh_tempo' => $request->jatuh_tempo,
                'status_piutang' => 'belum_lunas',
                'keterangan' => $request->keterangan,
            ]);

            DB::commit();
            Log::info("✅ Piutang created: ID {$piutang->id_piutang}");

            return redirect()->route('piutang.index')
                ->with('success', 'Piutang berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error("❌ Failed to create piutang: " . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Gagal menambahkan piutang: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show detail piutang
     */
    public function show($id)
    {
        $piutang = Piutang::with(['pelanggan', 'user', 'pembayaran.user'])
                          ->findOrFail($id);

        return view('piutang.show', compact('piutang'));
    }

    /**
     * Bayar piutang
     */
    public function bayar(Request $request, $id)
    {
        $request->validate([
            'jumlah_bayar' => 'required|numeric|min:1',
            'metode_pembayaran' => 'required|in:tunai,transfer,e-wallet',
            'keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $piutang = Piutang::findOrFail($id);

            // Validasi jumlah bayar tidak melebihi sisa
            if ($request->jumlah_bayar > $piutang->sisa_piutang) {
                return redirect()->back()
                    ->with('error', 'Jumlah pembayaran melebihi sisa piutang');
            }

            // Create pembayaran (trigger akan auto update piutang)
            PembayaranPiutang::create([
                'id_piutang' => $piutang->id_piutang,
                'id_user' => auth()->user()->id_user,
                'tanggal_bayar' => now(),
                'jumlah_bayar' => $request->jumlah_bayar,
                'metode_pembayaran' => $request->metode_pembayaran,
                'keterangan' => $request->keterangan,
            ]);

            DB::commit();
            Log::info("✅ Payment recorded: Piutang ID {$id}, Amount: {$request->jumlah_bayar}");

            return redirect()->route('piutang.show', $id)
                ->with('success', 'Pembayaran berhasil dicatat');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error("❌ Failed to record payment: " . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Gagal mencatat pembayaran: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete piutang (hanya jika belum ada pembayaran)
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $piutang = Piutang::findOrFail($id);

            // Cek apakah sudah ada pembayaran
            if ($piutang->pembayaran()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'Tidak dapat menghapus piutang yang sudah ada pembayaran');
            }

            $piutang->delete();
            
            DB::commit();
            Log::info("✅ Piutang deleted: ID {$id}");

            return redirect()->route('piutang.index')
                ->with('success', 'Piutang berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error("❌ Failed to delete piutang: " . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Gagal menghapus piutang: ' . $e->getMessage());
        }
    }

    /**
     * Laporan piutang
     */
    public function laporan(Request $request)
    {
        $query = Piutang::with(['pelanggan', 'user']);

        // Filter by date range
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_piutang', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_piutang', '<=', $request->tanggal_sampai);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status_piutang', $request->status);
        }

        $piutang = $query->orderBy('tanggal_piutang', 'desc')->get();

        // Summary
        $summary = [
            'total_piutang' => $piutang->sum('total_piutang'),
            'total_terbayar' => $piutang->sum(function($p) {
                return $p->total_terbayar;
            }),
            'total_sisa' => $piutang->sum('sisa_piutang'),
            'jumlah_transaksi' => $piutang->count(),
        ];

        return view('piutang.laporan', compact('piutang', 'summary'));
    }
}