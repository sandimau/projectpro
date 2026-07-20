@extends('layouts.app')

@section('title')
Create Akun Details
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        create kas
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("akunDetails.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group mb-3">
                <label class="required" for="nama">nama</label>
                <input class="form-control {{ $errors->has('nama') ? 'is-invalid' : '' }}" type="text" name="nama" id="nama" value="{{ old('nama', '') }}" required>
                @if($errors->has('nama'))
                    <div class="invalid-feedback">
                        {{ $errors->first('nama') }}
                    </div>
                @endif

            </div>
            <div class="form-group mb-3">
                <label for="akun_kategori_id">nama</label>
                <select class="form-select {{ $errors->has('akun_kategori_id') ? 'is-invalid' : '' }}" aria-label="Default select example" name="akun_kategori_id" id="akun_kategori_id">
                    @foreach($akun_kategoris as $id => $entry)
                        <option value="{{ $id }}" {{ old('akun_kategori_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('akun_kategori_id'))
                    <div class="invalid-feedback">
                        {{ $errors->first('akun_kategori_id') }}
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
