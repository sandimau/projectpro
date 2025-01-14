@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Stok Produk</h4>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
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
                                @forelse($produks as $index => $produk)
                                    <tr>
                                        <td>{{ $produk->created_at->format('d-m-Y') }}</td>
                                        <td>{{ $produk->keterangan }}</td>
                                        <td>{{ $produk->kode }}</td>
                                        <td>{{ $produk->hpp }}</td>
                                        <td>{{ $produk->tambah }}</td>
                                        <td>{{ $produk->kurang }}</td>
                                        <td>{{ $produk->saldo }}</td>
                                        <td>{{ $produk->user ? $produk->user->name : null }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada data stok produk</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
