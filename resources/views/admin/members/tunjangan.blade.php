@extends('layouts.app')

@section('title')
    Data Member Tunjangan
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
                        <h5 class="card-title">Tunjangan</h5>
                        <h6 class="card-subtitle mb-2 text-muted">Manage your tunjangans here.</h6>
                    </div>
                    @can('tunjangan_create')
                        <a href="{{ route('tunjangan.create', $member->id) }}" class="btn btn-primary"><i class='bx bx-plus-circle'></i> tambah</a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                {{ $tunjangans->links() }}
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" >
                        <thead>
                            <tr>
                                <th>
                                    Tanggal
                                </th>
                                <th>
                                    ket
                                </th>
                                <th>
                                    jumlah
                                </th>
                                <th>
                                    saldo
                                </th>
                                <th>
                                    kas
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tunjangans as $item)
                                <tr>
                                    <td>{{ $item->created_at }}</td>
                                    <td>{{ $item->ket }}</td>
                                    <td>{{ number_format($item->jumlah) }}</td>
                                    <td>{{ number_format($item->saldo) }}</td>
                                    <td>{{ $item->akunDetail->nama }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
