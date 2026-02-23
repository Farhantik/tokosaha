@extends('layouts.app')

@section('title', 'Transaksi - WPOS')
@section('page-title', 'Transaksi Kasir')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        .category-btn.active {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .product-detail-modal {
            backdrop-filter: blur(8px);
        }

        .payment-type-card {
            transition: all 0.3s ease;
            cursor: pointer;
            border: 3px solid transparent;
        }

        .payment-type-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .payment-type-card.active {
            border-color: #10b981;
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        }

        .piutang-filter-btn {
            transition: all 0.3s ease;
        }

        .piutang-filter-btn.active {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
        }

        .payment-row {
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            background: #f9fafb;
        }

        .payment-row .payment-method-select {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .pay-method-btn {
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 0.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            background: white;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .pay-method-btn:hover {
            border-color: #10b981;
        }

        .pay-method-btn.active {
            border-color: #10b981;
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        }

        .pay-method-btn.active-qris {
            border-color: #6366f1;
            background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
        }

        .pay-method-btn.active-piutang {
            border-color: #f97316;
            background: linear-gradient(135deg, #ffedd5, #fed7aa);
        }
    </style>
@endpush

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-lg px-6 py-4">
            <div class="flex flex-wrap items-center justify-between gap-4">

                <!-- Title -->
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                    <i class="fas fa-cash-register mr-2 text-emerald-600"></i>Transaksi Kasir
                </h1>

                <!-- Right Side Buttons -->
                <div class="flex flex-wrap items-center gap-3">

                    @if (!Auth::user()->isOwner())
                        <!-- Button Dashboard -->
                        <a href="{{ route('dashboard') }}"
                            class="flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm transition-all shadow-sm
                            {{ request()->routeIs('dashboard') ? 'bg-gray-700 text-white shadow-md' : 'bg-gray-100 hover:bg-gray-700 text-gray-600 hover:text-white' }}">
                            <i class="fas fa-home"></i>
                            <span class="hidden sm:inline">Dashboard</span>
                        </a>

                        <!-- Button Kasir -->
                        <a href="{{ route('kasir.index') }}"
                            class="flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm transition-all shadow-sm
                            {{ request()->routeIs('kasir.*') ? 'bg-blue-600 text-white shadow-md' : 'bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white' }}">
                            <i class="fas fa-cash-register"></i>
                            <span class="hidden sm:inline">Buka/Tutup Kasir</span>
                        </a>

                        <!-- Button Laporan -->
                        <a href="{{ route('laporan.index') }}"
                            class="flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm transition-all shadow-sm
                            {{ request()->routeIs('laporan.*') && !request()->routeIs('keuangan.*') ? 'bg-indigo-600 text-white shadow-md' : 'bg-indigo-50 hover:bg-indigo-600 text-indigo-600 hover:text-white' }}">
                            <i class="fas fa-chart-line"></i>
                            <span class="hidden sm:inline">Laporan</span>
                        </a>

                        <!-- Divider -->
                        <div class="w-px h-8 bg-gray-200 hidden sm:block mx-1"></div>
                    @endif

                    <!-- Button Daftar Piutang -->
                    <button id="btnShowPiutang"
                        class="flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm transition-all shadow-sm bg-orange-50 hover:bg-orange-600 text-orange-600 hover:text-white">
                        <i class="fas fa-clock"></i>
                        <span class="hidden sm:inline">Daftar Piutang</span>
                        <span class="sm:hidden">Piutang</span>
                    </button>

                    <!-- Button Keranjang (Mobile Only) -->
                    <button id="btnShowCart"
                        class="lg:hidden flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm transition-all shadow-sm bg-emerald-50 hover:bg-emerald-600 text-emerald-600 hover:text-white">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Keranjang</span>
                        <span id="cartBadge"
                            class="bg-emerald-600 text-white px-2 py-0.5 rounded-full text-xs font-bold">0</span>
                    </button>

                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Daftar Produk -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-lg p-4 md:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-3">
                                <i class="fas fa-box text-white"></i>
                            </div>
                            Daftar Produk
                        </h2>
                    </div>

                    <!-- Category Filter Dropdown -->
                    <div class="mb-4">
                        <div class="relative inline-block w-full sm:w-auto">
                            <button id="categoryDropdownBtn" onclick="toggleCategoryDropdown()"
                                class="w-full sm:w-auto flex items-center justify-between gap-3 px-5 py-2.5 bg-white border-2 border-gray-200 hover:border-emerald-500 rounded-xl font-semibold text-sm text-gray-700 transition-all shadow-sm min-w-[200px]">
                                <span class="flex items-center gap-2">
                                    <i class="fas fa-th-large text-emerald-600" id="categoryIcon"></i>
                                    <span id="categoryLabel">Semua Kategori</span>
                                </span>
                                <i class="fas fa-chevron-down text-gray-400 transition-transform duration-200"
                                    id="categoryChevron"></i>
                            </button>

                            <!-- Dropdown Menu -->
                            <div id="categoryDropdown"
                                class="hidden absolute left-0 top-full mt-2 bg-white border border-gray-200 rounded-xl shadow-xl z-30 min-w-[220px] overflow-hidden">

                                <button
                                    class="category-btn active w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-all"
                                    data-category="all" data-label="Semua Kategori" data-icon="fa-th-large"
                                    onclick="selectCategory(this)">
                                    <i class="fas fa-th-large text-emerald-600 w-5"></i>
                                    <span>Semua</span>
                                </button>

                                <div class="border-t border-gray-100"></div>

                                <button
                                    class="category-btn w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-all"
                                    data-category="makanan" data-label="Makanan" data-icon="fa-utensils"
                                    onclick="selectCategory(this)">
                                    <i class="fas fa-utensils text-orange-500 w-5"></i>
                                    <span>Makanan</span>
                                </button>

                                <div class="border-t border-gray-100"></div>

                                <button
                                    class="category-btn w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-all"
                                    data-category="minuman" data-label="Minuman" data-icon="fa-coffee"
                                    onclick="selectCategory(this)">
                                    <i class="fas fa-coffee text-blue-500 w-5"></i>
                                    <span>Minuman</span>
                                </button>

                                <div class="border-t border-gray-100"></div>

                                <button
                                    class="category-btn w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-all"
                                    data-category="snack" data-label="Snack" data-icon="fa-cookie-bite"
                                    onclick="selectCategory(this)">
                                    <i class="fas fa-cookie-bite text-yellow-500 w-5"></i>
                                    <span>Snack</span>
                                </button>

                                <div class="border-t border-gray-100"></div>

                                <button
                                    class="category-btn w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-all"
                                    data-category="kebutuhan rumah tangga" data-label="Kebutuhan RT" data-icon="fa-home"
                                    onclick="selectCategory(this)">
                                    <i class="fas fa-home text-purple-500 w-5"></i>
                                    <span>Kebutuhan RT</span>
                                </button>

                            </div>
                        </div>
                    </div>

                    <!-- Search -->
                    <div class="mb-4">
                        <div class="relative">
                            <input type="text" id="searchProduk"
                                class="w-full pl-12 pr-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:outline-none transition-all"
                                placeholder="Cari produk...">
                            <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>

                    <!-- Product Grid -->
                    <div id="produkList"
                        class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 max-h-[calc(100vh-24rem)] overflow-y-auto pr-2">
                        @foreach ($produk as $item)
                            <div class="produk-item group border-2 border-gray-200 rounded-xl p-3 hover:border-emerald-500 hover:shadow-xl transition-all duration-300 {{ $item->stock_produk <= 0 ? 'opacity-50' : '' }}"
                                data-id="{{ $item->id_produk }}" data-nama="{{ $item->nama_produk }}"
                                data-harga="{{ $item->harga_produk }}" data-stok="{{ $item->stock_produk }}"
                                data-kategori="{{ strtolower($item->kategori_produk ?? 'lainnya') }}"
                                data-kode="{{ $item->kode_produk ?? '-' }}"
                                data-deskripsi="{{ $item->deskripsi_produk ?? 'Tidak ada deskripsi' }}"
                                data-gambar="{{ $item->gambar_produk ?? '' }}">
                                <div class="text-center relative">
                                    <button
                                        class="info-btn absolute top-1 right-1 bg-blue-500 text-white w-6 h-6 rounded-full flex items-center justify-center hover:bg-blue-600 transition-colors z-10 opacity-0 group-hover:opacity-100"
                                        data-product-id="{{ $item->id_produk }}">
                                        <i class="fas fa-info text-xs"></i>
                                    </button>

                                    @if ($item->gambar_produk)
                                        <img src="{{ asset('uploads/produk/' . rawurlencode($item->gambar_produk)) }}"
                                            alt="{{ $item->nama_produk }}"
                                            class="w-full h-20 object-cover rounded-lg mb-2 group-hover:scale-105 transition-transform"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="bg-gradient-to-br from-blue-100 to-indigo-100 rounded-lg p-4 mb-2 h-20 items-center justify-center"
                                            style="display:none;">
                                            <i class="fas fa-box text-3xl text-blue-600"></i>
                                        </div>
                                    @else
                                        <div
                                            class="bg-gradient-to-br from-blue-100 to-indigo-100 rounded-lg p-4 mb-2 h-20 flex items-center justify-center group-hover:from-blue-200 group-hover:to-indigo-200 transition-colors">
                                            <i class="fas fa-box text-3xl text-blue-600"></i>
                                        </div>
                                    @endif

                                    <h3 class="font-semibold text-xs mb-1 line-clamp-2 text-gray-800">
                                        {{ $item->nama_produk }}</h3>
                                    <p class="text-emerald-600 font-bold text-sm">Rp
                                        {{ number_format($item->harga_produk, 0, ',', '.') }}</p>
                                    <p
                                        class="text-xs mt-1 {{ $item->stock_produk <= 10 ? 'text-red-600 font-bold' : 'text-gray-500' }}">
                                        Stok: {{ $item->stock_produk }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Keranjang - Desktop -->
            <div class="hidden lg:block">
                <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center mr-3">
                                <i class="fas fa-shopping-cart text-white"></i>
                            </div>
                            Keranjang
                        </h2>
                        <span id="cartCount"
                            class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-sm font-bold">0</span>
                    </div>

                    <!-- Cart Items -->
                    <div id="cartItems" class="space-y-2 mb-4 max-h-64 overflow-y-auto">
                        <div class="text-center py-8">
                            <i class="fas fa-shopping-basket text-4xl text-gray-300 mb-2"></i>
                            <p class="text-gray-500 text-sm">Keranjang kosong</p>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="border-t-2 border-gray-200 pt-4 space-y-3">
                        <div class="bg-gradient-to-r from-emerald-50 to-green-50 rounded-xl p-3">
                            <div class="flex justify-between text-lg font-bold text-gray-800">
                                <span>Total:</span>
                                <span id="totalAmount" class="text-emerald-600">Rp 0</span>
                            </div>
                        </div>

                        <!-- Payment Section Desktop -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-gray-700 font-semibold text-sm">Metode Pembayaran</label>
                            </div>
                            <div id="paymentRowsDesktop" class="space-y-2">
                                <!-- Rows injected by JS -->
                            </div>
                            <button id="btnAddPaymentDesktop"
                                class="w-full mt-2 flex items-center justify-center gap-2 px-4 py-2 border-2 border-dashed border-emerald-400 text-emerald-600 rounded-xl text-sm font-semibold hover:bg-emerald-50 transition hidden">
                                <i class="fas fa-plus"></i> Tambah Metode Bayar
                            </button>
                        </div>

                        <!-- Kembalian -->
                        <div id="kembalianSectionDesktop"
                            class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-3">
                            <div class="flex justify-between text-lg">
                                <span class="text-gray-700 font-semibold">Kembalian:</span>
                                <span id="kembalian" class="font-bold text-blue-600">Rp 0</span>
                            </div>
                        </div>

                        <!-- Info Piutang -->
                        <div id="infoPiutang" class="hidden">
                            <div class="bg-gradient-to-r from-orange-50 to-red-50 rounded-xl p-4">
                                <div class="flex items-start">
                                    <i class="fas fa-exclamation-circle text-orange-600 text-xl mr-3 mt-1"></i>
                                    <div>
                                        <p class="font-bold text-gray-800 mb-1">Mode Piutang</p>
                                        <p class="text-sm text-gray-600">Transaksi akan dicatat sebagai piutang dengan
                                            status <span class="font-bold text-orange-600">Belum Bayar</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button id="btnProses" disabled
                            class="w-full bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white font-bold py-3 rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:scale-[1.02] disabled:from-gray-400 disabled:to-gray-500 disabled:cursor-not-allowed disabled:transform-none">
                            <i class="fas fa-check-circle mr-2"></i>Proses Transaksi
                        </button>

                        <button id="btnReset"
                            class="w-full bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white font-bold py-2.5 rounded-xl transition-all shadow-md hover:shadow-lg transform hover:scale-[1.02]">
                            <i class="fas fa-trash-alt mr-2"></i>Reset Keranjang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Cart Modal -->
    <div id="cartModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden lg:hidden">
        <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl shadow-2xl max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-shopping-cart text-emerald-600 mr-2"></i>
                        Keranjang Belanja
                    </h2>
                    <button id="btnCloseCart" class="text-gray-500 hover:text-gray-700 text-2xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div id="cartItemsMobile" class="space-y-2 mb-4 max-h-48 overflow-y-auto">
                    <div class="text-center py-8">
                        <i class="fas fa-shopping-basket text-4xl text-gray-300 mb-2"></i>
                        <p class="text-gray-500 text-sm">Keranjang kosong</p>
                    </div>
                </div>

                <div class="border-t-2 border-gray-200 pt-4 space-y-3">
                    <div class="bg-gradient-to-r from-emerald-50 to-green-50 rounded-xl p-3">
                        <div class="flex justify-between text-lg font-bold text-gray-800">
                            <span>Total:</span>
                            <span id="totalAmountMobile" class="text-emerald-600">Rp 0</span>
                        </div>
                    </div>

                    <!-- Payment Section Mobile -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-gray-700 font-semibold text-sm">Metode Pembayaran</label>
                        </div>
                        <div id="paymentRowsMobile" class="space-y-2">
                            <!-- Rows injected by JS -->
                        </div>
                        <button id="btnAddPaymentMobile"
                            class="w-full mt-2 flex items-center justify-center gap-2 px-4 py-2 border-2 border-dashed border-emerald-400 text-emerald-600 rounded-xl text-sm font-semibold hover:bg-emerald-50 transition hidden">
                            <i class="fas fa-plus"></i> Tambah Metode Bayar
                        </button>
                    </div>

                    <!-- Kembalian Mobile -->
                    <div id="kembalianSectionMobile" class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-3">
                        <div class="flex justify-between text-lg">
                            <span class="text-gray-700 font-semibold">Kembalian:</span>
                            <span id="kembalianMobile" class="font-bold text-blue-600">Rp 0</span>
                        </div>
                    </div>

                    <!-- Info Piutang Mobile -->
                    <div id="infoPiutangMobile" class="hidden">
                        <div class="bg-gradient-to-r from-orange-50 to-red-50 rounded-xl p-3">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-circle text-orange-600 mr-2 mt-1"></i>
                                <div>
                                    <p class="font-bold text-gray-800 text-sm mb-1">Mode Piutang</p>
                                    <p class="text-xs text-gray-600">Transaksi dicatat sebagai <span
                                            class="font-bold text-orange-600">Belum Bayar</span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button id="btnProsesMobile" disabled
                        class="w-full bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white font-bold py-3 rounded-xl transition-all shadow-lg disabled:from-gray-400 disabled:to-gray-500 disabled:cursor-not-allowed">
                        <i class="fas fa-check-circle mr-2"></i>Proses Transaksi
                    </button>

                    <button id="btnResetMobile"
                        class="w-full bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white font-bold py-2.5 rounded-xl transition-all shadow-md">
                        <i class="fas fa-trash-alt mr-2"></i>Reset Keranjang
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Detail Modal -->
    <div id="productDetailModal" class="fixed inset-0 bg-black bg-opacity-60 z-50 hidden product-detail-modal">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-2xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-info-circle text-blue-500 mr-3"></i>
                            Detail Produk
                        </h3>
                        <button id="closeProductDetailBtn" class="text-gray-500 hover:text-gray-700 text-2xl">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div id="productDetailContent" class="space-y-4"></div>
                    <div class="mt-6 flex gap-3">
                        <button id="addToCartFromDetailBtn"
                            class="flex-1 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white font-bold py-3 rounded-xl transition-all">
                            <i class="fas fa-cart-plus mr-2"></i>Tambah ke Keranjang
                        </button>
                        <button id="closeDetailBtn"
                            class="px-6 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 rounded-xl transition-all">
                            <i class="fas fa-times mr-2"></i>Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Piutang Modal -->
    <div id="piutangModal" class="fixed inset-0 bg-black bg-opacity-60 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
                <div class="bg-gradient-to-r from-orange-500 to-red-600 p-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-2xl font-bold text-white flex items-center">
                            <i class="fas fa-clock mr-3"></i>Daftar Piutang
                        </h3>
                        <button id="closePiutangModalBtn" class="text-white hover:text-gray-200 text-2xl">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                    <div class="mb-4 flex gap-2">
                        <button
                            class="piutang-filter-btn active px-4 py-2 rounded-xl bg-orange-100 text-orange-700 font-semibold text-sm"
                            data-status="all">Semua</button>
                        <button
                            class="piutang-filter-btn px-4 py-2 rounded-xl bg-gray-100 text-gray-700 font-semibold text-sm"
                            data-status="belum_bayar">Belum Bayar</button>
                        <button
                            class="piutang-filter-btn px-4 py-2 rounded-xl bg-gray-100 text-gray-700 font-semibold text-sm"
                            data-status="bayar_sebagian">Bayar Sebagian</button>
                        <button
                            class="piutang-filter-btn px-4 py-2 rounded-xl bg-gray-100 text-gray-700 font-semibold text-sm"
                            data-status="lunas">Lunas</button>
                    </div>
                    <div id="piutangList" class="space-y-3">
                        <div class="text-center py-8">
                            <i class="fas fa-spinner fa-spin text-4xl text-gray-300 mb-2"></i>
                            <p class="text-gray-500">Memuat data...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Piutang Modal -->
    <div id="detailPiutangModal" class="fixed inset-0 bg-black bg-opacity-60 z-[60] hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-2xl font-bold text-gray-800">Detail Piutang</h3>
                        <button id="closeDetailPiutangBtn" class="text-gray-500 hover:text-gray-700 text-2xl">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div id="detailPiutangContent"></div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // ====================================================================
        // CATEGORY DROPDOWN FUNCTIONS
        // ====================================================================
        function toggleCategoryDropdown() {
            const dropdown = document.getElementById('categoryDropdown');
            const chevron = document.getElementById('categoryChevron');
            dropdown.classList.toggle('hidden');
            chevron.classList.toggle('rotate-180');
        }

        function selectCategory(btn) {
            document.getElementById('categoryLabel').textContent = btn.dataset.label;
            document.getElementById('categoryIcon').className = `fas ${btn.dataset.icon} text-emerald-600`;
            document.getElementById('categoryDropdown').classList.add('hidden');
            document.getElementById('categoryChevron').classList.remove('rotate-180');
            CategoryFilter.filterByCategory(btn);
        }

        document.addEventListener('click', function(e) {
            const btn = document.getElementById('categoryDropdownBtn');
            const dropdown = document.getElementById('categoryDropdown');
            if (btn && dropdown && !btn.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
                document.getElementById('categoryChevron').classList.remove('rotate-180');
            }
        });

        // ====================================================================
        // TRANSAKSI KASIR - MAIN APP
        // ====================================================================
        (function() {
            'use strict';

            // Payment rows state: array of { method: 'tunai'|'qris'|'piutang', amount: number }
            const AppState = {
                cart: [],
                totalBelanja: 0,
                currentDetailProduct: null,
                paymentRows: [], // [{method, amount}]
                piutangData: [],
                currentPiutangFilter: 'all'
            };

            const DOM = {
                categoryBtns: document.querySelectorAll('.category-btn'),
                produkList: document.querySelectorAll('.produk-item'),
                searchProduk: document.getElementById('searchProduk'),
                cartItems: document.getElementById('cartItems'),
                cartItemsMobile: document.getElementById('cartItemsMobile'),
                cartCount: document.getElementById('cartCount'),
                cartBadge: document.getElementById('cartBadge'),
                totalAmount: document.getElementById('totalAmount'),
                totalAmountMobile: document.getElementById('totalAmountMobile'),
                kembalian: document.getElementById('kembalian'),
                kembalianMobile: document.getElementById('kembalianMobile'),
                kembalianSectionDesktop: document.getElementById('kembalianSectionDesktop'),
                kembalianSectionMobile: document.getElementById('kembalianSectionMobile'),
                infoPiutang: document.getElementById('infoPiutang'),
                infoPiutangMobile: document.getElementById('infoPiutangMobile'),
                btnProses: document.getElementById('btnProses'),
                btnProsesMobile: document.getElementById('btnProsesMobile'),
                btnReset: document.getElementById('btnReset'),
                btnResetMobile: document.getElementById('btnResetMobile'),
                btnShowCart: document.getElementById('btnShowCart'),
                btnCloseCart: document.getElementById('btnCloseCart'),
                btnShowPiutang: document.getElementById('btnShowPiutang'),
                cartModal: document.getElementById('cartModal'),
                productDetailModal: document.getElementById('productDetailModal'),
                productDetailContent: document.getElementById('productDetailContent'),
                closeProductDetailBtn: document.getElementById('closeProductDetailBtn'),
                addToCartFromDetailBtn: document.getElementById('addToCartFromDetailBtn'),
                closeDetailBtn: document.getElementById('closeDetailBtn'),
                piutangModal: document.getElementById('piutangModal'),
                closePiutangModalBtn: document.getElementById('closePiutangModalBtn'),
                piutangList: document.getElementById('piutangList'),
                piutangFilterBtns: document.querySelectorAll('.piutang-filter-btn'),
                detailPiutangModal: document.getElementById('detailPiutangModal'),
                closeDetailPiutangBtn: document.getElementById('closeDetailPiutangBtn'),
                detailPiutangContent: document.getElementById('detailPiutangContent'),
                paymentRowsDesktop: document.getElementById('paymentRowsDesktop'),
                paymentRowsMobile: document.getElementById('paymentRowsMobile'),
                btnAddPaymentDesktop: document.getElementById('btnAddPaymentDesktop'),
                btnAddPaymentMobile: document.getElementById('btnAddPaymentMobile'),
            };

            const Utils = {
                formatRupiah: (amount) => `Rp ${parseFloat(amount || 0).toLocaleString('id-ID')}`,
                showToast: (title, icon = 'success') => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon,
                        title,
                        showConfirmButton: false,
                        timer: 1500
                    });
                },
                showError: (title, text) => {
                    Swal.fire({
                        icon: 'error',
                        title,
                        text,
                        confirmButtonColor: '#ef4444'
                    });
                },
                showConfirm: async (title, html, confirmText = 'Ya') => {
                    return await Swal.fire({
                        title,
                        html,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: confirmText,
                        cancelButtonText: 'Batal'
                    });
                }
            };

            // ================================================================
            // PAYMENT MANAGER (multi-method rows)
            // ================================================================
            const PaymentManager = {
                METHODS: {
                    tunai: {
                        label: 'Tunai',
                        icon: 'fa-money-bill-wave',
                        color: 'emerald',
                        activeClass: 'active'
                    },
                    qris: {
                        label: 'QRIS',
                        icon: 'fa-qrcode',
                        color: 'indigo',
                        activeClass: 'active-qris'
                    },
                    piutang: {
                        label: 'Piutang',
                        icon: 'fa-clock',
                        color: 'orange',
                        activeClass: 'active-piutang'
                    }
                },

                init() {
                    // Start with one payment row (tunai)
                    AppState.paymentRows = [{
                        method: 'tunai',
                        amount: 0
                    }];
                    this.renderAll();

                    DOM.btnAddPaymentDesktop?.addEventListener('click', () => this.addRow());
                    DOM.btnAddPaymentMobile?.addEventListener('click', () => this.addRow());
                },

                addRow() {
                    // Max 3 rows
                    if (AppState.paymentRows.length >= 3) return;
                    // Don't allow piutang as a second row
                    AppState.paymentRows.push({
                        method: 'tunai',
                        amount: 0
                    });
                    this.renderAll();
                    PaymentManager.recalculate();
                },

                removeRow(index) {
                    if (AppState.paymentRows.length <= 1) return;
                    AppState.paymentRows.splice(index, 1);
                    this.renderAll();
                    PaymentManager.recalculate();
                },

                setMethod(index, method) {
                    AppState.paymentRows[index].method = method;
                    // If piutang is selected, it must be the ONLY row
                    if (method === 'piutang') {
                        AppState.paymentRows = [{
                            method: 'piutang',
                            amount: 0
                        }];
                    }
                    this.renderAll();
                    PaymentManager.recalculate();
                },

                setAmount(index, value) {
                    AppState.paymentRows[index].amount = parseFloat(value) || 0;
                    PaymentManager.recalculate();
                },

                isPiutangMode() {
                    return AppState.paymentRows.length === 1 && AppState.paymentRows[0].method === 'piutang';
                },

                getTotalPaid() {
                    // QRIS rows are counted as exact (no change from QRIS)
                    // Tunai rows contribute to paid amount
                    if (this.isPiutangMode()) return 0;
                    return AppState.paymentRows.reduce((s, r) => s + (r.amount || 0), 0);
                },

                getTunaiAmount() {
                    return AppState.paymentRows
                        .filter(r => r.method === 'tunai')
                        .reduce((s, r) => s + (r.amount || 0), 0);
                },

                recalculate() {
                    if (this.isPiutangMode()) {
                        // Hide kembalian, show piutang info
                        DOM.kembalianSectionDesktop?.classList.add('hidden');
                        DOM.kembalianSectionMobile?.classList.add('hidden');
                        DOM.infoPiutang?.classList.remove('hidden');
                        DOM.infoPiutangMobile?.classList.remove('hidden');
                        const canProcess = AppState.cart.length > 0;
                        if (DOM.btnProses) DOM.btnProses.disabled = !canProcess;
                        if (DOM.btnProsesMobile) DOM.btnProsesMobile.disabled = !canProcess;
                        return;
                    }

                    DOM.infoPiutang?.classList.add('hidden');
                    DOM.infoPiutangMobile?.classList.add('hidden');

                    // Check if any tunai method exists
                    const hasTunai = AppState.paymentRows.some(r => r.method === 'tunai');
                    const hasQrisOnly = AppState.paymentRows.every(r => r.method === 'qris');

                    if (hasQrisOnly) {
                        // QRIS only: hide kembalian
                        DOM.kembalianSectionDesktop?.classList.add('hidden');
                        DOM.kembalianSectionMobile?.classList.add('hidden');
                    } else {
                        // Show kembalian based on tunai amount
                        DOM.kembalianSectionDesktop?.classList.remove('hidden');
                        DOM.kembalianSectionMobile?.classList.remove('hidden');
                        const tunaiAmount = this.getTunaiAmount();
                        const qrisAmount = AppState.paymentRows
                            .filter(r => r.method === 'qris')
                            .reduce((s, r) => s + (r.amount || 0), 0);
                        const nonTunaiCover = qrisAmount;
                        const remainingForTunai = Math.max(0, AppState.totalBelanja - nonTunaiCover);
                        const kembalian = tunaiAmount - remainingForTunai;
                        const kembalianText = Utils.formatRupiah(kembalian > 0 ? kembalian : 0);
                        if (DOM.kembalian) DOM.kembalian.textContent = kembalianText;
                        if (DOM.kembalianMobile) DOM.kembalianMobile.textContent = kembalianText;
                    }

                    const totalPaid = this.getTotalPaid();
                    const canProcess = AppState.cart.length > 0 && totalPaid >= AppState.totalBelanja;
                    if (DOM.btnProses) DOM.btnProses.disabled = !canProcess;
                    if (DOM.btnProsesMobile) DOM.btnProsesMobile.disabled = !canProcess;
                },

                buildRowHTML(row, index, isMobile) {
                    const prefix = isMobile ? 'M' : 'D';
                    const canRemove = AppState.paymentRows.length > 1;
                    const methodBtns = Object.entries(this.METHODS).map(([key, meta]) => {
                        if (key === 'piutang' && AppState.paymentRows.length > 1)
                            return ''; // piutang only solo
                        const isActive = row.method === key;
                        const activeClass = isActive ? `pay-method-btn ${meta.activeClass}` :
                            'pay-method-btn';
                        return `<button type="button" class="${activeClass}"
                            onclick="PaymentManager.setMethod(${index}, '${key}')"
                            title="${meta.label}">
                            <i class="fas ${meta.icon} mb-1 block text-lg"></i>
                            <span>${meta.label}</span>
                        </button>`;
                    }).join('');

                    const showAmountInput = row.method !== 'piutang' && row.method !== 'qris';
                    const showQrisNote = row.method === 'qris';

                    return `<div class="payment-row" id="payRow_${prefix}_${index}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-gray-500">Pembayaran ${index + 1}</span>
                            ${canRemove ? `<button type="button" onclick="PaymentManager.removeRow(${index})" class="text-red-400 hover:text-red-600 text-xs"><i class="fas fa-times"></i></button>` : ''}
                        </div>
                        <div class="payment-method-select">${methodBtns}</div>
                        ${showAmountInput ? `
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Jumlah Bayar</label>
                                    <input type="number" min="0" step="1000" placeholder="Masukkan nominal"
                                        value="${row.amount || ''}"
                                        class="w-full px-3 py-2 bg-white border-2 border-gray-200 rounded-lg text-sm focus:border-emerald-500 focus:outline-none"
                                        onchange="PaymentManager.setAmount(${index}, this.value)"
                                        oninput="PaymentManager.setAmount(${index}, this.value)">
                                </div>` : ''}
                        ${showQrisNote ? `
                                <div class="bg-indigo-50 rounded-lg p-2 text-xs text-indigo-600 font-semibold text-center">
                                    <i class="fas fa-qrcode mr-1"></i> Pembayaran QRIS — Scan QR Code
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Nominal QRIS</label>
                                    <input type="number" min="0" step="1000" placeholder="Nominal QRIS"
                                        value="${row.amount || ''}"
                                        class="w-full px-3 py-2 bg-white border-2 border-indigo-200 rounded-lg text-sm focus:border-indigo-500 focus:outline-none"
                                        onchange="PaymentManager.setAmount(${index}, this.value)"
                                        oninput="PaymentManager.setAmount(${index}, this.value)">
                                </div>` : ''}
                    </div>`;
                },

                renderAll() {
                    const isPiutang = this.isPiutangMode();
                    const canAddMore = !isPiutang && AppState.paymentRows.length < 3;

                    // Show/hide add button
                    if (DOM.btnAddPaymentDesktop) DOM.btnAddPaymentDesktop.classList.toggle('hidden', !canAddMore);
                    if (DOM.btnAddPaymentMobile) DOM.btnAddPaymentMobile.classList.toggle('hidden', !canAddMore);

                    // Render desktop rows
                    if (DOM.paymentRowsDesktop) {
                        DOM.paymentRowsDesktop.innerHTML = AppState.paymentRows
                            .map((row, i) => this.buildRowHTML(row, i, false)).join('');
                    }
                    // Render mobile rows
                    if (DOM.paymentRowsMobile) {
                        DOM.paymentRowsMobile.innerHTML = AppState.paymentRows
                            .map((row, i) => this.buildRowHTML(row, i, true)).join('');
                    }
                }
            };

            // Expose to global for inline onclick
            window.PaymentManager = PaymentManager;

            const CategoryFilter = {
                init() {
                    DOM.categoryBtns.forEach(btn => {
                        btn.addEventListener('click', (e) => this.filterByCategory(e.currentTarget));
                    });
                },
                filterByCategory(btn) {
                    const category = btn.dataset.category;
                    DOM.categoryBtns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    DOM.produkList.forEach(item => {
                        const itemCategory = item.dataset.kategori;
                        const matchCategory = category === 'all' || itemCategory === category;
                        const searchValue = DOM.searchProduk?.value.toLowerCase() || '';
                        const nama = item.dataset.nama.toLowerCase();
                        const kode = item.dataset.kode.toLowerCase();
                        const matchSearch = !searchValue || nama.includes(searchValue) || kode.includes(
                            searchValue);
                        item.style.display = (matchCategory && matchSearch) ? 'block' : 'none';
                    });
                }
            };

            const SearchManager = {
                init() {
                    DOM.searchProduk?.addEventListener('input', (e) => this.searchProducts(e.target.value));
                },
                searchProducts(searchValue) {
                    const search = searchValue.toLowerCase();
                    const activeCategory = document.querySelector('.category-btn.active')?.dataset.category;
                    DOM.produkList.forEach(item => {
                        const nama = item.dataset.nama.toLowerCase();
                        const kode = item.dataset.kode.toLowerCase();
                        const itemCategory = item.dataset.kategori;
                        const matchSearch = !search || nama.includes(search) || kode.includes(search);
                        const matchCategory = activeCategory === 'all' || itemCategory === activeCategory;
                        item.style.display = (matchSearch && matchCategory) ? 'block' : 'none';
                    });
                }
            };

            const ProductDetailManager = {
                init() {
                    document.querySelectorAll('.info-btn').forEach(btn => {
                        btn.addEventListener('click', (e) => {
                            e.stopPropagation();
                            this.showProductDetail(e.currentTarget.dataset.productId);
                        });
                    });
                    DOM.closeProductDetailBtn?.addEventListener('click', () => this.closeProductDetail());
                    DOM.closeDetailBtn?.addEventListener('click', () => this.closeProductDetail());
                    DOM.addToCartFromDetailBtn?.addEventListener('click', () => this.addToCartFromDetail());
                    DOM.productDetailModal?.addEventListener('click', (e) => {
                        if (e.target === DOM.productDetailModal) this.closeProductDetail();
                    });
                },
                showProductDetail(productId) {
                    const productItem = document.querySelector(`.produk-item[data-id="${productId}"]`);
                    if (!productItem) return;
                    AppState.currentDetailProduct = {
                        id: productItem.dataset.id,
                        nama: productItem.dataset.nama,
                        harga: parseFloat(productItem.dataset.harga),
                        stok: parseInt(productItem.dataset.stok),
                        kategori: productItem.dataset.kategori,
                        kode: productItem.dataset.kode,
                        deskripsi: productItem.dataset.deskripsi,
                        gambar: productItem.dataset.gambar
                    };
                    const gambarUrl = AppState.currentDetailProduct.gambar ?
                        `/uploads/produk/${encodeURIComponent(AppState.currentDetailProduct.gambar)}` : null;
                    const content = `
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="flex items-center justify-center">
                                ${gambarUrl
                                    ? `<img src="${gambarUrl}" alt="${AppState.currentDetailProduct.nama}" class="w-full h-64 object-cover rounded-xl shadow-lg" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                               <div class="w-full h-64 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-xl items-center justify-center" style="display:none;"><i class="fas fa-box text-6xl text-blue-600"></i></div>`
                                    : `<div class="w-full h-64 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-xl flex items-center justify-center"><i class="fas fa-box text-6xl text-blue-600"></i></div>`
                                }
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <h4 class="text-2xl font-bold text-gray-800 mb-2">${AppState.currentDetailProduct.nama}</h4>
                                    <div class="flex items-center space-x-2">
                                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold"><i class="fas fa-tag mr-1"></i>${AppState.currentDetailProduct.kategori}</span>
                                        <span class="px-3 py-1 ${AppState.currentDetailProduct.stok > 10 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'} rounded-full text-sm font-semibold"><i class="fas fa-box mr-1"></i>Stok: ${AppState.currentDetailProduct.stok}</span>
                                    </div>
                                </div>
                                <div class="bg-emerald-50 rounded-xl p-4">
                                    <p class="text-sm text-gray-600 mb-1">Harga</p>
                                    <p class="text-3xl font-bold text-emerald-600">${Utils.formatRupiah(AppState.currentDetailProduct.harga)}</p>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between py-2 border-b">
                                        <span class="text-gray-600 font-medium">Kode Produk</span>
                                        <span class="font-bold text-gray-800">${AppState.currentDetailProduct.kode}</span>
                                    </div>
                                    <div class="flex items-center justify-between py-2 border-b">
                                        <span class="text-gray-600 font-medium">Kategori</span>
                                        <span class="font-bold text-gray-800 capitalize">${AppState.currentDetailProduct.kategori}</span>
                                    </div>
                                    <div class="flex items-center justify-between py-2">
                                        <span class="text-gray-600 font-medium">Status</span>
                                        <span class="font-bold ${AppState.currentDetailProduct.stok > 0 ? 'text-green-600' : 'text-red-600'}">${AppState.currentDetailProduct.stok > 0 ? 'Tersedia' : 'Stok Habis'}</span>
                                    </div>
                                </div>
                                <div class="bg-gray-50 rounded-xl p-4">
                                    <p class="text-sm font-semibold text-gray-700 mb-2">Deskripsi</p>
                                    <p class="text-gray-600 text-sm">${AppState.currentDetailProduct.deskripsi}</p>
                                </div>
                            </div>
                        </div>`;
                    DOM.productDetailContent.innerHTML = content;
                    DOM.productDetailModal.classList.remove('hidden');
                },
                closeProductDetail() {
                    DOM.productDetailModal.classList.add('hidden');
                    AppState.currentDetailProduct = null;
                },
                addToCartFromDetail() {
                    if (!AppState.currentDetailProduct) return;
                    if (AppState.currentDetailProduct.stok <= 0) {
                        Utils.showError('Stok Habis!', 'Produk ini sedang tidak tersedia');
                        return;
                    }
                    const existingItem = AppState.cart.find(i => i.id === AppState.currentDetailProduct.id);
                    if (existingItem) {
                        if (existingItem.qty < AppState.currentDetailProduct.stok) {
                            existingItem.qty++;
                            CartManager.updateCart();
                            this.closeProductDetail();
                            Utils.showToast('Ditambahkan ke keranjang');
                        } else {
                            Utils.showError('Stok Tidak Cukup!',
                                `Stok tersedia: ${AppState.currentDetailProduct.stok} pcs`);
                        }
                    } else {
                        AppState.cart.push({
                            id: AppState.currentDetailProduct.id,
                            nama: AppState.currentDetailProduct.nama,
                            harga: AppState.currentDetailProduct.harga,
                            qty: 1,
                            stok: AppState.currentDetailProduct.stok,
                            gambar: AppState.currentDetailProduct.gambar
                        });
                        CartManager.updateCart();
                        this.closeProductDetail();
                        Utils.showToast('Ditambahkan ke keranjang');
                    }
                }
            };

            const CartManager = {
                init() {
                    DOM.produkList.forEach(item => {
                        item.addEventListener('click', (e) => {
                            if (e.target.closest('.info-btn')) return;
                            this.addToCart(e.currentTarget);
                        });
                    });
                    DOM.btnShowCart?.addEventListener('click', () => DOM.cartModal.classList.remove('hidden'));
                    DOM.btnCloseCart?.addEventListener('click', () => DOM.cartModal.classList.add('hidden'));
                },
                addToCart(productElement) {
                    const stok = parseInt(productElement.dataset.stok);
                    if (stok <= 0) {
                        Utils.showError('Stok Habis!', 'Produk ini sedang tidak tersedia');
                        return;
                    }
                    const id = productElement.dataset.id;
                    const nama = productElement.dataset.nama;
                    const harga = parseFloat(productElement.dataset.harga);
                    const gambar = productElement.dataset.gambar;
                    const existingItem = AppState.cart.find(i => i.id === id);
                    if (existingItem) {
                        if (existingItem.qty < stok) {
                            existingItem.qty++;
                            this.updateCart();
                            Utils.showToast('Ditambahkan ke keranjang');
                        } else {
                            Utils.showError('Stok Tidak Cukup!', `Stok tersedia: ${stok} pcs`);
                        }
                    } else {
                        AppState.cart.push({
                            id,
                            nama,
                            harga,
                            qty: 1,
                            stok,
                            gambar
                        });
                        this.updateCart();
                        Utils.showToast('Ditambahkan ke keranjang');
                    }
                },
                updateCart() {
                    const emptyHTML =
                        `<div class="text-center py-8"><i class="fas fa-shopping-basket text-4xl text-gray-300 mb-2"></i><p class="text-gray-500 text-sm">Keranjang kosong</p></div>`;
                    if (AppState.cart.length === 0) {
                        DOM.cartItems.innerHTML = emptyHTML;
                        DOM.cartItemsMobile.innerHTML = emptyHTML;
                        AppState.totalBelanja = 0;
                    } else {
                        const cartHTML = AppState.cart.map(item => `
                            <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-3 border border-gray-200">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1">
                                        <p class="font-bold text-sm text-gray-800">${item.nama}</p>
                                        <p class="text-xs text-gray-600">${Utils.formatRupiah(item.harga)} × ${item.qty}</p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-bold text-emerald-600">${Utils.formatRupiah(item.harga * item.qty)}</p>
                                    <div class="flex items-center space-x-2">
                                        <button onclick="window.cartChangeQty('${item.id}', -1)" class="bg-red-500 text-white w-7 h-7 rounded-lg hover:bg-red-600 transition">-</button>
                                        <span class="font-bold w-8 text-center">${item.qty}</span>
                                        <button onclick="window.cartChangeQty('${item.id}', 1)" class="bg-emerald-500 text-white w-7 h-7 rounded-lg hover:bg-emerald-600 transition">+</button>
                                        <button onclick="window.cartRemoveItem('${item.id}')" class="text-red-600 ml-2 hover:text-red-700 transition"><i class="fas fa-trash"></i></button>
                                    </div>
                                </div>
                            </div>`).join('');
                        DOM.cartItems.innerHTML = cartHTML;
                        DOM.cartItemsMobile.innerHTML = cartHTML;
                        AppState.totalBelanja = AppState.cart.reduce((sum, item) => sum + (item.harga * item.qty),
                            0);
                    }
                    DOM.cartCount.textContent = AppState.cart.length;
                    DOM.cartBadge.textContent = AppState.cart.length;
                    DOM.totalAmount.textContent = Utils.formatRupiah(AppState.totalBelanja);
                    DOM.totalAmountMobile.textContent = Utils.formatRupiah(AppState.totalBelanja);
                    PaymentManager.recalculate();
                },
                changeQty(id, delta) {
                    const item = AppState.cart.find(i => i.id === id);
                    if (item) {
                        const newQty = item.qty + delta;
                        if (newQty > 0 && newQty <= item.stok) {
                            item.qty = newQty;
                            this.updateCart();
                        } else if (newQty === 0) {
                            this.removeItem(id);
                        } else if (newQty > item.stok) {
                            Utils.showError('Stok Tidak Cukup!', `Stok tersedia: ${item.stok} pcs`);
                        }
                    }
                },
                async removeItem(id) {
                    const item = AppState.cart.find(i => i.id === id);
                    const result = await Utils.showConfirm('Hapus Item?',
                        `Hapus <strong>${item.nama}</strong> dari keranjang?`, 'Hapus');
                    if (result.isConfirmed) {
                        AppState.cart = AppState.cart.filter(i => i.id !== id);
                        this.updateCart();
                        Utils.showToast('Item berhasil dihapus', 'success');
                    }
                },
                async reset() {
                    if (AppState.cart.length > 0) {
                        const result = await Utils.showConfirm('Konfirmasi Reset',
                            'Yakin ingin menghapus semua item?', 'Ya, Reset');
                        if (result.isConfirmed) {
                            AppState.cart = [];
                            this.updateCart();
                            Utils.showToast('Keranjang telah dikosongkan', 'success');
                        }
                        return;
                    }
                    AppState.cart = [];
                    this.updateCart();
                }
            };

            const TransactionManager = {
                init() {
                    DOM.btnProses?.addEventListener('click', () => this.processTransaction());
                    DOM.btnProsesMobile?.addEventListener('click', () => this.processTransaction());
                    DOM.btnReset?.addEventListener('click', () => CartManager.reset());
                    DOM.btnResetMobile?.addEventListener('click', () => CartManager.reset());
                },

                buildPaymentSummaryHTML() {
                    const methodLabels = {
                        tunai: 'Tunai',
                        qris: 'QRIS',
                        piutang: 'Piutang'
                    };
                    return AppState.paymentRows.map(r =>
                        `<div class="flex justify-between text-sm">
                            <span>${methodLabels[r.method] || r.method}</span>
                            <span class="font-bold">${r.method === 'piutang' ? Utils.formatRupiah(AppState.totalBelanja) : Utils.formatRupiah(r.amount)}</span>
                        </div>`
                    ).join('');
                },

                async processTransaction() {
                    if (AppState.cart.length === 0) {
                        Utils.showError('Keranjang Kosong!', 'Tambahkan produk terlebih dahulu');
                        return;
                    }

                    const isPiutang = PaymentManager.isPiutangMode();
                    let statusPembayaran = isPiutang ? 'belum_bayar' : 'lunas';
                    let totalBayar = 0;
                    let kembalian = 0;

                    if (isPiutang) {
                        const result = await Swal.fire({
                            title: 'Proses Transaksi Piutang?',
                            html: `<div class="text-left space-y-2 bg-orange-50 rounded-xl p-4">
                                <div class="flex items-center mb-3"><i class="fas fa-exclamation-circle text-orange-600 text-2xl mr-2"></i><span class="font-bold text-gray-800">Mode Piutang</span></div>
                                <div class="flex justify-between"><span>Total Tagihan:</span><span class="font-bold text-orange-600">${Utils.formatRupiah(AppState.totalBelanja)}</span></div>
                                <div class="flex justify-between"><span>Status:</span><span class="font-bold text-red-600">Belum Bayar</span></div>
                                <p class="text-xs text-gray-600 mt-3 italic">Transaksi akan tersimpan dengan status belum bayar</p>
                            </div>`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Proses Piutang',
                            confirmButtonColor: '#f97316',
                            cancelButtonText: 'Batal'
                        });
                        if (!result.isConfirmed) return;
                    } else {
                        totalBayar = PaymentManager.getTotalPaid();
                        if (totalBayar < AppState.totalBelanja) {
                            Utils.showError('Pembayaran Kurang!',
                                `Kurang: ${Utils.formatRupiah(AppState.totalBelanja - totalBayar)}`);
                            return;
                        }
                        // Kembalian only from tunai
                        const tunaiAmount = PaymentManager.getTunaiAmount();
                        const qrisAmount = AppState.paymentRows.filter(r => r.method === 'qris').reduce((s,
                            r) => s + r.amount, 0);
                        kembalian = tunaiAmount - Math.max(0, AppState.totalBelanja - qrisAmount);

                        const result = await Swal.fire({
                            title: 'Proses Transaksi?',
                            html: `<div class="text-left space-y-2 bg-gray-50 rounded-xl p-4">
                                ${this.buildPaymentSummaryHTML()}
                                <div class="border-t pt-2 flex justify-between font-bold"><span>Total:</span><span>${Utils.formatRupiah(AppState.totalBelanja)}</span></div>
                                ${kembalian > 0 ? `<div class="flex justify-between text-blue-600 font-bold"><span>Kembalian Tunai:</span><span>${Utils.formatRupiah(kembalian)}</span></div>` : ''}
                            </div>`,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Proses',
                            cancelButtonText: 'Batal'
                        });
                        if (!result.isConfirmed) return;
                    }

                    this.setLoadingState(true);

                    const dataTransaksi = {
                        items: AppState.cart.map(item => ({
                            id_produk: item.id,
                            qty: item.qty,
                            harga: item.harga
                        })),
                        total_bayar: totalBayar,
                        total_pembayaran: AppState.totalBelanja,
                        kembalian_pembayaran: kembalian,
                        status_pembayaran: statusPembayaran,
                        payment_methods: AppState.paymentRows.map(r => ({
                            method: r.method,
                            amount: r.method === 'piutang' ? AppState.totalBelanja : r.amount
                        }))
                    };

                    try {
                        const response = await fetch('{{ route('transaksi.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content
                            },
                            body: JSON.stringify(dataTransaksi)
                        });
                        const data = await response.json();
                        if (data.success) {
                            let printSuccess = false;
                            if (data.data.auto_print && data.data.printer_name) {
                                if (typeof window.PrinterHelper !== 'undefined' && window.PrinterHelper
                                    .device) {
                                    try {
                                        const receiptData = {
                                            no_transaksi: `#${String(data.data.id_penjualan).padStart(6, '0')}`,
                                            tanggal: new Date().toLocaleString('id-ID'),
                                            kasir: '{{ Auth::user()->nama_user ?? Auth::user()->name }}',
                                            total_pembayaran: AppState.totalBelanja,
                                            total_bayar: totalBayar,
                                            kembalian_pembayaran: kembalian,
                                            status_pembayaran: statusPembayaran,
                                            payment_methods: dataTransaksi.payment_methods,
                                            items: AppState.cart.map(item => ({
                                                nama_produk: item.nama,
                                                qty_produk: item.qty,
                                                harga_produk: item.harga,
                                                subtotal_harga: item.harga * item.qty
                                            }))
                                        };
                                        await window.PrinterHelper.printReceipt(receiptData);
                                        printSuccess = true;
                                    } catch (printError) {
                                        console.error('❌ Auto print failed:', printError.message);
                                    }
                                }
                            }

                            if (statusPembayaran === 'lunas') {
                                const swalResult = await Swal.fire({
                                    icon: 'success',
                                    title: 'Transaksi Berhasil!',
                                    html: `<div class="bg-emerald-50 rounded-xl p-4">
                                        <p class="text-sm text-gray-600">ID Transaksi</p>
                                        <p class="text-2xl font-bold text-emerald-600">#${data.data.id_penjualan}</p>
                                        <p class="text-xs text-gray-500 mt-2">Status: Lunas</p>
                                        ${printSuccess ? '<p class="text-xs text-green-600 mt-2">✓ Struk sudah dicetak otomatis</p>' : ''}
                                    </div>`,
                                    showCancelButton: !printSuccess,
                                    confirmButtonText: 'OK',
                                    cancelButtonText: '<i class="fas fa-print mr-2"></i>Cetak Struk',
                                    confirmButtonColor: '#10b981',
                                    cancelButtonColor: '#3b82f6',
                                    timer: printSuccess ? 3000 : undefined,
                                    timerProgressBar: printSuccess
                                });
                                if (swalResult.dismiss === Swal.DismissReason.cancel)
                                    window.open(`/transaksi/struk/${data.data.id_penjualan}`, '_blank');
                            } else {
                                const swalResult = await Swal.fire({
                                    icon: 'success',
                                    title: 'Piutang Tercatat!',
                                    html: `<div class="bg-orange-50 rounded-xl p-4">
                                        <p class="text-sm text-gray-600">ID Transaksi</p>
                                        <p class="text-2xl font-bold text-orange-600">#${data.data.id_penjualan}</p>
                                        <p class="text-xs text-red-600 mt-2 font-semibold">Status: Belum Bayar</p>
                                        <p class="text-xs text-gray-600 mt-2">Total Tagihan: ${Utils.formatRupiah(AppState.totalBelanja)}</p>
                                    </div>`,
                                    showCancelButton: true,
                                    confirmButtonText: 'OK',
                                    cancelButtonText: '<i class="fas fa-print mr-2"></i>Cetak Nota',
                                    confirmButtonColor: '#10b981',
                                    cancelButtonColor: '#3b82f6'
                                });
                                if (swalResult.dismiss === Swal.DismissReason.cancel)
                                    window.open(`/transaksi/struk/${data.data.id_penjualan}`, '_blank');
                            }

                            this.resetAfterTransaction();
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            throw new Error(data.message || 'Transaksi gagal');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        Utils.showError('Gagal!', 'Terjadi kesalahan: ' + error.message);
                        this.setLoadingState(false);
                    }
                },

                setLoadingState(isLoading) {
                    const text = isLoading ? '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...' :
                        '<i class="fas fa-check-circle mr-2"></i>Proses Transaksi';
                    if (DOM.btnProses) {
                        DOM.btnProses.disabled = isLoading;
                        DOM.btnProses.innerHTML = text;
                    }
                    if (DOM.btnProsesMobile) {
                        DOM.btnProsesMobile.disabled = isLoading;
                        DOM.btnProsesMobile.innerHTML = text;
                    }
                },

                resetAfterTransaction() {
                    AppState.cart = [];
                    CartManager.updateCart();
                    AppState.paymentRows = [{
                        method: 'tunai',
                        amount: 0
                    }];
                    PaymentManager.renderAll();
                    PaymentManager.recalculate();
                }
            };

            // ================================================================
            // PIUTANG MANAGER
            // ================================================================
            const PiutangManager = {
                init() {
                    DOM.btnShowPiutang?.addEventListener('click', () => this.loadPiutang());
                    DOM.closePiutangModalBtn?.addEventListener('click', () => this.closePiutangModal());
                    DOM.closeDetailPiutangBtn?.addEventListener('click', () => this.closeDetailPiutang());
                    DOM.piutangFilterBtns.forEach(btn => {
                        btn.addEventListener('click', (e) => this.filterPiutang(e.currentTarget));
                    });
                },

                async loadPiutang() {
                    DOM.piutangModal.classList.remove('hidden');
                    DOM.piutangList.innerHTML =
                        `<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-4xl text-gray-300 mb-2"></i><p class="text-gray-500">Memuat data...</p></div>`;
                    try {
                        const response = await fetch('{{ route('transaksi.piutang') }}');
                        const result = await response.json();
                        if (result.success) {
                            AppState.piutangData = result.data;
                            this.renderPiutangList();
                        } else {
                            throw new Error(result.message || 'Gagal memuat data');
                        }
                    } catch (error) {
                        DOM.piutangList.innerHTML =
                            `<div class="text-center py-8"><i class="fas fa-exclamation-circle text-4xl text-red-300 mb-2"></i><p class="text-red-500">Gagal memuat data piutang</p></div>`;
                    }
                },

                filterPiutang(btn) {
                    DOM.piutangFilterBtns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    AppState.currentPiutangFilter = btn.dataset.status;
                    this.renderPiutangList();
                },

                renderPiutangList() {
                    const filtered = AppState.currentPiutangFilter === 'all' ?
                        AppState.piutangData :
                        AppState.piutangData.filter(p => p.status_pembayaran === AppState.currentPiutangFilter);

                    if (filtered.length === 0) {
                        DOM.piutangList.innerHTML =
                            `<div class="text-center py-8"><i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i><p class="text-gray-500">Tidak ada data piutang</p></div>`;
                        return;
                    }

                    const statusConfig = {
                        belum_bayar: {
                            gradient: 'from-orange-50 to-red-50 border-orange-200',
                            badge: 'bg-red-600 text-white',
                            label: 'Belum Bayar',
                            amountColor: 'text-orange-600'
                        },
                        bayar_sebagian: {
                            gradient: 'from-yellow-50 to-orange-50 border-yellow-200',
                            badge: 'bg-yellow-600 text-white',
                            label: 'Bayar Sebagian',
                            amountColor: 'text-yellow-600'
                        },
                        lunas: {
                            gradient: 'from-green-50 to-emerald-50 border-green-200',
                            badge: 'bg-green-600 text-white',
                            label: 'Lunas',
                            amountColor: 'text-green-600'
                        }
                    };

                    DOM.piutangList.innerHTML = filtered.map(p => {
                        const cfg = statusConfig[p.status_pembayaran] || statusConfig.belum_bayar;
                        const showBayarBtn = p.status_pembayaran === 'belum_bayar' || p
                            .status_pembayaran === 'bayar_sebagian';
                        const sisaTagihan = p.sisa_tagihan ?? p.total_pembayaran;
                        return `
                        <div class="bg-gradient-to-r ${cfg.gradient} border-2 rounded-xl p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">${p.tanggal_penjualan}</p>
                                    <p class="font-bold text-lg text-gray-800">#${p.id_penjualan}</p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-bold ${cfg.badge}">${cfg.label}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Total Tagihan</p>
                                    <p class="text-xl font-bold ${cfg.amountColor}">${Utils.formatRupiah(p.total_pembayaran)}</p>
                                    ${p.status_pembayaran === 'bayar_sebagian' ? `<p class="text-xs text-orange-500 font-semibold">Sisa: ${Utils.formatRupiah(sisaTagihan)}</p>` : ''}
                                </div>
                                <div class="flex gap-2 flex-wrap">
                                    <button onclick="window.piutangShowDetail('${p.id_penjualan}')" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm transition"><i class="fas fa-eye mr-1"></i>Detail</button>
                                    ${showBayarBtn ? `
                                            <button onclick="window.piutangBayar('${p.id_penjualan}', ${p.total_pembayaran}, ${sisaTagihan})" class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm transition"><i class="fas fa-check mr-1"></i>Bayar</button>` : ''}
                                </div>
                            </div>
                        </div>`;
                    }).join('');
                },

                async showDetailPiutang(idPenjualan) {
                    try {
                        const response = await fetch(`/transaksi/detail/${idPenjualan}`);
                        const result = await response.json();
                        if (result.success) {
                            const data = result.data;
                            const items = data.items.map(item => `
                                <tr>
                                    <td class="px-4 py-2 border-b">${item.nama_produk}</td>
                                    <td class="px-4 py-2 border-b text-center">${item.qty}</td>
                                    <td class="px-4 py-2 border-b text-right">${Utils.formatRupiah(item.harga)}</td>
                                    <td class="px-4 py-2 border-b text-right font-bold">${Utils.formatRupiah(item.subtotal)}</td>
                                </tr>`).join('');

                            const statusConfig = {
                                belum_bayar: 'bg-red-600',
                                bayar_sebagian: 'bg-yellow-600',
                                lunas: 'bg-green-600'
                            };
                            const statusLabel = {
                                belum_bayar: 'Belum Bayar',
                                bayar_sebagian: 'Bayar Sebagian',
                                lunas: 'Lunas'
                            };
                            const sisaTagihan = data.sisa_tagihan ?? data.total_pembayaran;

                            DOM.detailPiutangContent.innerHTML = `
                                <div class="space-y-4">
                                    <div class="bg-gray-50 rounded-xl p-4">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div><p class="text-sm text-gray-600">ID Transaksi</p><p class="font-bold text-lg">#${data.id_penjualan}</p></div>
                                            <div><p class="text-sm text-gray-600">Tanggal</p><p class="font-bold">${data.tanggal_penjualan}</p></div>
                                            <div><p class="text-sm text-gray-600">Status</p><span class="px-3 py-1 rounded-full text-xs font-bold ${statusConfig[data.status_pembayaran] || 'bg-red-600'} text-white">${statusLabel[data.status_pembayaran] || data.status_pembayaran}</span></div>
                                            <div><p class="text-sm text-gray-600">Total</p><p class="font-bold text-lg text-emerald-600">${Utils.formatRupiah(data.total_pembayaran)}</p></div>
                                            ${data.status_pembayaran === 'bayar_sebagian' ? `<div class="col-span-2"><p class="text-sm text-gray-600">Sisa Tagihan</p><p class="font-bold text-lg text-orange-600">${Utils.formatRupiah(sisaTagihan)}</p></div>` : ''}
                                        </div>
                                    </div>
                                    <div class="overflow-x-auto">
                                        <table class="w-full">
                                            <thead class="bg-gray-100"><tr>
                                                <th class="px-4 py-2 text-left">Produk</th>
                                                <th class="px-4 py-2 text-center">Qty</th>
                                                <th class="px-4 py-2 text-right">Harga</th>
                                                <th class="px-4 py-2 text-right">Subtotal</th>
                                            </tr></thead>
                                            <tbody>${items}</tbody>
                                        </table>
                                    </div>
                                </div>`;
                            DOM.detailPiutangModal.classList.remove('hidden');
                        }
                    } catch (error) {
                        Utils.showError('Error', 'Gagal memuat detail piutang');
                    }
                },

                // Build payment rows HTML for piutang bayar modal
                buildPiutangPaymentRowsHTML(sisaTagihan) {
                    // We reuse the multi-payment concept inline inside the Swal popup
                    return `
                    <div class="space-y-2" id="piutangPayRows">
                        <div class="payment-row" id="piutangRow_0">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold text-gray-500">Pembayaran 1</span>
                            </div>
                            <div class="grid grid-cols-3 gap-2 mb-2">
                                <button type="button" class="pay-method-btn active" onclick="piutangSelectMethod(0,'tunai')"><i class="fas fa-money-bill-wave block text-lg mb-1"></i>Tunai</button>
                                <button type="button" class="pay-method-btn" onclick="piutangSelectMethod(0,'qris')"><i class="fas fa-qrcode block text-lg mb-1"></i>QRIS</button>
                            </div>
                            <label class="block text-xs text-gray-500 mb-1">Jumlah Bayar</label>
                            <input type="number" id="piutangAmt_0" min="0" step="1000" value="${sisaTagihan}" class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg text-sm" oninput="piutangRecalc(${sisaTagihan})">
                        </div>
                    </div>
                    <button type="button" id="piutangAddRowBtn" onclick="piutangAddRow(${sisaTagihan})" class="w-full mt-2 flex items-center justify-center gap-2 px-4 py-2 border-2 border-dashed border-emerald-400 text-emerald-600 rounded-xl text-sm font-semibold hover:bg-emerald-50 transition">
                        <i class="fas fa-plus"></i> Tambah Metode Bayar
                    </button>
                    <div id="piutangKembalianDiv" class="hidden bg-blue-50 rounded-xl p-3 mt-2">
                        <div class="flex justify-between"><span class="text-gray-700 font-semibold text-sm">Kembalian Tunai:</span><span id="piutangKembalianVal" class="font-bold text-blue-600 text-sm">Rp 0</span></div>
                    </div>`;
                },

                async bayarPiutang(idPenjualan, totalPembayaran, sisaTagihan) {
                    const self = this;
                    // State for piutang payment rows
                    window._piutangRows = [{
                        method: 'tunai',
                        amount: sisaTagihan
                    }];

                    // Helper functions (exposed globally for inline HTML)
                    window.piutangSelectMethod = function(idx, method) {
                        window._piutangRows[idx].method = method;
                        const row = document.getElementById(`piutangRow_${idx}`);
                        if (!row) return;
                        // Update button styles
                        row.querySelectorAll('.pay-method-btn').forEach(b => {
                            b.className = 'pay-method-btn';
                            if (b.textContent.trim().toLowerCase().includes(method === 'qris' ?
                                    'qris' : method === 'tunai' ? 'tunai' : '')) {
                                b.className = method === 'qris' ? 'pay-method-btn active-qris' :
                                    'pay-method-btn active';
                            }
                        });
                        // For QRIS: rename label
                        const label = row.querySelector('label');
                        if (label) label.textContent = method === 'qris' ? 'Nominal QRIS' : 'Jumlah Bayar';
                        piutangRecalc(sisaTagihan);
                    };

                    window.piutangAddRow = function(sisa) {
                        if (window._piutangRows.length >= 3) return;
                        window._piutangRows.push({
                            method: 'tunai',
                            amount: 0
                        });
                        const container = document.getElementById('piutangPayRows');
                        const idx = window._piutangRows.length - 1;
                        const div = document.createElement('div');
                        div.className = 'payment-row';
                        div.id = `piutangRow_${idx}`;
                        div.innerHTML =
                            `
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold text-gray-500">Pembayaran ${idx + 1}</span>
                                <button type="button" onclick="piutangRemoveRow(${idx}, ${sisa})" class="text-red-400 hover:text-red-600 text-xs"><i class="fas fa-times"></i></button>
                            </div>
                            <div class="grid grid-cols-3 gap-2 mb-2">
                                <button type="button" class="pay-method-btn active" onclick="piutangSelectMethod(${idx},'tunai')"><i class="fas fa-money-bill-wave block text-lg mb-1"></i>Tunai</button>
                                <button type="button" class="pay-method-btn" onclick="piutangSelectMethod(${idx},'qris')"><i class="fas fa-qrcode block text-lg mb-1"></i>QRIS</button>
                            </div>
                            <label class="block text-xs text-gray-500 mb-1">Jumlah Bayar</label>
                            <input type="number" id="piutangAmt_${idx}" min="0" step="1000" value="0" class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg text-sm" oninput="piutangRecalc(${sisa})">`;
                        container.appendChild(div);
                        if (window._piutangRows.length >= 3) {
                            const addBtn = document.getElementById('piutangAddRowBtn');
                            if (addBtn) addBtn.classList.add('hidden');
                        }
                        piutangRecalc(sisa);
                    };

                    window.piutangRemoveRow = function(idx, sisa) {
                        window._piutangRows.splice(idx, 1);
                        const row = document.getElementById(`piutangRow_${idx}`);
                        if (row) row.remove();
                        // Re-index not needed for JS state, just sync amounts
                        piutangRecalc(sisa);
                        const addBtn = document.getElementById('piutangAddRowBtn');
                        if (addBtn) addBtn.classList.remove('hidden');
                    };

                    window.piutangRecalc = function(sisa) {
                        // Sync amounts from inputs
                        window._piutangRows.forEach((r, i) => {
                            const inp = document.getElementById(`piutangAmt_${i}`);
                            if (inp) r.amount = parseFloat(inp.value) || 0;
                        });
                        const tunaiAmt = window._piutangRows.filter(r => r.method === 'tunai').reduce((s,
                            r) => s + r.amount, 0);
                        const qrisAmt = window._piutangRows.filter(r => r.method === 'qris').reduce((s,
                            r) => s + r.amount, 0);
                        const totalPaid = tunaiAmt + qrisAmt;
                        const kembalian = tunaiAmt - Math.max(0, sisa - qrisAmt);
                        const kemDiv = document.getElementById('piutangKembalianDiv');
                        const kemVal = document.getElementById('piutangKembalianVal');
                        if (kembalian > 0) {
                            if (kemDiv) kemDiv.classList.remove('hidden');
                            if (kemVal) kemVal.textContent = `Rp ${kembalian.toLocaleString('id-ID')}`;
                        } else {
                            if (kemDiv) kemDiv.classList.add('hidden');
                        }
                    };

                    const result = await Swal.fire({
                        title: `Bayar Piutang #${idPenjualan}`,
                        html: `
                        <div class="text-left space-y-3">
                            <div class="bg-emerald-50 rounded-xl p-3">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Total Tagihan</span>
                                    <span class="font-bold text-emerald-600">${Utils.formatRupiah(totalPembayaran)}</span>
                                </div>
                                <div class="flex justify-between mt-1">
                                    <span class="text-sm text-gray-600">Sisa Tagihan</span>
                                    <span class="font-bold text-orange-600">${Utils.formatRupiah(sisaTagihan)}</span>
                                </div>
                            </div>
                            ${this.buildPiutangPaymentRowsHTML(sisaTagihan)}
                        </div>`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Bayar Sekarang',
                        confirmButtonColor: '#10b981',
                        cancelButtonText: 'Batal',
                        didOpen: () => {
                            // Init recalc
                            window.piutangRecalc(sisaTagihan);
                        },
                        preConfirm: () => {
                            // Sync amounts
                            window._piutangRows.forEach((r, i) => {
                                const inp = document.getElementById(`piutangAmt_${i}`);
                                if (inp) r.amount = parseFloat(inp.value) || 0;
                            });
                            const totalPaid = window._piutangRows.reduce((s, r) => s + r.amount, 0);
                            if (totalPaid < sisaTagihan && totalPaid > 0) {
                                // Bayar sebagian is allowed (partial)
                                // We'll accept it but mark bayar_sebagian
                            }
                            if (totalPaid <= 0) {
                                Swal.showValidationMessage('Masukkan jumlah pembayaran');
                                return false;
                            }
                            return {
                                paymentRows: [...window._piutangRows],
                                totalPaid
                            };
                        }
                    });

                    if (!result.isConfirmed) return;

                    const {
                        paymentRows,
                        totalPaid
                    } = result.value;
                    const tunaiAmt = paymentRows.filter(r => r.method === 'tunai').reduce((s, r) => s + r
                        .amount, 0);
                    const qrisAmt = paymentRows.filter(r => r.method === 'qris').reduce((s, r) => s + r.amount,
                        0);
                    const kembalian = tunaiAmt - Math.max(0, sisaTagihan - qrisAmt);
                    const newStatus = totalPaid >= sisaTagihan ? 'lunas' : 'bayar_sebagian';

                    try {
                        const response = await fetch(`/transaksi/bayar-piutang/${idPenjualan}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content
                            },
                            body: JSON.stringify({
                                total_bayar: totalPaid,
                                payment_methods: paymentRows,
                                status_pembayaran: newStatus
                            })
                        });
                        const apiResult = await response.json();
                        if (apiResult.success) {
                            let piutangPrintSuccess = false;
                            // Auto print if configured...
                            if (apiResult.data?.auto_print && apiResult.data?.printer_name) {
                                if (typeof window.PrinterHelper !== 'undefined' && window.PrinterHelper
                                    .device) {
                                    try {
                                        const detailResponse = await fetch(`/transaksi/detail/${idPenjualan}`);
                                        const detailResult = await detailResponse.json();
                                        if (detailResult.success) {
                                            const receiptData = {
                                                no_transaksi: `#${String(idPenjualan).padStart(6, '0')}`,
                                                tanggal: new Date().toLocaleString('id-ID'),
                                                kasir: '{{ Auth::user()->nama_user ?? Auth::user()->name }}',
                                                total_pembayaran: totalPembayaran,
                                                total_bayar: totalPaid,
                                                kembalian_pembayaran: kembalian > 0 ? kembalian : 0,
                                                status_pembayaran: newStatus,
                                                payment_methods: paymentRows,
                                                items: detailResult.data.items.map(item => ({
                                                    nama_produk: item.nama_produk,
                                                    qty_produk: item.qty,
                                                    harga_produk: item.harga,
                                                    subtotal_harga: item.subtotal
                                                }))
                                            };
                                            await window.PrinterHelper.printReceipt(receiptData);
                                            piutangPrintSuccess = true;
                                        }
                                    } catch (printError) {
                                        console.error('❌ Auto print failed:', printError.message);
                                    }
                                }
                            }

                            const swalResult = await Swal.fire({
                                icon: 'success',
                                title: newStatus === 'lunas' ? 'Pembayaran Lunas!' :
                                    'Bayar Sebagian Berhasil!',
                                html: `<div class="bg-green-50 rounded-xl p-4">
                                    <p class="text-sm text-gray-600">Piutang #${idPenjualan}</p>
                                    <p class="font-bold text-green-600 mt-1">Status: ${newStatus === 'lunas' ? 'LUNAS' : 'BAYAR SEBAGIAN'}</p>
                                    <p class="text-sm text-gray-600 mt-2">Dibayar: ${Utils.formatRupiah(totalPaid)}</p>
                                    ${kembalian > 0 ? `<p class="text-sm font-bold text-blue-600">Kembalian Tunai: ${Utils.formatRupiah(kembalian)}</p>` : ''}
                                    ${newStatus === 'bayar_sebagian' ? `<p class="text-sm font-bold text-orange-600">Sisa: ${Utils.formatRupiah(sisaTagihan - totalPaid)}</p>` : ''}
                                    ${piutangPrintSuccess ? '<p class="text-xs text-green-600 mt-2">✓ Struk sudah dicetak otomatis</p>' : ''}
                                </div>`,
                                showCancelButton: !piutangPrintSuccess,
                                confirmButtonText: 'OK',
                                cancelButtonText: '<i class="fas fa-print mr-2"></i>Cetak Struk',
                                confirmButtonColor: '#10b981',
                                cancelButtonColor: '#3b82f6'
                            });
                            if (swalResult.dismiss === Swal.DismissReason.cancel)
                                window.open(`/transaksi/struk/${idPenjualan}`, '_blank');

                            this.loadPiutang();
                            this.closeDetailPiutang();
                        } else {
                            throw new Error(apiResult.message || 'Gagal memproses pembayaran');
                        }
                    } catch (error) {
                        Utils.showError('Error', error.message);
                    }
                },

                closePiutangModal() {
                    DOM.piutangModal.classList.add('hidden');
                },
                closeDetailPiutang() {
                    DOM.detailPiutangModal.classList.add('hidden');
                }
            };

            window.cartChangeQty = (id, delta) => CartManager.changeQty(id, delta);
            window.cartRemoveItem = (id) => CartManager.removeItem(id);
            window.piutangShowDetail = (id) => PiutangManager.showDetailPiutang(id);
            window.piutangBayar = (id, total, sisa) => PiutangManager.bayarPiutang(id, total, sisa ?? total);

            function initApp() {
                PaymentManager.init();
                CategoryFilter.init();
                SearchManager.init();
                ProductDetailManager.init();
                CartManager.init();
                TransactionManager.init();
                PiutangManager.init();
                console.log('✅ Transaksi Kasir App Initialized');
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initApp);
            } else {
                initApp();
            }
        })();
    </script>
@endpush
