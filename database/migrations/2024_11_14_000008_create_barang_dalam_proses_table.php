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
        Schema::create('barang_dalam_proses', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('nomor_faktur')->unique();
            $table->text('keterangan')->nullable();
            $table->string('nama_supplier')->nullable();
            $table->string('kode_supplier')->nullable();
            $table->decimal('total_harga', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_dalam_proses');
    }
};
