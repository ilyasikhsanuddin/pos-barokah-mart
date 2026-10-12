<?php 
  
declare(strict_types=1); 
  
namespace App\Models; 
  
use App\Domain\MetodeBayar; 
use App\Domain\StatusTransaksi; 
use Carbon\CarbonImmutable; 
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\Relations\HasMany; 
  
final class Transaksi extends Model 
{ 
    use HasFactory; 
  
    protected $table = 'transaksi'; 
  
    protected $fillable = [ 
        'nomor', 'kasir', 'member', 'metode_bayar', 'status', 
        'subtotal', 'diskon_grosir', 'diskon_member', 'total_diskon', 
        'dpp', 'ppn', 'total', 'pembulatan', 'total_bayar', 
        'dibayar', 'kembalian', 
        'alasan_batal', 'dibatalkan_oleh', 'dibatalkan_pada', 
    ]; 
  
      protected function casts(): array 
    { 
        return [ 
            'member'          => 'boolean', 
            'metode_bayar'    => MetodeBayar::class, 
            'status'          => StatusTransaksi::class, 
            'dibatalkan_pada' => 'datetime', 
        ]; 
    } 
  
<<<<<<< HEAD:App/Models/Transaksi.php
    public function item() { return $this->hasMany(ItemTransaksi::class); }
    
    public function scopeSelesai($query) 
=======
    /* ---------------- Relasi ---------------- */ 
  
    /** 
     * Baris-baris struk (one-to-many). Dinamai "item" agar sama dengan 
     * kunci "item" pada response API sejak Modul 3. 
     */ 
    public function item(): HasMany 
>>>>>>> main:app/Models/Transaksi.php
    { 
        return $this->hasMany(ItemTransaksi::class, 'transaksi_id'); 
    } 
  
    /* ---------------- Query scope ---------------- */ 
  
    public function scopeSelesai(Builder $query): Builder 
    { 
        return $query->where('transaksi.status', StatusTransaksi::Selesai); 
    } 
  
    /** 
     * Struk pada satu tanggal, sebagai RENTANG waktu, bukan whereDate(). 
     * 
     * whereDate() menghasilkan  DATE(created_at) = '2026-09-14'. 
     * Fungsi DATE() yang membungkus kolom membuat indeks pada created_at 
     * tidak dapat dipakai. Rentang setengah terbuka [00:00, 00:00 besok) 
     * memberi hasil yang sama dan indeksnya terpakai (Langkah 11). 
     */ 
    public function scopeTanggal(Builder $query, string $tanggal): Builder 
    { 
        $awal = CarbonImmutable::parse($tanggal)->startOfDay(); 
  
        return $query 
            ->where('transaksi.created_at', '>=', $awal) 
            ->where('transaksi.created_at', '<', $awal->addDay()); 
    } 
}