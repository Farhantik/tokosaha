<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi #{{ str_pad($penjualan->id_penjualan, 6, '0', STR_PAD_LEFT) }} - TOKO SAHABAT</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            background: #f3f4f6;
            padding: 20px;
        }

        .container {
            max-width: 400px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px dashed #e5e7eb;
        }

        .store-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #10b981;
        }

        .store-address {
            font-size: 12px;
            line-height: 1.5;
            color: #6b7280;
            margin-bottom: 3px;
        }

        .store-phone {
            font-size: 12px;
            color: #6b7280;
        }

        .separator {
            text-align: center;
            margin: 15px 0;
            color: #9ca3af;
            font-size: 10px;
        }

        .trans-info {
            margin: 15px 0;
            font-size: 12px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .info-label {
            color: #6b7280;
        }

        .info-value {
            font-weight: bold;
            color: #1f2937;
        }

        .items {
            margin: 20px 0;
        }

        .item {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f3f4f6;
        }

        .item:last-child {
            border-bottom: none;
        }

        .item-name {
            font-weight: bold;
            margin-bottom: 5px;
            color: #1f2937;
            font-size: 13px;
        }

        .item-details {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #6b7280;
        }

        .item-qty-price {
            flex: 1;
        }

        .item-subtotal {
            font-weight: bold;
            color: #10b981;
        }

        .totals {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px dashed #e5e7eb;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .total-label {
            font-weight: bold;
            color: #4b5563;
        }

        .total-value {
            font-weight: bold;
            color: #1f2937;
        }

        .grand-total {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 12px;
            border-radius: 8px;
            margin: 15px 0;
            font-size: 16px;
        }

        .grand-total .total-value {
            color: white;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px dashed #e5e7eb;
        }

        .thank-you {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 14px;
            color: #10b981;
        }

        .footer-note {
            font-size: 11px;
            line-height: 1.5;
            font-style: italic;
            color: #6b7280;
        }

        .print-button {
            width: 100%;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.3s;
        }

        .print-button:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        .thermal-button {
            width: 100%;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .thermal-button:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        }

        .thermal-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .container {
                max-width: 100%;
                box-shadow: none;
                padding: 10px;
            }

            .print-button,
            .thermal-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="store-name">Point of Sale</div>
            <div class="store-address">
                Jl. Wonorejo, Rungkut<br>
                Surabaya
            </div>
            <div class="store-phone">Telp: 081234567890</div>
        </div>

        <div class="separator">================================</div>

        <div class="trans-info">
            <div class="info-row">
                <span class="info-label">No. Transaksi</span>
                <span class="info-value">#{{ str_pad($penjualan->id_penjualan, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($penjualan->tanggal_penjualan)->format('d/m/Y H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Kasir</span>
                <span class="info-value">{{ $penjualan->kasir }}</span>
            </div>
        </div>

        <div class="separator">================================</div>

        <div class="items">
            @foreach($detail as $item)
            <div class="item">
                <div class="item-name">{{ $item->nama_produk }}</div>
                <div class="item-details">
                    <span class="item-qty-price">{{ $item->qty_produk }} x Rp {{ number_format($item->harga_produk, 0, ',', '.') }}</span>
                    <span class="item-subtotal">Rp {{ number_format($item->subtotal_harga, 0, ',', '.') }}</span>
                </div>
            </div>
            @endforeach
        </div>

        <div class="separator">================================</div>

        <div class="totals">
            <div class="total-row grand-total">
                <span class="total-label">TOTAL</span>
                <span class="total-value">Rp {{ number_format($penjualan->total_bayar, 0, ',', '.') }}</span>
            </div>

            <div class="total-row">
                <span class="total-label">Tunai</span>
                <span class="total-value">Rp {{ number_format($penjualan->total_pembayaran, 0, ',', '.') }}</span>
            </div>

            <div class="total-row">
                <span class="total-label">Kembalian</span>
                <span class="total-value">Rp {{ number_format($penjualan->kembalian_pembayaran, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="separator">================================</div>

        <div class="footer">
            <div class="thank-you">
                Terima Kasih Atas<br>
                Kunjungan Anda!
            </div>
            <div class="footer-note">
                Barang yang sudah dibeli<br>
                tidak dapat ditukar/dikembalikan
            </div>
        </div>

        <button onclick="window.print()" class="print-button">
            Cetak Struk (A4/Letter)
        </button>

        <button onclick="printThermal()" class="thermal-button" id="thermalBtn">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                <rect x="6" y="14" width="12" height="8"></rect>
            </svg>
            <span id="thermalBtnText">Cetak Thermal (58mm)</span>
        </button>
    </div>

    <!-- ✅ LOAD PRINTER HELPER -->
    <script src="{{ asset('js/printer-helper.js') }}"></script>
    
    <script>
        // ============================================
        // PRINT THERMAL MENGGUNAKAN PRINTER HELPER
        // ============================================
        async function printThermal() {
            const btn = document.getElementById('thermalBtn');
            const btnText = document.getElementById('thermalBtnText');
            const originalText = btnText.textContent;
            
            btn.disabled = true;
            btnText.textContent = 'Connecting...';

            try {
                console.log('🖨️ Starting thermal print...');
                
                // ✅ CEK APAKAH PRINTER HELPER SUDAH LOADED
                if (!window.PrinterHelper) {
                    throw new Error('Printer Helper belum dimuat. Refresh halaman dan coba lagi.');
                }

                // ✅ SIAPKAN DATA RECEIPT
                const receiptData = {
                    no_transaksi: '#{{ str_pad($penjualan->id_penjualan, 6, "0", STR_PAD_LEFT) }}',
                    tanggal: '{{ \Carbon\Carbon::parse($penjualan->tanggal_penjualan)->format("d/m/Y H:i") }}',
                    kasir: '{{ $penjualan->kasir }}',
                    total_pembayaran: {{ $penjualan->total_bayar }},
                    total_bayar: {{ $penjualan->total_pembayaran }},
                    kembalian_pembayaran: {{ $penjualan->kembalian_pembayaran }},
                    status_pembayaran: 'lunas',
                    items: [
                        @foreach($detail as $item)
                        {
                            nama_produk: '{{ $item->nama_produk }}',
                            qty_produk: {{ $item->qty_produk }},
                            harga_produk: {{ $item->harga_produk }},
                            subtotal_harga: {{ $item->subtotal_harga }}
                        },
                        @endforeach
                    ]
                };

                console.log('📄 Receipt data:', receiptData);

                btnText.textContent = 'Printing...';

                // ✅ PANGGIL PRINTER HELPER UNTUK PRINT
                const result = await window.PrinterHelper.printReceipt(receiptData);

                console.log('✅ Print result:', result);

                // Success feedback
                btnText.textContent = '✅ Tercetak!';
                setTimeout(() => {
                    btnText.textContent = originalText;
                    btn.disabled = false;
                }, 2000);

            } catch (error) {
                console.error('❌ Print error:', error);
                
                btnText.textContent = originalText;
                btn.disabled = false;

                let errorMsg = error.message;
                let suggestion = '';

                if (error.message.includes('belum di-pair')) {
                    suggestion = '\n\nSolusi:\n1. Buka menu Settings\n2. Klik "Scan" atau "Reconnect"\n3. Pilih printer RPP02N\n4. Coba print lagi';
                } else if (error.message.includes('GATT') || error.message.includes('disconnected')) {
                    suggestion = '\n\nSolusi:\n1. Buka menu Settings\n2. Klik "Reconnect"\n3. Coba print lagi';
                } else if (error.message.includes('timeout')) {
                    suggestion = '\n\nSolusi:\n1. RESTART PRINTER\n2. Buka Settings → Reconnect\n3. Coba lagi';
                }

                alert('❌ Gagal Mencetak!\n\n' + errorMsg + suggestion);
            }
        }

        // ============================================
        // KEYBOARD SHORTCUT
        // ============================================
        window.addEventListener('keydown', function(e) {
            // Ctrl + P untuk print A4
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
            
            // Ctrl + T untuk thermal print
            if (e.ctrlKey && e.key === 't') {
                e.preventDefault();
                printThermal();
            }
        });

        // ============================================
        // LOG PAGE LOADED
        // ============================================
        console.log('✅ Struk page loaded');
        console.log('🖨️ Thermal printer ready');
    </script>
</body>
</html>