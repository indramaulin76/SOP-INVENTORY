<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                "kode_customer" => "CUST001",
                "nama_customer" => "Budi Santoso",
                "alamat" => "Jl. Sudirman No. 45, RT 05/RW 02, Jakarta Pusat 10220",
                "telepon" => "081234567890",
                "email" => "budi.santoso@gmail.com",
                "tipe_customer" => "Retail",
                "keterangan" => "Pelanggan rutin, pembelian setiap minggu",
            ],
            [
                "kode_customer" => "CUST002",
                "nama_customer" => "Sari Indah Bakery",
                "alamat" => "Jl. Gatot Subroto No. 128, Bandung 40124",
                "telepon" => "082345678901",
                "email" => "sariindah.bakery@yahoo.com",
                "tipe_customer" => "Grosir",
                "keterangan" => "Toko roti, pembelian dalam jumlah besar",
            ],
            [
                "kode_customer" => "CUST003",
                "nama_customer" => "PT. Makmur Jaya Abadi",
                "alamat" => "Jl. HR Rasuna Said Kav. C-22, Jakarta Selatan 12940",
                "telepon" => "083456789012",
                "email" => "info@makmurjaya.co.id",
                "tipe_customer" => "Corporate",
                "keterangan" => "Perusahaan korporat, kontrak bulanan",
            ],
            [
                "kode_customer" => "CUST004",
                "nama_customer" => "Ibu Siti Nurhaliza",
                "alamat" => "Jl. Merdeka No. 78, Surabaya 60264",
                "telepon" => "084567890123",
                "email" => "siti.nurhaliza@gmail.com",
                "tipe_customer" => "Retail",
                "keterangan" => "Pelanggan individu, suka roti manis",
            ],
            [
                "kode_customer" => "CUST005",
                "nama_customer" => "Toko Roti Segar Mandiri",
                "alamat" => "Jl. Ahmad Yani No. 156, Yogyakarta 55231",
                "telepon" => "085678901234",
                "email" => "rotisegar.mandiri@gmail.com",
                "tipe_customer" => "Grosir",
                "keterangan" => "Distributor roti ke berbagai toko",
            ],
            [
                "kode_customer" => "CUST006",
                "nama_customer" => "CV. Sumber Rezeki",
                "alamat" => "Jl. Diponegoro No. 89, Semarang 50241",
                "telepon" => "086789012345",
                "email" => "sumber.rezeki@cv.co.id",
                "tipe_customer" => "Corporate",
                "keterangan" => "Perusahaan catering, order rutin",
            ],
            [
                "kode_customer" => "CUST007",
                "nama_customer" => "Ahmad Fauzi",
                "alamat" => "Jl. Veteran No. 23, RT 03/RW 01, Malang 65111",
                "telepon" => "087890123456",
                "email" => "ahmad.fauzi@outlook.com",
                "tipe_customer" => "Retail",
                "keterangan" => "Pelanggan baru, pembelian mingguan",
            ],
            [
                "kode_customer" => "CUST008",
                "nama_customer" => "Warung Makan Sederhana",
                "alamat" => "Jl. Raya Bogor KM 30, Depok 16431",
                "telepon" => "088901234567",
                "email" => "warungsederhana@gmail.com",
                "tipe_customer" => "Grosir",
                "keterangan" => "Warung makan, pembelian harian",
            ],
            [
                "kode_customer" => "CUST009",
                "nama_customer" => "PT. Indah Sejahtera",
                "alamat" => "Jl. Thamrin No. 1, Jakarta Pusat 10310",
                "telepon" => "089012345678",
                "email" => "contact@indahsejahtera.com",
                "tipe_customer" => "Corporate",
                "keterangan" => "Perusahaan besar, kontrak tahunan",
            ],
            [
                "kode_customer" => "CUST010",
                "nama_customer" => "Dewi Lestari",
                "alamat" => "Jl. Cikapundung No. 42, Bandung 40115",
                "telepon" => "081123456789",
                "email" => "dewi.lestari@gmail.com",
                "tipe_customer" => "Retail",
                "keterangan" => "Pelanggan setia, suka produk premium",
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }

        echo "\n✅ 10 Customer dummy data berhasil dibuat!\n\n";
    }
}

