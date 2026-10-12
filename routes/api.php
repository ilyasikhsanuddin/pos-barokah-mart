<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ProdukController;
use App\Http\Controllers\Api\V1\TransaksiController;
use App\Http\Controllers\Api\V1\LaporanController;

Route::get('/ping', function () {
    return response()->json([
        'ok' => true,
        'message' => 'POS Barokah Mart API aktif',
    ]);
});

Route::prefix('v1/pos')
    ->name('api.v1.pos.')
    ->middleware('kasir')
    ->group(function () {
        Route::get('/produk', [ProdukController::class, 'index']);
        Route::get('/transaksi', [TransaksiController::class, 'index']);
        Route::post('/transaksi', [TransaksiController::class, 'store'])->middleware('jam.buka');
        Route::post('/transaksi/{nomor}/batal', [TransaksiController::class, 'batal'])->middleware('peran:supervisor');
        Route::get('/laporan/harian', [LaporanController::class, 'harian']);
        Route::get('/laporan/terlaris', [LaporanController::class, 'terlaris']);
    });
