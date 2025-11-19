<?php

namespace Database\Seeders;

use App\Models\Barang;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barangs = [
            [
                "kode_barang" => "KODE001",
                "nama_barang" => "Tepung Terigu Premium",
                "kategori" => "Bahan Roti",
                "limit_stock" => 20,
                "jenis_satuan" => "Kg",
            ],
            [
                "kode_barang" => "KODE002",
                "nama_barang" => "Gula Pasir Putih",
                "kategori" => "Bahan Roti",
                "limit_stock" => 15,
                "jenis_satuan" => "Kg",
            ],
            [
                "kode_barang" => "KODE003",
                "nama_barang" => "Mentega Tawar",
                "kategori" => "Bahan Roti",
                "limit_stock" => 10,
                "jenis_satuan" => "Kg",
            ],
            [
                "kode_barang" => "KODE004",
                "nama_barang" => "Telur Ayam Segar",
                "kategori" => "Bahan Roti",
                "limit_stock" => 50,
                "jenis_satuan" => "Pcs",
            ],
            [
                "kode_barang" => "KODE005",
                "nama_barang" => "Susu Cair UHT",
                "kategori" => "Bahan Roti",
                "limit_stock" => 12,
                "jenis_satuan" => "Liter",
            ],
            [
                "kode_barang" => "KODE006",
                "nama_barang" => "Kopi Arabica Robusta",
                "kategori" => "Bahan Kopi",
                "limit_stock" => 8,
                "jenis_satuan" => "Kg",
            ],
            [
                "kode_barang" => "KODE007",
                "nama_barang" => "Kopi Espresso Blend",
                "kategori" => "Bahan Kopi",
                "limit_stock" => 5,
                "jenis_satuan" => "Kg",
            ],
            [
                "kode_barang" => "KODE008",
                "nama_barang" => "Susu Kental Manis",
                "kategori" => "Bahan Kopi",
                "limit_stock" => 24,
                "jenis_satuan" => "Pcs",
            ],
            [
                "kode_barang" => "KODE009",
                "nama_barang" => "Gula Pasir untuk Kopi",
                "kategori" => "Bahan Kopi",
                "limit_stock" => 10,
                "jenis_satuan" => "Kg",
            ],
            [
                "kode_barang" => "KODE010",
                "nama_barang" => "Ragi Instan",
                "kategori" => "Bahan Roti",
                "limit_stock" => 30,
                "jenis_satuan" => "Pack",
            ],
        ];

        foreach ($barangs as $barang) {
            Barang::create([
                'kode_barang' => $barang['kode_barang'],
                'nama_barang' => $barang['nama_barang'],
                'deskripsi' => $barang['kategori'], // Simpan kategori di field deskripsi
                'satuan' => $barang['jenis_satuan'],
                'stok' => $barang['limit_stock'], // Set initial stock dari limit_stock
                'harga_beli' => 0,
                'harga_jual' => 0,
            ]);
        }

        echo "\n✅ 10 Barang dummy data berhasil dibuat!\n\n";
    }
}

