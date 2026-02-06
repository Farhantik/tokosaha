@extends('layouts.app')

@section('title', 'Pengaturan - Toko Sahabat')
@section('page-title', 'Pengaturan Sistem')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    .setting-card {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    .setting-card:hover {
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }
    .toggle-switch {
        position: relative;
        width: 60px;
        height: 30px;
        background-color: #cbd5e0;
        border-radius: 15px;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    .toggle-switch.active {
        background-color: #10b981;
    }
    .toggle-slider {
        position: absolute;
        top: 3px;
        left: 3px;
        width: 24px;
        height: 24px;
        background-color: white;
        border-radius: 50%;
        transition: transform 0.3s;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }
    .toggle-switch.active .toggle-slider {
        transform: translateX(30px);
    }
    .printer-status {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 600;
    }
    .printer-status.connected {
        background-color: #d1fae5;
        color: #065f46;
    }
    .printer-status.disconnected {
        background-color: #fee2e2;
        color: #991b1b;
    }
    .printer-status.pairing {
        background-color: #dbeafe;
        color: #1e40af;
    }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-xl">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
            <p class="text-green-700 font-semibold">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if ($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl">
        <div class="flex items-center mb-2">
            <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
            <p class="text-red-700 font-semibold">Terjadi kesalahan:</p>
        </div>
        <ul class="list-disc list-inside text-red-600 text-sm ml-8">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Printer Settings -->
    <div class="setting-card">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-print text-emerald-600 mr-3"></i>
                    Pengaturan Printer Thermal
                </h2>
                <p class="text-gray-600 text-sm mt-1">Konfigurasi printer thermal untuk cetak struk otomatis</p>
            </div>
        </div>

        <!-- Printer Status -->
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-4 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-print text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Status Printer</p>
                        <p id="printerStatusText" class="font-bold text-gray-800">
                            @if($settings && $settings->printer_name)
                                {{ $settings->printer_name }}
                            @else
                                Tidak Terhubung
                            @endif
                        </p>
                    </div>
                </div>
                <span id="printerStatusBadge" class="printer-status disconnected">
                    <i class="fas fa-circle mr-2 text-xs"></i>
                    Offline
                </span>
            </div>
        </div>

        <form id="settingsForm" method="POST" action="{{ route('settings.update') }}">
            @csrf

            <!-- Auto Print Toggle -->
            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-emerald-50 to-green-50 rounded-xl mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-emerald-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-bolt text-white"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">Auto Print Struk</p>
                        <p class="text-sm text-gray-600">Cetak struk otomatis setelah transaksi</p>
                    </div>
                </div>
                <div id="autoPrintToggle" class="toggle-switch {{ ($settings && $settings->auto_print) ? 'active' : '' }}" onclick="toggleAutoPrint()">
                    <div class="toggle-slider"></div>
                </div>
                <input type="hidden" name="auto_print" id="autoPrintInput" value="{{ ($settings && $settings->auto_print) ? '1' : '0' }}">
            </div>

            <!-- Printer Selection -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-print mr-2"></i>Nama Printer
                    </label>
                    <div class="flex space-x-2">
                        <input type="text" name="printer_name" id="printerName" 
                            value="{{ $settings ? $settings->printer_name : '' }}" 
                            placeholder="Contoh: RPP02N-XXXX"
                            class="flex-1 px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:outline-none transition-all"
                            required>
                        <button type="button" onclick="scanPrinters()" id="scanBtn" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold rounded-xl hover:shadow-lg transition-all">
                            <i class="fas fa-search mr-2"></i>Scan
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>Klik "Scan" untuk mencari printer Bluetooth atau masukkan nama manual
                    </p>
                </div>

                <!-- Printer Settings -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-ruler-horizontal mr-2"></i>Lebar Kertas (mm)
                        </label>
                        <select name="paper_width" id="paperWidth" class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:outline-none transition-all">
                            <option value="58" {{ ($settings && $settings->paper_width == 58) ? 'selected' : '' }}>58mm (2 inch)</option>
                            <option value="80" {{ ($settings && $settings->paper_width == 80) ? 'selected' : '' }}>80mm (3 inch)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-font mr-2"></i>Ukuran Font
                        </label>
                        <select name="font_size" id="fontSize" class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:outline-none transition-all">
                            <option value="small" {{ ($settings && $settings->font_size == 'small') ? 'selected' : '' }}>Kecil</option>
                            <option value="medium" {{ ($settings && $settings->font_size == 'medium') ? 'selected' : '' }}>Sedang</option>
                            <option value="large" {{ ($settings && $settings->font_size == 'large') ? 'selected' : '' }}>Besar</option>
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-3">
                    <button type="button" onclick="reconnectPrinter()" id="reconnectBtn" 
                            class="flex-1 bg-gradient-to-r from-purple-500 to-pink-600 hover:from-purple-600 hover:to-pink-700 text-white font-bold py-3 rounded-xl transition-all shadow-lg hover:shadow-xl"
                            style="display: {{ ($settings && $settings->printer_name) ? 'block' : 'none' }}">
                        <i class="fas fa-sync-alt mr-2"></i>Reconnect
                    </button>
                    <button type="button" onclick="testPrint()" id="testPrintBtn" class="flex-1 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white font-bold py-3 rounded-xl transition-all shadow-lg hover:shadow-xl">
                        <i class="fas fa-print mr-2"></i>Test Print
                    </button>
                    <button type="submit" class="flex-1 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-bold py-3 rounded-xl transition-all shadow-lg hover:shadow-xl">
                        <i class="fas fa-save mr-2"></i>Simpan Pengaturan
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Instructions -->
    <div class="setting-card">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-book text-blue-600 mr-3"></i>
            Panduan Penggunaan
        </h3>
        <div class="space-y-3">
            <div class="flex items-start space-x-3">
                <div class="w-8 h-8 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center font-bold flex-shrink-0">1</div>
                <p class="text-gray-700 text-sm">Pastikan printer thermal Bluetooth (RPP02N/ALGOO) sudah ON dan dalam keadaan siap</p>
            </div>
            <div class="flex items-start space-x-3">
                <div class="w-8 h-8 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center font-bold flex-shrink-0">2</div>
                <p class="text-gray-700 text-sm">Aktifkan Bluetooth di komputer/laptop Anda dan pastikan menggunakan browser Chrome/Edge</p>
            </div>
            <div class="flex items-start space-x-3">
                <div class="w-8 h-8 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center font-bold flex-shrink-0">3</div>
                <p class="text-gray-700 text-sm">Klik tombol "Scan" untuk mencari printer Bluetooth yang tersedia</p>
            </div>
            <div class="flex items-start space-x-3">
                <div class="w-8 h-8 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center font-bold flex-shrink-0">4</div>
                <p class="text-gray-700 text-sm">Pilih printer RPP02N/ALGOO dari popup Bluetooth browser</p>
            </div>
            <div class="flex items-start space-x-3">
                <div class="w-8 h-8 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center font-bold flex-shrink-0">5</div>
                <p class="text-gray-700 text-sm">Klik "Test Print" untuk memastikan printer berfungsi dengan baik</p>
            </div>
            <div class="flex items-start space-x-3">
                <div class="w-8 h-8 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center font-bold flex-shrink-0">6</div>
                <p class="text-gray-700 text-sm">Klik "Simpan Pengaturan" untuk menyimpan konfigurasi ke database</p>
            </div>
            <div class="flex items-start space-x-3">
                <div class="w-8 h-8 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center font-bold flex-shrink-0">7</div>
                <p class="text-gray-700 text-sm">Aktifkan "Auto Print" agar struk otomatis tercetak setelah transaksi selesai</p>
            </div>
            <div class="flex items-start space-x-3">
                <div class="w-8 h-8 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center font-bold flex-shrink-0">⚠️</div>
                <p class="text-gray-700 text-sm"><strong>Penting:</strong> Setelah simpan pengaturan, klik tombol "Reconnect" untuk menyambungkan ulang printer sebelum test print</p>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@once
<script src="{{ asset('js/printer-helper.js') }}"></script>
@endonce
<script>
// ============================================
// GLOBAL VARIABLES
// ============================================
let autoPrintEnabled = {{ ($settings && $settings->auto_print) ? 'true' : 'false' }};
let isReconnecting = false;

// ============================================
// DOCUMENT READY
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Settings page loaded');
    
    // Setup status callback untuk real-time updates
    if (window.PrinterHelper) {
        window.PrinterHelper.onStatusChange = handlePrinterStatusChange;
    }
    
    loadSettings();
});

// ============================================
// LOAD SETTINGS
// ============================================
async function loadSettings() {
    const printerName = '{{ $settings ? $settings->printer_name : "" }}';
    
    if (printerName) {
        // ✅ UBAH: Update UI dengan pesan yang lebih jelas
        const statusText = document.getElementById('printerStatusText');
        const statusBadge = document.getElementById('printerStatusBadge');
        
        if (statusText) {
            statusText.textContent = printerName + ' (klik Reconnect)';
        }
        
        if (statusBadge) {
            statusBadge.className = 'printer-status disconnected';
            statusBadge.innerHTML = '<i class="fas fa-circle mr-2 text-xs"></i>Offline';
        }
        
        // Sync to localStorage
        localStorage.setItem('thermal_printer_info', JSON.stringify({
            name: printerName,
            id: '',
            timestamp: new Date().toISOString()
        }));
        
        // Show reconnect button
        const reconnectBtn = document.getElementById('reconnectBtn');
        if (reconnectBtn) {
            reconnectBtn.style.display = 'block';
        }
    }
    
    // Sync settings to localStorage
    const settings = {
        autoPrint: autoPrintEnabled,
        selectedPrinter: printerName,
        paperWidth: {{ $settings ? $settings->paper_width : 58 }},
        fontSize: '{{ $settings ? $settings->font_size : "medium" }}'
    };
    localStorage.setItem('printerSettings', JSON.stringify(settings));
    
    console.log('📥 Settings loaded:', settings);
}

// ============================================
// HANDLE PRINTER STATUS CHANGE (CALLBACK)
// ============================================
function handlePrinterStatusChange(status, message) {
    console.log('🔔 Status change:', status, message);
    
    switch(status) {
        case 'scanning':
            updatePrinterStatus('pairing', 'Mencari printer...');
            break;
            
        case 'paired':
        case 'connected':
            updatePrinterStatus('connected', message || 'Terhubung');
            break;
            
        case 'disconnected':
            if (!isReconnecting) {
                updatePrinterStatus('disconnected', 'Terputus');
            }
            break;
            
        case 'error':
            updatePrinterStatus('disconnected', 'Error: ' + message);
            break;
            
        case 'printing':
            showToast('info', 'Sedang mencetak...');
            break;
            
        case 'printed':
            showToast('success', 'Struk berhasil dicetak!');
            break;
    }
}

// ============================================
// TOGGLE AUTO PRINT
// ============================================
function toggleAutoPrint() {
    autoPrintEnabled = !autoPrintEnabled;
    
    const toggle = document.getElementById('autoPrintToggle');
    const input = document.getElementById('autoPrintInput');
    
    if (toggle) {
        toggle.classList.toggle('active', autoPrintEnabled);
    }
    
    if (input) {
        input.value = autoPrintEnabled ? '1' : '0';
    }
    
    // Update localStorage
    const settings = JSON.parse(localStorage.getItem('printerSettings') || '{}');
    settings.autoPrint = autoPrintEnabled;
    localStorage.setItem('printerSettings', JSON.stringify(settings));
    
    console.log('🔄 Auto Print toggled:', autoPrintEnabled);
    
    showToast('info', `Auto Print ${autoPrintEnabled ? 'diaktifkan' : 'dinonaktifkan'}`);
}

// ============================================
// RECONNECT PRINTER (NEW FUNCTION)
// ============================================
async function reconnectPrinter() {
    const btn = document.getElementById('reconnectBtn');
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Reconnecting...';
    btn.disabled = true;
    isReconnecting = true;

    try {
        console.log('🔄 Starting reconnect...');
        
        if (!window.PrinterHelper) {
            throw new Error('Printer Helper belum dimuat. Refresh halaman dan coba lagi.');
        }
        
        // Get saved printer name
        const printerName = document.getElementById('printerName').value;
        if (!printerName) {
            throw new Error('Tidak ada printer yang tersimpan');
        }
        
        updatePrinterStatus('pairing', 'Menyambungkan ke ' + printerName + '...');
        
        // ✅ PANGGIL SCAN & PAIR UNTUK RECONNECT
        const result = await window.PrinterHelper.scanAndPair();
        
        console.log('✅ Reconnect result:', result);
        
        // Verify it's the same printer
        if (result.name !== printerName) {
            Swal.fire({
                icon: 'warning',
                title: 'Printer Berbeda',
                html: `Anda memilih <strong>${result.name}</strong> tapi yang tersimpan adalah <strong>${printerName}</strong>.<br><br>Update nama printer dan simpan pengaturan?`,
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Update',
                cancelButtonText: 'Batal'
            }).then((dialogResult) => {
                if (dialogResult.isConfirmed) {
                    document.getElementById('printerName').value = result.name;
                }
            });
        }
        
        updatePrinterStatus('connected', result.name);
        
        showToast('success', 'Printer berhasil tersambung! Silakan test print.');

    } catch (error) {
        console.error('❌ Reconnect error:', error);
        
        updatePrinterStatus('disconnected', 'Gagal reconnect');
        
        if (error.name === 'NotFoundError') {
            showToast('info', 'Koneksi dibatalkan');
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal Reconnect',
                html: `<div class="text-left"><strong>Error:</strong><br>${error.message}<br><br>Pastikan printer sudah ON dan Bluetooth aktif.</div>`,
                confirmButtonColor: '#10b981'
            });
        }
    } finally {
        btn.innerHTML = originalHtml;
        btn.disabled = false;
        isReconnecting = false;
    }
}

