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

        /* Payment methods section */
        .payment-methods {
            margin-top: 12px;
            padding-top: 10px;
            border-top: 1px dashed #e5e7eb;
        }

        .payment-methods-title {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 6px;
            font-weight: bold;
        }

        .payment-method-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 4px;
            padding: 4px 8px;
            background: #f9fafb;
            border-radius: 4px;
        }

        .payment-method-label {
            color: #374151;
            font-weight: 600;
        }

        .payment-method-amount {
            font-weight: bold;
            color: #10b981;
        }

        .payment-method-row.tunai .payment-method-label {
            color: #059669;
        }

        .payment-method-row.qris .payment-method-label {
            color: #4f46e5;
        }

        .payment-method-row.piutang .payment-method-label {
            color: #ea580c;
        }

        .kembalian-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            margin-top: 6px;
            padding: 6px 8px;
            background: #eff6ff;
            border-radius: 6px;
        }

        .kembalian-label {
            color: #2563eb;
            font-weight: bold;
        }

        .kembalian-value {
            color: #2563eb;
            font-weight: bold;
        }

        .piutang-badge {
            display: inline-block;
            background: #fee2e2;
            color: #dc2626;
            font-size: 11px;
            font-weight: bold;
            padding: 3px 10px;
            border-radius: 999px;
            margin-top: 6px;
        }

        .bayar-sebagian-badge {
            display: inline-block;
            background: #fef9c3;
            color: #d97706;
            font-size: 11px;
            font-weight: bold;
            padding: 3px 10px;
            border-radius: 999px;
            margin-top: 6px;
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
            line-height: 1.6;
            font-style: italic;
            color: #6b7280;
            margin-bottom: 6px;
        }

        .footer-made-by {
            font-size: 10px;
            color: #9ca3af;
            margin-top: 8px;
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

            /* Ensure footer info prints */
            .footer-note,
            .footer-made-by {
                display: block !important;
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
                <span
                    class="info-value">{{ \Carbon\Carbon::parse($penjualan->tanggal_penjualan)->format('d/m/Y H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Kasir</span>
                <span class="info-value">{{ $penjualan->kasir }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status</span>
                <span class="info-value">
                    @if ($penjualan->status_pembayaran === 'lunas')
                        <span style="color:#10b981">LUNAS</span>
                    @elseif($penjualan->status_pembayaran === 'bayar_sebagian')
                        <span style="color:#d97706">BAYAR SEBAGIAN</span>
                    @else
                        <span style="color:#dc2626">BELUM BAYAR</span>
                    @endif
                </span>
            </div>
        </div>

        <div class="separator">================================</div>

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

        <div class="totals">
            <div class="total-row grand-total">
                <span class="total-label">TOTAL</span>
                <span class="total-value">Rp {{ number_format($penjualan->total_pembayaran, 0, ',', '.') }}</span>
            </div>

            {{-- Payment Methods Detail --}}
            @php
                $paymentMethods = json_decode($penjualan->payment_methods ?? '[]', true);
                $methodLabels = ['tunai' => 'Tunai', 'qris' => 'QRIS', 'piutang' => 'Piutang'];
            @endphp

            @if (!empty($paymentMethods))
                <div class="payment-methods">
                    <div class="payment-methods-title">Rincian Pembayaran:</div>
                    @foreach ($paymentMethods as $pm)
                        <div class="payment-method-row {{ $pm['method'] ?? 'tunai' }}">
                            <span class="payment-method-label">
                                @if (($pm['method'] ?? '') === 'tunai')
                                    💵
                                @elseif(($pm['method'] ?? '') === 'qris')
                                    📱
                                @else
                                    ⏰
                                @endif
                                {{ $methodLabels[$pm['method'] ?? 'tunai'] ?? ucfirst($pm['method'] ?? '-') }}
                            </span>
                            <span class="payment-method-amount">Rp
                                {{ number_format($pm['amount'] ?? 0, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                {{-- Fallback for old records without payment_methods --}}
                @if ($penjualan->status_pembayaran === 'lunas')
                    <div class="payment-methods">
                        <div class="payment-methods-title">Rincian Pembayaran:</div>
                        <div class="payment-method-row tunai">
                            <span class="payment-method-label">💵 Tunai</span>
                            <span class="payment-method-amount">Rp
                                {{ number_format($penjualan->total_bayar ?? $penjualan->total_pembayaran, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endif
            @endif

            {{-- Kembalian (only for tunai transactions) --}}
            @if (
                $penjualan->status_pembayaran === 'lunas' &&
                    isset($penjualan->kembalian_pembayaran) &&
                    $penjualan->kembalian_pembayaran > 0)
                <div class="kembalian-row">
                    <span class="kembalian-label">↩ Kembalian Tunai</span>
                    <span class="kembalian-value">Rp
                        {{ number_format($penjualan->kembalian_pembayaran, 0, ',', '.') }}</span>
                </div>
            @endif

            {{-- Status badges --}}
            @if ($penjualan->status_pembayaran === 'belum_bayar')
                <div style="text-align:center; margin-top:10px;">
                    <span class="piutang-badge">⚠ PIUTANG - BELUM DIBAYAR</span>
                </div>
            @elseif($penjualan->status_pembayaran === 'bayar_sebagian')
                <div style="text-align:center; margin-top:10px;">
                    <span class="bayar-sebagian-badge">⚠ BAYAR SEBAGIAN</span>
                    @if (isset($penjualan->sisa_tagihan))
                        <div style="font-size:12px; color:#d97706; margin-top:4px; font-weight:bold;">
                            Sisa: Rp {{ number_format($penjualan->sisa_tagihan, 0, ',', '.') }}
                        </div>
                    @endif
                </div>
            @endif
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
            <div class="footer-note">
                Simpan struk ini sebagai<br>
                bukti pembelian yang sah
            </div>
            <div class="footer-made-by">
                ─────────────────<br>
                Made with ❤ by Toko Sahabat POS
            </div>
        </div>

        <button onclick="window.print()" class="print-button">
            Cetak Struk (A4/Letter)
        </button>

        <button onclick="printThermal()" class="thermal-button" id="thermalBtn">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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

                if (!window.PrinterHelper) {
                    throw new Error('Printer Helper belum dimuat. Refresh halaman dan coba lagi.');
                }

                // Parse payment methods from PHP
                const paymentMethods = @json(json_decode($penjualan->payment_methods ?? '[]', true) ?? []);

                const receiptData = {
                    no_transaksi: '#{{ str_pad($penjualan->id_penjualan, 6, '0', STR_PAD_LEFT) }}',
                    tanggal: '{{ \Carbon\Carbon::parse($penjualan->tanggal_penjualan)->format('d/m/Y H:i') }}',
                    kasir: '{{ $penjualan->kasir }}',
                    total_pembayaran: {{ $penjualan->total_pembayaran }},
                    total_bayar: {{ $penjualan->total_bayar ?? $penjualan->total_pembayaran }},
                    kembalian_pembayaran: {{ $penjualan->kembalian_pembayaran ?? 0 }},
                    sisa_tagihan: {{ $penjualan->sisa_tagihan ?? 0 }},
                    status_pembayaran: '{{ $penjualan->status_pembayaran }}',
                    payment_methods: paymentMethods,
                    items: [
                        @foreach ($detail as $item)
                            {
                                nama_produk: '{{ addslashes($item->nama_produk) }}',
                                qty_produk: {{ $item->qty_produk }},
                                harga_produk: {{ $item->harga_produk }},
                                subtotal_harga: {{ $item->subtotal_harga }}
                            },
                        @endforeach
                    ]
                };

                console.log('📄 Receipt data:', receiptData);

                btnText.textContent = 'Printing...';

                const result = await window.PrinterHelper.printReceipt(receiptData);

                console.log('✅ Print result:', result);

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
                    suggestion =
                        '\n\nSolusi:\n1. Buka menu Settings\n2. Klik "Scan" atau "Reconnect"\n3. Pilih printer RPP02N\n4. Coba print lagi';
                } else if (error.message.includes('GATT') || error.message.includes('disconnected')) {
                    suggestion = '\n\nSolusi:\n1. Buka menu Settings\n2. Klik "Reconnect"\n3. Coba print lagi';
                } else if (error.message.includes('timeout')) {
                    suggestion = '\n\nSolusi:\n1. RESTART PRINTER\n2. Buka Settings → Reconnect\n3. Coba lagi';
                }

                alert('❌ Gagal Mencetak!\n\n' + errorMsg + suggestion);
            }
        }

        window.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
            if (e.ctrlKey && e.key === 't') {
                e.preventDefault();
                printThermal();
            }
        });

        console.log('✅ Struk page loaded');
    </script>
</body>

</html>
