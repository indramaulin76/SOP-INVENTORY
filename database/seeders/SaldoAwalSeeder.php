<?php

namespace Database\Seeders;

use App\Models\Barang;
use Illuminate\Database\Seeder;

class SaldoAwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $saldoAwal = [
            [
                "kode_barang" => "KODE001",
                "nama_barang" => "Tepung Terigu Premium",
                "kategori" => "Bahan Roti",
                "jumlah_unit" => 25,
                "jenis_satuan" => "Kg",
                "harga_beli" => 8500,
                "jumlah_rp" => 212500,
            ],
            [
                "kode_barang" => "KODE002",
                "nama_barang" => "Gula Pasir Putih",
                "kategori" => "Bahan Roti",
                "jumlah_unit" => 20,
                "jenis_satuan" => "Kg",
                "harga_beli" => 12000,
                "jumlah_rp" => 240000,
            ],
            [
                "kode_barang" => "KODE003",
                "nama_barang" => "Mentega Tawar",
                "kategori" => "Bahan Roti",
                "jumlah_unit" => 15,
                "jenis_satuan" => "Kg",
                "harga_beli" => 35000,
                "jumlah_rp" => 525000,
            ],
            [
                "kode_barang" => "KODE004",
                "nama_barang" => "Telur Ayam Segar",
                "kategori" => "Bahan Roti",
                "jumlah_unit" => 60,
                "jenis_satuan" => "Pcs",
                "harga_beli" => 1500,
                "jumlah_rp" => 90000,
            ],
            [
                "kode_barang" => "KODE005",
                "nama_barang" => "Susu Cair UHT",
                "kategori" => "Bahan Roti",
                "jumlah_unit" => 18,
                "jenis_satuan" => "Liter",
                "harga_beli" => 15000,
                "jumlah_rp" => 270000,
            ],
            [
                "kode_barang" => "KODE006",
                "nama_barang" => "Kopi Arabica Robusta",
                "kategori" => "Bahan Kopi",
                "jumlah_unit" => 10,
                "jenis_satuan" => "Kg",
                "harga_beli" => 95000,
                "jumlah_rp" => 950000,
            ],
            [
                "kode_barang" => "KODE007",
                "nama_barang" => "Kopi Espresso Blend",
                "kategori" => "Bahan Kopi",
                "jumlah_unit" => 8,
                "jenis_satuan" => "Kg",
                "harga_beli" => 120000,
                "jumlah_rp" => 960000,
            ],
            [
                "kode_barang" => "KODE008",
                "nama_barang" => "Susu Kental Manis",
                "kategori" => "Bahan Kopi",
                "jumlah_unit" => 30,
                "jenis_satuan" => "Pcs",
                "harga_beli" => 8500,
                "jumlah_rp" => 255000,
            ],
            [
                "kode_barang" => "KODE009",
                "nama_barang" => "Gula Pasir untuk Kopi",
                "kategori" => "Bahan Kopi",
                "jumlah_unit" => 12,
                "jenis_satuan" => "Kg",
                "harga_beli" => 12000,
                "jumlah_rp" => 144000,
            ],
            [
                "kode_barang" => "KODE010",
                "nama_barang" => "Ragi Instan",
                "kategori" => "Bahan Roti",
                "jumlah_unit" => 40,
                "jenis_satuan" => "Pack",
                "harga_beli" => 5000,
                "jumlah_rp" => 200000,
            ],
        ];

        foreach ($saldoAwal as $item) {
            // Cari atau buat barang
            $barang = Barang::where('kode_barang', $item['kode_barang'])->first();
            
            if ($barang) {
                // Update barang yang sudah ada dengan saldo awal
                $barang->update([
                    'stok' => $item['jumlah_unit'],
                    'harga_beli' => $item['harga_beli'],
                ]);
            } else {
                // Buat barang baru jika belum ada
                Barang::create([
                    'kode_barang' => $item['kode_barang'],
                    'nama_barang' => $item['nama_barang'],
                    'deskripsi' => $item['kategori'],
                    'satuan' => $item['jenis_satuan'],
                    'stok' => $item['jumlah_unit'],
                    'harga_beli' => $item['harga_beli'],
                    'harga_jual' => 0,
                ]);
            }
        }

        echo "\n✅ 10 Saldo Awal dummy data berhasil dibuat!\n";
        echo "Total nilai saldo awal: Rp " . number_format(array_sum(array_column($saldoAwal, 'jumlah_rp')), 0, ',', '.') . "\n\n";
    }
}

