<?php

namespace App\Http\Middleware;

use App\Models\Company;
use App\Support\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class IdentifyCompany
{
    public function handle(Request $request, Closure $next): Response
    {
        $slug = $this->resolveSlug($request);

        if (! $slug) {
            abort(404, 'Company tidak ditemukan. Akses via subdomain, mis. souvenir.projectpro.com');
        }

        $company = Company::query()
            ->active()
            ->where('slug', $slug)
            ->first();

        if (! $company) {
            abort(404, 'Company tidak ditemukan atau tidak aktif.');
        }

        Tenant::set($company);

        $root = $request->getSchemeAndHttpHost();
        // Pertahankan path base jika app di subdirectory (mis. /projectpro)
        $basePath = rtrim(parse_url(config('app.url'), PHP_URL_PATH) ?: '', '/');
        if ($basePath && $basePath !== '/') {
            $root .= $basePath;
        }
        URL::forceRootUrl($root);
        config(['app.url' => $root]);

        return $next($request);
    }

    protected function resolveSlug(Request $request): ?string
    {
        $host = strtolower($request->getHost());
        $central = array_map('strtolower', config('tenancy.central_domains', []));
        $reserved = array_map('strtolower', config('tenancy.reserved_subdomains', []));

        foreach ($central as $domain) {
            if ($host === $domain) {
                return config('tenancy.default_company_slug') ?: null;
            }

            $suffix = '.'.$domain;
            if (str_ends_with($host, $suffix)) {
                $sub = substr($host, 0, -strlen($suffix));
                // hanya level pertama: souvenir.projectpro.com → souvenir
                // a.b.projectpro.com → abaikan nested kecuali exact satu segmen
                if ($sub === '' || str_contains($sub, '.') || in_array($sub, $reserved, true)) {
                    return config('tenancy.default_company_slug') ?: null;
                }

                return $sub;
            }
        }

        // Host tidak cocok central domain (mis. souvenir.localhost tanpa daftar)
        $parts = explode('.', $host);
        if (count($parts) >= 2) {
            $sub = $parts[0];
            if (! in_array($sub, $reserved, true) && $sub !== 'www') {
                // Jika host seperti souvenir.localhost
                if (in_array($parts[count($parts) - 1], ['localhost', 'local', 'test'], true)
                    || count($parts) >= 3) {
                    return $sub;
                }
            }
        }

        return config('tenancy.default_company_slug') ?: null;
    }
}
