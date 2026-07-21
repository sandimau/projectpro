@extends('layouts.app')

@section('title')
    Edit Company
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5>Edit Company — {{ $company->name }}</h5>
        </div>

        <div class="card-body">
            @include('layouts.includes.messages')
            <form method="POST" action="{{ route('companies.update', $company) }}">
                @csrf
                @method('PATCH')

                <div class="form-group mb-3">
                    <label for="name">Nama Company</label>
                    <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text"
                        name="name" id="name" value="{{ old('name', $company->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group mb-3">
                    <label for="slug">Slug (subdomain)</label>
                    <input class="form-control {{ $errors->has('slug') ? 'is-invalid' : '' }}" type="text"
                        name="slug" id="slug" value="{{ old('slug', $company->slug) }}" required>
                    @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                        {{ old('is_active', $company->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Aktif</label>
                </div>

                <hr>
                <h6>Pengaturan Absensi</h6>
                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label for="office_latitude">Office Latitude</label>
                        <input class="form-control" type="text" name="office_latitude" id="office_latitude"
                            value="{{ old('office_latitude', data_get($company->settings, 'office_latitude')) }}">
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label for="office_longitude">Office Longitude</label>
                        <input class="form-control" type="text" name="office_longitude" id="office_longitude"
                            value="{{ old('office_longitude', data_get($company->settings, 'office_longitude')) }}">
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label for="max_distance_radius">Max Distance (m)</label>
                        <input class="form-control" type="number" name="max_distance_radius" id="max_distance_radius"
                            value="{{ old('max_distance_radius', data_get($company->settings, 'max_distance_radius')) }}">
                    </div>
                    <div class="col-md-3 form-group mb-3">
                        <label for="clock_in_time">Jam Masuk</label>
                        <input class="form-control" type="text" name="clock_in_time" id="clock_in_time"
                            value="{{ old('clock_in_time', data_get($company->settings, 'clock_in_time')) }}"
                            placeholder="08:00">
                    </div>
                    <div class="col-md-3 form-group mb-3">
                        <label for="clock_out_time">Jam Pulang</label>
                        <input class="form-control" type="text" name="clock_out_time" id="clock_out_time"
                            value="{{ old('clock_out_time', data_get($company->settings, 'clock_out_time')) }}"
                            placeholder="17:00">
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label for="late_tolerance_minutes">Toleransi Terlambat (menit)</label>
                        <input class="form-control" type="number" name="late_tolerance_minutes"
                            id="late_tolerance_minutes"
                            value="{{ old('late_tolerance_minutes', data_get($company->settings, 'late_tolerance_minutes')) }}">
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label for="qr_code_secret">QR Code Secret</label>
                        <input class="form-control" type="text" name="qr_code_secret" id="qr_code_secret"
                            value="{{ old('qr_code_secret', data_get($company->settings, 'qr_code_secret')) }}">
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label for="fonnte_token">Fonnte Token</label>
                        <input class="form-control" type="text" name="fonnte_token" id="fonnte_token"
                            value="{{ old('fonnte_token', data_get($company->settings, 'fonnte_token')) }}">
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label for="whatsapp_group_target">WhatsApp Group Target</label>
                        <input class="form-control" type="text" name="whatsapp_group_target"
                            id="whatsapp_group_target"
                            value="{{ old('whatsapp_group_target', data_get($company->settings, 'whatsapp_group_target')) }}">
                    </div>
                </div>

                <div class="form-group">
                    <button class="btn btn-primary mt-2" type="submit">Update</button>
                    <a href="{{ route('companies.index') }}" class="btn btn-secondary mt-2">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
