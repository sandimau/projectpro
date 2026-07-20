@extends('layouts.app')

@section('title')
    Edit Sistems
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5>Update Sistem</h5>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('sistem.update') }}" enctype="multipart/form-data">
                @csrf
                @foreach ($sistems as $item)
                <div class="form-group mb-3">
                    <label for="{{ $item->nama }}">{{ $item->nama }}</label>
                    <input class="form-control {{ $errors->has($item->nama) ? 'is-invalid' : '' }}" type="{{ $item->type }}" name="{{ $item->nama }}"
                        id="{{ $item->nama }}" value="{{ $item->isi }}">
                    @if ($errors->has($item->nama))
                        <div class="invalid-feedback">
                            {{ $errors->first($item->nama) }}
                        </div>
                    @endif
                </div>
                @endforeach
                <div class="form-group">
                    <button class="btn btn-primary mt-4" type="submit">
                        save
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
