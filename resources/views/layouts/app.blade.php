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

        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .sidebar-gradient {
            background: linear-gradient(180deg, #064e3b 0%, #065f46 50%, #047857 100%);
        }

        /* Sidebar Collapsed State */
        #sidebar {
            transition: width 0.3s ease, transform 0.3s ease;
        }

        #sidebar.collapsed {
            width: 80px;
        }

        #sidebar.collapsed .sidebar-text {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        #sidebar.collapsed .logo-text {
            display: none;
        }

        #sidebar.collapsed .user-info-text {
            display: none;
        }

        #sidebar.collapsed .section-label {
            display: none;
        }

        .sidebar-text {
            transition: opacity 0.3s ease, width 0.3s ease;
            white-space: nowrap;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #d1fae5;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 500;
            border-radius: 12px;
            margin: 4px 12px;
        }

        #sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 12px;
        }

        .nav-link:hover {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
        }

        #sidebar.collapsed .nav-link:hover {
            transform: translateX(0);
        }

        .nav-link.active {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
        }

        .nav-link i {
            width: 20px;
            margin-right: 12px;
            font-size: 16px;
            text-align: center;
            flex-shrink: 0;
        }

        #sidebar.collapsed .nav-link i {
            margin-right: 0;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .alert-slide-in {
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        }

        #sidebar::-webkit-scrollbar {
            width: 4px;
        }

        #sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        #sidebar::-webkit-scrollbar-thumb {
            background: rgba(16, 185, 129, 0.3);
        }

        .logo-glow {
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.5);
        }

        .content-area {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-owner {
            background: rgba(16, 185, 129, 0.2);
            color: #6ee7b7;
        }

        .badge-kasir {
            background: rgba(52, 211, 153, 0.2);
            color: #6ee7b7;
        }

        /* User Avatar Styles */
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 0.75rem;
            object-fit: cover;
            border: 2px solid rgba(16, 185, 129, 0.3);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .user-avatar-sidebar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(16, 185, 129, 0.3);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        .owner-section-label {
            color: #6ee7b7;
        }

        .border-green-700 {
            border-color: #15803d;
        }

        /* Navigation area scrollable */
        .nav-scroll-area {
            max-height: calc(100vh - 280px);
            overflow-y: auto;
        }

        /* Toggle Button */
        .sidebar-toggle-btn {
            position: absolute;
            right: -12px;
            top: 20px;
            width: 24px;
            height: 24px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.4);
            transition: all 0.3s ease;
            z-index: 50;
        }

        .sidebar-toggle-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.6);
        }

        .sidebar-toggle-btn i {
            color: white;
            font-size: 12px;
            transition: transform 0.3s ease;
        }

        #sidebar.collapsed .sidebar-toggle-btn i {
            transform: rotate(180deg);
        }

        /* Navbar Dropdown animation */
        #navUserDropdown {
            animation: dropdownSlide 0.2s ease-out;
        }

        @keyframes dropdownSlide {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
<<<<<<< HEAD

=======
>>>>>>> 930047ac1763748d4e7621981cc5da996819a2ec
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Main content adjustment */
        .main-content {
            margin-left: 256px;
            transition: margin-left 0.3s ease;
        }

        .main-content.expanded {
            margin-left: 80px;
        }

        /* Compact user info for mobile */
        @media (max-height: 700px) {
            .user-info-compact .user-name {
                font-size: 0.8rem;
            }

            .user-info-compact .user-email {
                display: none;
            }

            .user-avatar-sidebar {
                width: 36px;
                height: 36px;
            }
        }

        /* Mobile responsive */
        @media (max-width: 1023px) {
            #sidebar {
                width: 256px;
            }

            #sidebar.collapsed {
                width: 256px;
            }

            .main-content {
                margin-left: 0;
            }

            .main-content.expanded {
                margin-left: 0;
            }

            .sidebar-toggle-btn {
                display: none;
            }
        }

        /* Stock Warning Styles */
        .stock-critical {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border: 2px solid #ef4444;
            animation: pulse-red 2s infinite;
        }

        .stock-warning {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            border: 2px solid #f59e0b;
        }

        .stock-low {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
            border: 2px solid #3b82f6;
        }

        .stock-safe {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border: 2px solid #10b981;
        }

        @keyframes pulse-red {
<<<<<<< HEAD

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            }

