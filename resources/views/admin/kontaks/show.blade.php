@extends('layouts.app')

@section('title')
    Detail Konsumens
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            Detail Kontak
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group mb-3">
                    <a class="btn btn-success" href="{{ route('kontaks.index') }}">
                        back
                    </a>
                    @can('kontak_edit')
                        <a class="btn btn-primary" href="{{ route('kontaks.edit', $kontak->id) }}">
                            edit
                        </a>
                    @endcan
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th>
                                    Id
                                </th>
                                <td>
                                    {{ $kontak->id }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    Nama
                                </th>
                                <td>
                                    {{ $kontak->nama }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    Kontak
                                </th>
                                <td>
                                    {{ $kontak->kontak }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    No Telp
                                </th>
                                <td>
                                    {{ $kontak->noTelp }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    Email
                                </th>
                                <td>
                                    {{ $kontak->email }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    Alamat
                                </th>
                                <td>
                                    {{ $kontak->alamat }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    Customer Service
                                </th>
                                <td>
                                    @if ($kontak->ar)
                                        {{ $kontak->ar->member->nama_lengkap }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    Jenis
                                </th>
                                <td>
                                    {{ $kontak->konsumen === 1 ? 'konsumen' : '' }}
                                    {{ $kontak->supplier === 1 ? 'supplier' : '' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
