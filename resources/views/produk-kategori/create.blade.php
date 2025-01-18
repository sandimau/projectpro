@extends('layouts.app')

@section('title')
    Add Produk Kategori
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Tambah Kategori Produk</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('produk-kategori.store') }}" method="POST">
            @csrf
            <div class="form-group mb-3">
                <label for="nama">Nama Kategori</label>
                <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama') }}">
                @error('nama')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <input type="hidden" id="kategori_utama_id" name="kategori_utama_id" value="{{ $utama->id }}">

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('produk-kategori.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection
