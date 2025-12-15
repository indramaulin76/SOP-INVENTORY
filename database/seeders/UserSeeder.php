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
        // 1. Clean Users Table
        User::truncate();

        // 2. Create Superadmin (Pimpinan)
        $superadmin = User::create([
            'name' => 'Super Admin',
            'email' => 'pimpinan@sea-bakery.com',
            'password' => Hash::make('Pimpinana123!'),
            'role' => 'superadmin',
        ]);

        // 3. Create Admin
        $admin = User::create([
            'name' => 'Admin Staff',
            'email' => 'admin@sea-bakery.com',
            'password' => Hash::make('Admin123!'),
            'role' => 'admin',
        ]);

        // 4. Create Employee (Karyawan)
        $karyawan = User::create([
            'name' => 'Karyawan Staff',
            'email' => 'karyawan@sea-bakery.com',
            'password' => Hash::make('Karyawan123!'),
            'role' => 'employee',
        ]);

        echo "\n✅ SYSTEM USERS GENERATED SUCCESSFULLY\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "SUPERADMIN (Pimpinan):\n";
        echo "  Email:    {$superadmin->email}\n";
        echo "  Password: Pimpinana123!\n";
        echo "  Role:     {$superadmin->role}\n";
        echo "----------------------------------------------------\n";
        echo "ADMIN:\n";
        echo "  Email:    {$admin->email}\n";
        echo "  Password: Admin123!\n";
        echo "  Role:     {$admin->role}\n";
        echo "----------------------------------------------------\n";
        echo "KARYAWAN (Employee):\n";
        echo "  Email:    {$karyawan->email}\n";
        echo "  Password: Karyawan123!\n";
        echo "  Role:     {$karyawan->role}\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    }
}
