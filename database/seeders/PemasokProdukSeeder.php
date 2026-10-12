<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Pemasok;
use App\Models\Produk;
use Illuminate\Database\Seeder;

final class PemasokProdukSeeder extends Seeder
{
    public function run(): void
    {
        $pemasok = Pemasok::query()
            ->orderBy('id')
            ->get();

        $produk = Produk::query()
            ->orderBy('id')
            ->get();

        if ($pemasok->isEmpty() || $produk->isEmpty()) {
            return;
        }

        foreach ($produk as $index => $item) {
            $supplier = $pemasok[$index % $pemasok->count()];

            $item->pemasok()->syncWithoutDetaching([
                $supplier->id => [
                    'harga_beli' => max(1, (int) $item->harga - 1000),
                    'utama' => true,
                ],
            ]);
        }
    }
}
