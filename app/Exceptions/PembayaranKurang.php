<?php
declare(strict_types=1);

namespace App\Exceptions;

final class StokTidakCukup extends KesalahanPos
{
    public function kodeHttp(): int
    {
        return 422; // Unprocessable Entity
    }
}