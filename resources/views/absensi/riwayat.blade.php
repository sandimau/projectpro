@extends('layouts.app')

@section('title')
    Riwayat Absensi
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="mb-0">Riwayat Absensi</h5>
                <div class="text-muted small">{{ $member->nama_lengkap }}</div>
            </div>
            <a href="{{ route('absensi.scan') }}" class="btn btn-primary btn-sm">
                <i class='bx bx-qr-scan'></i> Scan Absensi
            </a>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label">Bulan</label>
                    <select name="bulan" class="form-select">
                        @php
                            $bulanList = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
                        @endphp
                        @foreach($bulanList as $num => $label)
                            <option value="{{ $num }}" {{ (int) $bulan === $num ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tahun</label>
                    <input type="number" name="tahun" class="form-control" value="{{ $tahun }}" min="2020" max="2035">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-secondary">Filter</button>
                </div>
            </form>

            {{ $attendances->links() }}

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Tipe</th>
                            <th>Waktu</th>
                            <th>Status</th>
                            <th>Keterlambatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($attendances as $item)
                            <tr>
                                <td>{{ $item->attendance_date->format('d/m/Y') }}</td>
                                <td>
                                    @if ($item->type === 'clock_in')
                                        <span class="badge bg-primary">Masuk</span>
                                    @else
                                        <span class="badge bg-danger">Pulang</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($item->attendance_time)->format('H:i:s') }}</td>
                                <td>{{ ucfirst($item->status) }}</td>
                                <td>
                                    @if ($item->minutes_late)
                                        {{ $item->minutes_late }} menit
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Tidak ada data absensi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
