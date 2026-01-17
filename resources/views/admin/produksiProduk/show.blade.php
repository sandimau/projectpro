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
                                    <div class="col-lg-2 col-sm-4">
                                        <h6 class="mb-0 text-secondary">Tanggal Mulai</h6>
                                        <p>{{ $produksi->created_at->format('d-m-Y') }}</p>
                                    </div>
                                    <div class="col-lg-2 col-sm-4">
                                        <h6 class="mb-0 text-secondary">keterangan</h6>
                                        <p>{{ $produksi->ket }}</p>
                                    </div>
                                    <div class="col-lg-2 col-sm-4">
                                        <h6 class="mb-0 text-secondary">status</h6>
                                        <p>{{ $produksi->status }}</p>
                                    </div>
                                    <div class="col-lg-2 col-sm-4">
                                        <h6 class="mb-0 text-secondary">total biaya</h6>
                                        <p>{{ number_format($produksi->biaya, 0, ',', '.') }}</p>
                                    </div>
                                    @if ($produksi->status == 'finish')
                                        <div class="col-lg-2 col-sm-4">
                                            <h6 class="mb-0 text-secondary">waktu produksi</h6>
                                            <p> <?php
                                            if (!empty($produksi->updated_at)) {
                                                $datetime1 = new DateTime($produksi->created_at);
                                                $datetime2 = new DateTime($produksi->updated_at);
                                                $interval = $datetime1->diff($datetime2);
                                                echo $interval->format('%a') . ' hari';
                                            } else {
                                                echo '0 hari';
                                            }
                                            ?></p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3 text-end">
                                @if ($produksi->status != 'finish')
                                    @can('produk_stok_access')
                                        <div class="d-flex gap-2 justify-content-end">
                                            <a href="{{ route('produksi.edit', $produksi->id) }}"
                                                class="btn btn-info rounded-pill text-white">
                                                edit
                                            </a>
                                            @if ($produksi->hasilProduksi()->count() == 0)
                                                <a href="{{ route('produksi.selesai', $produksi->id) }}"
                                                    class="btn btn-success rounded-pill text-white">
                                                    selesai
                                                </a>
                                            @else
                                                @if ($produksi->cekkomplit())
                                                    <form action="{{ route('produksi.selesaiProduksi', $produksi->id) }}"
                                                        method="post" class="m-0">
                                                        @csrf
                                                        <button type="submit"
                                                            onclick="return confirm('Apakah anda yakin ingin selesai produksi?')"
                                                            class="btn btn-primary rounded-pill"><i class="bx bx-check"></i>
                                                            selesaikan</button>
                                                    </form>
                                                @endif
                                            @endif
                                        </div>
                                    @endcan
                                @endif
                                @if ($produksi->status == 'finish')
                                    @if ($produksi->hasilProduksi()->count() > 0)
                                        <div class="d-flex gap-2 justify-content-end">
                                            <a href="{{ route('produksi.produksiLagi', $produksi->id) }}"
                                                class="btn btn-success rounded-pill text-white">
                                                Produksi Lagi
                                            </a>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- TABEL HASIL PRODUKSI -->
            <div class="col-lg-12 mt-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h4 class="mb-0">Hasil Produksi</h4>
                    @if ($produksi->status != 'finish')
                        @if ($produksi->hasilProduksi()->count() > 0)
                            <a href="{{ route('produksi.hasilProduksi', $produksi->id) }}"
                                class="btn btn-success rounded-pill"><i class="bx bx-plus"></i> tambah data</a>
                        @endif
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>tgl</th>
                                <th>barang</th>
                                <th>jumlah</th>
                                <th>perbandingan</th>
                                <th>satuan</th>
                                <th>hpp</th>
                                <th>status</th>
                                <th>user</th>
                                @if ($produksi->status != 'finish')
                                    <th>action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @if ($produksi->hasilProduksi()->count())
                                @foreach ($produksi->hasilProduksi as $hasil)
                                    <tr class="{{ $hasil->status == 'finish' ? 'table-success' : '' }}">
                                        <td>{{ $hasil->created_at->format('d-m-Y') }}</td>
                                        <td>{{ $hasil->produk->namaLengkap }}</td>
                                        <td>{{ $hasil->jumlah }}</td>
                                        <td>{{ $hasil->perbandingan }}</td>
                                        <td>{{ $hasil->satuan }}</td>
                                        <td>{{ number_format($hasil->hpp, 0, ',', '.') }}</td>
                                        <td>
                                            @if ($hasil->status == 'finish')
                                                <span class="badge bg-success">Selesai</span>
                                            @else
                                                <span class="badge bg-warning">Proses</span>
                                            @endif
                                        </td>
                                        <td>{{ $hasil->user->name ?? '-' }}</td>
                                        @if ($produksi->status != 'finish')
                                            <td>
                                                <div class="d-flex gap-1">
                                                    @if ($hasil->status != 'finish' && $hasil->hpp > 0)
                                                        <form
                                                            action="{{ route('produksi.selesaiHasilProduksiSatuan', [$produksi->id, $hasil->id]) }}"
                                                            method="post">
                                                            @csrf
                                                            <button type="submit"
                                                                onclick="return confirm('Selesaikan hasil produksi ini? Stok akan ditambahkan.')"
                                                                class="btn btn-success btn-sm" title="Selesaikan"><i
                                                                    class="bx bx-check"></i></button>
                                                        </form>
                                                    @endif
                                                    @if ($hasil->status != 'finish')
                                                        <a href="{{ route('produksi.editHasilProduksi', [$produksi->id, $hasil->id]) }}"
                                                            class="btn btn-warning btn-sm"><i class="bx bx-edit"></i></a>
                                                        <form
                                                            action="{{ route('produksi.hapusHasilProduksi', [$produksi->id, $hasil->id]) }}"
                                                            method="post">
                                                            @csrf
                                                            @method('delete')
                                                            <button type="submit"
                                                                onclick="return confirm('Apakah anda yakin ingin menghapus data ini?')"
                                                                class="btn btn-danger btn-sm"><i
                                                                    class="bx bx-trash"></i></button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td>{{ $produksi->created_at->format('d-m-Y') }}</td>
                                    <td>{{ $produksi->produk ? $produksi->produk->namaLengkap : '-' }}</td>
                                    <td>{{ $produksi->target }}</td>
                                    <td>{{ $produksi->perbandingan ?? '-' }}</td>
                                    <td>{{ $produksi->satuan ?? '-' }}</td>
                                    <td>{{ $produksi->hpp ?? '-' }}</td>
                                    <td>-</td>
                                    <td>{{ $produksi->user->name ?? '-' }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
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
                                @if ($produksi->status != 'finish')
                                    <th>action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($produksi->bahan as $bahan)
                                <tr>
                                    <td>{{ $bahan->created_at->format('d-m-Y') }}</td>
                                    <td>{{ $bahan->produk->namaLengkap }}</td>
                                    <td>{{ $bahan->jumlah }}</td>
                                    <td>{{ number_format($bahan->hpp, 0, ',', '.') }}</td>
                                    <td>{{ $bahan->keterangan }}</td>
                                    <td>{{ $bahan->produkStok->user->name ?? '-' }}</td>
                                    @if ($produksi->status != 'finish')
                                        <td>
                                            <form
                                                action="{{ route('produksi.ambilBahanDestroy', [$produksi->id, $bahan->id]) }}"
                                                method="post">
                                                @csrf
                                                @method('delete')
                                                <button type="submit"
                                                    onclick="return confirm('Apakah anda yakin ingin menghapus data ini?')"
                                                    class="btn btn-danger btn-sm"><i class="bx bx-trash"></i></button>
                                            </form>
                                        </td>
                                    @endif
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
                                <th class="text-end">total</th>
                                <th class="text-end">kekurangan</th>
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
                                    <td class="text-end">{{ number_format($belanja->total ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-primary text-end">
                                        {{ number_format($belanja->total - $belanja->pembayaran ?? 0, 0, ',', '.') }}</td>
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
                    <div class="card-header">
                        notes
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('produksi.chatStore', $produksi->id) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="input-group mb-3">
                                <input type="text" class="form-control chat" placeholder="tulis pesan"
                                    name="isi">
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
