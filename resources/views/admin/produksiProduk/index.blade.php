@extends('layouts.app')

@section('title')
    Proses Produksi
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Proses Produksi</h5>
                    </div>
                    <a href="{{ route('produksi.create') }}" class="btn btn-primary">Tambah Produk</a>
                </div>
            </div>
            <div class="card-body">
                <div class="mt-2">
                    @include('layouts.includes.messages')
                </div>

                <!-- Tab Navigation -->
                <ul class="nav nav-tabs" id="produksiTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="semua-tab" data-bs-toggle="tab" data-bs-target="#semua" type="button" role="tab">Semua</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="selesai-tab" data-bs-toggle="tab" data-bs-target="#selesai" type="button" role="tab">Selesai</button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content mt-3" id="produksiTabsContent">
                    <!-- Tab Semua -->
                    <div class="tab-pane fade show active" id="semua" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-striped" id="myTable">
                                <thead>
                                    <tr>
                                        <th scope="col">Tanggal</th>
                                        <th scope="col">Produk</th>
                                        <th scope="col">Target Produksi</th>
                                        <th scope="col">Total Biaya</th>
                                        <th scope="col">Keterangan</th>
                                        <th scope="col">User</th>
                                        <th scope="col">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($produksis->where('status', '!=', 'finish') as $produksi)
                                        <tr>
                                            <td>{{ $produksi->created_at }}</td>
                                            <td><a href="{{ route('produksi.show', $produksi->id) }}">{{ $produksi->produk->namaLengkap }}</a></td>
                                            <td>{{ $produksi->target }}</td>
                                            <td>{{ $produksi->biaya }}</td>
                                            <td>{{ $produksi->keterangan }}</td>
                                            <td>{{ $produksi->user }}</td>
                                            <td>{{ $produksi->status }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab Selesai -->
                    <div class="tab-pane fade" id="selesai" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-striped" id="selesaiTable">
                                <thead>
                                    <tr>
                                        <th scope="col">Tanggal</th>
                                        <th scope="col">Produk</th>
                                        <th scope="col">Target Produksi</th>
                                        <th scope="col">Total Biaya</th>
                                        <th scope="col">Keterangan</th>
                                        <th scope="col">User</th>
                                        <th scope="col">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($produksis->where('status', 'finish') as $produksi)
                                        <tr>
                                            <td>{{ $produksi->created_at }}</td>
                                            <td><a href="{{ route('produksi.show', $produksi->id) }}">{{ $produksi->produk->namaLengkap }}</a></td>
                                            <td>{{ $produksi->target }}</td>
                                            <td>{{ $produksi->biaya }}</td>
                                            <td>{{ $produksi->keterangan }}</td>
                                            <td>{{ $produksi->user }}</td>
                                            <td>{{ $produksi->status }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable();
            $('#dalamProsesTable').DataTable();
            $('#selesaiTable').DataTable();
        });
    </script>
    @endpush
@endsection
