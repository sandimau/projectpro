@extends('layouts.app')

@section('title')
    AR List
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Ars</h5>
                    </div>
                    <x-crud-create permission="ar_create" :url="route('ars.create')" label="Add ar" />
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
                                <th scope="col">Kode</th>
                                <th scope="col">warna</th>
                                <th scope="col">ttd</th>
                                @canany(['ar_edit', 'ar_delete'])
                                    <th scope="col">actions</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ars as $ar)
                                <tr>
                                    <td>{{ $ar->id }}</td>
                                    <td>{{ $ar->member->nama_lengkap }}</td>
                                    <td>{{ $ar->kode }}</td>
                                    <td style="background-color: {{ $ar->warna }}; color: #fff;">warna</td>
                                    <td>
                                        @if ($ar->ttd)
                                            <img style="height: 60px" src="{{ url('uploads/ttd/' . $ar->ttd) }}"
                                                alt="" srcset="">
                                        @endif
                                    </td>
                                    @canany(['ar_edit', 'ar_delete'])
                                        <td>
                                            <x-crud-actions
                                                class="d-flex"
                                                edit="ar_edit"
                                                delete="ar_delete"
                                                :edit-url="route('ars.edit', $ar->id)"
                                                :delete-url="route('ars.destroy', $ar->id)"
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
