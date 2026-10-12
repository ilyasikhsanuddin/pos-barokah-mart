<?php

use Illuminate\Database\Migrations\Migration; 
use Illuminate\Database\Schema\Blueprint; 
use Illuminate\Support\Facades\Schema; 
  
return new class extends Migration 
{ 
    public function up(): void 
    { 
        Schema::create('item_transaksi', function (Blueprint $table) { 
            $table->id(); 
  
            // Struk dihapus -> barisnya ikut terhapus. Baris tanpa induk tidak bermakna. 
            $table->foreignId('transaksi_id') 
                  ->constrained(table: 'transaksi') 
                  ->cascadeOnDelete(); 
  
            // Produk TIDAK boleh terhapus selama masih tercatat di struk mana pun. 
            $table->foreignId('produk_id') 
                  ->constrained(table: 'produk') 
                  ->restrictOnDelete(); 
  
            // --- Salinan riwayat (snapshot) --- 
            // SKU, nama, dan harga disalin saat transaksi terjadi. Bila besok 
            // harga kopi naik, struk kemarin tetap menampilkan harga kemarin. 
            $table->string('sku', 20); 
            $table->string('nama_produk', 150); 
            $table->unsignedInteger('harga_satuan'); 
  
            $table->unsignedInteger('kuantitas'); 
            $table->unsignedInteger('diskon')->default(0); 
            $table->unsignedInteger('total'); 
  
            $table->timestamps(); 
  
            // Laporan produk terlaris mengelompokkan berdasarkan produk. 
            $table->index('produk_id'); 
        }); 
    } 
  
    public function down(): void 
    { 
        Schema::dropIfExists('item_transaksi'); 
    } 
}; 
