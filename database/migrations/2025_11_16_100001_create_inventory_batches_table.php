<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->date('tanggal_masuk');
            $table->decimal('qty_awal', 10, 2);
            $table->decimal('qty_sisa', 10, 2);
            $table->decimal('harga_beli', 15, 2); // Cost per unit
            $table->string('sumber'); // saldo_awal, pembelian, produksi
            $table->nullableMorphs('source'); // Reference to specific transaction (optional)
            $table->timestamps();

            // Index for performance (search for available batches)
            $table->index(['barang_id', 'qty_sisa']);
            $table->index(['barang_id', 'tanggal_masuk']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_batches');
    }
};
