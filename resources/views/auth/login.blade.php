@extends('layouts.guest')

@section('content')
    <div class="col-lg-8">
        <div class="card-group d-block d-md-flex row">
            <div class="card col-md-7 p-4 mb-0">
                <div class="card-body">
                    <h1 class="pb-3">{{ __('Login') }}</h1>
                    <!-- Errors block -->
                    @include('layouts.includes.errors')
                    <!-- / Errors block -->
                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="input-group mb-3"><span class="input-group-text">
                                <svg class="icon">
                                    <use xlink:href="{{ asset('icons/coreui.svg#cil-envelope-open') }}"></use>
                                </svg></span>
                            <input class="form-control @error('username') is-invalid @enderror" type="text"
                                name="email" placeholder="{{ __('Username') }}" required autofocus>
                            @error('username')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="input-group mb-4"><span class="input-group-text">
                                <svg class="icon">
                                    <use xlink:href="{{ asset('icons/coreui.svg#cil-lock-locked') }}"></use>
                                </svg></span>
                            <input class="form-control @error('password') is-invalid @enderror" type="password"
                                name="password" placeholder="{{ __('Password') }}" required>
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="row">
                            {{-- @if (Route::has('password.request'))
                                <div class="col-6">
                                    <a href="{{ route('register') }}"
                                        class="btn btn-outline-dark w-100">{{ __('Register') }}</a>
                                </div>
                            @endif --}}
                            <div class="col-12">
                                <button class="btn btn-primary w-100" type="submit">{{ __('Login') }}</button>
                            </div>
                            {{-- <a href="{{ route('password.request') }}" class="btn btn-link px-0"
                                type="button">{{ __('Forgot Your Password?') }}</a> --}}

                        </div>
                    </form>
                </div>
            </div>
            <div class="card col-md-5 text-white bg-login-image py-5">
            </div>
        </div>
    </div>
@endsection
