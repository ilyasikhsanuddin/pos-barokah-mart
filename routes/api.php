<?php

declare(strict_types=1);

use App\Http\Controllers\Api\PemasokController;
use App\Http\Controllers\Api\V1\LaporanController;
use Illuminate\Support\Facades\Route;

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
        Route::get('/produk', function () {
            return response()->json([
                'message' => 'Daftar produk',
            ]);
        });

        Route::get('/transaksi', function () {
            return response()->json([
                'message' => 'Daftar transaksi',
            ]);
        });

        Route::post('/transaksi', function () {
            return response()->json([
                'message' => 'Buat transaksi',
            ]);
        })->middleware('jam.buka');

        Route::post('/transaksi/{nomor}/batal', function ($nomor) {
            return response()->json([
                'message' => 'Transaksi berhasil dibatalkan',
                'nomor' => $nomor,
            ]);
        })->middleware('peran:supervisor');

        Route::get('/laporan/harian', [
            LaporanController::class,
            'harian',
        ])->name('laporan.harian');

        Route::get('/laporan/terlaris', [
            LaporanController::class,
            'terlaris',
        ])->name('laporan.terlaris');

        Route::get('/laporan/kategori', [
            LaporanController::class,
            'kategori',
        ])->name('laporan.kategori');

        Route::get('/pemasok', [
            PemasokController::class,
            'index',
        ])->name('pemasok.index');
    });