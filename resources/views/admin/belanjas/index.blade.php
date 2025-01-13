@extends('layouts.app')

@section('title')
    Data Belanja
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="mt-2">
            @include('layouts.includes.messages')
        </div>
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Belanjas</h5>
                        <h6 class="card-subtitle mb-2 text-muted">Manage your belanja here.</h6>
                    </div>
                    @can('member_create')
                        <a href="{{ route('belanja.create') }}" class="btn btn-primary"><i class='bx bx-plus-circle'></i> Add</a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                {{ $belanjas->links() }}
                <div class="table-responsive">
                    <table class=" table table-bordered table-striped table-hover mt-3">
                        <thead>
                            <tr>
                                <th>tanggal</th>
                                <th>supplier</th>
                                <th>produk</th>
                                <th>nota</th>
                                <th>total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($belanjas as $belanja)
                                <tr data-entry-id="{{ $belanja->id }}">
                                    <td>{{ date('d-m-Y', strtotime(($belanja->created_at)))}}</td>
                                    <td>{{ $belanja->kontak->nama }}</td>
                                    <td><a href="{{ route('belanja.detail',$belanja->id) }}">{{ $belanja->produk }}</a></td>
                                    <td>#{{ $belanja->nota }}</td>
                                    <td>{{ number_format($belanja->total, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
