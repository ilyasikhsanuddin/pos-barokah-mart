<?php 
  
declare(strict_types=1); 
  
namespace App\Models; 
  
use App\Domain\Uang; 
use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\SoftDeletes; 
  
final class Produk extends Model 
{ 
    use HasFactory, SoftDeletes; 
  
    protected $table = 'produk'; 
  
    /** 
     * Kolom yang boleh diisi massal. 
     * "stok" SENGAJA tidak dimasukkan: stok hanya boleh berubah lewat 
     * LayananKasir yang menjalankan pengurangan di dalam transaksi basis 
     * data. Bila stok ada di sini, satu request nakal dapat menambah stok 
     * sendiri tanpa jejak. 
     */ 
    protected $fillable = [ 
        'kategori_id', 
        'sku', 
        'nama', 
        'harga', 
        'aktif', 
    ]; 
  
    protected function casts(): array 
    { 
        return [ 
            'harga' => 'integer', 
            'stok'  => 'integer', 
            'aktif' => 'boolean', 
        ]; 
    } 
  
    /* ---------------- Query scope ---------------- */ 
  
    // Nama kolom ditulis lengkap (produk.aktif). Tabel kategori juga 
    // memiliki kolom "aktif", sehingga tanpa awalan nama tabel kueri 
    // yang memakai join akan gagal: "Column 'aktif' in where clause is ambiguous". 
    public function scopeAktif($query) 
    { 
        return $query->where('produk.aktif', true); 
    } 
  
    public function scopeTersedia($query) 
    { 
        return $query->where('produk.stok', '>', 0); 
    } 
  
    public function scopeKategoriKode($query, string $kode) 
    { 
        // Subquery: relasi Eloquent baru dibahas pada Modul 5. 
        return $query->whereIn( 
            'kategori_id', 
            Kategori::query()->where('kode', $kode)->select('id'), 
        ); 
    } 
  
    /* ---------------- Accessor ---------------- */ 
    /** Dipakai Uang dari Modul 2 agar format rupiah hanya ditulis satu kali. */ 
    public function hargaFormat(): string 
    { 
        return (new Uang($this->harga))->format(); 
    } 
} 