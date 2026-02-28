<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - WPOS</title>
    <style>
        /* ══════════════════════════════════════════════
           RESET & BASE
        ══════════════════════════════════════════════ */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11.5px;
            line-height: 1.6;
            color: #1e293b;
            background: #fff;
        }

        .page {
            max-width: 820px;
            margin: 0 auto;
            padding: 36px 40px 40px;
            background: #fff;
        }

        /* ══════════════════════════════════════════════
           KOP SURAT
        ══════════════════════════════════════════════ */
        .kop {
            display: table;
            width: 100%;
            margin-bottom: 0;
        }

        .kop-left {
            display: table-cell;
            width: 64px;
            vertical-align: middle;
            padding-right: 16px;
        }

        .kop-logo {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #15803d, #16a34a);
            border-radius: 12px;
            text-align: center;
            line-height: 56px;
            vertical-align: middle;
        }

        .kop-center {
            display: table-cell;
            vertical-align: middle;
        }

        .kop-brand {
            font-size: 20px;
            font-weight: 900;
            color: #15803d;
            letter-spacing: 0.5px;
            line-height: 1.1;
        }

        .kop-sub {
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            margin-top: 1px;
        }

        .kop-address {
            font-size: 10px;
            color: #9ca3af;
            margin-top: 2px;
        }

        .kop-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            width: 200px;
        }

        .doc-badge {
            display: inline-block;
            background: #15803d;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            padding: 5px 14px;
            border-radius: 4px;
            margin-bottom: 5px;
        }

        .doc-date {
            font-size: 10px;
            color: #6b7280;
        }

        .header-rule {
            height: 3px;
            background: linear-gradient(90deg, #15803d 0%, #16a34a 60%, #d1fae5 100%);
            border-radius: 2px;
            margin: 16px 0 20px;
        }

        /* ══════════════════════════════════════════════
           PERIODE BANNER
        ══════════════════════════════════════════════ */
        .periode-bar {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-left: 4px solid #15803d;
            border-radius: 0 6px 6px 0;
            padding: 9px 16px;
            margin-bottom: 22px;
            display: table;
            width: 100%;
        }

        .periode-left {
            display: table-cell;
            vertical-align: middle;
        }

        .periode-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
        }

        .periode-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6b7280;
        }

        .periode-value {
            font-size: 13px;
            font-weight: 800;
            color: #14532d;
            margin-top: 1px;
        }

        .periode-days {
            font-size: 10px;
            font-weight: 700;
            color: #15803d;
        }

        .periode-days-sub {
            font-size: 9.5px;
            color: #9ca3af;
        }

        /* ══════════════════════════════════════════════
           STAT CARDS
        ══════════════════════════════════════════════ */
        .cards-grid {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 0;
            margin-bottom: 28px;
        }

        .card {
            display: table-cell;
            width: 25%;
            padding: 14px 14px 12px;
            border-radius: 8px;
            vertical-align: top;
        }

        .card-green {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-top: 3px solid #16a34a;
        }

        .card-red {
            background: #fff1f2;
            border: 1px solid #fecdd3;
            border-top: 3px solid #dc2626;
        }

        .card-teal {
            background: #f0fdfa;
            border: 1px solid #99f6e4;
            border-top: 3px solid #0d9488;
        }

        .card-blue {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-top: 3px solid #2563eb;
        }

        .card-icon {
            width: 28px;
            height: 28px;
            border-radius: 7px;
            display: table-cell;
            text-align: center;
            vertical-align: middle;
            margin-bottom: 8px;
        }

        .card-green .card-icon {
            background: #dcfce7;
        }

        .card-red .card-icon {
            background: #fee2e2;
        }

        .card-teal .card-icon {
            background: #ccfbf1;
        }

        .card-blue .card-icon {
            background: #dbeafe;
        }

        .card-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .card-value {
            font-size: 16px;
            font-weight: 900;
            line-height: 1.1;
        }

        .cv-green {
            color: #15803d;
        }

        .cv-red {
            color: #dc2626;
        }

        .cv-teal {
            color: #0f766e;
        }

        .cv-blue {
            color: #1d4ed8;
        }

        /* ══════════════════════════════════════════════
           SECTION HEADER
        ══════════════════════════════════════════════ */
        .sec-hdr {
            background: linear-gradient(135deg, #14532d 0%, #15803d 100%);
            border-radius: 6px 6px 0 0;
            margin-top: 24px;
            display: table;
            width: 100%;
        }

        .sec-hdr-left {
            display: table-cell;
            vertical-align: middle;
            padding: 9px 14px;
        }

        .sec-hdr-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            padding: 9px 14px;
            width: 90px;
        }

        .sec-title {
            font-size: 11.5px;
            font-weight: 700;
            color: #fff;
        }

        .sec-sub-bar {
            background: #f8f8f8;
            border-left: 3px solid #15803d;
            border-right: 1px solid #d1fae5;
            border-bottom: 1px solid #d1fae5;
            padding: 5px 14px;
            font-size: 10px;
            color: #1e293b;
            font-style: italic;
        }

        .sec-pill {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
        }

        /* ══════════════════════════════════════════════
           TABLE
        ══════════════════════════════════════════════ */
        .tbl-wrap {
            border: 1px solid #d1fae5;
            border-top: none;
            border-radius: 0 0 6px 6px;
            overflow: hidden;
            margin-bottom: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background: #f8fffe;
            color: #15803d;
            padding: 8px 12px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1.5px solid #d1fae5;
        }

        tbody td {
            padding: 9px 12px;
            font-size: 11px;
            color: #374151;
            border-bottom: 1px solid #f0fdf4;
            vertical-align: middle;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        tbody tr:nth-child(even) td {
            background: #f9fffc;
        }

        tr.tbl-total td {
            background: #f0fdf4 !important;
            border-top: 1.5px solid #86efac;
            font-weight: 700;
            color: #14532d;
            font-size: 11.5px;
        }

        .tr {
            text-align: right;
        }

        .tc {
            text-align: center;
        }

        /* ══════════════════════════════════════════════
           BADGES
        ══════════════════════════════════════════════ */
        .badge {
            display: inline-block;
            padding: 2px 9px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
        }

        .b-green {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #86efac;
        }

        .b-red {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .b-blue {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }

        /* mini progress bar inside cell */
        .mini-bar-track {
            height: 4px;
            background: #f0fdf4;
            border-radius: 2px;
            overflow: hidden;
            margin-top: 4px;
        }

        .mini-bar-fill-green {
            height: 100%;
            background: linear-gradient(90deg, #15803d, #22c55e);
            border-radius: 2px;
        }

        .mini-bar-fill-red {
            height: 100%;
            background: linear-gradient(90deg, #dc2626, #f87171);
            border-radius: 2px;
        }

        /* ══════════════════════════════════════════════
           NO DATA
        ══════════════════════════════════════════════ */
        .no-data {
            text-align: center;
            padding: 22px;
            color: #9ca3af;
            font-style: italic;
            font-size: 11px;
            border: 1px solid #d1fae5;
            border-top: none;
            border-radius: 0 0 6px 6px;
            background: #fafafa;
        }

        /* ══════════════════════════════════════════════
           RINGKASAN AKHIR  (2-kolom menggunakan table)
        ══════════════════════════════════════════════ */
        .summary-outer {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px 0;
            margin-top: 28px;
        }

        .summary-col-l {
            display: table-cell;
            width: 56%;
            vertical-align: top;
        }

        .summary-col-r {
            display: table-cell;
            width: 44%;
            vertical-align: top;
        }

        /* Bar chart kolom kiri */
        .bar-box {
            border: 1px solid #d1fae5;
            border-radius: 8px;
            overflow: hidden;
        }

        .bar-box-hdr {
            background: linear-gradient(135deg, #14532d, #15803d);
            color: #fff;
            padding: 9px 14px;
            font-size: 11px;
            font-weight: 700;
        }

        .bar-box-body {
            padding: 14px 16px;
        }

        .bar-row-tbl {
            display: table;
            width: 100%;
            margin-bottom: 4px;
        }

        .bar-row-l {
            display: table-cell;
            font-size: 11px;
            color: #374151;
            font-weight: 600;
        }

        .bar-row-r {
            display: table-cell;
            text-align: right;
            font-size: 11px;
            font-weight: 700;
        }

        .bar-track {
            height: 8px;
            background: #f3f4f6;
            border-radius: 4px;
            overflow: hidden;
        }

        .bar-fill-g {
            height: 100%;
            background: linear-gradient(90deg, #15803d, #22c55e);
            border-radius: 4px;
        }

        .bar-fill-r {
            height: 100%;
            background: linear-gradient(90deg, #dc2626, #f87171);
            border-radius: 4px;
        }

        .bar-margin {
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px dashed #d1fae5;
            font-size: 10.5px;
            color: #6b7280;
        }

        /* Saldo kolom kanan */
        .saldo-box {
            border: 1px solid #d1fae5;
            border-radius: 8px;
            overflow: hidden;
        }

        .saldo-box-hdr {
            background: linear-gradient(135deg, #14532d, #15803d);
            color: #fff;
            padding: 9px 14px;
            font-size: 11px;
            font-weight: 700;
        }

        .saldo-row-item {
            display: table;
            width: 100%;
            padding: 10px 16px;
            border-bottom: 1px solid #f0fdf4;
        }

        .saldo-row-item:last-child {
            border-bottom: none;
        }

        .saldo-lbl {
            display: table-cell;
            font-size: 11px;
            color: #374151;
            vertical-align: middle;
        }

        .saldo-val {
            display: table-cell;
            text-align: right;
            font-weight: 700;
            font-size: 12px;
            vertical-align: middle;
        }

        .saldo-final {
            background: #f0fdf4;
            padding: 12px 16px;
            display: table;
            width: 100%;
        }

        .saldo-final .saldo-lbl {
            font-size: 12px;
            font-weight: 700;
            color: #14532d;
        }

        .saldo-final .saldo-val {
            font-size: 15px;
        }

        .status-pill {
            margin-top: 8px;
            text-align: center;
            padding: 9px;
            border-radius: 6px;
        }

        /* ══════════════════════════════════════════════
           FOOTER
        ══════════════════════════════════════════════ */
        .footer {
            margin-top: 30px;
            padding-top: 12px;
            border-top: 1.5px dashed #d1fae5;
            display: table;
            width: 100%;
        }

        .footer-l {
            display: table-cell;
            font-size: 10px;
            color: #9ca3af;
            vertical-align: middle;
        }

        .footer-r {
            display: table-cell;
            text-align: right;
            font-size: 10px;
            color: #9ca3af;
            vertical-align: middle;
        }

        .footer-r strong {
            color: #15803d;
        }

        /* ══════════════════════════════════════════════
           PRINT
        ══════════════════════════════════════════════ */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .page {
                padding: 20px 24px;
                max-width: 100%;
            }

            /* pastikan font awesome icons tercetak */
            .fas,
            .far,
            .fab {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    <div class="page">

        {{-- ══ KOP SURAT ══ --}}
        <div class="kop">
            <div class="kop-left">
                <div class="kop-logo" style="text-align:center; line-height:56px; font-size:24px; color:white;">
                    <i class="fas fa-store"></i>
                </div>
            </div>

            <div class="kop-center">
                <div class="kop-brand">
                    @if (isset($tokoSettings) && $tokoSettings->nama_toko)
                        {{ strtoupper($tokoSettings->nama_toko) }}
                    @else
                        WPOS
                    @endif
                </div>
                <div class="kop-sub">Laporan Keuangan</div>
                <div class="kop-address">
                    @if (isset($tokoSettings) && $tokoSettings->alamat_toko)
                        {{ $tokoSettings->alamat_toko }}
                    @else
                        Sistem Point of Sale
                    @endif
                </div>
            </div>

            <div class="kop-right">
                <div class="doc-badge">Laporan Keuangan</div><br>
                <div class="doc-date">Dicetak: {{ now()->format('d/m/Y H:i') }} WIB</div>
            </div>
        </div>

        <div class="header-rule"></div>

        {{-- ══ PERIODE BANNER ══ --}}
        <div class="periode-bar">
            <div class="periode-left">
                <div class="periode-label">Periode Laporan</div>
                <div class="periode-value">
                    {{ \Carbon\Carbon::parse($tanggalMulai)->format('d F Y') }}
                    &nbsp;&mdash;&nbsp;
                    {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d F Y') }}
                </div>
            </div>
            <div class="periode-right">
                @php $diffDays = \Carbon\Carbon::parse($tanggalMulai)->diffInDays(\Carbon\Carbon::parse($tanggalSelesai)) + 1; @endphp
                <div class="periode-days">{{ $diffDays }} hari</div>
                <div class="periode-days-sub">Durasi periode</div>
            </div>
        </div>

        {{-- ══ STAT CARDS ══ --}}
        <div class="cards-grid">
            <div class="card card-green">
                <div class="card-label">Total Pemasukan</div>
                <div class="card-value cv-green">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
            </div>

            <div class="card card-red">
                <div class="card-label">Total Pengeluaran</div>
                <div class="card-value cv-red">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
            </div>

            <div class="card {{ $saldoBersih >= 0 ? 'card-teal' : 'card-red' }}">
                <div class="card-label">Saldo Bersih</div>
                <div class="card-value" style="color:{{ $saldoBersih >= 0 ? '#0f766e' : '#dc2626' }};">
                    Rp {{ number_format($saldoBersih, 0, ',', '.') }}
                </div>
            </div>

            <div class="card card-blue">
                <div class="card-label">Total Transaksi</div>
                <div class="card-value cv-blue">{{ number_format($totalTransaksi) }}</div>
            </div>
        </div>

        {{-- ══ DETAIL PEMASUKAN ══ --}}
        <div class="sec-hdr">
            <div class="sec-hdr-left">
                <div class="sec-title">Detail Pemasukan</div>
            </div>
            @if ($pemasukan->count() > 0)
                <div class="sec-hdr-right">
                    <span class="sec-pill">{{ $pemasukan->count() }} jenis</span>
                </div>
            @endif
        </div>
        <div class="sec-sub-bar">Ringkasan sumber pemasukan pada periode ini</div>

        @if ($pemasukan->count() > 0)
            <div class="tbl-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="width:4%;" class="tc">No</th>
                            <th style="width:54%;">Jenis Pemasukan</th>
                            <th style="width:18%;" class="tc">Frekuensi</th>
                            <th style="width:24%;" class="tr">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pemasukan as $index => $item)
                            @php $pct = $totalPemasukan > 0 ? round($item->total / $totalPemasukan * 100) : 0; @endphp
                            <tr>
                                <td class="tc" style="color:#9ca3af; font-size:10px;">{{ $index + 1 }}</td>
                                <td>
                                    <div style="font-weight:600;">{{ $item->jenis_keuangan }}</div>
                                    <div class="mini-bar-track">
                                        <div class="mini-bar-fill-green" style="width:{{ $pct }}%;"></div>
                                    </div>
                                </td>
                                <td class="tc"><span
                                        class="badge b-blue">{{ number_format($item->jumlah) }}x</span></td>
                                <td class="tr" style="font-weight:700; color:#15803d;">Rp
                                    {{ number_format($item->total, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr class="tbl-total">
                            <td colspan="3" class="tr" style="padding-right:12px;">TOTAL PEMASUKAN</td>
                            <td class="tr" style="color:#15803d;">Rp
                                {{ number_format($totalPemasukan, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @else
            <div class="no-data">Tidak ada data pemasukan pada periode ini</div>
        @endif

        {{-- ══ DETAIL PENGELUARAN ══ --}}
        <div class="sec-hdr">
            <div class="sec-hdr-left">
                <div class="sec-title">Detail Pengeluaran</div>
            </div>
            @if ($pengeluaran->count() > 0)
                <div class="sec-hdr-right">
                    <span class="sec-pill">{{ $pengeluaran->count() }} jenis</span>
                </div>
            @endif
        </div>
        <div class="sec-sub-bar">Ringkasan sumber pengeluaran pada periode ini</div>

        @if ($pengeluaran->count() > 0)
            <div class="tbl-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="width:4%;" class="tc">No</th>
                            <th style="width:54%;">Jenis Pengeluaran</th>
                            <th style="width:18%;" class="tc">Frekuensi</th>
                            <th style="width:24%;" class="tr">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pengeluaran as $index => $item)
                            @php $pct = $totalPengeluaran > 0 ? round($item->total / $totalPengeluaran * 100) : 0; @endphp
                            <tr>
                                <td class="tc" style="color:#9ca3af; font-size:10px;">{{ $index + 1 }}</td>
                                <td>
                                    <div style="font-weight:600;">{{ $item->jenis_keuangan }}</div>
                                    <div class="mini-bar-track" style="background:#fff1f2;">
                                        <div class="mini-bar-fill-red" style="width:{{ $pct }}%;"></div>
                                    </div>
                                </td>
                                <td class="tc"><span
                                        class="badge b-blue">{{ number_format($item->jumlah) }}x</span></td>
                                <td class="tr" style="font-weight:700; color:#dc2626;">Rp
                                    {{ number_format($item->total, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr class="tbl-total">
                            <td colspan="3" class="tr" style="padding-right:12px;">TOTAL PENGELUARAN</td>
                            <td class="tr" style="color:#dc2626;">Rp
                                {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @else
            <div class="no-data">Tidak ada data pengeluaran pada periode ini</div>
        @endif

        {{-- ══ RIWAYAT TRANSAKSI ══ --}}
        <div class="sec-hdr">
            <div class="sec-hdr-left">
                <div class="sec-title">Riwayat Transaksi Keuangan</div>
            </div>
            @if ($transaksi->count() > 0)
                <div class="sec-hdr-right">
                    <span class="sec-pill">{{ $transaksi->count() }} data</span>
                </div>
            @endif
        </div>
        <div class="sec-sub-bar">Semua transaksi masuk dan keluar pada periode ini</div>

        @if ($transaksi->count() > 0)
            <div class="tbl-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="width:4%;" class="tc">No</th>
                            <th style="width:15%;">Tanggal</th>
                            <th style="width:45%;">Keterangan</th>
                            <th style="width:22%;" class="tr">Nominal</th>
                            <th style="width:14%;" class="tc">Tipe</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transaksi as $index => $item)
                            @php
                                $tanggal = null;
                                $jenisKeuangan = $item->jenis->jenis_keuangan ?? '-';
                                if ($item->penjualan) {
                                    $tanggal = $item->penjualan->tanggal_penjualan;
                                } elseif ($item->penerimaan) {
                                    $tanggal = $item->penerimaan->tanggal_penerimaan;
                                }
                                $isPemasukan = str_contains(strtoupper($jenisKeuangan), 'PEMASUKAN');
                            @endphp
                            <tr>
                                <td class="tc" style="color:#9ca3af; font-size:10px;">{{ $index + 1 }}</td>
                                <td style="font-size:10.5px; white-space:nowrap;">
                                    <div style="color:#374151;">
                                        {{ $tanggal ? \Carbon\Carbon::parse($tanggal)->format('d/m/Y') : '-' }}</div>
                                    <div style="color:#9ca3af; font-size:9.5px;">
                                        {{ $tanggal ? \Carbon\Carbon::parse($tanggal)->format('H:i') : '' }}</div>
                                </td>
                                <td style="font-weight:500;">{{ $jenisKeuangan }}</td>
                                <td class="tr"
                                    style="font-weight:700; color:{{ $isPemasukan ? '#15803d' : '#dc2626' }}; white-space:nowrap;">
                                    {{ $isPemasukan ? '+' : '-' }}&nbsp;Rp
                                    {{ number_format($item->total_keuangan, 0, ',', '.') }}
                                </td>
                                <td class="tc">
                                    @if ($isPemasukan)
                                        <span class="badge b-green">Masuk</span>
                                    @else
                                        <span class="badge b-red">Keluar</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="no-data">Tidak ada transaksi pada periode ini</div>
        @endif

        {{-- ══ RINGKASAN AKHIR (2 kolom) ══ --}}
        <div class="summary-outer">

            {{-- Kolom kiri: bar perbandingan --}}
            <div class="summary-col-l">
                <div class="bar-box">
                    <div class="bar-box-hdr">Perbandingan Arus Kas</div>
                    <div class="bar-box-body">
                        @php
                            $maxVal = max($totalPemasukan, $totalPengeluaran, 1);
                            $pctMasuk = round(($totalPemasukan / $maxVal) * 100);
                            $pctKeluar = round(($totalPengeluaran / $maxVal) * 100);
                        @endphp

                        <div style="margin-bottom:14px;">
                            <div class="bar-row-tbl">
                                <div class="bar-row-l">Pemasukan</div>
                                <div class="bar-row-r" style="color:#15803d;">Rp
                                    {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
                            </div>
                            <div class="bar-track">
                                <div class="bar-fill-g" style="width:{{ $pctMasuk }}%;"></div>
                            </div>
                        </div>

                        <div>
                            <div class="bar-row-tbl">
                                <div class="bar-row-l">Pengeluaran</div>
                                <div class="bar-row-r" style="color:#dc2626;">Rp
                                    {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
                            </div>
                            <div class="bar-track">
                                <div class="bar-fill-r" style="width:{{ $pctKeluar }}%;"></div>
                            </div>
                        </div>

                        @if ($totalPemasukan > 0)
                            @php $ratio = round(($totalPemasukan - $totalPengeluaran) / $totalPemasukan * 100, 1); @endphp
                            <div class="bar-margin">
                                Margin bersih:&nbsp;
                                <strong
                                    style="color:{{ $ratio >= 0 ? '#15803d' : '#dc2626' }};">{{ $ratio }}%</strong>
                                &nbsp;dari total pemasukan
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Kolom kanan: saldo & status --}}
            <div class="summary-col-r">
                <div class="saldo-box">
                    <div class="saldo-box-hdr">Ringkasan Saldo</div>

                    <div class="saldo-row-item">
                        <div class="saldo-lbl">Total Pemasukan</div>
                        <div class="saldo-val" style="color:#15803d;">Rp
                            {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
                    </div>
                    <div class="saldo-row-item">
                        <div class="saldo-lbl">Total Pengeluaran</div>
                        <div class="saldo-val" style="color:#dc2626;">(Rp
                            {{ number_format($totalPengeluaran, 0, ',', '.') }})</div>
                    </div>
                    <div class="saldo-final">
                        <div class="saldo-lbl">Saldo Bersih</div>
                        <div class="saldo-val" style="color:{{ $saldoBersih >= 0 ? '#15803d' : '#dc2626' }};">
                            Rp {{ number_format($saldoBersih, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <div class="status-pill"
                    style="margin-top:8px; border-radius:6px;
                background:{{ $saldoBersih >= 0 ? '#f0fdf4' : '#fff1f2' }};
                border:1px solid {{ $saldoBersih >= 0 ? '#bbf7d0' : '#fecdd3' }};">
                    <span
                        style="font-size:10.5px; font-weight:700; color:{{ $saldoBersih >= 0 ? '#15803d' : '#dc2626' }};">
                        {{ $saldoBersih >= 0 ? 'Keuangan Sehat — Surplus' : 'Perhatian — Defisit' }}
                    </span>
                </div>
            </div>

        </div>

        {{-- ══ FOOTER ══ --}}
        <div class="footer">
            <div class="footer-l">Dokumen dicetak otomatis &mdash; {{ now()->format('d/m/Y H:i:s') }} WIB</div>
            <div class="footer-r">
                &copy; {{ date('Y') }}
                <strong>
                    @if (isset($tokoSettings) && $tokoSettings->nama_toko)
                        {{ $tokoSettings->nama_toko }}
                    @else
                        WPOS
                    @endif
                </strong>
                &mdash; Sistem Point of Sale
            </div>
        </div>

    </div>{{-- end .page --}}

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>

</html>
