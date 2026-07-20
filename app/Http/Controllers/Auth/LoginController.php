<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Services\UserDeviceService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct(
        protected UserDeviceService $userDeviceService
    ) {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        if (! $this->userDeviceService->requiresDeviceLock($user)) {
            return null;
        }

        $result = $this->userDeviceService->handleLogin($request, $user);

        if (! $result['success']) {
            $this->guard()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                $this->username() => [$result['message']],
            ]);
        }

        session()->flash('absensi_device_token', $result['token']);

        return redirect()->intended($this->redirectPath())
            ->cookie($this->userDeviceService->makeCookie($result['token']));
    }
}
