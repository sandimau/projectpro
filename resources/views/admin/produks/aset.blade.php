@extends('layouts.app')

@section('title')
    Data Asets
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Asets</h5>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="mt-2">
                    @include('layouts.includes.messages')
                </div>
                <div class="table-responsive">
                    <table class="table table-striped" id="myTable">
                        <thead>
                            <tr>
                                <th scope="col">Produk</th>
                                <th scope="col">Stok</th>
                                <th scope="col" style="text-align: right;">Harga Beli</th>
                                <th scope="col" style="text-align: right;">Aset</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($asets as $aset)
                                <tr>
                                    <td>{{ $aset->nama }}</td>
                                    <td>{{ $aset->saldo }}</td>
                                    <td style="text-align: right;">
                                        {{ number_format($aset->harga_beli, 0, ',', '.') }}</td>
                                    <td style="text-align: right;">
                                        {{ number_format($aset->saldo * $aset->harga_beli, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
