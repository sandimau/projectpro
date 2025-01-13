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
                        <h5 class="card-title">Produk Stoks > {{ $produk->nama }}</h5>
                        <h6 class="card-subtitle mb-2 text-muted">Manage your produk stoks here.</h6>
                    </div>
                    <div style="text-align: right">
                        <a href="{{ route('produks.index', $produk->kategori_id) }}" class="btn btn-secondary mb-2">back</a>
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
                                <th scope="col">tanggal</th>
                                <th scope="col">keterangan</th>
                                <th scope="col">penambahan</th>
                                <th scope="col">pengurangan</th>
                                <th scope="col">stok akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($produkStoks as $stok)
                                <tr>
                                    <td>{{ $stok->tanggal }}</td>
                                    <td>{{ $stok->keterangan }}</td>
                                    <td>{{ number_format($stok->tambah) }}</td>
                                    <td>{{ number_format($stok->kurang) }}</td>
                                    <td>{{ number_format($stok->saldo) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
