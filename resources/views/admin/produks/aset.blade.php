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
                            <th class="text-end">Aset</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalAset = 0;
                            $currentKategoriUtama = '';
                        @endphp

                        @foreach($asets as $aset)
                            @php
                                $totalAset += $aset->nilai_aset;
                            @endphp
                            <tr>
                                <td>
                                    @if($currentKategoriUtama !== $aset->namaKategoriUtama)
                                        {{ $aset->namaKategoriUtama }}
                                        @php $currentKategoriUtama = $aset->namaKategoriUtama @endphp
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('produk.asetDetail', $aset->kategori_id) }}">
                                        {{ $aset->namaKategori }}
                                    </a>
                                </td>
                                <td class="text-end">{{ number_format($aset->nilai_aset, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr class="fw-bold">
                            <td colspan="2">Total Aset</td>
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
