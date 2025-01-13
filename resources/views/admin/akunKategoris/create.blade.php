@extends('layouts.app')

@section('title')
Create Akun Kategoris
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        create akun kategori
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("akunKategoris.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group mb-3">
                <label for="nama">nama</label>
                <input class="form-control {{ $errors->has('nama') ? 'is-invalid' : '' }}" type="text" name="nama" id="nama" value="{{ old('nama', '') }}">
                @if($errors->has('nama'))
                    <div class="invalid-feedback">
                        {{ $errors->first('nama') }}
                    </div>
                @endif
            </div>
            <div class="form-group mb-3">
                <label for="akun_id">akun</label>
                <select class="form-select {{ $errors->has('akun_id') ? 'is-invalid' : '' }}" aria-label="Default select example" name="akun_id" id="akun_id">
                    @foreach($akuns as $id => $entry)
                        <option value="{{ $id }}" {{ old('akun_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('akun_id'))
                    <div class="invalid-feedback">
                        {{ $errors->first('akun_id') }}
                    </div>
                @endif
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('save') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
