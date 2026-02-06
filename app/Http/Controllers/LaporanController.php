<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        // Set default date range - BULAN BERJALAN (bukan hari ini)
        $tanggalMulai = $request->get('tanggal_mulai', now()->startOfMonth()->format('Y-m-d'));
        $tanggalSelesai = $request->get('tanggal_selesai', now()->format('Y-m-d'));

        // Query transaksi with date range
        $query = DB::table('penjualan')
            ->leftJoin('kasir', 'penjualan.id_kasir', '=', 'kasir.id_kasir')
            ->leftJoin('user', 'kasir.id_user', '=', 'user.id_user')
            ->select(
                'penjualan.*',
                'user.nama_user as kasir'
            );

        // Apply date filter
        if ($tanggalMulai && $tanggalSelesai) {
            $query->whereBetween('penjualan.tanggal_penjualan', [
                $tanggalMulai . ' 00:00:00',
                $tanggalSelesai . ' 23:59:59'
            ]);
        }

        $transaksi = $query->orderBy('penjualan.tanggal_penjualan', 'desc')
            ->paginate(10);

        // Calculate stats
        $totalPenjualan = DB::table('penjualan')
            ->whereBetween('tanggal_penjualan', [
                $tanggalMulai . ' 00:00:00',
                $tanggalSelesai . ' 23:59:59'
            ])
            ->sum('total_pembayaran') ?? 0;

        $totalTransaksi = DB::table('penjualan')
            ->whereBetween('tanggal_penjualan', [
                $tanggalMulai . ' 00:00:00',
                $tanggalSelesai . ' 23:59:59'
            ])
            ->count();

        $totalKembalian = DB::table('penjualan')
            ->whereBetween('tanggal_penjualan', [
                $tanggalMulai . ' 00:00:00',
                $tanggalSelesai . ' 23:59:59'
            ])
            ->sum('kembalian_pembayaran') ?? 0;

        // Penjualan per hari (for chart)
        $penjualanPerHari = DB::table('penjualan')
            ->selectRaw('DATE(tanggal_penjualan) as tanggal')
            ->selectRaw('SUM(total_pembayaran) as total_penjualan')
            ->selectRaw('COUNT(*) as total_transaksi')
            ->whereBetween('tanggal_penjualan', [
                $tanggalMulai . ' 00:00:00',
                $tanggalSelesai . ' 23:59:59'
            ])
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        // Produk terlaris
        $produkTerlaris = DB::table('penjualan_detail')
            ->join('penjualan', 'penjualan_detail.id_penjualan', '=', 'penjualan.id_penjualan')
            ->join('produk', 'penjualan_detail.id_produk', '=', 'produk.id_produk')
            ->select(
                'produk.nama_produk',
                DB::raw('SUM(penjualan_detail.qty_produk) as total_qty'),
                DB::raw('SUM(penjualan_detail.subtotal_harga) as total_penjualan')
            )
            ->whereBetween('penjualan.tanggal_penjualan', [
                $tanggalMulai . ' 00:00:00',
                $tanggalSelesai . ' 23:59:59'
            ])
            ->groupBy('produk.id_produk', 'produk.nama_produk')
            ->orderBy('total_qty', 'desc')
            ->limit(10)
            ->get();

        // Laporan per kasir
        $laporanPerKasir = DB::table('penjualan')
            ->join('kasir', 'penjualan.id_kasir', '=', 'kasir.id_kasir')
            ->join('user', 'kasir.id_user', '=', 'user.id_user')
            ->select(
                'user.nama_user as nama_kasir',
                DB::raw('COUNT(*) as total_transaksi'),
                DB::raw('SUM(penjualan.total_pembayaran) as total_penjualan')
            )
            ->whereBetween('penjualan.tanggal_penjualan', [
                $tanggalMulai . ' 00:00:00',
                $tanggalSelesai . ' 23:59:59'
            ])
            ->groupBy('kasir.id_kasir', 'user.nama_user')
            ->orderBy('total_penjualan', 'desc')
            ->get();

        // AKTIFKAN kategori - ambil semua kategori
        $kategoriList = DB::table('produk_kategori')
            ->select('id_produk_kategori', 'nama_kategori')
            ->orderBy('nama_kategori', 'asc')
            ->get();

        // Detail Produk Terjual dengan Detail Transaksi
        $detailProduk = DB::table('penjualan_detail')
            ->join('penjualan', 'penjualan_detail.id_penjualan', '=', 'penjualan.id_penjualan')
            ->join('produk', 'penjualan_detail.id_produk', '=', 'produk.id_produk')
            ->leftJoin('produk_kategori', 'produk.id_produk_kategori', '=', 'produk_kategori.id_produk_kategori')
            ->select(
                'produk.id_produk',
                'produk.code_produk',
                'produk.nama_produk',
                'produk.id_produk_kategori',
                'produk_kategori.nama_kategori',
                'penjualan_detail.harga_produk as harga_satuan',
                DB::raw('SUM(penjualan_detail.qty_produk) as total_qty'),
                DB::raw('COUNT(DISTINCT penjualan_detail.id_penjualan) as total_transaksi'),
                DB::raw('SUM(penjualan_detail.subtotal_harga) as total_penjualan'),
                DB::raw('AVG(penjualan_detail.qty_produk) as rata_rata_qty')
            )
            ->whereBetween('penjualan.tanggal_penjualan', [
                $tanggalMulai . ' 00:00:00',
                $tanggalSelesai . ' 23:59:59'
            ])
            ->groupBy(
                'produk.id_produk', 
                'produk.code_produk', 
                'produk.nama_produk',
                'produk.id_produk_kategori',
                'produk_kategori.nama_kategori',
                'penjualan_detail.harga_produk'
            )
            ->orderBy('total_penjualan', 'desc')
            ->get()
            ->map(function($produk) use ($tanggalMulai, $tanggalSelesai) {
                // Ambil detail transaksi untuk setiap produk
                $produk->detailTransaksi = DB::table('penjualan_detail')
                    ->join('penjualan', 'penjualan_detail.id_penjualan', '=', 'penjualan.id_penjualan')
                    ->leftJoin('kasir', 'penjualan.id_kasir', '=', 'kasir.id_kasir')
                    ->leftJoin('user', 'kasir.id_user', '=', 'user.id_user')
                    ->where('penjualan_detail.id_produk', $produk->id_produk)
                    ->whereBetween('penjualan.tanggal_penjualan', [
                        $tanggalMulai . ' 00:00:00',
                        $tanggalSelesai . ' 23:59:59'
                    ])
                    ->select(
                        'penjualan_detail.id_penjualan',
                        'penjualan.tanggal_penjualan',
                        'penjualan_detail.qty_produk as qty',
                        'penjualan_detail.harga_produk as harga_jual',
                        'penjualan_detail.subtotal_harga as subtotal',
                        'user.nama_user as nama_kasir'
                    )
                    ->orderBy('penjualan.tanggal_penjualan', 'desc')
                    ->get();
                
                return $produk;
            });

        return view('laporan.index', compact(
            'transaksi',
            'totalPenjualan',
            'totalTransaksi',
            'totalKembalian',
            'penjualanPerHari',
            'produkTerlaris',
            'laporanPerKasir',
            'detailProduk',
            'kategoriList',
            'tanggalMulai',
            'tanggalSelesai'
        ));
    }

    public function exportPdf(Request $request)
    {
        $tanggalMulai = $request->get('tanggal_mulai', now()->startOfMonth()->format('Y-m-d'));
        $tanggalSelesai = $request->get('tanggal_selesai', now()->format('Y-m-d'));

        $transaksi = DB::table('penjualan')
            ->leftJoin('kasir', 'penjualan.id_kasir', '=', 'kasir.id_kasir')
            ->leftJoin('user', 'kasir.id_user', '=', 'user.id_user')
            ->select('penjualan.*', 'user.nama_user as kasir')
            ->whereBetween('penjualan.tanggal_penjualan', [
                $tanggalMulai . ' 00:00:00',
                $tanggalSelesai . ' 23:59:59'
            ])
            ->orderBy('penjualan.tanggal_penjualan', 'desc')
            ->get();

        $totalPenjualan = $transaksi->sum('total_pembayaran');
        $totalTransaksi = $transaksi->count();
        $totalKembalian = $transaksi->sum('kembalian_pembayaran');

        $produkTerlaris = DB::table('penjualan_detail')
            ->join('penjualan', 'penjualan_detail.id_penjualan', '=', 'penjualan.id_penjualan')
            ->join('produk', 'penjualan_detail.id_produk', '=', 'produk.id_produk')
            ->select(
                'produk.nama_produk',
                DB::raw('SUM(penjualan_detail.qty_produk) as total_qty'),
                DB::raw('SUM(penjualan_detail.subtotal_harga) as total_penjualan')
            )
            ->whereBetween('penjualan.tanggal_penjualan', [
                $tanggalMulai . ' 00:00:00',
                $tanggalSelesai . ' 23:59:59'
            ])
            ->groupBy('produk.id_produk', 'produk.nama_produk')
            ->orderBy('total_qty', 'desc')
            ->limit(10)
            ->get();

        $laporanPerKasir = DB::table('penjualan')
            ->join('kasir', 'penjualan.id_kasir', '=', 'kasir.id_kasir')
            ->join('user', 'kasir.id_user', '=', 'user.id_user')
            ->select(
                'user.nama_user as nama_kasir',
                DB::raw('COUNT(*) as total_transaksi'),
                DB::raw('SUM(penjualan.total_pembayaran) as total_penjualan')
            )
            ->whereBetween('penjualan.tanggal_penjualan', [
                $tanggalMulai . ' 00:00:00',
                $tanggalSelesai . ' 23:59:59'
            ])
            ->groupBy('kasir.id_kasir', 'user.nama_user')
            ->orderBy('total_penjualan', 'desc')
            ->get();

        $detailProduk = DB::table('penjualan_detail')
            ->join('penjualan', 'penjualan_detail.id_penjualan', '=', 'penjualan.id_penjualan')
            ->join('produk', 'penjualan_detail.id_produk', '=', 'produk.id_produk')
            ->leftJoin('produk_kategori', 'produk.id_produk_kategori', '=', 'produk_kategori.id_produk_kategori')
            ->select(
                'produk.id_produk',
                'produk.code_produk',
                'produk.nama_produk',
                'produk.id_produk_kategori',
                'produk_kategori.nama_kategori',
                'penjualan_detail.harga_produk as harga_satuan',
                DB::raw('SUM(penjualan_detail.qty_produk) as total_qty'),
                DB::raw('COUNT(DISTINCT penjualan_detail.id_penjualan) as total_transaksi'),
                DB::raw('SUM(penjualan_detail.subtotal_harga) as total_penjualan'),
                DB::raw('AVG(penjualan_detail.qty_produk) as rata_rata_qty')
            )
            ->whereBetween('penjualan.tanggal_penjualan', [
                $tanggalMulai . ' 00:00:00',
                $tanggalSelesai . ' 23:59:59'
            ])
            ->groupBy(
                'produk.id_produk', 
                'produk.code_produk', 
                'produk.nama_produk',
                'produk.id_produk_kategori',
                'produk_kategori.nama_kategori',
                'penjualan_detail.harga_produk'
            )
            ->orderBy('total_penjualan', 'desc')
            ->get()
            ->map(function($produk) use ($tanggalMulai, $tanggalSelesai) {
                $produk->detailTransaksi = DB::table('penjualan_detail')
                    ->join('penjualan', 'penjualan_detail.id_penjualan', '=', 'penjualan.id_penjualan')
                    ->leftJoin('kasir', 'penjualan.id_kasir', '=', 'kasir.id_kasir')
                    ->leftJoin('user', 'kasir.id_user', '=', 'user.id_user')
                    ->where('penjualan_detail.id_produk', $produk->id_produk)
                    ->whereBetween('penjualan.tanggal_penjualan', [
                        $tanggalMulai . ' 00:00:00',
                        $tanggalSelesai . ' 23:59:59'
                    ])
                    ->select(
                        'penjualan_detail.id_penjualan',
                        'penjualan.tanggal_penjualan',
                        'penjualan_detail.qty_produk as qty',
                        'penjualan_detail.harga_produk as harga_jual',
                        'penjualan_detail.subtotal_harga as subtotal',
                        'user.nama_user as nama_kasir'
                    )
                    ->orderBy('penjualan.tanggal_penjualan', 'desc')
                    ->get();
                return $produk;
            });

        $data = compact(
            'transaksi',
            'totalPenjualan',
            'totalTransaksi',
            'totalKembalian',
            'produkTerlaris',
            'laporanPerKasir',
            'detailProduk',
            'tanggalMulai',
            'tanggalSelesai'
        );

        $pdf = Pdf::loadView('laporan.pdf', $data);
        $pdf->setPaper('a4', 'landscape');
        $filename = 'laporan_penjualan_' . date('Y-m-d_His') . '.pdf';
        return $pdf->download($filename);
    }

    public function exportExcel(Request $request)
    {
        $tanggalMulai = $request->get('tanggal_mulai', now()->startOfMonth()->format('Y-m-d'));
        $tanggalSelesai = $request->get('tanggal_selesai', now()->format('Y-m-d'));

        $transaksi = DB::table('penjualan')
            ->leftJoin('kasir', 'penjualan.id_kasir', '=', 'kasir.id_kasir')
            ->leftJoin('user', 'kasir.id_user', '=', 'user.id_user')
            ->select('penjualan.*', 'user.nama_user as kasir')
            ->whereBetween('penjualan.tanggal_penjualan', [
                $tanggalMulai . ' 00:00:00',
                $tanggalSelesai . ' 23:59:59'
            ])
            ->orderBy('penjualan.tanggal_penjualan', 'desc')
            ->get();

        $produkTerlaris = DB::table('penjualan_detail')
            ->join('penjualan', 'penjualan_detail.id_penjualan', '=', 'penjualan.id_penjualan')
            ->join('produk', 'penjualan_detail.id_produk', '=', 'produk.id_produk')
            ->select(
                'produk.nama_produk',
                DB::raw('SUM(penjualan_detail.qty_produk) as total_qty'),
                DB::raw('SUM(penjualan_detail.subtotal_harga) as total_penjualan')
            )
            ->whereBetween('penjualan.tanggal_penjualan', [
                $tanggalMulai . ' 00:00:00',
                $tanggalSelesai . ' 23:59:59'
            ])
            ->groupBy('produk.id_produk', 'produk.nama_produk')
            ->orderBy('total_qty', 'desc')
            ->limit(10)
            ->get();

        $laporanPerKasir = DB::table('penjualan')
            ->join('kasir', 'penjualan.id_kasir', '=', 'kasir.id_kasir')
            ->join('user', 'kasir.id_user', '=', 'user.id_user')
            ->select(
                'user.nama_user as nama_kasir',
                DB::raw('COUNT(*) as total_transaksi'),
                DB::raw('SUM(penjualan.total_pembayaran) as total_penjualan')
            )
            ->whereBetween('penjualan.tanggal_penjualan', [
                $tanggalMulai . ' 00:00:00',
                $tanggalSelesai . ' 23:59:59'
            ])
            ->groupBy('kasir.id_kasir', 'user.nama_user')
            ->orderBy('total_penjualan', 'desc')
            ->get();

        $detailProduk = DB::table('penjualan_detail')
            ->join('penjualan', 'penjualan_detail.id_penjualan', '=', 'penjualan.id_penjualan')
            ->join('produk', 'penjualan_detail.id_produk', '=', 'produk.id_produk')
            ->leftJoin('produk_kategori', 'produk.id_produk_kategori', '=', 'produk_kategori.id_produk_kategori')
            ->select(
                'produk.id_produk',
                'produk.code_produk',
                'produk.nama_produk',
                'produk.id_produk_kategori',
                'produk_kategori.nama_kategori',
                'penjualan_detail.harga_produk as harga_satuan',
                DB::raw('SUM(penjualan_detail.qty_produk) as total_qty'),
                DB::raw('COUNT(DISTINCT penjualan_detail.id_penjualan) as total_transaksi'),
                DB::raw('SUM(penjualan_detail.subtotal_harga) as total_penjualan'),
                DB::raw('AVG(penjualan_detail.qty_produk) as rata_rata_qty')
            )
            ->whereBetween('penjualan.tanggal_penjualan', [
                $tanggalMulai . ' 00:00:00',
                $tanggalSelesai . ' 23:59:59'
            ])
            ->groupBy(
                'produk.id_produk', 
                'produk.code_produk', 
                'produk.nama_produk',
                'produk.id_produk_kategori',
                'produk_kategori.nama_kategori',
                'penjualan_detail.harga_produk'
            )
            ->orderBy('total_penjualan', 'desc')
            ->get()
            ->map(function($produk) use ($tanggalMulai, $tanggalSelesai) {
                $produk->detailTransaksi = DB::table('penjualan_detail')
                    ->join('penjualan', 'penjualan_detail.id_penjualan', '=', 'penjualan.id_penjualan')
                    ->leftJoin('kasir', 'penjualan.id_kasir', '=', 'kasir.id_kasir')
                    ->leftJoin('user', 'kasir.id_user', '=', 'user.id_user')
                    ->where('penjualan_detail.id_produk', $produk->id_produk)
                    ->whereBetween('penjualan.tanggal_penjualan', [
                        $tanggalMulai . ' 00:00:00',
                        $tanggalSelesai . ' 23:59:59'
                    ])
                    ->select(
                        'penjualan_detail.id_penjualan',
                        'penjualan.tanggal_penjualan',
                        'penjualan_detail.qty_produk as qty',
                        'penjualan_detail.harga_produk as harga_jual',
                        'penjualan_detail.subtotal_harga as subtotal',
                        'user.nama_user as nama_kasir'
                    )
                    ->orderBy('penjualan.tanggal_penjualan', 'desc')
                    ->get();
                return $produk;
            });

        $totalPenjualan = $transaksi->sum('total_pembayaran');
        $totalTransaksi = $transaksi->count();
        $totalKembalian = $transaksi->sum('kembalian_pembayaran');

        $data = compact(
            'transaksi',
            'totalPenjualan',
            'totalTransaksi',
            'totalKembalian',
            'produkTerlaris',
            'laporanPerKasir',
            'detailProduk',
            'tanggalMulai',
            'tanggalSelesai'
        );

        $filename = 'laporan_penjualan_' . date('Y-m-d_His') . '.xlsx';
        return Excel::download(new LaporanExport($data), $filename);
    }
}