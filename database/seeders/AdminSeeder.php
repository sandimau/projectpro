<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'super@souvenirbag.net'],
            [
                'name' => 'super',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );

        $user->assignRole('super');
    }
}
