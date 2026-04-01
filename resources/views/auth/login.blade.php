<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - WPOS</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(-45deg, #064e3b, #047857, #059669, #10b981);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }

        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .slide-in-left {
            animation: slideInLeft 0.6s ease-out;
        }

        @keyframes slideInLeft {
            0% {
                opacity: 0;
                transform: translateX(-30px);
            }

            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .slide-in-right {
            animation: slideInRight 0.6s ease-out;
        }

        @keyframes slideInRight {
            0% {
                opacity: 0;
                transform: translateX(30px);
            }

            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .logo-float {
            animation: logoFloat 3s ease-in-out infinite;
        }

        @keyframes logoFloat {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-8px);
            }
        }

        .dots-pulse span {
            animation: dotPulse 1.5s ease-in-out infinite;
        }

        .dots-pulse span:nth-child(2) {
            animation-delay: 0.3s;
        }

        .dots-pulse span:nth-child(3) {
            animation-delay: 0.6s;
        }

        @keyframes dotPulse {

            0%,
            100% {
                opacity: 0.3;
                transform: scale(1);
            }

            50% {
                opacity: 1;
                transform: scale(1.2);
            }
        }

        .input-focus {
            transition: all 0.3s ease;
        }

        .input-focus:focus {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.15);
        }

        .btn-shimmer {
            position: relative;
            overflow: hidden;
        }

        .btn-shimmer::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .btn-shimmer:hover::before {
            left: 100%;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .demo-card {
            transition: all 0.3s ease;
        }

        .demo-card:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }

        .feature-item {
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
        }

        .feature-item:nth-child(1) {
            animation-delay: 0.2s;
        }

        .feature-item:nth-child(2) {
            animation-delay: 0.3s;
        }

        .feature-item:nth-child(3) {
            animation-delay: 0.4s;
        }

        .feature-item:nth-child(4) {
            animation-delay: 0.5s;
        }

        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Hide password toggle button from browser (Chrome) */
        input::-ms-reveal,
        input::-ms-clear {
            display: none;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4 lg:p-8">
    <div class="w-full max-w-6xl flex flex-col lg:flex-row gap-8 items-center">

        <!-- Left Section - Info Content -->
        <div class="w-full lg:w-1/2 text-white slide-in-left">
            <div class="max-w-xl">
                <!-- Main Heading -->
                <div class="mb-8">
                    <div class="flex items-center mb-4">
                        <div class="w-16 h-16 rounded-2xl overflow-hidden mr-4 shadow-xl flex-shrink-0">
                            <img src="{{ asset('images/logo-wpos.png') }}" alt="WPOS Logo"
                                class="w-full h-full object-contain">
                        </div>
                        <div>
                            <h1 class="text-4xl lg:text-5xl font-black">WPOS</h1>
                            <p class="text-emerald-200 font-medium">Point of Sale System</p>
                        </div>
                    </div>
                    <p class="text-lg text-emerald-100 leading-relaxed">
                        Sistem kasir modern berbasis web yang memudahkan pengelolaan toko Anda dengan fitur lengkap dan
                        user-friendly.
                    </p>
                </div>

                <!-- Features List -->
                <div class="space-y-4">
                    <div class="flex items-start feature-item">
                        <div
                            class="w-12 h-12 bg-white/20 backdrop-blur-lg rounded-xl flex items-center justify-center mr-4 flex-shrink-0 shadow-lg">
                            <i class="fas fa-shopping-cart text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg mb-1">Transaksi Cepat</h3>
                            <p class="text-emerald-100 text-sm">Proses penjualan yang mudah dan efisien dengan interface
                                yang intuitif</p>
                        </div>
                    </div>

                    <div class="flex items-start feature-item">
                        <div
                            class="w-12 h-12 bg-white/20 backdrop-blur-lg rounded-xl flex items-center justify-center mr-4 flex-shrink-0 shadow-lg">
                            <i class="fas fa-box text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg mb-1">Manajemen Stok</h3>
                            <p class="text-emerald-100 text-sm">Pantau stok produk secara real-time dengan notifikasi
                                stok menipis</p>
                        </div>
                    </div>

                    <div class="flex items-start feature-item">
                        <div
                            class="w-12 h-12 bg-white/20 backdrop-blur-lg rounded-xl flex items-center justify-center mr-4 flex-shrink-0 shadow-lg">
                            <i class="fas fa-chart-line text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg mb-1">Laporan Lengkap</h3>
                            <p class="text-emerald-100 text-sm">Analisis penjualan dengan grafik dan laporan detail yang
                                informatif</p>
                        </div>
                    </div>

                    <div class="flex items-start feature-item">
                        <div
                            class="w-12 h-12 bg-white/20 backdrop-blur-lg rounded-xl flex items-center justify-center mr-4 flex-shrink-0 shadow-lg">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg mb-1">Multi User</h3>
                            <p class="text-emerald-100 text-sm">Sistem role berbasis Owner dan Kasir dengan hak akses
                                yang berbeda</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Section - Login Form -->
        <div class="w-full lg:w-1/2 max-w-md slide-in-right">
            <div class="glass-card rounded-3xl shadow-2xl">
                <!-- Header Section -->
                <div class="text-center pt-8 pb-6 px-6">
                    <!-- Logo -->
                    <div class="inline-block mb-4 logo-float">
                        <div class="w-20 h-20 rounded-2xl shadow-xl overflow-hidden relative">
                            <div
                                class="absolute inset-0 bg-gradient-to-br from-emerald-400 to-green-500 rounded-2xl blur-xl opacity-50">
                            </div>
                            <img src="{{ asset('images/logo-wpos.png') }}" alt="WPOS Logo"
                                class="w-full h-full object-contain relative z-10">
                        </div>
                    </div>

                    <!-- Brand Name -->
                    <h2
                        class="text-3xl font-black mb-1 bg-gradient-to-r from-emerald-600 via-green-600 to-emerald-700 bg-clip-text text-transparent">
                        WPOS
                    </h2>

                    <!-- Subtitle -->
                    <p class="text-gray-600 font-medium text-sm mb-2">Sistem Kasir Berbasis Mobile</p>

                    <!-- Animated Dots -->
                    <div class="flex justify-center space-x-1.5 dots-pulse">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                        <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                        <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                    </div>
                </div>

                <!-- Form Section -->
                <div class="px-6 pb-6">
                    <!-- Error Messages -->
                    @if ($errors->any())
                        <div class="bg-gradient-to-r from-red-500 to-rose-600 text-white rounded-xl p-4 mb-5 shadow-lg">
                            <div class="flex items-start">
                                <div
                                    class="flex-shrink-0 w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-exclamation-circle text-lg"></i>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="font-semibold text-sm mb-1">Terjadi Kesalahan</p>
                                    <ul class="space-y-1 text-xs opacity-95">
                                        @foreach ($errors->all() as $error)
                                            <li class="flex items-start">
                                                <i class="fas fa-circle text-xs mt-1 mr-2" style="font-size: 4px;"></i>
                                                <span>{{ $error }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- autocomplete="off" pada form mencegah browser autofill --}}
                    <form action="{{ route('login.post') }}" method="POST" class="space-y-4" autocomplete="off">
                        @csrf

                        <!-- Honeypot fields untuk trick browser agar tidak autofill field asli -->
                        <input type="text" name="username_fake" style="display:none;" tabindex="-1"
                            autocomplete="username">
                        <input type="password" name="password_fake" style="display:none;" tabindex="-1"
                            autocomplete="current-password">

                        <!-- Username Field -->
                        <div>
                            <label class="flex items-center text-gray-700 font-semibold text-sm mb-2">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-green-600 rounded-lg flex items-center justify-center mr-2 shadow-md">
                                    <i class="fas fa-user text-white text-xs"></i>
                                </div>
                                <span>Username</span>
                            </label>
                            <input type="text" name="username" value="{{ old('username') }}" autocomplete="off"
                                class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:outline-none input-focus text-gray-800 placeholder-gray-400 text-sm"
                                placeholder="Masukkan username" required>
                        </div>

                        <!-- Password Field -->
                        <div>
                            <label class="flex items-center text-gray-700 font-semibold text-sm mb-2">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-green-600 rounded-lg flex items-center justify-center mr-2 shadow-md">
                                    <i class="fas fa-lock text-white text-xs"></i>
                                </div>
                                <span>Password</span>
                            </label>
                            <div class="relative">
                                <input type="password" name="password" id="passwordInput" autocomplete="new-password"
                                    class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:outline-none input-focus text-gray-800 placeholder-gray-400 text-sm pr-12"
                                    placeholder="Masukkan password" required>
                                <!-- Toggle Show/Hide Password -->
                                <button type="button" id="togglePassword"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-emerald-500 transition-colors duration-200 focus:outline-none">
                                    <i class="fas fa-eye text-sm" id="eyeIcon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Login Button -->
                        <button type="submit"
                            class="w-full mt-5 bg-gradient-to-r from-emerald-500 via-green-600 to-emerald-600 hover:from-emerald-600 hover:via-green-700 hover:to-emerald-700 text-white font-bold py-3.5 rounded-xl shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-300 btn-shimmer">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Login Sekarang
                        </button>
                    </form>
                </div>

                <!-- Footer -->
                <p class="text-center text-xs text-gray-500 mt-4 pb-5">
                    © 2026 WPOS. All rights reserved.
                </p>
            </div>
        </div>
    </div>

    <script>
        // Toggle show/hide password
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('passwordInput');
        const eyeIcon = document.getElementById('eyeIcon');

        togglePassword.addEventListener('click', function() {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            eyeIcon.classList.toggle('fa-eye', !isPassword);
            eyeIcon.classList.toggle('fa-eye-slash', isPassword);
        });

        // Extra: clear autofilled values on page load jika tidak ada interaksi user
        window.addEventListener('load', function() {
            setTimeout(function() {
                const pwd = document.getElementById('passwordInput');
                // Hanya kosongkan jika browser autofill (bukan nilai dari old() Laravel)
                if (pwd && pwd.value && !pwd.dataset.userTyped) {
                    pwd.value = '';
                }
            }, 100);
        });

        // Tandai bahwa user mengetik sendiri
        document.getElementById('passwordInput').addEventListener('input', function() {
            this.dataset.userTyped = 'true';
        });
    </script>
</body>

</html>
