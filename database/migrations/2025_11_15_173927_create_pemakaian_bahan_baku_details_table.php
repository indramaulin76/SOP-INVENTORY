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
        Schema::create('pemakaian_bahan_baku_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pemakaian_bahan_baku_id');
            $table->foreign('pemakaian_bahan_baku_id', 'pemakaian_bahan_detail_fk')
                ->references('id')
                ->on('pemakaian_bahan_bakus')
                ->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->string('barang_nama');
            $table->string('barang_kode');
            $table->decimal('quantity', 10, 2);
            $table->string('satuan');
            $table->decimal('harga', 15, 2);
            $table->decimal('jumlah', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemakaian_bahan_baku_details');
    }
};
