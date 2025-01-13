@extends('layouts.app')

@section('title')
    Edit Order Details
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title">edit order details</h5>
                </div>
                <a href="{{ route('order.detail', $detail->order->id) }}" class="btn btn-success ">back</a>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('orderDetail.update', $detail->id) }}" enctype="multipart/form-data">
                @method('patch')
                @csrf
                <div class="form-group mb-3">
                    <label for="nama" class="mb-2">Produk</label>
                    <div id="autocompleteProduk" class="autocomplete">
                        <input class="autocomplete-input produk {{ $errors->has('produk_id') ? 'invalid' : '' }}"
                            placeholder="cari produk" aria-label="cari produk" value="{{ $detail->produk->nama }}">
                        <span id="closeBrgProduk" style="display: block">
                            @if ($detail->produk_id)
                                <button onclick="clearProduk()" type="button" class="btnClose btn-warning"><i
                                        class="bx bx-x-circle"></i></button>
                            @endif
                        </span>
                        <ul class="autocomplete-result-list"></ul>
                        <input type="hidden" id="produkId" name="produk_id">
                    </div>
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
                @foreach ($speks as $item)
                    <div class="form-group mb-3">
                        <label for="spek">{{ $item->nama }}</label>
                        <input class="form-control" type="text" name="{{ $item->nama }}" id="spek"
                            value="{{ $detail->spek()->where('spek_id', $item->id)->first() ? $detail->spek()->where('spek_id', $item->id)->first()->pivot->keterangan : '' }}">
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
                        name="deathline" id="deathline" value="{{ old('keterangan', $detail->deathline) }}">
                    @if ($errors->has('deathline'))
                        <div class="invalid-feedback">
                            {{ $errors->first('deathline') }}
                        </div>
                    @endif
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input name="bahan" class="form-check-input {{ $errors->has('bahan') ? 'is-invalid' : '' }}"
                            type="checkbox" value="1" id="flexCheckDefault" {{ $detail->bahan ? 'checked' : '' }}>
                        <label class="form-check-label" for="flexCheckDefault">
                            Bahan
                        </label>
                        @if ($errors->has('bahan'))
                            <div class="invalid-feedback">
                                {{ $errors->first('bahan') }}
                            </div>
                        @endif
                    </div>
                    <div class="form-check">
                        <input name="kalkir" class="form-check-input {{ $errors->has('kalkir') ? 'is-invalid' : '' }}"
                            type="checkbox" value="1" id="flexCheckChecked" {{ $detail->kalkir ? 'checked' : '' }}>
                        <label class="form-check-label" for="flexCheckChecked">
                            kalkir
                        </label>
                        @if ($errors->has('kalkir'))
                            <div class="invalid-feedback">
                                {{ $errors->first('kalkir') }}
                            </div>
                        @endif
                    </div>
                    <div class="form-check">
                        <input name="screen" class="form-check-input {{ $errors->has('screen') ? 'is-invalid' : '' }}"
                            type="checkbox" value="1" id="flexCheckChecked" {{ $detail->screen ? 'checked' : '' }}>
                        <label class="form-check-label" for="flexCheckChecked">
                            screen
                        </label>
                        @if ($errors->has('screen'))
                            <div class="invalid-feedback">
                                {{ $errors->first('screen') }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <button class="btn btn-primary mt-4" type="submit">
                        save
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('after-scripts')
    <script src="https://unpkg.com/@trevoreyre/autocomplete-js"></script>
    <link rel="stylesheet" href="https://unpkg.com/@trevoreyre/autocomplete-js/dist/style.css" />
    <script>
        new Autocomplete('#autocompleteProduk', {
            search: input => {
                const url = "{{ url('admin/produk/api?q=') }}" + `${escape(input)}`;
                return new Promise(resolve => {
                    if (input.length < 1) {
                        return resolve([])
                    }

                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            resolve(data);
                        })
                })
            },
            getResultValue: result => result.nama,
            onSubmit: result => {
                let idProduk = document.getElementById('produkId');
                idProduk.value = result.id;

                let btn = document.getElementById("closeBrgProduk");
                btn.style.display = "block";
                btn.innerHTML =
                    `<button onclick="clearProduk()" type="button" class="btnClose btn-warning"><i class='bx bx-x-circle' ></i></button>`;
            },
        })

        function clearData() {
            let btn = document.getElementById("closeBrg");
            btn.style.display = "none";
            let auto = document.querySelector(".autocomplete-input");
            auto.value = null;
            let idProduk = document.getElementById('kontakId');
            idProduk.value = null;
        }

        function clearProduk() {
            let btn = document.getElementById("closeBrgProduk");
            btn.style.display = "none";
            let auto = document.querySelector(".autocomplete-input.produk");
            auto.value = null;
            let idProduk = document.getElementById('produkId');
            idProduk.value = null;
        }
    </script>
    <style>
        #autocomplete,
        #autocompleteProduk {
            max-width: 600px;
        }

        #closeBrg,
        #closeBrgProduk {
            position: relative;
        }

        #closeBrg button,
        #closeBrgProduk button {
            position: absolute;
            right: -15px;
            top: -40px;
        }

        .btnClose {
            padding: 4px 8px;
            border: 0;
            border-radius: 50px;
            background: #fdc54c;
        }

        .autocomplete-input.is-invalid,
        .autocomplete-input.invalid {
            border: solid 1px red;
        }
    </style>
@endpush
