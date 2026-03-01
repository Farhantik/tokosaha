<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi #{{ str_pad($penjualan->id_penjualan, 6, '0', STR_PAD_LEFT) }} - WPOS</title>
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
            padding-bottom: 50px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        #printerStatusBar {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }

        #printerStatusBar.connecting {
            background: #dbeafe;
            color: #1e40af;
        }

        #printerStatusBar.connected {
            background: #d1fae5;
            color: #065f46;
        }

        #printerStatusBar.disconnected {
            background: #fee2e2;
            color: #991b1b;
        }

        #printerStatusBar .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        #printerStatusBar.connecting .dot {
            background: #3b82f6;
            animation: pulse 1s infinite;
        }

        #printerStatusBar.connected .dot {
            background: #10b981;
        }

        #printerStatusBar.disconnected .dot {
            background: #ef4444;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: .3
            }
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

        .item-subtotal {
            font-weight: bold;
            color: #10b981;
        }

        .totals {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px dashed #e5e7eb;
        }

        .grand-total {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 12px;
            border-radius: 8px;
            margin: 15px 0;
            font-size: 16px;
            display: flex;
            justify-content: space-between;
            font-weight: bold;
        }

        .payment-section {
            margin-top: 12px;
        }

        .payment-title {
            font-size: 11px;
            color: #6b7280;
            font-weight: bold;
            margin-bottom: 8px;
            padding-top: 10px;
            border-top: 1px dashed #e5e7eb;
        }

        .payment-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 5px;
            padding: 5px 8px;
            background: #f9fafb;
            border-radius: 5px;
        }

        .payment-row.tunai {
            border-left: 3px solid #10b981;
        }

        .payment-row.qris {
            border-left: 3px solid #4f46e5;
        }

        .payment-row.bayar_nanti {
            border-left: 3px solid #ea580c;
        }

        .payment-label {
            font-weight: 600;
            color: #374151;
        }

        .payment-amount {
            font-weight: bold;
            color: #10b981;
        }

        .payment-row.bayar_nanti .payment-amount {
            color: #ea580c;
        }

        .kembalian-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            margin-top: 8px;
            padding: 8px 10px;
            background: #eff6ff;
            border-radius: 6px;
            border-left: 3px solid #3b82f6;
        }

        .kembalian-label,
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
            padding: 4px 12px;
            border-radius: 999px;
        }

        .bayar-sebagian-badge {
            display: inline-block;
            background: #fef9c3;
            color: #d97706;
            font-size: 11px;
            font-weight: bold;
            padding: 4px 12px;
            border-radius: 999px;
        }

        .status-detail {
            font-size: 12px;
            margin-top: 5px;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            padding-bottom: 20px;
            border-top: 2px dashed #e5e7eb;
            page-break-inside: avoid;
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
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .thermal-button:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-2px);
        }

        .thermal-button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .reconnect-button {
            width: 100%;
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: white;
            padding: 10px;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 8px;
            display: none;
            transition: all 0.3s;
        }

        .reconnect-button:hover {
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
            transform: translateY(-1px);
        }

        .error-box {
            display: none;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 12px;
            margin-top: 10px;
            font-size: 12px;
            color: #991b1b;
            line-height: 1.7;
        }

        .error-box.show {
            display: block;
        }

        .error-box strong {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
        }

        .error-box ol {
            padding-left: 16px;
            margin-top: 4px;
        }

        @media print {

            html,
            body {
                height: auto !important;
                overflow: visible !important;
                background: white;
                padding: 0;
                margin: 0;
            }

            .container {
                max-width: 100%;
                box-shadow: none;
                padding: 10px;
                padding-bottom: 60px;
            }

            .footer {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                margin-top: 15px;
                padding-bottom: 30px;
            }

            .totals {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .items {
                page-break-inside: auto;
            }

            .item {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .print-button,
            .thermal-button,
            .reconnect-button,
            #printerStatusBar,
            .error-box {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <div class="container">

        <div id="printerStatusBar" class="connecting">
            <div class="dot"></div>
            <span id="printerStatusText">Menghubungkan printer...</span>
        </div>

        <div class="header">
            <div class="store-name">Point of Sale</div>
            <div class="store-address">Jl. Wonorejo, Rungkut<br>Surabaya</div>
            <div class="store-phone">Telp: 081234567890</div>
        </div>

        <div class="separator">================================</div>

        {{--
            FIX WAKTU:
            Simpan ke variable PHP sekali saja → dipakai di HTML dan RECEIPT_DATA
            Sehingga waktu yang tampil di struk HTML == waktu yang dicetak thermal
        --}}
        @php
            $tglTrx = \Carbon\Carbon::parse($penjualan->tanggal_penjualan);
            $tglFormat = $tglTrx->format('d/m/Y');
            $wktFormat = $tglTrx->format('H:i');
        @endphp

        <div class="trans-info">
            <div class="info-row">
                <span class="info-label">No. Transaksi</span>
                <span class="info-value">#{{ str_pad($penjualan->id_penjualan, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal</span>
                <span class="info-value">{{ $tglFormat }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Waktu</span>
                <span class="info-value">{{ $wktFormat }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Kasir</span>
                <span class="info-value">{{ $penjualan->kasir }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status</span>
                <span class="info-value">
                    @if ($penjualan->status_pembayaran === 'lunas')
                        <span style="color:#10b981">✓ LUNAS</span>
                    @elseif($penjualan->status_pembayaran === 'bayar_sebagian')
                        <span style="color:#d97706">⚠ BAYAR SEBAGIAN</span>
                    @else
                        <span style="color:#dc2626">✗ BELUM BAYAR</span>
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
                        <span>{{ $item->qty_produk }} x Rp {{ number_format($item->harga_produk, 0, ',', '.') }}</span>
                        <span class="item-subtotal">Rp {{ number_format($item->subtotal_harga, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="separator">================================</div>

        <div class="totals">

            <div class="grand-total">
                <span>TOTAL</span>
                <span>Rp {{ number_format($penjualan->total_pembayaran, 0, ',', '.') }}</span>
            </div>

            @php
                $rawPm = $penjualan->payment_methods ?? null;
                $pm = [];

                if (!empty($rawPm)) {
                    $decoded = is_array($rawPm) ? $rawPm : json_decode($rawPm, true);
                    if (is_array($decoded) && count($decoded) > 0) {
                        $pm = $decoded;
                    }
                }

                $status = $penjualan->status_pembayaran ?? 'lunas';
                $totalTagihan = (float) ($penjualan->total_pembayaran ?? 0);
                $totalBayar = (float) ($penjualan->total_bayar ?? $totalTagihan);
                $kembalian = (float) ($penjualan->kembalian_pembayaran ?? 0);
                $sisa = (float) ($penjualan->sisa_tagihan ?? 0);

                if ($sisa <= 0 && $status === 'bayar_sebagian') {
                    $sisa = max(0, $totalTagihan - $totalBayar);
                }

                if (empty($pm)) {
                    if ($status === 'lunas') {
                        $pm = [['method' => 'tunai', 'amount' => $totalBayar]];
                    } elseif ($status === 'bayar_sebagian') {
                        $pm = [
                            ['method' => 'tunai', 'amount' => $totalBayar],
                            ['method' => 'bayar_nanti', 'amount' => $sisa],
                        ];
                    } else {
                        $pm = [['method' => 'bayar_nanti', 'amount' => $totalTagihan]];
                    }
                }

                $labels = ['tunai' => 'Tunai', 'qris' => 'QRIS', 'bayar_nanti' => 'Bayar Nanti'];
                $icons = ['tunai' => '💵', 'qris' => '📱', 'bayar_nanti' => '⏰'];
                $adaTunai = collect($pm)->contains(fn($p) => ($p['method'] ?? '') === 'tunai');
            @endphp

            <div class="payment-section">
                <div class="payment-title">Rincian Pembayaran:</div>

                @foreach ($pm as $p)
                    @php
                        $method = $p['method'] ?? 'tunai';
                        $amount = (float) ($p['amount'] ?? 0);
                        if ($method === 'bayar_nanti' && $amount <= 0) {
                            $amount = $sisa > 0 ? $sisa : $totalTagihan;
                        }
                    @endphp
                    <div class="payment-row {{ $method }}">
                        <span class="payment-label">
                            {{ $icons[$method] ?? '💰' }} {{ $labels[$method] ?? ucfirst($method) }}
                        </span>
                        <span class="payment-amount">Rp {{ number_format($amount, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>

            @if ($kembalian > 0 && $status === 'lunas' && $adaTunai)
                <div class="kembalian-row">
                    <span class="kembalian-label">↩ Kembalian</span>
                    <span class="kembalian-value">Rp {{ number_format($kembalian, 0, ',', '.') }}</span>
                </div>
            @endif

            @if ($status === 'belum_bayar')
                <div style="text-align:center; margin-top:14px;">
                    <span class="piutang-badge">⚠ PIUTANG — BELUM DIBAYAR</span>
                    <div class="status-detail" style="color:#dc2626;">
                        Tagihan: Rp {{ number_format($totalTagihan, 0, ',', '.') }}
                    </div>
                </div>
            @elseif($status === 'bayar_sebagian')
                <div style="text-align:center; margin-top:14px;">
                    <span class="bayar-sebagian-badge">⚠ BAYAR SEBAGIAN</span>
                    <div class="status-detail" style="color:#d97706;">
                        Dibayar: Rp {{ number_format($totalBayar, 0, ',', '.') }} |
                        Sisa: Rp {{ number_format($sisa, 0, ',', '.') }}
                    </div>
                </div>
            @endif
        </div>

        <div class="separator">================================</div>

        <div class="footer">
            <div class="thank-you">Terima Kasih Atas<br>Kunjungan Anda!</div>
            <div class="footer-note">Barang yang sudah dibeli<br>tidak dapat ditukar/dikembalikan</div>
            <div class="footer-note">Simpan struk ini sebagai<br>bukti pembelian yang sah</div>
            <div class="footer-made-by">─────────────────<br>Made with by WPOS</div>
        </div>

        <button onclick="window.print()" class="print-button">🖨️ Cetak Struk (A4/Letter)</button>

        <button onclick="printThermal()" class="thermal-button" id="thermalBtn">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                <rect x="6" y="14" width="12" height="8"></rect>
            </svg>
            <span id="thermalBtnText">Cetak Thermal (58mm)</span>
        </button>

        <button onclick="manualReconnect()" class="reconnect-button" id="reconnectBtn">🔄 Sambungkan Printer</button>

        <div class="error-box" id="errorBox">
            <strong>❌ <span id="errorTitle">Gagal Mencetak</span></strong>
            <span id="errorMsg"></span>
            <ol id="errorSteps"></ol>
        </div>

    </div>

    <script src="{{ asset('js/printer-helper.js') }}"></script>

    <script>
        const RECEIPT_DATA = {
            no_transaksi: '#{{ str_pad($penjualan->id_penjualan, 6, '0', STR_PAD_LEFT) }}',
            {{-- FIX: pakai $tglFormat & $wktFormat — sama persis dengan yang tampil di HTML --}}
            tanggal: '{{ $tglFormat }}',
            waktu: '{{ $wktFormat }}',
            kasir: '{{ addslashes($penjualan->kasir) }}',
            total_pembayaran: {{ $penjualan->total_pembayaran }},
            total_bayar: {{ $penjualan->total_bayar ?? $penjualan->total_pembayaran }},
            kembalian_pembayaran: {{ $penjualan->kembalian_pembayaran ?? 0 }},
            sisa_tagihan: {{ $penjualan->sisa_tagihan ?? 0 }},
            status_pembayaran: '{{ $penjualan->status_pembayaran }}',
            payment_methods: @json(json_decode($penjualan->payment_methods ?? '[]', true) ?? []),
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

        const statusBar = document.getElementById('printerStatusBar');
        const statusText = document.getElementById('printerStatusText');
        const thermalBtn = document.getElementById('thermalBtn');
        const thermalBtnText = document.getElementById('thermalBtnText');
        const reconnectBtn = document.getElementById('reconnectBtn');
        const errorBox = document.getElementById('errorBox');

        function setStatus(type, msg) {
            statusBar.className = type;
            statusText.textContent = msg;
            reconnectBtn.style.display = type === 'disconnected' ? 'block' : 'none';
        }

        function showError(title, msg, steps = []) {
            document.getElementById('errorTitle').textContent = title;
            document.getElementById('errorMsg').textContent = msg ? '\n' + msg : '';
            document.getElementById('errorSteps').innerHTML = steps.map(s => `<li>${s}</li>`).join('');
            errorBox.classList.add('show');
        }

        function hideError() {
            errorBox.classList.remove('show');
        }

        function resetThermal() {
            thermalBtn.disabled = false;
            thermalBtnText.textContent = 'Cetak Thermal (58mm)';
        }

        if (window.PrinterHelper) {
            window.PrinterHelper.onStatusChange = function(status, msg) {
                switch (status) {
                    case 'scanning':
                    case 'pairing':
                        setStatus('connecting', msg || 'Menyambungkan...');
                        hideError();
                        break;
                    case 'connected':
                        setStatus('connected', '✓ ' + (msg || 'Printer terhubung'));
                        hideError();
                        break;
                    case 'disconnected':
                        setStatus('disconnected', msg || 'Printer tidak terhubung');
                        break;
                    case 'printing':
                        thermalBtn.disabled = true;
                        thermalBtnText.textContent = 'Mencetak...';
                        break;
                    case 'printed':
                        resetThermal();
                        setStatus('connected', '✓ Struk berhasil dicetak!');
                        break;
                    case 'error':
                        setStatus('disconnected', 'Error: ' + msg);
                        break;
                }
            };
        } else {
            setStatus('disconnected', 'Printer Helper gagal dimuat — refresh halaman');
        }

        async function printThermal() {
            hideError();
            thermalBtn.disabled = true;
            thermalBtnText.textContent = 'Menghubungkan...';

            try {
                if (!window.PrinterHelper) throw new Error('Printer Helper tidak tersedia.');

                if (!window.PrinterHelper.isConnected()) {
                    thermalBtnText.textContent = 'Menyambungkan printer...';
                    setStatus('connecting', 'Menyambungkan...');
                    await window.PrinterHelper.reconnect();
                }

                thermalBtnText.textContent = 'Mencetak...';
                await window.PrinterHelper.printReceipt(RECEIPT_DATA);

                thermalBtnText.textContent = '✅ Tercetak!';
                setTimeout(resetThermal, 2500);

            } catch (error) {
                resetThermal();
                const msg = error.message || '';

                if (msg.includes('no longer in range') || msg.includes('GATT')) {
                    setStatus('disconnected', 'Printer di luar jangkauan atau mati');
                    showError('Printer Tidak Terjangkau', 'Printer mati atau terlalu jauh.', [
                        'Pastikan printer <strong>sudah dinyalakan</strong>',
                        'Dekatkan printer ke komputer',
                        'Klik <strong>"Sambungkan Printer"</strong> di bawah',
                    ]);
                } else {
                    setStatus('disconnected', 'Gagal mencetak');
                    showError('Gagal Mencetak', msg, [
                        'Pastikan printer sudah ON',
                        'Klik <strong>"Sambungkan Printer"</strong> di bawah',
                        'Atau buka <strong>Pengaturan → Reconnect</strong>',
                    ]);
                }
            }
        }

        async function manualReconnect() {
            hideError();
            reconnectBtn.textContent = '⏳ Menyambungkan...';
            reconnectBtn.disabled = true;
            setStatus('connecting', 'Menyambungkan printer...');

            try {
                const result = await window.PrinterHelper.reconnect();
                setStatus('connected', '✓ ' + result.name + ' terhubung!');
                reconnectBtn.style.display = 'none';
            } catch (err) {
                setStatus('disconnected', 'Gagal menyambung — printer mati?');
                showError('Gagal Reconnect', err.message, [
                    'Pastikan printer <strong>sudah dinyalakan</strong>',
                    'Pastikan Bluetooth komputer aktif',
                ]);
            } finally {
                reconnectBtn.textContent = '🔄 Sambungkan Printer';
                reconnectBtn.disabled = false;
            }
        }

        window.addEventListener('keydown', e => {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
            if (e.ctrlKey && e.key === 't') {
                e.preventDefault();
                printThermal();
            }
        });
    </script>
</body>

</html>
