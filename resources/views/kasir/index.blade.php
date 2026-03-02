@extends('layouts.app')

@section('title', 'Buka/Tutup Kasir - WPOS')
@section('page-title', 'Buka/Tutup Kasir')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="space-y-4 sm:space-y-6">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center">
                <div
                    class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center mr-3 shadow-lg">
                    <i class="fas fa-cash-register text-white text-lg sm:text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Buka/Tutup Kasir</h2>
                    <p class="text-xs sm:text-sm text-gray-500">Kelola sesi kasir harian</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @if (auth()->user()->role_user === 'owner' && $kasirAktif)
                    <button id="btnAutoCloseAll"
                        class="px-3 sm:px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white text-xs sm:text-sm font-semibold rounded-xl shadow-md hover:shadow-lg transition-all transform hover:scale-[1.02] active:scale-[0.98]">
                        <i class="fas fa-robot mr-1.5"></i>Tutup Semua Otomatis
                    </button>
                @endif
                <div
                    class="px-3 sm:px-4 py-2 bg-gradient-to-r from-emerald-50 to-green-50 rounded-xl border border-emerald-200 shadow-sm">
                    <i class="fas fa-calendar-day text-emerald-600 mr-2 text-sm"></i>
                    <span class="text-xs sm:text-sm font-semibold text-gray-700">
                        {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Main Grid — pakai items-stretch agar kedua card sama tinggi -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 items-stretch">

            <!-- Status Kasir Card -->
            <div
                class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 hover:shadow-xl transition-shadow flex flex-col">
                <div class="flex items-center mb-4 sm:mb-6">
                    <div
                        class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-3 shadow-lg flex-shrink-0">
                        <i class="fas fa-info-circle text-white text-lg sm:text-xl"></i>
                    </div>
                    <h2 class="text-lg sm:text-xl font-bold text-gray-800">Status Kasir</h2>
                </div>

                @if ($kasirAktif)
                    @php
                        $totalPenjualanAktif = DB::table('penjualan')
                            ->where('id_kasir', $kasirAktif->id_kasir)
                            ->whereNull('deleted_at')
                            ->sum('total_pembayaran');
                        $saldoEstimasi = $kasirAktif->modal_awal + $totalPenjualanAktif;
                        $waktuOpenJkt = $kasirAktif->waktu_open->setTimezone('Asia/Jakarta');
                    @endphp

                    <div
                        class="bg-gradient-to-br from-emerald-50 to-green-50 border-2 border-emerald-200 rounded-xl p-4 sm:p-5 mb-4 relative overflow-hidden flex-1">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-200 rounded-full -mr-12 -mt-12 opacity-30">
                        </div>
                        <div class="relative z-10 h-full flex flex-col">
                            <div class="flex items-center mb-4">
                                <div
                                    class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center mr-3 shadow-lg flex-shrink-0">
                                    <i class="fas fa-check-circle text-white text-lg sm:text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-emerald-800 text-base sm:text-lg">Kasir Aktif</h3>
                                    <p class="text-xs sm:text-sm text-emerald-600">Sesi kasir sedang berjalan</p>
                                </div>
                            </div>
                            <div class="space-y-2.5 flex-1">
                                <div class="flex items-center justify-between bg-white rounded-lg p-3 shadow-sm">
                                    <div class="flex items-center">
                                        <i class="fas fa-wallet text-emerald-600 mr-2 text-sm"></i>
                                        <span class="text-sm text-gray-600 font-medium">Modal Awal</span>
                                    </div>
                                    <span class="font-bold text-gray-800 text-sm">Rp
                                        {{ number_format($kasirAktif->modal_awal, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex items-center justify-between bg-white rounded-lg p-3 shadow-sm">
                                    <div class="flex items-center">
                                        <i class="fas fa-clock text-blue-600 mr-2 text-sm"></i>
                                        <span class="text-sm text-gray-600 font-medium">Waktu Buka</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-semibold text-gray-800 text-sm">{{ $waktuOpenJkt->format('H:i') }}
                                            WIB</span>
                                        <span class="text-gray-400 text-xs mx-1">→</span>
                                        <span id="jamSekarang" class="font-semibold text-blue-600 text-sm"></span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between bg-white rounded-lg p-3 shadow-sm">
                                    <div class="flex items-center">
                                        <i class="fas fa-hourglass-half text-orange-600 mr-2 text-sm"></i>
                                        <span class="text-sm text-gray-600 font-medium">Durasi</span>
                                    </div>
                                    <span id="durasiKasir"
                                        class="font-semibold text-orange-600 text-sm">Menghitung...</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 mb-3 flex items-start gap-2">
                        <i class="fas fa-robot text-blue-500 mt-0.5 flex-shrink-0 text-sm"></i>
                        <p class="text-xs text-blue-700">
                            Saldo akhir dihitung <strong>otomatis</strong>: Modal Awal + Total Penjualan.
                            Tidak perlu input manual.
                        </p>
                    </div>

                    <button id="btnTutupKasir" data-kasir-id="{{ $kasirAktif->id_kasir }}"
                        data-modal-awal="{{ $kasirAktif->modal_awal }}" data-total-penjualan="{{ $totalPenjualanAktif }}"
                        data-saldo-estimasi="{{ $saldoEstimasi }}"
                        class="w-full bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white font-bold py-3.5 rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:scale-[1.02] active:scale-[0.98] text-sm sm:text-base mt-auto">
                        <i class="fas fa-lock mr-2"></i>Tutup Kasir
                    </button>
                @else
                    <div
                        class="bg-gradient-to-br from-amber-50 to-orange-50 border-2 border-amber-200 rounded-xl p-4 sm:p-5 mb-4 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-amber-200 rounded-full -mr-12 -mt-12 opacity-30">
                        </div>
                        <div class="relative z-10 flex items-center">
                            <div
                                class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center mr-3 shadow-lg flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-white text-lg sm:text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-amber-800 text-base sm:text-lg">Kasir Belum Dibuka</h3>
                                <p class="text-xs sm:text-sm text-amber-600">Buka kasir untuk memulai transaksi</p>
                            </div>
                        </div>
                    </div>

                    @php
                        $settingJam = DB::table('settings')->first();
                        $jamBukaSetting =
                            $settingJam && !empty($settingJam->open_time) ? substr($settingJam->open_time, 0, 5) : null;
                        $jamTutupSetting =
                            $settingJam && !empty($settingJam->auto_close_time)
                                ? substr($settingJam->auto_close_time, 0, 5)
                                : null;
                        $jamSekarangBlade = \Carbon\Carbon::now('Asia/Jakarta')->format('H:i');
                        $kasirBolehBuka =
                            (!$jamBukaSetting || $jamSekarangBlade >= $jamBukaSetting) &&
                            (!$jamTutupSetting ||
                                !$settingJam->auto_close_kasir ||
                                $jamSekarangBlade < $jamTutupSetting);
                    @endphp

                    @if ($jamBukaSetting || $jamTutupSetting)
                        <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-3 mb-4 flex items-start gap-2">
                            <i class="fas fa-clock text-indigo-500 mt-0.5 flex-shrink-0 text-sm"></i>
                            <div class="text-xs text-indigo-700">
                                <p class="font-semibold mb-1">Jam Operasional Kasir</p>
                                <div class="flex gap-4">
                                    @if ($jamBukaSetting)
                                        <span><i class="fas fa-door-open mr-1 text-emerald-500"></i>Buka:
                                            <strong>{{ $jamBukaSetting }} WIB</strong></span>
                                    @endif
                                    @if ($jamTutupSetting && $settingJam->auto_close_kasir)
                                        <span><i class="fas fa-door-closed mr-1 text-red-500"></i>Tutup:
                                            <strong>{{ $jamTutupSetting }} WIB</strong></span>
                                    @endif
                                </div>
                                @if (!$kasirBolehBuka)
                                    <p class="mt-1.5 text-amber-600 font-medium">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        @if ($jamBukaSetting && $jamSekarangBlade < $jamBukaSetting)
                                            Kasir baru bisa dibuka pukul {{ $jamBukaSetting }} WIB
                                        @else
                                            Kasir sudah melewati jam operasional hari ini
                                        @endif
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif

                    <form id="formBukaKasir" class="flex flex-col flex-1">
                        @csrf
                        <input type="hidden" id="modal_awal" name="modal_awal" value="">

                        <div class="mb-4 sm:mb-5 flex-1">
                            <label class="flex items-center text-gray-700 font-semibold mb-2 text-sm">
                                <div
                                    class="w-7 h-7 sm:w-8 sm:h-8 bg-gradient-to-br from-emerald-500 to-green-600 rounded-lg flex items-center justify-center mr-2 shadow-md flex-shrink-0">
                                    <i class="fas fa-money-bill-wave text-white text-xs"></i>
                                </div>
                                Modal Awal <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <span
                                    class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-sm">Rp</span>
                                <input type="text" id="modal_awal_display" inputmode="numeric" autocomplete="off"
                                    class="w-full pl-12 pr-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:outline-none transition-all text-gray-800 placeholder-gray-400 text-sm"
                                    placeholder="500.000">
                            </div>
                            <p class="text-gray-500 text-xs mt-2 flex items-start gap-1">
                                <i class="fas fa-info-circle text-blue-500 mt-0.5 flex-shrink-0"></i>
                                Masukkan jumlah uang di laci kasir saat ini — titik muncul otomatis
                            </p>
                        </div>

                        <button type="submit"
                            class="w-full bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white font-bold py-3.5 rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:scale-[1.02] active:scale-[0.98] text-sm sm:text-base mt-auto {{ !$kasirBolehBuka ? 'opacity-50 cursor-not-allowed' : '' }}"
                            {{ !$kasirBolehBuka ? 'disabled' : '' }}>
                            <i class="fas fa-unlock-alt mr-2"></i>Buka Kasir Sekarang
                        </button>
                    </form>
                @endif
            </div>

            <!-- ═══════════════════════════════════════════════════════ -->
            <!-- Statistik Hari Ini — card full height, cards stretch   -->
            <!-- ═══════════════════════════════════════════════════════ -->
            <div
                class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 hover:shadow-xl transition-shadow flex flex-col">
                <div class="flex items-center mb-4 sm:mb-6">
                    <div
                        class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center mr-3 shadow-lg flex-shrink-0">
                        <i class="fas fa-chart-line text-white text-lg sm:text-xl"></i>
                    </div>
                    <h2 class="text-lg sm:text-xl font-bold text-gray-800">Statistik Hari Ini</h2>
                </div>

                @if ($kasirAktif)
                    @php
                        $transaksiHariIni = DB::table('penjualan')
                            ->where('id_kasir', $kasirAktif->id_kasir)
                            ->whereNull('deleted_at')
                            ->count();
                    @endphp
                    {{-- flex-1 + flex flex-col + grid rows agar 3 card membagi ruang secara merata --}}
                    <div class="flex-1 grid grid-rows-3 gap-3">

                        <!-- Total Transaksi -->
                        <div
                            class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-200 hover:shadow-md transition-shadow flex items-center">
                            <div
                                class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-3 shadow-lg flex-shrink-0">
                                <i class="fas fa-shopping-cart text-white text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs sm:text-sm text-gray-600 font-medium">Total Transaksi</p>
                                <p class="text-2xl sm:text-3xl font-bold text-gray-800 mt-1">{{ $transaksiHariIni }}</p>
                            </div>
                        </div>

                        <!-- Total Penjualan -->
                        <div
                            class="bg-gradient-to-br from-emerald-50 to-green-50 rounded-xl p-4 border border-emerald-200 hover:shadow-md transition-shadow flex items-center">
                            <div
                                class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center mr-3 shadow-lg flex-shrink-0">
                                <i class="fas fa-money-bill-wave text-white text-xl"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs sm:text-sm text-gray-600 font-medium">Total Penjualan</p>
                                <p class="text-lg sm:text-xl font-bold text-gray-800 mt-1 break-words">Rp
                                    {{ number_format($totalPenjualanAktif, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <!-- Estimasi Saldo Akhir -->
                        <div
                            class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-4 border border-purple-200 hover:shadow-md transition-shadow flex items-center">
                            <div
                                class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center mr-3 shadow-lg flex-shrink-0">
                                <i class="fas fa-wallet text-white text-xl"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs sm:text-sm text-gray-600 font-medium flex items-center gap-1 flex-wrap">
                                    Estimasi Saldo Akhir
                                    <span
                                        class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-700">
                                        <i class="fas fa-robot mr-1 text-xs"></i>Otomatis
                                    </span>
                                </p>
                                <p class="text-lg sm:text-xl font-bold text-gray-800 mt-1 break-words">Rp
                                    {{ number_format($saldoEstimasi, 0, ',', '.') }}</p>
                            </div>
                        </div>

                    </div>
                @else
                    <div class="flex-1 flex flex-col items-center justify-center text-center py-10 sm:py-12">
                        <div
                            class="w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-chart-line text-4xl sm:text-5xl text-gray-400"></i>
                        </div>
                        <p class="text-gray-500 font-medium text-sm sm:text-base">Buka kasir untuk melihat statistik</p>
                        <p class="text-gray-400 text-xs sm:text-sm mt-1">Data akan ditampilkan setelah kasir dibuka</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- ════════════════════════════════════════════════════════ -->
        <!-- PENGATURAN AUTO-CLOSE KASIR (Khusus Owner)              -->
        <!-- ════════════════════════════════════════════════════════ -->
        @if (auth()->user()->role_user === 'owner')
            @php
                $setting = DB::table('settings')->first();
                $autoCloseAktif = $setting && isset($setting->auto_close_kasir) && $setting->auto_close_kasir;
                $autoCloseTime = $setting && isset($setting->auto_close_time) ? $setting->auto_close_time : '23:59';
                $openTime = $setting && isset($setting->open_time) ? $setting->open_time : '08:00';
            @endphp
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 hover:shadow-xl transition-shadow">
                <div class="flex items-center mb-4 sm:mb-6">
                    <div
                        class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center mr-3 shadow-lg flex-shrink-0">
                        <i class="fas fa-robot text-white text-lg sm:text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-lg sm:text-xl font-bold text-gray-800">Pengaturan Jam Operasional Kasir</h2>
                        <p class="text-xs sm:text-sm text-gray-500">Atur jam buka dan tutup otomatis kasir setiap hari</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Kiri: Form Setting --}}
                    <div>
                        <form id="formAutoClose">
                            @csrf

                            <div
                                class="flex items-center justify-between bg-gray-50 rounded-xl p-4 mb-4 border border-gray-200">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-power-off text-indigo-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">Aktifkan Auto-Close</p>
                                        <p class="text-xs text-gray-500">Tutup kasir otomatis setiap hari</p>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 ml-3">
                                    <input type="checkbox" id="auto_close_kasir" name="auto_close_kasir" value="1"
                                        class="sr-only peer" {{ $autoCloseAktif ? 'checked' : '' }}>
                                    <div
                                        class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300
                                        rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white
                                        after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white
                                        after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all
                                        peer-checked:bg-indigo-600">
                                    </div>
                                </label>
                            </div>

                            <div class="mb-4">
                                <label class="flex items-center text-gray-700 font-semibold mb-2 text-sm">
                                    <div
                                        class="w-7 h-7 sm:w-8 sm:h-8 bg-gradient-to-br from-emerald-500 to-green-600 rounded-lg flex items-center justify-center mr-2 shadow-md flex-shrink-0">
                                        <i class="fas fa-door-open text-white text-xs"></i>
                                    </div>
                                    Jam Buka Kasir
                                </label>
                                <input type="time" id="open_time" name="open_time" value="{{ $openTime }}"
                                    class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:outline-none transition-all text-gray-800 text-sm font-semibold">
                                <p class="text-gray-500 text-xs mt-2 flex items-start gap-1">
                                    <i class="fas fa-info-circle text-emerald-500 mt-0.5 flex-shrink-0"></i>
                                    Kasir tidak bisa dibuka sebelum jam ini setiap harinya
                                </p>
                            </div>

                            <div id="wrapperWaktu"
                                class="transition-all {{ $autoCloseAktif ? '' : 'opacity-50 pointer-events-none' }}">
                                <label class="flex items-center text-gray-700 font-semibold mb-2 text-sm">
                                    <div
                                        class="w-7 h-7 sm:w-8 sm:h-8 bg-gradient-to-br from-red-500 to-pink-600 rounded-lg flex items-center justify-center mr-2 shadow-md flex-shrink-0">
                                        <i class="fas fa-door-closed text-white text-xs"></i>
                                    </div>
                                    Jam Tutup Otomatis
                                </label>
                                <input type="time" id="auto_close_time" name="auto_close_time"
                                    value="{{ $autoCloseTime }}"
                                    class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:bg-white focus:border-red-400 focus:outline-none transition-all text-gray-800 text-sm font-semibold">
                                <p class="text-gray-500 text-xs mt-2 flex items-start gap-1">
                                    <i class="fas fa-info-circle text-red-500 mt-0.5 flex-shrink-0"></i>
                                    Kasir aktif akan ditutup otomatis setiap hari pada jam ini
                                </p>
                            </div>

                            <button type="submit"
                                class="mt-4 w-full bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-bold py-3 rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:scale-[1.02] active:scale-[0.98] text-sm">
                                <i class="fas fa-save mr-2"></i>Simpan Pengaturan
                            </button>
                        </form>
                    </div>

                    {{-- Kanan: Info Status --}}
                    <div class="flex flex-col gap-3">
                        <div
                            class="flex-1 bg-gradient-to-br from-emerald-50 to-indigo-50 border border-indigo-200 rounded-xl p-4">
                            <p class="text-xs font-semibold text-gray-700 mb-3 flex items-center gap-1.5">
                                <i class="fas fa-clock text-indigo-500"></i>
                                Jam Operasional Kasir
                            </p>
                            <div class="flex items-center justify-between gap-2">
                                <div
                                    class="flex-1 bg-white rounded-xl p-3 border border-emerald-200 text-center shadow-sm">
                                    <div
                                        class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center mx-auto mb-1.5">
                                        <i class="fas fa-door-open text-emerald-600 text-sm"></i>
                                    </div>
                                    <p class="text-xs text-gray-500 mb-0.5">Jam Buka</p>
                                    <p class="text-base font-bold text-emerald-700">{{ $openTime }} <span
                                            class="text-xs font-normal text-gray-400">WIB</span></p>
                                </div>
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-arrow-right text-gray-300 text-lg"></i>
                                </div>
                                <div
                                    class="flex-1 bg-white rounded-xl p-3 border {{ $autoCloseAktif ? 'border-red-200' : 'border-gray-200' }} text-center shadow-sm">
                                    <div
                                        class="w-8 h-8 {{ $autoCloseAktif ? 'bg-red-100' : 'bg-gray-100' }} rounded-lg flex items-center justify-center mx-auto mb-1.5">
                                        <i
                                            class="fas fa-door-closed {{ $autoCloseAktif ? 'text-red-500' : 'text-gray-400' }} text-sm"></i>
                                    </div>
                                    <p class="text-xs text-gray-500 mb-0.5">Jam Tutup</p>
                                    @if ($autoCloseAktif)
                                        <p class="text-base font-bold text-red-600">{{ $autoCloseTime }} <span
                                                class="text-xs font-normal text-gray-400">WIB</span></p>
                                    @else
                                        <p class="text-sm font-semibold text-gray-400">Manual</p>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-3 flex justify-center">
                                @if ($autoCloseAktif)
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700 border border-indigo-200">
                                        <i class="fas fa-robot mr-1.5 text-xs"></i>Auto-Close Aktif
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500 border border-gray-200">
                                        <i class="fas fa-hand-paper mr-1.5 text-xs"></i>Tutup Manual
                                    </span>
                                @endif
                            </div>
                        </div>

                        @php
                            $logAutoClose = DB::table('kasir')
                                ->where('is_auto_closed', true)
                                ->orderByDesc('waktu_close')
                                ->first();
                        @endphp

                        <!-- ══════════════════════════════════════════════ -->
                        <!-- Riwayat Auto-Close + JAM BUKA ditambahkan     -->
                        <!-- ══════════════════════════════════════════════ -->
                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                            <p class="text-xs font-semibold text-gray-700 mb-2 flex items-center gap-1.5">
                                <i class="fas fa-history text-gray-400"></i>
                                Riwayat Auto-Close Terakhir
                            </p>
                            @if ($logAutoClose)
                                @php
                                    $acClose = \Carbon\Carbon::parse($logAutoClose->waktu_close)->setTimezone(
                                        'Asia/Jakarta',
                                    );
                                    $acOpen = \Carbon\Carbon::parse($logAutoClose->waktu_open)->setTimezone(
                                        'Asia/Jakarta',
                                    );
                                @endphp
                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-500">Tanggal</span>
                                        <span
                                            class="text-xs font-semibold text-gray-700">{{ $acClose->format('d/m/Y') }}</span>
                                    </div>
                                    {{-- ✅ Baris Jam Buka yang ditambahkan --}}
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-500">Jam Buka</span>
                                        <span class="text-xs font-semibold text-emerald-600">
                                            <i class="fas fa-door-open mr-1 text-xs"></i>{{ $acOpen->format('H:i') }} WIB
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-500">Jam Tutup</span>
                                        <span class="text-xs font-semibold text-gray-700">
                                            <i
                                                class="fas fa-door-closed mr-1 text-xs text-red-400"></i>{{ $acClose->format('H:i') }}
                                            WIB
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between border-t border-gray-200 pt-1.5 mt-1">
                                        <span class="text-xs text-gray-500">Saldo Akhir</span>
                                        <span class="text-xs font-bold text-emerald-600">
                                            Rp {{ number_format($logAutoClose->saldo_akhir, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            @else
                                <p class="text-xs text-gray-400 italic">Belum ada riwayat auto-close</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Riwayat Kasir -->
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center mb-4 sm:mb-6">
                <div
                    class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-gray-600 to-gray-800 rounded-xl flex items-center justify-center mr-3 shadow-lg flex-shrink-0">
                    <i class="fas fa-history text-white text-lg sm:text-xl"></i>
                </div>
                <h2 class="text-lg sm:text-xl font-bold text-gray-800">Riwayat Kasir</h2>
            </div>

            <!-- Mobile: Card View -->
            <div class="block lg:hidden space-y-3">
                @forelse ($riwayatKasir as $index => $kasir)
                    @php
                        $waktuOpenCard = $kasir->waktu_open->setTimezone('Asia/Jakarta');
                        $waktuCloseCard = $kasir->waktu_close ? $kasir->waktu_close->setTimezone('Asia/Jakarta') : null;
                    @endphp
                    <div
                        class="bg-gradient-to-br from-gray-50 to-white border border-gray-200 rounded-xl p-4 hover:shadow-md transition-all">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <span
                                    class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-green-600 rounded-lg flex items-center justify-center text-white font-bold text-sm mr-2">
                                    {{ ($riwayatKasir->currentPage() - 1) * $riwayatKasir->perPage() + $index + 1 }}
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">{{ $waktuOpenCard->format('d/m/Y') }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $waktuOpenCard->format('H:i') }} -
                                        {{ $waktuCloseCard ? $waktuCloseCard->format('H:i') : 'Aktif' }}
                                        @if (!is_null($kasir->waktu_close) && $kasir->is_auto_closed)
                                            <span class="ml-1 text-blue-500"><i class="fas fa-robot"></i></span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @if (is_null($kasir->waktu_close))
                                <span
                                    class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-1.5 animate-pulse"></span>Aktif
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-700 border border-gray-300">
                                    <i class="fas fa-check-circle text-gray-500 mr-1 text-xs"></i>Tutup
                                </span>
                            @endif
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="bg-white rounded-lg p-2.5 border border-gray-200">
                                <p class="text-xs text-gray-500 mb-0.5">Modal</p>
                                <p class="text-sm font-bold text-gray-800">Rp
                                    {{ number_format($kasir->modal_awal, 0, ',', '.') }}</p>
                            </div>
                            <div class="bg-white rounded-lg p-2.5 border border-gray-200">
                                <p class="text-xs text-gray-500 mb-0.5">Saldo Akhir</p>
                                <p class="text-sm font-bold text-emerald-600">
                                    @if ($kasir->saldo_akhir)
                                        Rp {{ number_format($kasir->saldo_akhir, 0, ',', '.') }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10">
                        <div
                            class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-inbox text-3xl text-gray-400"></i>
                        </div>
                        <p class="text-gray-500 font-medium text-sm">Belum ada riwayat kasir</p>
                    </div>
                @endforelse
            </div>

            <!-- Desktop: Table View -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Buka
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tutup
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Modal
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Saldo
                                Akhir</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse ($riwayatKasir as $index => $kasir)
                            @php
                                $waktuOpenTbl = $kasir->waktu_open->setTimezone('Asia/Jakarta');
                                $waktuCloseTbl = $kasir->waktu_close
                                    ? $kasir->waktu_close->setTimezone('Asia/Jakarta')
                                    : null;
                            @endphp
                            <tr class="hover:bg-gradient-to-r hover:from-emerald-50 hover:to-green-50 transition-colors">
                                <td class="px-4 py-4 text-sm font-medium text-gray-800">
                                    {{ ($riwayatKasir->currentPage() - 1) * $riwayatKasir->perPage() + $index + 1 }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-700">
                                    <i class="fas fa-calendar text-gray-400 mr-2 text-xs"></i>
                                    {{ $waktuOpenTbl->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-700">
                                    <i class="fas fa-clock text-green-500 mr-2 text-xs"></i>
                                    {{ $waktuOpenTbl->format('H:i') }} WIB
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-700">
                                    @if ($waktuCloseTbl)
                                        <i class="fas fa-clock text-red-500 mr-2 text-xs"></i>
                                        {{ $waktuCloseTbl->format('H:i') }} WIB
                                        @if ($kasir->is_auto_closed)
                                            <span
                                                class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700">
                                                <i class="fas fa-robot mr-1 text-xs"></i>Auto
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-sm font-bold text-gray-800">
                                    Rp {{ number_format($kasir->modal_awal, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-4 text-sm font-bold text-emerald-600">
                                    @if ($kasir->saldo_akhir)
                                        Rp {{ number_format($kasir->saldo_akhir, 0, ',', '.') }}
                                    @else
                                        <span class="text-gray-400 font-normal">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if (is_null($kasir->waktu_close))
                                        <span
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200">
                                            <span
                                                class="w-2 h-2 bg-emerald-500 rounded-full mr-2 animate-pulse"></span>Aktif
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-full bg-gray-100 text-gray-700 border border-gray-300">
                                            <i class="fas fa-check-circle text-gray-500 mr-1.5 text-xs"></i>Ditutup
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mb-3">
                                            <i class="fas fa-inbox text-4xl text-gray-400"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium">Belum ada riwayat kasir</p>
                                        <p class="text-gray-400 text-sm mt-1">Riwayat akan muncul setelah kasir dibuka</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($riwayatKasir->hasPages())
                <div class="mt-4 sm:mt-6 border-t pt-4">
                    {{ $riwayatKasir->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const rupiah = (n) => parseFloat(n).toLocaleString('id-ID');
        const csrfToken = () => document.querySelector('meta[name="csrf-token"]').content;
        const swalClass = {
            popup: 'rounded-2xl',
            title: 'text-xl font-bold text-gray-800',
            confirmButton: 'px-6 py-3 rounded-xl font-semibold shadow-lg',
            cancelButton: 'px-6 py-3 rounded-xl font-semibold'
        };

        const modalDisplay = document.getElementById('modal_awal_display');
        const modalHidden = document.getElementById('modal_awal');

        if (modalDisplay && modalHidden) {
            modalDisplay.addEventListener('input', function() {
                const cursorPos = this.selectionStart;
                const oldLen = this.value.length;
                const angkaStr = this.value.replace(/[^\d]/g, '');
                if (angkaStr === '') {
                    this.value = '';
                    modalHidden.value = '';
                    return;
                }
                const angkaInt = parseInt(angkaStr);
                const formatted = angkaInt.toLocaleString('id-ID');
                this.value = formatted;
                modalHidden.value = angkaInt;
                const newLen = this.value.length;
                const newPos = Math.max(0, cursorPos + (newLen - oldLen));
                try {
                    this.setSelectionRange(newPos, newPos);
                } catch (e) {}
            });
            modalDisplay.addEventListener('keydown', function(e) {
                const allowed = ['Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown',
                    'Home', 'End'
                ];
                if (allowed.includes(e.key) || e.ctrlKey || e.metaKey) return;
                if (!/^\d$/.test(e.key)) e.preventDefault();
            });
            modalDisplay.addEventListener('blur', function() {
                const angka = this.value.replace(/[^\d]/g, '');
                modalHidden.value = angka ? parseInt(angka) : '';
            });
        }

        async function tutupKasirRequest(kasirId, isAuto = false) {
            const response = await fetch(`/kasir/${kasirId}/close`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken()
                },
                body: JSON.stringify({
                    is_auto: isAuto
                })
            });
            const data = await response.json();
            if (!response.ok || !data.success) throw new Error(data.message || 'Gagal menutup kasir');
            return data;
        }

        function ringkasanHTML(modalAwal, totalJual, saldoEstimasi) {
            return `<div class="bg-gray-50 rounded-xl p-4 mb-4 space-y-2 text-left text-sm">
                        <div class="flex justify-between"><span class="text-gray-600">Modal Awal:</span><span class="font-bold text-gray-800">Rp ${rupiah(modalAwal)}</span></div>
                        <div class="flex justify-between"><span class="text-gray-600">Total Penjualan:</span><span class="font-bold text-emerald-600">Rp ${rupiah(totalJual)}</span></div>
                        <div class="flex justify-between border-t pt-2"><span class="text-gray-700 font-semibold">Saldo Akhir (Otomatis):</span><span class="font-bold text-purple-600">Rp ${rupiah(saldoEstimasi)}</span></div>
                    </div>
                    <div class="flex items-start gap-2 bg-blue-50 rounded-xl p-3 text-left">
                        <i class="fas fa-robot text-blue-500 flex-shrink-0 mt-0.5 text-sm"></i>
                        <p class="text-xs text-blue-700">Saldo akhir dihitung otomatis. Pastikan semua transaksi sudah selesai.</p>
                    </div>`;
        }

        async function tampilHasilTutup(data) {
            await Swal.fire({
                icon: 'success',
                title: 'Kasir Ditutup!',
                html: `<div class="bg-gray-50 rounded-xl p-4 space-y-2 mb-3 text-left text-sm">
                            <div class="flex justify-between"><span class="text-gray-600">Modal Awal:</span><span class="font-bold">Rp ${rupiah(data.modal_awal)}</span></div>
                            <div class="flex justify-between"><span class="text-gray-600">Total Penjualan:</span><span class="font-bold text-emerald-600">Rp ${rupiah(data.total_penjualan)}</span></div>
                            <div class="flex justify-between border-t pt-2"><span class="text-gray-700 font-semibold">Saldo Akhir:</span><span class="font-bold text-purple-600">Rp ${rupiah(data.saldo_akhir)}</span></div>
                        </div>
                        <p class="text-gray-600 text-sm">${data.message}</p>`,
                confirmButtonColor: '#10b981',
                customClass: swalClass,
                buttonsStyling: false
            });
        }

        const formBukaKasir = document.getElementById('formBukaKasir');
        if (formBukaKasir) {
            formBukaKasir.addEventListener('submit', async function(e) {
                e.preventDefault();
                const modalAwal = modalHidden ? modalHidden.value : '';
                if (!modalAwal || parseInt(modalAwal) <= 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian!',
                        text: 'Masukkan modal awal yang valid',
                        confirmButtonColor: '#f59e0b',
                        customClass: swalClass,
                        buttonsStyling: false
                    });
                    if (modalDisplay) modalDisplay.focus();
                    return;
                }
                const confirm = await Swal.fire({
                    title: 'Buka Kasir?',
                    html: `<div class="bg-gray-50 rounded-xl p-4 mb-4 text-left text-sm"><div class="flex justify-between"><span class="text-gray-600">Modal Awal:</span><span class="font-bold text-emerald-600">Rp ${rupiah(modalAwal)}</span></div></div><p class="text-gray-600 text-sm">Pastikan modal awal sesuai uang di laci kasir</p>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '<i class="fas fa-unlock-alt mr-2"></i>Ya, Buka Kasir',
                    cancelButtonText: '<i class="fas fa-times mr-2"></i>Batal',
                    customClass: swalClass,
                    buttonsStyling: false
                });
                if (!confirm.isConfirmed) return;
                try {
                    const res = await fetch('{{ route('kasir.open') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken()
                        },
                        body: JSON.stringify({
                            modal_awal: modalAwal
                        })
                    });
                    const data = await res.json();
                    if (res.ok && data.success) {
                        await Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message || 'Kasir berhasil dibuka',
                            confirmButtonColor: '#10b981',
                            customClass: swalClass,
                            buttonsStyling: false
                        });
                        window.location.reload();
                    } else throw new Error(data.message || 'Gagal membuka kasir');
                } catch (err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: err.message,
                        confirmButtonColor: '#ef4444',
                        customClass: swalClass,
                        buttonsStyling: false
                    });
                }
            });
        }

        const btnTutupKasir = document.getElementById('btnTutupKasir');
        if (btnTutupKasir) {
            btnTutupKasir.addEventListener('click', async function() {
                const kasirId = this.dataset.kasirId;
                const modalAwal = parseFloat(this.dataset.modalAwal);
                const totalJual = parseFloat(this.dataset.totalPenjualan);
                const saldoEstimasi = parseFloat(this.dataset.saldoEstimasi);
                const confirm = await Swal.fire({
                    title: 'Tutup Kasir?',
                    html: ringkasanHTML(modalAwal, totalJual, saldoEstimasi),
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '<i class="fas fa-lock mr-2"></i>Ya, Tutup Kasir',
                    cancelButtonText: '<i class="fas fa-times mr-2"></i>Batal',
                    customClass: swalClass,
                    buttonsStyling: false
                });
                if (!confirm.isConfirmed) return;
                try {
                    const data = await tutupKasirRequest(kasirId, false);
                    await tampilHasilTutup(data);
                    window.location.reload();
                } catch (err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: err.message,
                        confirmButtonColor: '#ef4444',
                        customClass: swalClass,
                        buttonsStyling: false
                    });
                }
            });
        }

        const btnAutoCloseAll = document.getElementById('btnAutoCloseAll');
        if (btnAutoCloseAll) {
            btnAutoCloseAll.addEventListener('click', async function() {
                const confirm = await Swal.fire({
                    title: 'Tutup Semua Kasir Otomatis?',
                    html: `<div class="flex items-start gap-3 bg-amber-50 rounded-xl p-4 mb-4 text-left">
                                <i class="fas fa-exclamation-triangle text-amber-500 text-xl flex-shrink-0 mt-0.5"></i>
                                <div>
                                    <p class="text-sm font-semibold text-amber-800">Tindakan ini akan menutup semua sesi kasir aktif.</p>
                                    <p class="text-xs text-amber-600 mt-1">Saldo akhir tiap sesi dihitung otomatis: Modal Awal + Total Penjualan.</p>
                                </div>
                            </div>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3b82f6',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '<i class="fas fa-robot mr-2"></i>Ya, Tutup Semua',
                    cancelButtonText: '<i class="fas fa-times mr-2"></i>Batal',
                    customClass: swalClass,
                    buttonsStyling: false
                });
                if (!confirm.isConfirmed) return;
                try {
                    const res = await fetch('{{ route('kasir.auto-close-all') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken()
                        }
                    });
                    const data = await res.json();
                    if (res.ok && data.success) {
                        await Swal.fire({
                            icon: 'success',
                            title: 'Selesai!',
                            text: data.message,
                            confirmButtonColor: '#10b981',
                            customClass: swalClass,
                            buttonsStyling: false
                        });
                        window.location.reload();
                    } else throw new Error(data.message || 'Gagal menutup kasir');
                } catch (err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: err.message,
                        confirmButtonColor: '#ef4444',
                        customClass: swalClass,
                        buttonsStyling: false
                    });
                }
            });
        }

        const toggleAutoClose = document.getElementById('auto_close_kasir');
        const wrapperWaktu = document.getElementById('wrapperWaktu');
        const inputWaktu = document.getElementById('auto_close_time');
        const inputOpenTime = document.getElementById('open_time');

        if (toggleAutoClose) {
            toggleAutoClose.addEventListener('change', function() {
                if (this.checked) {
                    wrapperWaktu.classList.remove('opacity-50', 'pointer-events-none');
                } else {
                    wrapperWaktu.classList.add('opacity-50', 'pointer-events-none');
                }
            });
        }

        const formAutoClose = document.getElementById('formAutoClose');
        if (formAutoClose) {
            formAutoClose.addEventListener('submit', async function(e) {
                e.preventDefault();
                const openTimeVal = inputOpenTime ? inputOpenTime.value : '08:00';
                const closeTimeVal = inputWaktu ? inputWaktu.value : '23:59';
                if (toggleAutoClose.checked && openTimeVal >= closeTimeVal) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Jam Tidak Valid!',
                        text: 'Jam buka harus lebih awal dari jam tutup kasir.',
                        confirmButtonColor: '#f59e0b',
                        customClass: swalClass,
                        buttonsStyling: false
                    });
                    return;
                }
                const payload = {
                    auto_close_kasir: toggleAutoClose.checked ? 1 : 0,
                    auto_close_time: closeTimeVal,
                    open_time: openTimeVal
                };
                try {
                    const res = await fetch('{{ route('kasir.auto-close.setting') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken()
                        },
                        body: JSON.stringify(payload)
                    });
                    const data = await res.json();
                    if (res.ok && data.success) {
                        await Swal.fire({
                            icon: 'success',
                            title: 'Tersimpan!',
                            html: `<p class="text-gray-600 text-sm">${data.message}</p>
                                   <div class="mt-3 bg-indigo-50 rounded-xl p-3 text-sm font-semibold text-indigo-700">
                                       <div class="flex justify-between mb-1"><span><i class="fas fa-door-open mr-1 text-emerald-500"></i>Jam Buka</span><span>${payload.open_time} WIB</span></div>
                                       ${payload.auto_close_kasir
                                           ? `<div class="flex justify-between"><span><i class="fas fa-door-closed mr-1 text-red-400"></i>Jam Tutup Otomatis</span><span>${payload.auto_close_time} WIB</span></div>`
                                           : '<div class="text-center text-gray-400 text-xs">Auto-close nonaktif</div>'
                                       }
                                   </div>`,
                            confirmButtonColor: '#6366f1',
                            customClass: swalClass,
                            buttonsStyling: false
                        });
                        window.location.reload();
                    } else throw new Error(data.message || 'Gagal menyimpan');
                } catch (err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: err.message,
                        confirmButtonColor: '#ef4444',
                        customClass: swalClass,
                        buttonsStyling: false
                    });
                }
            });
        }

        @if ($kasirAktif)
            (function() {
                const waktuBuka = new Date({{ $kasirAktif->waktu_open->setTimezone('Asia/Jakarta')->timestamp }} *
                    1000);
                const elJam = document.getElementById('jamSekarang');
                const elDurasi = document.getElementById('durasiKasir');

                function pad(n) {
                    return String(n).padStart(2, '0');
                }

                function formatDurasi(totalDetik) {
                    const jam = Math.floor(totalDetik / 3600);
                    const menit = Math.floor((totalDetik % 3600) / 60);
                    const detik = totalDetik % 60;
                    if (jam > 0) return `${jam} jam ${pad(menit)} menit`;
                    if (menit > 0) return `${menit} menit ${pad(detik)} detik`;
                    return `${detik} detik`;
                }

                function tick() {
                    const sekarang = new Date();
                    const jamWIB = sekarang.toLocaleTimeString('id-ID', {
                        timeZone: 'Asia/Jakarta',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                        hour12: false
                    });
                    if (elJam) elJam.textContent = jamWIB + ' WIB';
                    const selisih = Math.floor((sekarang - waktuBuka) / 1000);
                    if (elDurasi && selisih >= 0) elDurasi.textContent = formatDurasi(selisih);
                }
                tick();
                setInterval(tick, 1000);
            })();
        @endif
    </script>
@endpush
