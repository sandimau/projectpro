<?php

namespace Database\Seeders;

use App\Auth\Permissions;
use App\Auth\RoleDefinitions;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (RoleDefinitions::roleNames() as $name) {
            Role::findOrCreate($name, Permissions::GUARD);
        }
    }
}