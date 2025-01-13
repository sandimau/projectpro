@extends('layouts.app')

@section('title')
    Create Sistems
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5>Add Sistem</h5>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('sistem.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group mb-3">
                    <label for="nama">Nama</label>
                    <input class="form-control {{ $errors->has('nama') ? 'is-invalid' : '' }}" type="text" name="nama"
                        id="nama" value="{{ old('nama', '') }}">
                    @if ($errors->has('nama'))
                        <div class="invalid-feedback">
                            {{ $errors->first('nama') }}
                        </div>
                    @endif
                </div>
                <div class="form-group mb-3">
                    <label for="type">Type</label>
                    <select class="form-select {{ $errors->has('type') ? 'is-invalid' : '' }}"
                        aria-label="Default select example" name="type" id="type">
                        <option value="text" {{ old('type', '') }}>text</option>
                        <option value="file" {{ old('type', '') }}>file</option>
                        <option value="number" {{ old('type', '') }}>number</option>
                    </select>
                    @if ($errors->has('type'))
                        <div class="invalid-feedback">
                            {{ $errors->first('type') }}
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
