<?php

namespace App\Support;

use App\Models\Company;
use Closure;

class Tenant
{
    public static function set(?Company $company): void
    {
        if ($company) {
            app()->instance('currentCompany', $company);
        } else {
            app()->forgetInstance('currentCompany');
        }
    }

    public static function get(): ?Company
    {
        if (! app()->bound('currentCompany')) {
            return null;
        }

        return app('currentCompany');
    }

    public static function id(): ?int
    {
        return static::get()?->id;
    }

    public static function check(): bool
    {
        return static::get() !== null;
    }

    /**
     * Jalankan callback untuk setiap company aktif (cron / artisan).
     */
    public static function runForEach(Closure $callback): void
    {
        $previous = static::get();

        Company::query()->active()->orderBy('id')->each(function (Company $company) use ($callback) {
            static::set($company);
            $callback($company);
        });

        static::set($previous);
    }

    public static function runFor(Company $company, Closure $callback): mixed
    {
        $previous = static::get();
        static::set($company);

        try {
            return $callback($company);
        } finally {
            static::set($previous);
        }
    }
}
