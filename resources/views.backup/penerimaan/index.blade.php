@extends('layouts.app')

@section('title', 'Penerimaan Barang')
@section('page-title', 'Penerimaan Barang')

@section('content')
    <div class="space-y-4 sm:space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-4">
            <div class="flex items-center">
                <div
                    class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-green-600 to-emerald-600 rounded-xl flex items-center justify-center mr-3 shadow-lg">
                    <i class="fas fa-box-open text-white text-lg sm:text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800">Penerimaan Barang</h1>
                    <p class="text-xs sm:text-sm text-gray-600">Kelola data penerimaan barang</p>
                </div>
            </div>
            <a href="{{ route('penerimaan.create') }}"
                class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold rounded-xl hover:shadow-lg transform hover:scale-[1.02] active:scale-[0.98] transition-all text-sm sm:text-base">
                <i class="fas fa-plus mr-2"></i>
                Tambah Penerimaan
            </a>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <div
                class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-xs sm:text-sm font-medium">Total Penerimaan</p>
                        @php
                            $totalPenerimaan = DB::table('penerimaan')->count();
                        @endphp
                        <h3 class="text-2xl sm:text-3xl font-bold mt-1 sm:mt-2">{{ $totalPenerimaan }}</h3>
                    </div>
                    <div class="bg-white/20 p-2 sm:p-4 rounded-lg sm:rounded-xl">
                        <i class="fas fa-box-open text-xl sm:text-3xl"></i>
                    </div>
                </div>
            </div>

            <div
                class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 text-white">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1 mr-2">
                        <p class="text-green-100 text-xs sm:text-sm font-medium">Total Nilai</p>
                        @php
                            $totalNilai = DB::table('penerimaan')->sum('total_harga') ?? 0;
                        @endphp
                        <h3 class="text-base sm:text-2xl font-bold mt-1 sm:mt-2 truncate">
                            Rp {{ number_format($totalNilai / 1000, 0) }}K
                        </h3>
                    </div>
                    <div class="bg-white/20 p-2 sm:p-4 rounded-lg sm:rounded-xl flex-shrink-0">
                        <i class="fas fa-money-bill-wave text-xl sm:text-3xl"></i>
                    </div>
                </div>
            </div>

            <div
                class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-xs sm:text-sm font-medium">Bulan Ini</p>
                        @php
                            $penerimaanBulanIni = DB::table('penerimaan')
                                ->whereMonth('tanggal_penerimaan', date('m'))
                                ->whereYear('tanggal_penerimaan', date('Y'))
                                ->count();
                        @endphp
                        <h3 class="text-2xl sm:text-3xl font-bold mt-1 sm:mt-2">{{ $penerimaanBulanIni }}</h3>
                    </div>
                    <div class="bg-white/20 p-2 sm:p-4 rounded-lg sm:rounded-xl">
                        <i class="fas fa-calendar text-xl sm:text-3xl"></i>
                    </div>
                </div>
            </div>

            <div
                class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-xs sm:text-sm font-medium">Halaman</p>
                        <h3 class="text-2xl sm:text-3xl font-bold mt-1 sm:mt-2">
                            {{ $penerimaan->currentPage() }}/{{ $penerimaan->lastPage() }}
                        </h3>
                    </div>
                    <div class="bg-white/20 p-2 sm:p-4 rounded-lg sm:rounded-xl">
                        <i class="fas fa-file-alt text-xl sm:text-3xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6">
            <form method="GET" action="{{ route('penerimaan.index') }}" class="space-y-3 sm:space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <!-- Search -->
                    <div class="sm:col-span-2 lg:col-span-1">
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..."
                                class="w-full pl-10 pr-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all text-sm">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>

                    <!-- Supplier Filter -->
                    <div>
                        <select name="supplier"
                            class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all text-sm">
                            <option value="">Semua Supplier</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id_supplier }}"
                                    {{ request('supplier') == $supplier->id_supplier ? 'selected' : '' }}>
                                    {{ $supplier->nama_supplier }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Start Date -->
                    <div>
                        <input type="date" name="start_date" value="{{ request('start_date') }}"
                            class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all text-sm">
                    </div>

                    <!-- End Date -->
                    <div>
                        <input type="date" name="end_date" value="{{ request('end_date') }}"
                            class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all text-sm">
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="submit"
                        class="px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold rounded-xl hover:shadow-lg transform hover:scale-[1.02] active:scale-[0.98] transition-all text-sm">
                        <i class="fas fa-filter mr-2"></i>
                        Filter
                    </button>
                    @if (request()->hasAny(['search', 'supplier', 'start_date', 'end_date']))
                        <a href="{{ route('penerimaan.index') }}"
                            class="px-4 sm:px-6 py-2.5 sm:py-3 bg-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-300 transition-all text-sm text-center">
                            <i class="fas fa-times mr-2"></i>
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Mobile: Card View -->
        <div class="block lg:hidden space-y-3">
            @if ($penerimaan->count() > 0)
                @foreach ($penerimaan as $index => $item)
                    <div
                        class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all overflow-hidden border border-gray-200">
                        <div class="p-4">
                            <!-- Header -->
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center flex-1 min-w-0">
                                    <div
                                        class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl flex items-center justify-center text-white font-bold mr-3 flex-shrink-0 shadow-md">
                                        {{ strtoupper(substr($item->nama_supplier, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-bold text-gray-900 text-sm truncate">{{ $item->nama_supplier }}</p>
                                        <p class="text-xs text-gray-500 font-mono">
                                            PNM-{{ str_pad($item->id_penerimaan, 6, '0', STR_PAD_LEFT) }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Info -->
                            <div class="space-y-2 mb-3">
                                <div class="flex items-center justify-between text-sm">
                                    <div class="flex items-center text-gray-600">
                                        <i class="fas fa-calendar text-blue-500 mr-2 text-xs"></i>
                                        <span>{{ \Carbon\Carbon::parse($item->tanggal_penerimaan)->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between pt-2 border-t border-gray-200">
                                    <span class="text-xs text-gray-600 font-medium">Total Harga:</span>
                                    <span class="font-bold text-green-600 text-base">
                                        Rp {{ number_format($item->total_harga, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="grid grid-cols-2 gap-2 pt-3 border-t border-gray-200">
                                <a href="{{ route('penerimaan.show', $item->id_penerimaan) }}"
                                    class="flex items-center justify-center px-3 py-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-all text-sm font-semibold">
                                    <i class="fas fa-eye mr-1.5"></i>
                                    Detail
                                </a>
                                <button
                                    onclick="confirmDelete({{ $item->id_penerimaan }}, 'PNM-{{ str_pad($item->id_penerimaan, 6, '0', STR_PAD_LEFT) }}')"
                                    class="flex items-center justify-center px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-all text-sm font-semibold">
                                    <i class="fas fa-trash mr-1.5"></i>
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Mobile Pagination -->
                <div class="bg-white rounded-xl shadow-lg p-4">
                    {{ $penerimaan->links() }}
                </div>
            @else
                <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                    <div
                        class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-inbox text-4xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-500 font-medium text-sm">
                        @if (request()->hasAny(['search', 'supplier', 'start_date', 'end_date']))
                            Tidak ada penerimaan ditemukan
                        @else
                            Belum ada penerimaan barang
                        @endif
                    </p>
                    <p class="text-gray-400 text-xs mt-1">
                        @if (request()->hasAny(['search', 'supplier', 'start_date', 'end_date']))
                            dengan filter tersebut
                        @else
                            Klik "Tambah Penerimaan" untuk menambahkan
                        @endif
                    </p>
                </div>
            @endif
        </div>

        <!-- Desktop: Table View -->
        <div class="hidden lg:block bg-white rounded-2xl shadow-lg overflow-hidden">
            @if ($penerimaan->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-green-600 to-emerald-600 text-white">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">No. Penerimaan
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Supplier</th>
                                <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider">Total Harga
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($penerimaan as $index => $item)
                                <tr
                                    class="hover:bg-gradient-to-r hover:from-green-50 hover:to-emerald-50 transition-colors">
                                    <td class="px-4 py-4">
                                        <span class="font-mono text-sm font-bold text-green-600">
                                            PNM-{{ str_pad($item->id_penerimaan, 6, '0', STR_PAD_LEFT) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-700">
                                        <div class="flex items-center">
                                            <i class="fas fa-calendar text-blue-500 mr-2"></i>
                                            {{ \Carbon\Carbon::parse($item->tanggal_penerimaan)->format('d/m/Y H:i') }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center">
                                            <div
                                                class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl flex items-center justify-center text-white font-bold mr-3 flex-shrink-0">
                                                {{ strtoupper(substr($item->nama_supplier, 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-900">{{ $item->nama_supplier }}</p>
                                                <p class="text-xs text-gray-500">ID: {{ $item->id_supplier }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <span class="font-bold text-green-600 text-lg">
                                            Rp {{ number_format($item->total_harga, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('penerimaan.show', $item->id_penerimaan) }}"
                                                class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-all"
                                                title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button
                                                onclick="confirmDelete({{ $item->id_penerimaan }}, 'PNM-{{ str_pad($item->id_penerimaan, 6, '0', STR_PAD_LEFT) }}')"
                                                class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-all"
                                                title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Desktop Pagination -->
                <div class="px-4 py-4 border-t border-gray-200">
                    {{ $penerimaan->links() }}
                </div>
            @else
                <div class="text-center py-16">
                    <div
                        class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-inbox text-5xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-500 font-medium text-lg">
                        @if (request()->hasAny(['search', 'supplier', 'start_date', 'end_date']))
                            Tidak ada penerimaan yang ditemukan dengan filter tersebut
                        @else
                            Belum ada penerimaan barang
                        @endif
                    </p>
                    <p class="text-gray-400 text-sm mt-2">
                        @if (!request()->hasAny(['search', 'supplier', 'start_date', 'end_date']))
                            Klik tombol "Tambah Penerimaan" untuk menambahkan
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all">
            <div class="p-6">
                <div class="flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-3xl text-red-600"></i>
                </div>
                <h3 class="text-xl sm:text-2xl font-bold text-center text-gray-900 mb-2">Konfirmasi Hapus</h3>
                <p class="text-sm sm:text-base text-gray-600 text-center mb-4">
                    Apakah Anda yakin ingin menghapus penerimaan <strong id="penerimaanNo"></strong>?
                </p>
                <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-6">
                    <p class="text-xs sm:text-sm text-red-600 text-center font-medium">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        Stock produk akan dikurangi sesuai qty penerimaan!
                    </p>
                </div>
                <form id="deleteForm" method="POST" class="flex flex-col sm:flex-row gap-3">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="closeDeleteModal()"
                        class="flex-1 px-4 sm:px-6 py-2.5 sm:py-3 bg-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-300 transition-all text-sm sm:text-base">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-red-600 to-red-700 text-white font-semibold rounded-xl hover:shadow-lg transform hover:scale-[1.02] active:scale-[0.98] transition-all text-sm sm:text-base">
                        <i class="fas fa-trash mr-2"></i>
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function confirmDelete(id, no) {
            document.getElementById('penerimaanNo').textContent = no;
            document.getElementById('deleteForm').action = `/penerimaan/${id}`;
            document.getElementById('deleteModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
            }
        });
    </script>
@endpush
