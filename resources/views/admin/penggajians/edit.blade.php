@extends('layouts.app')

@section('title')
Edit Penggajians
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.penggajian.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.penggajians.update", [$penggajian->id]) }}" enctype="multipart/form-data">
            @method('patch')
            @csrf
            <div class="form-group">
                <label for="member_id">{{ trans('cruds.penggajian.fields.member') }}</label>
                <select class="form-select select2 {{ $errors->has('member') ? 'is-invalid' : '' }}" name="member_id" id="member_id">
                    @foreach($members as $id => $entry)
                        <option value="{{ $id }}" {{ (old('member_id') ? old('member_id') : $penggajian->member->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('member'))
                    <div class="invalid-feedback">
                        {{ $errors->first('member') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.penggajian.fields.member_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="bulan">{{ trans('cruds.penggajian.fields.bulan') }}</label>
                <input class="form-control {{ $errors->has('bulan') ? 'is-invalid' : '' }}" type="text" name="bulan" id="bulan" value="{{ old('bulan', $penggajian->bulan) }}">
                @if($errors->has('bulan'))
                    <div class="invalid-feedback">
                        {{ $errors->first('bulan') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.penggajian.fields.bulan_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="tahun">{{ trans('cruds.penggajian.fields.tahun') }}</label>
                <input class="form-control {{ $errors->has('tahun') ? 'is-invalid' : '' }}" type="text" name="tahun" id="tahun" value="{{ old('tahun', $penggajian->tahun) }}">
                @if($errors->has('tahun'))
                    <div class="invalid-feedback">
                        {{ $errors->first('tahun') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.penggajian.fields.tahun_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="jam_lembur">{{ trans('cruds.penggajian.fields.jam_lembur') }}</label>
                <input class="form-control {{ $errors->has('jam_lembur') ? 'is-invalid' : '' }}" type="number" name="jam_lembur" id="jam_lembur" value="{{ old('jam_lembur', $penggajian->jam_lembur) }}" step="1">
                @if($errors->has('jam_lembur'))
                    <div class="invalid-feedback">
                        {{ $errors->first('jam_lembur') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.penggajian.fields.jam_lembur_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="pokok">{{ trans('cruds.penggajian.fields.pokok') }}</label>
                <input class="form-control {{ $errors->has('pokok') ? 'is-invalid' : '' }}" type="number" name="pokok" id="pokok" value="{{ old('pokok', $penggajian->pokok) }}" step="1">
                @if($errors->has('pokok'))
                    <div class="invalid-feedback">
                        {{ $errors->first('pokok') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.penggajian.fields.pokok_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="lembur">{{ trans('cruds.penggajian.fields.lembur') }}</label>
                <input class="form-control {{ $errors->has('lembur') ? 'is-invalid' : '' }}" type="number" name="lembur" id="lembur" value="{{ old('lembur', $penggajian->lembur) }}" step="1">
                @if($errors->has('lembur'))
                    <div class="invalid-feedback">
                        {{ $errors->first('lembur') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.penggajian.fields.lembur_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="kasbon">{{ trans('cruds.penggajian.fields.kasbon') }}</label>
                <input class="form-control {{ $errors->has('kasbon') ? 'is-invalid' : '' }}" type="number" name="kasbon" id="kasbon" value="{{ old('kasbon', $penggajian->kasbon) }}" step="1">
                @if($errors->has('kasbon'))
                    <div class="invalid-feedback">
                        {{ $errors->first('kasbon') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.penggajian.fields.kasbon_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="bonus">{{ trans('cruds.penggajian.fields.bonus') }}</label>
                <input class="form-control {{ $errors->has('bonus') ? 'is-invalid' : '' }}" type="number" name="bonus" id="bonus" value="{{ old('bonus', $penggajian->bonus) }}" step="1">
                @if($errors->has('bonus'))
                    <div class="invalid-feedback">
                        {{ $errors->first('bonus') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.penggajian.fields.bonus_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="total">{{ trans('cruds.penggajian.fields.total') }}</label>
                <input class="form-control {{ $errors->has('total') ? 'is-invalid' : '' }}" type="number" name="total" id="total" value="{{ old('total', $penggajian->total) }}" step="1">
                @if($errors->has('total'))
                    <div class="invalid-feedback">
                        {{ $errors->first('total') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.penggajian.fields.total_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="lama_kerja">{{ trans('cruds.penggajian.fields.lama_kerja') }}</label>
                <input class="form-control {{ $errors->has('lama_kerja') ? 'is-invalid' : '' }}" type="number" name="lama_kerja" id="lama_kerja" value="{{ old('lama_kerja', $penggajian->lama_kerja) }}" step="1">
                @if($errors->has('lama_kerja'))
                    <div class="invalid-feedback">
                        {{ $errors->first('lama_kerja') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.penggajian.fields.lama_kerja_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="bagian">{{ trans('cruds.penggajian.fields.bagian') }}</label>
                <input class="form-control {{ $errors->has('bagian') ? 'is-invalid' : '' }}" type="number" name="bagian" id="bagian" value="{{ old('bagian', $penggajian->bagian) }}" step="1">
                @if($errors->has('bagian'))
                    <div class="invalid-feedback">
                        {{ $errors->first('bagian') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.penggajian.fields.bagian_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="performance">{{ trans('cruds.penggajian.fields.performance') }}</label>
                <input class="form-control {{ $errors->has('performance') ? 'is-invalid' : '' }}" type="number" name="performance" id="performance" value="{{ old('performance', $penggajian->performance) }}" step="1">
                @if($errors->has('performance'))
                    <div class="invalid-feedback">
                        {{ $errors->first('performance') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.penggajian.fields.performance_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="transportasi">{{ trans('cruds.penggajian.fields.transportasi') }}</label>
                <input class="form-control {{ $errors->has('transportasi') ? 'is-invalid' : '' }}" type="number" name="transportasi" id="transportasi" value="{{ old('transportasi', $penggajian->transportasi) }}" step="1">
                @if($errors->has('transportasi'))
                    <div class="invalid-feedback">
                        {{ $errors->first('transportasi') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.penggajian.fields.transportasi_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="komunikasi">{{ trans('cruds.penggajian.fields.komunikasi') }}</label>
                <input class="form-control {{ $errors->has('komunikasi') ? 'is-invalid' : '' }}" type="number" name="komunikasi" id="komunikasi" value="{{ old('komunikasi', $penggajian->komunikasi) }}" step="1">
                @if($errors->has('komunikasi'))
                    <div class="invalid-feedback">
                        {{ $errors->first('komunikasi') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.penggajian.fields.komunikasi_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="kehadiran">{{ trans('cruds.penggajian.fields.kehadiran') }}</label>
                <input class="form-control {{ $errors->has('kehadiran') ? 'is-invalid' : '' }}" type="number" name="kehadiran" id="kehadiran" value="{{ old('kehadiran', $penggajian->kehadiran) }}" step="1">
                @if($errors->has('kehadiran'))
                    <div class="invalid-feedback">
                        {{ $errors->first('kehadiran') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.penggajian.fields.kehadiran_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="jumlah_lain">{{ trans('cruds.penggajian.fields.jumlah_lain') }}</label>
                <input class="form-control {{ $errors->has('jumlah_lain') ? 'is-invalid' : '' }}" type="number" name="jumlah_lain" id="jumlah_lain" value="{{ old('jumlah_lain', $penggajian->jumlah_lain) }}" step="1">
                @if($errors->has('jumlah_lain'))
                    <div class="invalid-feedback">
                        {{ $errors->first('jumlah_lain') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.penggajian.fields.jumlah_lain_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="lain_lain">{{ trans('cruds.penggajian.fields.lain_lain') }}</label>
                <input class="form-control {{ $errors->has('lain_lain') ? 'is-invalid' : '' }}" type="text" name="lain_lain" id="lain_lain" value="{{ old('lain_lain', $penggajian->lain_lain) }}">
                @if($errors->has('lain_lain'))
                    <div class="invalid-feedback">
                        {{ $errors->first('lain_lain') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.penggajian.fields.lain_lain_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="akun_detail_id">{{ trans('cruds.penggajian.fields.akun_detail') }}</label>
                <select class="form-select select2 {{ $errors->has('akun_detail') ? 'is-invalid' : '' }}" name="akun_detail_id" id="akun_detail_id">
                    @foreach($akun_details as $id => $entry)
                        <option value="{{ $id }}" {{ (old('akun_detail_id') ? old('akun_detail_id') : $penggajian->akun_detail->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('akun_detail'))
                    <div class="invalid-feedback">
                        {{ $errors->first('akun_detail') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.penggajian.fields.akun_detail_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
