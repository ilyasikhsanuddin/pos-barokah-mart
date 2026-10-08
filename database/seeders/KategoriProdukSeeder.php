<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Database\Seeder;

class KategoriProdukSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            [
                'kode' => 'makanan',
                'nama' => 'Makanan',
                'aktif' => true,
            ],
            [
                'kode' => 'minuman',
                'nama' => 'Minuman',
                'aktif' => true,
            ],
            [
                'kode' => 'kebutuhan_rumah',
                'nama' => 'Kebutuhan Rumah Tangga',
                'aktif' => true,
            ],
            [
                'kode' => 'alat_tulis',
                'nama' => 'Alat Tulis',
                'aktif' => true,
            ],
        ];

        foreach ($kategori as $data) {
            Kategori::create($data);
        }

        Produk::factory()
            ->count(20)
            ->create([
                'kategori_id' => fn () => Kategori::inRandomOrder()->value('id'),
            ]);
    }
}