<?php

namespace App\Http\Middleware;

use App\Models\Sistem;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LoadSistemConfig
{
    public function handle(Request $request, Closure $next): Response
    {
        // Otomatis scoped by BelongsToCompany jika current company sudah di-set
        foreach (Sistem::pluck('isi', 'nama') as $nama => $isi) {
            session([$nama => $isi]);
        }

        return $next($request);
    }
}
