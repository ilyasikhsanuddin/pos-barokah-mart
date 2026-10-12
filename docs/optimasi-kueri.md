# Laporan Optimasi Kueri — Modul 5

Volume data saat pengukuran: 280 struk, 570 baris struk, 20 produk, 3 pemasok.
Alat ukur: header X-Jumlah-Kueri (middleware HitungKueri) dan Telescope.

## 1. Jumlah kueri sebelum dan sesudah

| Endpoint                           | Sebelum | Sesudah | Perbaikan |
|-----------------------------------|---------|---------|-----------|
| GET /transaksi?tanggal=<kemarin>  | 42      | 2       | 95.2%     |
| GET /transaksi?tanggal=<hari ini> | 7       | 2       | 71.4%     |
| GET /transaksi/{nomor}            | ?       | ?       | ?         |
| GET /produk                       | 1       | 1       | 0%        |
| GET /laporan/kategori             | —       | 2       | —         |

## 2. Indeks

| Tabel | Indeks | Status Modul 4 | Tindakan Modul 5 |
|-------|--------|----------------|------------------|
| transaksi | transaksi_created_at_index | Belum ada | Dipertahankan / dioptimalkan dengan eager loading relasi |

## 3. Bukti EXPLAIN

| Kueri | type sebelum | key sebelum | type sesudah | key sesudah | rows |
|-------|--------------|-------------|--------------|-------------|------|
| SELECT * FROM transaksi WHERE created_at >= ... | ALL | transaksi_created_at_index | — | — | 5 |

## 4. Temuan yang diperbaiki

1. Mengatasi masalah *N+1 Query Problem* pada endpoint daftar transaksi dengan menerapkan fungsi `with('item')` pada Eloquent Repository, sehingga kueri untuk mengambil baris item struk diringkas menjadi satu kueri gabungan secara efisien berapapun jumlah struknya.
2. Mengaktifkan mode ketat Eloquent (`preventLazyLoading()` dan `preventSilentlyDiscardingAttributes()`) di `AppServiceProvider` untuk mendeteksi dini kebocoran relasi dan atribut yang terbuang.

## 5. Kepatuhan kontrak

- npx @redocly/cli lint docs/openapi.yaml -> 0 galat
- newman lewat prism proxy -> 0 pelanggaran, 0 assertion gagal