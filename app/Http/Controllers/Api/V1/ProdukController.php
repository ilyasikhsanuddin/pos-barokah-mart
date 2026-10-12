<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\LayananKatalog;
use Illuminate\Http\JsonResponse;

final class ProdukController extends Controller
{
    public function __construct(
        private readonly LayananKatalog $katalog,
    ) {
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'data' => $this->katalog->semua(),
        ]);
    }

    public function show(string $sku): JsonResponse
    {
        return response()->json([
            'data' => $this->katalog->detail($sku),
        ]);
    }

    public function pemasok(string $sku): JsonResponse
    {
        return response()->json([
            'data' => $this->katalog->pemasok($sku),
        ]);
    }
}
