@extends('layouts.app')

@section('title')
Create Akuns
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            create akun
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('akuns.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group mb-3">
                    <label for="nama">nama</label>
                    <input class="form-control {{ $errors->has('nama') ? 'is-invalid' : '' }}" type="text" name="nama"
                        id="nama" value="{{ old('nama', '') }}">
                    @if ($errors->has('nama'))
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
