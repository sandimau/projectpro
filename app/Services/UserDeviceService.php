<?php

namespace App\Services;

use App\Models\Member;
use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Cookie;

class UserDeviceService
{
    public const COOKIE_NAME = 'absensi_device_token';

    private const COOKIE_TTL_MINUTES = 60 * 24 * 365;

    public function requiresDeviceLock(User $user): bool
    {
        if ($user->hasAnyRole(['super', 'admin'])) {
            return false;
        }

        if (! $user->can('absensi_scan')) {
            return false;
        }

        return Member::where('user_id', $user->id)->where('status', 1)->exists();
    }

    /**
     * @return array{success: bool, message?: string, token?: string, is_new?: bool}
     */
    public function handleLogin(Request $request, User $user): array
    {
        $submittedTokens = $this->collectSubmittedTokens($request);
        $userDevice = UserDevice::where('user_id', $user->id)->first();

        if (! $userDevice) {
            return $this->registerDevice($request, $user->id);
        }

        if ($this->hasMatchingToken($userDevice, $submittedTokens)) {
            return [
                'success' => true,
                'token' => $userDevice->device_hash,
            ];
        }

        if ($submittedTokens->isEmpty()) {
            return $this->rebindDevice($request, $userDevice);
        }

        return [
            'success' => false,
            'message' => 'Perangkat tidak terdaftar. Hubungi admin untuk reset perangkat absensi.',
        ];
    }

    public function validateToken(Request $request, User $user): bool
    {
        if (! $this->requiresDeviceLock($user)) {
            return true;
        }

        $userDevice = UserDevice::where('user_id', $user->id)->first();

        if (! $userDevice) {
            return false;
        }

        return $this->hasMatchingToken($userDevice, $this->collectSubmittedTokens($request));
    }

    public function resetDevice(User $user): void
    {
        UserDevice::where('user_id', $user->id)->delete();
    }

    public function makeCookie(string $token): Cookie
    {
        return cookie(
            self::COOKIE_NAME,
            $token,
            $token === '' ? -2628000 : self::COOKIE_TTL_MINUTES,
            '/',
            config('session.domain'),
            (bool) config('session.secure', false),
            true,
            false,
            config('session.same_site', 'lax')
        );
    }

    public function collectSubmittedTokens(Request $request): Collection
    {
        return collect([
            $request->input('device_token'),
            $request->cookie(self::COOKIE_NAME),
            $request->cookie('device_token'),
        ])->filter(fn ($token) => filled($token))->unique()->values();
    }

    private function hasMatchingToken(UserDevice $userDevice, Collection $submittedTokens): bool
    {
        return $submittedTokens->contains(
            fn ($token) => hash_equals((string) $userDevice->device_hash, (string) $token)
        );
    }

    /**
     * @return array{success: true, token: string, is_new: true}
     */
    private function registerDevice(Request $request, int $userId): array
    {
        $newToken = Str::random(60);

        UserDevice::create([
            'user_id' => $userId,
            'device_hash' => $newToken,
            'user_agent' => $request->header('User-Agent'),
        ]);

        return [
            'success' => true,
            'token' => $newToken,
            'is_new' => true,
        ];
    }

    /**
     * @return array{success: true, token: string, is_new: true}
     */
    private function rebindDevice(Request $request, UserDevice $userDevice): array
    {
        $newToken = Str::random(60);

        $userDevice->update([
            'device_hash' => $newToken,
            'user_agent' => $request->header('User-Agent'),
        ]);

        return [
            'success' => true,
            'token' => $newToken,
            'is_new' => true,
        ];
    }
}
