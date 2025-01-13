@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Daftar Kategori Produk</h5>
        <a href="{{ route('produk-kategori.create', ['kategori_utama_id' => $kategoriUtama?->id]) }}" class="btn btn-primary">Tambah Kategori</a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Kategori Utama</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kategoris as $kategori)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td><a href="{{ route('produkModel.index', ['kategori_id' => $kategori->id]) }}">{{ $kategori->nama }}</a></td>
                    <td>{{ $kategori->kategoriUtama->nama ?? '-' }}</td>
                    <td>
                        <a href="{{ route('produk-kategori.edit', $kategori) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('produk-kategori.destroy', $kategori) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
