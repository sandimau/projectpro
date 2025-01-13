@extends('layouts.app')

@section('title')
    Create Bagian
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title">add Bagian</h5>
                </div>
                <a href="{{ route('bagian.index') }}" class="btn btn-primary ">back</a>
            </div>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('bagian.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group mb-3">
                    <label for="nama">Nama</label>
                    <input class="form-control {{ $errors->has('nama') ? 'is-invalid' : '' }}" type="text"
                        name="nama" id="nama" value="{{ old('nama') }}">
                    @if ($errors->has('nama'))
                        <div class="invalid-feedback">
                            {{ $errors->first('nama') }}
                        </div>
                    @endif
                </div>
                <div class="form-group mb-3">
                    <label for="grade">Grade</label>
                    <input class="form-control {{ $errors->has('grade') ? 'is-invalid' : '' }}" type="number"
                        name="grade" id="grade" value="{{ old('grade') }}">
                    @if ($errors->has('grade'))
                        <div class="invalid-feedback">
                            {{ $errors->first('grade') }}
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
