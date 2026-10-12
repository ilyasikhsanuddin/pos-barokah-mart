<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\RepositoriTransaksi;
use Illuminate\Support\Facades\Storage;

final class RepositoriTransaksiBerkas implements RepositoriTransaksi
{
    private string $path = 'transaksi.json';

    public function semua(): array
    {
        if (! Storage::disk('local')->exists($this->path)) {
            return [];
        }

        $isi = Storage::disk('local')->get($this->path);

        return json_decode($isi, true) ?? [];
    }

    public function cariNomor(string $nomor): ?array
    {
        $semua = $this->semua();

        return $semua[$nomor] ?? null;
    }

    public function simpan(array $transaksi): void
    {
        $semua = $this->semua();
        $semua[$transaksi['nomor']] = $transaksi;

        Storage::disk('local')->put(
            $this->path,
            json_encode($semua, JSON_PRETTY_PRINT)
        );
    }

    public function perbarui(string $nomor, array $perubahan): void
    {
        $semua = $this->semua();
        if (isset($semua[$nomor])) {
            $semua[$nomor] = array_merge($semua[$nomor], $perubahan);
            Storage::disk('local')->put(
                $this->path,
                json_encode($semua, JSON_PRETTY_PRINT)
            );
        }
    }
}
