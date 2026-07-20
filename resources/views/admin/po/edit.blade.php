@extends('layouts.app')

@section('title')
    Create PO
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title">add PO</h5>
                </div>
                <a href="{{ route('po.index') }}" class="btn btn-success ">back</a>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('po.update', [$po->id]) }}" enctype="multipart/form-data">
                @method('patch')
                @csrf
                <div class="form-group mb-3">
                    <label for="ket">Keterangan</label>
                    <input class="form-control {{ $errors->has('ket') ? 'is-invalid' : '' }}" type="text"
                        name="ket" id="ket" value="{{ old('ket', $po->ket) }}">
                    @if ($errors->has('ket'))
                        <div class="invalid-feedback">
                            {{ $errors->first('ket') }}
                        </div>
                    @endif
                </div>
                <div class="form-group mb-3">
                    <label for="tglKedatangan">Tanggal Kedatangan</label>
                    <input class="form-control {{ $errors->has('tglKedatangan') ? 'is-invalid' : '' }}" type="date"
                        name="tglKedatangan" id="tglKedatangan" value="{{ old('tglKedatangan', $po->tglKedatangan) }}">
                    @if ($errors->has('tglKedatangan'))
                        <div class="invalid-feedback">
                            {{ $errors->first('tglKedatangan') }}
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
