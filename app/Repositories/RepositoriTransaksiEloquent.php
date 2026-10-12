<<<<<<< HEAD
<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\RepositoriTransaksi;
use App\Models\ItemTransaksi;
use App\Models\Produk;
use App\Models\Transaksi;

final class RepositoriTransaksiEloquent implements RepositoriTransaksi
{
    /** @return array<int, array<string, mixed>> */
    public function semua(): array
    {
        return Transaksi::query()
            ->with('item')
            ->orderBy('nomor')
            ->get()
            ->map($this->keArray(...))
            ->all();
    }

    /** @return array<int, array<string, mixed>> */
    public function tanggal(string $tanggal): array
    {
        return Transaksi::query()
            ->tanggal($tanggal)
            ->with('item')
            ->orderBy('nomor')
            ->get()
            ->map($this->keArray(...))
            ->all();
    }

    /** @return array<string, mixed>|null */
    public function cariNomor(string $nomor): ?array
    {
        $transaksi = Transaksi::query()
            ->with('item')
            ->where('nomor', $nomor)
            ->first();

        return $transaksi === null
            ? null
            : $this->keArray($transaksi);
    }

    public function simpan(array $transaksi): void
    {
        $item = $transaksi['item'];
        unset($transaksi['item']);

        $baris = Transaksi::create($transaksi);

        // Mengambil ID produk berdasarkan SKU dalam satu query.
        $idProduk = Produk::query()
            ->whereIn('sku', array_column($item, 'sku'))
            ->pluck('id', 'sku');

        ItemTransaksi::insert(array_map(
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
        ));
    }

    public function perbarui(string $nomor, array $perubahan): void
    {
        Transaksi::query()
            ->where('nomor', $nomor)
            ->update($perubahan);
    }

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

    /** @return array<string, mixed> */
    private function keArray(Transaksi $transaksi): array
    {
        $item = $transaksi->item
            ->map(static fn (ItemTransaksi $i): array => [
                'sku' => $i->sku,
                'nama' => $i->nama_produk,
                'harga_satuan' => $i->harga_satuan,
                'kuantitas' => $i->kuantitas,
                'diskon' => $i->diskon,
                'total' => $i->total,
            ])
            ->all();

        return [
            'nomor' => $transaksi->nomor,
            'waktu' => $transaksi->created_at->toIso8601String(),
            'kasir' => $transaksi->kasir,
            'member' => $transaksi->member,
            'metode_bayar' => $transaksi->metode_bayar->value,
            'metode_label' => $transaksi->metode_bayar->label(),
            'status' => $transaksi->status->value,
            'item' => $item,
            'subtotal' => $transaksi->subtotal,
            'diskon_grosir' => $transaksi->diskon_grosir,
            'diskon_member' => $transaksi->diskon_member,
            'total_diskon' => $transaksi->total_diskon,
            'dpp' => $transaksi->dpp,
            'ppn' => $transaksi->ppn,
            'total' => $transaksi->total,
            'pembulatan' => $transaksi->pembulatan,
            'total_bayar' => $transaksi->total_bayar,
            'dibayar' => $transaksi->dibayar,
            'kembalian' => $transaksi->kembalian,
        ];
    }
}
=======
<?php 
  
declare(strict_types=1); 
  
namespace App\Repositories; 
  
use App\Contracts\RepositoriTransaksi; 
use App\Models\ItemTransaksi; 
use App\Models\Produk; 
use App\Models\Transaksi; 
use Illuminate\Support\Arr; 
  
/** 
 * Versi Modul 5: seluruh akses baris struk lewat relasi Transaksi::item(), 
 * dan SELALU dimuat di depan (eager loading) dengan with('item'). 
 */ 
final class RepositoriTransaksiEloquent implements RepositoriTransaksi 
{ 
    public function tanggal(string $tanggal): array 
    { 
        return Transaksi::query() 
            ->with('item')               // 1 kueri untuk SELURUH baris struk 
            ->tanggal($tanggal) 
            ->orderBy('nomor') 
            ->get() 
            ->map($this->keArray(...)) 
            ->all(); 
    } 
  
