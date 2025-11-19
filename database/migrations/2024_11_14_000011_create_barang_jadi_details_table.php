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
        Schema::create('barang_jadi_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_jadi_id')->constrained('barang_jadis')->onDelete('cascade');
            $table->string('barang_nama');
            $table->string('barang_kode');
            $table->decimal('barang_qty', 10, 2);
            $table->string('barang_satuan');
            $table->decimal('barang_harga', 15, 2);
            $table->decimal('barang_jumlah', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_jadi_details');
    }
};
