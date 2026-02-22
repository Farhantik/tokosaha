<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Bukti Penerimaan - PNM-{{ str_pad($penerimaan->id_penerimaan, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page {
            margin: 0mm;
            size: A4 portrait;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #1f2937;
            background: #fff;
            /* KRITIS: Jangan set width pada body/html — DomPDF otomatis pakai A4 */
        }

        /* ── HEADER ─────────────────────────────── */
        /*
         * SOLUSI UTAMA:
         * DomPDF tidak mendukung nested table dengan baik untuk layout header.
         * Gunakan SATU tabel luar saja, hapus nested table di dalam sel header.
         * Kolom kiri: logo + teks brand (inline / float)
         * Kolom kanan: doc-box
         */
        .header-bar {
            background-color: #16a34a;
            padding: 14px 16px 12px 16px;
        }

        .header-accent {
            background-color: #4ade80;
            height: 3px;
        }

        /* Tabel utama header — SATU level, tidak bersarang */
        .header-main-tbl {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .header-left-cell {
            width: 55%;
            vertical-align: middle;
        }

        .header-right-cell {
            width: 45%;
            vertical-align: middle;
            text-align: right;
        }

        /* Logo — tabel utama sejajarkan logo & brand */
        .logo-tbl {
            border-collapse: collapse;
            width: auto;
        }

        .logo-td {
            vertical-align: middle;
            padding-right: 9px;
        }

        /*
         * TEKNIK LINGKARAN DI DOMPDF:
         * DomPDF tidak bisa render border-radius pada <div>.
         * Solusi: pakai <table> 1 sel dengan border-radius di <td> langsung.
         * width & height harus sama, border-radius = setengahnya.
         */
        .logo-inner-tbl {
            border-collapse: collapse;
            width: 36px;
            height: 36px;
        }

        .logo-inner-td {
            background-color: #ffffff;
            border-radius: 18px;
            width: 36px;
            height: 36px;
            text-align: center;
            vertical-align: middle;
            font-size: 14px;
            font-weight: 900;
            color: #16a34a;
            padding: 0;
        }

        .brand-td {
            vertical-align: middle;
        }

        .brand-name {
            font-size: 14px;
            font-weight: 900;
            color: #ffffff;
        }

        .brand-sub {
            font-size: 7.5px;
            color: #bbf7d0;
            margin-top: 2px;
        }

        /* Doc box — kotak nomor dokumen di kanan */
        .doc-box {
            background-color: rgba(255, 255, 255, 0.18);
            border-radius: 6px;
            padding: 6px 10px;
            text-align: right;
            /* DomPDF: display:block lebih aman daripada inline-block */
            display: block;
        }

        .doc-label {
            font-size: 7px;
            color: #bbf7d0;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .doc-number {
            font-size: 13px;
            font-weight: 900;
            color: #ffffff;
        }

        .doc-date {
            font-size: 7px;
            color: #d1fae5;
        }

        /* ── BODY ─────────────────────────────────── */
        .body {
            padding: 10px 16px;
        }

        /* ── SECTION TITLES ───────────────────────── */
        .sec {
            background-color: #dcfce7;
            border-left: 3px solid #16a34a;
            padding: 4px 8px;
            font-size: 7.5px;
            font-weight: 700;
            color: #15803d;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 6px;
            margin-top: 10px;
        }

        .sec-purple {
            background-color: #f3e8ff;
            border-left: 3px solid #7c3aed;
            padding: 4px 8px;
            font-size: 7.5px;
            font-weight: 700;
            color: #6d28d9;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 6px;
            margin-top: 10px;
        }

        /* ── INFO CARDS ────────────────────────────── */
        .info-wrap {
            width: 100%;
            border-collapse: separate;
            border-spacing: 4px 0;
        }

        .info-card {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 5px;
            padding: 8px 10px;
            vertical-align: top;
            width: 50%;
        }

        .lbl {
            font-size: 7.5px;
            color: #6b7280;
            width: 36%;
            vertical-align: top;
            padding-bottom: 4px;
        }

        .val {
            font-size: 7.5px;
            font-weight: 700;
            color: #111827;
            vertical-align: top;
            padding-bottom: 4px;
        }

        .val-g {
            font-size: 8px;
            font-weight: 900;
            color: #16a34a;
            vertical-align: top;
            padding-bottom: 4px;
        }

        /* Avatar supplier — tabel 1 sel agar border-radius bekerja di DomPDF */
        .sup-av-tbl {
            border-collapse: collapse;
            width: 32px;
            height: 32px;
        }

        .sup-av-td {
            background-color: #16a34a;
            color: #ffffff;
            font-weight: 900;
            font-size: 14px;
            width: 32px;
            height: 32px;
            text-align: center;
            vertical-align: middle;
            border-radius: 6px;
            padding: 0;
        }

        /* ── STATS ──────────────────────────────────── */
        .stats-wrap {
            width: 100%;
            border-collapse: separate;
            border-spacing: 4px 0;
            margin: 7px 0;
        }

        .sbox {
            border-radius: 5px;
            padding: 7px 9px;
            vertical-align: middle;
            width: 33.33%;
        }

        .sbox-g {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
        }

        .sbox-b {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
        }

        .sbox-e {
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
        }

        .slbl {
            font-size: 7px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 2px;
        }

        .sval {
            font-size: 15px;
            font-weight: 900;
            color: #16a34a;
        }

        .sval-b {
            font-size: 15px;
            font-weight: 900;
            color: #2563eb;
        }

        .sval-t {
            font-size: 10px;
            font-weight: 900;
            color: #059669;
        }

        /* ── DATA TABLE ────────────────────────────── */
        .dtbl {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 4px;
        }

        .dtbl thead tr {
            background-color: #16a34a;
        }

        .dtbl thead th {
            color: #fff;
            font-size: 7.5px;
            font-weight: 700;
            padding: 6px 6px;
            text-align: left;
            text-transform: uppercase;
        }

        .dtbl tbody tr.re {
            background-color: #f0fdf4;
        }

        .dtbl tbody tr.ro {
            background-color: #ffffff;
        }

        .dtbl tbody td {
            padding: 5px 6px;
            font-size: 8px;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
            word-wrap: break-word;
        }

        .dtbl tfoot tr {
            background-color: #16a34a;
        }

        .dtbl tfoot td {
            padding: 7px 6px;
            font-size: 9px;
            font-weight: 900;
            color: #fff;
        }

        /* ── LOG TABLE ─────────────────────────────── */
        .ltbl {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 4px;
        }

        .ltbl thead tr {
            background-color: #7c3aed;
        }

        .ltbl thead th {
            color: #fff;
            font-size: 7.5px;
            font-weight: 700;
            padding: 6px 6px;
            text-align: left;
            text-transform: uppercase;
        }

        .ltbl tbody tr.re {
            background-color: #faf5ff;
        }

        .ltbl tbody tr.ro {
            background-color: #ffffff;
        }

        .ltbl tbody td {
            padding: 5px 6px;
            font-size: 8px;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
            word-wrap: break-word;
        }

        /* ── UTILS ──────────────────────────────────── */
        .tr {
            text-align: right;
        }

        .tc {
            text-align: center;
        }

        .pn {
            font-weight: 700;
            color: #111827;
            font-size: 8px;
        }

        .ps {
            font-size: 7px;
            color: #9ca3af;
            margin-top: 1px;
        }

        .bb {
            background-color: #dbeafe;
            color: #1d4ed8;
            padding: 1px 5px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 7.5px;
        }

        .bg {
            background-color: #dcfce7;
            color: #15803d;
            padding: 1px 5px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 7.5px;
        }

        .tg {
            color: #16a34a;
            font-weight: 700;
        }

        /* ── NOTE ────────────────────────────────────── */
        .note {
            background-color: #fefce8;
            border: 1px solid #fde68a;
            border-left: 3px solid #f59e0b;
            border-radius: 5px;
            padding: 6px 10px;
            margin-top: 10px;
            margin-bottom: 10px;
            font-size: 7.5px;
            color: #92400e;
        }

        /* ── SIGNATURE ──────────────────────────────── */
        .sig-wrap {
            width: 100%;
            border-collapse: separate;
            border-spacing: 5px 0;
            margin-top: 7px;
        }

        .sig-cell {
            width: 33.33%;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 5px;
            padding: 8px 6px 6px 6px;
            text-align: center;
            vertical-align: top;
        }

        .sig-lbl {
            font-size: 7px;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
        }

        .sig-sp {
            height: 34px;
        }

        .sig-line {
            border-top: 1px solid #9ca3af;
            margin: 0 8px;
            padding-top: 3px;
            font-size: 7px;
            color: #9ca3af;
        }

        /* ── FOOTER ─────────────────────────────────── */
        .footer {
            background-color: #f9fafb;
            border-top: 1.5px solid #e5e7eb;
            padding: 6px 16px;
            margin-top: 12px;
        }

        .footer-tbl {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .fl {
            font-size: 7px;
            color: #9ca3af;
            vertical-align: middle;
        }

        .fr {
            font-size: 7px;
            color: #9ca3af;
            text-align: right;
            vertical-align: middle;
        }

        .fg {
            color: #16a34a;
            font-weight: 700;
        }
    </style>
</head>

<body>

    {{-- ══════════════════════════════════════════════
         HEADER
         KUNCI: Satu tabel flat (tidak nested), table-layout:fixed,
                colgroup eksplisit. Logo pakai float:left.
         ══════════════════════════════════════════════ --}}
    <div class="header-bar">
        <table class="header-main-tbl">
            <colgroup>
                <col style="width:55%">
                <col style="width:45%">
            </colgroup>
            <tr>
                {{-- KOLOM KIRI: Logo + Brand --}}
                <td class="header-left-cell">
                    <table class="logo-tbl">
                        <tr>
                            <td class="logo-td">
                                {{-- Lingkaran via tabel 1 sel — satu-satunya cara border-radius bekerja di DomPDF --}}
                                <table class="logo-inner-tbl">
                                    <tr>
                                        <td class="logo-inner-td">W</td>
                                    </tr>
                                </table>
                            </td>
                            <td class="brand-td">
                                <div class="brand-name">WPOS POS System</div>
                                <div class="brand-sub">Bukti Penerimaan Barang &bull; Dokumen Resmi</div>
                            </td>
                        </tr>
                    </table>
                </td>

                {{-- KOLOM KANAN: Nomor Dokumen --}}
                <td class="header-right-cell">
                    <div class="doc-box">
                        <div class="doc-label">Nomor Dokumen</div>
                        <div class="doc-number">PNM-{{ str_pad($penerimaan->id_penerimaan, 6, '0', STR_PAD_LEFT) }}
                        </div>
                        <div class="doc-date">
                            {{ \Carbon\Carbon::parse($penerimaan->tanggal_penerimaan)->format('d/m/Y H:i') }}</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="header-accent"></div>

    <div class="body">

        {{-- INFO PENERIMAAN & SUPPLIER --}}
        <div class="sec">&#9654; Informasi Penerimaan &amp; Supplier</div>
        <table class="info-wrap">
            <colgroup>
                <col style="width:50%">
                <col style="width:50%">
            </colgroup>
            <tr>
                <td class="info-card">
                    <table style="width:100%; border-collapse:collapse">
                        <tr>
                            <td class="lbl">No. Penerimaan</td>
                            <td class="val-g">PNM-{{ str_pad($penerimaan->id_penerimaan, 6, '0', STR_PAD_LEFT) }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">ID Penerimaan</td>
                            <td class="val">{{ $penerimaan->id_penerimaan }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">Tanggal</td>
                            <td class="val">
                                {{ \Carbon\Carbon::parse($penerimaan->tanggal_penerimaan)->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">Total Item</td>
                            <td class="val">{{ $stats['total_item'] }} item &bull; {{ $stats['total_qty'] }} qty</td>
                        </tr>
                        <tr>
                            <td class="lbl">Total Nilai</td>
                            <td class="val-g">Rp {{ number_format($stats['total_harga'], 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </td>
                <td class="info-card">
                    <table style="width:100%; border-collapse:collapse; margin-bottom:6px">
                        <colgroup>
                            <col style="width:38px">
                            <col>
                        </colgroup>
                        <tr>
                            <td style="vertical-align:middle; width:38px; padding-right:8px">
                                {{-- Avatar supplier: tabel 1 sel agar border-radius bekerja di DomPDF --}}
                                <table class="sup-av-tbl">
                                    <tr>
                                        <td class="sup-av-td">{{ strtoupper(substr($penerimaan->nama_supplier, 0, 1)) }}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td style="vertical-align:middle">
                                <div style="font-size:10px; font-weight:900; color:#111827">
                                    {{ $penerimaan->nama_supplier }}</div>
                                @if ($penerimaan->telp_supplier)
                                    <div style="font-size:7.5px; color:#6b7280; margin-top:2px">
                                        {{ $penerimaan->telp_supplier }}</div>
                                @endif
                            </td>
                        </tr>
                    </table>
                    @if ($penerimaan->alamat_supplier)
                        <table style="width:100%; border-collapse:collapse">
                            <tr>
                                <td class="lbl" style="width:28%">Alamat</td>
                                <td class="val">{{ $penerimaan->alamat_supplier }}</td>
                            </tr>
                        </table>
                    @endif
                </td>
            </tr>
        </table>

        {{-- STATS --}}
        <table class="stats-wrap">
            <colgroup>
                <col style="width:33.33%">
                <col style="width:33.33%">
                <col style="width:33.34%">
            </colgroup>
            <tr>
                <td class="sbox sbox-g">
                    <div class="slbl">Total Item Produk</div>
                    <div class="sval">{{ $stats['total_item'] }}</div>
                </td>
                <td class="sbox sbox-b">
                    <div class="slbl">Total Qty Diterima</div>
                    <div class="sval-b">{{ $stats['total_qty'] }}</div>
                </td>
                <td class="sbox sbox-e">
                    <div class="slbl">Total Nilai Penerimaan</div>
                    <div class="sval-t">Rp {{ number_format($stats['total_harga'], 0, ',', '.') }}</div>
                </td>
            </tr>
        </table>

        {{-- DETAIL PRODUK --}}
        <div class="sec">&#9654; Detail Produk</div>
        <table class="dtbl">
            <colgroup>
                <col style="width:5%">
                <col style="width:11%">
                <col style="width:33%">
                <col style="width:19%">
                <col style="width:10%">
                <col style="width:22%">
            </colgroup>
            <thead>
                <tr>
                    <th class="tc">No</th>
                    <th>Kode</th>
                    <th>Nama Produk</th>
                    <th class="tr">Harga Satuan</th>
                    <th class="tc">Qty</th>
                    <th class="tr">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($detail as $index => $item)
                    <tr class="{{ $index % 2 === 0 ? 're' : 'ro' }}">
                        <td class="tc" style="color:#9ca3af">{{ $index + 1 }}</td>
                        <td style="font-size:7px; color:#9ca3af">{{ $item->code_produk ?? '-' }}</td>
                        <td>
                            <div class="pn">{{ $item->nama_produk }}</div>
                            <div class="ps">{{ $item->nama_kategori ?? '-' }}</div>
                        </td>
                        <td class="tr">Rp {{ number_format($item->harga_produk, 0, ',', '.') }}</td>
                        <td class="tc"><span class="bb">{{ $item->qty_produk }}</span></td>
                        <td class="tr"><span class="tg">Rp
                                {{ number_format($item->subtotal_harga, 0, ',', '.') }}</span></td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="tr" style="letter-spacing:0.4px">TOTAL KESELURUHAN</td>
                    <td class="tr" style="font-size:10px">Rp
                        {{ number_format($penerimaan->total_harga, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- LOG STOCK --}}
        @if ($logStock->count() > 0)
            <div class="sec-purple">&#9654; Riwayat Perubahan Stock</div>
            <table class="ltbl">
                <colgroup>
                    <col style="width:40%">
                    <col style="width:20%">
                    <col style="width:20%">
                    <col style="width:20%">
                </colgroup>
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th class="tc">Qty Masuk</th>
                        <th class="tc">Stock Awal</th>
                        <th class="tc">Stock Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logStock as $i => $log)
                        <tr class="{{ $i % 2 === 0 ? 're' : 'ro' }}">
                            <td>
                                <div class="pn">{{ $log->nama_produk }}</div>
                                @if ($log->code_produk)
                                    <div class="ps">Kode: {{ $log->code_produk }}</div>
                                @endif
                            </td>
                            <td class="tc"><span class="bg">+{{ $log->jumlah_aktivitas }}</span></td>
                            <td class="tc" style="color:#6b7280">{{ $log->jumlah_awal }}</td>
                            <td class="tc"><span
                                    style="font-weight:900; color:#16a34a">{{ $log->jumlah_akhir }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- NOTE --}}
        <div class="note">
            <strong>Catatan:</strong>
            Dokumen ini merupakan bukti resmi penerimaan barang. Menghapus data penerimaan akan
            <strong>mengurangi stock produk</strong> sesuai qty penerimaan dan log stock akan ikut terhapus.
        </div>

        {{-- TANDA TANGAN --}}
        <div class="sec">&#9654; Tanda Tangan</div>
        <table class="sig-wrap">
            <colgroup>
                <col style="width:33.33%">
                <col style="width:33.33%">
                <col style="width:33.34%">
            </colgroup>
            <tr>
                <td class="sig-cell">
                    <div class="sig-lbl">Dibuat Oleh</div>
                    <div class="sig-sp"></div>
                    <div class="sig-line">(____________________)</div>
                </td>
                <td class="sig-cell">
                    <div class="sig-lbl">Diperiksa Oleh</div>
                    <div class="sig-sp"></div>
                    <div class="sig-line">(____________________)</div>
                </td>
                <td class="sig-cell">
                    <div class="sig-lbl">Disetujui Oleh</div>
                    <div class="sig-sp"></div>
                    <div class="sig-line">(____________________)</div>
                </td>
            </tr>
        </table>

    </div>{{-- end body --}}

    {{-- FOOTER --}}
    <div class="footer">
        <table class="footer-tbl">
            <colgroup>
                <col style="width:70%">
                <col style="width:30%">
            </colgroup>
            <tr>
                <td class="fl">
                    <span class="fg">WPOS POS System</span> &bull;
                    Digenerate: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }} &bull;
                    PNM-{{ str_pad($penerimaan->id_penerimaan, 6, '0', STR_PAD_LEFT) }}
                </td>
                <td class="fr">Halaman 1 dari 1</td>
            </tr>
        </table>
    </div>

</body>

</html>
