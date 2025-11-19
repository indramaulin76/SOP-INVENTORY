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
        // Fix penjualan_barang_jadi table
        if (Schema::hasTable('penjualan_barang_jadi')) {
            Schema::table('penjualan_barang_jadi', function (Blueprint $table) {
                if (!Schema::hasColumn('penjualan_barang_jadi', 'tanggal')) {
                    $table->date('tanggal')->after('id');
                }
                if (!Schema::hasColumn('penjualan_barang_jadi', 'nomor_bukti')) {
                    $table->string('nomor_bukti')->unique()->after('tanggal');
                }
                if (!Schema::hasColumn('penjualan_barang_jadi', 'keterangan')) {
                    $table->text('keterangan')->nullable()->after('nomor_bukti');
                }
                if (!Schema::hasColumn('penjualan_barang_jadi', 'nama_customer')) {
                    $table->string('nama_customer')->nullable()->after('keterangan');
                }
                if (!Schema::hasColumn('penjualan_barang_jadi', 'kode_customer')) {
                    $table->string('kode_customer')->nullable()->after('nama_customer');
                }
                if (!Schema::hasColumn('penjualan_barang_jadi', 'total_harga')) {
                    $table->decimal('total_harga', 15, 2)->default(0)->after('kode_customer');
                }
            });
        }

        // Fix penjualan_barang_jadi_details table
        if (Schema::hasTable('penjualan_barang_jadi_details')) {
            Schema::table('penjualan_barang_jadi_details', function (Blueprint $table) {
                if (!Schema::hasColumn('penjualan_barang_jadi_details', 'penjualan_barang_jadi_id')) {
                    $table->foreignId('penjualan_barang_jadi_id')->after('id')->constrained('penjualan_barang_jadi')->onDelete('cascade');
                }
                if (!Schema::hasColumn('penjualan_barang_jadi_details', 'barang_id')) {
                    $table->foreignId('barang_id')->after('penjualan_barang_jadi_id')->constrained('barangs')->onDelete('cascade');
                }
                if (!Schema::hasColumn('penjualan_barang_jadi_details', 'barang_nama')) {
                    $table->string('barang_nama')->after('barang_id');
                }
                if (!Schema::hasColumn('penjualan_barang_jadi_details', 'barang_kode')) {
                    $table->string('barang_kode')->after('barang_nama');
                }
                if (!Schema::hasColumn('penjualan_barang_jadi_details', 'quantity')) {
                    $table->decimal('quantity', 10, 2)->after('barang_kode');
                }
                if (!Schema::hasColumn('penjualan_barang_jadi_details', 'satuan')) {
                    $table->string('satuan')->after('quantity');
                }
                if (!Schema::hasColumn('penjualan_barang_jadi_details', 'harga')) {
                    $table->decimal('harga', 15, 2)->after('satuan');
                }
                if (!Schema::hasColumn('penjualan_barang_jadi_details', 'jumlah')) {
                    $table->decimal('jumlah', 15, 2)->after('harga');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback changes if needed
        if (Schema::hasTable('penjualan_barang_jadi_details')) {
            Schema::table('penjualan_barang_jadi_details', function (Blueprint $table) {
                $columns = ['penjualan_barang_jadi_id', 'barang_id', 'barang_nama', 'barang_kode', 'quantity', 'satuan', 'harga', 'jumlah'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('penjualan_barang_jadi_details', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('penjualan_barang_jadi')) {
            Schema::table('penjualan_barang_jadi', function (Blueprint $table) {
                $columns = ['tanggal', 'nomor_bukti', 'keterangan', 'nama_customer', 'kode_customer', 'total_harga'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('penjualan_barang_jadi', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
