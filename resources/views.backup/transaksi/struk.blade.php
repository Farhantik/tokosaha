<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ str_pad($penjualan->id_penjualan, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: 52mm auto;
            margin: 0mm;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            line-height: 1.5;
            width: 52mm;
            margin: 0 auto;
            padding: 0;
            background: white;
            color: #000;
            font-weight: 600;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .receipt {
            width: 100%;
            max-width: 52mm;
            padding: 3mm 2mm 5mm 2mm;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 3mm;
        }

        .store-name {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 3px;
            margin-bottom: 1.5mm;
        }

        .store-address {
            font-size: 10px;
            line-height: 1.4;
            margin-bottom: 0.5mm;
            font-weight: 600;
        }

        .store-phone {
            font-size: 10px;
            margin-bottom: 2.5mm;
            font-weight: 600;
        }

        /* Separator */
        .separator {
            text-align: center;
            margin: 2mm 0;
            font-size: 8px;
            letter-spacing: -1.2px;
            font-weight: bold;
        }

        /* Transaction Info */
        .trans-info {
            font-size: 10px;
            margin: 2.5mm 0;
            font-weight: 600;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 0.8mm;
        }

        .info-label {
            display: table-cell;
            width: 38%;
            text-align: left;
            font-weight: 600;
        }

        .info-colon {
            display: table-cell;
            width: 3%;
            text-align: center;
            font-weight: bold;
        }

        .info-value {
            display: table-cell;
            width: 59%;
            text-align: left;
            font-weight: bold;
            word-wrap: break-word;
        }

        /* Items */
        .items {
            margin: 2.5mm 0;
        }

        .item {
            margin-bottom: 2mm;
            font-size: 10px;
        }

        .item-name {
            font-weight: bold;
            margin-bottom: 0.8mm;
            word-wrap: break-word;
        }

        .item-details {
            display: table;
            width: 100%;
            font-size: 9px;
            font-weight: 600;
            table-layout: fixed;
        }

        .item-qty-price {
            display: table-cell;
            width: 45%;
            text-align: left;
            padding-left: 2mm;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .item-subtotal {
            display: table-cell;
            width: 55%;
            text-align: right;
            font-weight: bold;
            padding-right: 2mm;
        }

        /* Totals */
        .totals {
            margin-top: 2.5mm;
            font-size: 11px;
        }

        .total-row {
            display: table;
            width: 100%;
            margin-bottom: 0.8mm;
            table-layout: fixed;
        }

        .total-label {
            display: table-cell;
            width: 28%;
            text-align: left;
            font-weight: bold;
        }

        .total-value {
            display: table-cell;
            width: 72%;
            text-align: right;
            font-weight: bold;
            padding-right: 2mm;
            word-wrap: break-word;
        }

        .grand-total {
            font-size: 14px;
            font-weight: bold;
            margin: 2mm 0;
            padding: 2mm 0;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
        }

        .payment-row {
            font-size: 11px;
            margin-top: 2mm;
            font-weight: bold;
        }

        .change-row {
            font-size: 11px;
            font-weight: bold;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 3.5mm;
            font-size: 10px;
            padding-bottom: 4mm;
        }

        .thank-you {
            font-weight: bold;
            margin-bottom: 2mm;
            line-height: 1.4;
        }

        .footer-note {
            font-size: 9px;
            line-height: 1.4;
            font-style: italic;
            font-weight: 600;
        }

        /* Print Media */
        @media print {
            body {
                width: 52mm;
                padding: 0;
                margin: 0;
            }

            .receipt {
                max-width: 52mm;
                padding: 3mm 2mm 5mm 2mm;
            }

            .no-print {
                display: none !important;
            }

            @page {
                size: 52mm auto;
                margin: 0;
            }
        }

        /* Buttons */
        .no-print {
            text-align: center;
            margin: 10mm auto;
            padding: 5mm;
            background: #f3f4f6;
        }

        .btn {
            font-family: Arial, sans-serif;
            padding: 10px 20px;
            margin: 0 5px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-print {
            background: #3b82f6;
            color: white;
        }

        .btn-print:hover {
            background: #2563eb;
            transform: scale(1.05);
        }

        .btn-close {
            background: #6b7280;
            color: white;
        }

        .btn-close:hover {
            background: #4b5563;
            transform: scale(1.05);
        }
    </style>
</head>

<body>
    <div class="receipt">
        <!-- Header -->
        <div class="header">
            <div class="store-name">TOKO SAHABAT</div>
            <div class="store-address">Jl. Wonorejo, Rungkut<br>Surabaya</div>
            <div class="store-phone">Telp: 081234567890</div>
        </div>

        <div class="separator">========================</div>

        <!-- Transaction Info -->
        <div class="trans-info">
            <div class="info-row">
                <span class="info-label">No. Transaksi</span>
                <span class="info-colon">:</span>
                <span class="info-value">#{{ str_pad($penjualan->id_penjualan, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal</span>
                <span class="info-colon">:</span>
                <span
                    class="info-value">{{ \Carbon\Carbon::parse($penjualan->tanggal_penjualan)->format('d/m/Y H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Kasir</span>
                <span class="info-colon">:</span>
                <span class="info-value">{{ $penjualan->kasir ?? 'Admin Toko' }}</span>
            </div>
        </div>

        <div class="separator">================================</div>

        <!-- Items -->
        <div class="items">
            @foreach ($detail as $item)
                <div class="item">
                    <div class="item-name">{{ $item->nama_produk }}</div>
                    <div class="item-details">
                        <span class="item-qty-price">{{ $item->qty_produk }} x Rp
                            {{ number_format($item->harga_produk, 0, ',', '.') }}</span>
                        <span class="item-subtotal">Rp {{ number_format($item->subtotal_harga, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="separator">================================</div>

        <!-- Totals -->
        <div class="totals">
            <div class="total-row grand-total">
                <span class="total-label">TOTAL</span>
                <span class="total-value">Rp {{ number_format($penjualan->total_pembayaran, 0, ',', '.') }}</span>
            </div>

            <div class="total-row payment-row">
                <span class="total-label">Tunai</span>
                <span class="total-value">Rp {{ number_format($penjualan->total_bayar, 0, ',', '.') }}</span>
            </div>

            <div class="total-row change-row">
                <span class="total-label">Kembalian</span>
                <span class="total-value">Rp {{ number_format($penjualan->kembalian_pembayaran, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="separator">================================</div>

        <!-- Footer -->
        <div class="footer">
            <div class="thank-you">Terima Kasih Atas<br>Kunjungan Anda!</div>
            <div class="footer-note">
                Barang yang sudah dibeli<br>
                tidak dapat ditukar/dikembalikan
            </div>
        </div>
    </div>

    <!-- Print Buttons -->
    <div class="no-print">
        <button class="btn btn-print" onclick="window.print()">
            🖨️ Cetak Struk
        </button>
        <button class="btn btn-close" onclick="window.close()">
            ✖️ Tutup
        </button>
    </div>

    <script>
        // Optional: Auto print when page loads
        // window.onload = function() {
        //     setTimeout(function() {
        //         window.print();
        //     }, 500);
        // };

        // Optional: Close window after printing
        // window.onafterprint = function() {
        //     window.close();
        // };
    </script>
</body>

</html>
