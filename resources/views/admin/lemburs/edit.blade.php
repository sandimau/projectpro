@extends('layouts.app')

@section('title')
Create lembur
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Edit lembur</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("lembur.update", [$lembur->id]) }}" enctype="multipart/form-data">
            @method('patch')
            @csrf
            <input type="hidden" name="member_id" value="{{ $lembur->member()->first()->id }}">
                <div class="form-group">
                    <label for="bulan">bulan</label>
                    <select class="form-select {{ $errors->has('bulan') ? 'is-invalid' : '' }}" aria-label="Default select example" name="bulan" name="bulan">
                        <option>pilih bulan</option>
                        @foreach ($bulans as $key => $bulan)
                            <option {{ $lembur->bulan === $bulan ? 'selected' : '' }} value="{{ $key }}">{{ $bulan }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('bulan'))
                        <div class="invalid-feedback">
                            {{ $errors->first('bulan') }}
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <label for="jam">jam</label>
                    <input type="number" class="form-control" name="jam" value="{{ old('jam',$lembur->jam) }}" >
                    @if ($errors->has('jam'))
                        <div class="invalid-feedback">
                            {{ $errors->first('jam') }}
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <label for="keterangan">keterangan</label>
                    <textarea class="form-control {{ $errors->has('keterangan') ? 'is-invalid' : '' }}" name="keterangan" id=""
                        cols="30" rows="10">{{ old('keterangan',$lembur->keterangan) }}</textarea>
                    @if ($errors->has('keterangan'))
                        <div class="invalid-feedback">
                            {{ $errors->first('keterangan') }}
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
