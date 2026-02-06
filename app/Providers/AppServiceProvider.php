<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Piutang;
use App\Models\Produk;
use App\Observers\PiutangObserver;
use App\Observers\ProdukObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Piutang Observer untuk auto-update total piutang pelanggan
        Piutang::observe(PiutangObserver::class);
        
        // Register Produk Observer untuk logging aktivitas produk
        Produk::observe(ProdukObserver::class);
    }
}
