<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\StrukResource;
use App\Services\LayananKasir;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class TransaksiController extends Controller
{
    public function __construct(
        private readonly LayananKasir $kasir,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $tanggal = $request->date('tanggal')?->toDateString() ?? now()->toDateString();

        return StrukResource::collection($this->kasir->tanggal($tanggal))
            ->additional(['meta' => ['tanggal' => $tanggal]]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'item' => ['required', 'array', 'min:1'],
            'item.*.sku' => ['required', 'string'],
            'item.*.kuantitas' => ['required', 'integer', 'min:1'],
            'metode_bayar' => ['required', 'in:tunai,qris,kartu_debit'],
        ]);

        $struk = $this->kasir->proses(
            $request->only(['item', 'member', 'metode_bayar', 'dibayar']),
            $request->attributes->get('kasir')['nama'],
        );

        return StrukResource::make($struk)
            ->response()
            ->setStatusCode(201)
            ->header('Location', route('api.v1.pos.transaksi.show', $struk->nomor));
    }

    public function show(string $nomor): StrukResource
    {
        return StrukResource::make($this->kasir->cari($nomor));
    }
}
