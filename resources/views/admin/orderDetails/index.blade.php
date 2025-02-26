@extends('layouts.app')

@section('title')
    Detail Order
@endsection

@section('content')
    <div class="bg-light rounded">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page"> <a href="{{ route('order.dashboard') }}" >Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Order Detail</li>
            </ol>
        </nav>
        @include('layouts.includes.messages')
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-details-center">
                            <div>
                                <h5 class="card-title">{{ $order->nota }} | {{ $order->kontak->nama }} - {{ $order->konsumen_detail }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-lg-2 col-sm-4">
                                        <h6 class="mb-0 text-secondary">Ongkir</h6>
                                        <p>{{ number_format($order->ongkir, 0, ',', '.') }}</p>
                                    </div>
                                    <div class="col-lg-2 col-sm-4">
                                        <h6 class="mb-0 text-secondary">Diskon</h6>
                                        <p>{{ number_format($order->diskon, 0, ',', '.') }}</p>
                                    </div>
                                    <div class="col-lg-2 col-sm-4">
                                        <h6 class="mb-0 text-secondary">Total</h6>
                                        <p>{{ number_format($order->total, 0, ',', '.') }}</p>
                                    </div>
                                    <div class="col-lg-2 col-sm-4">
                                        <h6 class="mb-0 text-secondary">Pembayaran</h6>
                                        <p>{{ number_format($order->bayar, 0, ',', '.') }}</p>
                                    </div>
                                    <div class="col-lg-2 col-sm-4">
                                        <h6 class="mb-0 text-secondary">Kekurangan</h6>
                                        <p>{{ number_format($order->kekurangan, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                @can('order_detail_create')
                                    <a href="{{ route('orderDetail.add', $order->id) }}"
                                        class="btn btn-success rounded-pill text-white">
                                        <i class='bx bx-plus-circle'></i> tambah
                                    </a>
                                    <a href="{{ route('order.edit', $order->id) }}"
                                        class="btn btn-info rounded-pill text-white">
                                        edit
                                    </a>
                                    <a href="{{ route('order.invoice', $order->id) }}"
                                        class="btn btn-primary rounded-pill text-white">
                                        invoice
                                    </a>
                                @endcan
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped" id="myTable">
                                <thead>
                                    <tr>
                                        <th>produk</th>
                                        <th>tema</th>
                                        <th>jml</th>
                                        <th>harga</th>
                                        <th>subtotal</th>
                                        <th>spesifikasi</th>
                                        <th>status</th>
                                        <th>gambar</th>
                                        <th>deadline</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orderDetails as $detail)
                                        <tr>
                                            <td style="font-weight: 600;"><a style="text-decoration:none"
                                                    href="{{ route('orderDetail.edit', $detail->id) }}">{{ $detail->produk->namaLengkap }}</a>
                                            </td>
                                            <td>{{ $detail->tema }}</td>
                                            <td>{{ $detail->jumlah }}</td>
                                            <td>{{ number_format($detail->harga) }}</td>
                                            <td>{{ number_format($detail->harga * $detail->jumlah) }}</td>
                                            <td>
                                                @foreach ($detail->spek as $spek)
                                                    <span style="font-weight: 600"> {{ $spek->nama }}: </span>
                                                    {{ $spek->pivot->keterangan }},
                                                @endforeach

                                                @if (!empty($detail->keterangan))
                                                    <span class='text-danger'> keterangan:</span>
                                                    {{ $detail->keterangan }}
                                                @endif
                                            </td>
                                            <td>
                                                <form action="{{ route('orderDetail.status', $detail->id) }}"
                                                    method="post"
                                                    onsubmit="document.getElementById('submit').disabled=true;
                                                    document.getElementById('submit').value='proses'">
                                                    {{ csrf_field() }}
                                                    {{ method_field('patch') }}
                                                    <select class="form-select" aria-label="Default select example"
                                                        name="produksi_id" id="produksi_id" onchange="this.form.submit()">
                                                        @foreach ($produksi as $entry)
                                                            <option value="{{ $entry->id }}"
                                                                {{ $detail->produksi_id == $entry->id ? 'selected' : '' }}>
                                                                {{ $entry->nama }}</option>
                                                        @endforeach
                                                    </select>
                                                </form>
                                            </td>
                                            <td>
                                                @if ($detail->gambar)
                                                    <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal{{ $detail->id }}">
                                                        <img style="height: 60px"
                                                            src="{{ asset('uploads/order/' . $detail->gambar) }}"
                                                            alt="" srcset="">
                                                    </a>

                                                    <!-- Modal -->
                                                    <div class="modal fade" id="imageModal{{ $detail->id }}" tabindex="-1" aria-labelledby="imageModalLabel{{ $detail->id }}" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="imageModalLabel{{ $detail->id }}">Gambar Order</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body text-center">
                                                                    <img class="img-fluid" style="width: 100%;" src="{{ asset('uploads/order/' . $detail->gambar) }}" alt="">
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <a href="{{ route('orderDetail.editGambar', $detail->id) }}" class="btn btn-primary">Edit Gambar</a>
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <a href="{{ route('orderDetail.gambar', $detail->id) }}"
                                                        class="btn btn-success text-white"><i
                                                            class='bx bx-image-alt'></i></a>
                                                @endif
                                            </td>
                                            <td>
                                                {{ date('d-m-Y', strtotime($detail->deathline)) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card mt-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="mb-0">pengiriman</p>
                                <h6>{{ $order->pengiriman }}</h6>
                            </div>
                            <div>
                                <p class="mb-0">invoice</p>
                                <h6>{{ $order->invoice }}</h6>
                            </div>
                            <div>
                                <p class="mb-0">pembayaran</p>
                                <h6>{{ $order->jenis_pembayaran }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card mt-4">
                    <div class="card-header">
                        notes
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('order.chatStore', $order->id) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="input-group mb-3">
                                <input type="text" class="form-control chat" placeholder="tulis pesan" name="isi">
                                <button class="input-group-text btn btn-primary rounded-pill" type="submit"><i
                                        class='bx bx-send'></i></button>
                            </div>
                        </form>
                        <div class="iframe">
                            <small>
                                <ul class="chat-list p-0 m-0">
                                    @foreach ($chats as $chat)
                                        <li class="d-flex justify-content-between align-items-end pt-2">
                                            <div class="chat-content">
                                                @if ($chat->member)
                                                    <div class="text-primary"><b>{{ $chat->member->nama_lengkap }}</b>
                                                    </div>
                                                @endif
                                                <div class="box">{{ $chat->isi }}</div>
                                            </div>
                                            <div class="ps-2">{{ date('d/m/Y', strtotime($chat->created_at)) }}</div>
                                        </li>
                                    @endforeach
                                </ul>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
    <style>
        .chat {
            border: none;
            border-bottom: solid #7c7c7c 1px
        }

        .chat:focus {
            box-shadow: none
        }

        .iframe {
            padding: 0px 10px;
        }

        .iframe ul {
            list-style: none;
        }

        .iframe .chat-content .box {
            padding: 10px 20px 10px 10px;
            background-color: #dddddd;
            border-radius: 5px;
        }
    </style>
@endpush
