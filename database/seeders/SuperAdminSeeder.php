<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete all existing users
        User::truncate();

        // Create superadmin (Pimpinan)
        User::create([
            'name' => 'Super Admin',
            'email' => 'pimpinan@sea-bakery.com',
            'password' => Hash::make('Pimpinana123!'),
            'role' => 'superadmin',
        ]);

        echo "\n✅ Pimpinan user created successfully!\n";
        echo "Email: pimpinan@sea-bakery.com\n";
        echo "Password: Pimpinana123!\n\n";
    }
}

