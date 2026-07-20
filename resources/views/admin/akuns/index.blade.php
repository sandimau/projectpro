@extends('layouts.app')

@section('title')
    Akun List
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Akuns</h5>
                    </div>
                    <x-crud-create permission="akun_create" :url="route('akuns.create')" label="Add akun" />
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
                                @canany(['akun_edit', 'akun_delete'])
                                    <th scope="col">actions</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($akuns as $akun)
                                <tr>
                                    <td>{{ $akun->id }}</td>
                                    <td>{{ $akun->nama }}</td>
                                    @canany(['akun_edit', 'akun_delete'])
                                        <td>
                                            <x-crud-actions
                                                class="d-flex"
                                                edit="akun_edit"
                                                delete="akun_delete"
                                                :edit-url="route('akuns.edit', $akun->id)"
                                                :delete-url="route('akuns.destroy', $akun->id)"
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
