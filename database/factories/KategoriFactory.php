<?php

namespace Database\Factories;

use App\Models\Kategori;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kategori>
 */
class KategoriFactory extends Factory
{
    public function definition(): array
    {
        $kategori = fake()->randomElement([
            'makanan',
            'minuman',
            'kebutuhan_rumah',
            'alat_tulis',
        ]);

        return [
            'kode' => $kategori,
            'nama' => match ($kategori) {
                'makanan' => 'Makanan',
                'minuman' => 'Minuman',
                'kebutuhan_rumah' => 'Kebutuhan Rumah Tangga',
                'alat_tulis' => 'Alat Tulis',
            },
            'aktif' => true,
        ];
    }
}