<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PenerimaanController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PiutangController;
use App\Http\Controllers\SettingsController;
use App\Http\Middleware\CheckRole;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Auth Routes
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Thermal Printer - Public Access (tidak perlu login)
Route::get('/thermal-printer', function () {
    return view('thermal-printer');
})->name('thermal.printer');

// Protected Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Kasir Management
    Route::get('/kasir', [KasirController::class, 'index'])->name('kasir.index');
    Route::post('/kasir/open', [KasirController::class, 'open'])->name('kasir.open');
    Route::post('/kasir/{id}/close', [KasirController::class, 'close'])->name('kasir.close');

    // Transaksi Routes
    Route::prefix('transaksi')->name('transaksi.')->group(function () {
        Route::get('/', [TransaksiController::class, 'index'])->name('index');
        Route::post('/', [TransaksiController::class, 'store'])->name('store');
        Route::get('/struk/{id}', [TransaksiController::class, 'struk'])->name('struk');
        Route::get('/printer/{id}', [TransaksiController::class, 'strukPrinter'])->name('struk.printer');
        Route::get('/piutang', [TransaksiController::class, 'getPiutang'])->name('piutang');
        Route::get('/detail/{id}', [TransaksiController::class, 'getDetail'])->name('detail');
        Route::post('/bayar-piutang/{id}', [TransaksiController::class, 'bayarPiutang'])->name('bayar.piutang');
    });

    // Profile (All authenticated users)
    Route::get('/profile', [UserController::class, 'profile'])->name('users.profile');
    Route::post('/profile', [UserController::class, 'updateProfile'])->name('users.update-profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('users.update-profile.put');

    // Settings Routes (Accessible by ALL authenticated users)
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::post('/update', [SettingsController::class, 'update'])->name('update');
        Route::get('/api', [SettingsController::class, 'getSettings'])->name('api');
    });

    // Produk Logs Route (Outside owner middleware - accessible by all authenticated users)
    Route::get('/produk/{id}/logs', [ProdukController::class, 'showLog'])->name('produk.logs');

    // Laporan Penjualan - Accessible by ALL authenticated users (Owner & Kasir)
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        Route::get('/export-pdf', [LaporanController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/export-excel', [LaporanController::class, 'exportExcel'])->name('export.excel');
    });

    // Routes Pelanggan (Accessible by ALL authenticated users - Kasir & Owner)
    Route::prefix('pelanggan')->name('pelanggan.')->group(function () {
        Route::get('/', [PelangganController::class, 'index'])->name('index');
        Route::get('/create', [PelangganController::class, 'create'])->name('create');
        Route::post('/', [PelangganController::class, 'store'])->name('store');
        Route::get('/{id}', [PelangganController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [PelangganController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PelangganController::class, 'update'])->name('update');
        Route::delete('/{id}', [PelangganController::class, 'destroy'])->name('destroy');
    });
    
    // Routes Piutang (Accessible by ALL authenticated users - Kasir & Owner)
    Route::prefix('piutang')->name('piutang.')->group(function () {
        Route::get('/', [PiutangController::class, 'index'])->name('index');
        Route::get('/create', [PiutangController::class, 'create'])->name('create');
        Route::post('/', [PiutangController::class, 'store'])->name('store');
        Route::get('/{id}', [PiutangController::class, 'show'])->name('show');
        Route::post('/{id}/bayar', [PiutangController::class, 'bayar'])->name('bayar');
        Route::delete('/{id}', [PiutangController::class, 'destroy'])->name('destroy');
        Route::get('/laporan/view', [PiutangController::class, 'laporan'])->name('laporan');
    });

    // Owner Only Routes
    Route::middleware(CheckRole::class . ':owner')->group(function () {
        // Produk Management
        Route::prefix('produk')->name('produk.')->group(function () {
            Route::get('/', [ProdukController::class, 'index'])->name('index');
            Route::get('/create', [ProdukController::class, 'create'])->name('create');
            Route::post('/', [ProdukController::class, 'store'])->name('store');
            Route::get('/{id}', [ProdukController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [ProdukController::class, 'edit'])->name('edit');
            Route::put('/{id}', [ProdukController::class, 'update'])->name('update');
            Route::delete('/{id}', [ProdukController::class, 'destroy'])->name('destroy');
        });

        // Kategori Management (for Produk)
        Route::prefix('kategori')->name('kategori.')->group(function () {
            Route::get('/', [KategoriController::class, 'index'])->name('index');
            Route::post('/', [KategoriController::class, 'store'])->name('store');
            Route::put('/{id}', [KategoriController::class, 'update'])->name('update');
            Route::delete('/{id}', [KategoriController::class, 'destroy'])->name('destroy');
        });

        // Supplier Management
        Route::prefix('suppliers')->name('suppliers.')->group(function () {
            Route::get('/', [SupplierController::class, 'index'])->name('index');
            Route::get('/create', [SupplierController::class, 'create'])->name('create');
            Route::post('/', [SupplierController::class, 'store'])->name('store');
            Route::get('/{id}', [SupplierController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [SupplierController::class, 'edit'])->name('edit');
            Route::put('/{id}', [SupplierController::class, 'update'])->name('update');
            Route::delete('/{id}', [SupplierController::class, 'destroy'])->name('destroy');
            Route::get('/export/csv', [SupplierController::class, 'export'])->name('export');
        });

        // Penerimaan Barang
        Route::prefix('penerimaan')->name('penerimaan.')->group(function () {
            Route::get('/', [PenerimaanController::class, 'index'])->name('index');
            Route::get('/create', [PenerimaanController::class, 'create'])->name('create');
            Route::post('/', [PenerimaanController::class, 'store'])->name('store');
            Route::get('/{id}', [PenerimaanController::class, 'show'])->name('show');
            Route::delete('/{id}', [PenerimaanController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/print', [PenerimaanController::class, 'print'])->name('print');
            Route::get('/export/csv', [PenerimaanController::class, 'export'])->name('export');
        });

        // User Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{id}', [UserController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{id}', [UserController::class, 'update'])->name('update');
            Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
        });

        // Laporan Keuangan - Owner Only
        Route::prefix('keuangan')->name('keuangan.')->group(function () {
            Route::get('/', [KeuanganController::class, 'index'])->name('index');
            Route::get('/export-pdf', [KeuanganController::class, 'exportPdf'])->name('export.pdf');
            Route::get('/export-excel', [KeuanganController::class, 'exportExcel'])->name('export.excel');
        });
    });
});

// API Routes for AJAX (within auth middleware)
Route::middleware('auth')->prefix('api')->name('api.')->group(function () {
    // Produk API
    Route::get('/produk/search', [ProdukController::class, 'search'])->name('produk.search');
    Route::get('/produk/{id}', [ProdukController::class, 'getDetail'])->name('produk.detail');

    // Kategori API
    Route::get('/kategori/list', [KategoriController::class, 'list'])->name('kategori.list');
    
    // Pelanggan API
    Route::get('/pelanggan/search', [PelangganController::class, 'search'])->name('pelanggan.search');

    // Supplier API (Owner Only)
    Route::middleware(CheckRole::class . ':owner')->group(function () {
        Route::get('/suppliers/search', [SupplierController::class, 'search'])->name('suppliers.search');
        Route::get('/suppliers/{id}/detail', [SupplierController::class, 'getDetail'])->name('suppliers.detail');
        Route::get('/suppliers/{id}/statistics', [SupplierController::class, 'statistics'])->name('suppliers.statistics');
        Route::get('/penerimaan/statistics', [PenerimaanController::class, 'statistics'])->name('penerimaan.statistics');
    });
});