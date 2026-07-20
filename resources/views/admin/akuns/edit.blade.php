@extends('layouts.app')

@section('title')
Edit Akuns
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        akuns
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("akuns.update", [$akun->id]) }}" enctype="multipart/form-data">
            @method('patch')
            @csrf
            <div class="form-group mb-3">
                <label for="nama">nama</label>
                <input class="form-control {{ $errors->has('nama') ? 'is-invalid' : '' }}" type="text" name="nama" id="nama" value="{{ old('nama', $akun->nama) }}">
                @if($errors->has('nama'))
                    <div class="invalid-feedback">
                        {{ $errors->first('nama') }}
                    </div>
                @endif
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
