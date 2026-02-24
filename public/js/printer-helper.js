/**
 * ============================================
 * PRINTER HELPER - Bluetooth Thermal Printer
 * ============================================
 * Mendukung printer: RPP02N, ALGOO, dan printer
 * thermal Bluetooth lainnya yang kompatibel ESC/POS
 *
 * Letakkan file ini di: public/js/printer-helper.js
 * ============================================
 */

(function (window) {
    'use strict';

    // ============================================
    // STATE
    // ============================================
    let _device = null;   // BluetoothDevice
    let _server = null;   // BluetoothRemoteGATTServer
    let _characteristic = null; // BluetoothRemoteGATTCharacteristic
    let _isConnected = false;
    let _printerName = '';

    // UUID umum printer thermal Bluetooth (ESC/POS)
    const SERVICE_UUIDS = [
        '000018f0-0000-1000-8000-00805f9b34fb', // RPP02N / ALGOO
        '00001101-0000-1000-8000-00805f9b34fb', // Serial Port Profile
        'e7810a71-73ae-499d-8c15-faa9aef0c3f2', // Xprinter
        '49535343-fe7d-4ae5-8fa9-9fafd205e455', // Generic BLE Serial
    ];

    const CHAR_UUIDS = [
        '00002af1-0000-1000-8000-00805f9b34fb',
        '000018f1-0000-1000-8000-00805f9b34fb',
        '49535343-8841-43f4-a8d4-ecbe34729bb3',
        '49535343-1e4d-4bd9-ba61-23c647249616',
    ];

    // ESC/POS Commands
    const ESC = 0x1B;
    const GS = 0x1D;
    const INIT = [ESC, 0x40];                          // Initialize printer
    const ALIGN_CENTER = [ESC, 0x61, 0x01];            // Center align
    const ALIGN_LEFT = [ESC, 0x61, 0x00];            // Left align
    const BOLD_ON = [ESC, 0x45, 0x01];            // Bold on
    const BOLD_OFF = [ESC, 0x45, 0x00];            // Bold off
    const FONT_NORMAL = [ESC, 0x21, 0x00];            // Normal font
    const FONT_DOUBLE = [ESC, 0x21, 0x30];            // Double height+width
    const CUT_PAPER = [GS, 0x56, 0x42, 0x00];       // Cut paper
    const LINE_FEED = [0x0A];                        // New line

    // ============================================
    // INTERNAL: SEND DATA KE PRINTER
    // ============================================
    async function _send(data) {
        if (!_characteristic) {
            throw new Error('Printer belum di-pair. Klik "Scan" atau "Reconnect" terlebih dahulu.');
        }

        // Kirim dalam chunk 512 byte agar tidak overflow BLE buffer
        const CHUNK_SIZE = 512;
        const bytes = data instanceof Uint8Array ? data : new Uint8Array(data);

        for (let i = 0; i < bytes.length; i += CHUNK_SIZE) {
            const chunk = bytes.slice(i, i + CHUNK_SIZE);
            await _characteristic.writeValue(chunk);
            // Delay kecil antar chunk agar printer tidak kewalahan
            await _delay(20);
        }
    }

    // ============================================
    // INTERNAL: ENCODE TEXT KE BYTES
    // ============================================
    function _encodeText(text) {
        // Gunakan TextEncoder untuk UTF-8, fallback manual
        try {
            return new TextEncoder().encode(text);
        } catch (e) {
            const bytes = [];
            for (let i = 0; i < text.length; i++) {
                bytes.push(text.charCodeAt(i) & 0xFF);
            }
            return new Uint8Array(bytes);
        }
    }

    // ============================================
    // INTERNAL: GABUNGKAN MULTIPLE BYTE ARRAYS
    // ============================================
    function _mergeBytes(...arrays) {
        const flat = [];
        for (const arr of arrays) {
            if (Array.isArray(arr)) {
                flat.push(...arr);
            } else if (arr instanceof Uint8Array) {
                flat.push(...arr);
            } else if (typeof arr === 'string') {
                flat.push(..._encodeText(arr));
            }
        }
        return new Uint8Array(flat);
    }

    // ============================================
    // INTERNAL: DELAY HELPER
    // ============================================
    function _delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    // ============================================
    // INTERNAL: FORMAT GARIS SEPARATOR
    // ============================================
    function _line(char = '-', width = 32) {
        return char.repeat(width) + '\n';
    }

    // ============================================
    // INTERNAL: FORMAT TEKS KIRI-KANAN (2 kolom)
    // ============================================
    function _columns(left, right, width = 32) {
        const spaces = width - left.length - right.length;
        return left + ' '.repeat(Math.max(1, spaces)) + right + '\n';
    }

    // ============================================
    // INTERNAL: TRIGGER STATUS CALLBACK
    // ============================================
    function _triggerStatus(status, message = '') {
        if (typeof window.PrinterHelper.onStatusChange === 'function') {
            window.PrinterHelper.onStatusChange(status, message);
        }
        console.log(`[PrinterHelper] Status: ${status}`, message || '');
    }

    // ============================================
    // INTERNAL: CARI CHARACTERISTIC YANG BISA WRITE
    // ============================================
    async function _findWritableCharacteristic(service) {
        try {
            const characteristics = await service.getCharacteristics();
            for (const char of characteristics) {
                if (char.properties.write || char.properties.writeWithoutResponse) {
                    console.log('[PrinterHelper] Found characteristic:', char.uuid);
                    return char;
                }
            }
        } catch (e) {
            // Fallback: coba UUID yang diketahui
            for (const uuid of CHAR_UUIDS) {
                try {
                    const char = await service.getCharacteristic(uuid);
                    if (char) return char;
                } catch (_) { /* skip */ }
            }
        }
        return null;
    }

    // ============================================
    // PUBLIC: SCAN & PAIR PRINTER BLUETOOTH
    // ============================================
    async function scanAndPair() {
        if (!navigator.bluetooth) {
            throw new Error(
                'Web Bluetooth tidak didukung di browser ini.\n' +
                'Gunakan Google Chrome atau Microsoft Edge versi terbaru.'
            );
        }

        _triggerStatus('scanning');

        try {
            // Request device dengan filter service UUID
            _device = await navigator.bluetooth.requestDevice({
                acceptAllDevices: true,
                optionalServices: SERVICE_UUIDS
            });

            _printerName = _device.name || 'Unknown Printer';
            _triggerStatus('pairing', _printerName);

            console.log('[PrinterHelper] Device selected:', _printerName);

            // Connect ke GATT Server
            _server = await _device.gatt.connect();
            console.log('[PrinterHelper] GATT connected');

            // Cari service yang bisa dipakai
            let service = null;
            for (const uuid of SERVICE_UUIDS) {
                try {
                    service = await _server.getPrimaryService(uuid);
                    console.log('[PrinterHelper] Service found:', uuid);
                    break;
                } catch (_) { /* try next */ }
            }

            // Fallback: ambil service pertama yang tersedia
            if (!service) {
                try {
                    const services = await _server.getPrimaryServices();
                    if (services.length > 0) {
                        service = services[0];
                        console.log('[PrinterHelper] Using first available service:', service.uuid);
                    }
                } catch (e) {
                    throw new Error('Tidak dapat menemukan service printer. Pastikan printer kompatibel ESC/POS.');
                }
            }

            if (!service) {
                throw new Error('Tidak ada service yang ditemukan pada printer ini.');
            }

            // Cari characteristic untuk write
            _characteristic = await _findWritableCharacteristic(service);

            if (!_characteristic) {
                throw new Error('Tidak dapat menemukan write characteristic. Printer mungkin tidak kompatibel.');
            }

            _isConnected = true;

            // Simpan info ke localStorage
            localStorage.setItem('thermal_printer_info', JSON.stringify({
                name: _printerName,
                id: _device.id || '',
                timestamp: new Date().toISOString()
            }));

            // Listen untuk disconnect event
            _device.addEventListener('gattserverdisconnected', () => {
                _isConnected = false;
                _characteristic = null;
                _server = null;
                _triggerStatus('disconnected', _printerName + ' terputus');
                console.warn('[PrinterHelper] Device disconnected');
            });

            _triggerStatus('connected', _printerName);

            return { name: _printerName, id: _device.id || '' };

        } catch (error) {
            _isConnected = false;
            _characteristic = null;
            _triggerStatus('error', error.message);
            throw error;
        }
    }

    // ============================================
    // PUBLIC: TEST PRINT
    // ============================================
    async function testPrint() {
        if (!_characteristic) {
            throw new Error('Printer belum di-pair. Klik "Scan" atau "Reconnect" terlebih dahulu.');
        }

        _triggerStatus('printing', 'Test print...');

        const now = new Date();
        const date = now.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
        const time = now.toLocaleTimeString('id-ID');

        const data = _mergeBytes(
            INIT,
            ALIGN_CENTER,
            FONT_DOUBLE, BOLD_ON,
            '*** TEST PRINT ***\n',
            FONT_NORMAL, BOLD_OFF,
            LINE_FEED,
            BOLD_ON, 'WPOS - POS System\n', BOLD_OFF,
            'Printer Thermal Bluetooth\n',
            LINE_FEED,
            ALIGN_LEFT,
            _line('-'),
            _columns('Tanggal:', date),
            _columns('Waktu:', time),
            _columns('Printer:', _printerName || 'Connected'),
            _columns('Status:', 'OK - Siap Cetak'),
            _line('-'),
            LINE_FEED,
            ALIGN_CENTER,
            'Printer berfungsi dengan baik!\n',
            'Silakan simpan pengaturan.\n',
            LINE_FEED,
            LINE_FEED,
            LINE_FEED,
            CUT_PAPER
        );

        await _send(data);
        _triggerStatus('printed', 'Test print selesai');

        return { success: true, printer: _printerName };
    }

    // ============================================
    // PUBLIC: PRINT STRUK TRANSAKSI
    // ============================================
    async function printReceipt(options = {}) {
        if (!_isConnected || !_characteristic) {
            // Coba auto-reconnect jika ada device tersimpan
            const saved = localStorage.getItem('thermal_printer_info');
            if (!saved) {
                throw new Error('Printer belum terhubung. Buka halaman Pengaturan untuk pair printer.');
            }
            throw new Error('Printer terputus. Buka halaman Pengaturan dan klik "Reconnect".');
        }

        _triggerStatus('printing', 'Mencetak struk...');

        const {
            namaToko = 'WPOS',
            alamat = '',
            telepon = '',
            noTransaksi = '-',
            tanggal = new Date().toLocaleDateString('id-ID'),
            waktu = new Date().toLocaleTimeString('id-ID'),
            kasir = '-',
            items = [],       // [{ nama, qty, harga, subtotal }]
            subtotal = 0,
            diskon = 0,
            pajak = 0,
            total = 0,
            bayar = 0,
            kembalian = 0,
            metodeBayar = 'Tunai',
            catatan = '',
            footer = 'Terima kasih atas kunjungan Anda!',
            paperWidth = 32,       // 32 char untuk 58mm, 42 char untuk 80mm
        } = options;

        const W = paperWidth;

        function col(left, right) {
            return _columns(left, right, W);
        }
        function line(char = '-') {
            return _line(char, W);
        }
        function formatRupiah(num) {
            return 'Rp ' + Number(num).toLocaleString('id-ID');
        }

        // Build struk
        const chunks = [
            INIT,
            ALIGN_CENTER,
            FONT_DOUBLE, BOLD_ON,
            namaToko + '\n',
            FONT_NORMAL, BOLD_OFF,
        ];

        if (alamat) chunks.push(alamat + '\n');
        if (telepon) chunks.push('Telp: ' + telepon + '\n');

        chunks.push(
            LINE_FEED,
            ALIGN_LEFT,
            line('='),
            BOLD_ON, 'STRUK PEMBAYARAN\n', BOLD_OFF,
            line('='),
            col('No. Transaksi:', noTransaksi),
            col('Tanggal:', tanggal),
            col('Waktu:', waktu),
            col('Kasir:', kasir),
            line('-'),
        );

        // Items
        for (const item of items) {
            const namaItem = String(item.nama || '').substring(0, W - 2);
            chunks.push(namaItem + '\n');
            const qtyHarga = `  ${item.qty} x ${formatRupiah(item.harga)}`;
            chunks.push(col(qtyHarga, formatRupiah(item.subtotal)));
        }

        chunks.push(line('-'));

        // Subtotal, diskon, pajak, total
        chunks.push(col('Subtotal:', formatRupiah(subtotal)));
        if (diskon > 0) chunks.push(col('Diskon:', '- ' + formatRupiah(diskon)));
        if (pajak > 0) chunks.push(col('Pajak:', formatRupiah(pajak)));

        chunks.push(
            line('='),
            BOLD_ON,
            col('TOTAL:', formatRupiah(total)),
            BOLD_OFF,
            line('='),
            col('Bayar (' + metodeBayar + '):', formatRupiah(bayar)),
            col('Kembalian:', formatRupiah(kembalian)),
            line('-'),
        );

        if (catatan) {
            chunks.push('Catatan: ' + catatan + '\n');
            chunks.push(line('-'));
        }

        chunks.push(
            LINE_FEED,
            ALIGN_CENTER,
            footer + '\n',
            LINE_FEED,
            LINE_FEED,
            LINE_FEED,
            CUT_PAPER
        );

        const data = _mergeBytes(...chunks);
        await _send(data);

        _triggerStatus('printed', 'Struk berhasil dicetak');
        return { success: true };
    }

    // ============================================
    // PUBLIC: CEK APAKAH PRINTER TERHUBUNG
    // ============================================
    function isConnected() {
        return _isConnected && _characteristic !== null;
    }

    // ============================================
    // PUBLIC: DISCONNECT MANUAL
    // ============================================
    function disconnect() {
        if (_device && _device.gatt.connected) {
            _device.gatt.disconnect();
        }
        _isConnected = false;
        _characteristic = null;
        _server = null;
        _triggerStatus('disconnected', 'Disconnected manual');
    }

    // ============================================
    // PUBLIC: GET PRINTER NAME
    // ============================================
    function getPrinterName() {
        return _printerName || '';
    }

    // ============================================
    // EXPOSE PUBLIC API
    // ============================================
    window.PrinterHelper = {
        // Methods
        scanAndPair,
        testPrint,
        printReceipt,
        isConnected,
        disconnect,
        getPrinterName,

        // Callback — set dari luar untuk monitor status
        // Contoh: window.PrinterHelper.onStatusChange = function(status, msg) { ... }
        // Status values: 'scanning' | 'pairing' | 'connected' | 'disconnected' | 'printing' | 'printed' | 'error'
        onStatusChange: null,
    };

    console.log('[PrinterHelper] ✅ Loaded & ready');

})(window);
