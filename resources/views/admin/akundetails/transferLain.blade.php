@extends('layouts.app')

@section('title')
Kas Pemasukan Lain
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        Kas Pemasukan Lain-Lain
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("transferLain.store") }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" value="{{ $akunDetail->id }}" name="akun_detail_id">
            <div class="form-group mb-3">
                <label class="required" for="jumlah">jumlah</label>
                <input class="form-control {{ $errors->has('jumlah') ? 'is-invalid' : '' }}" type="number" name="jumlah" id="jumlah" value="{{ old('jumlah') }}">
                @if($errors->has('jumlah'))
                    <div class="invalid-feedback">
                        {{ $errors->first('jumlah') }}
                    </div>
                @endif
            </div>
            <div class="form-group mb-3">
                <label class="required" for="keterangan">keterangan</label>
                <input class="form-control {{ $errors->has('keterangan') ? 'is-invalid' : '' }}" type="text" name="keterangan" id="keterangan" value="{{ old('keterangan') }}">
                @if($errors->has('keterangan'))
                    <div class="invalid-feedback">
                        {{ $errors->first('keterangan') }}
                    </div>
                @endif
            </div>
            <div class="form-group mb-3">
                <button class="btn btn-danger" type="submit">
                    {{ trans('save') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
