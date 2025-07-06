@extends('layouts.app')

@section('title')
    Detail PO
@endsection

@section('content')
    <div class="bg-light rounded">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page"> <a href="{{ route('po.index') }}">PO</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail PO</li>
            </ol>
        </nav>
        @include('layouts.includes.messages')
        <div class="row">
        </div>
    </div>
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Produk</span>
            @if ($po->status == 'proses')
                <a href="{{ route('po.detail.create', $po->id) }}" class="btn btn-success rounded-pill text-white">
                    <i class='bx bx-plus-circle'></i> tambah data
                </a>
            @endif
        </div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>produk</th>
                        <th>jumlah</th>
                        <th>yg belum</th>
                        @if ($po->status == 'proses')
                            <th>action</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($po->poDetail as $detail)
                        <tr>
                            <td>{{ $detail->produk->namaLengkap }}</td>
                            <td>{{ $detail->jumlah }}</td>
                            <td>{{ $detail->jumlah - $detail->jumlahKedatangan }}</td>
                            @if ($po->status == 'proses')
                                <td>
                                    <div class="d-flex">
                                        <a href="{{ route('po.detail.edit', ['po' => $po->id, 'detail' => $detail->id]) }}"
                                            class="btn btn-primary rounded-pill text-white me-1">
                                            <i class="bx bxs-edit"></i> edit
                                        </a>
                                        @if ($detail->jumlah - $detail->jumlahKedatangan > 0)
                                            <form
                                                action="{{ route('po.detail.destroy', ['po' => $po->id, 'detail' => $detail->id]) }}"
                                                method="post">
                                                {{ csrf_field() }}
                                                {{ method_field('delete') }}
                                                <button type="submit" onclick="return confirm('Apakah anda yakin?')"
                                                    class="btn btn-danger rounded-pill text-white">
                                                    <i class="bx bxs-trash"></i> hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Deposit</span>
            @if ($po->status == 'proses')
                <a href="{{ route('po.deposit', $po->id) }}" class="btn btn-success rounded-pill text-white">
                    <i class='bx bx-plus-circle'></i> tambah data
                </a>
            @endif
        </div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>tgl</th>
                        <th>jumlah</th>
                        <th>terpakai</th>
                        <th>sisa</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($po->hutang as $hutang)
                        <tr>
                            <td>{{ $hutang->tanggal->format('d-m-Y') }}</td>
                            <td>{{ number_format($hutang->jumlah) }}</td>
                            <td>{{ number_format($hutang->totalBayar) }}</td>
                            <td>{{ number_format($hutang->sisa) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <span>kedatangan</span>
            </div>
            @if ($po->status == 'proses')
                @if ($po->poDetail->sum('jumlah') > $po->poDetail->sum('jumlahKedatangan'))
                    <a href="{{ route('po.belanja.create', $po->id) }}" class="btn btn-success rounded-pill text-white">
                        <i class='bx bx-plus-circle'></i> tambah data
                    </a>
                @endif
            @endif
        </div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>tgl</th>
                        <th>produk</th>
                        <th>nota</th>
                        <th>diskon</th>
                        <th>total</th>
                        <th>kekurangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($po->belanja as $kedatangan)
                        <tr>
                            <td>{{ $kedatangan->created_at ?? '-' }}</td>
                            <td>
                                <a href="#">{{ $kedatangan->produk ?? '-' }}</a>
                            </td>
                            <td>{{ $kedatangan->nota ?? '-' }}</td>
                            <td>{{ $kedatangan->diskon ?? '' }}</td>
                            <td>{{ number_format($kedatangan->total) }}</td>
                            <td>
                                @if ($kedatangan->hutang > 0)
                                    <span class="badge bg-warning"
                                        title="Hutang yang belum dibayar: {{ number_format($kedatangan->hutang) }}">
                                        {{ number_format($kedatangan->hutang) }}
                                    </span>
                                    <br>
                                    </small>
                                @else
                                    <span class="badge bg-success" title="Hutang sudah lunas">
                                        Lunas
                                    </span>
                                    <br>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No matching records found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
