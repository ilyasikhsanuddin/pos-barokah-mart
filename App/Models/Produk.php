<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Uang;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
<<<<<<< HEAD:app/Models/Produk.php
=======
use Illuminate\Database\Eloquent\Relations\BelongsTo;
>>>>>>> main:App/Models/Produk.php
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Produk extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'produk';

    /**
     * Kolom yang boleh diisi massal.
     * Stok hanya boleh berubah lewat LayananKasir.
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
            'stok' => 'integer',
            'aktif' => 'boolean',
        ];
    }

    /* ---------------- Relasi Eloquent (HO5) ---------------- */

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function pemasok(): BelongsToMany
    {
        return $this->belongsToMany(Pemasok::class, 'pemasok_produk')
            ->as('pasokan')
            ->withPivot(['harga_beli', 'utama'])
            ->withTimestamps()
            ->orderByPivot('utama', 'desc')
            ->orderByPivot('harga_beli');
    }

    /* ---------------- Query scope ---------------- */

<<<<<<< HEAD:app/Models/Produk.php
    public function scopeAktif($query)
=======
    // Nama kolom ditulis lengkap (produk.aktif). Tabel kategori juga
    // memiliki kolom "aktif", sehingga tanpa awalan nama tabel kueri
    // yang memakai join akan gagal: "Column 'aktif' in where clause is ambiguous".
    public function scopeAktif(Builder $query): Builder
>>>>>>> main:App/Models/Produk.php
    {
        return $query->where('produk.aktif', true);
    }

    public function scopeTersedia(Builder $query): Builder
    {
        return $query->where('produk.stok', '>', 0);
    }

    public function scopeKategoriKode(Builder $query, string $kode): Builder
    {
<<<<<<< HEAD:app/Models/Produk.php
        return $query->whereIn(
            'kategori_id',
            Kategori::query()->where('kode', $kode)->select('id'),
        );
=======
        // Menggunakan whereRelation yang lebih ringkas dari HO5
        return $query->whereRelation('kategori', 'kode', $kode);
>>>>>>> main:App/Models/Produk.php
    }

    /* ---------------- Accessor ---------------- */

    public function hargaFormat(): string
    {
        return (new Uang($this->harga))->format();
    }

    /* ---------------- Relasi pemasok ---------------- */

    public function pemasok(): BelongsToMany
    {
        return $this->belongsToMany(
            Pemasok::class,
            'pemasok_produk'
        )
            ->as('pasokan')
            ->withPivot(['harga_beli', 'utama'])
            ->withTimestamps();
    }
}
