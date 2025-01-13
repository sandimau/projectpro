@extends('layouts.app')

@section('title')
    Edit Produks
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5>Edit Produk</h5>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('produks.update', [$produk->id]) }}" enctype="multipart/form-data">
                @method('patch')
                @csrf
                <div class="mb-3">
                    <label for="formFile" class="form-label">gambar</label>
                    <input class="form-control" type="file" id="formFile" name="gambar">
                </div>
                <div class="form-group mb-3">
                    <label for="nama">Nama</label>
                    <input class="form-control {{ $errors->has('nama') ? 'is-invalid' : '' }}" type="text" name="nama"
                        id="nama" value="{{ old('nama', $produk->nama) }}">
                    @if ($errors->has('nama'))
                        <div class="invalid-feedback">
                            {{ $errors->first('nama') }}
                        </div>
                    @endif
                </div>
                <div class="form-group mb-3">
                    <label for="harga">Harga</label>
                    <input class="form-control {{ $errors->has('harga') ? 'is-invalid' : '' }}" type="number"
                        name="harga" id="harga" value="{{ old('harga', $produk->harga) }}">
                    @if ($errors->has('harga'))
                        <div class="invalid-feedback">
                            {{ $errors->first('harga') }}
                        </div>
                    @endif
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input name="jual" class="form-check-input {{ $errors->has('jual') ? 'is-invalid' : '' }}"
                            type="checkbox" value="1" id="flexCheckDefault" {{ $produk->jual ? 'checked' : null }}>
                        <label class="form-check-label" for="flexCheckDefault">
                            Jual
                        </label>
                        @if ($errors->has('jual'))
                            <div class="invalid-feedback">
                                {{ $errors->first('jual') }}
                            </div>
                        @endif
                    </div>
                    <div class="form-check">
                        <input name="beli" class="form-check-input {{ $errors->has('beli') ? 'is-invalid' : '' }}" type="checkbox" value="1" id="flexCheckChecked"
                            {{ $produk->beli ? 'checked' : null }}>
                        <label class="form-check-label" for="flexCheckChecked">
                            beli
                        </label>
                        @if ($errors->has('beli'))
                            <div class="invalid-feedback">
                                {{ $errors->first('beli') }}
                            </div>
                        @endif
                    </div>
                    <div class="form-check">
                        <input name="stok" class="form-check-input {{ $errors->has('stok') ? 'is-invalid' : '' }}" type="checkbox" value="1" id="flexCheckChecked"
                            {{ $produk->stok ? 'checked' : null }}>
                        <label class="form-check-label" for="flexCheckChecked">
                            stok
                        </label>
                        @if ($errors->has('stok'))
                            <div class="invalid-feedback">
                                {{ $errors->first('stok') }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label for="satuan">satuan</label>
                    <select class="form-select {{ $errors->has('satuan') ? 'is-invalid' : '' }}" aria-label="Default select example" name="satuan"
                        id="satuan">
                        @foreach ($satuan as $id => $entry)
                            <option value="{{ $id }}" {{ $produk->satuan === $id ? 'selected' : '' }}>
                                {{ $entry }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('satuan'))
                        <div class="invalid-feedback">
                            {{ $errors->first('satuan') }}
                        </div>
                    @endif
                </div>
                <div class="form-group mb-3">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea class="form-control {{ $errors->has('deskripsi') ? 'is-invalid' : '' }}" name="deskripsi" id=""
                        cols="30" rows="10">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
                    @if ($errors->has('deskripsi'))
                        <div class="invalid-feedback">
                            {{ $errors->first('deskripsi') }}
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