    public function cariNomor(string $nomor): ?array 
    { 
        $transaksi = Transaksi::query() 
            ->with('item') 
            ->where('nomor', $nomor) 
            ->first(); 
  
        return $transaksi === null ? null : $this->keArray($transaksi); 
    } 
  
    public function simpan(array $transaksi): void 
    { 
        $item = $transaksi['item']; 
  
        // Hanya kolom tabel yang diteruskan. "total_bayar_format" adalah 
        // nilai tampilan hasil hitung(); ia dibentuk saat dibaca, bukan 
        // disimpan. Sejak Modul 4 kolom ini dibuang diam-diam oleh Eloquent; 
        // mode ketat (Langkah 5) memaksa pembuangan itu dinyatakan eksplisit. 
        $baris = Transaksi::create(Arr::except($transaksi, ['item', 'total_bayar_format'])); 
  
        $idProduk = Produk::query() 
            ->whereIn('sku', array_column($item, 'sku')) 
            ->pluck('id', 'sku'); 
  
        // insert() massal: SATU kueri untuk seluruh baris. Relasi punya 
        // createMany(), tetapi ia menjalankan satu INSERT per baris.

            ItemTransaksi::insert(array_map( 
            static fn (array $b): array => [ 
                'transaksi_id' => $baris->id, 
                'produk_id'    => $idProduk[$b['sku']], 
                'sku'          => $b['sku'], 
                'nama_produk'  => $b['nama'], 
                'harga_satuan' => $b['harga_satuan'], 
                'kuantitas'    => $b['kuantitas'], 
                'diskon'       => $b['diskon'], 
                'total'        => $b['total'], 
                'created_at'   => now(), 
                'updated_at'   => now(), 
            ], 
            $item, 
        )); 
    } 
  
    public function perbarui(string $nomor, array $perubahan): void 
    { 
        Transaksi::query()->where('nomor', $nomor)->update($perubahan); 
    } 
  
    public function urutanBerikutnya(string $tanggal): int 
    { 
        return Transaksi::query() 
            ->where('nomor', 'like', 'POS-' . str_replace('-', '', $tanggal) . '-%') 
            ->count() + 1; 
    } 
  
    /** @return array<string, mixed> */ 
    private function keArray(Transaksi $transaksi): array 
    { 
        // Relasi SUDAH termuat oleh with('item'): tidak ada kueri di sini. 
        $item = $transaksi->item 
            ->map(static fn (ItemTransaksi $i): array => [ 
                'sku'          => $i->sku, 
                'nama'         => $i->nama_produk, 
                'harga_satuan' => $i->harga_satuan, 
                'kuantitas'    => $i->kuantitas, 
                'diskon'       => $i->diskon, 
                'total'        => $i->total, 
            ]) 
            ->all(); 
  
        return [ 
            'nomor'           => $transaksi->nomor, 
            'waktu'           => $transaksi->created_at->toIso8601String(), 
            'kasir'           => $transaksi->kasir, 
            'member'          => $transaksi->member, 
            'metode_bayar'    => $transaksi->metode_bayar->value, 
            'metode_label'    => $transaksi->metode_bayar->label(), 
            'status'          => $transaksi->status->value, 
            'item'            => $item, 
            'subtotal'        => $transaksi->subtotal, 
            'diskon_grosir'   => $transaksi->diskon_grosir, 
            'diskon_member'   => $transaksi->diskon_member, 
            'total_diskon'    => $transaksi->total_diskon, 
            'dpp'             => $transaksi->dpp, 
            'ppn'             => $transaksi->ppn, 
            'total'           => $transaksi->total, 
            'pembulatan'      => $transaksi->pembulatan, 
            'total_bayar'     => $transaksi->total_bayar, 
            'dibayar'         => $transaksi->dibayar, 
            'kembalian'       => $transaksi->kembalian, 
            // Jejak pembatalan, sesuai kontrak response Modul 3. 
            'alasan_batal'    => $transaksi->alasan_batal, 
            'dibatalkan_oleh' => $transaksi->dibatalkan_oleh, 
            'dibatalkan_pada' => $transaksi->dibatalkan_pada?->toIso8601String(), 
        ]; 
    } 
} 
>>>>>>> main
