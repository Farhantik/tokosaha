@extends('layouts.app')

@section('title', 'Pelanggan - Toko Sahabat')
@section('page-title', 'Manajemen Pelanggan')

@section('content')
    <div class="space-y-4 sm:space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div class="flex items-center">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center mr-3 shadow-lg">
                    <i class="fas fa-users text-white text-lg sm:text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Data Pelanggan</h2>
                    <p class="text-xs sm:text-sm text-gray-500">Kelola data pelanggan</p>
                </div>
            </div>
            <button onclick="openModal()"
                class="w-full sm:w-auto bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white px-4 sm:px-6 py-2.5 sm:py-3 rounded-xl font-semibold transition-all shadow-lg hover:shadow-xl transform hover:scale-[1.02] active:scale-[0.98] text-sm sm:text-base">
                <i class="fas fa-plus mr-2"></i>Tambah Pelanggan
            </button>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm text-gray-500 mb-1">Total Pelanggan</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $stats['total_pelanggan'] }}</p>
                    </div>
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 text-xl sm:text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm text-gray-500 mb-1">Punya Piutang</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $stats['pelanggan_piutang'] }}</p>
                    </div>
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-orange-100 to-orange-200 rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-invoice-dollar text-orange-600 text-xl sm:text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm text-gray-500 mb-1">Total Piutang</p>
                        <p class="text-xl sm:text-2xl font-bold text-red-600">
                            Rp {{ number_format($stats['total_piutang'], 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-red-100 to-red-200 rounded-xl flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-red-600 text-xl sm:text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search -->
        <div class="bg-white rounded-xl shadow-lg p-4">
            <div class="relative">
                <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="searchTable"
                    class="w-full pl-11 pr-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all text-sm"
                    placeholder="Cari nama pelanggan, no telp...">
            </div>
        </div>

        <!-- Mobile: Card View -->
        <div class="block lg:hidden space-y-3">
            @forelse($pelanggan as $item)
                <div class="pelanggan-card bg-white rounded-xl shadow-lg hover:shadow-xl transition-all overflow-hidden border border-gray-200">
                    <div class="p-4">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                    {{ strtoupper(substr($item->nama_pelanggan, 0, 1)) }}
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-800">{{ $item->nama_pelanggan }}</h3>
                                    @if($item->no_telp)
                                        <p class="text-xs text-gray-500">
                                            <i class="fas fa-phone mr-1"></i>{{ $item->no_telp }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <span class="px-2 py-1 text-xs font-bold rounded-full bg-{{ $item->status_badge }}-100 text-{{ $item->status_badge }}-700 border border-{{ $item->status_badge }}-200">
                                {{ ucfirst($item->status) }}
                            </span>
                        </div>

                        @if($item->alamat)
                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                                <i class="fas fa-map-marker-alt mr-1 text-gray-400"></i>{{ $item->alamat }}
                            </p>
                        @endif

                        <div class="flex items-center justify-between mb-3 p-3 bg-gray-50 rounded-lg">
                            <span class="text-xs text-gray-500">Total Piutang</span>
                            <span class="font-bold {{ $item->has_piutang ? 'text-red-600' : 'text-green-600' }}">
                                {{ $item->total_piutang_format }}
                            </span>
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('pelanggan.show', $item->id_pelanggan) }}"
                                class="flex-1 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white px-3 py-2 rounded-lg text-xs font-semibold transition-all shadow-md hover:shadow-lg text-center">
                                <i class="fas fa-eye mr-1"></i>Detail
                            </a>
                            <button onclick='editPelanggan(@json($item))'
                                class="flex-1 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white px-3 py-2 rounded-lg text-xs font-semibold transition-all shadow-md hover:shadow-lg">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </button>
                            @if(!$item->has_piutang)
                                <form action="{{ route('pelanggan.destroy', $item->id_pelanggan) }}" method="POST" class="flex-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Yakin hapus pelanggan ini?')"
                                        class="w-full bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white px-3 py-2 rounded-lg text-xs font-semibold transition-all shadow-md hover:shadow-lg">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-4xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-500 font-medium">Belum ada pelanggan</p>
                    <p class="text-gray-400 text-sm mt-1">Tambah pelanggan untuk memulai</p>
                </div>
            @endforelse
        </div>

        <!-- Desktop: Table View -->
        <div class="hidden lg:block bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Kontak</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Alamat</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Total Piutang</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($pelanggan as $item)
                            <tr class="pelanggan-row hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-600 rounded-full flex items-center justify-center text-white font-bold">
                                            {{ strtoupper(substr($item->nama_pelanggan, 0, 1)) }}
                                        </div>
                                        <span class="font-semibold text-gray-800">{{ $item->nama_pelanggan }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    @if($item->no_telp)
                                        <div><i class="fas fa-phone text-gray-400 mr-1"></i>{{ $item->no_telp }}</div>
                                    @endif
                                    @if($item->email)
                                        <div class="text-xs text-gray-500"><i class="fas fa-envelope mr-1"></i>{{ $item->email }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 max-w-xs truncate">{{ $item->alamat ?? '-' }}</td>
                                <td class="px-4 py-3 text-right font-bold {{ $item->has_piutang ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $item->total_piutang_format }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-{{ $item->status_badge }}-100 text-{{ $item->status_badge }}-700 border border-{{ $item->status_badge }}-200">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('pelanggan.show', $item->id_pelanggan) }}"
                                            class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white px-3 py-1.5 rounded-lg text-sm font-semibold transition-all shadow-md hover:shadow-lg"
                                            title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button onclick='editPelanggan(@json($item))'
                                            class="bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white px-3 py-1.5 rounded-lg text-sm font-semibold transition-all shadow-md hover:shadow-lg"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        @if(!$item->has_piutang)
                                            <form action="{{ route('pelanggan.destroy', $item->id_pelanggan) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Yakin hapus pelanggan ini?')"
                                                    class="bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white px-3 py-1.5 rounded-lg text-sm font-semibold transition-all shadow-md hover:shadow-lg"
                                                    title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mb-3">
                                            <i class="fas fa-users text-4xl text-gray-400"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium">Belum ada pelanggan</p>
                                        <p class="text-gray-400 text-sm mt-1">Tambah pelanggan untuk memulai</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t">
                {{ $pelanggan->links() }}
            </div>
        </div>

        <!-- Pagination Mobile -->
        <div class="block lg:hidden">
            @if ($pelanggan->hasPages())
                <div class="bg-white rounded-xl shadow-lg p-4">
                    {{ $pelanggan->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Pelanggan -->
    <div id="modalPelanggan" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl w-full max-w-md my-8 shadow-2xl">
            <div class="p-4 sm:p-6">
                <div class="flex justify-between items-center mb-4 sm:mb-6">
                    <h2 id="modalTitle" class="text-xl sm:text-2xl font-bold text-gray-800">Tambah Pelanggan</h2>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-times text-xl sm:text-2xl"></i>
                    </button>
                </div>

                <form id="formPelanggan" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" id="methodField" name="_method" value="POST">

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2 text-sm">
                            Nama Pelanggan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_pelanggan" id="nama_pelanggan" required
                            class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all text-sm"
                            placeholder="Contoh: Budi Santoso">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2 text-sm">No. Telepon</label>
                        <input type="text" name="no_telp" id="no_telp"
                            class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all text-sm"
                            placeholder="081234567890">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2 text-sm">Email</label>
                        <input type="email" name="email" id="email"
                            class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all text-sm"
                            placeholder="email@example.com">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2 text-sm">Alamat</label>
                        <textarea name="alamat" id="alamat" rows="3"
                            class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all text-sm"
                            placeholder="Jl. ..."></textarea>
                    </div>

                    <div id="statusField" class="hidden">
                        <label class="block text-gray-700 font-semibold mb-2 text-sm">Status</label>
                        <select name="status" id="status"
                            class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all text-sm">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 pt-2">
                        <button type="submit"
                            class="flex-1 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-bold py-3 rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:scale-[1.02] active:scale-[0.98] text-sm sm:text-base">
                            <i class="fas fa-save mr-2"></i>Simpan
                        </button>
                        <button type="button" onclick="closeModal()"
                            class="flex-1 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-bold py-3 rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:scale-[1.02] active:scale-[0.98] text-sm sm:text-base">
                            <i class="fas fa-times mr-2"></i>Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Search functionality
            document.getElementById('searchTable').addEventListener('input', function() {
                const search = this.value.toLowerCase();

                document.querySelectorAll('.pelanggan-row, .pelanggan-card').forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(search) ? '' : 'none';
                });
            });

            // Modal functions
            function openModal() {
                document.getElementById('modalTitle').textContent = 'Tambah Pelanggan';
                document.getElementById('formPelanggan').action = '{{ route('pelanggan.store') }}';
                document.getElementById('methodField').value = 'POST';
                document.getElementById('formPelanggan').reset();
                document.getElementById('statusField').classList.add('hidden');
                document.getElementById('modalPelanggan').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function editPelanggan(pelanggan) {
                document.getElementById('modalTitle').textContent = 'Edit Pelanggan';
                document.getElementById('formPelanggan').action = `/pelanggan/${pelanggan.id_pelanggan}`;
                document.getElementById('methodField').value = 'PUT';

                document.getElementById('nama_pelanggan').value = pelanggan.nama_pelanggan;
                document.getElementById('no_telp').value = pelanggan.no_telp || '';
                document.getElementById('email').value = pelanggan.email || '';
                document.getElementById('alamat').value = pelanggan.alamat || '';
                document.getElementById('status').value = pelanggan.status;
                
                document.getElementById('statusField').classList.remove('hidden');
                document.getElementById('modalPelanggan').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeModal() {
                document.getElementById('modalPelanggan').classList.add('hidden');
                document.body.style.overflow = 'auto';
            }

            // Close modal on ESC
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeModal();
            });

            // Close modal on outside click
            document.getElementById('modalPelanggan').addEventListener('click', (e) => {
                if (e.target.id === 'modalPelanggan') closeModal();
            });
        </script>
    @endpush
@endsection