@extends('layouts.app')

@section('title')
    Akun Kategori List
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Akuns Kategori</h5>
                    </div>
                    <x-crud-create permission="akun_kategori_create" :url="route('akunKategoris.create')" label="Add akun kategori" />
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
                                <th scope="col">Akun</th>
                                @canany(['akun_kategori_edit', 'akun_kategori_delete'])
                                    <th scope="col">actions</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($akunKategoris as $akun)
                                <tr>
                                    <td>{{ $akun->id }}</td>
                                    <td>{{ $akun->nama }}</td>
                                    <td>{{ $akun->akun->nama }}</td>
                                    @canany(['akun_kategori_edit', 'akun_kategori_delete'])
                                        <td>
                                            <x-crud-actions
                                                class="d-flex"
                                                edit="akun_kategori_edit"
                                                delete="akun_kategori_delete"
                                                :edit-url="route('akunKategoris.edit', $akun->id)"
                                                :delete-url="route('akunKategoris.destroy', $akun->id)"
                                                confirm="Are you sure?"
                                                delete-label="delete"
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
