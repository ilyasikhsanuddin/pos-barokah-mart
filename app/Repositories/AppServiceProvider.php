<?php

namespace App\Providers;

use App\Contracts\RepositoriProduk;
use App\Contracts\RepositoriTransaksi;
use App\Repositories\RepositoriProdukArray;
use App\Repositories\RepositoriTransaksiBerkas;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Setiap kali butuh RepositoriProduk, buat instance baru dari RepositoriProdukArray
        $this->app->bind(RepositoriProduk::class, RepositoriProdukArray::class);

        // Setiap kali butuh RepositoriTransaksi, gunakan instance tunggal (singleton) dari RepositoriTransaksiBerkas
        $this->app->singleton(RepositoriTransaksi::class, RepositoriTransaksiBerkas::class);
    }

    public function boot(): void
    {
        //
    }
}
