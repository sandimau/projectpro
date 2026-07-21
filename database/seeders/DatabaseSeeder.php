<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(PermissionSeeder::class);
        $this->call(RolePermissionSeeder::class);

        // Admin & marketplace format butuh company (dibuat migrasi / company:create)
        if (\App\Models\Company::query()->exists()) {
            $this->call(AdminSeeder::class);
            $this->call(MarketplaceFormatSeeder::class);
        }
    }
}
