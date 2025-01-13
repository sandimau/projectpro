@extends('layouts.app')

@section('title')
Detail Produk Stok
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.produkStok.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.produk-stoks.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.produkStok.fields.id') }}
                        </th>
                        <td>
                            {{ $produkStok->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.produkStok.fields.tanggal') }}
                        </th>
                        <td>
                            {{ $produkStok->tanggal }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.produkStok.fields.tambah') }}
                        </th>
                        <td>
                            {{ $produkStok->tambah }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.produkStok.fields.kurang') }}
                        </th>
                        <td>
                            {{ $produkStok->kurang }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.produkStok.fields.saldo') }}
                        </th>
                        <td>
                            {{ $produkStok->saldo }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.produkStok.fields.keterangan') }}
                        </th>
                        <td>
                            {{ $produkStok->keterangan }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.produkStok.fields.kode') }}
                        </th>
                        <td>
                            {{ $produkStok->kode }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.produkStok.fields.order') }}
                        </th>
                        <td>
                            {{ $produkStok->order->total ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.produkStok.fields.created_at') }}
                        </th>
                        <td>
                            {{ $produkStok->created_at }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
