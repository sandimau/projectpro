@extends('layouts.app')

@section('title')
    Edit Produk Kategori
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Edit Kategori Produk</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('produk-kategori.update', $produkKategori) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group mb-3">
                <label for="nama">Nama Kategori</label>
                <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $produkKategori->nama) }}">
                @error('nama')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label for="kategori_utama_id">Kategori Utama</label>
                <select class="form-control @error('kategori_utama_id') is-invalid @enderror" id="kategori_utama_id" name="kategori_utama_id">
                    <option value="">Pilih Kategori Utama</option>
                    @foreach($kategoriUtamas as $kategoriUtama)
                        <option value="{{ $kategoriUtama->id }}" {{ old('kategori_utama_id', $produkKategori->kategori_utama_id) == $kategoriUtama->id ? 'selected' : '' }}>
                            {{ $kategoriUtama->nama }}
                        </option>
                    @endforeach
                </select>
                @error('kategori_utama_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('produk-kategori.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection
