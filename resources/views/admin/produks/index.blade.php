@extends('layouts.app')

@section('title')
    Data Produks
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Produks</h5>
                        <h6 class="card-subtitle mb-2 text-muted">Manage your produks here.</h6>
                    </div>
                    @can('produk_create')
                        <a href="{{ route('produks.create', $kategori->id) }}" class="btn btn-primary"><i
                                class='bx bx-plus-circle'></i> Add</a>
                    @endcan
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
                                <th scope="col">SKU</th>
                                <th scope="col">Gambar</th>
                                <th scope="col">Nama</th>
                                <th scope="col">Satuan</th>
                                <th scope="col">Harga</th>
                                <th scope="col">jual</th>
                                <th scope="col">beli</th>
                                <th scope="col">stok</th>
                                <th scope="col">action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($produks as $produk)
                                <tr>
                                    <td>{{ $produk->id }}</td>
                                    <td>
                                        @if ($produk->gambar)
                                            <a class="test-popup-link"
                                                href="{{ asset('uploads/produk/' . $produk->gambar) }}">
                                                <img style="height: 60px"
                                                    src="{{ url('uploads/produk/' . $produk->gambar) }}" alt=""
                                                    srcset="">
                                            </a>
                                        @endif
                                    </td>
                                    <td>{{ $produk->nama }}</td>
                                    <td>{{ $produk->satuan }}</td>
                                    <td>{{ $produk->harga }}</td>
                                    <td>
                                        @if ($produk->jual == 1)
                                            <i class='bx bxs-check-square'></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($produk->beli == 1)
                                            <i class='bx bxs-check-square'></i>
                                        @endif
                                    </td>
                                    <td><a href="{{ route('produkStok.index', $produk->id) }}">{{ $produk->lastStok }}</a>
                                    </td>
                                    <td>
                                        <a href="{{ route('produks.edit', $produk->id) }}"
                                            class="btn btn-info btn-sm me-1"><i class='bx bxs-edit'></i> Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
    <script>
        let table = new DataTable('#myTable');
        $(document).ready(function() {
            $('.test-popup-link').magnificPopup({
                type: 'image'
            });
        });
    </script>
@endpush
