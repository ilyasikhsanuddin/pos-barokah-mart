<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\AbstractPaginator;

final class ProdukCollection extends ResourceCollection
{
    /** Setiap anggota koleksi dibentuk oleh Resource ini. */
    public $collects = ProdukResource::class;

    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return ['data' => $this->collection];
    }

    /**
     * Merapikan bagian pagination bawaan Laravel.
     * Bawaannya menaruh SELURUH isi paginator ke dalam "meta", termasuk kunci "links" berisi satu
     * objek per nomor halaman dan kunci "path". Akibatnya ada dua kunci bernama "links" dengan bentuk
     * berbeda pada satu response yang sama - sumber kebingungan yang tidak perlu.
     *
     * @param  array<string, mixed>  $paginated
     * @param  array<string, mixed>  $default
     * @return array<string, mixed>
     */
    public function paginationInformation(Request $request, array $paginated, array $default): array
    {
        return [
            'links' => $default['links'], // first, last, prev, next
            'meta' => [
                'halaman' => $paginated['current_page'],
                'per_halaman' => $paginated['per_page'],
                'total_halaman' => $paginated['last_page'],
                'total' => $paginated['total'],
            ],
        ];
    }

    public function withResponse(Request $request, $response): void
    {
        // Katalog boleh disimpan sebentar oleh klien; harga berubah
        // jarang. Cache sungguhan dibahas pada Modul 12.
        $response->header('Cache-Control', 'private, max-age=30');
    }

    /** Membantu pembaca: koleksi ini memang selalu dipaginasi. */
    public static function daripada(AbstractPaginator $paginator): self
    {
        return new self($paginator);
    }
}