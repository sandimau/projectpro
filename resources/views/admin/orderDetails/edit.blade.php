@extends('layouts.app')

@section('title')
    Edit Order Details
@endsection

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active" aria-current="page"> <a href="{{ route('order.detail', $detail->order->id) }}">{{$detail->order->kontak->nama}}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Order Detail</li>
        </ol>
    </nav>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('orderDetail.update', $detail->id) }}" enctype="multipart/form-data">
                @method('patch')
                @csrf
                @if ($canEditLimited)
                    <div class="form-group mb-3">
                        <label for="nama" class="mb-2">Produk</label>
                        @include('admin.orderDetails.partials.produk-autocomplete', [
                            'produkId' => old('produk_id', $detail->produk_id),
                            'produkLabel' => $detail->produk->namaLengkap,
                        ])
                        @if ($errors->has('produk_id'))
                            <div class="invalid-feedback z-10">
                                {{ $errors->first('produk_id') }}
                            </div>
                        @endif
                    </div>
                    <div class="form-group mb-3">
                        <label for="tema">Tema</label>
                        <input class="form-control {{ $errors->has('tema') ? 'is-invalid' : '' }}" type="text" name="tema"
                            id="tema" value="{{ old('tema', $detail->tema) }}">
                        @if ($errors->has('tema'))
                            <div class="invalid-feedback">
                                {{ $errors->first('tema') }}
                            </div>
                        @endif
                    </div>
                @else
                    <div class="form-group mb-3">
                        <label class="mb-2">Produk</label>
                        <p class="form-control-plaintext fw-semibold">{{ $detail->produk->namaLengkap }}</p>
                    </div>
                    <div class="form-group mb-3">
                        <label>Tema</label>
                        <p class="form-control-plaintext">{{ $detail->tema ?: '-' }}</p>
                    </div>
                @endif
                @if ($canEditAll)
                    <div class="form-group mb-3">
                        <label for="jumlah">Jumlah</label>
                        <input class="form-control {{ $errors->has('jumlah') ? 'is-invalid' : '' }}" type="number"
                            name="jumlah" id="jumlah" value="{{ old('jumlah', $detail->jumlah) }}">
                        @if ($errors->has('jumlah'))
                            <div class="invalid-feedback">
                                {{ $errors->first('jumlah') }}
                            </div>
                        @endif
                    </div>
                    <div class="form-group mb-3">
                        <label for="harga">Harga</label>
                        <input class="form-control {{ $errors->has('harga') ? 'is-invalid' : '' }}" type="number"
                            name="harga" id="harga" value="{{ old('harga', $detail->harga) }}">
                        @if ($errors->has('harga'))
                            <div class="invalid-feedback">
                                {{ $errors->first('harga') }}
                            </div>
                        @endif
                    </div>
                @elseif ($canEditLimited)
                    <div class="form-group mb-3">
                        <label>Jumlah</label>
                        <p class="form-control-plaintext">{{ $detail->jumlah }}</p>
                    </div>
                    <div class="form-group mb-3">
                        <label>Harga</label>
                        <p class="form-control-plaintext">{{ number_format($detail->harga) }}</p>
                    </div>
                @endif
                @if ($canEditLimited)
                    @foreach ($speks as $item)
                        <div class="form-group mb-3">
                            <label for="spek">{{ $item->nama }}</label>
                            <input class="form-control" type="text" name="{{ $item->nama }}" id="spek"
                                value="{{ $detail->spek()->where('spek_id', $item->id)->first()? $detail->spek()->where('spek_id', $item->id)->first()->pivot->keterangan: '' }}">
                        </div>
                    @endforeach
                    <div class="form-group mb-3">
                        <label for="keterangan">Keterangan</label>
                        <textarea class="form-control {{ $errors->has('keterangan') ? 'is-invalid' : '' }}" name="keterangan" id=""
                            cols="30" rows="10">{{ old('keterangan', $detail->keterangan) }}</textarea>
                        @if ($errors->has('keterangan'))
                            <div class="invalid-feedback">
                                {{ $errors->first('keterangan') }}
                            </div>
                        @endif
                    </div>
                    <div class="form-group mb-3">
                        <label for="deathline">Deathline</label>
                        <input class="form-control {{ $errors->has('deathline') ? 'is-invalid' : '' }}" type="date"
                            name="deathline" id="deathline" value="{{ old('deathline', $detail->deathline) }}">
                        @if ($errors->has('deathline'))
                            <div class="invalid-feedback">
                                {{ $errors->first('deathline') }}
                            </div>
                        @endif
                    </div>
                @endif
                @if ($canEditLimited)
                    <div class="form-group">
                        <button class="btn btn-primary mt-4" type="submit">
                            save
                        </button>
                    </div>
                @endif
            </form>
        </div>
    </div>
@endsection

@push('after-scripts')
    @if ($canEditLimited)
        @include('admin.orderDetails.partials.produk-autocomplete-standalone')
    @endif
@endpush

