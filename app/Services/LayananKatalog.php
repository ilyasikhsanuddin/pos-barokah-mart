<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\RepositoriProduk;
use App\Exceptions\ProdukTidakDitemukan;

final class LayananKatalog
{
    public function __construct(
        private readonly RepositoriProduk $repositori,
    ) {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function semua(): array
    {
        return $this->repositori->semua();
    }

    /**
     * @return array<string, mixed>
     */
    public function detail(string $sku): array
    {
        $produk = $this->repositori->cariSku($sku);

        if ($produk === null) {
            throw new ProdukTidakDitemukan($sku);
        }

        return $produk;
    }

    /**
     * Pemasok sebuah produk beserta marginnya (AB-13).
     *
     * @return array<string, mixed>
     */
    public function pemasok(string $sku): array
    {
        $produk = $this->repositori->cariPemasok($sku);

        if ($produk === null) {
            throw new ProdukTidakDitemukan($sku);
        }

        $produk['pemasok'] = array_map(
            static function (array $p) use ($produk): array {
                $margin = $produk['harga'] - $p['harga_beli'];

                return $p + [
                    'margin' => $margin,
                    'margin_persen' => round($margin * 100 / $produk['harga'], 1),
                ];
            },
            $produk['pemasok'],
        );

        return $produk;
    }
}
