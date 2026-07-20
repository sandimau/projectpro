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
            <form method="POST" action="{{ route('po.detail.update', ['po' => $detail->po_id, 'detail' => $detail->id]) }}" enctype="multipart/form-data">
                @method('patch')
                @csrf
                <div class="form-group mb-3">
                    <label for="jumlah">Jumlah</label>
                    <input class="form-control {{ $errors->has('jumlah') ? 'is-invalid' : '' }}" type="number"
                        name="jumlah" id="jumlah" value="{{ old('jumlah', $detail->jumlah) }}">
                    @if ($errors->has('jumlah'))
                        <div class="invalid-feedback">
                            {{ $errors->first('jumlah') }}
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
