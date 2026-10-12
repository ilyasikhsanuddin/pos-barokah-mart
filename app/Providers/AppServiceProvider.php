<?php

namespace App\Providers;

use App\Contracts\RepositoriProduk;
use App\Repositories\RepositoriProdukEloquent;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(RepositoriProduk::class, RepositoriProdukEloquent::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
