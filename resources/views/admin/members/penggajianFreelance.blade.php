@extends('layouts.app')

@section('title')
    Data Penggajian
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="mt-2">
            @include('layouts.includes.messages')
        </div>
        <div class="card mt-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Penggajian {{ $member->nama_lengkap }}</h5>
                    </div>
                </div>
            </div>
            <div class="card-body">
                {{ $penggajians->links() }}
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>
                                    tanggal
                                </th>
                                <th>
                                    jumlah hari
                                </th>
                                <th>
                                    upah
                                </th>
                                <th>
                                    jumlah lain
                                </th>
                                <th>
                                    ket lain
                                </th>
                                <th>
                                    jam lembur
                                </th>
                                <th>
                                    lembur
                                </th>
                                <th>
                                    total
                                </th>
                                <th>
                                    print
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($penggajians as $item)
                                <tr>
                                    <td>{{ date('d-m-Y', strtotime($item->created_at)) }}</td>
                                    <td>{{ number_format($item->pokok) }}</td>
                                    <td>{{ number_format($member->upah) }}</td>
                                    <td>{{ number_format($item->jumlah_lain) }}</td>
                                    <td>{{ number_format($item->lain_lain) }}</td>
                                    <td>{{ number_format($item->jam_lembur) }}</td>
                                    <td>{{ number_format($item->lembur) }}</td>
                                    <td>{{ number_format($item->total) }}</td>
                                    <td><a href="{{ route('penggajian.slip', $item->id) }}"
                                            class="btn btn-primary btn-sm">slip gaji</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
