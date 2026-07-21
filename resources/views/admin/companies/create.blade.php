@extends('layouts.app')

@section('title')
    Tambah Company
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5>Tambah Company</h5>
        </div>

        <div class="card-body">
            @include('layouts.includes.messages')
            <form method="POST" action="{{ route('companies.store') }}">
                @csrf
                <div class="form-group mb-3">
                    <label for="name">Nama Company</label>
                    <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text"
                        name="name" id="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group mb-3">
                    <label for="slug">Slug (subdomain)</label>
                    <input class="form-control {{ $errors->has('slug') ? 'is-invalid' : '' }}" type="text"
                        name="slug" id="slug" value="{{ old('slug') }}"
                        placeholder="souvenir" required>
                    <small class="text-muted">Huruf kecil, angka, strip. Contoh: souvenir →
                        souvenir.projectpro.com</small>
                    @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                        {{ old('is_active', '1') ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Aktif</label>
                </div>

                <hr>
                <h6>Admin awal (opsional)</h6>
                <div class="form-group mb-3">
                    <label for="admin_name">Nama Admin</label>
                    <input class="form-control" type="text" name="admin_name" id="admin_name"
                        value="{{ old('admin_name', 'Admin') }}">
                </div>
                <div class="form-group mb-3">
                    <label for="admin_email">Email Admin</label>
                    <input class="form-control {{ $errors->has('admin_email') ? 'is-invalid' : '' }}" type="email"
                        name="admin_email" id="admin_email" value="{{ old('admin_email') }}"
                        placeholder="admin@souvenir.com">
                    @error('admin_email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group mb-3">
                    <label for="admin_password">Password Admin</label>
                    <input class="form-control {{ $errors->has('admin_password') ? 'is-invalid' : '' }}" type="text"
                        name="admin_password" id="admin_password" value="{{ old('admin_password', 'password') }}">
                    @error('admin_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <button class="btn btn-primary mt-2" type="submit">Simpan</button>
                    <a href="{{ route('companies.index') }}" class="btn btn-secondary mt-2">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
