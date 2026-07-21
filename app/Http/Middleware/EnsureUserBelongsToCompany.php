<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserBelongsToCompany
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $companyId = current_company_id();

        if ($user && $companyId && (int) $user->company_id !== (int) $companyId) {
            abort(403, 'Akun ini tidak terdaftar di company ini.');
        }

        return $next($request);
    }
}
