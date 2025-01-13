@extends('layouts.app')

@section('title')
    Akun Details | buku besar
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="mt-2">
            @include('layouts.includes.messages')
        </div>
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-lg-6">
                        <h5 class="card-title"><a href="{{ route('akunDetails.index') }}">Kas {{ $akunDetail->nama }}</a> > Buku Besar</h5>
                    </div>
                    <div style="text-align: right" class="col-lg-6">
                        @can('akun_kategori_create')
                            <a href="{{ route('akundetail.transferLain', $akunDetail->id) }}" class="btn btn-success text-white">pemasukan lain</a>
                            <a href="{{ route('akundetail.transfer', $akunDetail->id) }}" class="btn btn-primary">transfer</a>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="card-body">
                {{ $bukubesars->links() }}
                <div class="table-responsive mt-3">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">tanggal</th>
                                <th scope="col">ket</th>
                                <th scope="col">debet</th>
                                <th scope="col">kredit</th>
                                <th scope="col">saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bukubesars as $bukubesar)
                                <tr>
                                    <td>{{ $bukubesar->created_at }}</td>
                                    <td>{{ $bukubesar->ket }}</td>
                                    <td>{{ number_format($bukubesar->debet, 0, ',', '.') }}</td>
                                    <td>{{ number_format($bukubesar->kredit, 0, ',', '.') }}</td>
                                    <td>{{ number_format($bukubesar->saldo, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $bukubesars->links() }}
            </div>
        </div>
    </div>
@endsection
@push('after-scripts')
    <script>
        let table = new DataTable('#myTable');
    </script>
@endpush
