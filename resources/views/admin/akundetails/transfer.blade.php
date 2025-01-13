@extends('layouts.app')

@section('title')
Kas Transfer Create
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        Kas Transfer
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("transfer.store") }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" value="{{ $akunDetail->id }}" name="akun_detail_dari">
            <div class="form-group mb-3">
                <label class="required" for="nama">nama</label>
                <input disabled class="form-control {{ $errors->has('nama') ? 'is-invalid' : '' }}" type="text" name="nama" id="nama" value="{{ old('nama',$akunDetail->nama) }}" required>
                @if($errors->has('nama'))
                    <div class="invalid-feedback">
                        {{ $errors->first('nama') }}
                    </div>
                @endif
            </div>
            <div class="form-group mb-3">
                <label for="akun_detail_tujuan">kas</label>
                <select class="form-select {{ $errors->has('akun_detail_tujuan') ? 'is-invalid' : '' }}" name="akun_detail_tujuan" id="akun_detail_tujuan">
                    @foreach($kas as $id => $entry)
                        <option value="{{ $id }}" {{ old('akun_detail_tujuan') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('akun_detail_tujuan'))
                    <div class="invalid-feedback">
                        {{ $errors->first('akun_detail_tujuan') }}
                    </div>
                @endif
            </div>
            <div class="form-group mb-3">
                <label class="required" for="jumlah">jumlah</label>
                <input class="form-control {{ $errors->has('jumlah') ? 'is-invalid' : '' }}" type="number" name="jumlah" id="jumlah" value="{{ old('jumlah') }}" required>
                @if($errors->has('jumlah'))
                    <div class="invalid-feedback">
                        {{ $errors->first('jumlah') }}
                    </div>
                @endif
            </div>
            <div class="form-group mb-3">
                <button class="btn btn-danger" type="submit">
                    {{ trans('save') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
