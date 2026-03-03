@extends('layouts.app')

@section('title', 'Transaksi - WPOS')
@section('page-title', 'Transaksi Kasir')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
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
            overflow: hidden;
        }

        .payment-row .payment-method-select {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.35rem;
            margin-bottom: 0.5rem;
        }

        .pay-method-btn {
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 0.4rem 0.2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            background: white;
            font-size: 0.65rem;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
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

        .pay-method-btn.active-bayarnanti {
            border-color: #8b5cf6;
            background: linear-gradient(135deg, #ede9fe, #ddd6fe);
        }

        .panel-produk,
        .panel-keranjang {
            display: flex;
            flex-direction: column;
        }

        .panel-produk .panel-body,
        .panel-keranjang .panel-body {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .grid-transaksi {
            align-items: stretch;
        }

        .grid-transaksi>.col-produk,
        .grid-transaksi>.col-keranjang {
            display: flex;
            flex-direction: column;
        }

        .grid-transaksi>.col-produk>.panel-produk,
        .grid-transaksi>.col-keranjang>.panel-keranjang {
            flex: 1;
        }

        /* ── Cart Item ── */
        #cartItems,
        #cartItemsMobile {
            overflow-y: auto;
            overflow-x: hidden;
            scrollbar-gutter: stable;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        #cartItems::-webkit-scrollbar,
        #cartItemsMobile::-webkit-scrollbar {
            width: 4px;
        }

        #cartItems::-webkit-scrollbar-thumb,
        #cartItemsMobile::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 99px;
        }

        .cart-item-card {
            background: #fff;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 8px 10px;
            box-sizing: border-box;
            width: 100%;
            margin-bottom: 6px;
            transition: border-color 0.2s;
        }

        .cart-item-card:last-child {
            margin-bottom: 0;
        }

        .cart-item-card:hover {
            border-color: #10b981;
        }

        .cart-row1 {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
            margin-bottom: 6px;
        }

        .cart-item-name {
            font-weight: 700;
            font-size: 13px;
            color: #0f172a;
            flex: 1;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .cart-item-unit-price {
            font-size: 11px;
            color: #94a3b8;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .cart-row2 {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 4px;
        }

        .cart-item-total {
            font-size: 13px;
            font-weight: 700;
            color: #10b981;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .cart-ctrl {
            display: flex;
            align-items: center;
            gap: 4px;
            flex-shrink: 0;
        }

        .cc-btn {
            width: 26px;
            height: 26px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.15s;
            font-size: 10px;
        }

        .cc-btn.sub {
            background: #fef2f2;
            color: #dc2626;
        }

        .cc-btn.sub:hover {
            background: #dc2626;
            color: #fff;
        }

        .cc-btn.add {
            background: #f0fdf4;
            color: #16a34a;
        }

        .cc-btn.add:hover {
            background: #16a34a;
            color: #fff;
        }

        .cc-btn.del {
            background: #fef2f2;
            color: #dc2626;
            border: 1.5px solid #fecaca;
        }

        .cc-btn.del:hover {
            background: #dc2626;
            color: #fff;
            border-color: #dc2626;
        }

        /* ── Editable Qty Input ── */
        .cc-qty-input {
            width: 40px;
            text-align: center;
            font-weight: 700;
            font-size: 13px;
            color: #1e293b;
            border: 2px solid #e2e8f0;
            border-radius: 6px;
            padding: 2px 2px;
            background: #f8fafc;
            outline: none;
            transition: border-color 0.2s, background 0.2s;
            -moz-appearance: textfield;
        }

        .cc-qty-input:focus {
            border-color: #10b981;
            background: #fff;
        }

        .cc-qty-input::-webkit-outer-spin-button,
        .cc-qty-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* ── Tab Filter ── */
        .tab-filter-btn {
            padding: 0.4rem 0.85rem;
            border-radius: 0.6rem;
            font-size: 0.78rem;
            font-weight: 700;
            border: 2px solid #e5e7eb;
            background: white;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .tab-filter-btn:hover {
            border-color: #10b981;
            color: #059669;
        }

        .tab-filter-btn.active {
            background: linear-gradient(135deg, #10b981, #059669);
            border-color: #10b981;
            color: white;
        }

        .tab-filter-btn.terlaris-btn.active {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border-color: #f59e0b;
            color: white;
        }

        /* ── Badge Terlaris ── */
        .badge-terlaris {
            position: absolute;
            top: -4px;
            left: -4px;
            background: linear-gradient(135deg, #f59e0b, #ef4444);
            color: white;
            font-size: 9px;
            font-weight: 800;
            padding: 2px 5px;
            border-radius: 6px;
            z-index: 10;
        }

        /* Pagination styles */
        .piutang-page-btn {
            width: 2rem;
            height: 2rem;
            border-radius: 0.5rem;
            border: 2px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
            font-weight: 700;
            transition: all 0.2s;
            cursor: pointer;
            background: white;
            color: #4b5563;
        }

        .piutang-page-btn:hover:not(:disabled) {
            border-color: #f97316;
            color: #f97316;
        }

        .piutang-page-btn.active {
            border-color: #f97316;
            background: linear-gradient(135deg, #f97316, #ea580c);
            color: white;
        }

        .piutang-page-btn:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }
    </style>
@endpush

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-lg px-6 py-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                    <i class="fas fa-cash-register mr-2 text-emerald-600"></i>Transaksi Kasir
                </h1>
                <div class="flex flex-wrap items-center gap-3">
                    @if (!Auth::user()->isOwner())
                        <a href="{{ route('dashboard') }}"
                            class="flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm transition-all shadow-sm
                            {{ request()->routeIs('dashboard') ? 'bg-gray-700 text-white shadow-md' : 'bg-gray-100 hover:bg-gray-700 text-gray-600 hover:text-white' }}">
                            <i class="fas fa-home"></i><span class="hidden sm:inline">Dashboard</span>
                        </a>
                        <a href="{{ route('kasir.index') }}"
                            class="flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm transition-all shadow-sm
                            {{ request()->routeIs('kasir.*') ? 'bg-blue-600 text-white shadow-md' : 'bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white' }}">
                            <i class="fas fa-cash-register"></i><span class="hidden sm:inline">Buka/Tutup Kasir</span>
                        </a>
                        <a href="{{ route('laporan.index') }}"
                            class="flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm transition-all shadow-sm
                            {{ request()->routeIs('laporan.*') && !request()->routeIs('keuangan.*') ? 'bg-indigo-600 text-white shadow-md' : 'bg-indigo-50 hover:bg-indigo-600 text-indigo-600 hover:text-white' }}">
                            <i class="fas fa-chart-line"></i><span class="hidden sm:inline">Laporan</span>
                        </a>
                        <div class="w-px h-8 bg-gray-200 hidden sm:block mx-1"></div>
                    @endif
                    <button id="btnShowPiutang"
                        class="flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm transition-all shadow-sm bg-orange-50 hover:bg-orange-600 text-orange-600 hover:text-white">
                        <i class="fas fa-clock"></i>
                        <span class="hidden sm:inline">Daftar Piutang</span>
                        <span class="sm:hidden">Piutang</span>
                    </button>
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 grid-transaksi items-stretch">

            <!-- ── Daftar Produk ── -->
            <div class="lg:col-span-2 col-produk">
                <div class="bg-white rounded-2xl shadow-lg p-4 md:p-6 panel-produk h-full">

                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-3">
                                <i class="fas fa-box text-white"></i>
                            </div>
                            Daftar Produk
                        </h2>
                    </div>

                    {{-- ── Tab Filter: Terlaris + Kategori Dinamis ── --}}
                    <div class="mb-4 overflow-x-auto">
                        <div class="flex gap-2 pb-1" id="tabFilterContainer" style="min-width: max-content;">

                            {{-- Semua --}}
                            <button class="tab-filter-btn active" data-filter="all" data-type="all"
                                onclick="TabFilter.select(this)">
                                <i class="fas fa-th-large mr-1"></i> Semua
                            </button>

                            {{-- Terlaris --}}
                            <button class="tab-filter-btn terlaris-btn" data-filter="terlaris" data-type="terlaris"
                                onclick="TabFilter.select(this)">
                                <i class="fas fa-fire mr-1"></i> Terlaris
                            </button>

                            <div class="w-px bg-gray-200 self-stretch mx-1"></div>

                            {{-- Kategori dari DB --}}
                            @foreach ($kategori as $kat)
                                @php
                                    $iconMap = [
                                        'makanan' => ['icon' => 'fa-utensils', 'color' => '#f97316'],
                                        'minuman' => ['icon' => 'fa-coffee', 'color' => '#3b82f6'],
                                        'snack' => ['icon' => 'fa-cookie-bite', 'color' => '#eab308'],
                                        'kebutuhan rumah tangga' => ['icon' => 'fa-home', 'color' => '#8b5cf6'],
                                        'kebersihan' => ['icon' => 'fa-spray-can', 'color' => '#06b6d4'],
                                        'kesehatan' => ['icon' => 'fa-heartbeat', 'color' => '#ef4444'],
                                        'elektronik' => ['icon' => 'fa-plug', 'color' => '#6366f1'],
                                    ];
                                    $key = strtolower($kat->nama_kategori);
                                    $meta = $iconMap[$key] ?? ['icon' => 'fa-tag', 'color' => '#6b7280'];
                                    $icon = $kat->icon ?? $meta['icon'];
                                    $color = $kat->warna ?? $meta['color'];
                                @endphp
                                <button class="tab-filter-btn" data-filter="{{ strtolower($kat->nama_kategori) }}"
                                    data-type="kategori" onclick="TabFilter.select(this)">
                                    <i class="fas {{ $icon }} mr-1" style="color: {{ $color }}"></i>
                                    {{ $kat->nama_kategori }}
                                </button>
                            @endforeach

                        </div>
                    </div>

                    {{-- Data terlaris untuk JS --}}
                    <script>
                        window.TERLARIS_IDS = @json($terlarisIds ?? []);
                    </script>

                    <!-- Search -->
                    <div class="mb-4">
                        <div class="relative">
                            <input type="text" id="searchProduk"
                                class="w-full pl-12 pr-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:outline-none transition-all"
                                placeholder="Cari produk...">
                            <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>

                    <div id="produkList"
                        class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 flex-1 overflow-y-auto pr-1"
                        style="max-height: calc(130vh - 22rem); min-height: 300px;">
                        @foreach ($produk as $item)
                            @php
                                $isTerlaris = in_array($item->id_produk, $terlarisIds ?? []);
                                $terlarisRank = $isTerlaris
                                    ? array_search($item->id_produk, $terlarisIds ?? []) + 1
                                    : null;
                            @endphp
                            <div class="produk-item group border-2 {{ $isTerlaris ? 'border-amber-200' : 'border-gray-200' }} rounded-xl p-3
                                hover:border-emerald-500 hover:shadow-xl transition-all duration-300 cursor-pointer
                                {{ $item->stock_produk <= 0 ? 'opacity-50' : '' }}"
                                data-id="{{ $item->id_produk }}" data-nama="{{ $item->nama_produk }}"
                                data-harga="{{ $item->harga_produk }}" data-stok="{{ $item->stock_produk }}"
                                data-kategori="{{ strtolower($item->kategori_produk ?? 'lainnya') }}"
                                data-kode="{{ $item->kode_produk ?? '-' }}"
                                data-deskripsi="{{ $item->deskripsi_produk ?? 'Tidak ada deskripsi' }}"
                                data-gambar="{{ $item->gambar_produk ?? '' }}"
                                data-terlaris="{{ $isTerlaris ? 'true' : 'false' }}">
                                <div class="text-center relative">

                                    {{-- Badge Terlaris --}}
                                    @if ($isTerlaris)
                                        <span class="badge-terlaris">
                                            @if ($terlarisRank <= 3)
                                                🏆 #{{ $terlarisRank }}
                                            @else
                                                <i class="fas fa-fire"></i> Laris
                                            @endif
                                        </span>
                                    @endif

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
                                    @if ($isTerlaris && $item->total_terjual)
                                        <p class="text-xs text-amber-600 font-semibold mt-0.5">
                                            <i class="fas fa-chart-bar" style="font-size:9px;"></i>
                                            {{ $item->total_terjual }}x terjual
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>

            <!-- ── Keranjang Desktop ── -->
            <div class="hidden lg:flex col-keranjang">
                <div class="bg-white rounded-2xl shadow-lg panel-keranjang sticky top-6 w-full"
                    style="max-height: calc(200vh - 7rem); overflow-y: auto; padding: 20px;">

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

                    <div id="cartItems"
                        style="max-height: 340px; overflow-y: auto; overflow-x: hidden; margin-bottom: 16px;">
                        <div class="text-center py-8">
                            <i class="fas fa-shopping-basket text-4xl text-gray-300 mb-2"></i>
                            <p class="text-gray-500 text-sm">Keranjang kosong</p>
                        </div>
                    </div>

                    <div class="border-t-2 border-gray-200 pt-4 space-y-3">
                        <div class="bg-gradient-to-r from-emerald-50 to-green-50 rounded-xl p-3">
                            <div class="flex justify-between text-lg font-bold text-gray-800">
                                <span>Total:</span>
                                <span id="totalAmount" class="text-emerald-600">Rp 0</span>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-gray-700 font-semibold text-sm">Metode Pembayaran</label>
                            </div>
                            <div id="paymentRowsDesktop" class="space-y-2"></div>
                        </div>

                        <div id="kembalianSectionDesktop"
                            class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-3">
                            <div class="flex justify-between text-lg">
                                <span class="text-gray-700 font-semibold">Kembalian:</span>
                                <span id="kembalian" class="font-bold text-blue-600">Rp 0</span>
                            </div>
                        </div>

                        <div id="infoPiutang" class="hidden">
                            <div class="bg-gradient-to-r from-purple-50 to-violet-50 rounded-xl p-4">
                                <div class="flex items-start">
                                    <i class="fas fa-clock text-purple-600 text-xl mr-3 mt-1"></i>
                                    <div>
                                        <p class="font-bold text-gray-800 mb-1">Mode Bayar Nanti</p>
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
            <div class="p-4">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-shopping-cart text-emerald-600 mr-2"></i>Keranjang Belanja
                    </h2>
                    <button id="btnCloseCart" class="text-gray-500 hover:text-gray-700 text-2xl"><i
                            class="fas fa-times"></i></button>
                </div>

                <div id="cartItemsMobile" class="mb-4 overflow-y-auto overflow-x-hidden"
                    style="max-height: 220px; padding-right: 4px;">
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

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-gray-700 font-semibold text-sm">Metode Pembayaran</label>
                            <button type="button" id="btnBayarNantiMobile" onclick="toggleBayarNantiGlobal()"
                                class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold border-2 border-purple-300 text-purple-600 hover:bg-purple-600 hover:text-white transition-all">
                                <i class="fas fa-clock mr-1"></i> Bayar Nanti
                            </button>
                        </div>
                        <div id="paymentRowsMobile" class="space-y-2"></div>
                    </div>

                    <div id="kembalianSectionMobile" class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-3">
                        <div class="flex justify-between text-lg">
                            <span class="text-gray-700 font-semibold">Kembalian:</span>
                            <span id="kembalianMobile" class="font-bold text-blue-600">Rp 0</span>
                        </div>
                    </div>

                    <div id="infoPiutangMobile" class="hidden">
                        <div class="bg-gradient-to-r from-purple-50 to-violet-50 rounded-xl p-3">
                            <div class="flex items-start">
                                <i class="fas fa-clock text-purple-600 mr-2 mt-1"></i>
                                <div>
                                    <p class="font-bold text-gray-800 text-sm mb-1">Mode Bayar Nanti</p>
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
                            <i class="fas fa-info-circle text-blue-500 mr-3"></i>Detail Produk
                        </h3>
                        <button id="closeProductDetailBtn" class="text-gray-500 hover:text-gray-700 text-2xl"><i
                                class="fas fa-times"></i></button>
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
                        <button id="closePiutangModalBtn" class="text-white hover:text-gray-200 text-2xl"><i
                                class="fas fa-times"></i></button>
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
                    <div class="mb-3 flex items-center gap-2">
                        <span class="text-sm text-gray-500">Tampilkan:</span>
                        <select id="piutangPerPageSelect"
                            class="px-3 py-1.5 border-2 border-gray-200 rounded-lg text-sm font-semibold text-gray-700 focus:border-orange-400 focus:outline-none"
                            onchange="window.piutangChangePerPage(this.value)">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                        </select>
                        <span class="text-sm text-gray-500">data per halaman</span>
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
                        <button id="closeDetailPiutangBtn" class="text-gray-500 hover:text-gray-700 text-2xl"><i
                                class="fas fa-times"></i></button>
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
        // UTILITY
        // ====================================================================
        function formatRibuanInput(input) {
            let cursorPos = input.selectionStart;
            let rawValue = input.value;
            let digits = rawValue.replace(/\D/g, '');
            let formatted = digits === '' ? '' : parseInt(digits, 10).toLocaleString('id-ID');
            let rawBefore = rawValue.substring(0, cursorPos).replace(/\D/g, '').length;
            input.value = formatted;
            let newPos = 0,
                count = 0;
            for (let i = 0; i < formatted.length; i++) {
                if (/\d/.test(formatted[i])) count++;
                if (count === rawBefore) {
                    newPos = i + 1;
                    break;
                }
            }
            input.setSelectionRange(newPos, newPos);
        }

        function parseRibuan(value) {
            if (typeof value === 'number') return value;
            return parseFloat(String(value).replace(/\./g, '').replace(',', '.')) || 0;
        }

        function toggleBayarNantiGlobal() {
            if (window.PaymentManager && typeof window.PaymentManager.toggleBayarNanti === 'function') {
                window.PaymentManager.toggleBayarNanti();
            } else {
                setTimeout(function() {
                    if (window.PaymentManager) window.PaymentManager.toggleBayarNanti();
                }, 150);
            }
        }

        // ====================================================================
        // APP STATE
        // ====================================================================
        window.AppState = {
            cart: [],
            totalBelanja: 0,
            currentDetailProduct: null,
            paymentRows: [],
            piutangData: [],
            currentPiutangFilter: 'all',
            piutangPage: 1,
            piutangPerPage: 10,
        };

        (function() {
            'use strict';

            const AppState = window.AppState;

            const DOM = {
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
            // TAB FILTER (menggantikan CategoryFilter + SearchManager)
            // ================================================================
            const TabFilter = {
                currentFilter: 'all',
                currentType: 'all',

                select(btn) {
                    document.querySelectorAll('.tab-filter-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    this.currentFilter = btn.dataset.filter;
                    this.currentType = btn.dataset.type;
                    this.apply();
                },

                apply() {
                    const search = DOM.searchProduk?.value.toLowerCase() || '';
                    DOM.produkList.forEach(item => {
                        const nama = item.dataset.nama.toLowerCase();
                        const kode = item.dataset.kode.toLowerCase();
                        const kategori = item.dataset.kategori;
                        const terlaris = item.dataset.terlaris === 'true';

                        const matchSearch = !search || nama.includes(search) || kode.includes(search);
                        let matchFilter = true;
                        if (this.currentType === 'terlaris') matchFilter = terlaris;
                        else if (this.currentType === 'kategori') matchFilter = kategori === this
                            .currentFilter;

                        item.style.display = (matchSearch && matchFilter) ? 'block' : 'none';
                    });
                }
            };

            window.TabFilter = TabFilter;

            const SearchManager = {
                init() {
                    DOM.searchProduk?.addEventListener('input', () => TabFilter.apply());
                }
            };

            // ================================================================
            // PAYMENT MANAGER
            // ================================================================
            const PaymentManager = {
                METHODS: {
                    tunai: {
                        label: 'Tunai',
                        icon: 'fa-money-bill-wave',
                        activeClass: 'active'
                    },
                    qris: {
                        label: 'QRIS',
                        icon: 'fa-qrcode',
                        activeClass: 'active-qris'
                    },
                    bayar_nanti: {
                        label: 'Bayar Nanti',
                        icon: 'fa-clock',
                        activeClass: 'active-bayarnanti'
                    },
                },
                init() {
                    AppState.paymentRows.splice(0, AppState.paymentRows.length, {
                        method: 'tunai',
                        amount: 0
                    });
                    this.renderAll();
                },
                toggleBayarNanti() {
                    if (AppState.paymentRows.length < 3) {
                        AppState.paymentRows.push({
                            method: 'bayar_nanti',
                            amount: 0
                        });
                    } else {
                        AppState.paymentRows[AppState.paymentRows.length - 1].method = 'bayar_nanti';
                    }
                    this.renderAll();
                    this.recalculate();
                },
                updateBayarNantiButtons() {
                    ['btnBayarNantiDesktop', 'btnBayarNantiMobile'].forEach(id => {
                        const btn = document.getElementById(id);
                        if (!btn) return;
                        btn.className =
                            'flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold border-2 border-purple-300 text-purple-600 hover:bg-purple-600 hover:text-white transition-all';
                        btn.innerHTML = '<i class="fas fa-clock mr-1"></i> Bayar Nanti';
                    });
                },
                addRow() {
                    if (AppState.paymentRows.length >= 3) return;
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
                    if (method === 'bayar_nanti') AppState.paymentRows[index].amount = 0;
                    this.renderAll();
                    this.recalculate();
                },
                setAmountFromInput(index, inputEl) {
                    AppState.paymentRows[index].amount = parseRibuan(inputEl.value);
                    PaymentManager.recalculate();
                },
                isPiutangMode() {
                    return AppState.paymentRows.some(r => r.method === 'bayar_nanti');
                },
                getTunaiAmount() {
                    return AppState.paymentRows.filter(r => r.method === 'tunai').reduce((s, r) => s + (r.amount ||
                        0), 0);
                },
                getTotalPaid() {
                    if (this.isPiutangMode()) return 0;
                    return AppState.paymentRows.filter(r => r.method !== 'bayar_nanti').reduce((s, r) => s + (r
                        .amount || 0), 0);
                },
                recalculate() {
                    const hasBayarNanti = AppState.paymentRows.some(r => r.method === 'bayar_nanti');
                    const hasOnlyBayarNanti = AppState.paymentRows.every(r => r.method === 'bayar_nanti');
                    if (hasBayarNanti) {
                        DOM.infoPiutang?.classList.remove('hidden');
                        DOM.infoPiutangMobile?.classList.remove('hidden');
                    } else {
                        DOM.infoPiutang?.classList.add('hidden');
                        DOM.infoPiutangMobile?.classList.add('hidden');
                    }
                    const hasTunai = AppState.paymentRows.some(r => r.method === 'tunai');
                    if (!hasTunai || hasOnlyBayarNanti) {
                        DOM.kembalianSectionDesktop?.classList.add('hidden');
                        DOM.kembalianSectionMobile?.classList.add('hidden');
                    } else {
                        DOM.kembalianSectionDesktop?.classList.remove('hidden');
                        DOM.kembalianSectionMobile?.classList.remove('hidden');
                        const tunaiAmount = this.getTunaiAmount();
                        const qrisAmount = AppState.paymentRows.filter(r => r.method === 'qris').reduce((s, r) =>
                            s + (r.amount || 0), 0);
                        const remainingForTunai = Math.max(0, AppState.totalBelanja - qrisAmount);
                        const kembalian = tunaiAmount - remainingForTunai;
                        const kembalianText = Utils.formatRupiah(kembalian > 0 ? kembalian : 0);
                        if (DOM.kembalian) DOM.kembalian.textContent = kembalianText;
                        if (DOM.kembalianMobile) DOM.kembalianMobile.textContent = kembalianText;
                    }
                    let canProcess = AppState.cart.length > 0;
                    if (!hasBayarNanti) canProcess = canProcess && this.getTotalPaid() >= AppState.totalBelanja;
                    if (DOM.btnProses) DOM.btnProses.disabled = !canProcess;
                    if (DOM.btnProsesMobile) DOM.btnProsesMobile.disabled = !canProcess;
                },
                buildRowHTML(row, index, isMobile) {
                    const canRemove = AppState.paymentRows.length > 1;
                    const displayValue = row.amount > 0 ? row.amount.toLocaleString('id-ID') : '';
                    const methodBtns = Object.entries(this.METHODS).map(([key, meta]) => {
                        const isActive = row.method === key;
                        const activeClass = isActive ? `pay-method-btn ${meta.activeClass}` :
                            'pay-method-btn';
                        return `<button type="button" class="${activeClass}" onclick="PaymentManager.setMethod(${index}, '${key}')" title="${meta.label}">
                            <i class="fas ${meta.icon} mb-1 block text-base"></i><span>${meta.label}</span>
                        </button>`;
                    }).join('');
                    const showAmountInput = row.method === 'tunai';
                    const showQrisNote = row.method === 'qris';
                    const showBayarNanti = row.method === 'bayar_nanti';
                    const inputHtml = (id, label, borderClass = 'border-gray-200', placeholder =
                        'Masukkan nominal') =>
                        `<div><label class="block text-xs text-gray-500 mb-1">${label}</label>
                        <input type="text" inputmode="numeric" id="${id}" placeholder="${placeholder}" value="${displayValue}"
                            class="w-full px-3 py-2 bg-white border-2 ${borderClass} rounded-lg text-sm focus:border-emerald-500 focus:outline-none"
                            oninput="formatRibuanInput(this); PaymentManager.setAmountFromInput(${index}, this)"></div>`;
                    return `<div class="payment-row">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-gray-500">Pembayaran ${index + 1}</span>
                            ${canRemove ? `<button type="button" onclick="PaymentManager.removeRow(${index})" class="text-red-400 hover:text-red-600 text-xs"><i class="fas fa-times"></i></button>` : ''}
                        </div>
                        <div class="payment-method-select">${methodBtns}</div>
                        ${showAmountInput ? inputHtml(`payAmt_${isMobile ? 'M' : 'D'}_${index}`, 'Jumlah Bayar') : ''}
                        ${showQrisNote ? `<div class="bg-indigo-50 rounded-lg p-2 text-xs text-indigo-600 font-semibold text-center mb-2"><i class="fas fa-qrcode mr-1"></i> Pembayaran QRIS — Scan QR Code</div>${inputHtml(`payAmt_${isMobile ? 'M' : 'D'}_${index}`, 'Nominal QRIS', 'border-indigo-200', 'Nominal QRIS')}` : ''}
                        ${showBayarNanti ? `<div class="bg-purple-50 border border-purple-200 rounded-lg p-3 text-sm text-purple-700 font-semibold text-center"><i class="fas fa-clock mr-1"></i> Transaksi akan dicatat sebagai <span class="text-orange-600 font-bold">Piutang</span></div>` : ''}
                    </div>`;
                },
                renderAll() {
                    const isPiutang = this.isPiutangMode();
                    const canAddMore = !isPiutang && AppState.paymentRows.length < 3 && AppState.cart.length > 0;
                    const addBtnHTML = canAddMore ?
                        `<button type="button" onclick="PaymentManager.addRow()"
                            class="w-full mt-2 flex items-center justify-center gap-2 px-4 py-2 border-2 border-dashed border-emerald-400 text-emerald-600 rounded-xl text-sm font-semibold hover:bg-emerald-50 transition">
                            <i class="fas fa-plus"></i> Tambah Metode Bayar</button>` : '';
                    const rowsHTML = AppState.paymentRows.map((row, i) => this.buildRowHTML(row, i, false)).join(
                    '');
                    const rowsHTMLMobile = AppState.paymentRows.map((row, i) => this.buildRowHTML(row, i, true))
                        .join('');
                    if (DOM.paymentRowsDesktop) DOM.paymentRowsDesktop.innerHTML = rowsHTML + addBtnHTML;
                    if (DOM.paymentRowsMobile) DOM.paymentRowsMobile.innerHTML = rowsHTMLMobile + addBtnHTML;
                    this.updateBayarNantiButtons();
                }
            };

            window.PaymentManager = PaymentManager;

            // ================================================================
            // PRODUCT DETAIL MANAGER
            // ================================================================
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
                                    <div class="flex items-center justify-between py-2 border-b"><span class="text-gray-600 font-medium">Kode Produk</span><span class="font-bold text-gray-800">${AppState.currentDetailProduct.kode}</span></div>
                                    <div class="flex items-center justify-between py-2 border-b"><span class="text-gray-600 font-medium">Kategori</span><span class="font-bold text-gray-800 capitalize">${AppState.currentDetailProduct.kategori}</span></div>
                                    <div class="flex items-center justify-between py-2"><span class="text-gray-600 font-medium">Status</span><span class="font-bold ${AppState.currentDetailProduct.stok > 0 ? 'text-green-600' : 'text-red-600'}">${AppState.currentDetailProduct.stok > 0 ? 'Tersedia' : 'Stok Habis'}</span></div>
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

            // ================================================================
            // CART MANAGER
            // ================================================================
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
                            nama: productElement.dataset.nama,
                            harga: parseFloat(productElement.dataset.harga),
                            qty: 1,
                            stok,
                            gambar: productElement.dataset.gambar
                        });
                        this.updateCart();
                        Utils.showToast('Ditambahkan ke keranjang');
                    }
                },
                buildCartItemHTML(item) {
                    return `
                    <div class="cart-item-card">
                        <div class="cart-row1">
                            <span class="cart-item-name" title="${item.nama}">${item.nama}</span>
                            <span class="cart-item-unit-price">${Utils.formatRupiah(item.harga)}/pcs</span>
                        </div>
                        <div class="cart-row2">
                            <span class="cart-item-total">${Utils.formatRupiah(item.harga * item.qty)}</span>
                            <div class="cart-ctrl">
                                <button onclick="window.cartChangeQty('${item.id}',-1)" class="cc-btn sub"><i class="fas fa-minus"></i></button>
                                <input type="number" class="cc-qty-input" value="${item.qty}" min="1" max="${item.stok}"
                                    onclick="this.select()"
                                    onchange="window.cartSetQty('${item.id}', this)"
                                    onkeydown="if(event.key==='Enter'){this.blur()}">
                                <button onclick="window.cartChangeQty('${item.id}',1)" class="cc-btn add"><i class="fas fa-plus"></i></button>
                                <button onclick="window.cartRemoveItem('${item.id}')" class="cc-btn del"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </div>`;
                },
                updateCart() {
                    const emptyHTML =
                        `<div class="text-center py-8"><i class="fas fa-shopping-basket text-4xl text-gray-300 mb-2"></i><p class="text-gray-500 text-sm">Keranjang kosong</p></div>`;
                    if (AppState.cart.length === 0) {
                        DOM.cartItems.innerHTML = emptyHTML;
                        DOM.cartItemsMobile.innerHTML = emptyHTML;
                        AppState.totalBelanja = 0;
                    } else {
                        const cartHTML = AppState.cart.map(item => this.buildCartItemHTML(item)).join('');
                        DOM.cartItems.innerHTML = cartHTML;
                        DOM.cartItemsMobile.innerHTML = cartHTML;
                        AppState.totalBelanja = AppState.cart.reduce((sum, item) => sum + (item.harga * item.qty),
                            0);
                    }
                    DOM.cartCount.textContent = AppState.cart.length;
                    DOM.cartBadge.textContent = AppState.cart.length;
                    DOM.totalAmount.textContent = Utils.formatRupiah(AppState.totalBelanja);
                    DOM.totalAmountMobile.textContent = Utils.formatRupiah(AppState.totalBelanja);
                    if (AppState.cart.length === 0 && AppState.paymentRows.length > 1) {
                        AppState.paymentRows.splice(0, AppState.paymentRows.length, {
                            method: 'tunai',
                            amount: 0
                        });
                    }
                    PaymentManager.renderAll();
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
                setQty(id, inputEl) {
                    const item = AppState.cart.find(i => i.id === id);
                    if (!item) return;
                    let newQty = parseInt(inputEl.value) || 1;
                    if (newQty < 1) newQty = 1;
                    if (newQty > item.stok) {
                        Utils.showError('Stok Tidak Cukup!', `Stok tersedia: ${item.stok} pcs`);
                        newQty = item.stok;
                    }
                    item.qty = newQty;
                    this.updateCart();
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
                            AppState.paymentRows.splice(0, AppState.paymentRows.length, {
                                method: 'tunai',
                                amount: 0
                            });
                            this.updateCart();
                            Utils.showToast('Keranjang telah dikosongkan', 'success');
                        }
                        return;
                    }
                    AppState.cart = [];
                    AppState.paymentRows.splice(0, AppState.paymentRows.length, {
                        method: 'tunai',
                        amount: 0
                    });
                    this.updateCart();
                }
            };

            // ================================================================
            // TRANSACTION MANAGER
            // ================================================================
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
                        bayar_nanti: 'Bayar Nanti'
                    };
                    return AppState.paymentRows.map(r =>
                        `<div class="flex justify-between text-sm"><span>${methodLabels[r.method] || r.method}</span><span class="font-bold">${r.method === 'bayar_nanti' ? Utils.formatRupiah(AppState.totalBelanja) : Utils.formatRupiah(r.amount)}</span></div>`
                    ).join('');
                },
                async processTransaction() {
                    if (AppState.cart.length === 0) {
                        Utils.showError('Keranjang Kosong!', 'Tambahkan produk terlebih dahulu');
                        return;
                    }
                    const hasBayarNanti = PaymentManager.isPiutangMode();
                    let statusPembayaran;
                    if (hasBayarNanti) {
                        const sudahBayarCek = AppState.paymentRows.filter(r => r.method !== 'bayar_nanti')
                            .reduce((s, r) => s + (r.amount || 0), 0);
                        statusPembayaran = sudahBayarCek > 0 ? 'bayar_sebagian' : 'belum_bayar';
                    } else {
                        statusPembayaran = 'lunas';
                    }
                    let totalBayar = 0,
                        kembalian = 0;
                    if (hasBayarNanti) {
                        const sudahBayar = AppState.paymentRows.filter(r => r.method !== 'bayar_nanti').reduce((
                            s, r) => s + (r.amount || 0), 0);
                        totalBayar = sudahBayar;
                        const sisaPiutang = AppState.totalBelanja - sudahBayar;
                        const statusLabel = sudahBayar > 0 ? 'Bayar Sebagian' : 'Belum Bayar';
                        const statusColor = sudahBayar > 0 ? 'text-yellow-600' : 'text-red-600';
                        const result = await Swal.fire({
                            title: 'Konfirmasi Bayar Nanti',
                            html: `<div class="text-left space-y-2 bg-purple-50 rounded-xl p-4">
                                <div class="flex items-center mb-3"><i class="fas fa-clock text-purple-600 text-2xl mr-2"></i><span class="font-bold text-gray-800">Mode Bayar Nanti</span></div>
                                <div class="flex justify-between"><span>Total Tagihan:</span><span class="font-bold text-gray-800">${Utils.formatRupiah(AppState.totalBelanja)}</span></div>
                                ${sudahBayar > 0 ? `<div class="flex justify-between"><span>Dibayar Sekarang:</span><span class="font-bold text-green-600">${Utils.formatRupiah(sudahBayar)}</span></div>` : ''}
                                <div class="flex justify-between"><span>Sisa Piutang:</span><span class="font-bold text-orange-600">${Utils.formatRupiah(sisaPiutang > 0 ? sisaPiutang : AppState.totalBelanja)}</span></div>
                                <div class="flex justify-between border-t pt-2 mt-2"><span>Status:</span><span class="font-bold ${statusColor}">${statusLabel}</span></div>
                                <p class="text-xs text-gray-600 mt-2 italic">Sisa tagihan akan dicatat sebagai piutang</p>
                            </div>`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Proses',
                            confirmButtonColor: '#8b5cf6',
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
                            amount: r.method === 'bayar_nanti' ? AppState.totalBelanja : r
                                .amount
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
                                        console.error('Auto print failed:', printError.message);
                                    }
                                }
                            }
                            if (statusPembayaran === 'lunas') {
                                const swalResult = await Swal.fire({
                                    icon: 'success',
                                    title: 'Transaksi Berhasil!',
                                    html: `<div class="bg-emerald-50 rounded-xl p-4"><p class="text-sm text-gray-600">ID Transaksi</p><p class="text-2xl font-bold text-emerald-600">#${data.data.id_penjualan}</p><p class="text-xs text-gray-500 mt-2">Status: Lunas</p>${printSuccess ? '<p class="text-xs text-green-600 mt-2">✓ Struk sudah dicetak otomatis</p>' : ''}</div>`,
                                    showCancelButton: !printSuccess,
                                    confirmButtonText: 'OK',
                                    cancelButtonText: '<i class="fas fa-print mr-2"></i>Cetak Struk',
                                    confirmButtonColor: '#10b981',
                                    cancelButtonColor: '#3b82f6',
                                    timer: printSuccess ? 3000 : undefined,
                                    timerProgressBar: printSuccess
                                });
                                if (swalResult.dismiss === Swal.DismissReason.cancel) window.open(
                                    `/transaksi/struk/${data.data.id_penjualan}`, '_blank');
                            } else {
                                const isBayarSebagian = statusPembayaran === 'bayar_sebagian';
                                const sudahBayarFinal = AppState.paymentRows.filter(r => r.method !==
                                    'bayar_nanti').reduce((s, r) => s + (r.amount || 0), 0);
                                const sisaFinal = AppState.totalBelanja - sudahBayarFinal;
                                const swalResult = await Swal.fire({
                                    icon: 'success',
                                    title: isBayarSebagian ? 'Bayar Sebagian Tercatat!' :
                                        'Piutang Tercatat!',
                                    html: `<div class="${isBayarSebagian ? 'bg-yellow-50' : 'bg-orange-50'} rounded-xl p-4">
                                        <p class="text-sm text-gray-600">ID Transaksi</p>
                                        <p class="text-2xl font-bold ${isBayarSebagian ? 'text-yellow-600' : 'text-orange-600'}">#${data.data.id_penjualan}</p>
                                        <p class="text-xs font-semibold mt-2 ${isBayarSebagian ? 'text-yellow-600' : 'text-red-600'}">Status: ${isBayarSebagian ? 'Bayar Sebagian' : 'Belum Bayar'}</p>
                                        <div class="border-t mt-3 pt-3 space-y-1">
                                            <p class="text-xs text-gray-600">Total Tagihan: <span class="font-bold">${Utils.formatRupiah(AppState.totalBelanja)}</span></p>
                                            ${isBayarSebagian ? `<p class="text-xs text-green-600">Dibayar: <span class="font-bold">${Utils.formatRupiah(sudahBayarFinal)}</span></p><p class="text-xs text-orange-600 font-bold">Sisa Piutang: ${Utils.formatRupiah(sisaFinal)}</p>` : ''}
                                        </div>
                                    </div>`,
                                    showCancelButton: true,
                                    confirmButtonText: 'OK',
                                    cancelButtonText: '<i class="fas fa-print mr-2"></i>Cetak Nota',
                                    confirmButtonColor: '#10b981',
                                    cancelButtonColor: '#3b82f6'
                                });
                                if (swalResult.dismiss === Swal.DismissReason.cancel) window.open(
                                    `/transaksi/struk/${data.data.id_penjualan}`, '_blank');
                            }
                            this.resetAfterTransaction();
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            throw new Error(data.message || 'Transaksi gagal');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        const msg = error.message || '';
                        const isKasirError = msg.toLowerCase().includes('kasir belum dibuka') || msg
                            .toLowerCase().includes('kasir') && msg.toLowerCase().includes('buka');
                        if (isKasirError) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Kasir Belum Dibuka!',
                                html: `<div class="text-center space-y-3"><p class="text-gray-600">Anda harus membuka sesi kasir terlebih dahulu sebelum melakukan transaksi.</p><a href="{{ route('kasir.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl transition-all"><i class="fas fa-cash-register"></i> Buka Kasir Sekarang</a></div>`,
                                showConfirmButton: false,
                                showCancelButton: true,
                                cancelButtonText: 'Tutup',
                                cancelButtonColor: '#6b7280'
                            });
                        } else {
                            Utils.showError('Gagal!', 'Terjadi kesalahan: ' + msg);
                        }
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
                    AppState.paymentRows.splice(0, AppState.paymentRows.length, {
                        method: 'tunai',
                        amount: 0
                    });
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
                            AppState.piutangPage = 1;
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
                    AppState.piutangPage = 1;
                    this.renderPiutangList();
                },
                renderPiutangList() {
                    const filtered = AppState.currentPiutangFilter === 'all' ?
                        AppState.piutangData :
                        AppState.piutangData.filter(p => p.status_pembayaran === AppState.currentPiutangFilter);

                    const totalItems = filtered.length;
                    const totalPages = Math.max(1, Math.ceil(totalItems / AppState.piutangPerPage));
                    if (AppState.piutangPage > totalPages) AppState.piutangPage = totalPages;
                    if (AppState.piutangPage < 1) AppState.piutangPage = 1;

                    const start = (AppState.piutangPage - 1) * AppState.piutangPerPage;
                    const end = Math.min(start + AppState.piutangPerPage, totalItems);
                    const paginated = filtered.slice(start, end);

                    if (totalItems === 0) {
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

                    const cardsHTML = paginated.map(p => {
                        const cfg = statusConfig[p.status_pembayaran] || statusConfig.belum_bayar;
                        const showBayarBtn = p.status_pembayaran === 'belum_bayar' || p
                            .status_pembayaran === 'bayar_sebagian';
                        const sisaTagihan = (p.sisa_tagihan !== undefined && p.sisa_tagihan !== null) ?
                            parseFloat(p.sisa_tagihan) : parseFloat(p.total_pembayaran);
                        const sudahDibayar = parseFloat(p.total_pembayaran) - sisaTagihan;
                        return `<div class="bg-gradient-to-r ${cfg.gradient} border-2 rounded-xl p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">${p.tanggal_penjualan}</p>
                                    <p class="font-bold text-lg text-gray-800">${p.id_penjualan}</p>
                                    ${p.kasir ? `<p class="text-xs text-gray-500">Kasir: ${p.kasir}</p>` : ''}
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-bold ${cfg.badge}">${cfg.label}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="space-y-0.5">
                                    <p class="text-xs text-gray-500">Total Tagihan</p>
                                    <p class="text-xl font-bold ${cfg.amountColor}">${Utils.formatRupiah(p.total_pembayaran)}</p>
                                    ${p.status_pembayaran === 'bayar_sebagian' ? `<p class="text-xs text-green-600 font-semibold">✓ Sudah dibayar: ${Utils.formatRupiah(sudahDibayar)}</p><p class="text-xs text-orange-600 font-bold">Sisa tagihan: ${Utils.formatRupiah(sisaTagihan)}</p>` : ''}
                                    ${p.status_pembayaran === 'belum_bayar' ? `<p class="text-xs text-red-500 font-semibold">Belum ada pembayaran</p>` : ''}
                                </div>
                                <div class="flex gap-2 flex-wrap justify-end">
                                    <button onclick="window.piutangShowDetail('${p.id_penjualan}')" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm transition"><i class="fas fa-eye mr-1"></i>Detail</button>
                                    ${showBayarBtn ? `<button onclick="window.piutangBayar('${p.id_penjualan}', ${p.total_pembayaran}, ${sisaTagihan})" class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm transition"><i class="fas fa-check mr-1"></i>Bayar</button>` : ''}
                                </div>
                            </div>
                        </div>`;
                    }).join('');

                    let paginationHTML = '';
                    if (totalPages > 1) {
                        const maxVisible = 5;
                        let pageStart = Math.max(1, AppState.piutangPage - Math.floor(maxVisible / 2));
                        let pageEnd = Math.min(totalPages, pageStart + maxVisible - 1);
                        if (pageEnd - pageStart < maxVisible - 1) pageStart = Math.max(1, pageEnd - maxVisible + 1);
                        const pageButtons = [];
                        for (let i = pageStart; i <= pageEnd; i++) {
                            pageButtons.push(
                                `<button onclick="window.piutangGoToPage(${i})" class="piutang-page-btn ${i === AppState.piutangPage ? 'active' : ''}">${i}</button>`
                                );
                        }
                        paginationHTML = `
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 mt-4 pt-4 border-t-2 border-gray-100">
                            <p class="text-sm text-gray-500 order-2 sm:order-1">Menampilkan <span class="font-bold text-gray-700">${start + 1}–${end}</span> dari <span class="font-bold text-gray-700">${totalItems}</span> data</p>
                            <div class="flex items-center gap-1 order-1 sm:order-2">
                                <button onclick="window.piutangGoToPage(1)" class="piutang-page-btn" ${AppState.piutangPage === 1 ? 'disabled' : ''}><i class="fas fa-angle-double-left text-xs"></i></button>
                                <button onclick="window.piutangGoToPage(${AppState.piutangPage - 1})" class="piutang-page-btn" ${AppState.piutangPage === 1 ? 'disabled' : ''}><i class="fas fa-chevron-left text-xs"></i></button>
                                ${pageStart > 1 ? '<span class="text-gray-400 px-1 text-sm">...</span>' : ''}
                                ${pageButtons.join('')}
                                ${pageEnd < totalPages ? '<span class="text-gray-400 px-1 text-sm">...</span>' : ''}
                                <button onclick="window.piutangGoToPage(${AppState.piutangPage + 1})" class="piutang-page-btn" ${AppState.piutangPage === totalPages ? 'disabled' : ''}><i class="fas fa-chevron-right text-xs"></i></button>
                                <button onclick="window.piutangGoToPage(${totalPages})" class="piutang-page-btn" ${AppState.piutangPage === totalPages ? 'disabled' : ''}><i class="fas fa-angle-double-right text-xs"></i></button>
                            </div>
                        </div>`;
                    }
                    DOM.piutangList.innerHTML = `<div class="space-y-3">${cardsHTML}</div>${paginationHTML}`;
                },
                async showDetailPiutang(idPenjualan) {
                    try {
                        const response = await fetch(`/transaksi/detail/${idPenjualan}`);
                        const result = await response.json();
                        if (result.success) {
                            const data = result.data;
                            const items = data.items.map(item =>
                                `<tr><td class="px-4 py-2 border-b">${item.nama_produk}</td><td class="px-4 py-2 border-b text-center">${item.qty}</td><td class="px-4 py-2 border-b text-right">${Utils.formatRupiah(item.harga)}</td><td class="px-4 py-2 border-b text-right font-bold">${Utils.formatRupiah(item.subtotal)}</td></tr>`
                            ).join('');
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
                            const sisaTagihan = (data.sisa_tagihan !== undefined && data.sisa_tagihan !==
                                null) ?
                                parseFloat(data.sisa_tagihan) : parseFloat(data.total_pembayaran);
                            const sudahDibayar = parseFloat(data.total_pembayaran) - sisaTagihan;
                            DOM.detailPiutangContent.innerHTML = `
                                <div class="space-y-4">
                                    <div class="bg-gray-50 rounded-xl p-4">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div><p class="text-sm text-gray-600">ID Transaksi</p><p class="font-bold text-lg">#${data.id_penjualan}</p></div>
                                            <div><p class="text-sm text-gray-600">Tanggal</p><p class="font-bold">${data.tanggal_penjualan}</p></div>
                                            <div><p class="text-sm text-gray-600">Status</p><span class="px-3 py-1 rounded-full text-xs font-bold ${statusConfig[data.status_pembayaran] || 'bg-red-600'} text-white">${statusLabel[data.status_pembayaran] || data.status_pembayaran}</span></div>
                                            <div><p class="text-sm text-gray-600">Total Tagihan</p><p class="font-bold text-lg text-emerald-600">${Utils.formatRupiah(data.total_pembayaran)}</p></div>
                                            ${data.status_pembayaran === 'bayar_sebagian' ? `<div><p class="text-sm text-gray-600">Sudah Dibayar</p><p class="font-bold text-green-600">${Utils.formatRupiah(sudahDibayar)}</p></div><div><p class="text-sm text-gray-600">Sisa Tagihan</p><p class="font-bold text-lg text-orange-600">${Utils.formatRupiah(sisaTagihan)}</p></div>` : ''}
                                            ${data.status_pembayaran === 'lunas' ? `<div class="col-span-2"><p class="text-sm text-gray-600">Kembalian</p><p class="font-bold text-blue-600">${Utils.formatRupiah(data.kembalian_pembayaran)}</p></div>` : ''}
                                        </div>
                                    </div>
                                    <div class="overflow-x-auto">
                                        <table class="w-full">
                                            <thead class="bg-gray-100"><tr><th class="px-4 py-2 text-left">Produk</th><th class="px-4 py-2 text-center">Qty</th><th class="px-4 py-2 text-right">Harga</th><th class="px-4 py-2 text-right">Subtotal</th></tr></thead>
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
                buildPiutangPaymentRowsHTML(sisaTagihan) {
                    const displayVal = sisaTagihan.toLocaleString('id-ID');
                    return `
                    <div class="space-y-2" id="piutangPayRows">
                        <div class="payment-row" id="piutangRow_0">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">Pembayaran 1</span>
                            </div>
                            <div class="grid grid-cols-2 gap-3 mb-3 px-4">
                                <button type="button" class="pay-method-btn active" onclick="piutangSelectMethod(0,'tunai')"><i class="fas fa-money-bill-wave block text-xl mb-1"></i><span class="text-xs font-semibold">Tunai</span></button>
                                <button type="button" class="pay-method-btn" onclick="piutangSelectMethod(0,'qris')"><i class="fas fa-qrcode block text-xl mb-1"></i><span class="text-xs font-semibold">QRIS</span></button>
                            </div>
                            <label class="block text-xs text-gray-500 mb-1 font-medium">Jumlah Bayar</label>
                            <input type="text" inputmode="numeric" id="piutangAmt_0" placeholder="Masukkan nominal"
                                value="${displayVal}"
                                class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm font-semibold focus:border-emerald-400 focus:outline-none"
                                oninput="formatRibuanInput(this); piutangRecalc(${sisaTagihan})">
                        </div>
                    </div>
                    <button type="button" id="piutangAddRowBtn" onclick="piutangAddRow(${sisaTagihan})"
                        class="w-full mt-2 flex items-center justify-center gap-2 px-4 py-2.5 border-2 border-dashed border-emerald-400 text-emerald-600 rounded-xl text-sm font-semibold hover:bg-emerald-50 transition">
                        <i class="fas fa-plus"></i> Tambah Metode Bayar
                    </button>
                    <div id="piutangKembalianDiv" class="hidden bg-blue-50 rounded-xl p-3 mt-2">
                        <div class="flex justify-between"><span class="text-gray-700 font-semibold text-sm">Kembalian Tunai:</span><span id="piutangKembalianVal" class="font-bold text-blue-600 text-sm">Rp 0</span></div>
                    </div>`;
                },
                async bayarPiutang(idPenjualan, totalPembayaran, sisaTagihan) {
                    window._piutangRows = [{
                        method: 'tunai',
                        amount: sisaTagihan
                    }];
                    window.piutangSelectMethod = function(idx, method) {
                        window._piutangRows[idx].method = method;
                        const row = document.getElementById(`piutangRow_${idx}`);
                        if (!row) return;
                        row.querySelectorAll('.pay-method-btn').forEach(b => {
                            const label = b.textContent.trim().toLowerCase();
                            b.className = 'pay-method-btn';
                            if ((method === 'qris' && label.includes('qris')) || (method ===
                                    'tunai' && label.includes('tunai'))) {
                                b.className = method === 'qris' ? 'pay-method-btn active-qris' :
                                    'pay-method-btn active';
                            }
                        });
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
                            `<div class="flex items-center justify-between mb-3"><span class="text-xs font-bold text-gray-500 uppercase tracking-wide">Pembayaran ${idx + 1}</span><button type="button" onclick="piutangRemoveRow(${idx}, ${sisa})" class="text-red-400 hover:text-red-600 text-xs"><i class="fas fa-times"></i></button></div>
                            <div class="grid grid-cols-2 gap-3 mb-3 px-4"><button type="button" class="pay-method-btn active" onclick="piutangSelectMethod(${idx},'tunai')"><i class="fas fa-money-bill-wave block text-xl mb-1"></i><span class="text-xs font-semibold">Tunai</span></button><button type="button" class="pay-method-btn" onclick="piutangSelectMethod(${idx},'qris')"><i class="fas fa-qrcode block text-xl mb-1"></i><span class="text-xs font-semibold">QRIS</span></button></div>
                            <label class="block text-xs text-gray-500 mb-1 font-medium">Jumlah Bayar</label>
                            <input type="text" inputmode="numeric" id="piutangAmt_${idx}" placeholder="Masukkan nominal" value="" class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm font-semibold focus:border-emerald-400 focus:outline-none" oninput="formatRibuanInput(this); piutangRecalc(${sisa})">`;
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
                        piutangRecalc(sisa);
                        const addBtn = document.getElementById('piutangAddRowBtn');
                        if (addBtn) addBtn.classList.remove('hidden');
                    };
                    window.piutangRecalc = function(sisa) {
                        window._piutangRows.forEach((r, i) => {
                            const inp = document.getElementById(`piutangAmt_${i}`);
                            if (inp) r.amount = parseRibuan(inp.value);
                        });
                        const tunaiAmt = window._piutangRows.filter(r => r.method === 'tunai').reduce((s,
                            r) => s + r.amount, 0);
                        const qrisAmt = window._piutangRows.filter(r => r.method === 'qris').reduce((s,
                            r) => s + r.amount, 0);
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
                        html: `<div class="text-left space-y-3">
                            <div class="bg-emerald-50 rounded-xl p-3">
                                <div class="flex justify-between"><span class="text-sm text-gray-600">Total Tagihan</span><span class="font-bold text-emerald-600">${Utils.formatRupiah(totalPembayaran)}</span></div>
                                <div class="flex justify-between mt-1"><span class="text-sm text-gray-600">Sisa Tagihan</span><span class="font-bold text-orange-600">${Utils.formatRupiah(sisaTagihan)}</span></div>
                            </div>
                            ${this.buildPiutangPaymentRowsHTML(sisaTagihan)}</div>`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Bayar Sekarang',
                        confirmButtonColor: '#10b981',
                        cancelButtonText: 'Batal',
                        didOpen: () => {
                            window.piutangRecalc(sisaTagihan);
                        },
                        preConfirm: () => {
                            window._piutangRows.forEach((r, i) => {
                                const inp = document.getElementById(`piutangAmt_${i}`);
                                if (inp) r.amount = parseRibuan(inp.value);
                            });
                            const totalPaid = window._piutangRows.reduce((s, r) => s + r.amount, 0);
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
                            if (apiResult.data?.auto_print && apiResult.data?.printer_name) {
                                if (typeof window.PrinterHelper !== 'undefined' && window.PrinterHelper
                                    .device) {
                                    try {
                                        const detailResponse = await fetch(`/transaksi/detail/${idPenjualan}`);
                                        const detailResult = await detailResponse.json();
                                        if (detailResult.success) {
                                            await window.PrinterHelper.printReceipt({
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
                                            });
                                            piutangPrintSuccess = true;
                                        }
                                    } catch (printError) {
                                        console.error('Auto print failed:', printError.message);
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
                            if (swalResult.dismiss === Swal.DismissReason.cancel) window.open(
                                `/transaksi/struk/${idPenjualan}`, '_blank');
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

            // ── Global bindings ──
            window.cartChangeQty = (id, delta) => CartManager.changeQty(id, delta);
            window.cartRemoveItem = (id) => CartManager.removeItem(id);
            window.cartSetQty = (id, inputEl) => CartManager.setQty(id, inputEl);
            window.piutangShowDetail = (id) => PiutangManager.showDetailPiutang(id);
            window.piutangBayar = (id, total, sisa) => PiutangManager.bayarPiutang(id, total, sisa ?? total);

            window.piutangGoToPage = function(page) {
                const filtered = AppState.currentPiutangFilter === 'all' ?
                    AppState.piutangData :
                    AppState.piutangData.filter(p => p.status_pembayaran === AppState.currentPiutangFilter);
                const totalPages = Math.max(1, Math.ceil(filtered.length / AppState.piutangPerPage));
                if (page < 1 || page > totalPages) return;
                AppState.piutangPage = page;
                PiutangManager.renderPiutangList();
                const listEl = document.getElementById('piutangList');
                if (listEl) listEl.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            };

            window.piutangChangePerPage = function(value) {
                AppState.piutangPerPage = parseInt(value) || 10;
                AppState.piutangPage = 1;
                PiutangManager.renderPiutangList();
            };

            function initApp() {
                PaymentManager.init();
                SearchManager.init();
                ProductDetailManager.init();
                CartManager.init();
                TransactionManager.init();
                PiutangManager.init();
                console.log('Transaksi Kasir App Initialized');
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initApp);
            } else {
                initApp();
            }
        })();
    </script>
@endpush
