@extends('layouts.app')

@section('title')
Create Spek
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Add Spek</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("speks.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="nama">Nama</label>
                <input class="form-control {{ $errors->has('nama') ? 'is-invalid' : '' }}" type="text" name="nama" id="nama" value="{{ old('nama', '') }}">
                @if($errors->has('nama'))
                    <div class="invalid-feedback">
                        {{ $errors->first('nama') }}
                    </div>
                @endif
            </div>
            <div class="form-group">
                <button class="btn btn-primary mt-4" type="submit">
                    save
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
