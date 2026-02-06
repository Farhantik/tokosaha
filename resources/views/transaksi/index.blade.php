@extends('layouts.app')

@section('title', 'Transaksi - Toko Sahabat')
@section('page-title', 'Transaksi Kasir')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        .category-btn {
            transition: all 0.3s ease;
        }
        .category-btn.active {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
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
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
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
    </style>
@endpush

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                <i class="fas fa-cash-register mr-2 text-emerald-600"></i>Transaksi
            </h1>
            <div class="flex gap-2">
                <button id="btnShowPiutang" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-xl shadow-lg transition-all">
                    <i class="fas fa-clock mr-2"></i>
                    <span class="hidden sm:inline">Daftar Piutang</span>
                    <span class="sm:hidden">Piutang</span>
                </button>
                <button id="btnShowCart" class="lg:hidden bg-emerald-600 text-white px-4 py-2 rounded-xl shadow-lg">
                    <i class="fas fa-shopping-cart mr-2"></i>
                    Keranjang <span id="cartBadge"
                        class="bg-white text-emerald-600 px-2 py-0.5 rounded-full text-xs font-bold ml-1">0</span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Daftar Produk -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-lg p-4 md:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-3">
                                <i class="fas fa-box text-white"></i>
                            </div>
                            Daftar Produk
                        </h2>
                    </div>

                    <!-- Category Filter -->
                    <div class="mb-4">
                        <div class="flex flex-wrap gap-2">
                            <button class="category-btn active px-4 py-2 rounded-xl bg-gray-100 text-gray-700 font-semibold text-sm hover:bg-emerald-100" data-category="all">
                                <i class="fas fa-th-large mr-2"></i>Semua
                            </button>
                            <button class="category-btn px-4 py-2 rounded-xl bg-gray-100 text-gray-700 font-semibold text-sm hover:bg-emerald-100" data-category="makanan">
                                <i class="fas fa-utensils mr-2"></i>Makanan
                            </button>
                            <button class="category-btn px-4 py-2 rounded-xl bg-gray-100 text-gray-700 font-semibold text-sm hover:bg-emerald-100" data-category="minuman">
                                <i class="fas fa-coffee mr-2"></i>Minuman
                            </button>
                            <button class="category-btn px-4 py-2 rounded-xl bg-gray-100 text-gray-700 font-semibold text-sm hover:bg-emerald-100" data-category="snack">
                                <i class="fas fa-cookie-bite mr-2"></i>Snack
                            </button>
                            <button class="category-btn px-4 py-2 rounded-xl bg-gray-100 text-gray-700 font-semibold text-sm hover:bg-emerald-100" data-category="kebutuhan rumah tangga">
                                <i class="fas fa-home mr-2"></i>Kebutuhan RT
                            </button>
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
                    <div id="produkList" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 max-h-[calc(100vh-24rem)] overflow-y-auto pr-2">
                        @foreach ($produk as $item)
                            <div class="produk-item group border-2 border-gray-200 rounded-xl p-3 hover:border-emerald-500 hover:shadow-xl transition-all duration-300 {{ $item->stock_produk <= 0 ? 'opacity-50' : '' }}"
                                data-id="{{ $item->id_produk }}" 
                                data-nama="{{ $item->nama_produk }}"
                                data-harga="{{ $item->harga_produk }}" 
                                data-stok="{{ $item->stock_produk }}"
                                data-kategori="{{ strtolower($item->kategori_produk ?? 'lainnya') }}"
                                data-kode="{{ $item->kode_produk ?? '-' }}"
                                data-deskripsi="{{ $item->deskripsi_produk ?? 'Tidak ada deskripsi' }}"
                                data-gambar="{{ $item->gambar_produk ?? '' }}">
                                <div class="text-center relative">
                                    <button class="info-btn absolute top-1 right-1 bg-blue-500 text-white w-6 h-6 rounded-full flex items-center justify-center hover:bg-blue-600 transition-colors z-10 opacity-0 group-hover:opacity-100"
                                        data-product-id="{{ $item->id_produk }}">
                                        <i class="fas fa-info text-xs"></i>
                                    </button>

                                    @if ($item->gambar_produk)
                                        <img src="{{ asset('uploads/produk/' . $item->gambar_produk) }}"
                                            alt="{{ $item->nama_produk }}"
                                            class="w-full h-20 object-cover rounded-lg mb-2 group-hover:scale-105 transition-transform">
                                    @else
                                        <div class="bg-gradient-to-br from-blue-100 to-indigo-100 rounded-lg p-4 mb-2 h-20 flex items-center justify-center group-hover:from-blue-200 group-hover:to-indigo-200 transition-colors">
                                            <i class="fas fa-box text-3xl text-blue-600"></i>
                                        </div>
                                    @endif
                                    <h3 class="font-semibold text-xs mb-1 line-clamp-2 text-gray-800">{{ $item->nama_produk }}</h3>
                                    <p class="text-emerald-600 font-bold text-sm">Rp {{ number_format($item->harga_produk, 0, ',', '.') }}</p>
                                    <p class="text-xs mt-1 {{ $item->stock_produk <= 10 ? 'text-red-600 font-bold' : 'text-gray-500' }}">
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
                            <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center mr-3">
                                <i class="fas fa-shopping-cart text-white"></i>
                            </div>
                            Keranjang
                        </h2>
                        <span id="cartCount" class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-sm font-bold">0</span>
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

                        <!-- Tipe Pembayaran -->
                        <div>
                            <label class="block text-gray-700 font-semibold mb-3 text-sm">Pilih Metode Pembayaran</label>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="payment-type-card active bg-white rounded-xl p-3 text-center" data-type="tunai">
                                    <i class="fas fa-money-bill-wave text-3xl text-emerald-600 mb-2"></i>
                                    <p class="font-bold text-sm text-gray-800">Tunai</p>
                                    <p class="text-xs text-gray-500">Bayar Langsung</p>
                                </div>
                                <div class="payment-type-card bg-white rounded-xl p-3 text-center" data-type="piutang">
                                    <i class="fas fa-clock text-3xl text-orange-600 mb-2"></i>
                                    <p class="font-bold text-sm text-gray-800">Piutang</p>
                                    <p class="text-xs text-gray-500">Bayar Nanti</p>
                                </div>
                            </div>
                        </div>

                        <!-- Form Tunai -->
                        <div id="formTunai">
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2 text-sm">Bayar</label>
                                <input type="number" id="bayar" min="0" step="1000"
                                    class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:outline-none transition-all"
                                    placeholder="Masukkan jumlah bayar">
                            </div>

                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-3 mt-3">
                                <div class="flex justify-between text-lg">
                                    <span class="text-gray-700 font-semibold">Kembalian:</span>
                                    <span id="kembalian" class="font-bold text-blue-600">Rp 0</span>
                                </div>
                            </div>
                        </div>

                        <!-- Info Piutang (hidden) -->
                        <div id="infoPiutang" class="hidden">
                            <div class="bg-gradient-to-r from-orange-50 to-red-50 rounded-xl p-4">
                                <div class="flex items-start">
                                    <i class="fas fa-exclamation-circle text-orange-600 text-xl mr-3 mt-1"></i>
                                    <div>
                                        <p class="font-bold text-gray-800 mb-1">Mode Piutang</p>
                                        <p class="text-sm text-gray-600">Transaksi akan dicatat sebagai piutang dengan status <span class="font-bold text-orange-600">Belum Bayar</span></p>
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

                <!-- Cart Items Mobile -->
                <div id="cartItemsMobile" class="space-y-2 mb-4 max-h-48 overflow-y-auto">
                    <div class="text-center py-8">
                        <i class="fas fa-shopping-basket text-4xl text-gray-300 mb-2"></i>
                        <p class="text-gray-500 text-sm">Keranjang kosong</p>
                    </div>
                </div>

                <!-- Total Mobile -->
                <div class="border-t-2 border-gray-200 pt-4 space-y-3">
                    <div class="bg-gradient-to-r from-emerald-50 to-green-50 rounded-xl p-3">
                        <div class="flex justify-between text-lg font-bold text-gray-800">
                            <span>Total:</span>
                            <span id="totalAmountMobile" class="text-emerald-600">Rp 0</span>
                        </div>
                    </div>

                    <!-- Tipe Pembayaran Mobile -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-3 text-sm">Pilih Metode Pembayaran</label>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="payment-type-card active bg-white rounded-xl p-3 text-center" data-type="tunai">
                                <i class="fas fa-money-bill-wave text-2xl text-emerald-600 mb-1"></i>
                                <p class="font-bold text-xs text-gray-800">Tunai</p>
                            </div>
                            <div class="payment-type-card bg-white rounded-xl p-3 text-center" data-type="piutang">
                                <i class="fas fa-clock text-2xl text-orange-600 mb-1"></i>
                                <p class="font-bold text-xs text-gray-800">Piutang</p>
                            </div>
                        </div>
                    </div>

                    <!-- Form Tunai Mobile -->
                    <div id="formTunaiMobile">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2 text-sm">Bayar</label>
                            <input type="number" id="bayarMobile" min="0" step="1000"
                                class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:outline-none transition-all"
                                placeholder="Masukkan jumlah bayar">
                        </div>

                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-3 mt-3">
                            <div class="flex justify-between text-lg">
                                <span class="text-gray-700 font-semibold">Kembalian:</span>
                                <span id="kembalianMobile" class="font-bold text-blue-600">Rp 0</span>
                            </div>
                        </div>
                    </div>

                    <!-- Info Piutang Mobile -->
                    <div id="infoPiutangMobile" class="hidden">
                        <div class="bg-gradient-to-r from-orange-50 to-red-50 rounded-xl p-3">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-circle text-orange-600 mr-2 mt-1"></i>
                                <div>
                                    <p class="font-bold text-gray-800 text-sm mb-1">Mode Piutang</p>
                                    <p class="text-xs text-gray-600">Transaksi dicatat sebagai <span class="font-bold text-orange-600">Belum Bayar</span></p>
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
                            <i class="fas fa-clock mr-3"></i>
                            Daftar Piutang
                        </h3>
                        <button id="closePiutangModalBtn" class="text-white hover:text-gray-200 text-2xl">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                    <!-- Filter Status -->
                    <div class="mb-4 flex gap-2">
                        <button class="piutang-filter-btn active px-4 py-2 rounded-xl bg-orange-100 text-orange-700 font-semibold text-sm" data-status="all">
                            Semua
                        </button>
                        <button class="piutang-filter-btn px-4 py-2 rounded-xl bg-gray-100 text-gray-700 font-semibold text-sm" data-status="belum_bayar">
                            Belum Bayar
                        </button>
                        <button class="piutang-filter-btn px-4 py-2 rounded-xl bg-gray-100 text-gray-700 font-semibold text-sm" data-status="lunas">
                            Lunas
                        </button>
                    </div>

                    <!-- Piutang List -->
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
// TRANSAKSI KASIR - OPTIMIZED VERSION
// ====================================================================
(function() {
    'use strict';

    // State Management
    const AppState = {
        cart: [],
        totalBelanja: 0,
        currentDetailProduct: null,
        paymentType: 'tunai',
        piutangData: [],
        currentPiutangFilter: 'all'
    };

    // DOM Elements Cache
    const DOM = {
        // Category Buttons
        categoryBtns: document.querySelectorAll('.category-btn'),
        
        // Product
        produkList: document.querySelectorAll('.produk-item'),
        searchProduk: document.getElementById('searchProduk'),
        
        // Cart
        cartItems: document.getElementById('cartItems'),
        cartItemsMobile: document.getElementById('cartItemsMobile'),
        cartCount: document.getElementById('cartCount'),
        cartBadge: document.getElementById('cartBadge'),
        totalAmount: document.getElementById('totalAmount'),
        totalAmountMobile: document.getElementById('totalAmountMobile'),
        
        // Payment
        paymentTypeCards: document.querySelectorAll('.payment-type-card'),
        formTunai: document.getElementById('formTunai'),
        formTunaiMobile: document.getElementById('formTunaiMobile'),
        infoPiutang: document.getElementById('infoPiutang'),
        infoPiutangMobile: document.getElementById('infoPiutangMobile'),
        bayar: document.getElementById('bayar'),
        bayarMobile: document.getElementById('bayarMobile'),
        kembalian: document.getElementById('kembalian'),
        kembalianMobile: document.getElementById('kembalianMobile'),
        
        // Buttons
        btnProses: document.getElementById('btnProses'),
        btnProsesMobile: document.getElementById('btnProsesMobile'),
        btnReset: document.getElementById('btnReset'),
        btnResetMobile: document.getElementById('btnResetMobile'),
        btnShowCart: document.getElementById('btnShowCart'),
        btnCloseCart: document.getElementById('btnCloseCart'),
        btnShowPiutang: document.getElementById('btnShowPiutang'),
        
        // Modals
        cartModal: document.getElementById('cartModal'),
        productDetailModal: document.getElementById('productDetailModal'),
        productDetailContent: document.getElementById('productDetailContent'),
        closeProductDetailBtn: document.getElementById('closeProductDetailBtn'),
        addToCartFromDetailBtn: document.getElementById('addToCartFromDetailBtn'),
        closeDetailBtn: document.getElementById('closeDetailBtn'),
        
        // Piutang Modal
        piutangModal: document.getElementById('piutangModal'),
        closePiutangModalBtn: document.getElementById('closePiutangModalBtn'),
        piutangList: document.getElementById('piutangList'),
        piutangFilterBtns: document.querySelectorAll('.piutang-filter-btn'),
        detailPiutangModal: document.getElementById('detailPiutangModal'),
        closeDetailPiutangBtn: document.getElementById('closeDetailPiutangBtn'),
        detailPiutangContent: document.getElementById('detailPiutangContent')
    };

    // ====================================================================
    // UTILITY FUNCTIONS
    // ====================================================================
    const Utils = {
        formatRupiah: (amount) => {
            return `Rp ${parseFloat(amount).toLocaleString('id-ID')}`;
        },
        
        showToast: (title, icon = 'success') => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: icon,
                title: title,
                showConfirmButton: false,
                timer: 1500
            });
        },
        
        showError: (title, text) => {
            Swal.fire({
                icon: 'error',
                title: title,
                text: text,
                confirmButtonColor: '#ef4444'
            });
        },
        
        showConfirm: async (title, html, confirmText = 'Ya') => {
            return await Swal.fire({
                title: title,
                html: html,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: confirmText,
                cancelButtonText: 'Batal'
            });
        }
    };

    // ====================================================================
    // PAYMENT TYPE MANAGEMENT
    // ====================================================================
    const PaymentManager = {
        init() {
            DOM.paymentTypeCards.forEach(card => {
                card.addEventListener('click', (e) => this.selectPaymentType(e.currentTarget));
            });
        },
        
        selectPaymentType(card) {
            const type = card.dataset.type;
            
            // Update active state
            DOM.paymentTypeCards.forEach(c => c.classList.remove('active'));
            card.classList.add('active');
            
            AppState.paymentType = type;
            
            // Toggle forms
            if (type === 'tunai') {
                DOM.formTunai?.classList.remove('hidden');
                DOM.infoPiutang?.classList.add('hidden');
                DOM.formTunaiMobile?.classList.remove('hidden');
                DOM.infoPiutangMobile?.classList.add('hidden');
            } else {
                DOM.formTunai?.classList.add('hidden');
                DOM.infoPiutang?.classList.remove('hidden');
                DOM.formTunaiMobile?.classList.add('hidden');
                DOM.infoPiutangMobile?.classList.remove('hidden');
            }
            
            CartManager.calculateKembalian();
        }
    };

    // ====================================================================
    // CATEGORY FILTER
    // ====================================================================
    const CategoryFilter = {
        init() {
            DOM.categoryBtns.forEach(btn => {
                btn.addEventListener('click', (e) => this.filterByCategory(e.currentTarget));
            });
        },
        
        filterByCategory(btn) {
            const category = btn.dataset.category;
            
            // Update active state
            DOM.categoryBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            // Filter products
            DOM.produkList.forEach(item => {
                const itemCategory = item.dataset.kategori;
                const matchCategory = category === 'all' || itemCategory === category;
                
                // Also check search
                const searchValue = DOM.searchProduk?.value.toLowerCase() || '';
                const nama = item.dataset.nama.toLowerCase();
                const kode = item.dataset.kode.toLowerCase();
                const matchSearch = !searchValue || nama.includes(searchValue) || kode.includes(searchValue);
                
                item.style.display = (matchCategory && matchSearch) ? 'block' : 'none';
            });
        }
    };

    // ====================================================================
    // SEARCH FUNCTIONALITY
    // ====================================================================
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

    // ====================================================================
    // PRODUCT DETAIL MODAL
    // ====================================================================
    const ProductDetailManager = {
        init() {
            // Info buttons on products
            document.querySelectorAll('.info-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const productId = e.currentTarget.dataset.productId;
                    this.showProductDetail(productId);
                });
            });
            
            // Close buttons
            DOM.closeProductDetailBtn?.addEventListener('click', () => this.closeProductDetail());
            DOM.closeDetailBtn?.addEventListener('click', () => this.closeProductDetail());
            DOM.addToCartFromDetailBtn?.addEventListener('click', () => this.addToCartFromDetail());
            
            // Click outside to close
            DOM.productDetailModal?.addEventListener('click', (e) => {
                if (e.target === DOM.productDetailModal) {
                    this.closeProductDetail();
                }
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

            const gambarUrl = AppState.currentDetailProduct.gambar 
                ? `/uploads/produk/${AppState.currentDetailProduct.gambar}`
                : null;

            const content = `
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="flex items-center justify-center">
                        ${gambarUrl 
                            ? `<img src="${gambarUrl}" alt="${AppState.currentDetailProduct.nama}" class="w-full h-64 object-cover rounded-xl shadow-lg">`
                            : `<div class="w-full h-64 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-box text-6xl text-blue-600"></i>
                               </div>`
                        }
                    </div>
                    <div class="space-y-4">
                        <div>
                            <h4 class="text-2xl font-bold text-gray-800 mb-2">${AppState.currentDetailProduct.nama}</h4>
                            <div class="flex items-center space-x-2">
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold">
                                    <i class="fas fa-tag mr-1"></i>${AppState.currentDetailProduct.kategori}
                                </span>
                                <span class="px-3 py-1 ${AppState.currentDetailProduct.stok > 10 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'} rounded-full text-sm font-semibold">
                                    <i class="fas fa-box mr-1"></i>Stok: ${AppState.currentDetailProduct.stok}
                                </span>
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
                                <span class="font-bold ${AppState.currentDetailProduct.stok > 0 ? 'text-green-600' : 'text-red-600'}">
                                    ${AppState.currentDetailProduct.stok > 0 ? 'Tersedia' : 'Stok Habis'}
                                </span>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-sm font-semibold text-gray-700 mb-2">Deskripsi</p>
                            <p class="text-gray-600 text-sm">${AppState.currentDetailProduct.deskripsi}</p>
                        </div>
                    </div>
                </div>
            `;

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
                    Utils.showError('Stok Tidak Cukup!', `Stok tersedia: ${AppState.currentDetailProduct.stok} pcs`);
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

    // ====================================================================
    // CART MANAGEMENT
    // ====================================================================
    const CartManager = {
        init() {
            // Product click to add
            DOM.produkList.forEach(item => {
                item.addEventListener('click', (e) => {
                    if (e.target.closest('.info-btn')) return;
                    this.addToCart(e.currentTarget);
                });
            });
            
            // Mobile cart modal
            DOM.btnShowCart?.addEventListener('click', () => {
                DOM.cartModal.classList.remove('hidden');
            });
            
            DOM.btnCloseCart?.addEventListener('click', () => {
                DOM.cartModal.classList.add('hidden');
            });
            
            // Payment input
            DOM.bayar?.addEventListener('input', () => this.calculateKembalian());
            DOM.bayarMobile?.addEventListener('input', (e) => {
                if (DOM.bayar) DOM.bayar.value = e.target.value;
                this.calculateKembalian();
            });
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
                AppState.cart.push({id, nama, harga, qty: 1, stok, gambar});
                this.updateCart();
                Utils.showToast('Ditambahkan ke keranjang');
            }
        },
        
        updateCart() {
            const emptyHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-shopping-basket text-4xl text-gray-300 mb-2"></i>
                    <p class="text-gray-500 text-sm">Keranjang kosong</p>
                </div>
            `;

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
                    </div>
                `).join('');

                DOM.cartItems.innerHTML = cartHTML;
                DOM.cartItemsMobile.innerHTML = cartHTML;
                AppState.totalBelanja = AppState.cart.reduce((sum, item) => sum + (item.harga * item.qty), 0);
            }

            DOM.cartCount.textContent = AppState.cart.length;
            DOM.cartBadge.textContent = AppState.cart.length;
            DOM.totalAmount.textContent = Utils.formatRupiah(AppState.totalBelanja);
            DOM.totalAmountMobile.textContent = Utils.formatRupiah(AppState.totalBelanja);
            
            this.calculateKembalian();
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
            const result = await Utils.showConfirm(
                'Hapus Item?',
                `Hapus <strong>${item.nama}</strong> dari keranjang?`,
                'Hapus'
            );
            
            if (result.isConfirmed) {
                AppState.cart = AppState.cart.filter(i => i.id !== id);
                this.updateCart();
                Utils.showToast('Item berhasil dihapus', 'success');
            }
        },
        
        calculateKembalian() {
            if (AppState.paymentType === 'piutang') {
                const canProcess = AppState.cart.length > 0;
                if (DOM.btnProses) DOM.btnProses.disabled = !canProcess;
                if (DOM.btnProsesMobile) DOM.btnProsesMobile.disabled = !canProcess;
            } else {
                const bayar = parseFloat(DOM.bayar?.value || 0);
                const kembalian = bayar - AppState.totalBelanja;

                const kembalianText = Utils.formatRupiah(kembalian >= 0 ? kembalian : 0);
                if (DOM.kembalian) DOM.kembalian.textContent = kembalianText;
                if (DOM.kembalianMobile) DOM.kembalianMobile.textContent = kembalianText;

                const canProcess = AppState.cart.length > 0 && bayar >= AppState.totalBelanja;
                if (DOM.btnProses) DOM.btnProses.disabled = !canProcess;
                if (DOM.btnProsesMobile) DOM.btnProsesMobile.disabled = !canProcess;
            }
        },
        
        async reset() {
            if (AppState.cart.length > 0) {
                const result = await Utils.showConfirm(
                    'Konfirmasi Reset',
                    'Yakin ingin menghapus semua item?',
                    'Ya, Reset'
                );
                
                if (result.isConfirmed) {
                    AppState.cart = [];
                    this.updateCart();
                    if (DOM.bayar) DOM.bayar.value = '';
                    if (DOM.bayarMobile) DOM.bayarMobile.value = '';
                    Utils.showToast('Keranjang telah dikosongkan', 'success');
                }
                return;
            }

            AppState.cart = [];
            this.updateCart();
            if (DOM.bayar) DOM.bayar.value = '';
            if (DOM.bayarMobile) DOM.bayarMobile.value = '';
        }
    };

    // ====================================================================
    // TRANSACTION PROCESSING
    // ====================================================================
    const TransactionManager = {
        init() {
            DOM.btnProses?.addEventListener('click', () => this.processTransaction());
            DOM.btnProsesMobile?.addEventListener('click', () => this.processTransaction());
            DOM.btnReset?.addEventListener('click', () => CartManager.reset());
            DOM.btnResetMobile?.addEventListener('click', () => CartManager.reset());
        },
        
        async processTransaction() {
            if (AppState.cart.length === 0) {
                Utils.showError('Keranjang Kosong!', 'Tambahkan produk terlebih dahulu');
                return;
            }

            let bayar = 0;
            let statusPembayaran = 'lunas';

            if (AppState.paymentType === 'tunai') {
                bayar = parseFloat(DOM.bayar?.value || 0);

                if (bayar < AppState.totalBelanja) {
                    Utils.showError('Pembayaran Kurang!', `Kurang: ${Utils.formatRupiah(AppState.totalBelanja - bayar)}`);
                    return;
                }

                const result = await Swal.fire({
                    title: 'Proses Transaksi Tunai?',
                    html: `
                        <div class="text-left space-y-2 bg-gray-50 rounded-xl p-4">
                            <div class="flex justify-between">
                                <span>Total:</span>
                                <span class="font-bold">${Utils.formatRupiah(AppState.totalBelanja)}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Bayar:</span>
                                <span class="font-bold">${Utils.formatRupiah(bayar)}</span>
                            </div>
                            <div class="flex justify-between border-t pt-2">
                                <span>Kembalian:</span>
                                <span class="font-bold text-blue-600">${Utils.formatRupiah(bayar - AppState.totalBelanja)}</span>
                            </div>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Proses',
                    cancelButtonText: 'Batal'
                });

                if (!result.isConfirmed) return;
            } else {
                bayar = 0;
                statusPembayaran = 'belum_bayar';

                const result = await Swal.fire({
                    title: 'Proses Transaksi Piutang?',
                    html: `
                        <div class="text-left space-y-2 bg-orange-50 rounded-xl p-4">
                            <div class="flex items-center mb-3">
                                <i class="fas fa-exclamation-circle text-orange-600 text-2xl mr-2"></i>
                                <span class="font-bold text-gray-800">Mode Piutang</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Total Tagihan:</span>
                                <span class="font-bold text-orange-600">${Utils.formatRupiah(AppState.totalBelanja)}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Status:</span>
                                <span class="font-bold text-red-600">Belum Bayar</span>
                            </div>
                            <p class="text-xs text-gray-600 mt-3 italic">Transaksi akan tersimpan dengan status belum bayar</p>
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Proses Piutang',
                    confirmButtonColor: '#f97316',
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
                total_bayar: bayar,
                total_pembayaran: AppState.totalBelanja,
                status_pembayaran: statusPembayaran
            };

            try {
                const response = await fetch('{{ route("transaksi.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(dataTransaksi)
                });

                const data = await response.json();

                if (data.success) {
                    let printSuccess = false;
                    
                    // Auto Print dengan Comprehensive Error Handling
                    if (data.data.auto_print && data.data.printer_name) {
                        if (typeof window.PrinterHelper === 'undefined') {
                            console.warn('⚠️ PrinterHelper not loaded. Skipping auto print.');
                        } else if (!window.PrinterHelper.device) {
                            console.warn('⚠️ Printer not configured. Skipping auto print.');
                        } else {
                            try {
                                const receiptData = {
                                    no_transaksi: `#${String(data.data.id_penjualan).padStart(6, '0')}`,
                                    tanggal: new Date().toLocaleString('id-ID'),
                                    kasir: '{{ Auth::user()->nama_user ?? Auth::user()->name }}',
                                    total_pembayaran: AppState.totalBelanja,
                                    total_bayar: bayar,
                                    kembalian_pembayaran: bayar - AppState.totalBelanja,
                                    status_pembayaran: statusPembayaran,
                                    items: AppState.cart.map(item => ({
                                        nama_produk: item.nama,
                                        qty_produk: item.qty,
                                        harga_produk: item.harga,
                                        subtotal_harga: item.harga * item.qty
                                    }))
                                };

                                await window.PrinterHelper.printReceipt(receiptData);
                                printSuccess = true;
                                console.log('✅ Receipt printed successfully');
                            } catch (printError) {
                                console.error('❌ Auto print failed:', printError.message);
                                // Tidak menampilkan error ke user karena transaksi tetap berhasil
                            }
                        }
                    }

                    if (statusPembayaran === 'lunas') {
                        const swalResult = await Swal.fire({
                            icon: 'success',
                            title: 'Transaksi Berhasil!',
                            html: `
                                <div class="bg-emerald-50 rounded-xl p-4">
                                    <p class="text-sm text-gray-600">ID Transaksi</p>
                                    <p class="text-2xl font-bold text-emerald-600">#${data.data.id_penjualan}</p>
                                    <p class="text-xs text-gray-500 mt-2">Status: Lunas</p>
                                    ${printSuccess ? '<p class="text-xs text-green-600 mt-2">✓ Struk sudah dicetak otomatis</p>' : ''}
                                    ${!printSuccess && data.data.auto_print ? '<p class="text-xs text-orange-600 mt-2">⚠ Auto print gagal. Gunakan tombol Cetak Struk.</p>' : ''}
                                </div>
                            `,
                            showCancelButton: !printSuccess,
                            confirmButtonText: 'OK',
                            cancelButtonText: '<i class="fas fa-print mr-2"></i>Cetak Struk',
                            confirmButtonColor: '#10b981',
                            cancelButtonColor: '#3b82f6',
                            timer: printSuccess ? 3000 : undefined,
                            timerProgressBar: printSuccess
                        });
                        
                        // Jika user klik "Cetak Struk"
                        if (swalResult.dismiss === Swal.DismissReason.cancel) {
                            window.open(`/transaksi/struk/${data.data.id_penjualan}`, '_blank');
                        }
                    } else {
                        const swalResult = await Swal.fire({
                            icon: 'success',
                            title: 'Piutang Tercatat!',
                            html: `
                                <div class="bg-orange-50 rounded-xl p-4">
                                    <p class="text-sm text-gray-600">ID Transaksi</p>
                                    <p class="text-2xl font-bold text-orange-600">#${data.data.id_penjualan}</p>
                                    <p class="text-xs text-red-600 mt-2 font-semibold">Status: Belum Bayar</p>
                                    <p class="text-xs text-gray-600 mt-2">Total Tagihan: ${Utils.formatRupiah(AppState.totalBelanja)}</p>
                                </div>
                            `,
                            showCancelButton: true,
                            confirmButtonText: 'OK',
                            cancelButtonText: '<i class="fas fa-print mr-2"></i>Cetak Nota',
                            confirmButtonColor: '#10b981',
                            cancelButtonColor: '#3b82f6'
                        });
                        
                        // Jika user klik "Cetak Nota"
                        if (swalResult.dismiss === Swal.DismissReason.cancel) {
                            window.open(`/transaksi/struk/${data.data.id_penjualan}`, '_blank');
                        }
                    }

                    // Reset
                    this.resetAfterTransaction();
                    setTimeout(() => location.reload(), 1500);

                } else {
                    throw new Error(data.message || 'Transaksi gagal');
                }

            } catch (error) {
                console.error('Error:', error);
                Utils.showError('Gagal!', 'Terjadi kesalahan saat memproses transaksi: ' + error.message);
                this.setLoadingState(false);
            }
        },
        
        setLoadingState(isLoading) {
            const text = isLoading 
                ? '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...'
                : '<i class="fas fa-check-circle mr-2"></i>Proses Transaksi';
            
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
            if (DOM.bayar) DOM.bayar.value = '';
            if (DOM.bayarMobile) DOM.bayarMobile.value = '';
            
            AppState.paymentType = 'tunai';
            DOM.paymentTypeCards.forEach(c => c.classList.remove('active'));
            document.querySelectorAll('.payment-type-card[data-type="tunai"]').forEach(c => c.classList.add('active'));
            DOM.formTunai?.classList.remove('hidden');
            DOM.infoPiutang?.classList.add('hidden');
            DOM.formTunaiMobile?.classList.remove('hidden');
            DOM.infoPiutangMobile?.classList.add('hidden');
        }
    };

    // ====================================================================
    // PIUTANG MANAGEMENT
    // ====================================================================
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
            DOM.piutangList.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-4xl text-gray-300 mb-2"></i>
                    <p class="text-gray-500">Memuat data...</p>
                </div>
            `;

            try {
                const response = await fetch('{{ route("transaksi.piutang") }}');
                const result = await response.json();
                
                if (result.success) {
                    AppState.piutangData = result.data;
                    this.renderPiutangList();
                } else {
                    throw new Error(result.message || 'Gagal memuat data');
                }
            } catch (error) {
                console.error('Error:', error);
                DOM.piutangList.innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-exclamation-circle text-4xl text-red-300 mb-2"></i>
                        <p class="text-red-500">Gagal memuat data piutang</p>
                    </div>
                `;
            }
        },
        
        filterPiutang(btn) {
            DOM.piutangFilterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            AppState.currentPiutangFilter = btn.dataset.status;
            this.renderPiutangList();
        },
        
        renderPiutangList() {
            const filtered = AppState.currentPiutangFilter === 'all' 
                ? AppState.piutangData 
                : AppState.piutangData.filter(p => p.status_pembayaran === AppState.currentPiutangFilter);

            if (filtered.length === 0) {
                DOM.piutangList.innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                        <p class="text-gray-500">Tidak ada data piutang</p>
                    </div>
                `;
                return;
            }

            const html = filtered.map(p => `
                <div class="bg-gradient-to-r ${p.status_pembayaran === 'belum_bayar' ? 'from-orange-50 to-red-50 border-orange-200' : 'from-green-50 to-emerald-50 border-green-200'} border-2 rounded-xl p-4">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">${p.tanggal_penjualan}</p>
                            <p class="font-bold text-lg text-gray-800">#${p.id_penjualan}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold ${p.status_pembayaran === 'belum_bayar' ? 'bg-red-600 text-white' : 'bg-green-600 text-white'}">
                            ${p.status_pembayaran === 'belum_bayar' ? 'Belum Bayar' : 'Lunas'}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Total Tagihan</p>
                            <p class="text-xl font-bold ${p.status_pembayaran === 'belum_bayar' ? 'text-orange-600' : 'text-green-600'}">
                                ${Utils.formatRupiah(p.total_pembayaran)}
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="window.piutangShowDetail('${p.id_penjualan}')" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm transition">
                                <i class="fas fa-eye mr-1"></i>Detail
                            </button>
                            ${p.status_pembayaran === 'belum_bayar' ? `
                                <button onclick="window.piutangBayar('${p.id_penjualan}', ${p.total_pembayaran})" class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm transition">
                                    <i class="fas fa-check mr-1"></i>Bayar
                                </button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `).join('');

            DOM.piutangList.innerHTML = html;
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
                        </tr>
                    `).join('');

                    DOM.detailPiutangContent.innerHTML = `
                        <div class="space-y-4">
                            <div class="bg-gray-50 rounded-xl p-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-600">ID Transaksi</p>
                                        <p class="font-bold text-lg">#${data.id_penjualan}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Tanggal</p>
                                        <p class="font-bold">${data.tanggal_penjualan}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Status</p>
                                        <span class="px-3 py-1 rounded-full text-xs font-bold ${data.status_pembayaran === 'belum_bayar' ? 'bg-red-600' : 'bg-green-600'} text-white">
                                            ${data.status_pembayaran === 'belum_bayar' ? 'Belum Bayar' : 'Lunas'}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Total</p>
                                        <p class="font-bold text-lg text-emerald-600">${Utils.formatRupiah(data.total_pembayaran)}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-4 py-2 text-left">Produk</th>
                                            <th class="px-4 py-2 text-center">Qty</th>
                                            <th class="px-4 py-2 text-right">Harga</th>
                                            <th class="px-4 py-2 text-right">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>${items}</tbody>
                                </table>
                            </div>
                        </div>
                    `;

                    DOM.detailPiutangModal.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Error:', error);
                Utils.showError('Error', 'Gagal memuat detail piutang');
            }
        },
        
        async bayarPiutang(idPenjualan, totalPembayaran) {
            const result = await Swal.fire({
                title: 'Konfirmasi Pembayaran',
                html: `
                    <div class="text-left space-y-3">
                        <div class="bg-orange-50 rounded-xl p-4">
                            <p class="text-sm text-gray-600 mb-1">ID Transaksi</p>
                            <p class="font-bold text-lg">#${idPenjualan}</p>
                        </div>
                        <div class="bg-emerald-50 rounded-xl p-4">
                            <p class="text-sm text-gray-600 mb-1">Total yang harus dibayar</p>
                            <p class="font-bold text-2xl text-emerald-600">${Utils.formatRupiah(totalPembayaran)}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah Pembayaran</label>
                            <input type="number" id="bayarPiutangInput" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:outline-none" value="${totalPembayaran}" min="${totalPembayaran}">
                        </div>
                        <div id="kembalianPiutang" class="bg-blue-50 rounded-xl p-4 hidden">
                            <div class="flex justify-between">
                                <span class="text-gray-700 font-semibold">Kembalian:</span>
                                <span id="kembalianPiutangValue" class="font-bold text-blue-600">Rp 0</span>
                            </div>
                        </div>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Bayar Sekarang',
                confirmButtonColor: '#10b981',
                cancelButtonText: 'Batal',
                didOpen: () => {
                    const input = document.getElementById('bayarPiutangInput');
                    const kembalianDiv = document.getElementById('kembalianPiutang');
                    const kembalianValue = document.getElementById('kembalianPiutangValue');
                    
                    input.addEventListener('input', function() {
                        const bayar = parseFloat(this.value || 0);
                        const kembalian = bayar - totalPembayaran;
                        
                        if (kembalian >= 0) {
                            kembalianDiv.classList.remove('hidden');
                            kembalianValue.textContent = Utils.formatRupiah(kembalian);
                        } else {
                            kembalianDiv.classList.add('hidden');
                        }
                    });
                },
                preConfirm: () => {
                    const bayar = parseFloat(document.getElementById('bayarPiutangInput').value);
                    if (bayar < totalPembayaran) {
                        Swal.showValidationMessage('Pembayaran kurang dari total tagihan');
                        return false;
                    }
                    return bayar;
                }
            });

            if (result.isConfirmed) {
                try {
                    const response = await fetch(`/transaksi/bayar-piutang/${idPenjualan}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ total_bayar: result.value })
                    });

                    const apiResult = await response.json();

                    if (apiResult.success) {
                        const kembalian = result.value - totalPembayaran;
                        let piutangPrintSuccess = false;
                        
                        // Auto Print untuk Piutang dengan Comprehensive Error Handling
                        if (apiResult.data && apiResult.data.auto_print && apiResult.data.printer_name) {
                            if (typeof window.PrinterHelper === 'undefined') {
                                console.warn('⚠️ PrinterHelper not loaded. Skipping auto print.');
                            } else if (!window.PrinterHelper.device) {
                                console.warn('⚠️ Printer not configured. Skipping auto print.');
                            } else {
                                try {
                                    const detailResponse = await fetch(`/transaksi/detail/${idPenjualan}`);
                                    const detailResult = await detailResponse.json();
                                    
                                    if (detailResult.success) {
                                        const receiptData = {
                                            no_transaksi: `#${String(idPenjualan).padStart(6, '0')}`,
                                            tanggal: new Date().toLocaleString('id-ID'),
                                            kasir: '{{ Auth::user()->nama_user ?? Auth::user()->name }}',
                                            total_pembayaran: totalPembayaran,
                                            total_bayar: result.value,
                                            kembalian_pembayaran: kembalian,
                                            status_pembayaran: 'lunas',
                                            items: detailResult.data.items.map(item => ({
                                                nama_produk: item.nama_produk,
                                                qty_produk: item.qty,
                                                harga_produk: item.harga,
                                                subtotal_harga: item.subtotal
                                            }))
                                        };

                                        await window.PrinterHelper.printReceipt(receiptData);
                                        piutangPrintSuccess = true;
                                        console.log('✅ Piutang receipt printed successfully');
                                    }
                                } catch (printError) {
                                    console.error('❌ Auto print failed:', printError.message);
                                    // Tidak menampilkan error ke user karena pembayaran tetap berhasil
                                }
                            }
                        }
                        
                        const swalResult = await Swal.fire({
                            icon: 'success',
                            title: 'Pembayaran Berhasil!',
                            html: `
                                <div class="bg-green-50 rounded-xl p-4">
                                    <p class="text-sm text-gray-600">Piutang #${idPenjualan} telah lunas</p>
                                    <p class="text-lg font-bold text-green-600 mt-2">Kembalian: ${Utils.formatRupiah(kembalian)}</p>
                                    ${piutangPrintSuccess ? '<p class="text-xs text-green-600 mt-2">✓ Struk sudah dicetak otomatis</p>' : ''}
                                    ${!piutangPrintSuccess && apiResult.data && apiResult.data.auto_print ? '<p class="text-xs text-orange-600 mt-2">⚠ Auto print gagal. Gunakan tombol Cetak Struk.</p>' : ''}
                                </div>
                            `,
                            showCancelButton: !piutangPrintSuccess,
                            confirmButtonText: 'OK',
                            cancelButtonText: '<i class="fas fa-print mr-2"></i>Cetak Struk',
                            confirmButtonColor: '#10b981',
                            cancelButtonColor: '#3b82f6'
                        });
                        
                        // Jika user klik "Cetak Struk"
                        if (swalResult.dismiss === Swal.DismissReason.cancel) {
                            window.open(`/transaksi/struk/${idPenjualan}`, '_blank');
                        }
                        
                        this.loadPiutang();
                        this.closeDetailPiutang();
                    } else {
                        throw new Error(apiResult.message || 'Gagal memproses pembayaran');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Utils.showError('Error', error.message);
                }
            }
        },
        
        closePiutangModal() {
            DOM.piutangModal.classList.add('hidden');
        },
        
        closeDetailPiutang() {
            DOM.detailPiutangModal.classList.add('hidden');
        }
    };

    // ====================================================================
    // GLOBAL FUNCTIONS (untuk onclick di HTML)
    // ====================================================================
    window.cartChangeQty = (id, delta) => CartManager.changeQty(id, delta);
    window.cartRemoveItem = (id) => CartManager.removeItem(id);
    window.piutangShowDetail = (id) => PiutangManager.showDetailPiutang(id);
    window.piutangBayar = (id, total) => PiutangManager.bayarPiutang(id, total);

    // ====================================================================
    // INITIALIZE APP
    // ====================================================================
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

    // Run when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initApp);
    } else {
        initApp();
    }

})();
    </script>
@endpush