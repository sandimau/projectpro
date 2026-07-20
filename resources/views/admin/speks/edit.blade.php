@extends('layouts.app')

@section('title')
Edit Speks
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        Edit Spek
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("speks.update", [$spek->id]) }}" enctype="multipart/form-data">
            @method('patch')
            @csrf
            <div class="form-group mb-3">
                <label for="nama">Nama</label>
                <input class="form-control {{ $errors->has('nama') ? 'is-invalid' : '' }}" type="text" name="nama" id="nama" value="{{ old('nama',$spek->nama) }}">
                @if($errors->has('nama'))
                    <div class="invalid-feedback">
                        {{ $errors->first('nama') }}
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