// ============================================
// SCAN PRINTERS (BLUETOOTH)
// ============================================
async function scanPrinters() {
    const btn = document.getElementById('scanBtn');
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Pairing...';
    btn.disabled = true;

    try {
        console.log('🔍 Starting scan...');
        
        if (!window.PrinterHelper) {
            throw new Error('Printer Helper belum dimuat. Refresh halaman dan coba lagi.');
        }
        
        const result = await window.PrinterHelper.scanAndPair();

        console.log('✅ Pair result:', result);

        // Update input field
        document.getElementById('printerName').value = result.name;

        // Update status display
        updatePrinterStatus('connected', result.name);
        
        // Show reconnect button
        const reconnectBtn = document.getElementById('reconnectBtn');
        if (reconnectBtn) {
            reconnectBtn.style.display = 'block';
        }

        // Success alert
        Swal.fire({
            icon: 'success',
            title: 'Printer Berhasil Di-Pair!',
            html: `<strong>${result.name}</strong> siap untuk auto-print.<br><br>Klik <strong>"Test Print"</strong> untuk test atau <strong>"Simpan Pengaturan"</strong> untuk menyimpan ke database.`,
            confirmButtonColor: '#10b981',
            confirmButtonText: 'OK'
        });

    } catch (error) {
        console.error('❌ Scan error:', error);
        
        updatePrinterStatus('disconnected');
        
        if (error.name === 'NotFoundError') {
            Swal.fire({
                icon: 'info',
                title: 'Koneksi Dibatalkan',
                text: 'Tidak ada printer yang dipilih',
                confirmButtonColor: '#10b981'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal Pairing',
                html: `<div class="text-left"><strong>Error:</strong><br>${error.message}</div>`,
                confirmButtonColor: '#10b981'
            });
        }
    } finally {
        btn.innerHTML = originalHtml;
        btn.disabled = false;
    }
}

