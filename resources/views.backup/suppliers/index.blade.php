@extends('layouts.app')

@section('title', 'Kelola Supplier')
@section('page-title', 'Kelola Supplier')

@section('content')
    <div class="space-y-4 sm:space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-4">
            <div class="flex items-center">
                <div
                    class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-purple-600 to-blue-600 rounded-xl flex items-center justify-center mr-3 shadow-lg">
                    <i class="fas fa-truck text-white text-lg sm:text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800">Kelola Supplier</h1>
                    <p class="text-xs sm:text-sm text-gray-600">Manajemen data supplier</p>
                </div>
            </div>
            <a href="{{ route('suppliers.create') }}"
                class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white font-semibold rounded-xl hover:shadow-lg transform hover:scale-[1.02] active:scale-[0.98] transition-all text-sm sm:text-base">
                <i class="fas fa-plus mr-2"></i>
                Tambah Supplier
            </a>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
            <div
                class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-xs sm:text-sm font-medium">Total Supplier</p>
                        <h3 class="text-2xl sm:text-3xl font-bold mt-1 sm:mt-2">
                            @php
                                $totalSuppliers = DB::table('supplier')->count();
                            @endphp
                            {{ $totalSuppliers }}
                        </h3>
                    </div>
                    <div class="bg-white/20 p-3 sm:p-4 rounded-xl">
                        <i class="fas fa-truck text-2xl sm:text-3xl"></i>
                    </div>
                </div>
            </div>

            <div
                class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-xs sm:text-sm font-medium">Supplier Aktif</p>
                        <h3 class="text-2xl sm:text-3xl font-bold mt-1 sm:mt-2">
                            @php
                                $activeSuppliers = DB::table('supplier')
                                    ->whereExists(function ($query) {
                                        $query
                                            ->select(DB::raw(1))
                                            ->from('penerimaan')
                                            ->whereColumn('penerimaan.id_supplier', 'supplier.id_supplier');
                                    })
                                    ->count();
                            @endphp
                            {{ $activeSuppliers }}
                        </h3>
                    </div>
                    <div class="bg-white/20 p-3 sm:p-4 rounded-xl">
                        <i class="fas fa-check-circle text-2xl sm:text-3xl"></i>
                    </div>
                </div>
            </div>

            <div
                class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 text-white sm:col-span-2 lg:col-span-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-xs sm:text-sm font-medium">Halaman</p>
                        <h3 class="text-2xl sm:text-3xl font-bold mt-1 sm:mt-2">
                            {{ $suppliers->currentPage() }}/{{ $suppliers->lastPage() }}
                        </h3>
                    </div>
                    <div class="bg-white/20 p-3 sm:p-4 rounded-xl">
                        <i class="fas fa-file-alt text-2xl sm:text-3xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search & Filter -->
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6">
            <form method="GET" action="{{ route('suppliers.index') }}" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari supplier..."
                            class="w-full pl-11 pr-4 py-2.5 sm:py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all text-sm">
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>
                <button type="submit"
                    class="px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white font-semibold rounded-xl hover:shadow-lg transform hover:scale-[1.02] active:scale-[0.98] transition-all text-sm whitespace-nowrap">
                    <i class="fas fa-search mr-2"></i>
                    Cari
                </button>
                @if (request('search'))
                    <a href="{{ route('suppliers.index') }}"
                        class="px-4 sm:px-6 py-2.5 sm:py-3 bg-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-300 transition-all text-sm text-center whitespace-nowrap">
                        <i class="fas fa-times mr-2"></i>
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Mobile: Card View -->
        <div class="block lg:hidden space-y-3">
            @if ($suppliers->count() > 0)
                @foreach ($suppliers as $index => $supplier)
                    <div
                        class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all overflow-hidden border border-gray-200">
                        <div class="p-4">
                            <!-- Header -->
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center flex-1 min-w-0">
                                    <div
                                        class="w-12 h-12 bg-gradient-to-br from-purple-500 to-blue-500 rounded-xl flex items-center justify-center text-white font-bold mr-3 flex-shrink-0 shadow-md">
                                        {{ strtoupper(substr($supplier->nama_supplier, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-bold text-gray-900 text-base truncate">{{ $supplier->nama_supplier }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            #{{ ($suppliers->currentPage() - 1) * $suppliers->perPage() + $index + 1 }}
                                        </p>
                                    </div>
                                </div>
                                @php
                                    $totalPenerimaan = DB::table('penerimaan')
                                        ->where('id_supplier', $supplier->id_supplier)
                                        ->count();
                                @endphp
                                @if ($totalPenerimaan > 0)
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold ml-2 whitespace-nowrap">
                                        <i class="fas fa-box mr-1"></i>
                                        {{ $totalPenerimaan }}
                                    </span>
                                @endif
                            </div>

                            <!-- Contact Info -->
                            <div class="space-y-2 mb-3">
                                @if ($supplier->telp_supplier)
                                    <div class="flex items-center text-sm text-gray-700">
                                        <div
                                            class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-2 flex-shrink-0">
                                            <i class="fas fa-phone text-green-600 text-xs"></i>
                                        </div>
                                        <span class="truncate">{{ $supplier->telp_supplier }}</span>
                                    </div>
                                @endif
                                @if ($supplier->alamat_supplier)
                                    <div class="flex items-start text-sm text-gray-700">
                                        <div
                                            class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-2 flex-shrink-0">
                                            <i class="fas fa-map-marker-alt text-red-600 text-xs"></i>
                                        </div>
                                        <span class="line-clamp-2">{{ $supplier->alamat_supplier }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div class="grid grid-cols-3 gap-2 pt-3 border-t border-gray-200">
                                <a href="{{ route('suppliers.show', $supplier->id_supplier) }}"
                                    class="flex items-center justify-center px-3 py-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-all text-sm font-semibold">
                                    <i class="fas fa-eye mr-1.5"></i>
                                    <span class="hidden sm:inline">Detail</span>
                                </a>
                                <a href="{{ route('suppliers.edit', $supplier->id_supplier) }}"
                                    class="flex items-center justify-center px-3 py-2 bg-yellow-100 text-yellow-600 rounded-lg hover:bg-yellow-200 transition-all text-sm font-semibold">
                                    <i class="fas fa-edit mr-1.5"></i>
                                    <span class="hidden sm:inline">Edit</span>
                                </a>
                                <button
                                    onclick="confirmDelete({{ $supplier->id_supplier }}, '{{ $supplier->nama_supplier }}')"
                                    class="flex items-center justify-center px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-all text-sm font-semibold">
                                    <i class="fas fa-trash mr-1.5"></i>
                                    <span class="hidden sm:inline">Hapus</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Mobile Pagination -->
                <div class="bg-white rounded-xl shadow-lg p-4">
                    {{ $suppliers->links() }}
                </div>
            @else
                <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                    <div
                        class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-inbox text-4xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-500 font-medium text-sm sm:text-base">
                        @if (request('search'))
                            Tidak ada supplier yang ditemukan
                        @else
                            Belum ada supplier
                        @endif
                    </p>
                    <p class="text-gray-400 text-xs sm:text-sm mt-1">
                        @if (request('search'))
                            dengan kata kunci "{{ request('search') }}"
                        @else
                            Klik tombol "Tambah Supplier" untuk menambahkan
                        @endif
                    </p>
                </div>
            @endif
        </div>

        <!-- Desktop: Table View -->
        <div class="hidden lg:block bg-white rounded-2xl shadow-lg overflow-hidden">
            @if ($suppliers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-purple-600 to-blue-600 text-white">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">No</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Nama Supplier
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Telepon</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Alamat</th>
                                <th class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wider">Total
                                    Penerimaan</th>
                                <th class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($suppliers as $index => $supplier)
                                <tr class="hover:bg-gradient-to-r hover:from-purple-50 hover:to-blue-50 transition-colors">
                                    <td class="px-4 py-4 text-sm text-gray-700 font-medium">
                                        {{ ($suppliers->currentPage() - 1) * $suppliers->perPage() + $index + 1 }}
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center">
                                            <div
                                                class="w-10 h-10 bg-gradient-to-br from-purple-500 to-blue-500 rounded-xl flex items-center justify-center text-white font-bold mr-3 flex-shrink-0">
                                                {{ strtoupper(substr($supplier->nama_supplier, 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-900">{{ $supplier->nama_supplier }}</p>
                                                <p class="text-xs text-gray-500">ID: {{ $supplier->id_supplier }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-700">
                                        @if ($supplier->telp_supplier)
                                            <div class="flex items-center">
                                                <i class="fas fa-phone text-green-500 mr-2"></i>
                                                {{ $supplier->telp_supplier }}
                                            </div>
                                        @else
                                            <span class="text-gray-400 italic">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-700">
                                        @if ($supplier->alamat_supplier)
                                            <div class="max-w-xs truncate" title="{{ $supplier->alamat_supplier }}">
                                                <i class="fas fa-map-marker-alt text-red-500 mr-2"></i>
                                                {{ $supplier->alamat_supplier }}
                                            </div>
                                        @else
                                            <span class="text-gray-400 italic">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        @php
                                            $totalPenerimaan = DB::table('penerimaan')
                                                ->where('id_supplier', $supplier->id_supplier)
                                                ->count();
                                        @endphp
                                        @if ($totalPenerimaan > 0)
                                            <span
                                                class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-bold">
                                                <i class="fas fa-box mr-1"></i>
                                                {{ $totalPenerimaan }}
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-500 rounded-full text-sm font-medium">
                                                0
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('suppliers.show', $supplier->id_supplier) }}"
                                                class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-all"
                                                title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('suppliers.edit', $supplier->id_supplier) }}"
                                                class="p-2 bg-yellow-100 text-yellow-600 rounded-lg hover:bg-yellow-200 transition-all"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button
                                                onclick="confirmDelete({{ $supplier->id_supplier }}, '{{ $supplier->nama_supplier }}')"
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
                    {{ $suppliers->links() }}
                </div>
            @else
                <div class="text-center py-16">
                    <div
                        class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-inbox text-5xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-500 font-medium text-lg">
                        @if (request('search'))
                            Tidak ada supplier yang ditemukan
                        @else
                            Belum ada supplier
                        @endif
                    </p>
                    <p class="text-gray-400 text-sm mt-2">
                        @if (request('search'))
                            dengan kata kunci "{{ request('search') }}"
                        @else
                            Klik tombol "Tambah Supplier" untuk menambahkan
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
                <p class="text-sm sm:text-base text-gray-600 text-center mb-6">
                    Apakah Anda yakin ingin menghapus supplier <strong id="supplierName"></strong>?
                    <span class="block mt-2 text-xs sm:text-sm text-red-600">Aksi ini tidak dapat dibatalkan!</span>
                </p>
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
        function confirmDelete(id, name) {
            document.getElementById('supplierName').textContent = name;
            document.getElementById('deleteForm').action = `/suppliers/${id}`;
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
