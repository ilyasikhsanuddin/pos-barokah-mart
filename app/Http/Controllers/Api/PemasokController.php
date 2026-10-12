<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pemasok;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

#[OA\Get(
    path: '/api/v1/pos/pemasok',
    summary: 'Daftar pemasok',
    tags: ['Pemasok'],
    security: [['ApiKeyAuth' => []]],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Daftar pemasok berhasil diambil'
        ),
        new OA\Response(
            response: 401,
            description: 'API key tidak valid atau tidak tersedia'
        )
    ]
)]
final class PemasokController extends Controller
{
    public function index(): JsonResponse
    {
        $pemasok = Pemasok::query()
            ->select([
                'id',
                'kode',
                'nama',
                'kota',
                'telepon',
                'aktif',
            ])
            ->orderBy('nama')
            ->get();

        return response()->json([
            'data' => $pemasok,
        ]);
    }
}