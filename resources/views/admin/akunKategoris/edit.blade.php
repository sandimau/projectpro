@extends('layouts.app')

@section('title')
Edit Akun Kategoris
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        Edit Akun Kategoris
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("akunKategoris.update", [$akunKategori->id]) }}" enctype="multipart/form-data">
            @method('patch')
            @csrf
            <div class="form-group mb-3">
                <label for="nama">nama</label>
                <input class="form-control {{ $errors->has('nama') ? 'is-invalid' : '' }}" type="text" name="nama" id="nama" value="{{ old('nama', $akunKategori->nama) }}">
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
                        <option value="{{ $id }}" {{ (old('akun_id') ? old('akun_id') : $akunKategori->akun->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('akun'))
                    <div class="invalid-feedback">
                        {{ $errors->first('akun') }}
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
