@extends('layouts.app')

@section('title')
    Create Produk Stoks
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title">Add Produk Stoks > {{ $produk->nama }}</h5>
                </div>
                @can('kontak_create')
                    <a href="{{ route('produkStok.index', $produk->id) }}" class="btn btn-secondary">back</a>
                @endcan
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('produkStok.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="produk_id" value="{{ $produk->id }}">
                <div class="form-group mb-3">
                    <label for="tambah">tambah</label>
                    <input class="form-control {{ $errors->has('tambah') ? 'is-invalid' : '' }}" type="number"
                        name="tambah" id="tambah" value="0">
                    @if ($errors->has('tambah'))
                        <div class="invalid-feedback">
                            {{ $errors->first('tambah') }}
                        </div>
                    @endif
                </div>
                <div class="form-group mb-3">
                    <label for="kurang">kurang</label>
                    <input class="form-control {{ $errors->has('kurang') ? 'is-invalid' : '' }}" type="number"
                        name="kurang" id="kurang" value="0">
                    @if ($errors->has('kurang'))
                        <div class="invalid-feedback">
                            {{ $errors->first('kurang') }}
                        </div>
                    @endif
                </div>
                <div class="form-group mb-3">
                    <label for="deskripsi">Keterangan</label>
                    <textarea class="form-control {{ $errors->has('keterangan') ? 'is-invalid' : '' }}" name="keterangan" id=""
                        cols="30" rows="10">{{ old('keterangan', '') }}</textarea>
                    @if ($errors->has('keterangan'))
                        <div class="invalid-feedback">
                            {{ $errors->first('keterangan') }}
                        </div>
                    @endif
                </div>
                <div class="form-group mb-3">
                    <label for="tanggal">tanggal</label>
                    <input class="form-control {{ $errors->has('tanggal') ? 'is-invalid' : '' }}" type="date"
                        name="tanggal" id="tanggal" value="{{ old('tanggal', '') }}">
                    @if ($errors->has('tanggal'))
                        <div class="invalid-feedback">
                            {{ $errors->first('tanggal') }}
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
