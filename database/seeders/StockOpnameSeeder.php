<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Supplier;
use App\Models\BarangDalamProses;
use App\Models\BarangDalamProsesDetail;
use App\Models\BarangJadi;
use App\Models\BarangJadiDetail;
use App\Models\PembelianBahanBaku;
use App\Models\PembelianBahanBakuDetail;
use Illuminate\Database\Seeder;

class StockOpnameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample barang (products)
        $barangs = [
            [
                'kode_barang' => 'BRG-001',
                'nama_barang' => 'Kopi Arabika',
                'satuan' => 'kg',
                'harga_beli' => 50000,
            ],
            [
                'kode_barang' => 'BRG-002',
                'nama_barang' => 'Roti Sourdough',
                'satuan' => 'pcs',
                'harga_beli' => 15000,
            ],
            [
                'kode_barang' => 'BRG-003',
                'nama_barang' => 'Susu Full Cream',
                'satuan' => 'liter',
                'harga_beli' => 25000,
            ],
            [
                'kode_barang' => 'BRG-004',
                'nama_barang' => 'Susu Kental Manis',
                'satuan' => 'liter',
                'harga_beli' => 18000,
            ],
            [
                'kode_barang' => 'BRG-005',
                'nama_barang' => 'Margarin',
                'satuan' => 'kg',
                'harga_beli' => 35000,
            ],
        ];

        foreach ($barangs as $barang) {
            Barang::updateOrCreate(
                ['kode_barang' => $barang['kode_barang']],
                $barang
            );
        }

        // Create sample supplier
        $supplier = Supplier::updateOrCreate(
            ['kode_supplier' => 'SUP-001'],
            [
                'nama_supplier' => 'PT. Supplier Utama',
                'alamat' => 'Jl. Merdeka No. 123',
                'telepon' => '081234567890',
                'email' => 'supplier@example.com',
            ]
        );

        // Create Pembelian Bahan Baku
        $pembelian = PembelianBahanBaku::create([
            'tanggal' => now()->subDays(5),
            'nomor_faktur' => 'INV-2024-001',
            'keterangan' => 'Pembelian rutin',
            'supplier_id' => $supplier->id,
            'total_harga' => 300000,
        ]);

        // Add details to pembelian
        PembelianBahanBakuDetail::create([
            'pembelian_bahan_baku_id' => $pembelian->id,
            'barang_id' => Barang::where('kode_barang', 'BRG-001')->first()->id,
            'quantity' => 15,
            'satuan' => 'kg',
            'harga_beli' => 50000,
            'jumlah' => 750000,
        ]);

        // Create Barang Dalam Proses
        $proses = BarangDalamProses::create([
            'tanggal' => now()->subDays(3),
            'nomor_faktur' => 'PROSES-001',
            'keterangan' => 'Pengolahan barang',
            'nama_supplier' => 'PT. Supplier Utama',
            'kode_supplier' => 'SUP-001',
            'total_harga' => 625000,
        ]);

        BarangDalamProsesDetail::create([
            'barang_dalam_proses_id' => $proses->id,
            'barang_nama' => 'Roti Sourdough',
            'barang_kode' => 'BRG-002',
            'barang_qty' => 25,
            'barang_satuan' => 'pcs',
            'barang_harga' => 15000,
            'barang_jumlah' => 375000,
        ]);

        BarangDalamProsesDetail::create([
            'barang_dalam_proses_id' => $proses->id,
            'barang_nama' => 'Susu Full Cream',
            'barang_kode' => 'BRG-003',
            'barang_qty' => 8,
            'barang_satuan' => 'liter',
            'barang_harga' => 25000,
            'barang_jumlah' => 200000,
        ]);

        BarangDalamProsesDetail::create([
            'barang_dalam_proses_id' => $proses->id,
            'barang_nama' => 'Susu Kental Manis',
            'barang_kode' => 'BRG-004',
            'barang_qty' => 8,
            'barang_satuan' => 'liter',
            'barang_harga' => 18000,
            'barang_jumlah' => 144000,
        ]);

        BarangDalamProsesDetail::create([
            'barang_dalam_proses_id' => $proses->id,
            'barang_nama' => 'Margarin',
            'barang_kode' => 'BRG-005',
            'barang_qty' => 12,
            'barang_satuan' => 'kg',
            'barang_harga' => 35000,
            'barang_jumlah' => 420000,
        ]);

        // Create Barang Jadi
        $jadi = BarangJadi::create([
            'tanggal' => now()->subDays(1),
            'nomor_faktur' => 'JADI-001',
            'keterangan' => 'Produk jadi siap jual',
            'nama_supplier' => 'PT. Supplier Utama',
            'kode_supplier' => 'SUP-001',
            'total_harga' => 500000,
        ]);

        BarangJadiDetail::create([
            'barang_jadi_id' => $jadi->id,
            'barang_nama' => 'Kopi Arabika',
            'barang_kode' => 'BRG-001',
            'barang_qty' => 20,
            'barang_satuan' => 'kg',
            'barang_harga' => 50000,
            'barang_jumlah' => 1000000,
        ]);

        BarangJadiDetail::create([
            'barang_jadi_id' => $jadi->id,
            'barang_nama' => 'Roti Sourdough',
            'barang_kode' => 'BRG-002',
            'barang_qty' => 30,
            'barang_satuan' => 'pcs',
            'barang_harga' => 15000,
            'barang_jumlah' => 450000,
        ]);
    }
}
