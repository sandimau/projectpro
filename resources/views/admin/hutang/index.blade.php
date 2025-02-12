@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Hutang Piutang</h5>
                                <h6 class="card-subtitle mb-2 text-muted">Manage your hutang/piutang here.</h6>
                            </div>
                            @can('keuangan')
                                <div>
                                    <a href="{{ route('hutang.create',['jenis' => 'hutang']) }}" class="btn btn-primary">Hutang Baru</a>
                                    <a href="{{ route('hutang.create',['jenis' => 'piutang']) }}" class="btn btn-primary">Piutang Baru</a>
                                </div>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kontak</th>
                                    <th>Jumlah</th>
                                    <th>Jenis</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($hutangs as $hutang)
                                    <tr>
                                        <td>{{ $hutang->tanggal->format('d/m/Y') }}</td>
                                        <td>{{ $hutang->kontak->nama }}</td>
                                        <td>Rp {{ number_format($hutang->jumlah, 0, ',', '.') }}</td>
                                        <td>{{ $hutang->jenis }}</td>
                                        <td>
                                            @if ($hutang->sisa <= 0)
                                                <a href="{{ route('hutang.detail', $hutang) }}" class="btn btn-sm btn-success">
                                                    Lunas
                                                </a>
                                            @else
                                                <a href="{{ route('hutang.bayar', $hutang) }}" class="btn btn-sm btn-warning">
                                                    Belum Lunas
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Tidak ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="mt-3">
                            {{ $hutangs->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
