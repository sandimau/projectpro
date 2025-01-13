@extends('layouts.app')

@section('title')
    Data Member Gaji
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="mt-2">
            @include('layouts.includes.messages')
        </div>
        <div class="card mt-4">
            <div class="card-header">
                <a href="{{ route('gaji.create', $member->id) }}" class="btn btn-success text-white me-1"><i
                        class='bx bxs-edit'></i> add gaji</a>
            </div>
            <div class="card-body">
                {{ $gajis->links() }}
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>
                                    Tanggal
                                </th>
                                <th>
                                    Bagian
                                </th>
                                <th>
                                    Level
                                </th>
                                <th>
                                    Performance
                                </th>
                                <th>
                                    Transportasi
                                </th>
                                <th>
                                    Tunjangan Lain
                                </th>
                                <th>
                                    Nilai Tunjangan Lain
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($gajis as $item)
                                <tr>
                                    <td>{{ $item->created_at }}</td>
                                    <td>{{ $item->bagian->nama }}</td>
                                    <td>{{ $item->level->nama }}</td>
                                    <td>{{ $item->performance }}</td>
                                    <td>{{ $item->transportasi == 1 ? 'ya' : 'tidak' }}</td>
                                    <td>{{ $item->lain_lain }}</td>
                                    <td>{{ $item->jumlah_lain }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
