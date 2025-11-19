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
        Schema::create('pembelian_bahan_bakus', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('nomor_faktur')->unique();
            $table->text('keterangan')->nullable();
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->decimal('total_harga', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelian_bahan_bakus');
    }
};
