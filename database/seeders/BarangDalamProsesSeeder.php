<?php

namespace Database\Seeders;

use App\Models\BarangDalamProses;
use App\Models\BarangDalamProsesDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangDalamProsesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pembelians = [
            [
                "tanggal" => "2025-11-10",
                "nomor_faktur" => "PRC-001",
                "keterangan" => "Pembelian bahan baku untuk produksi roti tawar",
                "nama_supplier" => "PT. Supplier Bahan Baku Nusantara",
                "kode_supplier" => "SUP-001",
                "daftar_barang" => [
                    [
                        "nama_barang" => "Tepung Terigu",
                        "kode_barang" => "KODE001",
                        "quantity" => 25,
                        "satuan" => "kg",
                        "harga_beli" => 8500,
                        "jumlah" => 212500
                    ],
                    [
                        "nama_barang" => "Gula Pasir",
                        "kode_barang" => "KODE002",
                        "quantity" => 15,
                        "satuan" => "kg",
                        "harga_beli" => 12000,
                        "jumlah" => 180000
                    ],
                    [
                        "nama_barang" => "Telur Ayam",
                        "kode_barang" => "KODE003",
                        "quantity" => 40,
                        "satuan" => "pcs",
                        "harga_beli" => 1500,
                        "jumlah" => 60000
                    ]
                ],
                "total_rp" => 452500
            ],
            [
                "tanggal" => "2025-11-11",
                "nomor_faktur" => "PRC-002",
                "keterangan" => "Pembelian bahan untuk produksi roti manis",
                "nama_supplier" => "PT. Supplier Bahan Baku Sejahtera",
                "kode_supplier" => "SUP-002",
                "daftar_barang" => [
                    [
                        "nama_barang" => "Mentega",
                        "kode_barang" => "KODE004",
                        "quantity" => 12,
                        "satuan" => "kg",
                        "harga_beli" => 35000,
                        "jumlah" => 420000
                    ],
                    [
                        "nama_barang" => "Susu Bubuk",
                        "kode_barang" => "KODE005",
                        "quantity" => 8,
                        "satuan" => "kg",
                        "harga_beli" => 45000,
                        "jumlah" => 360000
                    ],
                    [
                        "nama_barang" => "Vanilla",
                        "kode_barang" => "KODE009",
                        "quantity" => 500,
                        "satuan" => "gram",
                        "harga_beli" => 800,
                        "jumlah" => 400000
                    ]
                ],
                "total_rp" => 1180000
            ],
            [
                "tanggal" => "2025-11-12",
                "nomor_faktur" => "PRC-003",
                "keterangan" => "Pembelian bahan untuk produksi roti coklat",
                "nama_supplier" => "PT. Supplier Bahan Baku Makmur",
                "kode_supplier" => "SUP-003",
                "daftar_barang" => [
                    [
                        "nama_barang" => "Coklat Bubuk",
                        "kode_barang" => "KODE006",
                        "quantity" => 10,
                        "satuan" => "kg",
                        "harga_beli" => 45000,
                        "jumlah" => 450000
                    ],
                    [
                        "nama_barang" => "Tepung Terigu",
                        "kode_barang" => "KODE001",
                        "quantity" => 20,
                        "satuan" => "kg",
                        "harga_beli" => 8700,
                        "jumlah" => 174000
                    ],
                    [
                        "nama_barang" => "Gula Pasir",
                        "kode_barang" => "KODE002",
                        "quantity" => 12,
                        "satuan" => "kg",
                        "harga_beli" => 12500,
                        "jumlah" => 150000
                    ]
                ],
                "total_rp" => 774000
            ],
            [
                "tanggal" => "2025-11-13",
                "nomor_faktur" => "PRC-004",
                "keterangan" => "Pembelian bahan untuk produksi roti keju",
                "nama_supplier" => "PT. Supplier Bahan Baku Jaya",
                "kode_supplier" => "SUP-004",
                "daftar_barang" => [
                    [
                        "nama_barang" => "Keju",
                        "kode_barang" => "KODE008",
                        "quantity" => 5,
                        "satuan" => "kg",
                        "harga_beli" => 48000,
                        "jumlah" => 240000
                    ],
                    [
                        "nama_barang" => "Ragi Instan",
                        "kode_barang" => "KODE007",
                        "quantity" => 30,
                        "satuan" => "pcs",
                        "harga_beli" => 5000,
                        "jumlah" => 150000
                    ],
                    [
                        "nama_barang" => "Telur Ayam",
                        "kode_barang" => "KODE003",
                        "quantity" => 35,
                        "satuan" => "pcs",
                        "harga_beli" => 1550,
                        "jumlah" => 54250
                    ]
                ],
                "total_rp" => 444250
            ],
            [
                "tanggal" => "2025-11-14",
                "nomor_faktur" => "PRC-005",
                "keterangan" => "Pembelian bahan untuk produksi roti tawar premium",
                "nama_supplier" => "PT. Supplier Bahan Baku Utama",
                "kode_supplier" => "SUP-005",
                "daftar_barang" => [
                    [
                        "nama_barang" => "Tepung Terigu",
                        "kode_barang" => "KODE001",
                        "quantity" => 30,
                        "satuan" => "kg",
                        "harga_beli" => 9000,
                        "jumlah" => 270000
                    ],
                    [
                        "nama_barang" => "Garam",
                        "kode_barang" => "KODE010",
                        "quantity" => 3,
                        "satuan" => "kg",
                        "harga_beli" => 5000,
                        "jumlah" => 15000
                    ],
                    [
                        "nama_barang" => "Ragi Instan",
                        "kode_barang" => "KODE007",
                        "quantity" => 25,
                        "satuan" => "pcs",
                        "harga_beli" => 5200,
                        "jumlah" => 130000
                    ]
                ],
                "total_rp" => 415000
            ],
            [
                "tanggal" => "2025-11-15",
                "nomor_faktur" => "PRC-006",
                "keterangan" => "Pembelian bahan untuk produksi roti manis spesial",
                "nama_supplier" => "PT. Supplier Bahan Baku Indah",
                "kode_supplier" => "SUP-006",
                "daftar_barang" => [
                    [
                        "nama_barang" => "Mentega",
                        "kode_barang" => "KODE004",
                        "quantity" => 18,
                        "satuan" => "kg",
                        "harga_beli" => 36000,
                        "jumlah" => 648000
                    ],
                    [
                        "nama_barang" => "Susu Bubuk",
                        "kode_barang" => "KODE005",
                        "quantity" => 10,
                        "satuan" => "kg",
                        "harga_beli" => 47000,
                        "jumlah" => 470000
                    ],
                    [
                        "nama_barang" => "Vanilla",
                        "kode_barang" => "KODE009",
                        "quantity" => 600,
                        "satuan" => "gram",
                        "harga_beli" => 850,
                        "jumlah" => 510000
                    ],
                    [
                        "nama_barang" => "Gula Pasir",
                        "kode_barang" => "KODE002",
                        "quantity" => 18,
                        "satuan" => "kg",
                        "harga_beli" => 13000,
                        "jumlah" => 234000
                    ]
                ],
                "total_rp" => 1862000
            ],
            [
                "tanggal" => "2025-11-16",
                "nomor_faktur" => "PRC-007",
                "keterangan" => "Pembelian bahan untuk produksi roti coklat premium",
                "nama_supplier" => "PT. Supplier Bahan Baku Prima",
                "kode_supplier" => "SUP-007",
                "daftar_barang" => [
                    [
                        "nama_barang" => "Coklat Bubuk",
                        "kode_barang" => "KODE006",
                        "quantity" => 15,
                        "satuan" => "kg",
                        "harga_beli" => 48000,
                        "jumlah" => 720000
                    ],
                    [
                        "nama_barang" => "Tepung Terigu",
                        "kode_barang" => "KODE001",
                        "quantity" => 22,
                        "satuan" => "kg",
                        "harga_beli" => 8800,
                        "jumlah" => 193600
                    ]
                ],
                "total_rp" => 913600
            ],
            [
                "tanggal" => "2025-11-17",
                "nomor_faktur" => "PRC-008",
                "keterangan" => "Pembelian bahan untuk produksi roti keju spesial",
                "nama_supplier" => "PT. Supplier Bahan Baku Sentosa",
                "kode_supplier" => "SUP-008",
                "daftar_barang" => [
                    [
                        "nama_barang" => "Keju",
                        "kode_barang" => "KODE008",
                        "quantity" => 8,
                        "satuan" => "kg",
                        "harga_beli" => 50000,
                        "jumlah" => 400000
                    ],
                    [
                        "nama_barang" => "Telur Ayam",
                        "kode_barang" => "KODE003",
                        "quantity" => 45,
                        "satuan" => "pcs",
                        "harga_beli" => 1600,
                        "jumlah" => 72000
                    ],
                    [
                        "nama_barang" => "Ragi Instan",
                        "kode_barang" => "KODE007",
                        "quantity" => 35,
                        "satuan" => "pcs",
                        "harga_beli" => 5100,
                        "jumlah" => 178500
                    ]
                ],
                "total_rp" => 650500
            ],
            [
                "tanggal" => "2025-11-18",
                "nomor_faktur" => "PRC-009",
                "keterangan" => "Pembelian bahan untuk produksi roti tawar standar",
                "nama_supplier" => "PT. Supplier Bahan Baku Mandiri",
                "kode_supplier" => "SUP-009",
                "daftar_barang" => [
                    [
                        "nama_barang" => "Tepung Terigu",
                        "kode_barang" => "KODE001",
                        "quantity" => 28,
                        "satuan" => "kg",
                        "harga_beli" => 8600,
                        "jumlah" => 240800
                    ],
                    [
                        "nama_barang" => "Gula Pasir",
                        "kode_barang" => "KODE002",
                        "quantity" => 14,
                        "satuan" => "kg",
                        "harga_beli" => 11800,
                        "jumlah" => 165200
                    ],
                    [
                        "nama_barang" => "Garam",
                        "kode_barang" => "KODE010",
                        "quantity" => 2,
                        "satuan" => "kg",
                        "harga_beli" => 4800,
                        "jumlah" => 9600
                    ],
                    [
                        "nama_barang" => "Ragi Instan",
                        "kode_barang" => "KODE007",
                        "quantity" => 20,
                        "satuan" => "pcs",
                        "harga_beli" => 4900,
                        "jumlah" => 98000
                    ]
                ],
                "total_rp" => 603600
            ],
            [
                "tanggal" => "2025-11-19",
                "nomor_faktur" => "PRC-010",
                "keterangan" => "Pembelian bahan untuk produksi roti manis variasi",
                "nama_supplier" => "PT. Supplier Bahan Baku Bersama",
                "kode_supplier" => "SUP-010",
                "daftar_barang" => [
                    [
                        "nama_barang" => "Mentega",
                        "kode_barang" => "KODE004",
                        "quantity" => 15,
                        "satuan" => "kg",
                        "harga_beli" => 37000,
                        "jumlah" => 555000
                    ],
                    [
                        "nama_barang" => "Susu Bubuk",
                        "kode_barang" => "KODE005",
                        "quantity" => 9,
                        "satuan" => "kg",
                        "harga_beli" => 46000,
                        "jumlah" => 414000
                    ],
                    [
                        "nama_barang" => "Vanilla",
                        "kode_barang" => "KODE009",
                        "quantity" => 550,
                        "satuan" => "gram",
                        "harga_beli" => 750,
                        "jumlah" => 412500
                    ],
                    [
                        "nama_barang" => "Telur Ayam",
                        "kode_barang" => "KODE003",
                        "quantity" => 38,
                        "satuan" => "pcs",
                        "harga_beli" => 1520,
                        "jumlah" => 57760
                    ]
                ],
                "total_rp" => 1439260
            ],
        ];

        DB::beginTransaction();
        try {
            foreach ($pembelians as $pembelianData) {
                // Cek apakah nomor_faktur sudah ada
                $existing = BarangDalamProses::where('nomor_faktur', $pembelianData['nomor_faktur'])->first();
                if ($existing) {
                    echo "⚠️  Nomor faktur {$pembelianData['nomor_faktur']} sudah ada, skip...\n";
                    continue;
                }
                
                // Buat header barang dalam proses
                $barangDalamProses = BarangDalamProses::create([
                    'tanggal' => $pembelianData['tanggal'],
                    'nomor_faktur' => $pembelianData['nomor_faktur'],
                    'keterangan' => $pembelianData['keterangan'],
                    'nama_supplier' => $pembelianData['nama_supplier'],
                    'kode_supplier' => $pembelianData['kode_supplier'],
                    'total_harga' => $pembelianData['total_rp'],
                ]);

                // Buat detail barang dalam proses
                foreach ($pembelianData['daftar_barang'] as $barangData) {
                    BarangDalamProsesDetail::create([
                        'barang_dalam_proses_id' => $barangDalamProses->id,
                        'barang_nama' => $barangData['nama_barang'],
                        'barang_kode' => $barangData['kode_barang'],
                        'barang_qty' => $barangData['quantity'],
                        'barang_satuan' => $barangData['satuan'],
                        'barang_harga' => $barangData['harga_beli'],
                        'barang_jumlah' => $barangData['jumlah'],
                    ]);
                }
            }

            DB::commit();
            echo "\n✅ 10 Barang Dalam Proses dummy data berhasil dibuat!\n";
            echo "Total transaksi: " . count($pembelians) . " pembelian\n\n";
        } catch (\Exception $e) {
            DB::rollBack();
            echo "\n❌ Error: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
}

