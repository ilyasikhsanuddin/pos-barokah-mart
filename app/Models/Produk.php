<?php 
  
declare(strict_types=1); 
  
namespace App\Models; 
  
use App\Domain\Uang; 
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\Relations\BelongsTo; 
use Illuminate\Database\Eloquent\Relations\BelongsToMany; 
use Illuminate\Database\Eloquent\SoftDeletes; 
  
final class Produk extends Model 
{ 
    use HasFactory, SoftDeletes; 
  
    protected $table = 'produk'; 
  
    // "stok" tetap di luar $fillable (alasan: lihat Modul 4, Langkah 7). 
    protected $fillable = ['kategori_id', 'sku', 'nama', 'harga', 'aktif']; 
  
    protected function casts(): array 
    { 
        return [ 
            'harga' => 'integer', 
            'stok'  => 'integer', 
            'aktif' => 'boolean', 
        ]; 
    } 
  
    /* ---------------- Relasi ---------------- */ 
  
    /** Kebalikan dari Kategori::produk() (many-to-one). */ 
    public function kategori(): BelongsTo 
    { 
        return $this->belongsTo(Kategori::class, 'kategori_id'); 
    } 
  
    /** 
     * Satu produk dipasok beberapa pemasok, satu pemasok memasok 
     * banyak produk (many-to-many) lewat tabel pivot pemasok_produk. 
     */ 
    public function pemasok(): BelongsToMany 
    { 
        return $this->belongsToMany(Pemasok::class, 'pemasok_produk') 
            ->as('pasokan')                          // $p->pasokan->harga_beli, bukan $p->pivot 
            ->withPivot(['harga_beli', 'utama'])     // kolom pivot yang ikut dibaca 
            ->withTimestamps() 
            ->orderByPivot('utama', 'desc')          // pemasok utama selalu pertama 
            ->orderByPivot('harga_beli'); 
    } 
  
    /* ---------------- Query scope ---------------- */ 
  
    public function scopeAktif(Builder $query): Builder 
    { 
        return $query->where('produk.aktif', true); 
    } 
  
    public function scopeTersedia(Builder $query): Builder 
    { 
        return $query->where('produk.stok', '>', 0); 
    } 
  
    public function scopeKategoriKode(Builder $query, string $kode): Builder 
    { 
        // Modul 4 memakai subquery manual. Kini cukup menyebut relasinya. 
        return $query->whereRelation('kategori', 'kode', $kode); 
    } 
  
    public function hargaFormat(): string 
    { 
        return (new Uang($this->harga))->format(); 
    }
}