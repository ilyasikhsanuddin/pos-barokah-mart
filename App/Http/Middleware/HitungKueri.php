<?php 
  
declare(strict_types=1); 
  
namespace App\Http\Middleware; 
  
use Closure; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use Symfony\Component\HttpFoundation\Response; 
  
/** 
 * Menghitung jumlah kueri basis data selama satu permintaan, lalu 
 * mengirimkannya lewat header X-Jumlah-Kueri. 
 * 
 * Hanya aktif di lingkungan "local". Di produksi, jumlah kueri adalah 
 * informasi internal yang tidak boleh terlihat dari luar (Modul 10). 
 */ 
final class HitungKueri 
{ 
    public function handle(Request $request, Closure $next): Response 
    { 
        if (! app()->isLocal()) { 
            return $next($request); 
        } 
  
        $jumlah = 0; 
  
        // Dipanggil setiap kali satu kueri selesai dijalankan. 
        DB::listen(function () use (&$jumlah): void { 
            $jumlah++; 
        }); 
  
        $response = $next($request); 
  
        $response->headers->set('X-Jumlah-Kueri', (string) $jumlah); 
  
        return $response; 
    } 
} 