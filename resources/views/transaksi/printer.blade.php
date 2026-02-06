<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Struk - Toko Sahabat</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        .receipt-container {
            max-width: 300px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .status-indicator {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
        }
        .status-indicator.connecting {
            background: #fef3c7;
            color: #92400e;
        }
        .status-indicator.printing {
            background: #dbeafe;
            color: #1e40af;
        }
        .status-indicator.success {
            background: #d1fae5;
            color: #065f46;
        }
        .status-indicator.error {
            background: #fee2e2;
            color: #991b1b;
        }
        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(0,0,0,0.1);
            border-radius: 50%;
            border-top-color: #3b82f6;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .receipt-data {
            display: none;
        }
        .btn {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-primary {
            background: #3b82f6;
            color: white;
        }
        .btn-secondary {
            background: #6b7280;
            color: white;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div id="statusIndicator" class="status-indicator connecting">
            <div class="spinner"></div>
            <p style="margin: 10px 0 0 0;">Menghubungkan ke printer...</p>
        </div>

        <button id="retryBtn" class="btn btn-primary" style="display: none;" onclick="printReceipt()">
            Cetak Ulang
        </button>
        <button class="btn btn-secondary" onclick="window.close()">
            Tutup
        </button>
    </div>

    <!-- Receipt Data (Hidden) -->
    <div class="receipt-data">
        @php
            $data = $receiptData ?? [];
        @endphp
        <div id="receiptData" 
            data-no-transaksi="{{ $data['no_transaksi'] ?? '' }}"
            data-tanggal="{{ $data['tanggal_penjualan'] ?? '' }}"
            data-kasir="{{ $data['kasir'] ?? '' }}"
            data-total-pembayaran="{{ $data['total_pembayaran'] ?? 0 }}"
            data-total-bayar="{{ $data['total_bayar'] ?? 0 }}"
            data-kembalian="{{ $data['kembalian_pembayaran'] ?? 0 }}"
            data-status="{{ $data['status_pembayaran'] ?? 'lunas' }}"
            data-items="{{ json_encode($data['items'] ?? []) }}">
        </div>
    </div>

    <script>
        let cachedDevice = null;

        // Auto print saat halaman load
        window.addEventListener('DOMContentLoaded', function() {
            console.log('🖨️ Printer page loaded');
            
            // Cek apakah ada printer tersimpan
            const printerStatus = localStorage.getItem('thermal_printer_status');
            if (printerStatus) {
                const printer = JSON.parse(printerStatus);
                console.log('📱 Printer info:', printer);
            }

            // Auto print
            setTimeout(() => {
                printReceipt();
            }, 500);
        });

        async function printReceipt() {
            const statusDiv = document.getElementById('statusIndicator');
            const retryBtn = document.getElementById('retryBtn');
            
            statusDiv.className = 'status-indicator printing';
            statusDiv.innerHTML = '<div class="spinner"></div><p style="margin: 10px 0 0 0;">Mencetak struk...</p>';
            retryBtn.style.display = 'none';

            try {
                // Get printer info dari localStorage
                const printerStatus = localStorage.getItem('thermal_printer_status');
                if (!printerStatus) {
                    throw new Error('Printer belum dikonfigurasi. Silakan ke menu Pengaturan untuk setup printer.');
                }

                const printer = JSON.parse(printerStatus);
                const printerName = printer.printerName;

                if (!printerName) {
                    throw new Error('Nama printer tidak ditemukan.');
                }

                console.log('🖨️ Printing to:', printerName);

                // Coba ambil device yang sudah di-pair
                let device = cachedDevice;

                if (!device || !device.gatt.connected) {
                    if (navigator.bluetooth.getDevices) {
                        const devices = await navigator.bluetooth.getDevices();
                        device = devices.find(d => d.name === printerName);
                    }

                    if (!device) {
                        // Request device jika belum ada
                        device = await navigator.bluetooth.requestDevice({
                            filters: [{ name: printerName }],
                            optionalServices: ['000018f0-0000-1000-8000-00805f9b34fb']
                        });
                    }

                    cachedDevice = device;
                }

                // Connect
                if (!device.gatt.connected) {
                    await device.gatt.connect();
                }

                const service = await device.gatt.getPrimaryService('000018f0-0000-1000-8000-00805f9b34fb');
                const char = await service.getCharacteristic('00002af1-0000-1000-8000-00805f9b34fb');

                // Get receipt data
                const receiptData = document.getElementById('receiptData');
                const items = JSON.parse(receiptData.dataset.items || '[]');

                // ESC/POS commands
                const ESC = 0x1B, GS = 0x1D;
                const encoder = new TextEncoder();

                // Initialize
                await char.writeValue(new Uint8Array([ESC, 0x40]));
                await new Promise(r => setTimeout(r, 100));

                // Header - Center, Bold
                await char.writeValue(new Uint8Array([ESC, 0x61, 1])); // Center
                await char.writeValue(new Uint8Array([ESC, 0x45, 1])); // Bold
                await char.writeValue(encoder.encode('TOKO SAHABAT\n'));
                await char.writeValue(new Uint8Array([ESC, 0x45, 0])); // Bold off
                
                await char.writeValue(encoder.encode('Jl. Contoh No. 123\n'));
                await char.writeValue(encoder.encode('Telp: 0812-3456-7890\n'));
                await char.writeValue(new Uint8Array([ESC, 0x61, 0])); // Left align
                await char.writeValue(encoder.encode('================================\n'));

                // Info transaksi
                await char.writeValue(encoder.encode(`No   : ${receiptData.dataset.noTransaksi}\n`));
                await char.writeValue(encoder.encode(`Tgl  : ${receiptData.dataset.tanggal}\n`));
                await char.writeValue(encoder.encode(`Kasir: ${receiptData.dataset.kasir}\n`));
                await char.writeValue(encoder.encode('================================\n'));

                // Items
                for (const item of items) {
                    const nama = item.nama_produk.substring(0, 20).padEnd(20);
                    const qty = `${item.qty_produk}x`;
                    const harga = formatRupiah(item.harga_produk);
                    const subtotal = formatRupiah(item.subtotal_harga);
                    
                    await char.writeValue(encoder.encode(`${nama}\n`));
                    await char.writeValue(encoder.encode(`  ${qty} @ ${harga} = ${subtotal}\n`));
                }

                await char.writeValue(encoder.encode('================================\n'));

                // Total
                const total = formatRupiah(parseFloat(receiptData.dataset.totalPembayaran));
                const bayar = formatRupiah(parseFloat(receiptData.dataset.totalBayar));
                const kembalian = formatRupiah(parseFloat(receiptData.dataset.kembalian));
                const status = receiptData.dataset.status;

                await char.writeValue(encoder.encode(`TOTAL       : ${total}\n`));
                
                if (status === 'lunas') {
                    await char.writeValue(encoder.encode(`BAYAR       : ${bayar}\n`));
                    await char.writeValue(encoder.encode(`KEMBALIAN   : ${kembalian}\n`));
                } else {
                    await char.writeValue(new Uint8Array([ESC, 0x45, 1])); // Bold
                    await char.writeValue(encoder.encode('STATUS      : PIUTANG\n'));
                    await char.writeValue(new Uint8Array([ESC, 0x45, 0])); // Bold off
                }

                await char.writeValue(encoder.encode('================================\n'));

                // Footer
                await char.writeValue(new Uint8Array([ESC, 0x61, 1])); // Center
                await char.writeValue(encoder.encode('Terima Kasih\n'));
                await char.writeValue(encoder.encode('Selamat Berbelanja Kembali\n'));
                await char.writeValue(new Uint8Array([ESC, 0x61, 0])); // Left
                await char.writeValue(encoder.encode('\n\n\n'));

                // Cut paper
                await char.writeValue(new Uint8Array([GS, 0x56, 0x00]));

                console.log('✅ Print successful!');

                // Success
                statusDiv.className = 'status-indicator success';
                statusDiv.innerHTML = '<p style="margin: 0;">✓ Struk berhasil dicetak!</p>';
                retryBtn.style.display = 'block';

                // Auto close after 2 seconds
                setTimeout(() => {
                    window.close();
                }, 2000);

            } catch (error) {
                console.error('❌ Print error:', error);
                
                statusDiv.className = 'status-indicator error';
                statusDiv.innerHTML = `<p style="margin: 0;">✗ Gagal mencetak</p><small>${error.message}</small>`;
                retryBtn.style.display = 'block';
            }
        }

        function formatRupiah(amount) {
            return 'Rp ' + amount.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }
    </script>
</body>
</html>