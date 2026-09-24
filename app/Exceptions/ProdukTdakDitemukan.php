<?php
declare(strict_types=1);

namespace App\Exceptions;

final class ProdukTidakDitemukan extends KesalahanPos
{
    public function kodeHttp(): int
    {
        return 404; // Not Found
    }
}