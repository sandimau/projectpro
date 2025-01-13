@extends('layouts.app')

@section('title')
    Data Kontaks
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Kontaks</h5>
                        <h6 class="card-subtitle mb-2 text-muted">Manage your kontaks here.</h6>
                    </div>
                    @can('kontak_create')
                        <a href="{{ route('kontaks.create') }}" class="btn btn-primary">Add kontaks</a>
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
                                <th scope="col">perusahaan</th>
                                <th scope="col">kontak</th>
                                <th scope="col">No Telp</th>
                                <th scope="col">Lama Gabung(bln)</th>
                                <th scope="col">Order Terakhir(bln)</th>
                                <th scope="col">Total Omzet</th>
                                <th scope="col">Omzet/Bln</th>
                                <th scope="col">Jenis</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($kontaks as $kontak)
                                <tr>
                                    <td><a href="{{ route('kontaks.show',$kontak->id) }}">{{ $kontak->nama }}</a></td>
                                    <td>{{ $kontak->kontak }}</td>
                                    <td>{{ $kontak->noTelp }}</td>
                                    <td>{{ $kontak->bergabung }}</td>
                                    <td>{{ $kontak->lastOrder }}</td>
                                    <td>{{ number_format($kontak->allOmzet) }}</td>
                                    <td>{{ number_format($kontak->monthOmzet) }}</td>
                                    <td>{!! html_entity_decode($kontak->jenis) !!}</td>
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
