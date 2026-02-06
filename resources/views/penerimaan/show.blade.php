@extends('layouts.app')

@section('title', 'Detail Penerimaan')
@section('page-title', 'Detail Penerimaan')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('penerimaan.index') }}"
                class="inline-flex items-center text-green-600 hover:text-green-700 font-semibold">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Daftar Penerimaan
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Header Card -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-green-600 to-emerald-600 p-6">
                        <div class="flex items-center justify-between text-white">
                            <div>
                                <p class="text-green-100 text-sm font-medium">Nomor Penerimaan</p>
                                <h2 class="text-3xl font-bold mt-1">
                                    PNM-{{ str_pad($penerimaan->id_penerimaan, 6, '0', STR_PAD_LEFT) }}
                                </h2>
                            </div>
                            <div class="bg-white/20 p-4 rounded-xl">
                                <i class="fas fa-box-open text-4xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Tanggal Penerimaan</p>
                                <p class="text-gray-900 font-semibold flex items-center">
                                    <i class="fas fa-calendar text-blue-500 mr-2"></i>
                                    {{ \Carbon\Carbon::parse($penerimaan->tanggal_penerimaan)->format('d/m/Y H:i') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-semibold mb-1">ID Penerimaan</p>
                                <p class="text-gray-900 font-semibold">{{ $penerimaan->id_penerimaan }}</p>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-200">
                            <p class="text-xs text-gray-500 uppercase font-semibold mb-2">Supplier</p>
                            <div class="flex items-center">
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl flex items-center justify-center text-white font-bold mr-3">
                                    {{ strtoupper(substr($penerimaan->nama_supplier, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $penerimaan->nama_supplier }}</p>
                                    @if ($penerimaan->telp_supplier)
                                        <p class="text-sm text-gray-600">
                                            <i class="fas fa-phone text-green-500 mr-1"></i>
                                            {{ $penerimaan->telp_supplier }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            @if ($penerimaan->alamat_supplier)
                                <p class="mt-2 text-sm text-gray-600">
                                    <i class="fas fa-map-marker-alt text-red-500 mr-1"></i>
                                    {{ $penerimaan->alamat_supplier }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Detail Items -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                        <h3 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-list mr-3"></i>
                            Detail Produk
                        </h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Produk
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Harga
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Qty</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Subtotal
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($detail as $index => $item)
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="px-6 py-4 text-gray-700">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4">
                                            <div>
                                                <p class="font-semibold text-gray-900">{{ $item->nama_produk }}</p>
                                                <p class="text-xs text-gray-500">
                                                    {{ $item->nama_kategori ?? '-' }}
                                                    @if ($item->code_produk)
                                                        | Kode: {{ $item->code_produk }}
                                                    @endif
                                                </p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right text-gray-700">
                                            Rp {{ number_format($item->harga_produk, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span
                                                class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">
                                                {{ $item->qty_produk }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="font-semibold text-green-600">
                                                Rp {{ number_format($item->subtotal_harga, 0, ',', '.') }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50 border-t-2 border-gray-300">
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-right font-bold text-gray-900">
                                        TOTAL:
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-2xl font-bold text-green-600">
                                            Rp {{ number_format($penerimaan->total_harga, 0, ',', '.') }}
                                        </span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Log Stock -->
                @if ($logStock->count() > 0)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                            <h3 class="text-xl font-bold text-white flex items-center">
                                <i class="fas fa-history mr-3"></i>
                                Riwayat Perubahan Stock
                            </h3>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Produk
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Qty
                                            Masuk</th>
                                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                                            Stock Awal</th>
                                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                                            Stock Akhir</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($logStock as $log)
                                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                                            <td class="px-6 py-4">
                                                <div>
                                                    <p class="font-semibold text-gray-900">{{ $log->nama_produk }}</p>
                                                    @if ($log->code_produk)
                                                        <p class="text-xs text-gray-500">Kode: {{ $log->code_produk }}</p>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span
                                                    class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">
                                                    +{{ $log->jumlah_aktivitas }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-center text-gray-700">
                                                {{ $log->jumlah_awal }}
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="font-bold text-green-600">{{ $log->jumlah_akhir }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Statistics Card -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl shadow-lg p-6 border border-blue-100">
                    <div class="flex items-center mb-4">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-xl flex items-center justify-center text-white mr-3">
                            <i class="fas fa-chart-bar text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Statistik</h3>
                    </div>
                    <div class="space-y-4">
                        <div class="bg-white rounded-xl p-4">
                            <p class="text-sm text-gray-600 mb-1">Total Item</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_item'] }}</p>
                        </div>
                        <div class="bg-white rounded-xl p-4">
                            <p class="text-sm text-gray-600 mb-1">Total Qty</p>
                            <p class="text-2xl font-bold text-blue-600">{{ $stats['total_qty'] }}</p>
                        </div>
                        <div class="bg-white rounded-xl p-4">
                            <p class="text-sm text-gray-600 mb-1">Total Harga</p>
                            <p class="text-xl font-bold text-green-600">
                                Rp {{ number_format($stats['total_harga'], 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Actions Card -->
                <div class="bg-white rounded-2xl shadow-lg p-6 space-y-3">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Aksi</h3>

                    <button onclick="window.print()"
                        class="w-full px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                        <i class="fas fa-print mr-2"></i>
                        Print
                    </button>

                    <a href="{{ route('suppliers.show', $penerimaan->id_supplier) }}"
                        class="w-full inline-block text-center px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold rounded-xl hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                        <i class="fas fa-truck mr-2"></i>
                        Lihat Supplier
                    </a>

                    <button onclick="confirmDelete()"
                        class="w-full px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white font-semibold rounded-xl hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                        <i class="fas fa-trash mr-2"></i>
                        Hapus Penerimaan
                    </button>
                </div>

                <!-- Info Card -->
                <div
                    class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-2xl shadow-lg p-6 border border-yellow-100">
                    <div class="flex items-center mb-4">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center text-white mr-3">
                            <i class="fas fa-exclamation-triangle text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Perhatian</h3>
                    </div>
                    <ul class="space-y-3 text-sm text-gray-700">
                        <li class="flex items-start">
                            <i class="fas fa-info-circle text-yellow-500 mt-1 mr-2 flex-shrink-0"></i>
                            <span>Menghapus penerimaan akan <strong>mengurangi stock produk</strong> sesuai qty
                                penerimaan</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-info-circle text-yellow-500 mt-1 mr-2 flex-shrink-0"></i>
                            <span>Log stock akan <strong>ikut terhapus</strong></span>
                        </li>
                    </ul>
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
                    Apakah Anda yakin ingin menghapus penerimaan
                    <strong>PNM-{{ str_pad($penerimaan->id_penerimaan, 6, '0', STR_PAD_LEFT) }}</strong>?
                    <span class="block mt-2 text-sm text-red-600">Stock produk akan dikurangi sesuai qty penerimaan!</span>
                </p>
                <form action="{{ route('penerimaan.destroy', $penerimaan->id_penerimaan) }}" method="POST"
                    class="flex gap-3">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="closeDeleteModal()"
                        class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-300 transition-all duration-200">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white font-semibold rounded-xl hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
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
