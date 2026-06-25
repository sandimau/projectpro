<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

trait RespondsToMemberModal
{
    protected function memberModalResponse(Request $request, string $message, string $redirectUrl): JsonResponse|RedirectResponse
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => $message,
                'redirect' => $redirectUrl,
            ]);
        }

        return redirect($redirectUrl)->withSuccess($message);
    }
}
