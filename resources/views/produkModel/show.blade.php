@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Detail Produk</h5>
            <a href="{{ route('produkModel.index', ['kategori_id' => $produkModel->kategori_id]) }}"
                class="btn btn-secondary">Kembali</a>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="detail-tab" data-bs-toggle="tab" data-bs-target="#detail"
                        type="button" role="tab" aria-controls="detail" aria-selected="true">Detail</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="varian-tab" data-bs-toggle="tab" data-bs-target="#varian" type="button"
                        role="tab" aria-controls="varian" aria-selected="false">Varian</button>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="detail" role="tabpanel" aria-labelledby="detail-tab">
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <a href="{{ route('produkModel.edit', ['produkModel' => $produkModel->id, 'kategori_id' => $produkModel->kategori_id]) }}"
                                class="btn btn-primary mb-3">Edit Produk</a>
                            {{-- <form action="{{ route('produkModel.destroy', $produkModel->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">Hapus</button>
                            </form> --}}
                            @if ($produkModel->gambar)
                                <a class="test-popup-link" href="{{ asset('uploads/produk/' . $produkModel->gambar) }}">
                                    <img src="{{ asset('uploads/produk/' . $produkModel->gambar) }}" class="img-fluid"
                                        alt="Produk Image">
                                </a>
                            @else
                                <div class="text-center p-4 bg-light">
                                    <span class="text-muted">No image available</span>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <table class="table">
                                <tr>
                                    <th width="200">Produk</th>
                                    <td>{{ $produkModel->nama }}</td>
                                </tr>
                                <tr>
                                    <th>Kategori</th>
                                    <td>{{ $produkModel->kategori->nama }}</td>
                                </tr>
                                <tr>
                                    <th>Harga</th>
                                    <td>Rp {{ number_format($produkModel->harga, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Satuan</th>
                                    <td>{{ $produkModel->satuan }}</td>
                                </tr>
                                <tr>
                                    <th>Status Jual</th>
                                    <td>
                                        @if ($produkModel->jual)
                                            <span class="badge bg-success">Dijual</span>
                                        @else
                                            <span class="badge bg-danger">Tidak Dijual</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status Beli</th>
                                    <td>
                                        @if ($produkModel->beli)
                                            <span class="badge bg-success">Dapat Dibeli</span>
                                        @else
                                            <span class="badge bg-danger">Tidak Dapat Dibeli</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status Stok</th>
                                    <td>
                                        @if ($produkModel->stok)
                                            <span class="badge bg-success">Stok Tersedia</span>
                                        @else
                                            <span class="badge bg-danger">Stok Kosong</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Deskripsi</th>
                                    <td>{{ $produkModel->deskripsi ?? 'Tidak ada deskripsi' }}</td>
                                </tr>
                            </table>


                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="varian" role="tabpanel" aria-labelledby="varian-tab">
                    <div class="mt-3">
                        <a href="{{ route('produks.create', ['produkModel' => $produkModel->id, 'kategori_id' => $produkModel->kategori_id]) }}"
                            class="btn btn-primary mb-3">Tambah Varian</a>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>SKU</th>
                                        <th>Varian</th>
                                        <th>Status</th>
                                        <th>Hpp</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($produkModel->produk as $produk)
                                        <tr>
                                            <td>{{ $produk->id }}</td>
                                            <td>{{ $produk->nama }}</td>
                                            <td>
                                                @if ($produk->status == 1)
                                                    <span class="badge bg-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-danger">Tidak Aktif</span>
                                                @endif
                                            </td>
                                            <td>Rp {{ number_format($produk->hpp, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
