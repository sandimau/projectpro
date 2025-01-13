@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><a href="{{ route('produk-kategori-utama.index') }}"
                        class="text-decoration-none text-primary">{{ $kategori->kategoriUtama->nama }}</a> > <a
                        href="{{ route('produk-kategori.index', $kategori->kategoriUtama->id) }}"
                        class="text-decoration-none text-primary">{{ $kategori->nama }}</a> > Model Produk</h5>
                <a href="{{ route('produkModel.create', ['kategori_id' => $kategori->id]) }}" class="btn btn-primary">Tambah
                    Produk</a>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Gambar</th>
                                <th>Nama</th>
                                <th>varian</th>
                                <th>Satuan</th>
                                <th>Harga Jual</th>
                                <th>hpp</th>
                                <th>stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($produks as $produk)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        @if ($produk->gambar)
                                            <a class="test-popup-link"
                                                href="{{ asset('uploads/produk/' . $produk->gambar) }}">
                                                <img style="height: 60px"
                                                    src="{{ url('uploads/produk/' . $produk->gambar) }}" alt=""
                                                    srcset="">
                                            </a>
                                        @else
                                            <span class="text-muted">No image</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a
                                            href="{{ route('produkModel.edit', ['produkModel' => $produk->id, 'kategori_id' => $kategori->id]) }}">{{ $produk->nama }}</a>
                                    </td>
                                    <td>Rp {{ number_format($produk->harga, 0, ',', '.') }}</td>
                                    <td>{{ $produk->satuan }}</td>
                                    <td>{{ $produk->kategori->nama ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
