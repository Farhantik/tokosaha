<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Keuangan - Excel Export</title>
</head>

<body>
    <table border="1" cellpadding="0" cellspacing="0">
        <thead>
            <!-- ══════════════════════════════════════════════════════ -->
            <!-- HEADER UTAMA -->
            <!-- ══════════════════════════════════════════════════════ -->
            <tr>
                <th colspan="6"
                    style="text-align:center; font-size:22px; font-weight:900; background-color:#15803d; color:white; padding:18px; letter-spacing:1.5px;">
                    LAPORAN KEUANGAN
                </th>
            </tr>
            <tr>
                <th colspan="6"
                    style="text-align:center; background-color:#166534; color:#d1fae5; padding:10px; font-size:13px; font-weight:600;">
                    Periode: {{ \Carbon\Carbon::parse($tanggalMulai)->format('d F Y') }} s/d
                    {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d F Y') }}
                </th>
            </tr>
            <tr>
                <th colspan="6"
                    style="text-align:center; background-color:#14532d; color:#86efac; padding:6px; font-size:11px; font-weight:normal;">
                    Dicetak pada: {{ date('d F Y H:i:s') }} WIB
                </th>
            </tr>

            <!-- Spacer -->
            <tr>
                <th colspan="6" style="background-color:#ffffff; padding:8px; border:none;"></th>
            </tr>

            <!-- ══════════════════════════════════════════════════════ -->
            <!-- RINGKASAN KEUANGAN -->
            <!-- ══════════════════════════════════════════════════════ -->
            <tr>
                <th colspan="6"
                    style="font-weight:800; background-color:#15803d; color:white; padding:12px; font-size:14px; text-align:left;">
                    RINGKASAN KEUANGAN
                </th>
            </tr>

            <!-- Total Pemasukan -->
            <tr style="background-color:#f0fdf4;">
                <th colspan="3"
                    style="text-align:left; padding:12px 16px; font-weight:700; color:#1e293b; font-size:13px;">
                    Total Pemasukan
                </th>
                <th colspan="3"
                    style="text-align:right; padding:12px 16px; color:#15803d; font-weight:800; font-size:14px;">
                    Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
                </th>
            </tr>

            <!-- Total Pengeluaran -->
            <tr style="background-color:#fff1f2;">
                <th colspan="3"
                    style="text-align:left; padding:12px 16px; font-weight:700; color:#1e293b; font-size:13px;">
                    Total Pengeluaran
                </th>
                <th colspan="3"
                    style="text-align:right; padding:12px 16px; color:#dc2626; font-weight:800; font-size:14px;">
                    Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                </th>
            </tr>

            <!-- Saldo Bersih -->
            <tr style="background-color:{{ $saldoBersih >= 0 ? '#f0fdfa' : '#fef2f2' }};">
                <th colspan="3"
                    style="text-align:left; padding:12px 16px; font-weight:800; color:#1e293b; font-size:13px;">
                    Saldo Bersih
                </th>
                <th colspan="3"
                    style="text-align:right; padding:12px 16px; font-weight:900; font-size:15px; color:{{ $saldoBersih >= 0 ? '#0f766e' : '#dc2626' }};">
                    Rp {{ number_format($saldoBersih, 0, ',', '.') }}
                </th>
            </tr>

            <!-- Total Transaksi -->
            <tr style="background-color:#eff6ff;">
                <th colspan="3"
                    style="text-align:left; padding:12px 16px; font-weight:700; color:#1e293b; font-size:13px;">
                    Total Transaksi
                </th>
                <th colspan="3"
                    style="text-align:right; padding:12px 16px; color:#1d4ed8; font-weight:800; font-size:13px;">
                    {{ number_format($totalTransaksi, 0, ',', '.') }} transaksi
                </th>
            </tr>

            <!-- Spacer -->
            <tr>
                <th colspan="6" style="background-color:#ffffff; padding:10px; border:none;"></th>
            </tr>

            <!-- ══════════════════════════════════════════════════════ -->
            <!-- DETAIL TRANSAKSI -->
            <!-- ══════════════════════════════════════════════════════ -->
            <tr>
                <th colspan="6"
                    style="font-weight:800; background-color:#15803d; color:white; padding:12px; font-size:14px; text-align:left;">
                    DETAIL TRANSAKSI
                </th>
            </tr>

            <!-- Header Tabel Detail -->
            <tr style="background-color:#dcfce7;">
                <th style="text-align:center; padding:10px; font-weight:700; color:#14532d; font-size:11px;">No</th>
                <th style="text-align:center; padding:10px; font-weight:700; color:#14532d; font-size:11px;">Tanggal
                </th>
                <th style="text-align:left; padding:10px; font-weight:700; color:#14532d; font-size:11px;">Jenis</th>
                <th style="text-align:left; padding:10px; font-weight:700; color:#14532d; font-size:11px;">Keterangan
                </th>
                <th style="text-align:right; padding:10px; font-weight:700; color:#14532d; font-size:11px;">Pemasukan
                </th>
                <th style="text-align:right; padding:10px; font-weight:700; color:#14532d; font-size:11px;">Pengeluaran
                </th>
            </tr>
        </thead>

        <tbody>
            @if ($transaksi->count() > 0)
                @foreach ($transaksi as $index => $t)
                    @php
                        $isPemasukan = str_contains(strtoupper($t->jenis->jenis_keuangan ?? ''), 'PEMASUKAN');
                        $bgColor = $index % 2 == 0 ? '#ffffff' : '#f9fffe';
                    @endphp
                    <tr style="background-color:{{ $bgColor }};">
                        <!-- No -->
                        <td style="text-align:center; padding:10px; color:#6b7280; font-size:11px;">
                            {{ $index + 1 }}
                        </td>

                        <!-- Tanggal -->
                        <td style="text-align:center; padding:10px; color:#374151; font-size:11px;">
                            @if ($t->penjualan)
                                {{ \Carbon\Carbon::parse($t->penjualan->tanggal_penjualan)->format('d/m/Y H:i') }}
                            @elseif($t->penerimaan)
                                {{ \Carbon\Carbon::parse($t->penerimaan->tanggal_penerimaan)->format('d/m/Y H:i') }}
                            @else
                                -
                            @endif
                        </td>

                        <!-- Jenis -->
                        <td style="padding:10px; color:#374151; font-size:11px; font-weight:600;">
                            {{ $t->jenis->jenis_keuangan ?? '-' }}
                        </td>

                        <!-- Keterangan -->
                        <td style="padding:10px; color:#6b7280; font-size:11px;">
                            @if ($t->penjualan)
                                Penjualan #{{ $t->penjualan->id_penjualan }}
                            @elseif($t->penerimaan)
                                Penerimaan #{{ $t->penerimaan->id_penerimaan }}
                            @else
                                {{ $t->keterangan ?? '-' }}
                            @endif
                        </td>

                        <!-- Pemasukan -->
                        <td
                            style="text-align:right; padding:10px; font-weight:700; font-size:12px; color:{{ $isPemasukan ? '#15803d' : '#e5e7eb' }};">
                            @if ($isPemasukan)
                                Rp {{ number_format($t->total_keuangan, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>

                        <!-- Pengeluaran -->
                        <td
                            style="text-align:right; padding:10px; font-weight:700; font-size:12px; color:{{ !$isPemasukan ? '#dc2626' : '#e5e7eb' }};">
                            @if (!$isPemasukan)
                                Rp {{ number_format($t->total_keuangan, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach

                <!-- Total Row -->
                <tr style="background-color:#dcfce7; border-top:2px solid #15803d;">
                    <td colspan="4"
                        style="text-align:right; padding:12px; font-weight:800; color:#14532d; font-size:12px;">
                        TOTAL
                    </td>
                    <td style="text-align:right; padding:12px; font-weight:800; color:#15803d; font-size:13px;">
                        Rp
                        {{ number_format($transaksi->where(function ($t) {return str_contains(strtoupper($t->jenis->jenis_keuangan ?? ''), 'PEMASUKAN');})->sum('total_keuangan'),0,',','.') }}
                    </td>
                    <td style="text-align:right; padding:12px; font-weight:800; color:#dc2626; font-size:13px;">
                        Rp
                        {{ number_format($transaksi->where(function ($t) {return !str_contains(strtoupper($t->jenis->jenis_keuangan ?? ''), 'PEMASUKAN');})->sum('total_keuangan'),0,',','.') }}
                    </td>
                </tr>
            @else
                <tr>
                    <td colspan="6"
                        style="text-align:center; padding:30px; font-style:italic; color:#9ca3af; background-color:#f9fffe; font-size:12px;">
                        Tidak ada data transaksi pada periode ini
                    </td>
                </tr>
            @endif
        </tbody>

        <tfoot>
            <!-- Spacer -->
            <tr>
                <th colspan="6" style="background-color:#ffffff; padding:8px; border:none;"></th>
            </tr>

            <!-- Footer -->
            <tr>
                <th colspan="6"
                    style="text-align:center; padding:14px; background-color:#f0fdf4; border-top:3px solid #15803d; font-size:10px; font-style:italic; color:#6b7280; font-weight:normal;">
                    Laporan ini digenerate secara otomatis oleh sistem pada {{ date('d F Y H:i:s') }} WIB - Dokumen ini
                    sah tanpa tanda tangan dan meterai
                </th>
            </tr>
        </tfoot>
    </table>
</body>

</html>
