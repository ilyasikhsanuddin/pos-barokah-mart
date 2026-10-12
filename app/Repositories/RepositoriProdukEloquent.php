<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\RepositoriProduk;
use App\Models\Produk;

final class RepositoriProdukEloquent implements RepositoriProduk
{
    public function semua(): array
    {
        return Produk::query()
            ->orderBy('nama')
            ->get()
            ->map(fn (Produk $produk): array => $this->keArray($produk))
            ->all();
    }

    public function cariSku(string $sku): ?array
    {
        $produk = Produk::query()
            ->where('sku', $sku)
            ->first();

        return $produk ? $this->keArray($produk) : null;
    }

    private function keArray(Produk $produk): array
    {
        return [
            'sku' => $produk->sku,
            'nama' => $produk->nama,
            'kategori' => $produk->kategori?->kode,
            'harga' => (int) $produk->harga,
            'stok' => (int) $produk->stok,
        ];
    }
}