<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\LayananLaporan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Get(
    path: '/api/v1/pos/laporan/harian',
    summary: 'Laporan penjualan harian',
    tags: ['Laporan'],
    security: [['ApiKeyAuth' => []]],
    parameters: [
        new OA\Parameter(
            name: 'tanggal',
            in: 'query',
            required: false,
            schema: new OA\Schema(type: 'string', format: 'date')
        )
    ],
    responses: [
        new OA\Response(response: 200, description: 'Berhasil'),
        new OA\Response(response: 401, description: 'API key tidak valid')
    ]
)]
#[OA\Get(
    path: '/api/v1/pos/laporan/terlaris',
    summary: 'Laporan produk terlaris',
    tags: ['Laporan'],
    security: [['ApiKeyAuth' => []]],
    parameters: [
        new OA\Parameter(
            name: 'tanggal',
            in: 'query',
            required: false,
            schema: new OA\Schema(type: 'string', format: 'date')
        ),
        new OA\Parameter(
            name: 'batas',
            in: 'query',
            required: false,
            schema: new OA\Schema(
                type: 'integer',
                minimum: 1,
                maximum: 20,
                default: 5
            )
        )
    ],
    responses: [
        new OA\Response(response: 200, description: 'Berhasil'),
        new OA\Response(response: 401, description: 'API key tidak valid')
    ]
)]
#[OA\Get(
    path: '/api/v1/pos/laporan/kategori',
    summary: 'Laporan penjualan per kategori',
    tags: ['Laporan'],
    security: [['ApiKeyAuth' => []]],
    responses: [
        new OA\Response(response: 200, description: 'Berhasil'),
        new OA\Response(response: 401, description: 'API key tidak valid')
    ]
)]
final class LaporanController extends Controller
{
    public function __construct(
        private readonly LayananLaporan $laporan,
    ) {
    }

    public function harian(Request $request): JsonResponse
    {
        $tanggal = $this->tanggal($request);

        return response()->json([
            'data' => $this->laporan->harian($tanggal),
        ]);
    }

    public function terlaris(Request $request): JsonResponse
    {
        $tanggal = $this->tanggal($request);
        $batas = (int) ($request->query('batas') ?? 5);

        return response()->json([
            'data' => $this->laporan->terlaris(
                $tanggal,
                max(1, min($batas, 20))
            ),
            'meta' => [
                'tanggal' => $tanggal,
                'batas' => $batas,
            ],
        ]);
    }

    public function kategori(): JsonResponse
    {
        return response()->json([
            'data' => $this->laporan->perKategori(),
        ]);
    }

    private function tanggal(Request $request): string
    {
        $tanggal = $request->string('tanggal')->trim()->toString();

        return $tanggal !== ''
            ? $tanggal
            : now()->toDateString();
    }
}