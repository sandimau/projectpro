@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-grid gap-2 d-md-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Detail Riwayat Belanja</h4>
                        <div class="card-tools">
                            <a href="{{ route('produkModel.index', ['kategori_id' => $produk->produkModel->kategori_id]) }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Product Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Informasi Produk</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="150">Nama Produk</th>
                                    <td>{{ $produk->nama }}</td>
                                </tr>
                                <tr>
                                    <th>Model</th>
                                    <td>{{ $produk->produkModel->nama }}</td>
                                </tr>
                                <tr>
                                    <th>Kategori</th>
                                    <td>{{ $produk->produkModel->kategori->nama }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Purchase History -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Jumlah</th>
                                    <th>Harga Satuan</th>
                                    <th>Total</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($belanjas as $index => $belanja)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $belanja->created_at->format('d-m-Y') }}</td>
                                    <td>{{ number_format($belanja->jumlah) }}</td>
                                    <td>Rp {{ number_format($belanja->harga) }}</td>
                                    <td>Rp {{ number_format($belanja->jumlah * $belanja->harga) }}</td>
                                    <td>{{ $belanja->keterangan }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data belanja</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-right">Total Belanja:</th>
                                    <th colspan="3">Rp {{ number_format($belanjas->sum(function($item) { return $item->jumlah * $item->harga; })) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
