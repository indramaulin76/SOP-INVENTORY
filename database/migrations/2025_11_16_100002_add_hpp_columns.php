<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add HPP columns to output transaction tables

        // Penjualan
        Schema::table('penjualan_barang_jadi_details', function (Blueprint $table) {
            $table->decimal('hpp_unit', 15, 2)->default(0)->after('harga');
            $table->decimal('hpp_total', 15, 2)->default(0)->after('jumlah');
        });

        // Pemakaian Bahan Baku
        Schema::table('pemakaian_bahan_baku_details', function (Blueprint $table) {
            $table->decimal('hpp_unit', 15, 2)->default(0)->after('harga');
            $table->decimal('hpp_total', 15, 2)->default(0)->after('jumlah');
        });

        // Pemakaian Barang Dalam Proses
        Schema::table('pemakaian_barang_dalam_proses_details', function (Blueprint $table) {
            $table->decimal('hpp_unit', 15, 2)->default(0)->after('harga');
            $table->decimal('hpp_total', 15, 2)->default(0)->after('jumlah');
        });
    }

    public function down(): void
    {
        Schema::table('penjualan_barang_jadi_details', function (Blueprint $table) {
            $table->dropColumn(['hpp_unit', 'hpp_total']);
        });
        Schema::table('pemakaian_bahan_baku_details', function (Blueprint $table) {
            $table->dropColumn(['hpp_unit', 'hpp_total']);
        });
        Schema::table('pemakaian_barang_dalam_proses_details', function (Blueprint $table) {
            $table->dropColumn(['hpp_unit', 'hpp_total']);
        });
    }
};
