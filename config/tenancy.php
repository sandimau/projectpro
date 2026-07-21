<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Central Domains
    |--------------------------------------------------------------------------
    |
    | Host tanpa subdomain tenant (apex). Request ke domain ini tanpa subdomain
    | tidak dianggap company, kecuali DEFAULT_COMPANY_SLUG diisi (dev/fallback).
    |
    */
    'central_domains' => array_values(array_filter(array_map(
        'trim',
        explode(',', env('CENTRAL_DOMAINS', 'projectpro.com,localhost,127.0.0.1'))
    ))),

    /*
    |--------------------------------------------------------------------------
    | Reserved Subdomains
    |--------------------------------------------------------------------------
    */
    'reserved_subdomains' => [
        'www',
        'app',
        'api',
        'mail',
        'ftp',
        'admin',
        'status',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Company Slug (dev / path-based install)
    |--------------------------------------------------------------------------
    |
    | Jika request tidak punya subdomain (mis. localhost:82/projectpro),
    | pakai slug ini. Kosongkan di production agar wajib subdomain.
    |
    */
    'default_company_slug' => env('DEFAULT_COMPANY_SLUG'),

];
