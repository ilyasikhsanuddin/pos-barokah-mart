<?php

namespace App\Providers;

use App\Contracts\RepositoriProduk;
use App\Contracts\RepositoriTransaksi;
use App\Repositories\RepositoriProdukEloquent;
use App\Repositories\RepositoriTransaksiEloquent;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(RepositoriProduk::class, RepositoriProdukEloquent::class);
        $this->app->bind(RepositoriTransaksi::class, RepositoriTransaksiEloquent::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}