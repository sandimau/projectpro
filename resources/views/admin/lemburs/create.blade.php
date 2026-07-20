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
                    <label for="jam">jam</label>
                    <select class="form-select {{ $errors->has('jam') ? 'is-invalid' : '' }}" name="jam" id="jam">
                        <option value="">pilih jam</option>
                        @foreach ([0.5, 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5, 5.5, 6] as $jam)
                            <option value="{{ $jam }}" {{ old('jam', '') == $jam ? 'selected' : '' }}>{{ $jam }} jam</option>
                        @endforeach
                    </select>
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
