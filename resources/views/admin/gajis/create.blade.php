@extends('layouts.app')

@section('title')
    Create Gaji
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            tambah gaji
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('gaji.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="member_id" value="{{ $member->id }}">
                <div class="form-group mb-3">
                    <label for="bagian_id">Bagian</label>
                    <select class="form-select {{ $errors->has('bagian_id') ? 'is-invalid' : '' }}" aria-label="Default select example" name="bagian_id"
                        id="bagian_id">
                        @foreach ($bagians as $id => $entry)
                            <option value="{{ $id }}" {{ ($gaji ? $gaji->bagian->id == $id : null) ? 'selected' : '' }}>
                                {{ $entry }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('bagian_id'))
                        <div class="invalid-feedback">
                            {{ $errors->first('bagian_id') }}
                        </div>
                    @endif
                </div>
                <div class="form-group mb-3">
                    <label for="level_id">level</label>
                    <select class="form-select {{ $errors->has('level_id') ? 'is-invalid' : '' }}" aria-label="Default select example" name="level_id"
                        id="level_id">
                        @foreach ($levels as $id => $entry)
                            <option value="{{ $id }}" {{ ($gaji ? $gaji->level->id == $id : null) ? 'selected' : '' }}>
                                {{ $entry }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('level_id'))
                        <div class="invalid-feedback">
                            {{ $errors->first('level_id') }}
                        </div>
                    @endif
                </div>
                <div class="form-group mb-3">
                    <label for="performance">performance</label>
                    <select class="form-select {{ $errors->has('performance') ? 'is-invalid' : '' }}" aria-label="Default select example"
                        name="performance" id="performance">
                        <option>pilih performance</option>
                        <option value="0" {{ ($gaji ? $gaji->performance == 0 : null) ? 'selected' : '' }}>0</option>
                        <option value="1" {{ ($gaji ? $gaji->performance == 1 : null) ? 'selected' : '' }}>1</option>
                        <option value="2" {{ ($gaji ? $gaji->performance == 2 : null) ? 'selected' : '' }}>2</option>
                        <option value="3" {{ ($gaji ? $gaji->performance == 3 : null) ? 'selected' : '' }}>3</option>
                        <option value="4" {{ ($gaji ? $gaji->performance == 4 : null) ? 'selected' : '' }}>4</option>
                        <option value="5" {{ ($gaji ? $gaji->performance == 5 : null) ? 'selected' : '' }}>5</option>
                    </select>
                    @if ($errors->has('performance'))
                        <div class="invalid-feedback">
                            {{ $errors->first('performance') }}
                        </div>
                    @endif
                </div>
                <div class="form-group mb-3">
                    <div class="form-check form-switch">
                        <input name="transportasi" class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" {{ ($gaji ? $gaji->transportasi : null) ? 'checked' : '' }} >
                        <label class="form-check-label" for="flexSwitchCheckDefault">Transportasi</label>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label for="lain_lain">Tunjangan Lain</label>
                    <input class="form-control {{ $errors->has('lain_lain') ? 'is-invalid' : '' }}" type="text"
                        name="lain_lain" id="lain_lain" value="{{ old('lain_lain', $gaji ? $gaji->lain_lain : null) }}">
                    @if ($errors->has('lain_lain'))
                        <div class="invalid-feedback">
                            {{ $errors->first('lain_lain') }}
                        </div>
                    @endif
                </div>
                <div class="form-group mb-3">
                    <label for="jumlah_lain">Nilai Tunjangan Lain</label>
                    <input class="form-control {{ $errors->has('jumlah_lain') ? 'is-invalid' : '' }}" type="number"
                        name="jumlah_lain" id="jumlah_lain" value="{{ old('jumlah_lain', $gaji ? $gaji->jumlah_lain : null) }}">
                    @if ($errors->has('jumlah_lain'))
                        <div class="invalid-feedback">
                            {{ $errors->first('jumlah_lain') }}
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <button class="btn btn-danger" type="submit">
                        {{ trans('save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
