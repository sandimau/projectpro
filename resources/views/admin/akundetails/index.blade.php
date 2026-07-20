@extends('layouts.app')

@section('title')
    Akun Details List
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Akun</h5>
                    </div>
                    <x-crud-create permission="akun_detail_create" :url="route('akunDetails.create')" label="Add akun" />
                </div>
            </div>
            <div class="card-body">
                @include('layouts.includes.messages')
                <div class="table-responsive">
                    <table class="table table-striped" id="myTable">
                        <thead>
                            <tr>
                                <th scope="col">id</th>
                                <th scope="col">nama</th>
                                <th scope="col">kategori</th>
                                <th scope="col">saldo</th>
                                @canany(['akun_detail_edit', 'akun_detail_delete'])
                                    <th scope="col">actions</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($akunDetails as $akun)
                                <tr>
                                    <td>{{ $akun->id }}</td>
                                    <td><a href="{{ route('akundetail.bukubesar', $akun->id) }}">{{ $akun->nama }}</a></td>
                                    <td>{{ $akun->akun_kategori->nama }}</td>
                                    <td>{{ number_format($akun->saldo) }}</td>
                                    @canany(['akun_detail_edit', 'akun_detail_delete'])
                                        <td>
                                            <x-crud-actions
                                                class="d-flex"
                                                edit="akun_detail_edit"
                                                :edit-url="route('akunDetails.edit', $akun->id)"
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
