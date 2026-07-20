@extends('layouts.app')

@section('title')
    Pengaturan Absensi
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Pengaturan Absensi</h5>
            <a href="{{ route('absensi.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
        </div>
        <div class="card-body">
            @include('layouts.includes.messages')

            <form action="{{ route('absensi.settings.update') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="mb-3">Jadwal Kerja</h6>
                        <div class="mb-3">
                            <label for="clock_in_time" class="form-label">Jam Masuk Standar</label>
                            <input type="time" class="form-control" id="clock_in_time" name="clock_in_time" value="{{ $settings['clock_in_time'] }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="clock_out_time" class="form-label">Jam Pulang Standar</label>
                            <input type="time" class="form-control" id="clock_out_time" name="clock_out_time" value="{{ $settings['clock_out_time'] }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="late_tolerance_minutes" class="form-label">Toleransi Keterlambatan (Menit)</label>
                            <input type="number" class="form-control" id="late_tolerance_minutes" name="late_tolerance_minutes" value="{{ $settings['late_tolerance_minutes'] }}" min="0" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="mb-3">Lokasi & Jarak</h6>
                        <div class="mb-3">
                            <label for="office_latitude" class="form-label">Latitude Kantor</label>
                            <input type="text" class="form-control" id="office_latitude" name="office_latitude" value="{{ $settings['office_latitude'] }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="office_longitude" class="form-label">Longitude Kantor</label>
                            <input type="text" class="form-control" id="office_longitude" name="office_longitude" value="{{ $settings['office_longitude'] }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="max_distance_radius" class="form-label">Radius Jarak Absensi (Meter)</label>
                            <input type="number" class="form-control" id="max_distance_radius" name="max_distance_radius" value="{{ $settings['max_distance_radius'] }}" min="1" required>
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-6">
                        <h6 class="mb-3">QR Code & WhatsApp</h6>
                        <div class="mb-3">
                            <label for="qr_code_secret" class="form-label">Kode Rahasia QR</label>
                            <input type="text" class="form-control" id="qr_code_secret" name="qr_code_secret" value="{{ $settings['qr_code_secret'] }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="fonnte_token" class="form-label">Token Fonnte</label>
                            <input type="text" class="form-control" id="fonnte_token" name="fonnte_token" value="{{ $settings['fonnte_token'] ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label for="whatsapp_group_target" class="form-label">Target Grup WhatsApp</label>
                            <input type="text" class="form-control" id="whatsapp_group_target" name="whatsapp_group_target" value="{{ $settings['whatsapp_group_target'] ?? '' }}" placeholder="120363...@g.us">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">QR Code Absensi Kantor</h5>
        </div>
        <div class="card-body">
            <p class="text-muted">
                Karyawan absen dengan memindai QR Code yang ditempel di kantor. Cetak gambar di bawah ini.
            </p>
            <div class="row align-items-center">
                <div class="col-md-5">
                    <p class="mb-1"><strong>Kode rahasia saat ini:</strong></p>
                    <code class="d-block p-3 rounded mb-3">{{ $settings['qr_code_secret'] }}</code>
                </div>
                <div class="col-md-4 text-center">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ urlencode($settings['qr_code_secret']) }}"
                        alt="QR Code Absensi" class="img-fluid border rounded p-2" style="max-width: 260px;">
                    <p class="small text-muted mt-2">Cetak gambar ini lalu tempel di lokasi absensi kantor.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
