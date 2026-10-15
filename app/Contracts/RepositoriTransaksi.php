<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Transaksi;
use Illuminate\Support\Collection;

interface RepositoriTransaksi
{
    public function tanggal(string $tanggal): Collection;

    public function cariNomor(string $nomor): ?Transaksi;

    public function simpan(array $transaksi): Transaksi;

    public function perbarui(string $nomor, array $perubahan): void;

    public function urutanBerikutnya(string $tanggal): int;
}