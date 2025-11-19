<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pembelian_bahan_baku_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembelian_bahan_baku_id')->constrained('pembelian_bahan_bakus')->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->decimal('quantity', 10, 2);
            $table->string('satuan');
            $table->decimal('harga_beli', 15, 2);
            $table->decimal('jumlah', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelian_bahan_baku_details');
    }
};
