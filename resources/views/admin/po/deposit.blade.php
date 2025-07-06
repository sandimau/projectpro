@extends('layouts.app')

@section('title')
    Create Deposit
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title">add Deposit</h5>
                </div>
                <a href="{{ route('po.index') }}" class="btn btn-success ">back</a>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('po.deposit.store', $po) }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group mb-3">
                    <label for="jumlah">Jumlah</label>
                    <input class="form-control {{ $errors->has('jumlah') ? 'is-invalid' : '' }}" type="number"
                        name="jumlah" id="jumlah" value="{{ old('jumlah', '') }}">
                    @if ($errors->has('jumlah'))
                        <div class="invalid-feedback">
                            {{ $errors->first('jumlah') }}
                        </div>
                    @endif
                </div>
                <div class="form-group mb-3">
                    <label for="ket">Keterangan</label>
                    <input class="form-control {{ $errors->has('ket') ? 'is-invalid' : '' }}" type="text"
                        name="ket" id="ket" value="{{ old('ket', '') }}">
                    @if ($errors->has('ket'))
                        <div class="invalid-feedback">
                            {{ $errors->first('ket') }}
                        </div>
                    @endif
                </div>
                <div class="form-group mb-3">
                    <label for="akun_detail_id">dari kas</label>
                    <select class="form-control {{ $errors->has('akun_detail_id') ? 'is-invalid' : '' }}" name="akun_detail_id" id="akun_detail_id">
                        <option value="">Pilih kas</option>
                        @foreach ($kas as $k)
                            <option value="{{ $k->id }}">{{ $k->nama }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('akun_detail_id'))
                        <div class="invalid-feedback">
                            {{ $errors->first('akun_detail_id') }}
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
