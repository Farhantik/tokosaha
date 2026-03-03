@extends('layouts.app')

@section('title', 'Produk - Toko Sahabat')
@section('page-title', 'Manajemen Produk')

@section('content')
    <div class="space-y-4 sm:space-y-6">

        {{-- Alert --}}
        @if (session('success'))
            <div
                class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm">
                <i class="fas fa-check-circle text-green-500 text-lg"></i>
                <span class="font-medium text-sm">{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm">
                <i class="fas fa-exclamation-circle text-red-500 text-lg"></i>
                <span class="font-medium text-sm">{{ session('error') }}</span>
            </div>
        @endif

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
            <div class="flex flex-col sm:flex-row w-full sm:w-auto gap-2">
                <button onclick="openModalKategori()"
                    class="w-full sm:w-auto bg-gradient-to-r from-purple-500 to-violet-600 hover:from-purple-600 hover:to-violet-700 text-white px-4 sm:px-5 py-2.5 sm:py-3 rounded-xl font-semibold transition-all shadow-lg hover:shadow-xl transform hover:scale-[1.02] active:scale-[0.98] text-sm sm:text-base">
                    <i class="fas fa-tags mr-2"></i>Tambah Kategori
                </button>
                <button onclick="openModal()"
                    class="w-full sm:w-auto bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-4 sm:px-6 py-2.5 sm:py-3 rounded-xl font-semibold transition-all shadow-lg hover:shadow-xl transform hover:scale-[1.02] active:scale-[0.98] text-sm sm:text-base">
                    <i class="fas fa-plus mr-2"></i>Tambah Produk
                </button>
            </div>
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
    <!-- MODAL TAMBAH / EDIT PRODUK                                    -->
    <!-- ============================================================ -->
    <div id="modalProduk"
        class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl w-full max-w-2xl my-8 shadow-2xl">
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
                    <input type="hidden" id="harga_produk" name="harga_produk" value="">

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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2 text-sm">
                                Nama Produk <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama_produk" id="nama_produk" required
                                class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm"
                                placeholder="Contoh: Indomie Goreng">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2 text-sm">Kode Produk</label>
                            <input type="text" name="code_produk" id="code_produk"
                                class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm"
                                placeholder="Contoh: PRD001">
                        </div>
                    </div>

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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2 text-sm">
                                Harga <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span
                                    class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-sm">Rp</span>
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
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2 text-sm">Deskripsi Produk</label>
                        <textarea name="deskripsi_produk" id="deskripsi_produk" rows="4"
                            class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm resize-none"
                            placeholder="Contoh: Air mineral berkualitas tinggi..."></textarea>
                        <div class="flex items-center justify-between mt-1">
                            <p class="text-xs text-gray-400">
                                <i class="fas fa-info-circle mr-1 text-blue-400"></i>Opsional
                            </p>
                            <p class="text-xs text-gray-400"><span id="charCount" class="font-semibold">0</span>/500
                                karakter</p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 pt-2">
                        <button type="submit"
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

    <!-- ============================================================ -->
    <!-- MODAL TAMBAH / EDIT KATEGORI                                  -->
    <!-- ============================================================ -->
    <div id="modalKategori" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl">
            <div class="p-5 sm:p-6">
                <div class="flex justify-between items-center mb-5">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-purple-500 to-violet-600 rounded-xl flex items-center justify-center shadow-md">
                            <i class="fas fa-tags text-white"></i>
                        </div>
                        <h2 id="modalKategoriTitle" class="text-xl font-bold text-gray-800">Tambah Kategori</h2>
                    </div>
                    <button onclick="closeModalKategori()"
                        class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                {{-- Form kategori (store/update) — TIDAK ada form hapus di sini --}}
                <form id="formKategori" method="POST" action="{{ route('kategori.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" id="kategoriMethodField" name="_method" value="POST">

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2 text-sm">
                            Nama Kategori <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_kategori" id="nama_kategori"
                            class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all text-sm"
                            placeholder="Contoh: Minuman, Makanan, Snack...">
                        <p id="nama_kategori_error" class="text-xs text-red-500 mt-1 hidden">
                            <i class="fas fa-exclamation-circle mr-1"></i>Nama kategori wajib diisi!
                        </p>
                    </div>

                    <!-- Daftar Kategori yang Ada -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2 text-sm">Kategori yang Ada</label>
                        <div class="border-2 border-gray-200 rounded-xl overflow-hidden max-h-52 overflow-y-auto">
                            @forelse($kategori as $kat)
                                <div
                                    class="flex items-center justify-between px-4 py-2.5 hover:bg-purple-50 transition-colors border-b border-gray-100 last:border-0">
                                    <span class="text-sm font-medium text-gray-700">
                                        <i class="fas fa-tag text-purple-400 mr-2 text-xs"></i>{{ $kat->nama_kategori }}
                                    </span>
                                    <div class="flex gap-1.5">
                                        <button type="button"
                                            onclick="editKategori({{ $kat->id_produk_kategori }}, '{{ addslashes($kat->nama_kategori) }}')"
                                            class="bg-yellow-100 hover:bg-yellow-500 text-yellow-600 hover:text-white w-7 h-7 rounded-lg flex items-center justify-center transition-all text-xs"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button"
                                            onclick="hapusKategori({{ $kat->id_produk_kategori }}, '{{ addslashes($kat->nama_kategori) }}')"
                                            class="bg-red-100 hover:bg-red-500 text-red-600 hover:text-white w-7 h-7 rounded-lg flex items-center justify-center transition-all text-xs"
                                            title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="px-4 py-6 text-center text-gray-400 text-sm">
                                    <i class="fas fa-inbox text-2xl mb-2 block"></i>
                                    Belum ada kategori
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="flex gap-3 pt-1">
                        <button type="submit" id="btnSubmitKategori"
                            class="flex-1 bg-gradient-to-r from-purple-500 to-violet-600 hover:from-purple-600 hover:to-violet-700 text-white font-bold py-2.5 rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:scale-[1.02] active:scale-[0.98] text-sm">
                            <i class="fas fa-save mr-2"></i>Simpan
                        </button>
                        <button type="button" onclick="closeModalKategori()"
                            class="flex-1 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-bold py-2.5 rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:scale-[1.02] active:scale-[0.98] text-sm">
                            <i class="fas fa-times mr-2"></i>Tutup
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ✅ Form hapus kategori — di LUAR semua modal agar tidak nested --}}
    <form id="formHapusKategori" method="POST" action="" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    <!-- ============================================================ -->
    <!-- MODAL KONFIRMASI HAPUS KATEGORI (Custom — mengganti confirm()) -->
    <!-- ============================================================ -->
    <div id="modalKonfirmasiHapus"
        class="fixed inset-0 bg-black bg-opacity-60 hidden z-[60] flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-sm shadow-2xl">
            <div class="p-6">

                <!-- Icon -->
                <div class="flex justify-center mb-4">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-red-100 to-pink-100 rounded-full flex items-center justify-center border-4 border-red-200 shadow-inner">
                        <i class="fas fa-trash-alt text-2xl text-red-500"></i>
                    </div>
                </div>

                <!-- Title -->
                <h3 class="text-xl font-bold text-gray-800 text-center mb-1">Hapus Kategori?</h3>
                <p class="text-gray-500 text-sm text-center mb-3">Anda akan menghapus kategori:</p>

                <!-- Nama Kategori -->
                <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-2.5 mb-4 text-center">
                    <p id="konfirmasiNamaKategori" class="font-bold text-gray-800 text-base"></p>
                </div>

                <!-- Warning -->
                <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-2.5 mb-5 flex items-start gap-2">
                    <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5 flex-shrink-0 text-sm"></i>
                    <p class="text-xs text-amber-700 leading-relaxed">
                        Kategori yang masih dipakai oleh produk <strong>tidak bisa dihapus</strong>.
                        Pastikan tidak ada produk yang menggunakan kategori ini.
                    </p>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3">
                    <button type="button" onclick="closeKonfirmasiHapus()"
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 rounded-xl transition-all text-sm border-2 border-gray-200 hover:border-gray-300">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    <button type="button" onclick="konfirmasiHapusKategori()"
                        class="flex-1 bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white font-bold py-3 rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:scale-[1.02] active:scale-[0.98] text-sm">
                        <i class="fas fa-trash mr-2"></i>Ya, Hapus
                    </button>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // ============================================================
            // FORMAT HARGA
            // ============================================================
            const hargaDisplay = document.getElementById('harga_produk_display');
            const hargaHidden = document.getElementById('harga_produk');

            function formatRibuan(angka) {
                if (angka === null || angka === undefined || angka === '') return '';
                const num = parseInt(String(angka).replace(/[^\d]/g, ''));
                return isNaN(num) ? '' : num.toLocaleString('id-ID');
            }

            hargaDisplay.addEventListener('input', function() {
                const cursorPos = this.selectionStart;
                const oldLen = this.value.length;
                const angkaStr = this.value.replace(/[^\d]/g, '');
                if (angkaStr === '') {
                    this.value = '';
                    hargaHidden.value = '';
                    resetHargaError();
                    return;
                }
                const angkaInt = parseInt(angkaStr);
                const formatted = angkaInt.toLocaleString('id-ID');
                this.value = formatted;
                hargaHidden.value = angkaInt;
                const newLen = this.value.length;
                const newPos = Math.max(0, cursorPos + (newLen - oldLen));
                try {
                    this.setSelectionRange(newPos, newPos);
                } catch (e) {}
                resetHargaError();
            });

            hargaDisplay.addEventListener('keydown', function(e) {
                const allowed = ['Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown',
                    'Home', 'End'
                ];
                if (allowed.includes(e.key) || e.ctrlKey || e.metaKey) return;
                if (!/^\d$/.test(e.key)) e.preventDefault();
            });

            hargaDisplay.addEventListener('blur', function() {
                const angka = this.value.replace(/[^\d]/g, '');
                hargaHidden.value = angka ? parseInt(angka) : '';
            });

            function resetHargaError() {
                document.getElementById('harga_error').classList.add('hidden');
                hargaDisplay.classList.remove('border-red-500');
            }

            // ============================================================
            // STOK
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
            // DESKRIPSI — Character Counter
            // ============================================================
            const deskripsiInput = document.getElementById('deskripsi_produk');
            const charCount = document.getElementById('charCount');
            const maxChars = 500;

            deskripsiInput.addEventListener('input', function() {
                const len = this.value.length;
                if (len > maxChars) this.value = this.value.substring(0, maxChars);
                charCount.textContent = Math.min(len, maxChars);
                charCount.className = len > maxChars * 0.9 ? 'font-semibold text-red-600' :
                    len > maxChars * 0.7 ? 'font-semibold text-orange-600' : 'font-semibold';
            });

            // ============================================================
            // GAMBAR
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
                reader.onload = e => {
                    document.getElementById('preview').src = e.target.result;
                    document.getElementById('imagePreview').classList.remove('hidden');
                    document.getElementById('hapusGambar').value = '0';
                };
                reader.readAsDataURL(file);
            }

            // ============================================================
            // VALIDASI FORM PRODUK
            // ============================================================
            document.getElementById('formProduk').addEventListener('submit', function(e) {
                let valid = true;
                document.getElementById('harga_error').classList.add('hidden');
                document.getElementById('stok_error').classList.add('hidden');
                hargaDisplay.classList.remove('border-red-500');
                stokInput.classList.remove('border-red-500');

                const namaInput = document.getElementById('nama_produk');
                if (!namaInput.value.trim()) {
                    namaInput.focus();
                    valid = false;
                }

                const hargaVal = hargaHidden.value.trim();
                if (hargaVal === '' || isNaN(parseInt(hargaVal)) || parseInt(hargaVal) < 0) {
                    document.getElementById('harga_error').classList.remove('hidden');
                    hargaDisplay.classList.add('border-red-500');
                    if (valid) hargaDisplay.focus();
                    valid = false;
                }

                const stokVal = stokInput.value.trim();
                if (stokVal === '' || isNaN(parseInt(stokVal)) || parseInt(stokVal) < 0) {
                    document.getElementById('stok_error').classList.remove('hidden');
                    stokInput.classList.add('border-red-500');
                    if (valid) stokInput.focus();
                    valid = false;
                }

                if (!valid) e.preventDefault();
            });

            // ============================================================
            // SEARCH & FILTER
            // ============================================================
            document.getElementById('searchTable').addEventListener('input', filterProducts);
            document.getElementById('filterKategori').addEventListener('change', filterProducts);

            function filterProducts() {
                const search = document.getElementById('searchTable').value.toLowerCase();
                const kategori = document.getElementById('filterKategori').value;
                document.querySelectorAll('.produk-row').forEach(row => {
                    row.style.display = (row.textContent.toLowerCase().includes(search) &&
                        (!kategori || row.dataset.kategori === kategori)) ? '' : 'none';
                });
                document.querySelectorAll('.produk-card').forEach(card => {
                    card.style.display = (card.textContent.toLowerCase().includes(search) &&
                        (!kategori || card.dataset.kategori === kategori)) ? '' : 'none';
                });
            }

            // ============================================================
            // MODAL PRODUK
            // ============================================================
            function openModal() {
                document.getElementById('modalTitle').textContent = 'Tambah Produk';
                document.getElementById('formProduk').action = '{{ route('produk.store') }}';
                document.getElementById('methodField').value = 'POST';
                document.getElementById('formProduk').reset();
                hargaDisplay.value = '';
                hargaHidden.value = '';
                hargaDisplay.classList.remove('border-red-500');
                document.getElementById('imagePreview').classList.add('hidden');
                document.getElementById('hapusGambar').value = '0';
                document.getElementById('existingImage').value = '';
                deskripsiInput.value = '';
                charCount.textContent = '0';
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
                document.getElementById('formProduk').reset();
                document.getElementById('gambar_produk').value = '';
                document.getElementById('hapusGambar').value = '0';
                document.getElementById('harga_error').classList.add('hidden');
                document.getElementById('stok_error').classList.add('hidden');
                hargaDisplay.classList.remove('border-red-500');
                stokInput.classList.remove('border-red-500');

                document.getElementById('nama_produk').value = produk.nama_produk;
                document.getElementById('code_produk').value = produk.code_produk || '';
                document.getElementById('id_produk_kategori').value = produk.id_produk_kategori || '';

                const hargaAsli = parseInt(produk.harga_produk) || 0;
                hargaDisplay.value = formatRibuan(hargaAsli);
                hargaHidden.value = hargaAsli;
                stokInput.value = Math.max(0, parseInt(produk.stock_produk) || 0);

                const deskripsi = produk.deskripsi_produk || '';
                deskripsiInput.value = deskripsi;
                charCount.textContent = deskripsi.length;

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

            // ============================================================
            // MODAL KATEGORI
            // ============================================================
            const ROUTE_KATEGORI_STORE = '{{ route('kategori.store') }}';

            function openModalKategori() {
                document.getElementById('modalKategoriTitle').textContent = 'Tambah Kategori';
                document.getElementById('formKategori').setAttribute('action', ROUTE_KATEGORI_STORE);
                document.getElementById('kategoriMethodField').value = 'POST';
                document.getElementById('nama_kategori').value = '';
                document.getElementById('nama_kategori_error').classList.add('hidden');
                document.getElementById('nama_kategori').classList.remove('border-red-500');
                document.getElementById('btnSubmitKategori').innerHTML = '<i class="fas fa-save mr-2"></i>Simpan';
                document.getElementById('modalKategori').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                setTimeout(() => document.getElementById('nama_kategori').focus(), 100);
            }

            function editKategori(id, nama) {
                document.getElementById('modalKategoriTitle').textContent = 'Edit Kategori';
                document.getElementById('formKategori').setAttribute('action', `/kategori/${id}`);
                document.getElementById('kategoriMethodField').value = 'PUT';
                document.getElementById('nama_kategori').value = nama;
                document.getElementById('nama_kategori_error').classList.add('hidden');
                document.getElementById('nama_kategori').classList.remove('border-red-500');
                document.getElementById('btnSubmitKategori').innerHTML = '<i class="fas fa-save mr-2"></i>Update';
                document.getElementById('modalKategori').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                setTimeout(() => document.getElementById('nama_kategori').focus(), 100);
            }

            function closeModalKategori() {
                document.getElementById('formKategori').setAttribute('action', ROUTE_KATEGORI_STORE);
                document.getElementById('kategoriMethodField').value = 'POST';
                document.getElementById('modalKategori').classList.add('hidden');
                document.body.style.overflow = 'auto';
            }

            // ============================================================
            // MODAL KONFIRMASI HAPUS KATEGORI (Custom)
            // ============================================================
            let hapusKategoriId = null;

            function hapusKategori(id, nama) {
                hapusKategoriId = id;
                document.getElementById('konfirmasiNamaKategori').textContent = nama;
                document.getElementById('modalKonfirmasiHapus').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeKonfirmasiHapus() {
                hapusKategoriId = null;
                document.getElementById('modalKonfirmasiHapus').classList.add('hidden');
            }

            function konfirmasiHapusKategori() {
                if (!hapusKategoriId) return;
                const form = document.getElementById('formHapusKategori');
                form.setAttribute('action', `/kategori/${hapusKategoriId}`);
                form.submit();
            }

            // Validasi form kategori
            document.getElementById('formKategori').addEventListener('submit', function(e) {
                const namaKat = document.getElementById('nama_kategori');
                const namaKatError = document.getElementById('nama_kategori_error');
                namaKatError.classList.add('hidden');
                namaKat.classList.remove('border-red-500');
                if (!namaKat.value.trim()) {
                    namaKatError.classList.remove('hidden');
                    namaKat.classList.add('border-red-500');
                    namaKat.focus();
                    e.preventDefault();
                }
            });

            // ESC tutup semua modal
            document.addEventListener('keydown', e => {
                if (e.key === 'Escape') {
                    closeKonfirmasiHapus();
                    closeModal();
                    closeModalKategori();
                }
            });

            // Klik luar modal produk
            document.getElementById('modalProduk').addEventListener('click', e => {
                if (e.target.id === 'modalProduk') closeModal();
            });

            // Klik luar modal kategori
            document.getElementById('modalKategori').addEventListener('click', e => {
                if (e.target.id === 'modalKategori') closeModalKategori();
            });

            // Klik luar modal konfirmasi hapus
            document.getElementById('modalKonfirmasiHapus').addEventListener('click', e => {
                if (e.target.id === 'modalKonfirmasiHapus') closeKonfirmasiHapus();
            });
        </script>
    @endpush
@endsection
