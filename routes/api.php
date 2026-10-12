<?php

use App\Http\Controllers\Api\V1\LaporanController;
use App\Http\Controllers\Api\V1\ProdukController;
use App\Http\Controllers\Api\V1\TransaksiController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return response()->json(['status' => 'ok']);
});

Route::prefix('v1/pos')->middleware('kasir')->group(function () {
    // Rute Produk & Katalog
    Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index');
    Route::get('/produk/{sku}', [ProdukController::class, 'show'])
        ->where('sku', 'SKU-[0-9]{3}')
        ->name('produk.show');

    // AB-13: Harga beli dan margin hanya untuk supervisor
    Route::get('/produk/{sku}/pemasok', [ProdukController::class, 'pemasok'])
        ->middleware('peran:supervisor')
        ->where('sku', 'SKU-[0-9]{3}')
        ->name('produk.pemasok');

    // Rute Transaksi
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::post('/transaksi', [TransaksiController::class, 'store'])
        ->middleware('jam.buka')
        ->name('transaksi.store');
    Route::get('/transaksi/{nomor}', [TransaksiController::class, 'show'])
        ->where('nomor', 'POS-[0-9]{8}-[0-9]{4}')
        ->name('transaksi.show');
    Route::post('/transaksi/{nomor}/batal', [TransaksiController::class, 'batal'])
        ->middleware('peran:supervisor')
        ->where('nomor', 'POS-[0-9]{8}-[0-9]{4}')
        ->name('transaksi.batal');

    // Rute Laporan
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/harian', [LaporanController::class, 'harian'])->name('harian');
        Route::get('/terlaris', [LaporanController::class, 'terlaris'])->name('terlaris');

        // AB-14: Penjualan per kategori (has-many-through)
        Route::get('/kategori', [LaporanController::class, 'kategori'])->name('kategori');
    });
});
