@extends('layouts.app')

@section('title')
    Update Orders
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title">Update Order</h5>
                </div>
                <a href="{{ route('order.dashboard') }}" class="btn btn-success ">back</a>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('order.update', [$order->id]) }}" enctype="multipart/form-data">
                @method('patch')
                @csrf
                <div class="form-group mb-3">
                    <label for="username">username</label>
                    <input class="form-control {{ $errors->has('username') ? 'is-invalid' : '' }}" type="text" name="username"
                        id="username" value="{{ old('username',$order->username) }}">
                    @if ($errors->has('username'))
                        <div class="invalid-feedback">
                            {{ $errors->first('username') }}
                        </div>
                    @endif
                </div>
                <div class="form-group mb-3">
                    <label for="diskon">Diskon</label>
                    <input class="form-control {{ $errors->has('diskon') ? 'is-invalid' : '' }}" type="number" name="diskon"
                        id="diskon" value="{{ old('diskon',$order->diskon) }}">
                    @if ($errors->has('diskon'))
                        <div class="invalid-feedback">
                            {{ $errors->first('diskon') }}
                        </div>
                    @endif
                </div>
                <div class="form-group mb-3">
                    <label for="ket_diskon">ket diskon</label>
                    <input class="form-control {{ $errors->has('ket_diskon') ? 'is-invalid' : '' }}" type="text"
                        name="ket_diskon" id="ket_diskon" value="{{ old('ket_diskon',$order->ket_diskon) }}">
                    @if ($errors->has('ket_diskon'))
                        <div class="invalid-feedback">
                            {{ $errors->first('ket_diskon') }}
                        </div>
                    @endif
                </div>
                <div class="form-group mb-3">
                    <div class="row">
                        <label for="harga">Pengiriman</label>
                        <div class="row">
                            <div class="col-lg-3 col-sm-12">
                                <div class="form-check ms-3">
                                    <input class="form-check-input" type="radio" name="pengiriman" id="flexRadioDefault"
                                        value="diambil" {{ $order->pengiriman == 'diambil' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="flexRadioDefault">
                                        diambil
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-12">
                                <div class="form-check ms-3">
                                    <input class="form-check-input" type="radio" name="pengiriman"
                                        id="flexRadioDefault2" value="dikirim" {{ $order->pengiriman == 'dikirim' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="flexRadioDefault2">
                                        dikirim
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-12">
                                <div class="form-check ms-3">
                                    <input class="form-check-input" type="radio" name="pengiriman"
                                        id="flexRadioDefault3" value="jasa pengiriman" {{ $order->pengiriman == 'jasa pengiriman' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="flexRadioDefault3">
                                        jasa pengiriman
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <div class="row">
                        <label for="harga">Invoice</label>
                        <div class="row">
                            <div class="col-sm-12 col-lg-3">
                                <div class="form-check me-1">
                                    <input class="form-check-input" type="radio" name="invoice" id="invoice"
                                        value="disertakan barang" {{ $order->invoice == 'disertakan barang' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="invoice">
                                        disertakan barang
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-lg-3">
                                <div class="form-check me-1">
                                    <input class="form-check-input" type="radio" name="invoice" id="invoice2"
                                        value="dikirim terpisah" {{ $order->invoice == 'dikirim terpisah' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="invoice2">
                                        dikirim terpisah
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-lg-2">
                                <div class="form-check me-1">
                                    <input class="form-check-input" type="radio" name="invoice" id="invoice3"
                                        value="diemail" {{ $order->invoice == 'diemail' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="invoice3">
                                        diemail
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-lg-2">
                                <div class="form-check me-1">
                                    <input class="form-check-input" type="radio" name="invoice" id="invoice4"
                                        value="tidak pakai" {{ $order->invoice == 'tidak pakai' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="invoice4">
                                        tidak pakai
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <div class="row">
                        <label for="harga">Pembayaran</label>
                        <div class="d-flex">
                            <div class="form-check me-2">
                                <input class="form-check-input" type="radio" name="jenis_pembayaran" id="pembayaran" value="cod" {{ $order->jenis_pembayaran == 'cod' ? 'checked' : '' }}>
                                <label class="form-check-label" for="pembayaran">
                                    cod
                                </label>
                            </div>
                            <div class="form-check me-2">
                                <input class="form-check-input" type="radio" name="jenis_pembayaran" id="pembayaran2"
                                    value="transfer" {{ $order->jenis_pembayaran == 'transfer' ? 'checked' : '' }}>
                                <label class="form-check-label" for="pembayaran2">
                                    transfer
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label for="jasa">jasa pengiriman</label>
                    <input class="form-control {{ $errors->has('jasa') ? 'is-invalid' : '' }}" type="text" name="jasa"
                        id="jasa" value="{{ old('jasa',$order->jasa) }}">
                    @if ($errors->has('jasa'))
                        <div class="invalid-feedback">
                            {{ $errors->first('jasa') }}
                        </div>
                    @endif
                </div>
                <div class="form-group mb-3">
                    <label for="ongkir">ongkir</label>
                    <input class="form-control {{ $errors->has('ongkir') ? 'is-invalid' : '' }}" type="number"
                        name="ongkir" id="ongkir" value="{{ old('ongkir',$order->ongkir) }}">
                    @if ($errors->has('ongkir'))
                        <div class="invalid-feedback">
                            {{ $errors->first('ongkir') }}
                        </div>
                    @endif
                </div>
                <div class="form-group mb-3">
                    <label for="ket_kirim">ket ongkir</label>
                    <input class="form-control {{ $errors->has('ket_kirim') ? 'is-invalid' : '' }}" type="text"
                        name="ket_kirim" id="ket_kirim" value="{{ old('ket_kirim',$order->ket_kirim) }}">
                    @if ($errors->has('ket_kirim'))
                        <div class="invalid-feedback">
                            {{ $errors->first('ket_kirim') }}
                        </div>
                    @endif
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
        new Autocomplete('#autocomplete', {
            search: input => {
                const url = "{{ url('admin/konsumen/api?q=') }}" + `${escape(input)}`;
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
                let kontak = document.getElementById('kontakId');
                kontak.value = result.id;

                let btn = document.getElementById("closeBrg");
                btn.style.display = "block";
                btn.innerHTML =
                    `<button onclick="clearData()" type="button" class="btnClose btn-warning"><i class='bx bx-x-circle' ></i></button>`;

            },
        })

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
