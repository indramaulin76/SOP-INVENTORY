<?php

namespace Database\Seeders;

use App\Models\PembelianBahanBaku;
use App\Models\PembelianBahanBakuDetail;
use App\Models\Supplier;
use App\Models\Barang;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PembelianBahanBakuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pembelians = [
            [
                "tanggal" => "2025-11-10",
                "nomor_faktur" => "INV-001",
                "keterangan" => "Pembelian rutin bahan baku roti mingguan",
                "kode_supplier" => "SUP001",
                "nama_supplier" => "PT. Sumber Pangan Jaya",
                "barang" => [
                    [
                        "kode_barang" => "KODE001",
                        "nama_barang" => "Tepung Terigu Premium",
                        "quantity" => 25,
                        "satuan" => "Kg",
                        "harga_beli" => 8500,
                        "jumlah" => 212500
                    ],
                    [
                        "kode_barang" => "KODE002",
                        "nama_barang" => "Gula Pasir Putih",
                        "quantity" => 20,
                        "satuan" => "Kg",
                        "harga_beli" => 12000,
                        "jumlah" => 240000
                    ],
                    [
                        "kode_barang" => "KODE003",
                        "nama_barang" => "Mentega Tawar",
                        "quantity" => 15,
                        "satuan" => "Kg",
                        "harga_beli" => 35000,
                        "jumlah" => 525000
                    ],
                    [
                        "kode_barang" => "KODE004",
                        "nama_barang" => "Telur Ayam Segar",
                        "quantity" => 60,
                        "satuan" => "Pcs",
                        "harga_beli" => 1500,
                        "jumlah" => 90000
                    ]
                ],
                "total_rp" => 1067500
            ],
            [
                "tanggal" => "2025-11-12",
                "nomor_faktur" => "INV-002",
                "keterangan" => "Pembelian bahan baku untuk produksi roti spesial",
                "kode_supplier" => "SUP002",
                "nama_supplier" => "CV. Makmur Sejahtera",
                "barang" => [
                    [
                        "kode_barang" => "KODE005",
                        "nama_barang" => "Susu Cair UHT",
                        "quantity" => 18,
                        "satuan" => "Liter",
                        "harga_beli" => 15000,
                        "jumlah" => 270000
                    ],
                    [
                        "kode_barang" => "KODE002",
                        "nama_barang" => "Gula Pasir Putih",
                        "quantity" => 10,
                        "satuan" => "Kg",
                        "harga_beli" => 12000,
                        "jumlah" => 120000
                    ],
                    [
                        "kode_barang" => "KODE010",
                        "nama_barang" => "Ragi Instan",
                        "quantity" => 12,
                        "satuan" => "Pack",
                        "harga_beli" => 5000,
                        "jumlah" => 60000
                    ]
                ],
                "total_rp" => 450000
            ],
            [
                "tanggal" => "2025-11-13",
                "nomor_faktur" => "INV-003",
                "keterangan" => "Pembelian bahan kopi untuk stok bulanan",
                "kode_supplier" => "SUP003",
                "nama_supplier" => "PT. Bahan Baku Nusantara",
                "barang" => [
                    [
                        "kode_barang" => "KODE006",
                        "nama_barang" => "Kopi Arabica Robusta",
                        "quantity" => 10,
                        "satuan" => "Kg",
                        "harga_beli" => 95000,
                        "jumlah" => 950000
                    ],
                    [
                        "kode_barang" => "KODE007",
                        "nama_barang" => "Kopi Espresso Blend",
                        "quantity" => 8,
                        "satuan" => "Kg",
                        "harga_beli" => 120000,
                        "jumlah" => 960000
                    ],
                    [
                        "kode_barang" => "KODE008",
                        "nama_barang" => "Susu Kental Manis",
                        "quantity" => 30,
                        "satuan" => "Pcs",
                        "harga_beli" => 8500,
                        "jumlah" => 255000
                    ],
                    [
                        "kode_barang" => "KODE009",
                        "nama_barang" => "Gula Pasir untuk Kopi",
                        "quantity" => 12,
                        "satuan" => "Kg",
                        "harga_beli" => 12000,
                        "jumlah" => 144000
                    ]
                ],
                "total_rp" => 2309000
            ],
            [
                "tanggal" => "2025-11-14",
                "nomor_faktur" => "INV-004",
                "keterangan" => "Pembelian kopi premium dan bahan pendukung",
                "kode_supplier" => "SUP004",
                "nama_supplier" => "UD. Tani Makmur",
                "barang" => [
                    [
                        "kode_barang" => "KODE006",
                        "nama_barang" => "Kopi Arabica Robusta",
                        "quantity" => 12,
                        "satuan" => "Kg",
                        "harga_beli" => 98000,
                        "jumlah" => 1176000
                    ],
                    [
                        "kode_barang" => "KODE008",
                        "nama_barang" => "Susu Kental Manis",
                        "quantity" => 25,
                        "satuan" => "Pcs",
                        "harga_beli" => 8600,
                        "jumlah" => 215000
                    ],
                    [
                        "kode_barang" => "KODE009",
                        "nama_barang" => "Gula Pasir untuk Kopi",
                        "quantity" => 15,
                        "satuan" => "Kg",
                        "harga_beli" => 12500,
                        "jumlah" => 187500
                    ]
                ],
                "total_rp" => 1578500
            ],
            [
                "tanggal" => "2025-11-15",
                "nomor_faktur" => "INV-005",
                "keterangan" => "Pembelian bahan baku lengkap untuk produksi",
                "kode_supplier" => "SUP005",
                "nama_supplier" => "PT. Kemasan Indah",
                "barang" => [
                    [
                        "kode_barang" => "KODE001",
                        "nama_barang" => "Tepung Terigu Premium",
                        "quantity" => 30,
                        "satuan" => "Kg",
                        "harga_beli" => 8700,
                        "jumlah" => 261000
                    ],
                    [
                        "kode_barang" => "KODE003",
                        "nama_barang" => "Mentega Tawar",
                        "quantity" => 12,
                        "satuan" => "Kg",
                        "harga_beli" => 36000,
                        "jumlah" => 432000
                    ],
                    [
                        "kode_barang" => "KODE004",
                        "nama_barang" => "Telur Ayam Segar",
                        "quantity" => 50,
                        "satuan" => "Pcs",
                        "harga_beli" => 1550,
                        "jumlah" => 77500
                    ],
                    [
                        "kode_barang" => "KODE005",
                        "nama_barang" => "Susu Cair UHT",
                        "quantity" => 20,
                        "satuan" => "Liter",
                        "harga_beli" => 15200,
                        "jumlah" => 304000
                    ]
                ],
                "total_rp" => 1074500
            ],
        ];

        DB::beginTransaction();
        try {
            foreach ($pembelians as $pembelianData) {
                // Cari supplier berdasarkan kode_supplier
                $supplier = Supplier::where('kode_supplier', $pembelianData['kode_supplier'])->first();
                
                if (!$supplier) {
                    echo "⚠️  Supplier {$pembelianData['kode_supplier']} tidak ditemukan, skip pembelian {$pembelianData['nomor_faktur']}\n";
                    continue;
                }

                // Buat header pembelian
                $pembelian = PembelianBahanBaku::create([
                    'tanggal' => $pembelianData['tanggal'],
                    'nomor_faktur' => $pembelianData['nomor_faktur'],
                    'keterangan' => $pembelianData['keterangan'],
                    'supplier_id' => $supplier->id,
                    'total_harga' => $pembelianData['total_rp'],
                ]);

                // Buat detail pembelian dan update stock
                foreach ($pembelianData['barang'] as $barangData) {
                    // Cari barang berdasarkan kode_barang
                    $barang = Barang::where('kode_barang', $barangData['kode_barang'])->first();
                    
                    if (!$barang) {
                        echo "⚠️  Barang {$barangData['kode_barang']} tidak ditemukan, skip item\n";
                        continue;
                    }

                    // Buat detail pembelian
                    PembelianBahanBakuDetail::create([
                        'pembelian_bahan_baku_id' => $pembelian->id,
                        'barang_id' => $barang->id,
                        'quantity' => $barangData['quantity'],
                        'satuan' => $barangData['satuan'],
                        'harga_beli' => $barangData['harga_beli'],
                        'jumlah' => $barangData['jumlah'],
                    ]);

                    // Update stock barang (tambah stok)
                    $barang->addStock((int) round($barangData['quantity']));
                    
                    // Update harga beli jika lebih baru
                    if ($barangData['harga_beli'] > 0) {
                        $barang->update(['harga_beli' => $barangData['harga_beli']]);
                    }
                }
            }

            DB::commit();
            echo "\n✅ 5 Pembelian Bahan Baku dummy data berhasil dibuat!\n";
            echo "Total pembelian: " . count($pembelians) . " transaksi\n\n";
        } catch (\Exception $e) {
            DB::rollBack();
            echo "\n❌ Error: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
}

