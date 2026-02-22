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
        $tanggalMulai   = $request->get('tanggal_mulai', now()->startOfMonth()->format('Y-m-d'));
        $tanggalSelesai = $request->get('tanggal_selesai', now()->format('Y-m-d'));

        $query = DB::table('penjualan')
            ->leftJoin('kasir', 'penjualan.id_kasir', '=', 'kasir.id_kasir')
            ->leftJoin('user', 'kasir.id_user', '=', 'user.id_user')
            ->select('penjualan.*', 'user.nama_user as kasir');

        if ($tanggalMulai && $tanggalSelesai) {
            $query->whereBetween('penjualan.tanggal_penjualan', [
                $tanggalMulai . ' 00:00:00',
                $tanggalSelesai . ' 23:59:59'
            ]);
        }

        $transaksi = $query->orderBy('penjualan.tanggal_penjualan', 'desc')->paginate(10);

        $totalPenjualan = DB::table('penjualan')
            ->whereBetween('tanggal_penjualan', [$tanggalMulai . ' 00:00:00', $tanggalSelesai . ' 23:59:59'])
            ->sum('total_pembayaran') ?? 0;

        $totalTransaksi = DB::table('penjualan')
            ->whereBetween('tanggal_penjualan', [$tanggalMulai . ' 00:00:00', $tanggalSelesai . ' 23:59:59'])
            ->count();

        $totalKembalian = DB::table('penjualan')
            ->whereBetween('tanggal_penjualan', [$tanggalMulai . ' 00:00:00', $tanggalSelesai . ' 23:59:59'])
            ->sum('kembalian_pembayaran') ?? 0;

        $penjualanPerHari = DB::table('penjualan')
            ->selectRaw('DATE(tanggal_penjualan) as tanggal')
            ->selectRaw('SUM(total_pembayaran) as total_penjualan')
            ->selectRaw('COUNT(*) as total_transaksi')
            ->whereBetween('tanggal_penjualan', [$tanggalMulai . ' 00:00:00', $tanggalSelesai . ' 23:59:59'])
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        $produkTerlaris = DB::table('penjualan_detail')
            ->join('penjualan', 'penjualan_detail.id_penjualan', '=', 'penjualan.id_penjualan')
            ->join('produk', 'penjualan_detail.id_produk', '=', 'produk.id_produk')
            ->select(
                'produk.nama_produk',
                DB::raw('SUM(penjualan_detail.qty_produk) as total_qty'),
                DB::raw('SUM(penjualan_detail.subtotal_harga) as total_penjualan')
            )
            ->whereBetween('penjualan.tanggal_penjualan', [$tanggalMulai . ' 00:00:00', $tanggalSelesai . ' 23:59:59'])
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
            ->whereBetween('penjualan.tanggal_penjualan', [$tanggalMulai . ' 00:00:00', $tanggalSelesai . ' 23:59:59'])
            ->groupBy('kasir.id_kasir', 'user.nama_user')
            ->orderBy('total_penjualan', 'desc')
            ->get();

        $kategoriList = DB::table('produk_kategori')
            ->select('id_produk_kategori', 'nama_kategori')
            ->orderBy('nama_kategori', 'asc')
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
            ->whereBetween('penjualan.tanggal_penjualan', [$tanggalMulai . ' 00:00:00', $tanggalSelesai . ' 23:59:59'])
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
            ->map(function ($produk) use ($tanggalMulai, $tanggalSelesai) {
                $produk->detailTransaksi = DB::table('penjualan_detail')
                    ->join('penjualan', 'penjualan_detail.id_penjualan', '=', 'penjualan.id_penjualan')
                    ->leftJoin('kasir', 'penjualan.id_kasir', '=', 'kasir.id_kasir')
                    ->leftJoin('user', 'kasir.id_user', '=', 'user.id_user')
                    ->where('penjualan_detail.id_produk', $produk->id_produk)
                    ->whereBetween('penjualan.tanggal_penjualan', [$tanggalMulai . ' 00:00:00', $tanggalSelesai . ' 23:59:59'])
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

    // ══════════════════════════════════════════════════════════════
    //  EXPORT PDF
    // ══════════════════════════════════════════════════════════════
    public function exportPdf(Request $request)
    {
        $tanggalMulai   = $request->get('tanggal_mulai', now()->startOfMonth()->format('Y-m-d'));
        $tanggalSelesai = $request->get('tanggal_selesai', now()->format('Y-m-d'));

        $transaksi = DB::table('penjualan')
            ->leftJoin('kasir', 'penjualan.id_kasir', '=', 'kasir.id_kasir')
            ->leftJoin('user', 'kasir.id_user', '=', 'user.id_user')
            ->select('penjualan.*', 'user.nama_user as kasir')
            ->whereBetween('penjualan.tanggal_penjualan', [
                $tanggalMulai . ' 00:00:00',
                $tanggalSelesai . ' 23:59:59'
            ])
            ->orderBy('penjualan.tanggal_penjualan', 'asc')
            ->get();

        $totalPenjualan = $transaksi->sum('total_pembayaran');
        $totalTransaksi = $transaksi->count();
        $totalKembalian = $transaksi->sum('kembalian_pembayaran');

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

        $chartBase64 = $this->generateChartBase64($penjualanPerHari);

        $pdf = Pdf::loadView('laporan.pdf', compact(
            'transaksi',
            'totalPenjualan',
            'totalTransaksi',
            'totalKembalian',
            'penjualanPerHari',
            'produkTerlaris',
            'laporanPerKasir',
            'tanggalMulai',
            'tanggalSelesai',
            'chartBase64'
        ));

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('laporan_penjualan_' . date('Y-m-d_His') . '.pdf');
    }

    // ══════════════════════════════════════════════════════════════
    //  EXPORT EXCEL
    // ══════════════════════════════════════════════════════════════
    public function exportExcel(Request $request)
    {
        $tanggalMulai   = $request->get('tanggal_mulai', now()->startOfMonth()->format('Y-m-d'));
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
            ->map(function ($produk) use ($tanggalMulai, $tanggalSelesai) {
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

        return Excel::download(
            new LaporanExport($data),
            'laporan_penjualan_' . date('Y-m-d_His') . '.xlsx'
        );
    }

    // ══════════════════════════════════════════════════════════════
    //  HELPER: Generate chart PNG via PHP GD → base64
    // ══════════════════════════════════════════════════════════════
    private function generateChartBase64($penjualanPerHari): string
    {
        $data = collect($penjualanPerHari);
        $n    = $data->count();

        if ($n === 0) return '';
        if (!function_exists('imagecreatetruecolor')) return '';

        // ── Ukuran canvas ─────────────────────────────────────
        $W  = 900;
        $H  = 320;
        $pL = 90;
        $pR = 70;
        $pT = 40;
        $pB = 55;
        $iW = $W - $pL - $pR;
        $iH = $H - $pT - $pB;

        $img = imagecreatetruecolor($W, $H);
        imagealphablending($img, true);
        imagesavealpha($img, true);

        // ── Warna ─────────────────────────────────────────────
        $white     = imagecolorallocate($img, 255, 255, 255);
        $bgPlot    = imagecolorallocate($img, 248, 250, 252);
        $gridCol   = imagecolorallocate($img, 226, 232, 240);
        $axisCol   = imagecolorallocate($img, 148, 163, 184);
        $blueL     = imagecolorallocate($img,  37,  99, 235);
        $blueArea  = imagecolorallocatealpha($img, 37, 99, 235, 110);
        $greenL    = imagecolorallocate($img,   5, 150, 105);
        $greenArea = imagecolorallocatealpha($img, 5, 150, 105, 110);
        $textDark  = imagecolorallocate($img,  71,  85, 105);
        $textBlue  = imagecolorallocate($img,  29,  78, 216);
        $textGreen = imagecolorallocate($img,  21, 128,  61);

        // ── Background ────────────────────────────────────────
        imagefilledrectangle($img, 0,   0,   $W,        $H,        $white);
        imagefilledrectangle($img, $pL, $pT, $pL + $iW, $pT + $iH, $bgPlot);

        // ── Skala max ─────────────────────────────────────────
        $maxO = (float)($data->max('total_penjualan') ?: 1);
        $maxT = (float)($data->max('total_transaksi')  ?: 1);

        $exp  = floor(log10($maxO));
        $nice = pow(10, $exp);
        $maxO = ceil($maxO / $nice) * $nice;
        $maxT = ceil($maxT / 5) * 5 ?: 5;

        // ── Grid horizontal dashed (simulasi manual) ──────────
        $ticks = 4;
        for ($t = 0; $t <= $ticks; $t++) {
            $y = (int)($pT + $iH - ($t / $ticks) * $iH);

            for ($dx = $pL; $dx < $pL + $iW; $dx += 8) {
                imageline($img, $dx, $y, min($dx + 4, $pL + $iW), $y, $gridCol);
            }

            $vO   = ($t / $ticks) * $maxO;
            $lblO = $vO >= 1000000
                ? 'Rp ' . number_format($vO / 1000000, 1) . 'jt'
                : ($vO >= 1000 ? 'Rp ' . number_format($vO / 1000, 0) . 'rb' : 'Rp ' . (int)$vO);
            $lx = max(0, $pL - strlen($lblO) * 6 - 5);
            imagestring($img, 1, $lx, $y - 5, $lblO, $textBlue);

            $vT = round(($t / $ticks) * $maxT);
            $yt = (int)($pT + $iH - ($vT / $maxT) * $iH);
            imagestring($img, 1, $pL + $iW + 6, $yt - 5, $vT . ' trx', $textGreen);
        }

        // ── Hitung koordinat pixel ─────────────────────────────
        $ptsO   = [];
        $ptsT   = [];
        $labels = [];
        $vals   = $data->values();

        foreach ($vals as $i => $row) {
            $row  = is_array($row) ? $row : (array)$row;
            $xRaw = $n > 1 ? $i / ($n - 1) : 0.5;
            $x    = (int)($pL + $xRaw * $iW);
            $yO   = (int)($pT + $iH - ((float)$row['total_penjualan'] / $maxO) * $iH);
            $yT   = (int)($pT + $iH - ((float)$row['total_transaksi']  / $maxT) * $iH);

            $ptsO[]   = [$x, max($pT, min($pT + $iH, $yO))];
            $ptsT[]   = [$x, max($pT, min($pT + $iH, $yT))];
            $labels[] = isset($row['tanggal'])
                ? \Carbon\Carbon::parse($row['tanggal'])->format('d/m')
                : '';
        }

        $botY = $pT + $iH;

        // ── Area fill ─────────────────────────────────────────
        if ($n >= 2) {
            $polyO = [];
            foreach ($ptsO as $p) {
                $polyO[] = $p[0];
                $polyO[] = $p[1];
            }
            $polyO[] = $ptsO[$n - 1][0];
            $polyO[] = $botY;
            $polyO[] = $ptsO[0][0];
            $polyO[] = $botY;
            imagefilledpolygon($img, $polyO, count($polyO) / 2, $blueArea);

            $polyT = [];
            foreach ($ptsT as $p) {
                $polyT[] = $p[0];
                $polyT[] = $p[1];
            }
            $polyT[] = $ptsT[$n - 1][0];
            $polyT[] = $botY;
            $polyT[] = $ptsT[0][0];
            $polyT[] = $botY;
            imagefilledpolygon($img, $polyT, count($polyT) / 2, $greenArea);
        }

        // ── Garis ─────────────────────────────────────────────
        imagesetthickness($img, 3);
        for ($i = 0; $i < $n - 1; $i++) {
            imageline($img, $ptsO[$i][0], $ptsO[$i][1], $ptsO[$i + 1][0], $ptsO[$i + 1][1], $blueL);
            imageline($img, $ptsT[$i][0], $ptsT[$i][1], $ptsT[$i + 1][0], $ptsT[$i + 1][1], $greenL);
        }
        imagesetthickness($img, 1);

        // ── Sumbu ─────────────────────────────────────────────
        imagesetthickness($img, 2);
        imageline($img, $pL, $botY, $pL + $iW, $botY, $axisCol);
        imageline($img, $pL, $pT,   $pL,        $botY, $axisCol);
        imagesetthickness($img, 1);

        // ── Dot + label X ─────────────────────────────────────
        $step = $n > 20 ? (int)ceil($n / 20) : 1;
        for ($i = 0; $i < $n; $i++) {
            imagefilledellipse($img, $ptsO[$i][0], $ptsO[$i][1], 10, 10, $blueL);
            imagefilledellipse($img, $ptsO[$i][0], $ptsO[$i][1],  6,  6, $white);
            imagefilledellipse($img, $ptsT[$i][0], $ptsT[$i][1], 10, 10, $greenL);
            imagefilledellipse($img, $ptsT[$i][0], $ptsT[$i][1],  6,  6, $white);

            if ($i % $step === 0 || $i === $n - 1) {
                $lx = $ptsO[$i][0] - (int)(strlen($labels[$i]) * 3);
                imagestring($img, 1, $lx, $botY + 8, $labels[$i], $textDark);
            }
        }

        // ── Legend ────────────────────────────────────────────
        $legY = 14;
        $midX = (int)($W / 2) - 90;
        imagefilledellipse($img, $midX,        $legY, 10, 10, $blueL);
        imagestring($img, 2,   $midX + 8,  $legY - 7, 'Omzet Penjualan', $textBlue);
        imagefilledellipse($img, $midX + 140, $legY, 10, 10, $greenL);
        imagestring($img, 2,   $midX + 148, $legY - 7, 'Jml Transaksi',   $textGreen);

        // ── Capture → base64 ──────────────────────────────────
        ob_start();
        imagepng($img);
        $raw = ob_get_clean();
        imagedestroy($img);

        return base64_encode($raw);
    }
}
