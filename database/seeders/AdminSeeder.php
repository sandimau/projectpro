<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $company = current_company();

        if (! $company) {
            $company = \App\Models\Company::query()
                ->where('slug', config('tenancy.default_company_slug', 'default'))
                ->first()
                ?? \App\Models\Company::query()->orderBy('id')->first();
        }

        if (! $company) {
            $this->command?->warn('AdminSeeder dilewati: belum ada company. Jalankan company:create atau migrasi dulu.');

            return;
        }

        Tenant::runFor($company, function () use ($company) {
            $user = User::firstOrCreate(
                [
                    'email' => 'super@souvenirbag.net',
                    'company_id' => $company->id,
                ],
                [
                    'name' => 'super',
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                ]
            );

            $user->assignRole('super');
        });
    }
}
