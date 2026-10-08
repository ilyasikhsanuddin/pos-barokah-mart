<?php

namespace Database\Seeders;

use App\Models\ItemTransaksi;
use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Database\Seeder;

class TransaksiContohSeeder extends Seeder
{
    public function run(): void
    {
        $produk = Produk::all();

        if ($produk->isEmpty()) {
            return;
        }

        for ($i = 1; $i <= 5; $i++) {
            $produkDipilih = $produk->random(min(2, $produk->count()));

            $items = [];
            $subtotal = 0;

            foreach ($produkDipilih as $produkItem) {
                $kuantitas = rand(1, 3);
                $totalItem = $produkItem->harga * $kuantitas;

                $items[] = [
                    'produk' => $produkItem,
                    'kuantitas' => $kuantitas,
                    'total' => $totalItem,
                ];

                $subtotal += $totalItem;
            }

            $diskonGrosir = 0;
            $diskonMember = 0;
            $totalDiskon = $diskonGrosir + $diskonMember;

            $dpp = $subtotal - $totalDiskon;
            $ppn = (int) round($dpp * 0.11);
            $total = $dpp + $ppn;

            $transaksi = Transaksi::create([
                'nomor' => 'POS-' . date('Ymd') . '-' . str_pad(
                    (string) $i,
                    4,
                    '0',
                    STR_PAD_LEFT
                ),
                'kasir' => 'Kasir ' . $i,
                'member' => $i % 2 === 0,
                'metode_bayar' => 'tunai',
                'status' => 'selesai',
                'subtotal' => $subtotal,
                'diskon_grosir' => $diskonGrosir,
                'diskon_member' => $diskonMember,
                'total_diskon' => $totalDiskon,
                'dpp' => $dpp,
                'ppn' => $ppn,
                'total' => $total,
                'pembulatan' => 0,
                'total_bayar' => $total,
                'dibayar' => $total,
                'kembalian' => 0,
            ]);

            foreach ($items as $item) {
                $produkItem = $item['produk'];

                ItemTransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'produk_id' => $produkItem->id,
                    'sku' => $produkItem->sku,
                    'nama_produk' => $produkItem->nama,
                    'harga_satuan' => $produkItem->harga,
                    'kuantitas' => $item['kuantitas'],
                    'diskon' => 0,
                    'total' => $item['total'],
                ]);
            }
        }
    }
}