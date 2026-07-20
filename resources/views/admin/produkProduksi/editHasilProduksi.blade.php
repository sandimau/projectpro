@extends('layouts.app')

@section('title')
Edit Hasil Produksi
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Edit Hasil Produksi</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("produksi.updateHasilProduksi", [$produksi->id, $hasil->id]) }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="form-group mb-3">
                <label for="nama" class="mb-2">Produk</label>
                <input class="form-control {{ $errors->has('produk_id') ? 'is-invalid' : '' }}" type="text" name="produk_id" id="produk_id" value="{{ old('produk_id', $hasil->produk->namaLengkap) }}" disabled>
            </div>
            <div class="form-group mb-3">
                <label for="jumlah" class="mb-2">Hasil Produksi</label>
                <input class="form-control {{ $errors->has('jumlah') ? 'is-invalid' : '' }}" type="number" name="jumlah" id="jumlah" value="{{ old('jumlah', $hasil->jumlah) }}">
                @if($errors->has('jumlah'))
                    <div class="invalid-feedback">
                        {{ $errors->first('jumlah') }}
                    </div>
                @endif
            </div>
            <div class="form-group">
                <button class="btn btn-primary mt-4" type="submit">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
