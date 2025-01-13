@extends('layouts.app')

@section('title')
Create Produksi
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Add Produksi</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("produksis.store") }}" enctype="multipart/form-data">
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
                <label for="warna">Warna</label>
                <input class="form-control {{ $errors->has('warna') ? 'is-invalid' : '' }}" type="color" name="warna" id="warna" value="{{ old('warna', '') }}">
                @if($errors->has('warna'))
                    <div class="invalid-feedback">
                        {{ $errors->first('warna') }}
                    </div>
                @endif
            </div>
            <div class="form-group">
                <label>grup</label>
                <select class="form-select {{ $errors->has('warna') ? 'is-invalid' : '' }}" aria-label="Default select example" name="grup" >
                    <option>pilih grup</option>
                    <option value="awal">awal</option>
                    <option value="produksi">produksi</option>
                    <option value="selesai">selesai</option>
                </select>
                @if($errors->has('grup'))
                    <div class="invalid-feedback">
                        {{ $errors->first('grup') }}
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
