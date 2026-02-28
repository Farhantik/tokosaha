<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - WPOS</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        /* FIX: cegah horizontal scroll di seluruh halaman */
        html,
        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            max-width: 100%;
        }

        body {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        /* ═══════════════════════════════════════
           SIDEBAR
        ═══════════════════════════════════════ */
        .sidebar-gradient {
            background: linear-gradient(180deg, #064e3b 0%, #065f46 50%, #047857 100%);
        }

        #sidebar {
            width: 256px;
            transition: width 0.3s ease;
            /* FIX: hapus overflow:hidden di sini agar toggle-btn tidak terpotong */
            overflow: visible;
        }

        /* Collapse state */
        #sidebar.collapsed {
            width: 72px;
        }

        #sidebar.collapsed .sidebar-text,
        #sidebar.collapsed .logo-text,
        #sidebar.collapsed .user-info-text,
        #sidebar.collapsed .section-label {
            opacity: 0;
            width: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .sidebar-text {
            transition: opacity 0.25s ease, width 0.25s ease;
            white-space: nowrap;
            overflow: hidden;
        }

        /* FIX: konten dalam sidebar harus overflow-hidden agar tidak meluber
           tapi wrapper-nya bisa overflow-visible untuk toggle btn */
        .sidebar-inner {
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
            width: 100%;
        }

        /* ── Nav Links ── */
        .nav-link {
            display: flex;
            align-items: center;
            padding: 11px 16px;
            color: #d1fae5;
            transition: all 0.25s ease;
            font-size: 13.5px;
            font-weight: 500;
            border-radius: 10px;
            margin: 3px 10px;
            gap: 12px;
            text-decoration: none;
        }

        #sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 11px 0;
            margin: 3px 8px;
            gap: 0;
        }

        .nav-link:hover {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.35) 0%, rgba(5, 150, 105, 0.35) 100%);
            color: white;
        }

        /* FIX: hapus translateX agar tidak ada geser saat collapsed */
        @media (min-width: 1024px) {
            .nav-link:hover {
                transform: translateX(4px);
            }

            #sidebar.collapsed .nav-link:hover {
                transform: none;
            }
        }

        .nav-link.active {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
        }

        .nav-link i {
            width: 18px;
            font-size: 15px;
            text-align: center;
            flex-shrink: 0;
        }

        /* ── Toggle Button ──
           FIX: posisi absolute pada wrapper .sidebar-position-wrapper
           agar tombol tidak terpotong sidebar
        ── */
        .sidebar-position-wrapper {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            z-index: 40;
            /* FIX: overflow visible supaya toggle-btn terlihat keluar */
            overflow: visible;
        }

        .sidebar-toggle-btn {
            position: absolute;
            /* FIX: right:-14px sehingga setengah tombol keluar dari sisi kanan sidebar */
            right: -14px;
            top: 24px;
            width: 28px;
            height: 28px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.5);
            transition: all 0.3s ease;
            z-index: 50;
        }

        .sidebar-toggle-btn:hover {
            transform: scale(1.15);
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.7);
        }

        .sidebar-toggle-btn i {
            color: white;
            font-size: 11px;
            transition: transform 0.3s ease;
        }

        #sidebar.collapsed .sidebar-toggle-btn i {
            transform: rotate(180deg);
        }

        /* ── Nav Scroll Area ── */
        .nav-scroll-area {
            flex: 1 1 0%;
            overflow-y: auto;
            overflow-x: hidden;
            padding-bottom: 1rem;
        }

        .nav-scroll-area::-webkit-scrollbar {
            width: 3px;
        }

        .nav-scroll-area::-webkit-scrollbar-track {
            background: transparent;
        }

        .nav-scroll-area::-webkit-scrollbar-thumb {
            background: rgba(16, 185, 129, 0.25);
            border-radius: 4px;
        }

        /* ═══════════════════════════════════════
           MAIN CONTENT
        ═══════════════════════════════════════ */
        .main-content {
            margin-left: 256px;
            transition: margin-left 0.3s ease;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            /* FIX: paksa lebar tidak melebihi sisa viewport */
            width: calc(100% - 256px);
            max-width: calc(100% - 256px);
            overflow-x: hidden;
        }

        .main-content.expanded {
            margin-left: 72px;
            width: calc(100% - 72px);
            max-width: calc(100% - 72px);
        }

        /* ═══════════════════════════════════════
           GLASS / MISC
        ═══════════════════════════════════════ */
        .glass-effect {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(16, 185, 129, 0.15);
        }

        .content-area {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 40%, #e0f2fe 100%);
            flex: 1;
        }

        /* ═══════════════════════════════════════
           ALERTS
        ═══════════════════════════════════════ */
        .alert-slide-in {
            animation: slideIn 0.4s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(60px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* ═══════════════════════════════════════
           DROPDOWN
        ═══════════════════════════════════════ */
        #navUserDropdown {
            animation: dropdownSlide 0.2s ease-out;
        }

        @keyframes dropdownSlide {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ═══════════════════════════════════════
           USER AVATAR
        ═══════════════════════════════════════ */
        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            object-fit: cover;
            border: 2px solid rgba(16, 185, 129, 0.4);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        }

        .user-avatar-sidebar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(16, 185, 129, 0.35);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
        }

        .logo-glow {
            box-shadow: 0 0 16px rgba(16, 185, 129, 0.45);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: .25rem .75rem;
            border-radius: 9999px;
            font-size: .75rem;
            font-weight: 600;
        }

        .badge-owner {
            background: rgba(16, 185, 129, .2);
            color: #6ee7b7;
        }

        .badge-kasir {
            background: rgba(52, 211, 153, .2);
            color: #6ee7b7;
        }

        .owner-section-label {
            color: #6ee7b7;
        }

        /* ═══════════════════════════════════════
           STOCK BADGES
        ═══════════════════════════════════════ */
        .stock-critical {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #991b1b;
            border: 2px solid #ef4444;
            animation: pulse-red 2s infinite;
        }

        .stock-warning {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #92400e;
            border: 2px solid #f59e0b;
        }

        .stock-low {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            color: #1e40af;
            border: 2px solid #3b82f6;
        }

        .stock-safe {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: #065f46;
            border: 2px solid #10b981;
        }

        @keyframes pulse-red {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, .7);
            }

            50% {
                box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
            }
        }

        .stock-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 13px;
            letter-spacing: .3px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, .1);
            transition: all .3s ease;
        }

        .stock-badge:hover {
            transform: scale(1.05);
        }

        .stock-badge i {
            font-size: 14px;
        }

        /* ═══════════════════════════════════════
           TABLE MODERN
        ═══════════════════════════════════════ */
        .table-modern {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, .08);
        }

        .table-modern thead {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .table-modern thead th {
            padding: 15px 16px;
            color: white;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .table-modern tbody tr {
            border-bottom: 1px solid #f3f4f6;
            transition: all .2s ease;
        }

        .table-modern tbody tr:hover {
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
        }

        .table-modern tbody td {
            padding: 14px 16px;
            font-size: 13.5px;
            color: #374151;
        }

        /* ═══════════════════════════════════════
           BUTTONS
        ═══════════════════════════════════════ */
        .btn-detail {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 16px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            transition: all .25s ease;
            box-shadow: 0 2px 4px rgba(59, 130, 246, .3);
            text-decoration: none;
        }

        .btn-detail:hover {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(59, 130, 246, .45);
        }

        .btn-detail i {
            font-size: 13px;
        }

        /* ═══════════════════════════════════════
           TOOLTIP
        ═══════════════════════════════════════ */
        .tooltip {
            position: relative;
            display: inline-block;
        }

        .tooltip .tooltiptext {
            visibility: hidden;
            width: 200px;
            background: #1f2937;
            color: #fff;
            text-align: center;
            border-radius: 8px;
            padding: 8px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            margin-left: -100px;
            opacity: 0;
            transition: opacity .3s;
            font-size: 12px;
        }

        .tooltip .tooltiptext::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #1f2937 transparent transparent transparent;
        }

        .tooltip:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }

        /* ═══════════════════════════════════════
           FOOTER
        ═══════════════════════════════════════ */
        .footer-area {
            background: rgba(255, 255, 255, .88);
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(16, 185, 129, .2);
        }

        .footer-area .footer-brand {
            color: #1f2937;
            font-weight: 700;
        }

        .footer-area .footer-sub {
            color: #4b5563;
        }

        .footer-area .footer-copyright {
            color: #374151;
        }

        .footer-area .footer-meta {
            color: #374151;
        }

        .footer-area .footer-meta i {
            color: #059669;
        }

        .footer-area .footer-divider {
            color: #9ca3af;
        }

        /* ═══════════════════════════════════════
           SCROLLBAR GLOBAL
        ═══════════════════════════════════════ */
        ::-webkit-scrollbar {
            width: 7px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, .1);
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 4px;
        }

        /* ═══════════════════════════════════════
           MOBILE
        ═══════════════════════════════════════ */
        @media (max-width:1023px) {
            .sidebar-position-wrapper {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar-position-wrapper.open {
                transform: translateX(0);
            }

            .main-content,
            .main-content.expanded {
                margin-left: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }

            .sidebar-toggle-btn {
                display: none !important;
            }
        }

        @media (max-height:700px) {
            .user-avatar-sidebar {
                width: 34px;
                height: 34px;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <div class="flex min-h-screen">

        <!-- ════════════════════════════════════════════
             SIDEBAR WRAPPER — overflow:visible untuk toggle-btn
        ════════════════════════════════════════════ -->
        <div class="sidebar-position-wrapper" id="sidebarWrapper">

            <!-- Toggle Button (desktop only) -->
            <div class="sidebar-toggle-btn hidden lg:flex" onclick="toggleSidebarCollapse()" title="Toggle Sidebar">
                <i class="fas fa-chevron-left"></i>
            </div>

            <aside id="sidebar" class="sidebar-gradient h-screen shadow-2xl">
                <!-- FIX: inner wrapper menampung konten agar overflow-hidden tidak memotong toggle-btn di luar -->
                <div class="sidebar-inner">

                    <!-- Logo & Brand -->
                    <div class="p-4 flex-shrink-0 border-b border-emerald-700/40">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                            <div
                                class="bg-gradient-to-br from-emerald-400 to-green-600 p-2.5 rounded-xl flex-shrink-0 logo-glow">
                                <i class="fas fa-store text-white text-sm"></i>
                            </div>
                            <div class="logo-text sidebar-text">
                                <div class="text-white text-sm font-bold leading-tight">
                                    @if (isset($tokoSettings))
                                        {{ $tokoSettings->nama_toko ?? 'WPOS' }}
                                    @else
                                        WPOS
                                    @endif
                                </div>
                                <div class="text-emerald-300 text-xs">POS System</div>
                            </div>
                        </a>
                    </div>

                    <!-- Navigation -->
                    <nav class="nav-scroll-area py-2">

                        @if (Auth::user()->isOwner())
                            <a href="{{ route('dashboard') }}"
                                class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                                title="Dashboard">
                                <i class="fas fa-home"></i>
                                <span class="sidebar-text">Dashboard</span>
                            </a>

                            <a href="{{ route('kasir.index') }}"
                                class="nav-link {{ request()->routeIs('kasir.*') ? 'active' : '' }}" title="Kasir">
                                <i class="fas fa-cash-register"></i>
                                <span class="sidebar-text">Buka/Tutup Kasir</span>
                            </a>
                        @endif

                        <a href="{{ route('transaksi.index') }}"
                            class="nav-link {{ request()->routeIs('transaksi.*') ? 'active' : '' }}" title="Transaksi">
                            <i class="fas fa-receipt"></i>
                            <span class="sidebar-text">Transaksi Kasir</span>
                        </a>

                        <a href="{{ route('settings.index') }}"
                            class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" title="Pengaturan">
                            <i class="fas fa-cog"></i>
                            <span class="sidebar-text">Pengaturan Sistem</span>
                        </a>

                        @if (Auth::user()->isOwner())
                            <!-- Section: Owner Only -->
                            <div class="px-4 mt-5 mb-2 section-label">
                                <div
                                    class="text-xs font-semibold owner-section-label uppercase tracking-wider flex items-center gap-2">
                                    <i class="fas fa-crown text-yellow-400 flex-shrink-0"></i>
                                    <span class="sidebar-text">Owner Only</span>
                                </div>
                            </div>

                            <a href="{{ route('produk.index') }}"
                                class="nav-link {{ request()->routeIs('produk.*') ? 'active' : '' }}" title="Produk">
                                <i class="fas fa-box"></i>
                                <span class="sidebar-text">Manajemen Produk</span>
                            </a>

                            <a href="{{ route('suppliers.index') }}"
                                class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}"
                                title="Supplier">
                                <i class="fas fa-truck"></i>
                                <span class="sidebar-text">Kelola Supplier</span>
                            </a>

                            <a href="{{ route('penerimaan.index') }}"
                                class="nav-link {{ request()->routeIs('penerimaan.*') ? 'active' : '' }}"
                                title="Penerimaan Barang">
                                <i class="fas fa-box-open"></i>
                                <span class="sidebar-text">Penerimaan Barang</span>
                            </a>

                            <a href="{{ route('users.index') }}"
                                class="nav-link {{ request()->routeIs('users.*') && !request()->routeIs('users.profile') ? 'active' : '' }}"
                                title="Kelola User">
                                <i class="fas fa-users"></i>
                                <span class="sidebar-text">Kelola User</span>
                            </a>

                            <!-- Section: Laporan -->
                            <div class="px-4 mt-5 mb-2 section-label">
                                <div
                                    class="text-xs font-semibold text-emerald-400 uppercase tracking-wider flex items-center gap-2">
                                    <i class="fas fa-file-invoice-dollar flex-shrink-0"></i>
                                    <span class="sidebar-text">Laporan</span>
                                </div>
                            </div>

                            <a href="{{ route('keuangan.index') }}"
                                class="nav-link {{ request()->routeIs('keuangan.*') ? 'active' : '' }}"
                                title="Laporan Keuangan">
                                <i class="fas fa-file-invoice-dollar"></i>
                                <span class="sidebar-text">Laporan Keuangan</span>
                            </a>

                            <a href="{{ route('laporan.index') }}"
                                class="nav-link {{ request()->routeIs('laporan.*') && !request()->routeIs('keuangan.*') ? 'active' : '' }}"
                                title="Laporan Penjualan">
                                <i class="fas fa-chart-line"></i>
                                <span class="sidebar-text">Laporan Penjualan</span>
                            </a>
                        @endif

                    </nav>
                    <!-- End nav -->

                </div>
                <!-- End sidebar-inner -->
            </aside>
        </div>
        <!-- End sidebarWrapper -->

        <!-- Overlay Mobile -->
        <div id="sidebarOverlay" class="fixed inset-0 bg-black/60 z-30 hidden lg:hidden backdrop-blur-sm"></div>

        <!-- ════════════════════════════════════════════
             MAIN CONTENT
        ════════════════════════════════════════════ -->
        <div id="mainContent" class="main-content flex-1">

            <!-- Top Navbar -->
            <header class="glass-effect sticky top-0 z-20 shadow-sm">
                <div class="px-4 md:px-6 py-3">
                    <div class="flex items-center justify-between">
                        <!-- Left: hamburger + page title -->
                        <div class="flex items-center gap-3">
                            <button id="sidebarToggle"
                                class="lg:hidden text-gray-600 hover:text-green-600 transition p-1.5 rounded-lg hover:bg-green-50">
                                <i class="fas fa-bars text-lg"></i>
                            </button>
                            <h1
                                class="text-lg md:text-xl font-bold bg-gradient-to-r from-emerald-600 to-green-600 bg-clip-text text-transparent">
                                @yield('page-title', 'Dashboard')
                            </h1>
                        </div>

                        <!-- Right: User dropdown -->
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <button onclick="toggleNavUserDropdown()"
                                    class="group focus:outline-none flex items-center gap-2 pl-2 pr-3 py-1.5 rounded-xl hover:bg-gray-100 transition">
                                    @if (Auth::user()->gambar_user)
                                        <img src="{{ asset('storage/users/' . Auth::user()->gambar_user) }}"
                                            alt="{{ Auth::user()->name }}"
                                            class="user-avatar transition-transform group-hover:scale-105">
                                    @else
                                        <div
                                            class="user-avatar bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center text-white font-bold text-sm transition-transform group-hover:scale-105">
                                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                    <!-- FIX: Tampilkan nama user di navbar (hidden di mobile) -->
                                    <span
                                        class="hidden md:block text-sm font-medium text-gray-700">{{ Auth::user()->name }}</span>
                                    <i
                                        class="fas fa-chevron-down text-gray-400 text-xs transition-transform group-hover:rotate-180"></i>
                                </button>

                                <!-- Dropdown -->
                                <div id="navUserDropdown"
                                    class="hidden absolute right-0 top-full mt-2 w-56 bg-white rounded-xl shadow-2xl overflow-hidden border border-gray-100 z-50">
                                    <div
                                        class="px-4 py-3 bg-gradient-to-r from-emerald-50 to-green-50 border-b border-gray-100">
                                        <div class="flex items-center gap-3">
                                            @if (Auth::user()->gambar_user)
                                                <img src="{{ asset('storage/users/' . Auth::user()->gambar_user) }}"
                                                    alt="{{ Auth::user()->name }}"
                                                    class="w-9 h-9 rounded-full object-cover border-2 border-emerald-300">
                                            @else
                                                <div
                                                    class="w-9 h-9 rounded-full bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center text-white font-bold text-sm border-2 border-emerald-300">
                                                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                                </div>
                                            @endif
                                            <div class="flex-1 min-w-0">
                                                <div class="text-sm font-semibold text-gray-900 truncate">
                                                    {{ Auth::user()->name }}</div>
                                                <div class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}
                                                </div>
                                                <div class="mt-1">
                                                    @if (Auth::user()->isOwner())
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                                            <i
                                                                class="fas fa-crown mr-1 text-yellow-500 text-xs"></i>Owner
                                                        </span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                                            <i class="fas fa-user mr-1 text-xs"></i>Kasir
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="py-1.5">
                                        <a href="{{ route('users.profile') }}"
                                            class="flex items-center px-4 py-2.5 text-gray-700 hover:bg-emerald-50 transition text-sm group">
                                            <i
                                                class="fas fa-user-circle mr-3 text-emerald-500 group-hover:text-emerald-700 w-4"></i>
                                            <span>Lihat Profil</span>
                                        </a>

                                        <div class="border-t border-gray-100 my-1"></div>

                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="w-full flex items-center px-4 py-2.5 text-red-600 hover:bg-red-50 transition text-sm group">
                                                <i class="fas fa-sign-out-alt mr-3 group-hover:text-red-700 w-4"></i>
                                                <span>Logout</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 p-4 md:p-6 content-area">

                <!-- Alert: Success -->
                @if (session('success'))
                    <div class="bg-gradient-to-r from-green-400 to-emerald-500 text-white p-4 mb-5 rounded-xl alert-slide-in shadow-lg"
                        role="alert">
                        <div class="flex items-start gap-3">
                            <div class="bg-white/20 p-1.5 rounded-lg flex-shrink-0">
                                <i class="fas fa-check-circle text-base"></i>
                            </div>
                            <div>
                                <p class="font-bold text-sm">Berhasil!</p>
                                <p class="text-sm opacity-90">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Alert: Error -->
                @if (session('error'))
                    <div class="bg-gradient-to-r from-red-500 to-pink-500 text-white p-4 mb-5 rounded-xl alert-slide-in shadow-lg"
                        role="alert">
                        <div class="flex items-start gap-3">
                            <div class="bg-white/20 p-1.5 rounded-lg flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-base"></i>
                            </div>
                            <div>
                                <p class="font-bold text-sm">Error!</p>
                                <p class="text-sm opacity-90">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Alert: Validation Errors -->
                @if ($errors->any())
                    <div class="bg-gradient-to-r from-orange-400 to-red-500 text-white p-4 mb-5 rounded-xl alert-slide-in shadow-lg"
                        role="alert">
                        <div class="flex items-start gap-3">
                            <div class="bg-white/20 p-1.5 rounded-lg flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-base"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-bold mb-1.5 text-sm">Terdapat kesalahan:</p>
                                <ul class="space-y-0.5 text-sm opacity-90">
                                    @foreach ($errors->all() as $error)
                                        <li class="flex items-start gap-1.5">
                                            <span class="mt-0.5 flex-shrink-0">•</span>
                                            <span>{{ $error }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="footer-area px-4 md:px-6 py-3.5">
                <div class="flex flex-col md:flex-row items-center justify-between gap-2">

                    <!-- Brand -->
                    <div class="flex items-center gap-2">
                        <div class="bg-gradient-to-br from-emerald-500 to-green-600 p-1.5 rounded-lg shadow-sm">
                            <i class="fas fa-store text-white text-xs"></i>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="footer-brand text-sm">
                                @if (isset($tokoSettings))
                                    {{ $tokoSettings->nama_toko ?? 'WPOS' }}
                                @else
                                    WPOS
                                @endif
                            </span>
                            <span class="footer-sub text-xs font-medium">— POS System</span>
                        </div>
                    </div>

                    <!-- Copyright -->
                    <div class="footer-copyright text-xs text-center font-medium">
                        &copy; {{ date('Y') }}
                        <span class="font-bold" style="color:#065f46;">
                            @if (isset($tokoSettings))
                                {{ $tokoSettings->nama_toko ?? 'WPOS' }}
                            @else
                                WPOS
                            @endif
                        </span>
                        &mdash; All rights reserved.
                    </div>

                    <!-- Meta -->
                    <div class="footer-meta flex items-center gap-3 text-xs font-medium">
                        <div class="flex items-center gap-1.5">
                            <i class="fas fa-code" style="color:#059669;"></i>
                            <span>v1.0.0</span>
                        </div>
                        <span class="footer-divider">|</span>
                        <div class="flex items-center gap-1.5">
                            <i class="fas fa-shield-alt" style="color:#059669;"></i>
                            <span>Secure</span>
                        </div>
                        <span class="footer-divider">|</span>
                        <div class="flex items-center gap-1.5">
                            <i class="fas fa-clock" style="color:#059669;"></i>
                            <span id="footerTime"></span>
                        </div>
                    </div>

                </div>
            </footer>
        </div>
        <!-- End mainContent -->

    </div>
    <!-- End flex wrapper -->

    <script>
        // ── Elemen DOM ──────────────────────────────────────────
        const sidebar = document.getElementById('sidebar');
        const sidebarWrapper = document.getElementById('sidebarWrapper');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const mainContent = document.getElementById('mainContent');

        // ── Mobile: buka/tutup sidebar ──────────────────────────
        function toggleSidebar() {
            sidebarWrapper.classList.toggle('open');
            sidebarOverlay.classList.toggle('hidden');
        }

        if (sidebarToggle) sidebarToggle.addEventListener('click', toggleSidebar);
        if (sidebarOverlay) sidebarOverlay.addEventListener('click', toggleSidebar);

        // ── Desktop: collapse/expand sidebar ───────────────────
        function toggleSidebarCollapse() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            // FIX: update width secara eksplisit agar tidak ada gap/overflow
            const isCollapsed = sidebar.classList.contains('collapsed');
            mainContent.style.width = isCollapsed ? 'calc(100% - 72px)' : 'calc(100% - 256px)';
            mainContent.style.maxWidth = mainContent.style.width;
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        }

        // ── Navbar dropdown ─────────────────────────────────────
        function toggleNavUserDropdown() {
            document.getElementById('navUserDropdown').classList.toggle('hidden');
        }

        // Tutup dropdown saat klik luar
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('navUserDropdown');
            const btn = e.target.closest('button[onclick="toggleNavUserDropdown()"]');
            if (!btn && dropdown && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });

        // Escape key menutup dropdown
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const d = document.getElementById('navUserDropdown');
                if (d) d.classList.add('hidden');
            }
        });

        // ── Restore collapse state ──────────────────────────────
        document.addEventListener('DOMContentLoaded', function() {
            if (localStorage.getItem('sidebarCollapsed') === 'true' && window.innerWidth >= 1024) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
                mainContent.style.width = 'calc(100% - 72px)';
                mainContent.style.maxWidth = 'calc(100% - 72px)';
            }
        });

        // ── Resize: reset mobile state ──────────────────────────
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                sidebarWrapper.classList.remove('open');
                sidebarOverlay.classList.add('hidden');
            }
        });

        // ── Auto-dismiss alerts setelah 5 detik ─────────────────
        setTimeout(function() {
            document.querySelectorAll('[role="alert"]').forEach(function(el) {
                el.style.transition = 'all 0.5s ease-out';
                el.style.opacity = '0';
                el.style.transform = 'translateX(40px)';
                setTimeout(() => el.remove(), 500);
            });
        }, 5000);

        // ── Smooth scroll ───────────────────────────────────────
        document.documentElement.style.scrollBehavior = 'smooth';

        // ── Stock helper functions ──────────────────────────────
        function getStockBadgeClass(stock, minStock = 10) {
            if (stock <= 0) return 'stock-critical';
            if (stock <= 5) return 'stock-critical';
            if (stock <= minStock) return 'stock-warning';
            if (stock <= minStock * 2) return 'stock-low';
            return 'stock-safe';
        }

        function getStockIcon(stock, minStock = 10) {
            if (stock <= 0) return 'fa-times-circle';
            if (stock <= 5) return 'fa-exclamation-triangle';
            if (stock <= minStock) return 'fa-exclamation-circle';
            if (stock <= minStock * 2) return 'fa-info-circle';
            return 'fa-check-circle';
        }

        function getStockText(stock) {
            if (stock <= 0) return 'Habis';
            if (stock <= 5) return 'Sangat Menipis!';
            if (stock <= 10) return 'Menipis';
            if (stock <= 20) return 'Perlu Restock';
            return 'Aman';
        }

        // ── Footer clock ────────────────────────────────────────
        function updateFooterTime() {
            const el = document.getElementById('footerTime');
            if (el) {
                el.textContent = new Date().toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
            }
        }
        updateFooterTime();
        setInterval(updateFooterTime, 1000);
    </script>

    <script src="{{ asset('js/printer-helper.js') }}"></script>

    @stack('scripts')
</body>

</html>
