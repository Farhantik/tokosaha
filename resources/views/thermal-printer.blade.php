<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Cetak Struk - TOKO SAHABAT</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            padding: 15px;
            min-height: 100vh;
        }
        .container { max-width: 450px; margin: 0 auto; }
        .page-header {
            background: white;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin-bottom: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .page-header h1 { 
            font-size: 18px; 
            color: #333; 
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .page-header p { font-size: 12px; color: #666; }
        .control-panel {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .connection-tabs { 
            display: flex; 
            gap: 10px; 
            margin-bottom: 20px; 
        }
        .tab {
            flex: 1;
            padding: 12px;
            background: #f0f0f0;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            color: #666;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .tab.active {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
        }
        .connection-panel { animation: fadeIn 0.3s; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .info {
            font-size: 13px;
            color: #666;
            margin-bottom: 15px;
            text-align: center;
            padding: 10px;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border-radius: 8px;
            border: 1px solid #bbf7d0;
        }
        .port-selector { margin-bottom: 15px; }
        .port-selector label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        .port-selector select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            background: white;
            cursor: pointer;
            transition: all 0.3s;
        }
        .port-selector select:focus { 
            outline: none; 
            border-color: #10b981; 
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }
        .status {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 13px;
            font-weight: 500;
        }
        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #ccc;
            animation: pulse 2s infinite;
            flex-shrink: 0;
        }
        .status-indicator.connected { 
            background: #10b981;
            box-shadow: 0 0 8px rgba(16, 185, 129, 0.5);
        }
        .status-indicator.connecting { 
            background: #f59e0b;
            box-shadow: 0 0 8px rgba(245, 158, 11, 0.5);
        }
        .status-indicator.error { 
            background: #ef4444;
            box-shadow: 0 0 8px rgba(239, 68, 68, 0.5);
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.7; transform: scale(1.1); }
        }
        .button-group { 
            display: flex; 
            flex-direction: column; 
            gap: 10px; 
        }
        button {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        button:active { transform: scale(0.98); }
        button:disabled { 
            opacity: 0.5; 
            cursor: not-allowed; 
            transform: none !important; 
        }
        .btn-connect {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }
        .btn-connect:hover:not(:disabled) { 
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
            transform: translateY(-1px);
        }
        .btn-test { 
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white; 
        }
        .btn-test:hover:not(:disabled) {
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
            transform: translateY(-1px);
        }
        .btn-print { 
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white; 
        }
        .btn-print:hover:not(:disabled) {
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
            transform: translateY(-1px);
        }
        .btn-close { 
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            color: white; 
        }
        .btn-close:hover {
            box-shadow: 0 4px 12px rgba(107, 114, 128, 0.4);
            transform: translateY(-1px);
        }
        .receipt {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            line-height: 1.4;
            max-width: 58mm;
            margin: 0 auto;
        }
        .header { text-align: center; margin-bottom: 2mm; }
        .store-name { 
            font-size: 16px; 
            font-weight: bold; 
            margin-bottom: 1mm;
            color: #10b981;
        }
        .store-address { 
            font-size: 10px; 
            line-height: 1.3; 
            margin-bottom: 0.5mm;
            color: #4b5563;
        }
        .store-phone { 
            font-size: 10px; 
            margin-bottom: 2mm;
            color: #4b5563;
        }
        .separator { 
            text-align: center; 
            margin: 1.5mm 0; 
            font-size: 8px;
            color: #9ca3af;
        }
        .trans-info { 
            font-size: 10px; 
            margin: 2mm 0;
        }
        .info-row { 
            display: flex; 
            justify-content: space-between; 
            margin-bottom: 1mm;
        }
        .info-label { 
            flex: 0 0 40%; 
            text-align: left;
            color: #6b7280;
        }
        .info-value { 
            flex: 0 0 60%; 
            text-align: right; 
            font-weight: bold;
            color: #1f2937;
        }
        .items { margin: 2mm 0; }
        .item { 
            margin-bottom: 2mm; 
            font-size: 10px; 
        }
        .item-name { 
            font-weight: bold; 
            margin-bottom: 0.5mm;
            color: #1f2937;
        }
        .item-details { 
            display: flex; 
            justify-content: space-between; 
            font-size: 9px;
        }
        .item-qty-price { 
            flex: 0 0 50%; 
            text-align: left; 
            padding-left: 2mm;
            color: #6b7280;
        }
        .item-subtotal { 
            flex: 0 0 50%; 
            text-align: right; 
            font-weight: bold;
            color: #10b981;
        }
        .totals { 
            margin-top: 2mm; 
            font-size: 11px; 
        }
        .total-row { 
            display: flex; 
            justify-content: space-between; 
            margin-bottom: 1mm;
        }
        .total-label { 
            flex: 0 0 35%; 
            text-align: left; 
            font-weight: bold;
            color: #4b5563;
        }
        .total-value { 
            flex: 0 0 65%; 
            text-align: right; 
            font-weight: bold;
            color: #1f2937;
        }
        .grand-total {
            font-size: 14px;
            font-weight: bold;
            margin: 2mm 0;
            padding: 2mm 0;
            border-top: 2px solid #10b981;
            border-bottom: 2px solid #10b981;
        }
        .grand-total .total-value {
            color: #10b981;
        }
        .footer { 
            text-align: center; 
            margin-top: 3mm; 
            font-size: 10px; 
            padding-bottom: 3mm; 
        }
        .thank-you { 
            font-weight: bold; 
            margin-bottom: 2mm; 
            line-height: 1.4;
            color: #10b981;
        }
        .footer-note { 
            font-size: 9px; 
            line-height: 1.3; 
            font-style: italic;
            color: #6b7280;
        }
        .page-footer { 
            text-align: center; 
            color: white; 
            font-size: 12px; 
            margin-top: 20px; 
            opacity: 0.9; 
        }
        .icon {
            display: inline-block;
            width: 1em;
            text-align: center;
        }
        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 13px;
        }
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        @media print {
            @page { size: 58mm auto; margin: 0; }
            body { background: white; padding: 0; }
            .control-panel, .page-header, .page-footer { display: none !important; }
            .receipt { 
                box-shadow: none; 
                border-radius: 0; 
                width: 58mm; 
                padding: 2mm; 
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1><span class="icon">🖨️</span> Printer Thermal TOKO SAHABAT</h1>
            <p>ALGOO AT-58XA Support</p>
        </div>

        <div class="control-panel">
            <div id="errorAlert" class="alert alert-error" style="display:none;"></div>

            <div class="connection-tabs">
                <button class="tab active" onclick="switchTab('bluetooth')">
                    <span class="icon">📱</span> Bluetooth
                </button>
                <button class="tab" onclick="switchTab('serial')">
                    <span class="icon">💻</span> COM Port
                </button>
            </div>

            <div id="bluetoothPanel" class="connection-panel">
                <div class="info"><span class="icon">📱</span> Untuk HP/Tablet via Bluetooth</div>
                <div class="status" id="btStatus">
                    <span class="status-indicator"></span>
                    <span>Belum Terhubung</span>
                </div>
                <div class="button-group">
                    <button onclick="connectBluetooth()" id="btConnectBtn" class="btn-connect">
                        <span class="icon">🔌</span> Scan & Connect Bluetooth
                    </button>
                    <button onclick="testPrint()" id="btTestBtn" class="btn-test" disabled>
                        <span class="icon">🧪</span> Test Print
                    </button>
                    <button onclick="printReceipt()" id="btPrintBtn" class="btn-print" disabled>
                        <span class="icon">🖨️</span> Cetak Struk
                    </button>
                </div>
            </div>

            <div id="serialPanel" class="connection-panel" style="display:none;">
                <div class="info"><span class="icon">💻</span> Untuk PC/Laptop via USB</div>
                <div class="port-selector">
                    <label for="comPort">Pilih COM Port:</label>
                    <select id="comPort">
                        <option value="">-- Pilih Port --</option>
                        <option value="COM1">COM1</option>
                        <option value="COM2">COM2</option>
                        <option value="COM3">COM3</option>
                        <option value="COM4" selected>COM4 (Recommended)</option>
                        <option value="COM5">COM5</option>
                        <option value="COM6">COM6</option>
                        <option value="COM7">COM7</option>
                        <option value="COM8">COM8</option>
                    </select>
                </div>
                <div class="status" id="serialStatus">
                    <span class="status-indicator"></span>
                    <span>Belum Terhubung</span>
                </div>
                <div class="button-group">
                    <button onclick="connectSerial()" id="serialConnectBtn" class="btn-connect">
                        <span class="icon">🔌</span> Connect ke Printer
                    </button>
                    <button onclick="testPrint()" id="serialTestBtn" class="btn-test" disabled>
                        <span class="icon">🧪</span> Test Print
                    </button>
                    <button onclick="printReceipt()" id="serialPrintBtn" class="btn-print" disabled>
                        <span class="icon">🖨️</span> Cetak Struk
                    </button>
                </div>
            </div>

            <div class="button-group" style="margin-top: 15px;">
                <button class="btn-close" onclick="window.close()">
                    <span class="icon">✖️</span> Tutup
                </button>
            </div>
        </div>

        <div class="receipt" id="receiptPreview">
            <div class="header">
                <div class="store-name">Loading...</div>
            </div>
        </div>

        <div class="page-footer">
            <p>Powered by Web Bluetooth & Serial API</p>
            <p>Compatible with ALGOO AT-58XA</p>
        </div>
    </div>

    <script>
        let serialPort = null, serialWriter = null, btDevice = null, btCharacteristic = null, currentMode = 'bluetooth';
        let receiptData = null;
        
        const ESC = 0x1B, GS = 0x1D;
        const createCommand = (...bytes) => new Uint8Array(bytes);
        const textToBytes = (text) => new TextEncoder().encode(text);

        // Load data struk dari URL parameter
        function loadReceiptData() {
            const urlParams = new URLSearchParams(window.location.search);
            const dataParam = urlParams.get('data');
            
            if (dataParam) {
                try {
                    receiptData = JSON.parse(decodeURIComponent(dataParam));
                    console.log('Data loaded:', receiptData);
                } catch (e) {
                    console.error('Error parsing receipt data:', e);
                    showError('Gagal memuat data transaksi: ' + e.message);
                    receiptData = null;
                }
            } else {
                showError('Data transaksi tidak ditemukan. Pastikan Anda membuka halaman ini dari Riwayat Transaksi.');
            }
            
            renderReceipt();
        }

        function showError(message) {
            const errorAlert = document.getElementById('errorAlert');
            errorAlert.textContent = '⚠️ ' + message;
            errorAlert.style.display = 'block';
        }

        function formatRupiah(amount) {
            return 'Rp ' + Math.round(parseFloat(amount)).toLocaleString('id-ID');
        }

        function renderReceipt() {
            const receipt = document.getElementById('receiptPreview');
            
            if (!receiptData) {
                receipt.innerHTML = `
                    <div class="header">
                        <div class="store-name">TOKO SAHABAT</div>
                        <div class="store-address">Jl. Wonorejo, Rungkut<br>Surabaya</div>
                        <div class="store-phone">Telp: 081234567890</div>
                    </div>
                    <div class="separator">================================</div>
                    <div style="text-align:center; padding: 20px; color: #ef4444;">
                        <p>❌ Data tidak ditemukan</p>
                        <p style="font-size:9px; margin-top:5px;">Buka dari Riwayat Transaksi</p>
                    </div>
                `;
                return;
            }

            let itemsHTML = '';
            if (receiptData.items && receiptData.items.length > 0) {
                receiptData.items.forEach(item => {
                    itemsHTML += `
                        <div class="item">
                            <div class="item-name">${item.nama_produk}</div>
                            <div class="item-details">
                                <span class="item-qty-price">${item.qty_produk} x ${formatRupiah(item.harga_produk)}</span>
                                <span class="item-subtotal">${formatRupiah(item.subtotal_harga)}</span>
                            </div>
                        </div>
                    `;
                });
            }

            const noTransaksi = receiptData.no_transaksi || '#' + String(receiptData.id_penjualan).padStart(6, '0');

            receipt.innerHTML = `
                <div class="header">
                    <div class="store-name">TOKO SAHABAT</div>
                    <div class="store-address">Jl. Wonorejo, Rungkut<br>Surabaya</div>
                    <div class="store-phone">Telp: 081234567890</div>
                </div>
                <div class="separator">================================</div>
                <div class="trans-info">
                    <div class="info-row"><span class="info-label">No. Transaksi</span><span class="info-value">${noTransaksi}</span></div>
                    <div class="info-row"><span class="info-label">Tanggal</span><span class="info-value">${receiptData.tanggal_penjualan}</span></div>
                    <div class="info-row"><span class="info-label">Kasir</span><span class="info-value">${receiptData.kasir}</span></div>
                </div>
                <div class="separator">================================</div>
                <div class="items">
                    ${itemsHTML}
                </div>
                <div class="separator">================================</div>
                <div class="totals">
                    <div class="total-row grand-total">
                        <span class="total-label">TOTAL</span>
                        <span class="total-value">${formatRupiah(receiptData.total_bayar)}</span>
                    </div>
                    <div class="total-row">
                        <span class="total-label">Tunai</span>
                        <span class="total-value">${formatRupiah(receiptData.total_pembayaran)}</span>
                    </div>
                    <div class="total-row">
                        <span class="total-label">Kembalian</span>
                        <span class="total-value">${formatRupiah(receiptData.kembalian_pembayaran)}</span>
                    </div>
                </div>
                <div class="separator">================================</div>
                <div class="footer">
                    <div class="thank-you">Terima Kasih Atas<br>Kunjungan Anda!</div>
                    <div class="footer-note">Barang yang sudah dibeli<br>tidak dapat ditukar/dikembalikan</div>
                </div>
            `;
        }

        function switchTab(mode) {
            currentMode = mode;
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            event.target.closest('.tab').classList.add('active');
            document.getElementById('bluetoothPanel').style.display = mode === 'bluetooth' ? 'block' : 'none';
            document.getElementById('serialPanel').style.display = mode === 'serial' ? 'block' : 'none';
        }

        async function connectBluetooth() {
            try {
                if (btDevice && btDevice.gatt.connected) { 
                    disconnectBluetooth(); 
                    return; 
                }
                
                if (!navigator.bluetooth) {
                    alert('❌ Browser tidak support Web Bluetooth!\n\n✅ Gunakan Chrome di Android/iOS.');
                    return;
                }
                
                updateStatus('btStatus', 'Scanning printer...', 'connecting');
                
                btDevice = await navigator.bluetooth.requestDevice({
                    filters: [
                        { services: ['000018f0-0000-1000-8000-00805f9b34fb'] },
                        { namePrefix: 'ALGOO' }, 
                        { namePrefix: 'AT-58' }, 
                        { namePrefix: 'BlueTooth Printer' }, 
                        { namePrefix: 'Printer' }
                    ],
                    optionalServices: [
                        '000018f0-0000-1000-8000-00805f9b34fb', 
                        '0000fee0-0000-1000-8000-00805f9b34fb', 
                        '49535343-fe7d-4ae5-8fa9-9fafd205e455'
                    ]
                });
                
                updateStatus('btStatus', 'Menghubungkan ke ' + btDevice.name + '...', 'connecting');
                const server = await btDevice.gatt.connect();
                
                let service;
                try {
                    service = await server.getPrimaryService('000018f0-0000-1000-8000-00805f9b34fb');
                    btCharacteristic = await service.getCharacteristic('00002af1-0000-1000-8000-00805f9b34fb');
                } catch (e) {
                    try {
                        service = await server.getPrimaryService('49535343-fe7d-4ae5-8fa9-9fafd205e455');
                        btCharacteristic = await service.getCharacteristic('49535343-8841-43f4-a8d4-ecbe34729bb3');
                    } catch (e2) {
                        service = await server.getPrimaryService('0000fee0-0000-1000-8000-00805f9b34fb');
                        btCharacteristic = await service.getCharacteristic('0000fee1-0000-1000-8000-00805f9b34fb');
                    }
                }
                
                updateStatus('btStatus', '✅ Terhubung ke ' + btDevice.name, 'connected');
                document.getElementById('btTestBtn').disabled = false;
                document.getElementById('btPrintBtn').disabled = false;
                document.getElementById('btConnectBtn').innerHTML = '<span class="icon">🔌</span> Disconnect';
                
                btDevice.addEventListener('gattserverdisconnected', onBtDisconnected);
            } catch (error) {
                console.error('Bluetooth error:', error);
                updateStatus('btStatus', '❌ Gagal terhubung', 'error');
                
                let errorMsg = '❌ Gagal terhubung!\n\n';
                errorMsg += '📋 Pastikan:\n';
                errorMsg += '1️⃣ Bluetooth HP aktif\n';
                errorMsg += '2️⃣ Printer menyala\n';
                errorMsg += '3️⃣ Printer tidak terhubung ke device lain\n\n';
                errorMsg += '⚠️ Error: ' + error.message;
                
                alert(errorMsg);
            }
        }

        function onBtDisconnected() {
            updateStatus('btStatus', '⚠️ Terputus dari printer', 'error');
            document.getElementById('btTestBtn').disabled = true;
            document.getElementById('btPrintBtn').disabled = true;
            document.getElementById('btConnectBtn').innerHTML = '<span class="icon">🔌</span> Scan & Connect Bluetooth';
        }

        function disconnectBluetooth() {
            if (btDevice && btDevice.gatt.connected) btDevice.gatt.disconnect();
            btDevice = null; 
            btCharacteristic = null;
            updateStatus('btStatus', 'Belum Terhubung', 'disconnected');
            document.getElementById('btTestBtn').disabled = true;
            document.getElementById('btPrintBtn').disabled = true;
            document.getElementById('btConnectBtn').innerHTML = '<span class="icon">🔌</span> Scan & Connect Bluetooth';
        }

        async function connectSerial() {
            try {
                if (serialPort && serialPort.readable) { 
                    await disconnectSerial(); 
                    return; 
                }
                
                const comPort = document.getElementById('comPort').value;
                if (!comPort) { 
                    alert('⚠️ Pilih COM Port terlebih dahulu!'); 
                    return; 
                }
                
                if (!('serial' in navigator)) { 
                    alert('❌ Browser tidak support Web Serial API!\n\n✅ Gunakan Chrome/Edge di PC/Laptop.'); 
                    return; 
                }
                
                updateStatus('serialStatus', 'Menghubungkan...', 'connecting');
                
                serialPort = await navigator.serial.requestPort();
                await serialPort.open({ 
                    baudRate: 9600, 
                    dataBits: 8, 
                    stopBits: 1, 
                    parity: 'none', 
                    flowControl: 'none' 
                });
                
                serialWriter = serialPort.writable.getWriter();
                
                updateStatus('serialStatus', '✅ Terhubung ke ' + comPort, 'connected');
                document.getElementById('serialTestBtn').disabled = false;
                document.getElementById('serialPrintBtn').disabled = false;
                document.getElementById('serialConnectBtn').innerHTML = '<span class="icon">🔌</span> Disconnect';
            } catch (error) {
                console.error('Serial error:', error);
                updateStatus('serialStatus', '❌ Gagal terhubung', 'error');
                
                let errorMsg = '❌ Gagal terhubung!\n\n';
                errorMsg += '📋 Pastikan:\n';
                errorMsg += '1️⃣ Driver terinstall\n';
                errorMsg += '2️⃣ Printer terhubung USB\n';
                errorMsg += '3️⃣ Port COM benar\n\n';
                errorMsg += '⚠️ Error: ' + error.message;
                
                alert(errorMsg);
            }
        }

        async function disconnectSerial() {
            if (serialWriter) { 
                serialWriter.releaseLock(); 
                serialWriter = null; 
            }
            if (serialPort) { 
                await serialPort.close(); 
                serialPort = null; 
            }
            updateStatus('serialStatus', 'Belum Terhubung', 'disconnected');
            document.getElementById('serialTestBtn').disabled = true;
            document.getElementById('serialPrintBtn').disabled = true;
            document.getElementById('serialConnectBtn').innerHTML = '<span class="icon">🔌</span> Connect ke Printer';
        }

        function updateStatus(statusId, message, state) {
            const status = document.getElementById(statusId);
            const indicator = status.querySelector('.status-indicator');
            const text = status.querySelector('span:last-child');
            
            text.textContent = message;
            indicator.className = 'status-indicator';
            if (state) indicator.classList.add(state);
        }

        async function testPrint() {
            try {
                const commands = [
                    createCommand(ESC, 0x40),
                    createCommand(ESC, 0x61, 0x01),
                    textToBytes('TEST PRINT\n'),
                    textToBytes('ALGOO AT-58XA\n'),
                    createCommand(ESC, 0x61, 0x00),
                    textToBytes('Printer berhasil terhubung!\n\n'),
                    textToBytes('================================\n\n\n'),
                    createCommand(GS, 0x56, 0x00)
                ];
                
                await sendToPrinter(commands);
                alert('✅ Test print berhasil!\nCek printer Anda.');
            } catch (error) {
                console.error('Print error:', error);
                alert('❌ Gagal print!\n\n' + error.message);
            }
        }

        async function printReceipt() {
            if (!receiptData) {
                alert('❌ Data struk tidak ditemukan!\nPastikan Anda membuka halaman ini dari Riwayat Transaksi.');
                return;
            }

            try {
                const noTransaksi = receiptData.no_transaksi || '#' + String(receiptData.id_penjualan).padStart(6, '0');
                
                const commands = [
                    createCommand(ESC, 0x40),
                    createCommand(ESC, 0x61, 0x01),
                    createCommand(ESC, 0x21, 0x30),
                    textToBytes('TOKO SAHABAT\n'),
                    createCommand(ESC, 0x21, 0x00),
                    textToBytes('Jl. Wonorejo, Rungkut\n'),
                    textToBytes('Surabaya\n'),
                    textToBytes('Telp: 081234567890\n'),
                    createCommand(ESC, 0x61, 0x00),
                    textToBytes('================================\n'),
                    textToBytes('No. Trans : ' + noTransaksi + '\n'),
                    textToBytes('Tanggal   : ' + receiptData.tanggal_penjualan + '\n'),
                    textToBytes('Kasir     : ' + receiptData.kasir + '\n'),
                    textToBytes('================================\n')
                ];

                if (receiptData.items && receiptData.items.length > 0) {
                    receiptData.items.forEach(item => {
                        commands.push(textToBytes(item.nama_produk + '\n'));
                        
                        const qtyPrice = item.qty_produk + ' x ' + formatRupiah(item.harga_produk);
                        const subtotal = formatRupiah(item.subtotal_harga);
                        const spaces = ' '.repeat(Math.max(1, 32 - qtyPrice.length - subtotal.length));
                        
                        commands.push(textToBytes('  ' + qtyPrice + spaces + subtotal + '\n'));
                    });
                }

                commands.push(textToBytes('================================\n'));
                
                const totalLabel = 'TOTAL';
                const totalValue = formatRupiah(receiptData.total_bayar);
                const totalSpaces = ' '.repeat(Math.max(1, 32 - totalLabel.length - totalValue.length));
                commands.push(
                    createCommand(ESC, 0x21, 0x10),
                    textToBytes(totalLabel + totalSpaces + totalValue + '\n'),
                    createCommand(ESC, 0x21, 0x00)
                );

                const tunaiLabel = 'Tunai';
                const tunaiValue = formatRupiah(receiptData.total_pembayaran);
                const tunaiSpaces = ' '.repeat(Math.max(1, 32 - tunaiLabel.length - tunaiValue.length));
                commands.push(textToBytes(tunaiLabel + tunaiSpaces + tunaiValue + '\n'));

                const kembaliLabel = 'Kembalian';
                const kembaliValue = formatRupiah(receiptData.kembalian_pembayaran);
                const kembaliSpaces = ' '.repeat(Math.max(1, 32 - kembaliLabel.length - kembaliValue.length));
                commands.push(textToBytes(kembaliLabel + kembaliSpaces + kembaliValue + '\n'));

                commands.push(
                    textToBytes('================================\n'),
                    createCommand(ESC, 0x61, 0x01),
                    textToBytes('Terima Kasih Atas\n'),
                    textToBytes('Kunjungan Anda!\n\n'),
                    textToBytes('Barang yang sudah dibeli\n'),
                    textToBytes('tidak dapat ditukar/dikembalikan\n'),
                    createCommand(ESC, 0x61, 0x00),
                    textToBytes('\n\n\n'),
                    createCommand(GS, 0x56, 0x00)
                );

                await sendToPrinter(commands);
                alert('✅ Struk berhasil dicetak!');
            } catch (error) {
                console.error('Print error:', error);
                alert('❌ Gagal mencetak struk!\n\n' + error.message);
            }
        }

        async function sendToPrinter(commands) {
            if (currentMode === 'bluetooth') {
                if (!btCharacteristic) throw new Error('Printer tidak terhubung');
                for (const cmd of commands) {
                    await btCharacteristic.writeValue(cmd);
                    await new Promise(resolve => setTimeout(resolve, 50));
                }
            } else {
                if (!serialWriter) throw new Error('Printer tidak terhubung');
                for (const cmd of commands) {
                    await serialWriter.write(cmd);
                    await new Promise(resolve => setTimeout(resolve, 50));
                }
            }
        }

        window.addEventListener('DOMContentLoaded', loadReceiptData);
    </script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const savedPrinter = localStorage.getItem("thermal_printer_status");
    if (savedPrinter) {
        try {
            const status = JSON.parse(savedPrinter);
            if (status.connected && status.printerName) {
                console.log("🔌 Auto-loading saved printer:", status.printerName);
                
                const btStatus = document.getElementById("btStatus");
                if (btStatus) {
                    btStatus.innerHTML = "<span class=\"status-indicator connected\"></span><span>Terhubung ke " + status.printerName + "</span>";
                }
                
                const btTestBtn = document.getElementById("btTestBtn");
                const btPrintBtn = document.getElementById("btPrintBtn");
                if (btTestBtn) btTestBtn.disabled = false;
                if (btPrintBtn) btPrintBtn.disabled = false;
                
                console.log("✅ Printer ready");
            }
        } catch (e) {
            console.error("Error loading printer:", e);
        }
    }
});
</script>

<!-- Auto-Print Script -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    console.log("📄 Thermal printer page loaded");
    
    const autoPrintTrigger = localStorage.getItem("trigger_auto_print");
    const printerStatus = localStorage.getItem("thermal_printer_status");
    
    if (autoPrintTrigger === "true" && printerStatus) {
        console.log("🖨️ Auto-print trigger detected!");
        localStorage.removeItem("trigger_auto_print");
        
        const printer = JSON.parse(printerStatus);
        console.log("📱 Printer:", printer.printerName);
        
        setTimeout(function() {
            const btnPrint = document.getElementById("btPrintBtn") || document.querySelector("[onclick*=printReceipt]");
            
            if (btnPrint && !btnPrint.disabled) {
                console.log("✅ Auto-clicking print button...");
                btnPrint.click();
            } else {
                console.log("⚠️ Print button not ready, checking test button...");
                const btnTest = document.getElementById("btTestBtn");
                if (btnTest && !btnTest.disabled) {
                    console.log("🔄 Auto-clicking test print...");
                    btnTest.click();
                } else {
                    console.log("❌ No buttons available, showing alert...");
                    alert("⚠️ Printer belum siap. Silakan klik tombol Cetak Struk manual.");
                }
            }
        }, 1500);
    } else {
        console.log("ℹ️ No auto-print trigger");
    }
});
</script>
</body>
</html>
