<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 2 Karyawan (Employee) users
        $karyawan1 = User::create([
            'name' => 'Karyawan Satu',
            'email' => 'karyawan1@sae-bakery.com',
            'password' => Hash::make('Karyawan123!'),
            'role' => 'employee',
        ]);

        $karyawan2 = User::create([
            'name' => 'Karyawan Dua',
            'email' => 'karyawan2@sae-bakery.com',
            'password' => Hash::make('Karyawan123!'),
            'role' => 'employee',
        ]);

        // Create 1 Admin user
        $admin = User::create([
            'name' => 'Admin Sae Bakery',
            'email' => 'admin@sae-bakery.com',
            'password' => Hash::make('Admin123!'),
            'role' => 'admin',
        ]);

        echo "\n✅ User berhasil dibuat:\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "KARYAWAN 1:\n";
        echo "  Email: {$karyawan1->email}\n";
        echo "  Password: Karyawan123!\n";
        echo "  Role: {$karyawan1->role}\n\n";
        
        echo "KARYAWAN 2:\n";
        echo "  Email: {$karyawan2->email}\n";
        echo "  Password: Karyawan123!\n";
        echo "  Role: {$karyawan2->role}\n\n";
        
        echo "ADMIN:\n";
        echo "  Email: {$admin->email}\n";
        echo "  Password: Admin123!\n";
        echo "  Role: {$admin->role}\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    }
}

