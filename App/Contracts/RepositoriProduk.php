<?php 
  
declare(strict_types=1); 
  
namespace App\Contracts; 
  
interface RepositoriProduk 
{ 
    /** @return array<int, array<string, mixed>> */ 
    public function semua(): array; 
  
    /** @return array<string, mixed>|null */ 
    public function cariSku(string $sku): ?array; 
  
    /** 
     * BARU (Modul 5): produk beserta daftar pemasok dan harga belinya. 
     * Alasan penambahan: kebutuhan bisnis baru (AB-13), bukan pergantian 
     * teknologi — kriteria yang sama seperti Modul 4. 
     * 
     * @return array<string, mixed>|null 
     */ 
    public function cariPemasok(string $sku): ?array; 
  
    public function kunciStok(string $sku): int; 
  
    public function ubahStok(string $sku, int $selisih): void; 
}
