<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            KategoriProdukSeeder::class,
            PemasokSeeder::class,           // butuh produk
            TransaksiContohSeeder::class,
            RiwayatPenjualanSeeder::class,  // setelah produk ada
        ]);
    }
}
