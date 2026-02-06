@extends('layouts.app')

@section('title', 'Detail Supplier')
@section('page-title', 'Detail Supplier')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('suppliers.index') }}"
                class="inline-flex items-center text-purple-600 hover:text-purple-700 font-semibold">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Daftar Supplier
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Supplier Profile -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <!-- Profile Header -->
                    <div class="bg-gradient-to-br from-purple-600 to-blue-600 p-8 text-center">
                        <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <span class="text-4xl font-bold text-purple-600">
                                {{ strtoupper(substr($supplier->nama_supplier, 0, 1)) }}
                            </span>
                        </div>
                        <h2 class="text-2xl font-bold text-white mb-1">{{ $supplier->nama_supplier }}</h2>
                        <p class="text-purple-100">ID: {{ $supplier->id_supplier }}</p>
                    </div>

                    <!-- Contact Info -->
                    <div class="p-6 space-y-4">
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-semibold mb-2">Kontak</p>
                            @if ($supplier->telp_supplier)
                                <div class="flex items-center text-gray-700">
                                    <i class="fas fa-phone text-green-500 mr-3"></i>
                                    <span>{{ $supplier->telp_supplier }}</span>
                                </div>
                            @else
                                <p class="text-gray-400 italic">Tidak ada nomor telepon</p>
                            @endif
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 uppercase font-semibold mb-2">Alamat</p>
                            @if ($supplier->alamat_supplier)
                                <div class="flex items-start text-gray-700">
                                    <i class="fas fa-map-marker-alt text-red-500 mr-3 mt-1"></i>
                                    <span>{{ $supplier->alamat_supplier }}</span>
                                </div>
                            @else
                                <p class="text-gray-400 italic">Tidak ada alamat</p>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="p-6 pt-0 space-y-3">
                        <a href="{{ route('suppliers.edit', $supplier->id_supplier) }}"
                            class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-yellow-500 to-orange-500 text-white font-semibold rounded-xl hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                            <i class="fas fa-edit mr-2"></i>
                            Edit Supplier
                        </a>
                        <button onclick="confirmDelete()"
                            class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white font-semibold rounded-xl hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                            <i class="fas fa-trash mr-2"></i>
                            Hapus Supplier
                        </button>
                    </div>
                </div>
            </div>

            <!-- Statistics & History -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                                <i class="fas fa-box text-white text-xl"></i>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mb-1">Total Penerimaan</p>
                        <h3 class="text-3xl font-bold text-gray-900">{{ $stats['total_penerimaan'] }}</h3>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center">
                                <i class="fas fa-money-bill-wave text-white text-xl"></i>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mb-1">Total Nilai</p>
                        <h3 class="text-2xl font-bold text-gray-900">
                            Rp {{ number_format($stats['total_nilai'], 0, ',', '.') }}
                        </h3>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                                <i class="fas fa-calendar text-white text-xl"></i>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mb-1">Terakhir</p>
                        <h3 class="text-lg font-bold text-gray-900">
                            @if ($stats['penerimaan_terakhir'])
                                {{ \Carbon\Carbon::parse($stats['penerimaan_terakhir']->tanggal_penerimaan)->format('d/m/Y') }}
                            @else
                                -
                            @endif
                        </h3>
                    </div>
                </div>

                <!-- Recent Penerimaan -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-600 to-blue-600 px-6 py-4 flex items-center justify-between">
                        <h3 class="text-xl font-bold text-white">Riwayat Penerimaan Terakhir</h3>
                        <span class="px-3 py-1 bg-white/20 rounded-full text-sm text-white">
                            {{ $recentPenerimaan->count() }} transaksi
                        </span>
                    </div>

                    @if ($recentPenerimaan->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">No.
                                            Penerimaan</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                            Tanggal</th>
                                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Total
                                            Harga</th>
                                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($recentPenerimaan as $penerimaan)
                                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                                            <td class="px-6 py-4">
                                                <span class="font-mono text-sm font-semibold text-purple-600">
                                                    PNM-{{ str_pad($penerimaan->id_penerimaan, 6, '0', STR_PAD_LEFT) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-gray-700">
                                                {{ \Carbon\Carbon::parse($penerimaan->tanggal_penerimaan)->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <span class="font-semibold text-green-600">
                                                    Rp {{ number_format($penerimaan->total_harga, 0, ',', '.') }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <a href="{{ route('penerimaan.show', $penerimaan->id_penerimaan) }}"
                                                    class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-colors duration-200">
                                                    <i class="fas fa-eye mr-1"></i>
                                                    Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-16">
                            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-lg">Belum ada riwayat penerimaan</p>
                            <p class="text-gray-400 text-sm mt-2">Penerimaan dari supplier ini akan muncul di sini</p>
                        </div>
                    @endif
                </div>
            </div>
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
                <h3 class="text-2xl font-bold text-center text-gray-900 mb-2">Konfirmasi Hapus</h3>
                <p class="text-gray-600 text-center mb-6">
                    Apakah Anda yakin ingin menghapus supplier <strong>{{ $supplier->nama_supplier }}</strong>?
                    @if ($stats['total_penerimaan'] > 0)
                        <span class="block mt-2 text-sm text-red-600 font-semibold">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            Supplier ini memiliki {{ $stats['total_penerimaan'] }} riwayat penerimaan dan tidak dapat
                            dihapus!
                        </span>
                    @else
                        <span class="block mt-2 text-sm text-red-600">Aksi ini tidak dapat dibatalkan!</span>
                    @endif
                </p>
                <form action="{{ route('suppliers.destroy', $supplier->id_supplier) }}" method="POST" class="flex gap-3">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="closeDeleteModal()"
                        class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-300 transition-all duration-200">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white font-semibold rounded-xl hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200"
                        @if ($stats['total_penerimaan'] > 0) disabled @endif>
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
        function confirmDelete() {
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
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
