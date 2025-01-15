@extends('layouts.app')

@section('title')
    Data Aset Produk
@endsection

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4>Aset Produk</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Kategori Utama</th>
                            <th>Kategori</th>
                            <th>Model</th>
                            <th class="text-end">Aset</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalAset = 0;
                            $currentKategori = '';
                        @endphp

                        @foreach($asets as $aset)
                            @php
                                $nilaiAset = $aset->saldo * $aset->harga;
                                $totalAset += $nilaiAset;
                            @endphp
                            <tr>
                                <td>
                                    @if($currentKategori != $aset->namaKategoriUtama)
                                        {{ $aset->namaKategoriUtama }}
                                        @php $currentKategori = $aset->namaKategoriUtama @endphp
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('produk.asetDetail', $aset->kategori_id) }}">
                                        {{ $aset->namaKategori }}
                                    </a>
                                </td>
                                <td>{{ $aset->namaProdukModel }}</td>
                                <td class="text-end">{{ number_format($nilaiAset, 2, ',', '.') }}</td>
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
