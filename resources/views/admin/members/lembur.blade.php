@extends('layouts.app')

@section('title')
    Data Member Lembur
@endsection

@section('content')
    <div class="bg-light rounded">
        @include('layouts.includes.messages')
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Lembur</h5>
                    </div>
                    @can('lembur_create')
                        <a href="{{ route('lembur.create', $member->id) }}" class="popup btn btn-primary"><i
                                class='bx bx-plus-circle'></i> tambah</a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                {{ $lemburs->links() }}
                <div class="table-responsive">
                    <table class=" table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>
                                    Tahun
                                </th>
                                <th>
                                    bulan
                                </th>
                                <th>
                                    jam
                                </th>
                                <th>
                                    keterangan
                                </th>
                                <th>
                                    dibayar
                                </th>
                                <th>
                                    status
                                </th>
                                @can('lembur_edit')
                                    <th>
                                        actions
                                    </th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lemburs as $item)
                                <tr>
                                    <td>{{ $item->tahun }}</td>
                                    <td>{{ $item->bulan }}</td>
                                    <td>{{ $item->jam }}</td>
                                    <td>{{ $item->keterangan }}</td>
                                    <td>{{ $item->dibayar }}</td>
                                    <td>
                                        @can('penggajian_access')
                                            @if ($item->status == 'waiting')
                                                <a href="{{ route('lembur.approve', $item->id) }}"
                                                    class="popup btn btn-warning btn-sm">approved</a>
                                                <a href="{{ route('lembur.reject', $item->id) }}"
                                                    class="popup btn btn-danger btn-sm">reject</a>
                                            @elseif ($item->status == 'approved')
                                                <span class="badge bg-success">{{ $item->status }}</span>
                                            @else
                                                <span class="badge bg-danger">rejected</span>
                                            @endif
                                        @elsecan('order_access')
                                            <span class="badge bg-info">{{ $item->status }}</span>
                                        @endif

                                    </td>
                                    @can('lembur_edit')
                                        <td>
                                            <div class="d-flex">
                                                @if (!in_array($item->status, ['approved', 'rejected']))
                                                    <a href="{{ route('lembur.edit', $item->id) }}"
                                                        class="popup btn btn-info btn-sm me-1"><i class='bx bxs-edit'></i>
                                                        Edit</a>
                                                @endif
                                            </div>
                                        </td>
                                    @endcan
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
