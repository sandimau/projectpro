@extends('layouts.app')

@section('title')
    Create Whattodos
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title">add whattodo</h5>
                </div>
                <a href="{{ route('whattodo') }}" class="btn btn-success ">back</a>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('whattodo.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group ">
                    <label for="isi" class="mb-3">tugas</label>
                    <input class="form-control {{ $errors->has('isi') ? 'is-invalid' : '' }}" type="text" name="isi"
                        id="isi" value="{{ old('isi', '') }}">
                    @if ($errors->has('isi'))
                        <div class="invalid-feedback">
                            {{ $errors->first('isi') }}
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
