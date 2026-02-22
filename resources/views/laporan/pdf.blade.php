<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4 portrait;
            margin: 1.2cm 1.1cm 1cm 1.1cm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #1a2e1a;
            background: #fff;
        }

        .header {
            background-color: #065f46;
            border-radius: 10px;
            padding: 18px 22px 14px;
            margin-bottom: 14px;
        }

        .header-title {
            color: #fff;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .header-sub {
            color: #6ee7b7;
            font-size: 9.5px;
            margin-bottom: 12px;
        }

        .header-meta {
            width: 100%;
            border-collapse: collapse;
        }

        .header-meta td {
            padding: 7px 12px;
            color: #fff;
            border-right: 1px solid rgba(255, 255, 255, 0.15);
            width: 33.33%;
            background-color: rgba(255, 255, 255, 0.08);
        }

        .header-meta td:last-child {
            border-right: none;
        }

        .meta-label {
            font-size: 7.5px;
            color: #6ee7b7;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 2px;
        }

        .meta-value {
            font-size: 10.5px;
            font-weight: bold;
            display: block;
        }

        .section-title {
            background-color: #065f46;
            color: white;
            padding: 7px 14px;
            margin: 14px 0 9px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 11px;
        }

        .cards-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 7px 0;
            margin-bottom: 4px;
        }

        .card {
            padding: 11px 13px;
            color: white;
            border-radius: 8px;
        }

        .card-label {
            font-size: 7.5px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 4px;
            opacity: 0.88;
        }

        .card-value {
            font-size: 13px;
            font-weight: bold;
        }

        .c1 {
            background-color: #065f46;
        }

        .c2 {
            background-color: #1e3a8a;
        }

        .c3 {
            background-color: #78350f;
        }

        .c4 {
            background-color: #7f1d1d;
        }

        .chart-box {
            background-color: #f8faff;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 12px 8px;
            margin: 8px 0;
        }

        .chart-title {
            font-size: 10px;
            font-weight: bold;
            color: #1e293b;
            text-align: center;
            margin-bottom: 8px;
        }

        /* ── LINE CHART ── */
        .line-chart-wrap {
            position: relative;
            width: 100%;
        }

        .line-chart-legend {
            display: flex;
            justify-content: center;
            gap: 16px;
            font-size: 8px;
            color: #6b7280;
            margin-bottom: 8px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .legend-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            display: inline-block;
        }

        /* ── BAR CHART (produk & kasir) ── */
        .bar-row {
            margin-bottom: 5px;
        }

        .bar-labels {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }

        .bar-name {
            font-size: 9px;
            color: #064e3b;
            font-weight: 600;
        }

        .bar-val-right {
            font-size: 9px;
            color: #047857;
            font-weight: bold;
            text-align: right;
        }

        .bar-track {
            background-color: #d1fae5;
            border-radius: 3px;
            height: 10px;
            margin-bottom: 1px;
        }

        .bar-fill {
            background-color: #059669;
            border-radius: 3px;
            height: 10px;
        }

        .bar-fill-k {
            background-color: #34d399;
            border-radius: 3px;
            height: 10px;
        }

        .tbl {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 10px;
        }

        .tbl thead tr {
            background-color: #064e3b;
        }

        .tbl thead th {
            padding: 7px 8px;
            color: #fff;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .tbl tbody tr {
            border-bottom: 1px solid #ecfdf5;
        }

        .tbl tbody tr.even {
            background-color: #f0fdf4;
        }

        .tbl tbody td {
            padding: 6px 8px;
            color: #1f2937;
        }

        .tbl tfoot tr {
            background-color: #064e3b;
        }

        .tbl tfoot td {
            padding: 7px 8px;
            font-size: 10px;
            font-weight: bold;
            color: #fff;
        }

        .rbadge {
            display: inline-block;
            width: 17px;
            height: 17px;
            border-radius: 50%;
            font-size: 8px;
            font-weight: bold;
            color: white;
            text-align: center;
            line-height: 17px;
        }

        .rb1 {
            background-color: #f59e0b;
        }

        .rb2 {
            background-color: #9ca3af;
        }

        .rb3 {
            background-color: #d97706;
        }

        .rbn {
            background-color: #10b981;
        }

        .tc {
            text-align: center;
        }

        .tr {
            text-align: right;
        }

        .no-data {
            text-align: center;
            padding: 18px;
            color: #6b7280;
            font-style: italic;
            background-color: #f9fafb;
            border-radius: 7px;
            border: 1px dashed #d1fae5;
            font-size: 10px;
        }

        .footer {
            margin-top: 18px;
            padding: 11px 16px;
            background-color: #064e3b;
            border-radius: 8px;
            text-align: center;
            color: #a7f3d0;
            font-size: 9px;
            line-height: 2;
        }

        .footer strong {
            color: #fff;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <div class="header">
        <div class="header-title">LAPORAN PENJUALAN</div>
        <div class="header-sub">Toko Sahabat &nbsp;&middot;&nbsp; POS System &nbsp;&middot;&nbsp; Dicetak:
            {{ date('d F Y, H:i') }} WIB</div>
        <table class="header-meta">
            <tr>
                <td>
                    <span class="meta-label">Periode Laporan</span>
                    <span class="meta-value">{{ \Carbon\Carbon::parse($tanggalMulai)->format('d M Y') }} &ndash;
                        {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d M Y') }}</span>
                </td>
                <td>
                    <span class="meta-label">Total Transaksi</span>
                    <span class="meta-value">{{ number_format($totalTransaksi ?? 0, 0, ',', '.') }} transaksi</span>
                </td>
                <td>
                    <span class="meta-label">Total Penjualan</span>
                    <span class="meta-value">Rp {{ number_format($totalPenjualan ?? 0, 0, ',', '.') }}</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- RINGKASAN -->
    <div class="section-title">RINGKASAN PENJUALAN</div>
    <table class="cards-table">
        <tr>
            <td>
                <div class="card c1">
                    <div class="card-label">Total Penjualan</div>
                    <div class="card-value">Rp {{ number_format($totalPenjualan ?? 0, 0, ',', '.') }}</div>
                </div>
            </td>
            <td>
                <div class="card c2">
                    <div class="card-label">Total Transaksi</div>
                    <div class="card-value">{{ number_format($totalTransaksi ?? 0, 0, ',', '.') }} Trx</div>
                </div>
            </td>
            <td>
                <div class="card c3">
                    <div class="card-label">Total Kembalian</div>
                    <div class="card-value">Rp {{ number_format($totalKembalian ?? 0, 0, ',', '.') }}</div>
                </div>
            </td>
            <td>
                <div class="card c4">
                    <div class="card-label">Penjualan Bersih</div>
                    <div class="card-value">Rp
                        {{ number_format(($totalPenjualan ?? 0) - ($totalKembalian ?? 0), 0, ',', '.') }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- GRAFIK PENJUALAN HARIAN -->
    <div class="section-title">GRAFIK PENJUALAN HARIAN</div>

    <div class="chart-box" style="padding:14px 12px 10px 12px;">
        <div class="chart-title">Tren Omzet &amp; Jumlah Transaksi Per Hari</div>

        @php
            $hariData = collect($penjualanPerHari ?? [])
                ->map(function ($row) {
                    $r = is_array($row) ? $row : (array) $row;
                    return [
                        'date' => \Carbon\Carbon::parse($r['tanggal'])->format('d/m'),
                        'sales' => (int) $r['total_penjualan'],
                        'trx' => (int) $r['total_transaksi'],
                    ];
                })
                ->values();
        @endphp

        @if ($hariData->count() > 0)

            @php
                /* === DIMENSI STABIL UNTUK A4 PDF === */
                $padLeft = 60;
                $padRight = 40;
                $padTop = 14;

                $chartW = 560;
                $chartH = 170;

                $n = $hariData->count();

                $maxS = $hariData->max('sales') ?: 1;
                $maxT = $hariData->max('trx') ?: 1;

                $topS = $maxS * 1.2;
                $topT = $maxT * 1.2;

                $xPx = fn(int $i): float => round($n > 1 ? ($i / ($n - 1)) * $chartW : $chartW / 2, 2);

                $yS = fn(float $v): float => round($chartH - ($v / $topS) * $chartH, 2);

                $yT = fn(float $v): float => round($chartH - ($v / $topT) * $chartH, 2);

                $makeSegs = function (array $pts): array {
                    $segs = [];
                    for ($i = 1; $i < count($pts); $i++) {
                        [$x1, $y1] = $pts[$i - 1];
                        [$x2, $y2] = $pts[$i];
                        $dx = $x2 - $x1;
                        $dy = $y2 - $y1;
                        $segs[] = [
                            'x1' => $x1,
                            'y1' => $y1,
                            'len' => sqrt($dx * $dx + $dy * $dy),
                            'ang' => rad2deg(atan2($dy, $dx)),
                        ];
                    }
                    return $segs;
                };

                $sPts = [];
                $tPts = [];

                foreach ($hariData as $i => $row) {
                    $sPts[] = [$xPx($i), $yS($row['sales'])];
                    $tPts[] = [$xPx($i), $yT($row['trx'])];
                }

                $sSegs = $makeSegs($sPts);
                $tSegs = $makeSegs($tPts);

                $gridSteps = 4;
                $trxStep = max(1, (int) ceil($maxT / 5));
            @endphp

            <!-- CENTER WRAPPER -->
            <div style="width:100%; text-align:center;">

                <table style="width:660px; margin:0 auto; border-collapse:collapse;">
                    <tr valign="top">

                        <!-- Y LEFT -->
                        <td style="width:{{ $padLeft }}px; padding:0;">
                            <div
                                style="position:relative;height:{{ $chartH }}px;padding-top:{{ $padTop }}px;">
                                @for ($gi = 0; $gi <= $gridSteps; $gi++)
                                    @php
                                        $val = round(($topS / $gridSteps) * $gi);
                                        $y = round($chartH - ($val / $topS) * $chartH);
                                    @endphp
                                    <div
                                        style="position:absolute;right:6px;top:{{ $y }}px;
                                            font-size:7.5px;color:#64748b;
                                            transform:translateY(-50%);">
                                        Rp {{ number_format($val, 0, ',', '.') }}
                                    </div>
                                @endfor
                            </div>
                        </td>

                        <!-- PLOT AREA -->
                        <td style="width:{{ $chartW }}px;padding:0;">

                            <div
                                style="position:relative;
                                    width:{{ $chartW }}px;
                                    height:{{ $chartH + $padTop }}px;
                                    background:#eef4fb;
                                    border-left:1.5px solid #6b7f94;
                                    border-bottom:1.5px solid #6b7f94;
                                    overflow:hidden;
                                    padding-top:{{ $padTop }}px;">

                                <!-- GRID -->
                                @for ($gi = 1; $gi <= $gridSteps; $gi++)
                                    @php $y = round($chartH - ($gi/$gridSteps)*$chartH); @endphp
                                    <div
                                        style="position:absolute;left:0;top:{{ $y }}px;
                                            width:100%;height:1px;background:#cbd5e1;">
                                    </div>
                                @endfor

                                <!-- GARIS OMZET -->
                                @foreach ($sSegs as $seg)
                                    <div
                                        style="position:absolute;
                                            left:{{ $seg['x1'] }}px;
                                            top:{{ $seg['y1'] }}px;
                                            width:{{ $seg['len'] }}px;
                                            height:2.5px;
                                            background:#2563eb;
                                            transform-origin:0 50%;
                                            transform:rotate({{ $seg['ang'] }}deg);">
                                    </div>
                                @endforeach

                                <!-- GARIS TRANSAKSI -->
                                @foreach ($tSegs as $seg)
                                    <div
                                        style="position:absolute;
                                            left:{{ $seg['x1'] }}px;
                                            top:{{ $seg['y1'] }}px;
                                            width:{{ $seg['len'] }}px;
                                            height:2.5px;
                                            background:#10b981;
                                            transform-origin:0 50%;
                                            transform:rotate({{ $seg['ang'] }}deg);">
                                    </div>
                                @endforeach

                            </div>

                            <!-- X LABEL -->
                            <div style="position:relative;width:{{ $chartW }}px;height:20px;">
                                @foreach ($hariData as $i => $row)
                                    <div
                                        style="position:absolute;
                                            left:{{ $xPx($i) }}px;
                                            top:5px;
                                            font-size:7px;
                                            color:#64748b;
                                            transform:translateX(-50%);">
                                        {{ $row['date'] }}
                                    </div>
                                @endforeach
                            </div>

                        </td>

                        <!-- Y RIGHT -->
                        <td style="width:{{ $padRight }}px;padding:0 0 0 8px;">
                            <div
                                style="position:relative;height:{{ $chartH }}px;padding-top:{{ $padTop }}px;">
                                @for ($ti = 0; $ti <= $maxT; $ti += $trxStep)
                                    @php $y = round($chartH - ($ti/$topT)*$chartH); @endphp
                                    <div
                                        style="position:absolute;top:{{ $y }}px;
                                            font-size:7.5px;color:#64748b;
                                            transform:translateY(-50%);">
                                        {{ $ti }}
                                    </div>
                                @endfor
                            </div>
                        </td>

                    </tr>
                </table>

            </div>
        @else
            <div class="no-data">Tidak ada data grafik pada periode ini</div>
        @endif
    </div>
    <!-- TOP 10 PRODUK TERLARIS -->
    <div class="section-title">TOP 10 PRODUK TERLARIS</div>

    @php $produkTerlaris = collect($produkTerlaris ?? []); @endphp

    @if ($produkTerlaris->count() > 0)
        <div class="chart-box">
            <div class="chart-title">Perbandingan Qty Terjual &mdash; Top 10 Produk</div>
            @php $maxQty = $produkTerlaris->take(10)->max('total_qty') ?: 1; @endphp
            @foreach ($produkTerlaris->take(10) as $idx => $produk)
                @php
                    $pArr = is_array($produk) ? $produk : (array) $produk;
                    $pct = round(($pArr['total_qty'] / $maxQty) * 100);
                @endphp
                <div class="bar-row">
                    <table class="bar-labels">
                        <tr>
                            <td class="bar-name">{{ $idx + 1 }}. {{ $pArr['nama_produk'] }}</td>
                            <td class="bar-val-right">{{ number_format($pArr['total_qty'], 0, ',', '.') }} pcs
                                &nbsp;&middot;&nbsp; Rp {{ number_format($pArr['total_penjualan'], 0, ',', '.') }}</td>
                        </tr>
                    </table>
                    <div class="bar-track">
                        <div class="bar-fill" style="width:{{ $pct }}%;"></div>
                    </div>
                </div>
            @endforeach
        </div>
        <table class="tbl">
            <thead>
                <tr>
                    <th class="tc" style="width:28px;">No</th>
                    <th>Nama Produk</th>
                    <th class="tc" style="width:80px;">Qty Terjual</th>
                    <th class="tr" style="width:125px;">Total Penjualan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($produkTerlaris as $index => $produk)
                    @php
                        $pArr = is_array($produk) ? $produk : (array) $produk;
                        $rc = $index < 3 ? ['rb1', 'rb2', 'rb3'][$index] : 'rbn';
                    @endphp
                    <tr class="{{ $index % 2 == 1 ? 'even' : '' }}">
                        <td class="tc"><span class="rbadge {{ $rc }}">{{ $index + 1 }}</span></td>
                        <td>{{ $pArr['nama_produk'] }}</td>
                        <td class="tc" style="font-weight:bold;color:#065f46;">
                            {{ number_format($pArr['total_qty'], 0, ',', '.') }}</td>
                        <td class="tr" style="color:#059669;font-weight:bold;">Rp
                            {{ number_format($pArr['total_penjualan'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="tr">TOTAL KESELURUHAN</td>
                    <td class="tc">{{ number_format($produkTerlaris->sum('total_qty'), 0, ',', '.') }} pcs</td>
                    <td class="tr">Rp {{ number_format($produkTerlaris->sum('total_penjualan'), 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    @else
        <div class="no-data">Tidak ada data produk terlaris</div>
    @endif

    <!-- LAPORAN PER KASIR -->
    <div class="section-title">LAPORAN PER KASIR</div>

    @php $laporanPerKasir = collect($laporanPerKasir ?? []); @endphp

    @if ($laporanPerKasir->count() > 0)
        <div class="chart-box">
            <div class="chart-title">Perbandingan Total Penjualan Per Kasir</div>
            @php $maxK = $laporanPerKasir->max('total_penjualan') ?: 1; @endphp
            @foreach ($laporanPerKasir as $idx => $kasir)
                @php
                    $kArr = is_array($kasir) ? $kasir : (array) $kasir;
                    $pct = round(($kArr['total_penjualan'] / $maxK) * 100);
                    $kLbl =
                        $kArr['total_penjualan'] >= 1000000
                            ? 'Rp ' . number_format($kArr['total_penjualan'] / 1000000, 1) . 'jt'
                            : 'Rp ' . number_format($kArr['total_penjualan'] / 1000, 0) . 'rb';
                @endphp
                <div class="bar-row">
                    <table class="bar-labels">
                        <tr>
                            <td class="bar-name">{{ $idx + 1 }}. {{ $kArr['nama_kasir'] }}</td>
                            <td class="bar-val-right">{{ $kLbl }} &nbsp;&middot;&nbsp;
                                {{ $kArr['total_transaksi'] }} trx</td>
                        </tr>
                    </table>
                    <div class="bar-track">
                        <div class="bar-fill-k" style="width:{{ $pct }}%;"></div>
                    </div>
                </div>
            @endforeach
        </div>
        <table class="tbl">
            <thead>
                <tr>
                    <th class="tc" style="width:28px;">No</th>
                    <th>Nama Kasir</th>
                    <th class="tc" style="width:85px;">Transaksi</th>
                    <th class="tr" style="width:125px;">Total Penjualan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($laporanPerKasir as $index => $kasir)
                    @php
                        $kArr = is_array($kasir) ? $kasir : (array) $kasir;
                        $rc = $index < 3 ? ['rb1', 'rb2', 'rb3'][$index] : 'rbn';
                    @endphp
                    <tr class="{{ $index % 2 == 1 ? 'even' : '' }}">
                        <td class="tc"><span class="rbadge {{ $rc }}">{{ $index + 1 }}</span></td>
                        <td style="font-weight:600;">{{ $kArr['nama_kasir'] }}</td>
                        <td class="tc" style="font-weight:bold;color:#065f46;">
                            {{ number_format($kArr['total_transaksi'], 0, ',', '.') }}</td>
                        <td class="tr" style="color:#7c3aed;font-weight:bold;">Rp
                            {{ number_format($kArr['total_penjualan'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="tr">TOTAL KESELURUHAN</td>
                    <td class="tc">{{ number_format($laporanPerKasir->sum('total_transaksi'), 0, ',', '.') }} trx
                    </td>
                    <td class="tr">Rp {{ number_format($laporanPerKasir->sum('total_penjualan'), 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    @else
        <div class="no-data">Tidak ada data kasir</div>
    @endif

    <!-- DETAIL TRANSAKSI -->
    <div class="section-title">DETAIL TRANSAKSI PENJUALAN</div>

    @php $transaksi = collect($transaksi ?? []); @endphp

    @if ($transaksi->count() > 0)
        <table class="tbl">
            <thead>
                <tr>
                    <th class="tc" style="width:24px;">No</th>
                    <th class="tc" style="width:50px;">ID</th>
                    <th class="tc" style="width:90px;">Tanggal</th>
                    <th>Kasir</th>
                    <th class="tr" style="width:90px;">Kembalian</th>
                    <th class="tr" style="width:105px;">Total Bayar</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaksi as $index => $t)
                    @php $tArr = is_array($t) ? $t : (array) $t; @endphp
                    <tr class="{{ $index % 2 == 1 ? 'even' : '' }}">
                        <td class="tc" style="color:#9ca3af;">{{ $index + 1 }}</td>
                        <td class="tc" style="font-weight:bold;color:#065f46;">
                            #{{ str_pad($tArr['id_penjualan'] ?? 0, 5, '0', STR_PAD_LEFT) }}</td>
                        <td class="tc">
                            {{ \Carbon\Carbon::parse($tArr['tanggal_penjualan'] ?? now())->format('d/m/Y H:i') }}</td>
                        <td>{{ $tArr['kasir'] ?? '-' }}</td>
                        <td class="tr" style="color:#dc2626;">Rp
                            {{ number_format($tArr['kembalian_pembayaran'] ?? 0, 0, ',', '.') }}</td>
                        <td class="tr" style="color:#059669;font-weight:bold;">Rp
                            {{ number_format($tArr['total_pembayaran'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="tc">GRAND TOTAL &mdash; {{ $transaksi->count() }} Transaksi</td>
                    <td class="tr">Rp {{ number_format($transaksi->sum('kembalian_pembayaran'), 0, ',', '.') }}
                    </td>
                    <td class="tr">Rp {{ number_format($transaksi->sum('total_pembayaran'), 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    @else
        <div class="no-data">Tidak ada data transaksi pada periode ini</div>
    @endif

    <!-- FOOTER -->
    <div class="footer">
        <strong>Toko Sahabat &nbsp;&middot;&nbsp; POS System</strong><br>
        Laporan digenerate secara otomatis pada {{ date('d F Y, H:i:s') }} WIB<br>
        Dokumen ini sah tanpa tanda tangan basah
    </div>

</body>

</html>
