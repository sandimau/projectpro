@extends('layouts.app')

@section('title')
Create Cuti
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Add Cuti</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("cuti.update", [$cuti->id]) }}" enctype="multipart/form-data">
            @method('patch')
            @csrf
            <input type="hidden" name="member_id" value="{{ $cuti->member()->first()->id }}">
            <div class="form-group">
                <label for="tanggal">Tanggal</label>
                <input class="form-control date {{ $errors->has('tanggal') ? 'is-invalid' : '' }}" type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', $cuti->tanggal) }}">
                @if($errors->has('tanggal'))
                    <div class="invalid-feedback">
                        {{ $errors->first('tanggal') }}
                    </div>
                @endif
            </div>
            <div class="form-group">
                <label for="keterangan">keterangan</label>
                <textarea class="form-control {{ $errors->has('keterangan') ? 'is-invalid' : '' }}" name="keterangan" id="" cols="30" rows="10">{{ old('keterangan',$cuti->keterangan) }}</textarea>
                @if($errors->has('keterangan'))
                    <div class="invalid-feedback">
                        {{ $errors->first('keterangan') }}
                    </div>
                @endif
            </div>
            <div class="form-group">
                <label>cuti/ijin</label>
                <select class="form-select {{ $errors->has('cuti') ? 'is-invalid' : '' }}" aria-label="Default select example" name="cuti" name="cuti">
                    <option>pilih cuti/ijin</option>
                    <option value="1" {{ $cuti->cuti == '1' ? 'selected' : ''  }} >cuti</option>
                    <option value="0" {{ $cuti->cuti == '0' ? 'selected' : ''  }}>ijin</option>
                </select>
                @if($errors->has('cuti'))
                    <div class="invalid-feedback">
                        {{ $errors->first('cuti') }}
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
