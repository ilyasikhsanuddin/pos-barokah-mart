<<<<<<< HEAD:App/Services/LayananLaporan.php
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
=======
<?php 
  
declare(strict_types=1); 
  
namespace App\Services; 
  
use App\Domain\Uang; 
use App\Models\ItemTransaksi; 
use App\Models\Kategori; 
use App\Models\Transaksi; 
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Support\Facades\DB; 
  
final class LayananLaporan 
{ 
    /** @return array<string, mixed> */ 
    public function harian(string $tanggal): array 
    { 
        // Tidak ada yang berubah di sini. Scope tanggal() yang berubah, 
        // dan kueri ini ikut menjadi cepat tanpa disentuh (Langkah 11). 
        $ringkas = Transaksi::query() 
            ->selesai() 
            ->tanggal($tanggal) 
            ->selectRaw('COUNT(*) AS jumlah') 
            ->selectRaw('COALESCE(SUM(total_bayar), 0)  AS omzet') 
            ->selectRaw('COALESCE(SUM(ppn), 0)          AS ppn') 
            ->selectRaw('COALESCE(SUM(total_diskon), 0) AS diskon') 
            ->first(); 
  
        $perMetode = Transaksi::query() 
            ->selesai() 
            ->tanggal($tanggal) 
            ->groupBy('metode_bayar') 
            ->pluck(DB::raw('SUM(total_bayar)'), 'metode_bayar') 
            ->map(static fn ($nilai): int => (int) $nilai) 
            ->all(); 
  
        $jumlah = (int) $ringkas->jumlah; 
        $omzet  = (int) $ringkas->omzet; 
  
        return [ 
            'tanggal'          => $tanggal, 
            'jumlah_transaksi' => $jumlah, 
            'omzet'            => $omzet, 
            'omzet_format'     => (new Uang($omzet))->format(), 
            'total_diskon'     => (int) $ringkas->diskon, 
            'total_ppn'        => (int) $ringkas->ppn, 
            'rata_rata_struk'  => $jumlah > 0 ? intdiv($omzet, $jumlah) : 0, 
            'per_metode_bayar' => $perMetode, 
        ]; 
    } 
  
    /** @return array<int, array<string, mixed>> */ 
    public function terlaris(string $tanggal, int $batas = 5): array 
    { 
        return ItemTransaksi::query() 
            ->whereIn('item_transaksi.transaksi_id', $this->idStrukSelesai($tanggal)) 
            ->groupBy('item_transaksi.sku', 'item_transaksi.nama_produk') 
            ->orderByDesc('kuantitas') 
            ->limit($batas) 
            ->get([ 
                'item_transaksi.sku', 
                'item_transaksi.nama_produk AS nama', 
                DB::raw('SUM(item_transaksi.kuantitas) AS kuantitas'), 
                DB::raw('SUM(item_transaksi.total)     AS pendapatan'), 
            ]) 
            ->map(static fn ($baris): array => [ 
                'sku'        => $baris->sku, 
                'nama'       => $baris->nama, 
                'kuantitas'  => (int) $baris->kuantitas, 
                'pendapatan' => (int) $baris->pendapatan, 
            ]) 
            ->all(); 
    } 
  
    /** 
     * Penjualan per kategori: SATU kueri berapa pun jumlah kategorinya. 
     * 
     * withSum() menambahkan subquery SUM(...) pada relasi itemTerjual 
     * tanpa memuat satu baris struk pun ke memori PHP. 
     * 
     * @return array<int, array<string, mixed>> 
     */ 
    public function perKategori(string $tanggal): array 
    { 
        $struk = $this->idStrukSelesai($tanggal); 
        $saring = fn (Builder $q) => $q->whereIn('item_transaksi.transaksi_id', $struk); 
  
        return Kategori::query() 
            ->withSum(['itemTerjual as kuantitas'  => $saring], 'item_transaksi.kuantitas') 
            ->withSum(['itemTerjual as pendapatan' => $saring], 'item_transaksi.total') 
            ->orderByDesc('pendapatan') 
            ->orderBy('kode') 
            ->get() 
            ->map(static fn (Kategori $k): array => [ 
                'kode'       => $k->kode, 
                'nama'       => $k->nama, 
                // SUM() atas nol baris menghasilkan NULL, bukan 0. 
                'kuantitas'  => (int) $k->kuantitas, 
                'pendapatan' => (int) $k->pendapatan, 
            ]) 
            ->all(); 
    } 
  
    /** Subquery "id struk selesai pada tanggal ini", dipakai dua laporan. */ 
    private function idStrukSelesai(string $tanggal): Builder 
    { 
        return Transaksi::query()->selesai()->tanggal($tanggal)->select('transaksi.id'); 
    } 
} 
>>>>>>> main:app/Services/LayananLaporan.php