=======
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            }
>>>>>>> 930047ac1763748d4e7621981cc5da996819a2ec
            50% {
                box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
            }
        }

        /* Table Styles */
        .table-modern {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .table-modern thead {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .table-modern thead th {
            padding: 16px;
            color: white;
            font-weight: 600;
            text-align: left;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-modern tbody tr {
            border-bottom: 1px solid #f3f4f6;
            transition: all 0.3s ease;
        }

        .table-modern tbody tr:hover {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            transform: scale(1.01);
        }

        .table-modern tbody td {
            padding: 16px;
            font-size: 14px;
            color: #374151;
        }

        /* Action Button Styles */
        .btn-detail {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
        }

        .btn-detail:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(59, 130, 246, 0.5);
        }

        .btn-detail i {
            font-size: 14px;
        }

        /* Stock Badge Enhanced */
        .stock-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 13px;
            letter-spacing: 0.3px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .stock-badge:hover {
            transform: scale(1.05);
        }

        .stock-badge i {
            font-size: 14px;
        }

        /* Tooltip */
        .tooltip {
            position: relative;
            display: inline-block;
        }

        .tooltip .tooltiptext {
            visibility: hidden;
            width: 200px;
            background-color: #1f2937;
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
            transition: opacity 0.3s;
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
    </style>

    @stack('styles')
</head>

<body>
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside id="sidebar"
            class="w-64 sidebar-gradient fixed lg:fixed left-0 top-0 h-screen z-40 transform -translate-x-full lg:translate-x-0 transition-all duration-300 flex flex-col shadow-2xl">

            <!-- Toggle Button (Desktop Only) -->
            <div class="sidebar-toggle-btn hidden lg:flex" onclick="toggleSidebarCollapse()">
                <i class="fas fa-chevron-left"></i>
            </div>

            <!-- Logo & Brand -->
            <div class="p-4 flex-shrink-0">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                    <div
                        class="bg-gradient-to-br from-emerald-500 to-green-600 p-2.5 rounded-xl flex-shrink-0 logo-glow">
                        <i class="fas fa-store text-white text-base"></i>
                    </div>
                    <div class="logo-text">
<<<<<<< HEAD
                        <div class="text-white text-base font-bold leading-tight sidebar-text">WPOS</div>
=======
                        <div class="text-white text-base font-bold leading-tight sidebar-text">Toko Sahabat</div>
>>>>>>> 930047ac1763748d4e7621981cc5da996819a2ec
                        <div class="text-emerald-300 text-xs font-medium sidebar-text">POS System</div>
                    </div>
                </a>
            </div>

            <!-- Navigation - Scrollable -->
            <nav class="flex-1 overflow-y-auto py-2 nav-scroll-area">
<<<<<<< HEAD

                @if (Auth::user()->isOwner())
                    <!-- Dashboard - Owner Only -->
                    <a href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" title="Dashboard">
                        <i class="fas fa-home"></i>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                @endif
=======
                <!-- Main Menu -->
                <a href="{{ route('dashboard') }}"
                    class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                    title="Dashboard">
                    <i class="fas fa-home"></i>
                    <span class="sidebar-text">Dashboard</span>
                </a>

                <a href="{{ route('kasir.index') }}"
                    class="nav-link {{ request()->routeIs('kasir.*') ? 'active' : '' }}"
                    title="Kasir">
                    <i class="fas fa-cash-register"></i>
                    <span class="sidebar-text">Kasir</span>
                </a>
>>>>>>> 930047ac1763748d4e7621981cc5da996819a2ec

                <!-- Kasir - Semua Role (tapi tampil di nav hanya Owner) -->
                @if (Auth::user()->isOwner())
                    <a href="{{ route('kasir.index') }}"
                        class="nav-link {{ request()->routeIs('kasir.*') ? 'active' : '' }}" title="Kasir">
                        <i class="fas fa-cash-register"></i>
                        <span class="sidebar-text">Kasir</span>
                    </a>
                @endif

                <!-- Transaksi - Semua Role -->
                <a href="{{ route('transaksi.index') }}"
<<<<<<< HEAD
                    class="nav-link {{ request()->routeIs('transaksi.*') ? 'active' : '' }}" title="Transaksi">
=======
                    class="nav-link {{ request()->routeIs('transaksi.*') ? 'active' : '' }}"
                    title="Transaksi">
>>>>>>> 930047ac1763748d4e7621981cc5da996819a2ec
                    <i class="fas fa-receipt"></i>
                    <span class="sidebar-text">Transaksi</span>
                </a>

<<<<<<< HEAD


                <!-- Profile Menu - Sembunyikan dari Kasir -->
                @if (Auth::user()->isOwner())
                    <a href="{{ route('users.profile') }}"
                        class="nav-link {{ request()->routeIs('users.profile') ? 'active' : '' }}" title="Profil Saya">
                        <i class="fas fa-user-circle"></i>
                        <span class="sidebar-text">Profil Saya</span>
                    </a>
                @endif

                <!-- Settings Menu -->
                <a href="{{ route('settings.index') }}"
                    class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" title="Pengaturan">
=======
                <!-- Laporan Penjualan - Accessible by All Users -->
                <a href="{{ route('laporan.index') }}"
                    class="nav-link {{ request()->routeIs('laporan.*') && !request()->routeIs('keuangan.*') ? 'active' : '' }}"
                    title="Laporan Penjualan">
                    <i class="fas fa-chart-line"></i>
                    <span class="sidebar-text">Laporan Penjualan</span>
                </a>

                <!-- Profile Menu -->
                <a href="{{ route('users.profile') }}"
                    class="nav-link {{ request()->routeIs('users.profile') ? 'active' : '' }}"
                    title="Profil Saya">
                    <i class="fas fa-user-circle"></i>
                    <span class="sidebar-text">Profil Saya</span>
                </a>
                
                <!-- Settings Menu -->
                <a href="{{ route('settings.index') }}"
                    class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}"
                    title="Pengaturan">
>>>>>>> 930047ac1763748d4e7621981cc5da996819a2ec
                    <i class="fas fa-cog"></i>
                    <span class="sidebar-text">Pengaturan</span>
                </a>

                @if (Auth::user()->isOwner())
                    <!-- Owner Only Section -->
                    <div class="px-4 mt-4 mb-2 section-label">
                        <div
                            class="text-xs font-semibold owner-section-label uppercase tracking-wider flex items-center">
                            <i class="fas fa-crown mr-2 text-yellow-400"></i>
                            <span class="sidebar-text">Owner Only</span>
                        </div>
                    </div>

                    <a href="{{ route('produk.index') }}"
<<<<<<< HEAD
                        class="nav-link {{ request()->routeIs('produk.*') ? 'active' : '' }}" title="Produk">
=======
                        class="nav-link {{ request()->routeIs('produk.*') ? 'active' : '' }}"
                        title="Produk">
>>>>>>> 930047ac1763748d4e7621981cc5da996819a2ec
                        <i class="fas fa-box"></i>
                        <span class="sidebar-text">Produk</span>
                    </a>

                    <a href="{{ route('suppliers.index') }}"
<<<<<<< HEAD
                        class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" title="Supplier">
=======
                        class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}"
                        title="Supplier">
>>>>>>> 930047ac1763748d4e7621981cc5da996819a2ec
                        <i class="fas fa-truck"></i>
                        <span class="sidebar-text">Supplier</span>
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

                    <!-- Laporan Keuangan - Owner Only -->
                    <div class="px-4 mt-4 mb-2 section-label">
                        <div class="text-xs font-semibold text-emerald-400 uppercase tracking-wider flex items-center">
                            <i class="fas fa-file-invoice-dollar mr-2"></i>
<<<<<<< HEAD
                            <span class="sidebar-text">Laporan </span>
=======
                            <span class="sidebar-text">Laporan Keuangan</span>
>>>>>>> 930047ac1763748d4e7621981cc5da996819a2ec
                        </div>
                    </div>

                    <a href="{{ route('keuangan.index') }}"
                        class="nav-link {{ request()->routeIs('keuangan.*') ? 'active' : '' }}"
                        title="Laporan Keuangan">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span class="sidebar-text">Laporan Keuangan</span>
                    </a>
                @endif

                <!-- Laporan Penjualan - Sembunyikan dari Kasir -->
                @if (Auth::user()->isOwner())
                    <a href="{{ route('laporan.index') }}"
                        class="nav-link {{ request()->routeIs('laporan.*') && !request()->routeIs('keuangan.*') ? 'active' : '' }}"
                        title="Laporan Penjualan">
                        <i class="fas fa-chart-line"></i>
                        <span class="sidebar-text">Laporan Penjualan</span>
                    </a>
                @endif

            </nav>

            <!-- User Info - Fixed at bottom (Simple Display) -->
<<<<<<< HEAD
            {{-- <div class="flex-shrink-0 p-3 border-t border-green-700">
=======
            <div class="flex-shrink-0 p-3 border-t border-green-700">
>>>>>>> 930047ac1763748d4e7621981cc5da996819a2ec
                <div class="mb-2 flex items-center space-x-2 user-info-compact">
                    <!-- User Photo -->
                    @if (Auth::user()->gambar_user)
                        <img src="{{ asset('storage/users/' . Auth::user()->gambar_user) }}"
                            alt="{{ Auth::user()->name }}" class="user-avatar-sidebar flex-shrink-0">
                    @else
                        <div
                            class="user-avatar-sidebar flex-shrink-0 bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center text-white font-bold text-sm shadow-lg">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                        </div>
                    @endif

                    <!-- User Info -->
                    <div class="flex-1 min-w-0 user-info-text">
                        <div class="text-white text-xs font-semibold truncate user-name sidebar-text">
                            {{ Auth::user()->name }}
                        </div>
                        <div class="text-emerald-300 text-xs truncate user-email sidebar-text">
                            {{ Auth::user()->email }}
                        </div>
                        <div class="mt-1">
                            @if (Auth::user()->isOwner())
                                <span
                                    class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300">
                                    <i class="fas fa-crown mr-1 text-yellow-400 text-xs"></i>
                                    <span class="sidebar-text">Owner</span>
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-semibold bg-green-500/20 text-green-300">
                                    <i class="fas fa-user mr-1 text-xs"></i>
                                    <span class="sidebar-text">Kasir</span>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
<<<<<<< HEAD
            </div> --}}
=======
            </div>
>>>>>>> 930047ac1763748d4e7621981cc5da996819a2ec
        </aside>

        <!-- Overlay for mobile -->
        <div id="sidebarOverlay" class="fixed inset-0 bg-black/60 z-30 hidden lg:hidden backdrop-blur-sm"></div>

        <!-- Main Content Area -->
        <div id="mainContent" class="main-content flex-1">
            <!-- Top Navigation Bar -->
            <header class="glass-effect sticky top-0 z-20 shadow-lg">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <button id="sidebarToggle" class="lg:hidden text-gray-700 hover:text-green-600 transition">
                                <i class="fas fa-bars text-xl"></i>
                            </button>
                            <h1
                                class="text-2xl font-bold bg-gradient-to-r from-emerald-600 to-green-600 bg-clip-text text-transparent">
                                @yield('page-title', 'Dashboard')
                            </h1>
                        </div>

                        <div class="flex items-center space-x-3">
                            <!-- User Avatar in Navbar with Dropdown -->
                            <div class="relative">
                                <button onclick="toggleNavUserDropdown()" class="group relative focus:outline-none">
                                    @if (Auth::user()->gambar_user)
                                        <img src="{{ asset('storage/users/' . Auth::user()->gambar_user) }}"
                                            alt="{{ Auth::user()->name }}"
                                            class="user-avatar transition-transform group-hover:scale-110">
                                    @else
                                        <div
                                            class="user-avatar bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center text-white font-bold text-sm shadow-lg transition-transform group-hover:scale-110">
                                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                </button>

                                <!-- Dropdown Menu -->
<<<<<<< HEAD
                                <div id="navUserDropdown"
                                    class="hidden absolute right-0 top-full mt-2 w-56 bg-white rounded-xl shadow-2xl overflow-hidden border border-gray-200 z-50">
                                    <!-- User Info in Dropdown -->
                                    <div
                                        class="px-4 py-3 bg-gradient-to-r from-emerald-50 to-green-50 border-b border-gray-200">
=======
                                <div id="navUserDropdown" class="hidden absolute right-0 top-full mt-2 w-56 bg-white rounded-xl shadow-2xl overflow-hidden border border-gray-200 z-50">
                                    <!-- User Info in Dropdown -->
                                    <div class="px-4 py-3 bg-gradient-to-r from-emerald-50 to-green-50 border-b border-gray-200">
>>>>>>> 930047ac1763748d4e7621981cc5da996819a2ec
                                        <div class="flex items-center space-x-3">
                                            @if (Auth::user()->gambar_user)
                                                <img src="{{ asset('storage/users/' . Auth::user()->gambar_user) }}"
                                                    alt="{{ Auth::user()->name }}"
                                                    class="w-10 h-10 rounded-full object-cover border-2 border-emerald-300">
                                            @else
<<<<<<< HEAD
                                                <div
                                                    class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center text-white font-bold text-sm border-2 border-emerald-300">
=======
                                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center text-white font-bold text-sm border-2 border-emerald-300">
>>>>>>> 930047ac1763748d4e7621981cc5da996819a2ec
                                                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                                </div>
                                            @endif
                                            <div class="flex-1 min-w-0">
                                                <div class="text-sm font-semibold text-gray-900 truncate">
                                                    {{ Auth::user()->name }}
                                                </div>
                                                <div class="text-xs text-gray-600 truncate">
                                                    {{ Auth::user()->email }}
                                                </div>
                                                <div class="mt-1">
                                                    @if (Auth::user()->isOwner())
<<<<<<< HEAD
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
=======
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
>>>>>>> 930047ac1763748d4e7621981cc5da996819a2ec
                                                            <i class="fas fa-crown mr-1 text-yellow-500 text-xs"></i>
                                                            Owner
                                                        </span>
                                                    @else
<<<<<<< HEAD
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">
=======
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">
>>>>>>> 930047ac1763748d4e7621981cc5da996819a2ec
                                                            <i class="fas fa-user mr-1 text-xs"></i>
                                                            Kasir
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Menu Items -->
                                    <div class="py-2">
<<<<<<< HEAD
                                        <a href="{{ route('users.profile') }}"
                                            class="flex items-center px-4 py-2.5 text-gray-700 hover:bg-emerald-50 transition text-sm group">
                                            <i
                                                class="fas fa-user-circle mr-3 text-emerald-600 group-hover:text-emerald-700"></i>
                                            <span>Lihat Profil</span>
                                        </a>

                                        <div class="border-t border-gray-200 my-1"></div>

=======
                                        <a href="{{ route('users.profile') }}" 
                                            class="flex items-center px-4 py-2.5 text-gray-700 hover:bg-emerald-50 transition text-sm group">
                                            <i class="fas fa-user-circle mr-3 text-emerald-600 group-hover:text-emerald-700"></i>
                                            <span>Lihat Profil</span>
                                        </a>
                                        
                                        <div class="border-t border-gray-200 my-1"></div>
                                        
>>>>>>> 930047ac1763748d4e7621981cc5da996819a2ec
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="w-full flex items-center px-4 py-2.5 text-red-600 hover:bg-red-50 transition text-sm group">
                                                <i class="fas fa-sign-out-alt mr-3 group-hover:text-red-700"></i>
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

            <!-- Content Area -->
            <main class="p-6 content-area">
                <!-- Alert Messages -->
                @if (session('success'))
                    <div class="bg-gradient-to-r from-green-400 to-emerald-500 text-white p-4 mb-6 rounded-xl alert-slide-in shadow-xl"
                        role="alert">
                        <div class="flex items-start">
                            <div class="bg-white/20 p-2 rounded-lg mr-3">
                                <i class="fas fa-check-circle text-lg"></i>
                            </div>
                            <div>
                                <p class="font-bold text-sm">Berhasil!</p>
                                <p class="text-sm opacity-90">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-gradient-to-r from-red-500 to-pink-500 text-white p-4 mb-6 rounded-xl alert-slide-in shadow-xl"
                        role="alert">
                        <div class="flex items-start">
                            <div class="bg-white/20 p-2 rounded-lg mr-3">
                                <i class="fas fa-exclamation-circle text-lg"></i>
                            </div>
                            <div>
                                <p class="font-bold text-sm">Error!</p>
                                <p class="text-sm opacity-90">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-gradient-to-r from-orange-400 to-red-500 text-white p-4 mb-6 rounded-xl alert-slide-in shadow-xl"
                        role="alert">
                        <div class="flex items-start">
                            <div class="bg-white/20 p-2 rounded-lg mr-3">
                                <i class="fas fa-exclamation-triangle text-lg"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-bold mb-2 text-sm">Terdapat kesalahan:</p>
                                <ul class="space-y-1 text-sm opacity-90">
                                    @foreach ($errors->all() as $error)
                                        <li class="flex items-start">
                                            <span class="mr-2">•</span>
                                            <span>{{ $error }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Page Content -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Sidebar Toggle Script -->
    <script>
        // Sidebar Toggle for Mobile
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const mainContent = document.getElementById('mainContent');

        function toggleSidebar() {
            sidebar.classList.toggle('-translate-x-full');
            sidebarOverlay.classList.toggle('hidden');
        }

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', toggleSidebar);
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', toggleSidebar);
        }

        // Sidebar Collapse/Expand for Desktop
        function toggleSidebarCollapse() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
<<<<<<< HEAD

=======
            
>>>>>>> 930047ac1763748d4e7621981cc5da996819a2ec
            // Save state to localStorage
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        }

        // Navbar User Dropdown Toggle
        function toggleNavUserDropdown() {
            const dropdown = document.getElementById('navUserDropdown');
            dropdown.classList.toggle('hidden');
        }

        // Close navbar dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('navUserDropdown');
            const userButton = event.target.closest('button[onclick="toggleNavUserDropdown()"]');
