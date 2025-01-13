@extends('layouts.app')

@section('title')
Create Kasbon
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Add Kasbon</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("kasbon.update", [$kasbon->id]) }}" enctype="multipart/form-data">
            @method('patch')
            @csrf
            <input type="hidden" name="member_id" value="{{ $kasbon->member()->first()->id }}">
            <div class="form-group">
                <label for="tanggal">Tanggal</label>
                <input class="form-control date {{ $errors->has('tanggal') ? 'is-invalid' : '' }}" type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', $kasbon->tanggal) }}">
                @if($errors->has('tanggal'))
                    <div class="invalid-feedback">
                        {{ $errors->first('tanggal') }}
                    </div>
                @endif
            </div>
            <div class="form-group">
                <label for="keterangan">keterangan</label>
                <textarea class="form-control {{ $errors->has('keterangan') ? 'is-invalid' : '' }}" name="keterangan" id="" cols="30" rows="10">{{ old('keterangan',$kasbon->keterangan) }}</textarea>
                @if($errors->has('keterangan'))
                    <div class="invalid-feedback">
                        {{ $errors->first('keterangan') }}
                    </div>
                @endif
            </div>
            <div class="form-group">
                <label>kasbon/ijin</label>
                <select class="form-select" name="kasbon" name="kasbon">
                    <option>pilih kasbon/ijin</option>
                    <option value="1" {{ $kasbon->kasbon == '1' ? 'selected' : ''  }} >kasbon</option>
                    <option value="0" {{ $kasbon->kasbon == '0' ? 'selected' : ''  }}>ijin</option>
                </select>
                @if($errors->has('kasbon'))
                    <div class="invalid-feedback">
                        {{ $errors->first('kasbon') }}
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
