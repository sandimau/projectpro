@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Edit Produk</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('produkModel.update', $produk) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Nama Produk</label>
                    <input type="text" class="form-control @error('nama') is-invalid @enderror" name="nama" value="{{ old('nama', $produk->nama) }}">
                    @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Harga</label>
                    <input type="number" class="form-control @error('harga') is-invalid @enderror" name="harga" value="{{ old('harga', $produk->harga) }}">
                    @error('harga')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Satuan</label>
                    <input type="text" class="form-control @error('satuan') is-invalid @enderror" name="satuan" value="{{ old('satuan', $produk->satuan) }}">
                    @error('satuan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" name="deskripsi">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Stok</label>
                    <input type="number" class="form-control" name="stok" value="{{ old('stok', $produk->stok) }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Kategori</label>
                    <select class="form-control @error('kategori_id') is-invalid @enderror" name="kategori_id">
                        <option value="">Pilih Kategori</option>
                        @foreach($kategoris as $kategori)
                            <option value="{{ $kategori->id }}" {{ old('kategori_id', $produk->kategori_id) == $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('kategori_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Supplier</label>
                    <select class="form-control @error('kontak_id') is-invalid @enderror" name="kontak_id">
                        <option value="">Pilih Supplier</option>
                        @foreach($kontaks as $kontak)
                            <option value="{{ $kontak->id }}" {{ old('kontak_id', $produk->kontak_id) == $kontak->id ? 'selected' : '' }}>
                                {{ $kontak->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('kontak_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Gambar</label>
                    @if($produk->gambar)
                        <div class="mb-2">
                            <img src="{{ asset('storage/'.$produk->gambar) }}" alt="Current Image" style="max-width: 200px;">
                        </div>
                    @endif
                    <input type="file" class="form-control @error('gambar') is-invalid @enderror" name="gambar">
                    @error('gambar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="jual" value="1" {{ old('jual', $produk->jual) ? 'checked' : '' }}>
                        <label class="form-check-label">Dapat Dijual</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="beli" value="1" {{ old('beli', $produk->beli) ? 'checked' : '' }}>
                        <label class="form-check-label">Dapat Dibeli</label>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('produkModel.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