// ============================================
// TEST PRINT
// ============================================
async function testPrint() {
    const btn = document.getElementById('testPrintBtn');
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Printing...';
    btn.disabled = true;

    try {
        console.log('🖨️ Starting test print...');
        
        if (!window.PrinterHelper) {
            throw new Error('Printer Helper belum dimuat. Refresh halaman dan coba lagi.');
        }
        
        const result = await window.PrinterHelper.testPrint();

        console.log('✅ Test print result:', result);

        Swal.fire({
            icon: 'success',
            title: 'Print Berhasil!',
            text: 'Cek output di printer Anda',
            confirmButtonColor: '#10b981',
            timer: 3000,
            showConfirmButton: false
        });

    } catch (error) {
        console.error('❌ Test print error:', error);
        
        let errorMsg = error.message;
        let suggestion = '';
        
        if (error.message.includes('belum di-pair')) {
            suggestion = '<br><br>Klik tombol <strong>"Reconnect"</strong> atau <strong>"Scan"</strong> untuk pair printer terlebih dahulu.';
        } else if (error.message.includes('GATT') || error.message.includes('disconnected')) {
            suggestion = '<br><br>Printer terputus. Klik <strong>"Reconnect"</strong> untuk koneksi ulang.';
            updatePrinterStatus('disconnected', 'Terputus - klik Reconnect');
        }
        
        Swal.fire({
            icon: 'error',
            title: 'Gagal Print',
            html: `<div class="text-left">${errorMsg}${suggestion}</div>`,
            confirmButtonColor: '#10b981'
        });
    } finally {
        btn.innerHTML = originalHtml;
        btn.disabled = false;
    }
}

// ============================================
// UPDATE PRINTER STATUS UI
// ============================================
function updatePrinterStatus(status, message = '') {
    const statusText = document.getElementById('printerStatusText');
    const statusBadge = document.getElementById('printerStatusBadge');
    
    let badgeClass, badgeText, statusMessage;
    
    switch(status) {
        case 'connected':
            badgeClass = 'printer-status connected';
            badgeText = '<i class="fas fa-circle mr-2 text-xs"></i>Online';
            statusMessage = message || 'Terhubung';
            break;
            
        case 'pairing':
            badgeClass = 'printer-status pairing';
            badgeText = '<i class="fas fa-spinner fa-spin mr-2 text-xs"></i>Pairing';
            statusMessage = message || 'Mencari printer...';
            break;
            
        case 'disconnected':
        default:
            badgeClass = 'printer-status disconnected';
            badgeText = '<i class="fas fa-circle mr-2 text-xs"></i>Offline';
            statusMessage = message || 'Tidak Terhubung';
            break;
    }
    
    if (statusText) {
        statusText.textContent = statusMessage;
    }
    
    if (statusBadge) {
        statusBadge.className = badgeClass;
        statusBadge.innerHTML = badgeText;
    }
}

// ============================================
// SHOW TOAST (SIMPLE NOTIFICATION)
// ============================================
function showToast(type, message) {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });
    
    Toast.fire({
        icon: type,
        title: message
    });
}

// ============================================
// FORM SUBMIT HANDLER
// ============================================
document.getElementById('settingsForm').addEventListener('submit', function(e) {
    const printerName = document.getElementById('printerName').value.trim();
    
    if (!printerName) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Nama Printer Kosong',
            text: 'Silakan klik "Scan" untuk pair printer terlebih dahulu',
            confirmButtonColor: '#10b981'
        });
        return false;
    }
    
    console.log('📝 Submitting form with data:', {
        printerName: printerName,
        autoPrint: document.getElementById('autoPrintInput').value,
        paperWidth: document.getElementById('paperWidth').value,
        fontSize: document.getElementById('fontSize').value
    });
    
    // Show info that reconnect is needed after save
    showToast('info', 'Setelah simpan, klik "Reconnect" untuk menyambungkan printer');
});
</script>
@endpush