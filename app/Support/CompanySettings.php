<?php

namespace App\Support;

class CompanySettings
{
    public static function get(string $key, mixed $default = null): mixed
    {
        $company = current_company();

        if ($company) {
            $fromSettings = $company->setting($key);
            if ($fromSettings !== null) {
                return $fromSettings;
            }
        }

        return config('company.'.$key, $default);
    }
}
