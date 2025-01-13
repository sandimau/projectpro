@extends('layouts.app')

@section('title')
Edit Produk Stok
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.produkStok.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.produk-stoks.update", [$produkStok->id]) }}" enctype="multipart/form-data">
            @method('patch')
            @csrf
            <div class="form-group">
                <label for="tanggal">{{ trans('cruds.produkStok.fields.tanggal') }}</label>
                <input class="form-control date {{ $errors->has('tanggal') ? 'is-invalid' : '' }}" type="text" name="tanggal" id="tanggal" value="{{ old('tanggal', $produkStok->tanggal) }}">
                @if($errors->has('tanggal'))
                    <div class="invalid-feedback">
                        {{ $errors->first('tanggal') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.produkStok.fields.tanggal_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="tambah">{{ trans('cruds.produkStok.fields.tambah') }}</label>
                <input class="form-control {{ $errors->has('tambah') ? 'is-invalid' : '' }}" type="number" name="tambah" id="tambah" value="{{ old('tambah', $produkStok->tambah) }}" step="1">
                @if($errors->has('tambah'))
                    <div class="invalid-feedback">
                        {{ $errors->first('tambah') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.produkStok.fields.tambah_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="kurang">{{ trans('cruds.produkStok.fields.kurang') }}</label>
                <input class="form-control {{ $errors->has('kurang') ? 'is-invalid' : '' }}" type="number" name="kurang" id="kurang" value="{{ old('kurang', $produkStok->kurang) }}" step="1">
                @if($errors->has('kurang'))
                    <div class="invalid-feedback">
                        {{ $errors->first('kurang') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.produkStok.fields.kurang_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="saldo">{{ trans('cruds.produkStok.fields.saldo') }}</label>
                <input class="form-control {{ $errors->has('saldo') ? 'is-invalid' : '' }}" type="number" name="saldo" id="saldo" value="{{ old('saldo', $produkStok->saldo) }}" step="1">
                @if($errors->has('saldo'))
                    <div class="invalid-feedback">
                        {{ $errors->first('saldo') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.produkStok.fields.saldo_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="keterangan">{{ trans('cruds.produkStok.fields.keterangan') }}</label>
                <input class="form-control {{ $errors->has('keterangan') ? 'is-invalid' : '' }}" type="text" name="keterangan" id="keterangan" value="{{ old('keterangan', $produkStok->keterangan) }}">
                @if($errors->has('keterangan'))
                    <div class="invalid-feedback">
                        {{ $errors->first('keterangan') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.produkStok.fields.keterangan_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="kode">{{ trans('cruds.produkStok.fields.kode') }}</label>
                <input class="form-control {{ $errors->has('kode') ? 'is-invalid' : '' }}" type="text" name="kode" id="kode" value="{{ old('kode', $produkStok->kode) }}">
                @if($errors->has('kode'))
                    <div class="invalid-feedback">
                        {{ $errors->first('kode') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.produkStok.fields.kode_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="order_id">{{ trans('cruds.produkStok.fields.order') }}</label>
                <select class="form-select select2 {{ $errors->has('order') ? 'is-invalid' : '' }}" name="order_id" id="order_id">
                    @foreach($orders as $id => $entry)
                        <option value="{{ $id }}" {{ (old('order_id') ? old('order_id') : $produkStok->order->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('order'))
                    <div class="invalid-feedback">
                        {{ $errors->first('order') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.produkStok.fields.order_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
