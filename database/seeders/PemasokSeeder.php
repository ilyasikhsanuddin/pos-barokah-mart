<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Pemasok;
use Illuminate\Database\Seeder;

final class PemasokSeeder extends Seeder
{
    public function run(): void
    {
        $pemasok = [
            [
                'kode' => 'PSK001',
                'nama' => 'PT Sumber Makmur',
                'kota' => 'Surakarta',
                'telepon' => '081234567801',
                'aktif' => true,
            ],
            [
                'kode' => 'PSK002',
                'nama' => 'CV Berkah Jaya',
                'kota' => 'Semarang',
                'telepon' => '081234567802',
                'aktif' => true,
            ],
            [
                'kode' => 'PSK003',
                'nama' => 'UD Mitra Sejahtera',
                'kota' => 'Yogyakarta',
                'telepon' => '081234567803',
                'aktif' => true,
            ],
        ];

        foreach ($pemasok as $data) {
            Pemasok::updateOrCreate(
                ['kode' => $data['kode']],
                $data
            );
        }
    }
}
