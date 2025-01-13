@extends('layouts.app')

@section('title')
    Detail Gajis
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.gaji.title') }}
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.gajis.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th>
                                    {{ trans('cruds.gaji.fields.id') }}
                                </th>
                                <td>
                                    {{ $gaji->id }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.gaji.fields.member') }}
                                </th>
                                <td>
                                    {{ $gaji->member->nama_lengkap ?? '' }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.gaji.fields.gaji') }}
                                </th>
                                <td>
                                    {{ $gaji->gaji }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.gaji.fields.keterangan') }}
                                </th>
                                <td>
                                    {{ $gaji->keterangan }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.gaji.fields.bagian') }}
                                </th>
                                <td>
                                    {{ $gaji->bagian->nama ?? '' }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.gaji.fields.level') }}
                                </th>
                                <td>
                                    {{ $gaji->level->nama ?? '' }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.gaji.fields.transportasi') }}
                                </th>
                                <td>
                                    {{ $gaji->transportasi }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.gaji.fields.performance') }}
                                </th>
                                <td>
                                    {{ $gaji->performance }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.gaji.fields.lain_lain') }}
                                </th>
                                <td>
                                    {{ $gaji->lain_lain }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.gaji.fields.jumlah_lain') }}
                                </th>
                                <td>
                                    {{ $gaji->jumlah_lain }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.gaji.fields.created_at') }}
                                </th>
                                <td>
                                    {{ $gaji->created_at }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.gajis.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
