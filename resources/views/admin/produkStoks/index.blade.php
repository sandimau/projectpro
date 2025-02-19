@extends('layouts.app')

@section('title')
    Data produk stoks
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title"><a href="{{route('produkModel.index', $produk->produkModel->kategori_id)}}">{{ $produk->namaLengkap }}</a></h5>
                    </div>
                    <div style="text-align: right">
                        @can('kontak_create')
                            <a href="{{ route('produkStok.create', $produk->id) }}" class="btn btn-primary mb-2">opname</a>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="mt-2">
                    @include('layouts.includes.messages')
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <th>kode</th>
                                <th>hpp</th>
                                <th>tambah</th>
                                <th>kurang</th>
                                <th>saldo</th>
                                <th>user</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($produkStoks as $stok)
                                <tr>
                                    <td>{{ $stok->created_at->format('d-m-Y') }}</td>
                                    <td>{{ $stok->keterangan }}</td>
                                    <td>{{ $stok->kode }}</td>
                                    <td>{{ $stok->hpp }}</td>
                                    <td>{{ $stok->tambah }}</td>
                                    <td>{{ $stok->kurang }}</td>
                                    <td>{{ $stok->saldo }}</td>
                                    <td>{{ $stok->user ? $stok->user->name : null }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
