<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\RepositoriTransaksi;
use App\Domain\Uang;
use Illuminate\Support\Facades\DB;

final class LayananLaporan
{
    public function __construct(
        private readonly RepositoriTransaksi $transaksi,
    ) {
    }

    /** @return array<string, mixed> */
    public function harian(string $tanggal): array
    {
        $selesai = $this->transaksiSelesai($tanggal);

        $omzet = array_sum(array_column($selesai, 'total_bayar'));
        $ppn = array_sum(array_column($selesai, 'ppn'));
        $diskon = array_sum(array_column($selesai, 'total_diskon'));
        $jumlah = count($selesai);

        $perMetode = [];

        foreach ($selesai as $t) {
            $perMetode[$t['metode_bayar']] =
                ($perMetode[$t['metode_bayar']] ?? 0)
                + $t['total_bayar'];
        }

        return [
            'tanggal' => $tanggal,
            'jumlah_transaksi' => $jumlah,
            'omzet' => $omzet,
            'omzet_format' => (new Uang($omzet))->format(),
            'total_diskon' => $diskon,
            'total_ppn' => $ppn,
            'rata_rata_struk' => $jumlah > 0
                ? intdiv($omzet, $jumlah)
                : 0,
            'per_metode_bayar' => $perMetode,
        ];
    }

    /** @return array<int, array<string, mixed>> */
    public function terlaris(string $tanggal, int $batas = 5): array
    {
        $rekap = [];

        foreach ($this->transaksiSelesai($tanggal) as $transaksi) {
            foreach ($transaksi['item'] as $baris) {
                $sku = $baris['sku'];

                $rekap[$sku] ??= [
                    'sku' => $sku,
                    'nama' => $baris['nama'],
                    'kuantitas' => 0,
                    'pendapatan' => 0,
                ];

                $rekap[$sku]['kuantitas'] += $baris['kuantitas'];
                $rekap[$sku]['pendapatan'] += $baris['total'];
            }
        }

        // Urutkan menurun berdasarkan kuantitas terjual.
        usort(
            $rekap,
            static fn (array $a, array $b): int =>
                $b['kuantitas'] <=> $a['kuantitas']
        );

        return array_slice($rekap, 0, $batas);
    }

    /**
     * Laporan penjualan berdasarkan kategori.
     *
     * Produk yang di-soft delete tetap diperhitungkan
     * selama data produk dan item transaksi historisnya masih ada.
     *
     * @return array<int, array<string, mixed>>
     */
    public function perKategori(): array
    {
        return DB::table('kategori')
            ->leftJoin(
                'produk',
                'produk.kategori_id',
                '=',
                'kategori.id'
            )
            ->leftJoin(
                'item_transaksi',
                'item_transaksi.produk_id',
                '=',
                'produk.id'
            )
            ->leftJoin('transaksi', function ($join) {
                $join->on(
                    'transaksi.id',
                    '=',
                    'item_transaksi.transaksi_id'
                )->where('transaksi.status', '=', 'selesai');
            })
            ->where('kategori.aktif', true)
            ->select([
                'kategori.id',
                'kategori.kode',
                'kategori.nama',
            ])
            ->selectRaw(
                'COALESCE(
                    SUM(
                        CASE
                            WHEN transaksi.id IS NOT NULL
                            THEN item_transaksi.kuantitas
                            ELSE 0
                        END
                    ),
                    0
                ) AS jumlah_terjual'
            )
            ->selectRaw(
                'COALESCE(
                    SUM(
                        CASE
                            WHEN transaksi.id IS NOT NULL
                            THEN item_transaksi.total
                            ELSE 0
                        END
                    ),
                    0
                ) AS total_penjualan'
            )
            ->groupBy(
                'kategori.id',
                'kategori.kode',
                'kategori.nama'
            )
            ->orderBy('kategori.nama')
            ->get()
            ->map(static fn ($baris): array => (array) $baris)
            ->all();
    }

    /** @return array<int, array<string, mixed>> */
    private function transaksiSelesai(string $tanggal): array
    {
        return array_values(array_filter(
            $this->transaksi->semua(),
            static fn (array $t): bool =>
                $t['status'] === 'selesai'
                && str_starts_with($t['waktu'], $tanggal),
        ));
    }
}