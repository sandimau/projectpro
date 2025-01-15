@extends('layouts.app')

@section('title')
    Detail Aset Produk
@endsection

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4>Detail Aset Produk</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama Produk</th>
                            <th>Varian</th>
                            <th>Stok</th>
                            <th class="text-end">Aset</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalAset = 0;
                            $currentProduk = '';
                        @endphp

                        @foreach($asets as $aset)
                            @php
                                $totalAset += $aset->nilai_aset;
                            @endphp
                            <tr>
                                <td>
                                    @if($currentProduk != $aset->namaProduk)
                                        {{ $aset->namaProduk }}
                                        @php $currentProduk = $aset->namaProduk @endphp
                                    @endif
                                </td>
                                <td>{{ $aset->varian }}</td>
                                <td>{{ number_format($aset->stok, 0, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($aset->nilai_aset, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr class="fw-bold">
                            <td colspan="3">Total Aset</td>
                            <td class="text-end">{{ number_format($totalAset, 2, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table td, .table th {
        vertical-align: middle;
    }
</style>
@endpush
