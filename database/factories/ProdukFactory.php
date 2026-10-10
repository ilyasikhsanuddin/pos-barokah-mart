<?php

namespace Database\Factories;

use App\Models\Produk;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Produk>
 */
class ProdukFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sku' => strtoupper(fake()->unique()->bothify('SKU-####')),
            'nama' => fake()->words(3, true),
            'harga' => fake()->numberBetween(5000, 100000),
            'stok' => fake()->numberBetween(0, 100),
            'aktif' => true,
        ];
    }
}