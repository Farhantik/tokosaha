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
    let _device = null;
    let _server = null;
    let _characteristic = null;
    let _isConnected = false;
    let _printerName = '';
    let _autoReconnectTimer = null;

    const SERVICE_UUIDS = [
        '000018f0-0000-1000-8000-00805f9b34fb',
        '00001101-0000-1000-8000-00805f9b34fb',
        'e7810a71-73ae-499d-8c15-faa9aef0c3f2',
        '49535343-fe7d-4ae5-8fa9-9fafd205e455',
    ];

    const CHAR_UUIDS = [
        '00002af1-0000-1000-8000-00805f9b34fb',
        '000018f1-0000-1000-8000-00805f9b34fb',
        '49535343-8841-43f4-a8d4-ecbe34729bb3',
        '49535343-1e4d-4bd9-ba61-23c647249616',
    ];

    const ESC = 0x1B;
    const GS = 0x1D;
    const INIT = [ESC, 0x40];
    const ALIGN_CENTER = [ESC, 0x61, 0x01];
    const ALIGN_LEFT = [ESC, 0x61, 0x00];
    const BOLD_ON = [ESC, 0x45, 0x01];
    const BOLD_OFF = [ESC, 0x45, 0x00];
    const FONT_NORMAL = [ESC, 0x21, 0x00];
    const FONT_DOUBLE = [ESC, 0x21, 0x30];
    const CUT_PAPER = [GS, 0x56, 0x42, 0x00];
    const LINE_FEED = [0x0A];

    // ============================================
    // INTERNAL HELPERS
    // ============================================
    async function _send(data) {
        if (!_characteristic) {
            throw new Error('Printer belum terhubung. Klik "Reconnect" terlebih dahulu.');
        }
        const CHUNK_SIZE = 512;
        const bytes = data instanceof Uint8Array ? data : new Uint8Array(data);
        for (let i = 0; i < bytes.length; i += CHUNK_SIZE) {
            await _characteristic.writeValue(bytes.slice(i, i + CHUNK_SIZE));
            await _delay(20);
        }
    }

    function _encodeText(text) {
        try { return new TextEncoder().encode(text); }
        catch (e) {
            const b = [];
            for (let i = 0; i < text.length; i++) b.push(text.charCodeAt(i) & 0xFF);
            return new Uint8Array(b);
        }
    }

    function _mergeBytes(...arrays) {
        const flat = [];
        for (const a of arrays) {
            if (Array.isArray(a)) flat.push(...a);
            else if (a instanceof Uint8Array) flat.push(...a);
            else if (typeof a === 'string') flat.push(..._encodeText(a));
        }
        return new Uint8Array(flat);
    }

    function _delay(ms) { return new Promise(r => setTimeout(r, ms)); }

    function _line(char = '-', width = 32) { return char.repeat(width) + '\n'; }

    function _columns(left, right, width = 32) {
        return left + ' '.repeat(Math.max(1, width - left.length - right.length)) + right + '\n';
    }

    function _triggerStatus(status, message = '') {
        if (typeof window.PrinterHelper.onStatusChange === 'function') {
            window.PrinterHelper.onStatusChange(status, message);
        }
        console.log(`[PrinterHelper] ${status}:`, message || '');
    }

    async function _findWritableChar(service) {
        try {
            const chars = await service.getCharacteristics();
            for (const c of chars) {
                if (c.properties.write || c.properties.writeWithoutResponse) return c;
            }
        } catch (_) { }
        for (const uuid of CHAR_UUIDS) {
            try { const c = await service.getCharacteristic(uuid); if (c) return c; } catch (_) { }
        }
        return null;
    }

    // ============================================
    // INTERNAL: INTI KONEKSI KE DEVICE
    // ============================================
    async function _connectToDevice(device) {
        if (device.gatt.connected) {
            try { device.gatt.disconnect(); } catch (e) { }
            await _delay(400);
        }

        _server = await device.gatt.connect();

        let service = null;
        for (const uuid of SERVICE_UUIDS) {
            try { service = await _server.getPrimaryService(uuid); break; } catch (_) { }
        }
        if (!service) {
            try {
                const services = await _server.getPrimaryServices();
                if (services.length > 0) service = services[0];
            } catch (e) {
                throw new Error('Tidak dapat menemukan service printer.');
            }
        }
        if (!service) throw new Error('Tidak ada service yang ditemukan.');

        const char = await _findWritableChar(service);
        if (!char) throw new Error('Tidak dapat menemukan write characteristic.');

        return char;
    }

    // ============================================
    // INTERNAL: HANDLER DISCONNECT
    // ============================================
    function _onDisconnected() {
        _isConnected = false;
        _characteristic = null;
        console.warn('[PrinterHelper] Terputus dari', _printerName);
        _triggerStatus('disconnected', (_printerName || 'Printer') + ' terputus, mencoba sambung ulang...');

        if (!_device) return;
        if (_autoReconnectTimer) clearTimeout(_autoReconnectTimer);

        _autoReconnectTimer = setTimeout(async () => {
            try {
                _characteristic = await _connectToDevice(_device);
                _isConnected = true;
                _triggerStatus('connected', _printerName + ' (tersambung kembali)');
            } catch (err) {
                console.warn('[PrinterHelper] Auto-reconnect gagal:', err.message);
                _triggerStatus('disconnected', _printerName + ' — klik Reconnect.');
            }
        }, 2000);
    }

    function _saveInfo(device) {
        localStorage.setItem('thermal_printer_info', JSON.stringify({
            name: device.name || 'Unknown',
            id: device.id || '',
            timestamp: new Date().toISOString()
        }));
    }

    // ============================================
    // PUBLIC: AUTO-RECONNECT SAAT PAGE LOAD
    // ============================================
    async function autoReconnectOnLoad() {
        if (!navigator.bluetooth) return;
        if (typeof navigator.bluetooth.getDevices !== 'function') {
            console.log('[PrinterHelper] getDevices() tidak tersedia — skip auto-reconnect on load');
            return;
        }

        const savedRaw = localStorage.getItem('thermal_printer_info');
        if (!savedRaw) return;

        let savedName = '';
        try { savedName = JSON.parse(savedRaw).name || ''; } catch (e) { return; }
        if (!savedName) return;

        try {
            _triggerStatus('pairing', 'Menyambung otomatis ke ' + savedName + '...');

            const devices = await navigator.bluetooth.getDevices();
            console.log('[PrinterHelper] getDevices() hasil:', devices.map(d => d.name));

            if (!devices || devices.length === 0) {
                _triggerStatus('disconnected', savedName + ' — klik Reconnect');
                return;
            }

            const target = devices.find(d => d.name === savedName) || devices[0];

            _device = target;
            _printerName = target.name || savedName;

            target.removeEventListener('gattserverdisconnected', _onDisconnected);
            target.addEventListener('gattserverdisconnected', _onDisconnected);

            _characteristic = await _connectToDevice(target);
            _isConnected = true;

            _saveInfo(target);
            _triggerStatus('connected', _printerName + ' (otomatis tersambung)');

        } catch (err) {
            _isConnected = false;
            _characteristic = null;
            console.warn('[PrinterHelper] Auto-reconnect on load gagal:', err.message);
            _triggerStatus('disconnected', savedName + ' — klik Reconnect untuk sambung manual');
        }
    }

    // ============================================
    // PUBLIC: SCAN & PAIR PRINTER BARU
    // ============================================
    async function scanAndPair() {
        if (!navigator.bluetooth) {
            throw new Error('Web Bluetooth tidak didukung. Gunakan Chrome atau Edge terbaru.');
        }

        _triggerStatus('scanning');

        try {
            _device = await navigator.bluetooth.requestDevice({
                acceptAllDevices: true,
                optionalServices: SERVICE_UUIDS
            });

            _printerName = _device.name || 'Unknown Printer';
            _triggerStatus('pairing', _printerName);

            _device.removeEventListener('gattserverdisconnected', _onDisconnected);
            _device.addEventListener('gattserverdisconnected', _onDisconnected);

            _characteristic = await _connectToDevice(_device);
            _isConnected = true;

            _saveInfo(_device);
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
    // PUBLIC: RECONNECT MANUAL
    // ============================================
    async function reconnect() {
        if (_device) {
            _triggerStatus('pairing', 'Menyambungkan ulang ke ' + _printerName + '...');
            try {
                _characteristic = await _connectToDevice(_device);
                _isConnected = true;
                _triggerStatus('connected', _printerName);
                return { name: _printerName, id: _device.id || '' };
            } catch (err) {
                console.warn('[PrinterHelper] Reconnect dengan device lama gagal:', err.message);
            }
        }

        if (navigator.bluetooth && typeof navigator.bluetooth.getDevices === 'function') {
            try {
                const savedRaw = localStorage.getItem('thermal_printer_info');
                const savedName = savedRaw ? (JSON.parse(savedRaw).name || '') : '';
                const devices = await navigator.bluetooth.getDevices();

                if (devices && devices.length > 0) {
                    const target = devices.find(d => d.name === savedName) || devices[0];
                    _device = target;
                    _printerName = target.name || savedName;
                    target.removeEventListener('gattserverdisconnected', _onDisconnected);
                    target.addEventListener('gattserverdisconnected', _onDisconnected);

                    _triggerStatus('pairing', 'Menyambung ke ' + _printerName + '...');
                    _characteristic = await _connectToDevice(target);
                    _isConnected = true;
                    _triggerStatus('connected', _printerName);
                    return { name: _printerName, id: target.id || '' };
                }
            } catch (e) {
                console.warn('[PrinterHelper] getDevices() dalam reconnect gagal:', e.message);
            }
        }

        return await scanAndPair();
    }

    // ============================================
    // PUBLIC: TEST PRINT
    // ============================================
    async function testPrint() {
        if (!_characteristic) {
            throw new Error('Printer belum terhubung. Klik "Reconnect" terlebih dahulu.');
        }
        _triggerStatus('printing', 'Test print...');

        const now = new Date();
        const date = now.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
        const time = now.toLocaleTimeString('id-ID');

        await _send(_mergeBytes(
            INIT,
            ALIGN_CENTER, FONT_DOUBLE, BOLD_ON, '*** TEST PRINT ***\n', FONT_NORMAL, BOLD_OFF,
            LINE_FEED,
            BOLD_ON, 'WPOS - POS System\n', BOLD_OFF,
            'Printer Thermal Bluetooth\n', LINE_FEED,
            ALIGN_LEFT, _line('-'),
            _columns('Tanggal:', date), _columns('Waktu:', time),
            _columns('Printer:', _printerName || 'Connected'),
            _columns('Status:', 'OK - Siap Cetak'), _line('-'), LINE_FEED,
            ALIGN_CENTER,
            'Printer berfungsi dengan baik!\n',
            'Silakan simpan pengaturan.\n',
            LINE_FEED, LINE_FEED, LINE_FEED, CUT_PAPER
        ));

        _triggerStatus('printed', 'Test print selesai');
        return { success: true, printer: _printerName };
    }

    // ============================================
    // PUBLIC: PRINT STRUK TRANSAKSI
    // FIX: payment_methods dicetak per-baris (bukan digabung)
    // FIX: footer lengkap dengan semua kalimat
    // ============================================
    async function printReceipt(options = {}) {
        if (!_isConnected || !_characteristic) {
            if (_device) {
                try {
                    _triggerStatus('pairing', 'Menyambung ulang sebelum print...');
                    _characteristic = await _connectToDevice(_device);
                    _isConnected = true;
                    _triggerStatus('connected', _printerName);
                } catch (err) {
                    throw new Error('Printer terputus dan gagal reconnect: ' + err.message);
                }
            } else {
                throw new Error('Printer belum terhubung. Buka Pengaturan dan klik "Reconnect".');
            }
        }

        _triggerStatus('printing', 'Mencetak struk...');

        const {
            namaToko = 'WPOS',
            alamat = '',
            telepon = '',
            noTransaksi = '-',
            kasir = '-',
            diskon = 0,
            pajak = 0,
            catatan = '',
            paperWidth = 32,
            // Dari transaksi.blade.php
            no_transaksi,
            tanggal,       // dari blade: format d/m/Y
            waktu,         // dari blade: format H:i
            total_pembayaran,
            total_bayar,
            kembalian_pembayaran,
            sisa_tagihan,
            status_pembayaran,
            payment_methods,
        } = options;

        // FIX: Gunakan tanggal & waktu dari transaksi, bukan waktu sekarang
        const _now = new Date();
        const _tanggal = tanggal || _now.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
        const _waktu = waktu || _now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

        const _noTrx = no_transaksi ?? noTransaksi;
        const _total = Number(total_pembayaran ?? options.total ?? 0);
        const _bayar = Number(total_bayar ?? options.bayar ?? 0);
        const _kembalian = Number(kembalian_pembayaran ?? options.kembalian ?? 0);
        const _sisa = Number(sisa_tagihan ?? Math.max(0, _total - _bayar));
        const _status = status_pembayaran || 'lunas';

        const _items = (options.items || []).map(i => ({
            nama: i.nama || i.nama_produk || '-',
            qty: i.qty || i.qty_produk || 1,
            harga: i.harga || i.harga_produk || 0,
            subtotal: i.subtotal || i.subtotal_harga || 0,
        }));

        const W = paperWidth;
        const col = (l, r) => _columns(l, r, W);
        const ln = (c = '-') => _line(c, W);
        const fmt = n => 'Rp ' + Number(n).toLocaleString('id-ID');

        // Label & icon untuk payment_methods
        const pmLabels = { tunai: 'Tunai', qris: 'QRIS', bayar_nanti: 'Bayar Nanti' };

        // ─── Bangun chunks cetak ────────────────────────
        const chunks = [
            INIT,
            ALIGN_CENTER, FONT_DOUBLE, BOLD_ON, namaToko + '\n', FONT_NORMAL, BOLD_OFF,
        ];
        if (alamat) chunks.push(alamat + '\n');
        if (telepon) chunks.push('Telp: ' + telepon + '\n');

        chunks.push(
            LINE_FEED, ALIGN_LEFT,
            ln('='), BOLD_ON, 'STRUK PEMBAYARAN\n', BOLD_OFF, ln('='),
            col('No:', _noTrx),
            col('Tanggal:', _tanggal),
            col('Waktu:', _waktu),
            col('Kasir:', kasir),
            ln('-'),
        );

        // Items
        for (const it of _items) {
            chunks.push(String(it.nama).substring(0, W - 2) + '\n');
            chunks.push(col(`  ${it.qty} x ${fmt(it.harga)}`, fmt(it.subtotal)));
        }

        const _subtotal = _items.reduce((s, i) => s + Number(i.subtotal), 0);
        chunks.push(ln('-'), col('Subtotal:', fmt(_subtotal || _total)));
        if (diskon > 0) chunks.push(col('Diskon:', '- ' + fmt(diskon)));
        if (pajak > 0) chunks.push(col('Pajak:', fmt(pajak)));

        chunks.push(ln('='), BOLD_ON, col('TOTAL:', fmt(_total)), BOLD_OFF, ln('='));

        // ─── FIX: Payment methods dicetak per-baris ────
        if (payment_methods && Array.isArray(payment_methods) && payment_methods.length > 0) {

            // Hapus duplikat berdasarkan method+amount
            const seen = new Set();
            const uniquePm = payment_methods.filter(p => {
                const key = (p.method || '') + ':' + (p.amount || 0);
                if (seen.has(key)) return false;
                seen.add(key);
                return true;
            });

            chunks.push('Rincian Pembayaran:\n');
            for (const p of uniquePm) {
                const label = pmLabels[p.method] || p.method || 'Lainnya';
                const amount = Number(p.amount || 0);

                // Jika bayar_nanti & amount 0, gunakan sisa
                const displayAmount = (p.method === 'bayar_nanti' && amount <= 0)
                    ? (_sisa > 0 ? _sisa : _total)
                    : amount;

                chunks.push(col('  ' + label + ':', fmt(displayAmount)));
            }

        } else {
            // Fallback jika payment_methods kosong
            chunks.push(col('Bayar:', fmt(_bayar || _total)));
        }

        // ─── Kembalian / Status ─────────────────────────
        if (_status === 'lunas') {
            if (_kembalian > 0) {
                chunks.push(col('Kembalian:', fmt(_kembalian)));
            }
        } else if (_status === 'belum_bayar') {
            chunks.push(
                ln('-'),
                BOLD_ON, '** BELUM LUNAS **\n', BOLD_OFF,
                col('Sisa Tagihan:', fmt(_total)),
            );
        } else if (_status === 'bayar_sebagian') {
            chunks.push(
                ln('-'),
                BOLD_ON, '** BAYAR SEBAGIAN **\n', BOLD_OFF,
                col('Dibayar:', fmt(_bayar)),
                col('Sisa:', fmt(_sisa > 0 ? _sisa : Math.max(0, _total - _bayar))),
            );
        }

        if (catatan) chunks.push(ln('-'), 'Catatan: ' + catatan + '\n');

        // ─── FIX: Footer lengkap semua kalimat ──────────
        chunks.push(
            ln('-'),
            LINE_FEED,
            ALIGN_CENTER,
            'Terima Kasih Atas\n',
            'Kunjungan Anda!\n',
            LINE_FEED,
            'Barang yang sudah dibeli\n',
            'tidak dapat ditukar/dikembalikan\n',
            LINE_FEED,
            'Simpan struk ini sebagai\n',
            'bukti pembelian yang sah\n',
            LINE_FEED,
            '--------------------------------\n',
            'Made with by WPOS\n',
            LINE_FEED, LINE_FEED, LINE_FEED,
            CUT_PAPER,
        );

        await _send(_mergeBytes(...chunks));
        _triggerStatus('printed', 'Struk berhasil dicetak');
        return { success: true };
    }

    // ============================================
    // PUBLIC API
    // ============================================
    function isConnected() { return _isConnected && _characteristic !== null; }
    function hasDevice() { return _device !== null; }
    function getPrinterName() { return _printerName || ''; }

    function disconnect() {
        if (_autoReconnectTimer) { clearTimeout(_autoReconnectTimer); _autoReconnectTimer = null; }
        if (_device && _device.gatt && _device.gatt.connected) {
            try { _device.gatt.disconnect(); } catch (e) { }
        }
        _isConnected = false; _characteristic = null; _server = null;
        _triggerStatus('disconnected', 'Terputus manual');
    }

    window.PrinterHelper = {
        scanAndPair,
        reconnect,
        testPrint,
        printReceipt,
        isConnected,
        hasDevice,
        disconnect,
        getPrinterName,
        onStatusChange: null,
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => setTimeout(autoReconnectOnLoad, 500));
    } else {
        setTimeout(autoReconnectOnLoad, 500);
    }

    console.log('[PrinterHelper] ✅ Loaded (auto-reconnect aktif)');

})(window);
