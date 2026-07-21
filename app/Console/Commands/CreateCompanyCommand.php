<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Services\CompanyProvisioner;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateCompanyCommand extends Command
{
    protected $signature = 'company:create
                            {name : Nama perusahaan}
                            {slug : Subdomain (huruf kecil, angka, strip)}
                            {--email= : Email admin awal}
                            {--password= : Password admin awal}
                            {--admin-name=Admin : Nama admin awal}';

    protected $description = 'Buat company baru + admin awal + seed sistems & marketplace formats';

    public function handle(CompanyProvisioner $provisioner): int
    {
        $name = $this->argument('name');
        $slug = Str::slug($this->argument('slug'));

        if ($slug === '') {
            $this->error('Slug tidak valid.');

            return self::FAILURE;
        }

        if (Company::where('slug', $slug)->exists()) {
            $this->error("Company dengan slug '{$slug}' sudah ada.");

            return self::FAILURE;
        }

        $result = $provisioner->create([
            'name' => $name,
            'slug' => $slug,
            'admin_name' => $this->option('admin-name') ?: 'Admin',
            'admin_email' => $this->option('email') ?: "admin@{$slug}.local",
            'admin_password' => $this->option('password') ?: 'password',
        ]);

        $this->info("Company dibuat: {$result['company']->name} ({$result['company']->slug})");
        if ($result['admin']) {
            $this->line("Admin: {$result['admin']->email} / {$result['password']}");
        }
        $this->line('URL: https://'.$result['company']->slug.'.'.(config('tenancy.central_domains')[0] ?? 'projectpro.com'));

        return self::SUCCESS;
    }
}
