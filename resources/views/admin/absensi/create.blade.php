@extends('layouts.app')

@section('title')
    Tambah Absensi
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title">Tambah Absensi</h5>
                </div>
                <a href="{{ route('absensi.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('absensi.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group mb-3">
                    <label for="member_id">Member</label>
                    <select class="form-select {{ $errors->has('member_id') ? 'is-invalid' : '' }}" name="member_id" id="member_id" required>
                        <option value="">Pilih Member</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}" {{ old('member_id') == $member->id ? 'selected' : '' }}>{{ $member->nama_lengkap }} ({{ $member->jenis ?? 'karyawan' }})</option>
                        @endforeach
                    </select>
                    @if ($errors->has('member_id'))
                        <div class="invalid-feedback">{{ $errors->first('member_id') }}</div>
                    @endif
                </div>
                <div class="form-group mb-3">
                    <label for="tanggal">Tanggal</label>
                    <input type="date" class="form-control {{ $errors->has('tanggal') ? 'is-invalid' : '' }}" name="tanggal" id="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                    @if ($errors->has('tanggal'))
                        <div class="invalid-feedback">{{ $errors->first('tanggal') }}</div>
                    @endif
                </div>
                <div class="form-group mb-3">
                    <label for="jenis">Jenis</label>
                    <select class="form-select {{ $errors->has('jenis') ? 'is-invalid' : '' }}" name="jenis" id="jenis" required>
                        <option value="sakit" {{ old('jenis') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                        <option value="ijin" {{ old('jenis') == 'ijin' ? 'selected' : '' }}>Ijin</option>
                        <option value="terlambat" {{ old('jenis') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                        <option value="cuti" {{ old('jenis') == 'cuti' ? 'selected' : '' }}>Cuti (aman untuk karyawan)</option>
                        <option value="alpha" {{ old('jenis') == 'alpha' ? 'selected' : '' }}>Alpha</option>
                    </select>
                    @if ($errors->has('jenis'))
                        <div class="invalid-feedback">{{ $errors->first('jenis') }}</div>
                    @endif
                </div>
                <div class="form-group mb-3">
                    <label for="keterangan">Keterangan</label>
                    <input type="text" class="form-control {{ $errors->has('keterangan') ? 'is-invalid' : '' }}" name="keterangan" id="keterangan" value="{{ old('keterangan') }}">
                    @if ($errors->has('keterangan'))
                        <div class="invalid-feedback">{{ $errors->first('keterangan') }}</div>
                    @endif
                </div>
                <div class="form-group mb-3">
                    <button class="btn btn-primary" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
