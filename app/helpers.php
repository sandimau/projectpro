<?php

use App\Models\Company;
use App\Support\Tenant;

if (! function_exists('current_company')) {
    function current_company(): ?Company
    {
        return Tenant::get();
    }
}

if (! function_exists('current_company_id')) {
    function current_company_id(): ?int
    {
        return Tenant::id();
    }
}

if (! function_exists('company_setting')) {
    function company_setting(string $key, mixed $default = null): mixed
    {
        return \App\Support\CompanySettings::get($key, $default);
    }
}

if (! function_exists('company_where')) {
    /**
     * Tambah filter company_id ke query builder (Eloquent atau Query\Builder).
     */
    function company_where($query, string $column = 'company_id')
    {
        $id = current_company_id();
        if ($id) {
            $query->where($column, $id);
        }

        return $query;
    }
}
