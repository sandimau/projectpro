@extends('layouts.app')

@section('title')
    Kategori List
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Kategoris</h5>
                    </div>
                    <x-crud-create permission="kategori_create" :url="route('kategori.create')" label="Add Kategori" />
                </div>
            </div>
            <div class="card-body">
                @include('layouts.includes.messages')
                <div class="table-responsive">
                    <table class="table table-striped" id="myTable">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Nama</th>
                                @canany(['kategori_edit', 'kategori_delete'])
                                    <th scope="col">actions</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($kategoris as $kategori)
                                <tr>
                                    <td>{{ $kategori->id }}</td>
                                    <td><a href="{{ route('produks.index', $kategori->id) }}">{{ $kategori->nama }}</a></td>
                                    @canany(['kategori_edit', 'kategori_delete'])
                                        <td>
                                            <x-crud-actions
                                                class="d-flex"
                                                edit="kategori_edit"
                                                :edit-url="route('kategori.edit', $kategori->id)"
                                            />
                                        </td>
                                    @endcanany
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
    </script>
@endpush
