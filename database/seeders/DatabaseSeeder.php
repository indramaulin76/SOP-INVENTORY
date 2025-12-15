<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $this->call(SuperAdminSeeder::class);
        $this->call(UserSeeder::class); // Use the new consolidated seeder
        // $this->call(StockOpnameSeeder::class); // Disabled for QC: System must start clean
    }
}
