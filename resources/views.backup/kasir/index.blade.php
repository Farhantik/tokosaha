@extends('layouts.app')

@section('title', 'Manajemen Kasir - Toko Sahabat')

@section('page-title', 'Manajemen Kasir')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="space-y-4 sm:space-y-6">
        <!-- Header with Date Badge -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center mr-3 shadow-lg">
                    <i class="fas fa-cash-register text-white text-lg sm:text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Manajemen Kasir</h2>
                    <p class="text-xs sm:text-sm text-gray-500">Kelola sesi kasir harian</p>
                </div>
            </div>
            <div class="px-3 sm:px-4 py-2 bg-gradient-to-r from-emerald-50 to-green-50 rounded-xl border border-emerald-200 shadow-sm">
                <i class="fas fa-calendar-day text-emerald-600 mr-2 text-sm"></i>
                <span class="text-xs sm:text-sm font-semibold text-gray-700">{{ date('d F Y') }}</span>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
            <!-- Status Kasir Card -->
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 hover:shadow-xl transition-shadow">
                <div class="flex items-center mb-4 sm:mb-6">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-3 shadow-lg flex-shrink-0">
                        <i class="fas fa-info-circle text-white text-lg sm:text-xl"></i>
                    </div>
                    <h2 class="text-lg sm:text-xl font-bold text-gray-800">Status Kasir</h2>
                </div>

                @if ($kasirAktif)
                    <!-- Kasir Aktif Status -->
                    <div class="bg-gradient-to-br from-emerald-50 to-green-50 border-2 border-emerald-200 rounded-xl p-4 sm:p-5 mb-4 sm:mb-6 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-20 h-20 sm:w-24 sm:h-24 bg-emerald-200 rounded-full -mr-10 sm:-mr-12 -mt-10 sm:-mt-12 opacity-30"></div>
                        <div class="relative z-10">
                            <div class="flex items-center mb-3 sm:mb-4">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center mr-3 shadow-lg flex-shrink-0">
                                    <i class="fas fa-check-circle text-white text-lg sm:text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-emerald-800 text-base sm:text-lg">Kasir Aktif</h3>
                                    <p class="text-xs sm:text-sm text-emerald-600">Sesi kasir sedang berjalan</p>
                                </div>
                            </div>

                            <div class="space-y-2.5 sm:space-y-3">
                                <div class="flex items-center justify-between bg-white rounded-lg p-3 shadow-sm">
                                    <div class="flex items-center min-w-0 flex-1">
                                        <i class="fas fa-wallet text-emerald-600 mr-2 text-sm flex-shrink-0"></i>
                                        <span class="text-sm text-gray-600 font-medium">Modal Awal</span>
                                    </div>
                                    <span class="font-bold text-gray-800 text-sm ml-2 whitespace-nowrap">Rp {{ number_format($kasirAktif->modal_awal, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex items-center justify-between bg-white rounded-lg p-3 shadow-sm">
                                    <div class="flex items-center min-w-0 flex-1">
                                        <i class="fas fa-clock text-blue-600 mr-2 text-sm flex-shrink-0"></i>
                                        <span class="text-sm text-gray-600 font-medium">Waktu Buka</span>
                                    </div>
                                    <span class="font-semibold text-gray-800 text-sm ml-2 whitespace-nowrap">{{ $kasirAktif->waktu_open->format('H:i') }}</span>
                                </div>
                                <div class="flex items-center justify-between bg-white rounded-lg p-3 shadow-sm">
                                    <div class="flex items-center min-w-0 flex-1">
                                        <i class="fas fa-hourglass-half text-orange-600 mr-2 text-sm flex-shrink-0"></i>
                                        <span class="text-sm text-gray-600 font-medium">Durasi</span>
                                    </div>
                                    <span class="font-semibold text-gray-800 text-sm ml-2 whitespace-nowrap">{{ $kasirAktif->waktu_open->diffForHumans(null, true) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Close Button -->
                    <button id="btnTutupKasir" data-kasir-id="{{ $kasirAktif->id_kasir }}" class="w-full bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white font-bold py-3.5 rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:scale-[1.02] active:scale-[0.98] text-sm sm:text-base">
                        <i class="fas fa-lock mr-2"></i>Tutup Kasir
                    </button>
                @else
                    <!-- Kasir Belum Dibuka -->
                    <div class="bg-gradient-to-br from-amber-50 to-orange-50 border-2 border-amber-200 rounded-xl p-4 sm:p-5 mb-4 sm:mb-6 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-20 h-20 sm:w-24 sm:h-24 bg-amber-200 rounded-full -mr-10 sm:-mr-12 -mt-10 sm:-mt-12 opacity-30"></div>
                        <div class="relative z-10">
                            <div class="flex items-center">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center mr-3 shadow-lg flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-white text-lg sm:text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-amber-800 text-base sm:text-lg">Kasir Belum Dibuka</h3>
                                    <p class="text-xs sm:text-sm text-amber-600">Buka kasir untuk memulai transaksi</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Buka Kasir -->
                    <form id="formBukaKasir">
                        @csrf
                        <div class="mb-4 sm:mb-5">
                            <label class="flex items-center text-gray-700 font-semibold mb-2 text-sm">
                                <div class="w-7 h-7 sm:w-8 sm:h-8 bg-gradient-to-br from-emerald-500 to-green-600 rounded-lg flex items-center justify-center mr-2 shadow-md flex-shrink-0">
                                    <i class="fas fa-money-bill-wave text-white text-xs"></i>
                                </div>
                                <span>Modal Awal <span class="text-red-500">*</span></span>
                            </label>
                            <input type="number" name="modal_awal" id="modal_awal" required min="0" step="1000" class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:outline-none transition-all text-gray-800 placeholder-gray-400 text-sm" placeholder="Contoh: 500000">
                            <p class="text-gray-500 text-xs mt-2 flex items-start">
                                <i class="fas fa-info-circle text-blue-500 mr-1 mt-0.5 flex-shrink-0"></i>
                                <span>Masukkan jumlah uang di laci kasir saat ini</span>
                            </p>
                        </div>

                        <button type="submit" class="w-full bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white font-bold py-3.5 rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:scale-[1.02] active:scale-[0.98] text-sm sm:text-base">
                            <i class="fas fa-unlock-alt mr-2"></i>Buka Kasir Sekarang
                        </button>
                    </form>
                @endif
            </div>

            <!-- Statistik Hari Ini Card -->
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 hover:shadow-xl transition-shadow">
                <div class="flex items-center mb-4 sm:mb-6">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center mr-3 shadow-lg flex-shrink-0">
                        <i class="fas fa-chart-line text-white text-lg sm:text-xl"></i>
                    </div>
                    <h2 class="text-lg sm:text-xl font-bold text-gray-800">Statistik Hari Ini</h2>
                </div>

                @if ($kasirAktif)
                    @php
                        $transaksiHariIni = DB::table('penjualan')->where('id_kasir', $kasirAktif->id_kasir)->count();
                        $totalPenjualan = DB::table('penjualan')->where('id_kasir', $kasirAktif->id_kasir)->sum('total_pembayaran');
                        $saldoSaatIni = $kasirAktif->modal_awal + $totalPenjualan;
                    @endphp

                    <div class="space-y-3">
                        <!-- Transaksi -->
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-200 hover:shadow-md transition-shadow">
                            <div class="flex items-center">
                                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-3 shadow-lg flex-shrink-0">
                                    <i class="fas fa-shopping-cart text-white text-xl"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs sm:text-sm text-gray-600 font-medium">Total Transaksi</p>
                                    <p class="text-2xl sm:text-3xl font-bold text-gray-800 mt-1">{{ $transaksiHariIni }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Total Penjualan -->
                        <div class="bg-gradient-to-br from-emerald-50 to-green-50 rounded-xl p-4 border border-emerald-200 hover:shadow-md transition-shadow">
                            <div class="flex items-center">
                                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center mr-3 shadow-lg flex-shrink-0">
                                    <i class="fas fa-money-bill-wave text-white text-xl"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs sm:text-sm text-gray-600 font-medium">Total Penjualan</p>
                                    <p class="text-lg sm:text-xl font-bold text-gray-800 mt-1 break-words">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Saldo Saat Ini -->
                        <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-4 border border-purple-200 hover:shadow-md transition-shadow">
                            <div class="flex items-center">
                                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center mr-3 shadow-lg flex-shrink-0">
                                    <i class="fas fa-wallet text-white text-xl"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs sm:text-sm text-gray-600 font-medium">Saldo Kasir</p>
                                    <p class="text-lg sm:text-xl font-bold text-gray-800 mt-1 break-words">Rp {{ number_format($saldoSaatIni, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-10 sm:py-12">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-chart-line text-4xl sm:text-5xl text-gray-400"></i>
                        </div>
                        <p class="text-gray-500 font-medium text-sm sm:text-base">Buka kasir untuk melihat statistik</p>
                        <p class="text-gray-400 text-xs sm:text-sm mt-1">Data akan ditampilkan setelah kasir dibuka</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Riwayat Kasir -->
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center mb-4 sm:mb-6">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-gray-600 to-gray-800 rounded-xl flex items-center justify-center mr-3 shadow-lg flex-shrink-0">
                    <i class="fas fa-history text-white text-lg sm:text-xl"></i>
                </div>
                <h2 class="text-lg sm:text-xl font-bold text-gray-800">Riwayat Kasir</h2>
            </div>

            <!-- Mobile: Card View -->
            <div class="block lg:hidden space-y-3">
                @forelse ($riwayatKasir as $index => $kasir)
                    <div class="bg-gradient-to-br from-gray-50 to-white border border-gray-200 rounded-xl p-4 hover:shadow-md transition-all">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <span class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-green-600 rounded-lg flex items-center justify-center text-white font-bold text-sm mr-2">
                                    {{ ($riwayatKasir->currentPage() - 1) * $riwayatKasir->perPage() + $index + 1 }}
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">{{ $kasir->waktu_open->format('d/m/Y') }}</p>
                                    <p class="text-xs text-gray-500">{{ $kasir->waktu_open->format('H:i') }} - {{ $kasir->waktu_close ? $kasir->waktu_close->format('H:i') : 'Aktif' }}</p>
                                </div>
                            </div>
                            @if ($kasir->status === 'open')
                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-full bg-gradient-to-r from-emerald-100 to-green-100 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-1.5 animate-pulse"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-full bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 border border-gray-300">
                                    <i class="fas fa-check-circle text-gray-500 mr-1 text-xs"></i>
                                    Tutup
                                </span>
                            @endif
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="bg-white rounded-lg p-2.5 border border-gray-200">
                                <p class="text-xs text-gray-500 mb-0.5">Modal</p>
                                <p class="text-sm font-bold text-gray-800">Rp {{ number_format($kasir->modal_awal, 0, ',', '.') }}</p>
                            </div>
                            <div class="bg-white rounded-lg p-2.5 border border-gray-200">
                                <p class="text-xs text-gray-500 mb-0.5">Saldo</p>
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
                        <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-inbox text-3xl text-gray-400"></i>
                        </div>
                        <p class="text-gray-500 font-medium text-sm">Belum ada riwayat kasir</p>
                        <p class="text-gray-400 text-xs mt-1">Riwayat akan muncul setelah kasir dibuka</p>
                    </div>
                @endforelse
            </div>

            <!-- Desktop: Table View -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Buka</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tutup</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Modal</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Saldo</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse ($riwayatKasir as $index => $kasir)
                            <tr class="hover:bg-gradient-to-r hover:from-emerald-50 hover:to-green-50 transition-colors">
                                <td class="px-4 py-4 text-sm font-medium text-gray-800">{{ ($riwayatKasir->currentPage() - 1) * $riwayatKasir->perPage() + $index + 1 }}</td>
                                <td class="px-4 py-4 text-sm text-gray-700">
                                    <i class="fas fa-calendar text-gray-400 mr-2 text-xs"></i>
                                    {{ $kasir->waktu_open->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-700">
                                    <i class="fas fa-clock text-green-500 mr-2 text-xs"></i>
                                    {{ $kasir->waktu_open->format('H:i') }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-700">
                                    @if ($kasir->waktu_close)
                                        <i class="fas fa-clock text-red-500 mr-2 text-xs"></i>
                                        {{ $kasir->waktu_close->format('H:i') }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-sm font-bold text-gray-800">Rp {{ number_format($kasir->modal_awal, 0, ',', '.') }}</td>
                                <td class="px-4 py-4 text-sm font-bold text-emerald-600">
                                    @if ($kasir->saldo_akhir)
                                        Rp {{ number_format($kasir->saldo_akhir, 0, ',', '.') }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if ($kasir->status === 'open')
                                        <span class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-full bg-gradient-to-r from-emerald-100 to-green-100 text-emerald-700 border border-emerald-200">
                                            <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2 animate-pulse"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-full bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 border border-gray-300">
                                            <i class="fas fa-check-circle text-gray-500 mr-1.5 text-xs"></i>
                                            Ditutup
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mb-3">
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

            <!-- Pagination -->
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
        // Form Buka Kasir
        const formBukaKasir = document.getElementById('formBukaKasir');
        if (formBukaKasir) {
            formBukaKasir.addEventListener('submit', async function(e) {
                e.preventDefault();

                const modalAwal = document.getElementById('modal_awal').value;

                if (!modalAwal || modalAwal <= 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian!',
                        text: 'Masukkan modal awal yang valid',
                        confirmButtonColor: '#f59e0b',
                        customClass: {
                            popup: 'rounded-2xl',
                            confirmButton: 'px-6 py-3 rounded-xl font-semibold'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                const result = await Swal.fire({
                    title: 'Buka Kasir?',
                    html: `
                        <div class="text-left space-y-2 bg-gray-50 rounded-xl p-4 mb-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Modal Awal:</span>
                                <span class="font-bold text-emerald-600">Rp ${parseInt(modalAwal).toLocaleString('id-ID')}</span>
                            </div>
                        </div>
                        <p class="text-gray-600">Pastikan modal awal sudah sesuai dengan uang di kasir</p>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '<i class="fas fa-unlock-alt mr-2"></i>Ya, Buka Kasir',
                    cancelButtonText: '<i class="fas fa-times mr-2"></i>Batal',
                    customClass: {
                        popup: 'rounded-2xl',
                        title: 'text-xl font-bold text-gray-800',
                        confirmButton: 'px-6 py-3 rounded-xl font-semibold shadow-lg',
                        cancelButton: 'px-6 py-3 rounded-xl font-semibold'
                    },
                    buttonsStyling: false
                });

                if (!result.isConfirmed) return;

                try {
                    const response = await fetch('{{ route('kasir.open') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            modal_awal: modalAwal
                        })
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        await Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message || 'Kasir berhasil dibuka',
                            confirmButtonColor: '#10b981',
                            customClass: {
                                popup: 'rounded-2xl',
                                confirmButton: 'px-6 py-3 rounded-xl font-semibold'
                            },
                            buttonsStyling: false
                        });
                        window.location.reload();
                    } else {
                        throw new Error(data.message || 'Gagal membuka kasir');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: error.message || 'Terjadi kesalahan saat membuka kasir',
                        confirmButtonColor: '#ef4444',
                        customClass: {
                            popup: 'rounded-2xl',
                            confirmButton: 'px-6 py-3 rounded-xl font-semibold'
                        },
                        buttonsStyling: false
                    });
                }
            });
        }

        // Button Tutup Kasir
        const btnTutupKasir = document.getElementById('btnTutupKasir');
        if (btnTutupKasir) {
            btnTutupKasir.addEventListener('click', async function() {
                const kasirId = this.getAttribute('data-kasir-id');

                const result = await Swal.fire({
                    title: 'Tutup Kasir?',
                    html: `
                        <div class="bg-amber-50 rounded-xl p-4 mb-4">
                            <i class="fas fa-exclamation-triangle text-amber-500 text-2xl mb-2"></i>
                            <p class="text-gray-700 font-medium">Pastikan semua transaksi sudah selesai</p>
                        </div>
                        <p class="text-gray-600 text-sm">Anda akan diminta memasukkan saldo akhir kasir</p>
                    `,
                    icon: 'warning',
                    input: 'number',
                    inputLabel: 'Saldo Akhir Kasir',
                    inputPlaceholder: 'Masukkan total uang di kasir',
                    inputAttributes: {
                        min: 0,
                        step: 1000,
                        required: true
                    },
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '<i class="fas fa-lock mr-2"></i>Ya, Tutup Kasir',
                    cancelButtonText: '<i class="fas fa-times mr-2"></i>Batal',
                    customClass: {
                        popup: 'rounded-2xl',
                        title: 'text-xl font-bold text-gray-800',
                        input: 'rounded-xl border-2 border-gray-300 px-4 py-3',
                        confirmButton: 'px-6 py-3 rounded-xl font-semibold shadow-lg',
                        cancelButton: 'px-6 py-3 rounded-xl font-semibold'
                    },
                    buttonsStyling: false,
                    preConfirm: (saldoAkhir) => {
                        if (!saldoAkhir || saldoAkhir <= 0) {
                            Swal.showValidationMessage('Masukkan saldo akhir yang valid');
                            return false;
                        }
                        return saldoAkhir;
                    }
                });

                if (!result.isConfirmed) return;

                const saldoAkhir = result.value;

                try {
                    const response = await fetch(`/kasir/${kasirId}/close`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            saldo_akhir: saldoAkhir
                        })
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        await Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            html: `
                                <div class="bg-gray-50 rounded-xl p-4 space-y-2 mb-4">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Modal Awal:</span>
                                        <span class="font-bold">Rp ${parseInt(data.modal_awal).toLocaleString('id-ID')}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Saldo Akhir:</span>
                                        <span class="font-bold">Rp ${parseInt(saldoAkhir).toLocaleString('id-ID')}</span>
                                    </div>
                                    <div class="flex justify-between border-t pt-2">
                                        <span class="text-gray-600 font-semibold">Selisih:</span>
                                        <span class="font-bold text-emerald-600">Rp ${parseInt(data.selisih || (saldoAkhir - data.modal_awal)).toLocaleString('id-ID')}</span>
                                    </div>
                                </div>
                                <p class="text-gray-600">${data.message || 'Kasir berhasil ditutup'}</p>
                            `,
                            confirmButtonColor: '#10b981',
                            customClass: {
                                popup: 'rounded-2xl',
                                confirmButton: 'px-6 py-3 rounded-xl font-semibold'
                            },
                            buttonsStyling: false
                        });
                        window.location.reload();
                    } else {
                        throw new Error(data.message || 'Gagal menutup kasir');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: error.message || 'Terjadi kesalahan saat menutup kasir',
                        confirmButtonColor: '#ef4444',
                        customClass: {
                            popup: 'rounded-2xl',
                            confirmButton: 'px-6 py-3 rounded-xl font-semibold'
                        },
                        buttonsStyling: false
                    });
                }
            });
        }
    </script>
@endpush