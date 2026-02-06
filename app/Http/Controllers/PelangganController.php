<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Piutang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PelangganController extends Controller
{
    /**
     * Display listing of pelanggan
     */
    public function index()
    {
        try {
            // Load pelanggan dengan perhitungan piutang
            $pelanggan = Pelanggan::select('pelanggan.*')
                ->selectSub(function ($query) {
                    $query->from('piutang')
                        ->whereColumn('piutang.id_pelanggan', 'pelanggan.id_pelanggan')
                        ->where('status_piutang', '!=', 'lunas')
                        ->selectRaw('COALESCE(SUM(sisa_piutang), 0)');
                }, 'total_piutang')
                ->orderBy('nama_pelanggan')
                ->paginate(15);
            
            // Hitung stats yang benar
            $stats = [
                'total_pelanggan' => Pelanggan::where('status', 'aktif')->count(),
                'pelanggan_piutang' => Pelanggan::whereHas('piutang', function($q) {
                    $q->where('status_piutang', '!=', 'lunas');
                })->count(),
                'total_piutang' => Piutang::where('status_piutang', '!=', 'lunas')
                    ->sum('sisa_piutang'),
            ];
            
            return view('pelanggan.index', compact('pelanggan', 'stats'));
            
        } catch (\Exception $e) {
            Log::error("❌ Error loading pelanggan index: " . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('pelanggan.create');
    }

    /**
     * Store new pelanggan
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:150',
            'no_telp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'email' => 'nullable|email|max:100',
        ]);

        try {
            $pelanggan = Pelanggan::create([
                'nama_pelanggan' => $request->nama_pelanggan,
                'no_telp' => $request->no_telp,
                'alamat' => $request->alamat,
                'email' => $request->email,
                'status' => 'aktif',
            ]);
            
            Log::info("✅ Pelanggan created: {$pelanggan->nama_pelanggan}");
            
            return redirect()->route('pelanggan.index')
                ->with('success', 'Pelanggan berhasil ditambahkan');
                
        } catch (\Exception $e) {
            Log::error("❌ Failed to create pelanggan: " . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Gagal menambahkan pelanggan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show detail pelanggan
     */
    public function show($id)
    {
        try {
            $pelanggan = Pelanggan::findOrFail($id);
            
            // Hitung total piutang yang belum lunas
            $totalPiutang = Piutang::where('id_pelanggan', $id)
                ->where('status_piutang', '!=', 'lunas')
                ->sum('sisa_piutang');
            
            $pelanggan->total_piutang = $totalPiutang;
            
            // Load piutang list
            $piutangList = Piutang::where('id_pelanggan', $id)
                ->orderBy('tanggal_piutang', 'desc')
                ->get();
            
            return view('pelanggan.show', compact('pelanggan', 'piutangList'));
            
        } catch (\Exception $e) {
            Log::error("❌ Error loading pelanggan detail: " . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return redirect()->route('pelanggan.index')
                ->with('error', 'Pelanggan tidak ditemukan');
        }
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        try {
            $pelanggan = Pelanggan::findOrFail($id);
            
            return view('pelanggan.edit', compact('pelanggan'));
            
        } catch (\Exception $e) {
            return redirect()->route('pelanggan.index')
                ->with('error', 'Pelanggan tidak ditemukan');
        }
    }

    /**
     * Update pelanggan
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:150',
            'no_telp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'email' => 'nullable|email|max:100',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        try {
            $pelanggan = Pelanggan::findOrFail($id);
            
            $pelanggan->update([
                'nama_pelanggan' => $request->nama_pelanggan,
                'no_telp' => $request->no_telp,
                'alamat' => $request->alamat,
                'email' => $request->email,
                'status' => $request->status,
            ]);
            
            Log::info("✅ Pelanggan updated: {$pelanggan->nama_pelanggan}");
            
            return redirect()->route('pelanggan.index')
                ->with('success', 'Pelanggan berhasil diperbarui');
                
        } catch (\Exception $e) {
            Log::error("❌ Failed to update pelanggan: " . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Gagal memperbarui pelanggan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete pelanggan
     */
    public function destroy($id)
    {
        try {
            $pelanggan = Pelanggan::findOrFail($id);
            
            // Cek apakah masih punya piutang aktif
            $hasPiutang = Piutang::where('id_pelanggan', $id)
                ->where('status_piutang', '!=', 'lunas')
                ->exists();
            
            if ($hasPiutang) {
                return redirect()->back()
                    ->with('error', 'Tidak dapat menghapus pelanggan yang masih memiliki piutang');
            }
            
            $nama = $pelanggan->nama_pelanggan;
            $pelanggan->delete();
            
            Log::info("✅ Pelanggan deleted: {$nama}");
            
            return redirect()->route('pelanggan.index')
                ->with('success', 'Pelanggan berhasil dihapus');
                
        } catch (\Exception $e) {
            Log::error("❌ Failed to delete pelanggan: " . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Gagal menghapus pelanggan: ' . $e->getMessage());
        }
    }

    /**
     * Search pelanggan (API)
     */
    public function search(Request $request)
    {
        try {
            $query = $request->get('q', '');
            
            $pelanggan = Pelanggan::where('nama_pelanggan', 'LIKE', "%{$query}%")
                ->orWhere('no_telp', 'LIKE', "%{$query}%")
                ->where('status', 'aktif')
                ->limit(10)
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $pelanggan
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}