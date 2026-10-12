<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            // WAJIB sebutkan table: 'kategori' karena nama tabel tunggal
            $table->foreignId('kategori_id')
                ->constrained(table: 'kategori')
                ->restrictOnDelete();
            $table->string('sku', 20)->unique();
            $table->string('nama', 150);
            $table->unsignedInteger('harga'); // Rupiah bulat
            $table->unsignedInteger('stok')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['aktif', 'nama']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
