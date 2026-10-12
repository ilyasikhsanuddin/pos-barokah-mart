<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Support\Str;
use RuntimeException;

abstract class KesalahanPos extends RuntimeException
{
    abstract public function kodeHttp(): int;

    public function kodeKesalahan(): string
    {
        return Str::snake(class_basename($this));
    }

    public function konteks(): array
    {
        return [];
    }
}
