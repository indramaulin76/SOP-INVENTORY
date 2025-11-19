<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                "kode_supplier" => "SUP001",
                "nama_supplier" => "PT. Sumber Pangan Jaya",
                "alamat" => "Jl. Merdeka No. 12, RT 01/RW 03, Bandung 40111",
                "telepon" => "081234567890",
                "email" => "info@sumberpanganjaya.com",
                "nama_pemilik" => "Budi Hartono",
                "keterangan" => "Supplier bahan baku tepung terigu dan gula",
            ],
            [
                "kode_supplier" => "SUP002",
                "nama_supplier" => "CV. Makmur Sejahtera",
                "alamat" => "Jl. Gatot Subroto No. 88, Jakarta Selatan 12930",
                "telepon" => "082345678901",
                "email" => "contact@makmursejahtera.co.id",
                "nama_pemilik" => "Siti Rahayu",
                "keterangan" => "Supplier telur dan susu segar",
            ],
            [
                "kode_supplier" => "SUP003",
                "nama_supplier" => "PT. Bahan Baku Nusantara",
                "alamat" => "Jl. Ahmad Yani No. 234, Surabaya 60231",
                "telepon" => "083456789012",
                "email" => "sales@bahanbakunusantara.com",
                "nama_pemilik" => "Ahmad Fauzi",
                "keterangan" => "Supplier mentega, margarin, dan minyak goreng",
            ],
            [
                "kode_supplier" => "SUP004",
                "nama_supplier" => "UD. Tani Makmur",
                "alamat" => "Jl. Raya Bogor KM 25, Cibinong, Bogor 16913",
                "telepon" => "084567890123",
                "email" => "udtanimakmur@gmail.com",
                "nama_pemilik" => "Dedi Kurniawan",
                "keterangan" => "Supplier bahan tambahan dan perisa makanan",
            ],
            [
                "kode_supplier" => "SUP005",
                "nama_supplier" => "PT. Kemasan Indah",
                "alamat" => "Jl. Industri Raya No. 45, Tangerang 15320",
                "telepon" => "085678901234",
                "email" => "info@kemasanindah.com",
                "nama_pemilik" => "Rina Wati",
                "keterangan" => "Supplier kemasan plastik dan kertas untuk produk bakery",
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }

        echo "\n✅ 5 Supplier dummy data berhasil dibuat!\n\n";
    }
}

