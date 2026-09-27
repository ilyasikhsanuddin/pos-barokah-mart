<?php

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

        Route::get('/laporan/harian', function () {
            return response()->json([
                'message' => 'Laporan harian',
            ]);
        });

        Route::get('/laporan/terlaris', function () {
            return response()->json([
                'message' => 'Produk terlaris',
            ]);
        });
    });