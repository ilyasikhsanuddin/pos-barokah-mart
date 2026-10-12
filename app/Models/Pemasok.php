<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class Pemasok extends Model
{
    protected $table = 'pemasok';

<<<<<<< HEAD:app/Models/Pemasok.php
    protected $fillable = [
        'kode',
        'nama',
        'kota',
        'telepon',
        'aktif',
    ];

    protected function casts(): array
    {
        return [
            'aktif' => 'boolean',
        ];
=======
    protected $fillable = ['kode', 'nama', 'kota', 'telepon', 'aktif'];

    protected function casts(): array
    {
        return ['aktif' => 'boolean'];
>>>>>>> main:App/Models/Pemasok.php
    }

    public function produk(): BelongsToMany
    {
<<<<<<< HEAD:app/Models/Pemasok.php
        return $this->belongsToMany(
            Produk::class,
            'pemasok_produk'
        )
=======
        return $this->belongsToMany(Produk::class, 'pemasok_produk')
>>>>>>> main:App/Models/Pemasok.php
            ->as('pasokan')
            ->withPivot(['harga_beli', 'utama'])
            ->withTimestamps();
    }
}
