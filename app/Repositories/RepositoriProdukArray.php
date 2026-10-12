<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\RepositoriProduk;

final class RepositoriProdukArray implements RepositoriProduk
{
    private const KATALOG = [
        [
            'sku' => 'PRD-001',
            'nama' => 'Kopi Kenangan',
            'kategori' => 'minuman',
            'harga' => 15000,
            'stok' => 10,
        ],
        [
            'sku' => 'PRD-002',
            'nama' => 'Roti Cokelat',
            'kategori' => 'makanan',
            'harga' => 12000,
            'stok' => 5,
        ],
    ];

    public function semua(): array
    {
        return self::KATALOG;
    }

    public function cariSku(string $sku): ?array
    {
        foreach (self::KATALOG as $produk) {
            if ($produk['sku'] === $sku) {
                return $produk;
            }
        }

        return null;
    }
}
