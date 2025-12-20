@extends('layouts.app')

@section('title')
Edit Produksi
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Edit Produksi</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("produksi.update", $produksi->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="form-group mb-3">
                <label for="keterangan" class="mb-2">Keterangan</label>
                <textarea class="form-control {{ $errors->has('ket') ? 'is-invalid' : '' }}" name="ket" id="ket" rows="3">{{ old('ket', $produksi->ket) }}</textarea>
                @if($errors->has('ket'))
                    <div class="invalid-feedback">
                        {{ $errors->first('ket') }}
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
