@extends('layouts.app')

@section('title')
    Create lembur
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title">add Lembur</h5>
                </div>
                <a href="{{ route('members.show', $member->id) }}" class="btn btn-primary ">back</a>
            </div>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('lembur.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="member_id" value="{{ $member->id }}">
                <div class="form-group">
                    <label for="bulan">bulan</label>
                    <select class="form-select {{ $errors->has('bulan') ? 'is-invalid' : '' }}" aria-label="Default select example" name="bulan" name="bulan">
                        <option>pilih bulan</option>
                        @foreach ($bulans as $key => $bulan)
                            <option value="{{ $key }}">{{ $bulan }}</option>
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
                    <input type="number" class="form-control" name="jam" >
                    @if ($errors->has('jam'))
                        <div class="invalid-feedback">
                            {{ $errors->first('jam') }}
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
                <div class="form-group">
                    <button class="btn btn-primary mt-4" type="submit">
                        save
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
