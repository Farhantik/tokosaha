@extends('layouts.app')

@section('title', 'Produk - Toko Sahabat')
@section('page-title', 'Manajemen Produk')

@section('content')
    <div class="space-y-4 sm:space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div class="flex items-center">
                <div
                    class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-3 shadow-lg">
                    <i class="fas fa-box text-white text-lg sm:text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Manajemen Produk</h2>
                    <p class="text-xs sm:text-sm text-gray-500">Kelola produk toko</p>
                </div>
            </div>
            <button onclick="openModal()"
                class="w-full sm:w-auto bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-4 sm:px-6 py-2.5 sm:py-3 rounded-xl font-semibold transition-all shadow-lg hover:shadow-xl transform hover:scale-[1.02] active:scale-[0.98] text-sm sm:text-base">
                <i class="fas fa-plus mr-2"></i>Tambah Produk
            </button>
        </div>

        <!-- Search & Filter -->
        <div class="bg-white rounded-xl shadow-lg p-4">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="searchTable"
                        class="w-full pl-11 pr-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm"
                        placeholder="Cari nama, kode produk...">
                </div>
                <select id="filterKategori"
                    class="w-full sm:w-48 px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm">
                    <option value="">Semua Kategori</option>
                    @foreach ($kategori as $kat)
                        <option value="{{ $kat->id_produk_kategori }}">{{ $kat->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Mobile: Card View -->
        <div class="block lg:hidden space-y-3" id="produkMobileView">
            @forelse($produk as $item)
                <div data-kategori="{{ $item->id_produk_kategori }}"
                    class="produk-card bg-white rounded-xl shadow-lg hover:shadow-xl transition-all overflow-hidden border border-gray-200">
                    <div class="p-4">
                        <div class="flex gap-3">
                            <div class="flex-shrink-0">
                                @if ($item->gambar_produk)
                                    <img src="{{ asset('uploads/produk/' . $item->gambar_produk) }}"
                                        alt="{{ $item->nama_produk }}"
                                        class="w-20 h-20 object-cover rounded-lg border-2 border-gray-200">
                                @else
                                    <div
                                        class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg flex items-center justify-center border-2 border-gray-200">
                                        <i class="fas fa-image text-2xl text-gray-400"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1 min-w-0 mr-2">
                                        <h3 class="font-bold text-gray-800 text-sm leading-tight line-clamp-2">
                                            {{ $item->nama_produk }}</h3>
                                        @if ($item->code_produk)
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $item->code_produk }}</p>
                                        @endif
                                    </div>
                                    @if ($item->stock_produk < 10)
                                        <span
                                            class="inline-flex items-center px-2 py-1 text-xs font-bold rounded-full bg-red-100 text-red-700 border border-red-200 whitespace-nowrap flex-shrink-0">
                                            <i
                                                class="fas fa-exclamation-triangle mr-1"></i>{{ max(0, $item->stock_produk) }}
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2 py-1 text-xs font-bold rounded-full bg-green-100 text-green-700 border border-green-200 whitespace-nowrap flex-shrink-0">
                                            <i class="fas fa-check-circle mr-1"></i>{{ $item->stock_produk }}
                                        </span>
                                    @endif
                                </div>
                                <div class="mb-2">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-lg bg-blue-50 text-blue-700 border border-blue-200">
                                        <i
                                            class="fas fa-tag mr-1 text-xs"></i>{{ $item->kategori->nama_kategori ?? 'Tanpa Kategori' }}
                                    </span>
                                </div>
                                <div class="mb-3">
                                    <p class="text-base sm:text-lg font-bold text-emerald-600">
                                        Rp {{ number_format($item->harga_produk, 0, ',', '.') }}
                                    </p>
                                </div>
                                <div class="grid grid-cols-3 gap-1.5">
                                    <a href="{{ route('produk.logs', $item->id_produk) }}"
                                        class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white px-2 py-2 rounded-lg text-xs font-semibold transition-all shadow-md text-center flex items-center justify-center"
                                        title="Lihat Log Aktivitas">
                                        <i class="fas fa-history"></i>
                                    </a>
                                    <button onclick='editProduk(@json($item))'
                                        class="bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white px-2 py-2 rounded-lg text-xs font-semibold transition-all shadow-md"
                                        title="Edit Produk">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('produk.destroy', $item->id_produk) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Yakin hapus produk ini?')"
                                            class="w-full bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white px-2 py-2 rounded-lg text-xs font-semibold transition-all shadow-md"
                                            title="Hapus Produk">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                    <div
                        class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-box text-4xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-500 font-medium">Belum ada produk</p>
                    <p class="text-gray-400 text-sm mt-1">Tambah produk untuk memulai</p>
                </div>
            @endforelse
        </div>

        <!-- Desktop: Table View -->
        <div class="hidden lg:block bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Gambar
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Kode
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama
                                Produk</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Kategori</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Harga
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Stok
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white" id="produkTableBody">
                        @forelse($produk as $item)
                            <tr data-kategori="{{ $item->id_produk_kategori }}"
                                class="produk-row hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-colors">
                                <td class="px-4 py-3">
                                    @if ($item->gambar_produk)
                                        <img src="{{ asset('uploads/produk/' . $item->gambar_produk) }}"
                                            alt="{{ $item->nama_produk }}"
                                            class="w-12 h-12 object-cover rounded-lg border-2 border-gray-200">
                                    @else
                                        <div
                                            class="w-12 h-12 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg flex items-center justify-center border-2 border-gray-200">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $item->code_produk ?? '-' }}</td>
                                <td class="px-4 py-3 font-semibold text-gray-800">{{ $item->nama_produk }}</td>
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-lg bg-blue-50 text-blue-700 border border-blue-200">
                                        {{ $item->kategori->nama_kategori ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-emerald-600">
                                    Rp {{ number_format($item->harga_produk, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if ($item->stock_produk < 10)
                                        <span
                                            class="inline-flex items-center px-3 py-1 text-xs font-bold rounded-full bg-red-100 text-red-700 border border-red-200">
                                            <i
                                                class="fas fa-exclamation-triangle mr-1"></i>{{ max(0, $item->stock_produk) }}
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-3 py-1 text-xs font-bold rounded-full bg-green-100 text-green-700 border border-green-200">
                                            <i class="fas fa-check-circle mr-1"></i>{{ $item->stock_produk }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('produk.logs', $item->id_produk) }}"
                                            class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white px-3 py-1.5 rounded-lg text-sm font-semibold transition-all shadow-md hover:shadow-lg"
                                            title="Lihat Log Aktivitas">
                                            <i class="fas fa-history"></i>
                                        </a>
                                        <button onclick='editProduk(@json($item))'
                                            class="bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white px-3 py-1.5 rounded-lg text-sm font-semibold transition-all shadow-md hover:shadow-lg"
                                            title="Edit Produk">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('produk.destroy', $item->id_produk) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Yakin hapus produk ini?')"
                                                class="bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white px-3 py-1.5 rounded-lg text-sm font-semibold transition-all shadow-md hover:shadow-lg"
                                                title="Hapus Produk">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mb-3">
                                            <i class="fas fa-box text-4xl text-gray-400"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium">Belum ada produk</p>
                                        <p class="text-gray-400 text-sm mt-1">Tambah produk untuk memulai</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t">
                {{ $produk->links() }}
            </div>
        </div>

        <!-- Mobile Pagination -->
        <div class="block lg:hidden">
            @if ($produk->hasPages())
                <div class="bg-white rounded-xl shadow-lg p-4">
                    {{ $produk->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- MODAL PRODUK                                                  -->
    <!-- ============================================================ -->
    <div id="modalProduk"
        class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl w-full max-w-md my-8 shadow-2xl">
            <div class="p-4 sm:p-6">
                <div class="flex justify-between items-center mb-4 sm:mb-6">
                    <h2 id="modalTitle" class="text-xl sm:text-2xl font-bold text-gray-800">Tambah Produk</h2>
                    <button onclick="closeModal()"
                        class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-times text-xl sm:text-2xl"></i>
                    </button>
                </div>

                <form id="formProduk" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="hidden" id="methodField" name="_method" value="POST">
                    <input type="hidden" id="existingImage" name="existing_image" value="">
                    <input type="hidden" id="hapusGambar" name="hapus_gambar" value="0">
                    {{-- ✅ Hidden: nilai angka murni yang dikirim ke server --}}
                    <input type="hidden" id="harga_produk" name="harga_produk" value="">

                    <!-- Gambar -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2 text-sm">Gambar Produk</label>
                        <div id="imagePreview" class="mb-3 hidden">
                            <div class="relative inline-block">
                                <img id="preview" src="" alt="Preview"
                                    class="w-32 h-32 object-cover rounded-xl border-2 border-gray-300 shadow-md">
                                <button type="button" onclick="removeImage()"
                                    class="absolute -top-2 -right-2 bg-gradient-to-r from-red-500 to-pink-600 text-white rounded-full w-7 h-7 flex items-center justify-center hover:from-red-600 hover:to-pink-700 shadow-lg transform hover:scale-110 transition-all">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Klik ✕ untuk menghapus gambar</p>
                        </div>
                        <input type="file" name="gambar_produk" id="gambar_produk" accept="image/*"
                            class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                            onchange="previewImage(event)">
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-info-circle text-blue-500 mr-1"></i>Format: JPG, JPEG, PNG, GIF (Max: 2MB)
                        </p>
                    </div>

                    <!-- Nama -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2 text-sm">
                            Nama Produk <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_produk" id="nama_produk" required
                            class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm"
                            placeholder="Contoh: Indomie Goreng">
                    </div>

                    <!-- Kode -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2 text-sm">Kode Produk</label>
                        <input type="text" name="code_produk" id="code_produk"
                            class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm"
                            placeholder="Contoh: PRD001">
                    </div>

                    <!-- Kategori -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2 text-sm">Kategori</label>
                        <select name="id_produk_kategori" id="id_produk_kategori"
                            class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm">
                            <option value="">- Pilih Kategori -</option>
                            @foreach ($kategori as $kat)
                                <option value="{{ $kat->id_produk_kategori }}">{{ $kat->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- ✅ Harga dengan format ribuan otomatis -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2 text-sm">
                            Harga <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span
                                class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-sm">Rp</span>
                            {{-- Input display: user ketik di sini, tampil dengan titik ribuan --}}
                            <input type="text" id="harga_produk_display" inputmode="numeric" autocomplete="off"
                                class="w-full pl-12 pr-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm"
                                placeholder="50.000">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">
                            <i class="fas fa-info-circle mr-1 text-blue-400"></i>Titik pemisah ribuan muncul otomatis
                        </p>
                        <p id="harga_error" class="text-xs text-red-500 mt-1 hidden">
                            <i class="fas fa-exclamation-circle mr-1"></i>Harga tidak boleh kosong atau negatif!
                        </p>
                    </div>

                    <!-- Stok -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2 text-sm">
                            Stok <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="stock_produk" id="stock_produk" min="0" required
                            class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm"
                            placeholder="100">
                        <p id="stok_error" class="text-xs text-red-500 mt-1 hidden">
                            <i class="fas fa-exclamation-circle mr-1"></i>Stok tidak boleh kosong atau negatif!
                        </p>
                    </div>

                    <!-- Tombol -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-2">
                        <button type="submit" id="btnSubmit"
                            class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-3 rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:scale-[1.02] active:scale-[0.98] text-sm sm:text-base">
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
            // ============================================================
            // FORMAT HARGA — Titik ribuan otomatis (format Indonesia)
            // ============================================================

            const hargaDisplay = document.getElementById('harga_produk_display');
            const hargaHidden = document.getElementById('harga_produk');

            /**
             * Format angka ke string ribuan Indonesia
             * Contoh: 50000 → "50.000"
             */
            function formatRibuan(angka) {
                if (angka === null || angka === undefined || angka === '') return '';
                const num = parseInt(String(angka).replace(/[^\d]/g, ''));
                if (isNaN(num)) return '';
                return num.toLocaleString('id-ID');
            }

            // Event: saat user mengetik di input harga
            hargaDisplay.addEventListener('input', function() {
                const cursorPos = this.selectionStart;
                const oldLen = this.value.length;

                // Ambil hanya digit
                const angkaStr = this.value.replace(/[^\d]/g, '');

                if (angkaStr === '') {
                    this.value = '';
                    hargaHidden.value = '';
                    resetHargaError();
                    return;
                }

                const angkaInt = parseInt(angkaStr);
                const formatted = angkaInt.toLocaleString('id-ID'); // "50.000"

                this.value = formatted;
                hargaHidden.value = angkaInt;

                // Kembalikan posisi kursor
                const newLen = this.value.length;
                const newPos = Math.max(0, cursorPos + (newLen - oldLen));
                try {
                    this.setSelectionRange(newPos, newPos);
                } catch (e) {}

                resetHargaError();
            });

            // Blokir karakter non-angka
            hargaDisplay.addEventListener('keydown', function(e) {
                const allowed = ['Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown',
                    'Home', 'End'
                ];
                if (allowed.includes(e.key) || e.ctrlKey || e.metaKey) return;
                if (!/^\d$/.test(e.key)) e.preventDefault();
            });

            // Sinkronisasi nilai saat blur
            hargaDisplay.addEventListener('blur', function() {
                const angka = this.value.replace(/[^\d]/g, '');
                hargaHidden.value = angka ? parseInt(angka) : '';
            });

            function resetHargaError() {
                document.getElementById('harga_error').classList.add('hidden');
                hargaDisplay.classList.remove('border-red-500');
            }

            // ============================================================
            // STOK — Paksa tidak negatif, blokir karakter invalid
            // ============================================================
            const stokInput = document.getElementById('stock_produk');

            stokInput.addEventListener('keydown', function(e) {
                if (e.key === '-' || e.key === 'e' || e.key === '+') e.preventDefault();
            });

            stokInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^\d]/g, '');
                if (this.value !== '' && parseInt(this.value) < 0) this.value = 0;
            });

            // ============================================================
            // GAMBAR — Preview & Remove
            // ============================================================
            function removeImage() {
                document.getElementById('preview').src = '';
                document.getElementById('imagePreview').classList.add('hidden');
                document.getElementById('gambar_produk').value = '';
                document.getElementById('hapusGambar').value = '1';
            }

            function previewImage(event) {
                const file = event.target.files[0];
                if (!file) {
                    document.getElementById('imagePreview').classList.add('hidden');
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview').src = e.target.result;
                    document.getElementById('imagePreview').classList.remove('hidden');
                    document.getElementById('hapusGambar').value = '0';
                };
                reader.readAsDataURL(file);
            }

            // ============================================================
            // VALIDASI FORM SEBELUM SUBMIT
            // ============================================================
            document.getElementById('formProduk').addEventListener('submit', function(e) {
                let valid = true;

                const namaInput = document.getElementById('nama_produk');
                const hargaError = document.getElementById('harga_error');
                const stokError = document.getElementById('stok_error');

                // Reset semua error
                hargaError.classList.add('hidden');
                stokError.classList.add('hidden');
                hargaDisplay.classList.remove('border-red-500');
                stokInput.classList.remove('border-red-500');

                // Validasi nama
                if (!namaInput.value.trim()) {
                    namaInput.focus();
                    valid = false;
                }

                // Validasi harga — baca dari hidden (angka murni)
                const hargaVal = hargaHidden.value.trim();
                if (hargaVal === '' || isNaN(parseInt(hargaVal)) || parseInt(hargaVal) < 0) {
                    hargaError.classList.remove('hidden');
                    hargaDisplay.classList.add('border-red-500');
                    if (valid) hargaDisplay.focus();
                    valid = false;
                }

                // Validasi stok
                const stokVal = stokInput.value.trim();
                if (stokVal === '' || isNaN(parseInt(stokVal)) || parseInt(stokVal) < 0) {
                    stokError.classList.remove('hidden');
                    stokInput.classList.add('border-red-500');
                    if (valid) stokInput.focus();
                    valid = false;
                }

                if (!valid) e.preventDefault();
            });

            // ============================================================
            // SEARCH & FILTER TABEL
            // ============================================================
            document.getElementById('searchTable').addEventListener('input', filterProducts);
            document.getElementById('filterKategori').addEventListener('change', filterProducts);

            function filterProducts() {
                const search = document.getElementById('searchTable').value.toLowerCase();
                const kategori = document.getElementById('filterKategori').value;

                document.querySelectorAll('.produk-row').forEach(row => {
                    const matchSearch = row.textContent.toLowerCase().includes(search);
                    const matchKategori = !kategori || row.dataset.kategori === kategori;
                    row.style.display = (matchSearch && matchKategori) ? '' : 'none';
                });

                document.querySelectorAll('.produk-card').forEach(card => {
                    const matchSearch = card.textContent.toLowerCase().includes(search);
                    const matchKategori = !kategori || card.dataset.kategori === kategori;
                    card.style.display = (matchSearch && matchKategori) ? '' : 'none';
                });
            }

            // ============================================================
            // MODAL — Open / Close / Edit
            // ============================================================
            function openModal() {
                document.getElementById('modalTitle').textContent = 'Tambah Produk';
                document.getElementById('formProduk').action = '{{ route('produk.store') }}';
                document.getElementById('methodField').value = 'POST';
                document.getElementById('formProduk').reset();

                // Reset harga
                hargaDisplay.value = '';
                hargaHidden.value = '';
                hargaDisplay.classList.remove('border-red-500');

                // Reset gambar
                document.getElementById('imagePreview').classList.add('hidden');
                document.getElementById('hapusGambar').value = '0';
                document.getElementById('existingImage').value = '';

                // Reset error
                document.getElementById('harga_error').classList.add('hidden');
                document.getElementById('stok_error').classList.add('hidden');
                stokInput.classList.remove('border-red-500');

                document.getElementById('modalProduk').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function editProduk(produk) {
                document.getElementById('modalTitle').textContent = 'Edit Produk';
                document.getElementById('formProduk').action = `/produk/${produk.id_produk}`;
                document.getElementById('methodField').value = 'PUT';

                // Reset form & error
                document.getElementById('formProduk').reset();
                document.getElementById('gambar_produk').value = '';
                document.getElementById('hapusGambar').value = '0';
                document.getElementById('harga_error').classList.add('hidden');
                document.getElementById('stok_error').classList.add('hidden');
                hargaDisplay.classList.remove('border-red-500');
                stokInput.classList.remove('border-red-500');

                // Isi data produk
                document.getElementById('nama_produk').value = produk.nama_produk;
                document.getElementById('code_produk').value = produk.code_produk || '';
                document.getElementById('id_produk_kategori').value = produk.id_produk_kategori || '';

                // ✅ Harga: tampilan berformat + hidden angka murni
                const hargaAsli = parseInt(produk.harga_produk) || 0;
                hargaDisplay.value = formatRibuan(hargaAsli); // "50.000"
                hargaHidden.value = hargaAsli; // 50000

                // ✅ Stok: tampilkan 0 jika negatif
                stokInput.value = Math.max(0, parseInt(produk.stock_produk) || 0);

                // Gambar existing
                if (produk.gambar_produk) {
                    document.getElementById('preview').src = `/uploads/produk/${produk.gambar_produk}`;
                    document.getElementById('imagePreview').classList.remove('hidden');
                    document.getElementById('existingImage').value = produk.gambar_produk;
                } else {
                    document.getElementById('imagePreview').classList.add('hidden');
                    document.getElementById('existingImage').value = '';
                }

                document.getElementById('modalProduk').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeModal() {
                document.getElementById('modalProduk').classList.add('hidden');
                document.body.style.overflow = 'auto';
            }

            // Tutup modal dengan ESC
            document.addEventListener('keydown', e => {
                if (e.key === 'Escape') closeModal();
            });

            // Tutup modal klik di luar
            document.getElementById('modalProduk').addEventListener('click', e => {
                if (e.target.id === 'modalProduk') closeModal();
            });
        </script>
    @endpush
@endsection
