<?php

declare(strict_types=1);

return [
    'nama_toko' => env('POS_NAMA_TOKO', 'Barokah Mart Solo'),

    'ppn_persen' => (float) env('POS_PPN_PERSEN', 11),

    'pembulatan' => (int) env('POS_PEMBULATAN', 100),

    'member' => [
        'persen' => (float) env('POS_DISKON_MEMBER', 3),
    ],

    'grosir' => [
        'minimal_kuantitas' => (int) env('POS_GROSIR_MINIMAL', 12),
        'persen' => (float) env('POS_GROSIR_PERSEN', 5),
    ],

    'jam' => [
        'buka' => env('POS_JAM_BUKA', '07:00'),
        'tutup' => env('POS_JAM_TUTUP', '22:00'),
    ],

    'kasir' => [
        env('POS_KUNCI_KASIR', 'kasir-dev-001') => [
            'nama' => 'Bambang Saputra',
            'peran' => 'kasir',
        ],

        env('POS_KUNCI_SUPERVISOR', 'spv-dev-001') => [
            'nama' => 'Bagas Prakoso',
            'peran' => 'supervisor',
        ],
    ],
];
