@extends('layouts.app')

@section('title')
    Create Kasbon
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title">add Kasbon</h5>
                </div>
                <a href="{{ route('members.show', $member->id) }}" class="btn btn-primary ">back</a>
            </div>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('kasbon.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="member_id" value="{{ $member->id }}">
                <div class="form-group mb-3">
                    <label for="tanggal">Tanggal</label>
                    <input class="form-control date {{ $errors->has('tanggal') ? 'is-invalid' : '' }}" type="date"
                        name="tanggal" id="tanggal" value="{{ old('tanggal',date('Y-m-d')) }}">
                    @if ($errors->has('tanggal'))
                        <div class="invalid-feedback">
                            {{ $errors->first('tanggal') }}
                        </div>
                    @endif
                </div>
                <div class="form-group mb-3">
                    <label class="required" for="jumlah">jumlah</label>
                    <input class="form-control {{ $errors->has('jumlah') ? 'is-invalid' : '' }}" type="number" name="jumlah" id="jumlah" value="{{ old('jumlah') }}" required>
                    @if($errors->has('jumlah'))
                        <div class="invalid-feedback">
                            {{ $errors->first('jumlah') }}
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <label for="keterangan">keterangan</label>
                    <textarea class="form-control {{ $errors->has('keterangan') ? 'is-invalid' : '' }}" name="keterangan" id=""
                        cols="30" rows="10">{{ old('keterangan', '') }}</textarea>
                    @if ($errors->has('keterangan'))
                        <div class="invalid-feedback">
                            {{ $errors->first('keterangan') }}
                        </div>
                    @endif
                </div>
                <div class="form-group mb-3">
                    <label for="akun_detail_id">kas</label>
                    <select class="form-select {{ $errors->has('akun_detail_id') ? 'is-invalid' : '' }}" aria-label="Default select example" name="akun_detail_id" id="akun_detail_id">
                        @foreach($kas as $id => $entry)
                            <option value="{{ $id }}" {{ old('akun_detail_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('akun'))
                        <div class="invalid-feedback">
                            {{ $errors->first('akun') }}
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
