@extends('layouts.app')

@section('title')
Create Ar
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        create ar
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('ars.update',$ar->id) }}" enctype="multipart/form-data">
            @method('patch')
            @csrf
            <div class="form-group mb-3">
                <label for="member_id">member</label>
                <select class="form-select {{ $errors->has('member_id') ? 'is-invalid' : '' }}" aria-label="Default select example" name="member_id" id="member_id">
                    @foreach($members as $id => $entry)
                        <option value="{{ $id }}" {{ $id == $ar->member_id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('member_id'))
                    <div class="invalid-feedback">
                        {{ $errors->first('member_id') }}
                    </div>
                @endif
            </div>
            <div class="form-group mb-3">
                <label class="required" for="kode">kode</label>
                <input class="form-control {{ $errors->has('kode') ? 'is-invalid' : '' }}" type="text" name="kode" id="kode" value="{{ old('kode',$ar->kode) }}" required>
                @if($errors->has('kode'))
                    <div class="invalid-feedback">
                        {{ $errors->first('kode') }}
                    </div>
                @endif
            </div>
            <div class="form-group mb-3">
                <label class="required" for="warna">warna</label>
                <input class="form-control {{ $errors->has('warna') ? 'is-invalid' : '' }}" type="color" name="warna" id="warna" value="{{ old('warna',$ar->warna) }}" required>
                @if($errors->has('warna'))
                    <div class="invalid-feedback">
                        {{ $errors->first('warna') }}
                    </div>
                @endif
            </div>
            <div class="mb-3">
                <label for="formFile" class="form-label">ttd</label>
                <input class="form-control" type="file" id="formFile" name="ttd">
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
