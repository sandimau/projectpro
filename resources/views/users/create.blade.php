@extends('layouts.app')

@section('title')
    Create User
@endsection

@section('content')
    <div class="bg-light p-4 rounded">
        <div class="card">
            <div class="card-body">
                <h5>Add new user</h5>
                <div class="container mt-4">
                    <form method="POST" action="">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input value="{{ old('name') }}" type="text" class="form-control" name="name"
                                placeholder="Name" required>

                            @if ($errors->has('name'))
                                <span class="text-danger text-left">{{ $errors->first('name') }}</span>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input value="{{ old('email') }}" type="email" class="form-control" name="email"
                                placeholder="Email address" required>
                            @if ($errors->has('email'))
                                <span class="text-danger text-left">{{ $errors->first('email') }}</span>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Password</label>
                            <input class="form-control @error('password') is-invalid @enderror" type="password"
                                name="password" placeholder="{{ __('password') }}" required>
                            @error('password')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Password Confirm</label>
                            <input class="form-control @error('password_confirmation') is-invalid @enderror" type="password"
                                name="password_confirmation" placeholder="{{ __('password confirmation') }}" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Save user</button>
                        <a href="{{ route('users.index') }}" class="btn btn-default">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
