<?php 
  
declare(strict_types=1); 
  
namespace App\Providers; 
  
use App\Contracts\RepositoriProduk; 
use App\Contracts\RepositoriTransaksi; 
use App\Repositories\RepositoriProdukEloquent; 
use App\Repositories\RepositoriTransaksiEloquent; 
use Illuminate\Database\Eloquent\Model; 
use Illuminate\Support\ServiceProvider; 
  
final class AppServiceProvider extends ServiceProvider 
{ 
    public function register(): void 
    { 
        $this->app->bind(RepositoriProduk::class, RepositoriProdukEloquent::class); 
        $this->app->bind(RepositoriTransaksi::class, RepositoriTransaksiEloquent::class); 
    } 
  
    public function boot(): void 
    { 
        /* 
        | Mode ketat Eloquent, SELAIN di produksi. Satu baris ini 
        | menyalakan tiga detektor sekaligus: 
        | 
        |   preventLazyLoading()                   relasi dimuat diam-diam (N+1) 
        |   preventSilentlyDiscardingAttributes()  kolom dibuang diam-diam saat 
        |                                          mass assignment 
        |   preventAccessingMissingAttributes()    membaca kolom yang tidak 
        |                                          ikut di-SELECT 
        | 
        | Ketiganya mengubah kesalahan yang DIAM menjadi exception yang 
        | TERLIHAT, pada saat yang paling murah untuk memperbaikinya. 
        */ 
        Model::shouldBeStrict(! $this->app->isProduction()); 
    } 
} 