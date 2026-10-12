<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\RepositoriTransaksi;
use App\Domain\Uang;

final class LayananLaporan
{
    public function __construct(
        private readonly RepositoriTransaksi $transaksi,
    ) {
    }

    /** @return array<string, mixed> */
    public function harian(string $tanggal): array
    {
        // Kueri 1: Agregat ringkasan transaksi utama pada tanggal tersebut
        $ringkasan = \App\Models\Transaksi::query()
            ->whereDate('created_at', $tanggal)
            ->where('status', \App\Domain\StatusTransaksi::Selesai)
            ->selectRaw('COUNT(*) as jumlah, SUM(total_bayar) as omzet, SUM(ppn) as total_ppn, SUM(total_diskon) as total_diskon')
            ->first();

        // Kueri 2: Pengelompokan omzet berdasarkan metode bayar
        $perMetodeData = \App\Models\Transaksi::query()
            ->whereDate('created_at', $tanggal)
            ->where('status', \App\Domain\StatusTransaksi::Selesai)
            ->selectRaw('metode_bayar, SUM(total_bayar) as total')
            ->groupBy('metode_bayar')
            ->pluck('total', 'metode_bayar')
            ->all();

        $jumlah = (int) ($ringkasan->jumlah ?? 0);
        $omzet = (int) ($ringkasan->omzet ?? 0);
        $ppn = (int) ($ringkasan->total_ppn ?? 0);
        $diskon = (int) ($ringkasan->total_diskon ?? 0);

        $perMetode = [];
        foreach ($perMetodeData as $metode => $total) {
            $label = is_string($metode) ? $metode : ($metode->value ?? $metode);
            $perMetode[$label] = (int) $total;
        }

        return [
            'tanggal' => $tanggal,
            'jumlah_transaksi' => $jumlah,
            'omzet' => $omzet,
            'omzet_format' => (new Uang($omzet))->format(),
            'total_diskon' => $diskon,
            'total_ppn' => $ppn,
            'rata_rata_struk' => $jumlah > 0 ? intdiv($omzet, $jumlah) : 0,
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

        // Urutkan menurun berdasarkan kuantitas terjual
        usort($rekap, static fn (array $a, array $b): int => $b['kuantitas'] <=> $a['kuantitas']);

        return array_slice($rekap, 0, $batas);
    }

    /** @return array<int, array<string, mixed>> */
    private function transaksiSelesai(string $tanggal): array 
    { 
        return array_values(array_filter(
            $this->transaksi->tanggal($tanggal),
            static fn (array $t): bool => $t['status'] === 'selesai'
        ));
    }
}