<<<<<<< HEAD

=======
            
>>>>>>> 930047ac1763748d4e7621981cc5da996819a2ec
            if (!userButton && dropdown && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });

        // Load saved state on page load
        document.addEventListener('DOMContentLoaded', function() {
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed && window.innerWidth >= 1024) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
            }
        });

        // Close sidebar on window resize to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                sidebar.classList.remove('-translate-x-full');
                sidebarOverlay.classList.add('hidden');
            }
        });

        // Auto hide alerts after 5 seconds
        setTimeout(function() {
            document.querySelectorAll('[role="alert"]').forEach(function(el) {
                el.style.transition = 'all 0.5s ease-out';
                el.style.opacity = '0';
                el.style.transform = 'translateX(100%)';
                setTimeout(function() {
                    el.remove();
                }, 500);
            });
        }, 5000);

        // Add smooth scroll behavior
        document.documentElement.style.scrollBehavior = 'smooth';

        // Close modal dengan ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const dropdown = document.getElementById('navUserDropdown');
                if (dropdown && !dropdown.classList.contains('hidden')) {
                    dropdown.classList.add('hidden');
                }
            }
        });

        // Stock Badge Helper Function
        function getStockBadgeClass(stock, minStock = 10) {
            if (stock <= 0) {
                return 'stock-critical';
            } else if (stock <= 5) {
                return 'stock-critical';
            } else if (stock <= minStock) {
                return 'stock-warning';
            } else if (stock <= minStock * 2) {
                return 'stock-low';
            } else {
                return 'stock-safe';
            }
        }

        function getStockIcon(stock, minStock = 10) {
            if (stock <= 0) {
                return 'fa-times-circle';
            } else if (stock <= 5) {
                return 'fa-exclamation-triangle';
            } else if (stock <= minStock) {
                return 'fa-exclamation-circle';
            } else if (stock <= minStock * 2) {
                return 'fa-info-circle';
            } else {
                return 'fa-check-circle';
            }
        }

        function getStockText(stock) {
            if (stock <= 0) {
                return 'Habis';
            } else if (stock <= 5) {
                return 'Sangat Menipis!';
            } else if (stock <= 10) {
                return 'Menipis';
            } else if (stock <= 20) {
                return 'Perlu Restock';
            } else {
                return 'Aman';
            }
        }
    </script>

    <script src="{{ asset('js/printer-helper.js') }}"></script>

    @stack('scripts')
</body>

</html>
