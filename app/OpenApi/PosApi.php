<?php

declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'POS Barokah Mart API',
    version: '1.0.0',
    description: 'Dokumentasi API untuk sistem POS Barokah Mart.'
)]
#[OA\Server(
    url: 'http://127.0.0.1:8000',
    description: 'Server lokal'
)]
#[OA\SecurityScheme(
    securityScheme: 'ApiKeyAuth',
    type: 'apiKey',
    name: 'X-API-Key',
    in: 'header'
)]
#[OA\Tag(
    name: 'Laporan',
    description: 'Endpoint laporan penjualan'
)]
#[OA\Tag(
    name: 'Pemasok',
    description: 'Endpoint data pemasok'
)]
final class PosApi
{
}
