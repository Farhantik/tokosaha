<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Produk;
use App\Models\Kasir;
use App\Models\ProdukKategori;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Transaksi Hari Ini
            $totalTransaksiHariIni = Penjualan::whereDate('tanggal_penjualan', Carbon::today())
                ->count();

            // Omzet Hari Ini
            $totalOmzetHariIni = Penjualan::whereDate('tanggal_penjualan', Carbon::today())
                ->sum('total_pembayaran') ?? 0;

            // Kasir Aktif (yang masih buka/belum tutup)
            $kasirAktif = Kasir::with('user')
                ->whereNotNull('waktu_open')
                ->whereNull('waktu_close')
                ->first();

            // Produk Stok Menipis dengan detail - SESUAI DATABASE ASLI
            $produkStokMenupis = DB::table('produk')
                ->leftJoin('produk_kategori', 'produk.id_produk_kategori', '=', 'produk_kategori.id_produk_kategori')
                ->select(
                    'produk.id_produk',
                    'produk.id_produk_kategori',
                    'produk.code_produk as kode_produk',
                    'produk.nama_produk',
                    'produk.stock_produk',
                    'produk.harga_produk',
                    'produk.gambar_produk',
                    'produk_kategori.nama_kategori'
                )
                ->where('produk.stock_produk', '<=', 20)
                ->whereNull('produk.deleted_at')
                ->orderByRaw('
                    CASE
                        WHEN produk.stock_produk <= 0 THEN 1
                        WHEN produk.stock_produk BETWEEN 1 AND 5 THEN 2
                        WHEN produk.stock_produk BETWEEN 6 AND 10 THEN 3
                        ELSE 4
                    END
                ')
                ->orderBy('produk.stock_produk', 'asc')
                ->get();

            // Ambil semua kategori untuk filter
            $kategoris = ProdukKategori::all();

            // Hitung jumlah produk per kategori (yang stok menipis)
            $kategoriCounts = DB::table('produk')
                ->leftJoin('produk_kategori', 'produk.id_produk_kategori', '=', 'produk_kategori.id_produk_kategori')
                ->select('produk.id_produk_kategori', DB::raw('COUNT(*) as total'))
                ->where('produk.stock_produk', '<=', 20)
                ->whereNull('produk.deleted_at')
                ->groupBy('produk.id_produk_kategori')
                ->pluck('total', 'id_produk_kategori');

            // Debug
            Log::info('Produk Stok Menipis: ' . $produkStokMenupis->count());
            foreach ($produkStokMenupis as $produk) {
                Log::info('Produk: ' . $produk->nama_produk . ' | Stok: ' . $produk->stock_produk . ' | Kategori: ' . ($produk->nama_kategori ?? 'NULL'));
            }

            // Grafik Penjualan 7 Hari Terakhir
            $startDate = Carbon::now()->subDays(6)->startOfDay();
            $endDate = Carbon::now()->endOfDay();

            // Buat array tanggal 7 hari terakhir
            $dateRange = [];
            for ($i = 6; $i >= 0; $i--) {
                $dateRange[] = Carbon::now()->subDays($i)->format('Y-m-d');
            }

            // Query penjualan
            $salesData = Penjualan::selectRaw('DATE(tanggal_penjualan) as tanggal, SUM(total_pembayaran) as total')
                ->whereBetween('tanggal_penjualan', [$startDate, $endDate])
                ->groupBy('tanggal')
                ->pluck('total', 'tanggal')
                ->toArray();

            // Gabungkan dengan tanggal kosong
            $grafikPenjualan = collect($dateRange)->map(function ($date) use ($salesData) {
                return [
                    'tanggal' => Carbon::parse($date)->format('d/m'),
                    'total' => $salesData[$date] ?? 0
                ];
            });

            // Produk Terlaris (30 hari terakhir)
            $produkTerlaris = DB::table('penjualan_detail')
                ->select('penjualan_detail.id_produk', DB::raw('SUM(penjualan_detail.qty_produk) as total_terjual'))
                ->join('penjualan', 'penjualan_detail.id_penjualan', '=', 'penjualan.id_penjualan')
                ->join('produk', 'penjualan_detail.id_produk', '=', 'produk.id_produk')
                ->where('penjualan.tanggal_penjualan', '>=', Carbon::now()->subDays(30))
                ->groupBy('penjualan_detail.id_produk')
                ->orderBy('total_terjual', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    $produk = Produk::find($item->id_produk);
                    return (object) [
                        'nama_produk' => $produk->nama_produk ?? 'Unknown',
                        'total_terjual' => $item->total_terjual,
                        'gambar_produk' => $produk->gambar_produk ?? null
                    ];
                });

            return view('dashboard', compact(
                'totalTransaksiHariIni',
                'totalOmzetHariIni',
                'kasirAktif',
                'produkStokMenupis',
                'grafikPenjualan',
                'produkTerlaris',
                'kategoris',
                'kategoriCounts'
            ));
        } catch (\Exception $e) {
            Log::error('Dashboard Error: ' . $e->getMessage());
            Log::error('Stack Trace: ' . $e->getTraceAsString());

            // Return with empty data if error
            return view('dashboard', [
                'totalTransaksiHariIni' => 0,
                'totalOmzetHariIni' => 0,
                'kasirAktif' => null,
                'produkStokMenupis' => collect([]),
                'grafikPenjualan' => collect([]),
                'produkTerlaris' => collect([]),
                'kategoris' => collect([]),
                'kategoriCounts' => collect([])
            ]);
        }
    }
}
