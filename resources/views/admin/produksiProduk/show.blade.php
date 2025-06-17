@extends('layouts.app')

@section('title')
    Detail Order
@endsection

@section('content')
    <div class="bg-light rounded">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page"> <a href="{{ route('produksi.index') }}">Produksi</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Detail</li>
            </ol>
        </nav>
        @include('layouts.includes.messages')
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-lg-3 col-sm-4">
                                        <h6 class="mb-0 text-secondary">Barang yg diproduksi</h6>
                                        <p>{{ $produksi->produk->namaLengkap }}</p>
                                    </div>
                                    <div class="col-lg-2 col-sm-4">
                                        <h6 class="mb-0 text-secondary">Tanggal Mulai</h6>
                                        <p>{{ $produksi->created_at->format('d-m-Y') }}</p>
                                    </div>
                                    <div class="col-lg-2 col-sm-4">
                                        <h6 class="mb-0 text-secondary">Target</h6>
                                        <p>{{ $produksi->target }}</p>
                                    </div>
                                    <div class="col-lg-2 col-sm-4">
                                        <h6 class="mb-0 text-secondary">keterangan</h6>
                                        <p>{{ $produksi->keterangan }}</p>
                                    </div>
                                    <div class="col-lg-2 col-sm-4">
                                        <h6 class="mb-0 text-secondary">status</h6>
                                        <p>{{ $produksi->status }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 text-end">
                                @if ($produksi->status != 'finish')
                                    @can('produk_stok_access')
                                        <a href="{{ route('produksi.edit', $produksi->id) }}"
                                            class="btn btn-info rounded-pill text-white">
                                            edit
                                        </a>
                                        <a href="{{ route('produksi.selesai', $produksi->id) }}"
                                            class="btn btn-primary rounded-pill text-white">
                                            selesai
                                        </a>
                                    @endcan
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- TABEL AMBIL BAHAN DI GUDANG -->
            <div class="col-lg-12 mt-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h4 class="mb-0">ambil bahan di gudang</h4>
                    @if ($produksi->status != 'finish')
                        <a href="{{ route('produksi.ambilBahan', $produksi->id) }}" class="btn btn-success rounded-pill"><i
                                class="bx bx-plus"></i> tambah data</a>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>tgl</th>
                                <th>barang</th>
                                <th>jumlah</th>
                                <th>hpp</th>
                                <th>keterangan</th>
                                <th>penginput</th>
                                <th>action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($produksi->bahan as $bahan)
                                <tr>
                                    <td>{{ $bahan->created_at->format('d-m-Y') }}</td>
                                    <td>{{ $bahan->produk->namaLengkap }}</td>
                                    <td>{{ $bahan->jumlah }}</td>
                                    <td>{{ $bahan->hpp }}</td>
                                    <td>{{ $bahan->keterangan }}</td>
                                    <td>{{ $bahan->produkStok->user->name ?? '-' }}</td>
                                    <td>
                                        @if ($produksi->status != 'finish')
                                            <form
                                                action="{{ route('produksi.ambilBahanDestroy', [$produksi->id, $bahan->id]) }}"
                                                method="post">
                                                @csrf
                                                @method('delete')
                                                <button type="submit"
                                                    onclick="return confirm('Apakah anda yakin ingin menghapus data ini?')"
                                                    class="btn btn-danger btn-sm"><i class="bx bx-trash"></i></button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TABEL BELANJA BAHAN / PENGELUARAN -->
            <div class="col-lg-12 mt-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h4 class="mb-0">belanja bahan / pengeluaran</h4>
                    @if ($produksi->status != 'finish')
                        <a href="{{ route('produksi.belanja', $produksi->id) }}" class="btn btn-success rounded-pill"><i
                                class="bx bx-plus"></i> tambah data</a>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>tgl</th>
                                <th>supplier</th>
                                <th>barang/jasa</th>
                                <th>total</th>
                                <th>kekurangan</th>
                                <th>penginput</th>
                                <th>action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($produksi->belanja as $belanja)
                                <tr>
                                    <td>{{ $belanja->created_at->format('d-m-Y') }}</td>
                                    <td>{{ $belanja->kontak->nama }}</td>
                                    <td><a href="{{ route('belanja.detail', $belanja->id) }}"
                                            target="_blank">{{ $belanja->produk }}</a></td>
                                    <td>{{ $belanja->total }}</td>
                                    <td class="text-primary">{{ $belanja->kekurangan }}</td>
                                    <td>{{ $belanja->user->name ?? '-' }}</td>
                                    <td>
                                        @if ($produksi->status != 'finish')
                                            <form
                                                action="{{ route('produksi.belanjaDestroy', [$produksi->id, $belanja->id]) }}"
                                                method="post">
                                                @csrf
                                                @method('delete')
                                                <button type="submit"
                                                    onclick="return confirm('Apakah anda yakin ingin menghapus data ini?')"
                                                    class="btn btn-danger btn-sm"><i class="bx bx-trash"></i></button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>


            <div class="col-lg-6">
                <div class="card mt-4">
                    <div class="card-body">
                        <h5>hasil</h5>
                        <div class="row">
                            <div class="col-12">
                                <table style="width:100%">
                                    <tr>
                                        <td>total biaya</td>
                                        <td class="text-end">{{ number_format($produksi->biaya, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td>target produksi</td>
                                        <td class="text-end">{{ $produksi->target }}</td>
                                    </tr>
                                    <tr>
                                        <td>hasil produksi</td>
                                        <td class="text-end">{{ $produksi->hasil ?? 0 }}</td>
                                    </tr>
                                    <tr>
                                        <td>waktu produksi</td>
                                        <td class="text-end">
                                            <?php
                                            if (!empty($produksi->updated_at)) {
                                                $datetime1 = new DateTime($produksi->created_at);
                                                $datetime2 = new DateTime($produksi->updated_at);
                                                $interval = $datetime1->diff($datetime2);
                                                echo $interval->format('%a') . ' hari';
                                            } else {
                                                echo '0 hari';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>hpp</td>
                                        <td class="text-end">{{ number_format($produksi->hpp ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                </table>
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
                        <form method="POST" action="{{ route('produksi.chatStore', $produksi->id) }}"
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
