<?php

declare(strict_types=1);

namespace App\Exceptions;

final class TransaksiSudahDibatalkan extends KesalahanPos
{
    public function kodeHttp(): int
    {
        return 409; // Conflict
    }
}
