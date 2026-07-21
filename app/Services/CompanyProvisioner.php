<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Sistem;
use App\Models\User;
use App\Support\Tenant;
use Database\Seeders\MarketplaceFormatSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CompanyProvisioner
{
    /**
     * @param  array{name: string, slug: string, is_active?: bool, settings?: array|null, admin_name?: string, admin_email?: string, admin_password?: string}  $data
     * @return array{company: Company, admin: User|null, password: string|null}
     */
    public function create(array $data): array
    {
        $slug = Str::slug($data['slug']);

        $settings = $data['settings'] ?? [
            'office_latitude' => config('company.office_latitude'),
            'office_longitude' => config('company.office_longitude'),
            'max_distance_radius' => config('company.max_distance_radius'),
            'clock_in_time' => config('company.clock_in_time'),
            'clock_out_time' => config('company.clock_out_time'),
            'late_tolerance_minutes' => config('company.late_tolerance_minutes'),
            'fonnte_token' => config('company.fonnte_token'),
            'whatsapp_group_target' => config('company.whatsapp_group_target'),
            'qr_code_secret' => config('company.qr_code_secret'),
        ];

        $company = Company::create([
            'name' => $data['name'],
            'slug' => $slug,
            'is_active' => $data['is_active'] ?? true,
            'settings' => $settings,
        ]);

        $admin = null;
        $plainPassword = $data['admin_password'] ?? 'password';
        $email = $data['admin_email'] ?? "admin@{$slug}.local";
        $adminName = $data['admin_name'] ?? 'Admin';

        Tenant::runFor($company, function () use ($company, $email, $plainPassword, $adminName, &$admin) {
            $admin = User::create([
                'name' => $adminName,
                'email' => $email,
                'password' => Hash::make($plainPassword),
                'email_verified_at' => now(),
                'company_id' => $company->id,
            ]);

            try {
                $admin->assignRole('super');
            } catch (\Throwable $e) {
                // role belum di-seed
            }

            foreach ([
                ['nama' => 'nama', 'isi' => $company->name, 'type' => 'text'],
                ['nama' => 'logo', 'isi' => '', 'type' => 'file'],
            ] as $row) {
                Sistem::firstOrCreate(
                    ['nama' => $row['nama'], 'company_id' => $company->id],
                    ['isi' => $row['isi'], 'type' => $row['type']]
                );
            }

            (new MarketplaceFormatSeeder())->seedForCompany($company->id);
        });

        return [
            'company' => $company,
            'admin' => $admin,
            'password' => $plainPassword,
        ];
    }
}
