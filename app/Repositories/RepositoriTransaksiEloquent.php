<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\RepositoriTransaksi;
use App\Models\ItemTransaksi;
use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

final class RepositoriTransaksiEloquent implements RepositoriTransaksi
{
    /**
     * Mengambil transaksi berdasarkan tanggal.
     *
     * @return Collection<int, Transaksi>
     */
    public function tanggal(string $tanggal): Collection
    {
        return Transaksi::query()
            ->with('item')
            ->tanggal($tanggal)
            ->orderBy('nomor')
            ->get();
    }

    /**
     * Mencari transaksi berdasarkan nomor struk.
     */
    public function cariNomor(string $nomor): ?Transaksi
    {
        return Transaksi::query()
            ->with('item')
            ->where('nomor', $nomor)
            ->first();
    }

    /**
     * Menyimpan transaksi beserta item struk.
     */
    public function simpan(array $transaksi): Transaksi
    {
        $item = $transaksi['item'];

        $baris = Transaksi::create(
            Arr::except($transaksi, [
                'item',
                'total_bayar_format',
            ])
        );

        $idProduk = Produk::query()
            ->whereIn('sku', array_column($item, 'sku'))
            ->pluck('id', 'sku');

        ItemTransaksi::insert(
            array_map(
                static fn (array $b): array => [
                    'transaksi_id' => $baris->id,
                    'produk_id' => $idProduk[$b['sku']],
                    'sku' => $b['sku'],
                    'nama_produk' => $b['nama'],
                    'harga_satuan' => $b['harga_satuan'],
                    'kuantitas' => $b['kuantitas'],
                    'diskon' => $b['diskon'],
                    'total' => $b['total'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                $item,
            )
        );

        return $baris->load('item');
    }

    /**
     * Memperbarui data transaksi.
     */
    public function perbarui(
        string $nomor,
        array $perubahan
    ): void {
        Transaksi::query()
            ->where('nomor', $nomor)
            ->update($perubahan);
    }

    /**
     * Menghitung urutan nomor struk berikutnya pada tanggal tertentu.
     */
    public function urutanBerikutnya(string $tanggal): int
    {
        return Transaksi::query()
            ->where(
                'nomor',
                'like',
                'POS-' . str_replace('-', '', $tanggal) . '-%'
            )
            ->count() + 1;
    }
}