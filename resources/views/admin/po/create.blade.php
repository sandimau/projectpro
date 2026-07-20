@extends('layouts.app')

@section('title')
    Create PO
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title">add PO</h5>
                </div>
                <a href="{{ route('po.index') }}" class="btn btn-success ">back</a>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('po.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group mb-3">
                    <label for="nama" class="mb-2">Supplier</label>
                    <div id="autocomplete" class="autocomplete">
                        <input class="autocomplete-input {{ $errors->has('kontak_id') ? 'is-invalid' : '' }}"
                            placeholder="cari kontak" aria-label="cari kontak">
                        <span id="closeBrg"></span>
                        <ul class="autocomplete-result-list"></ul>
                        <input type="hidden" id="kontakId" name="kontak_id">
                    </div>
                    @if ($errors->has('kontak_id'))
                        <div class="invalid-feedback z-10">
                            {{ $errors->first('kontak_id') }}
                        </div>
                    @endif
                </div>
                <div class="form-group mb-3">
                    <label for="nama" class="mb-2">Produk</label>
                    <div id="autocompleteProduk" class="autocomplete">
                        <input class="autocomplete-input produk {{ $errors->has('produk_id') ? 'invalid' : '' }}"
                            placeholder="cari produk" aria-label="cari produk">
                        <span id="closeBrgProduk"></span>
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
                    <label for="jumlah">Jumlah</label>
                    <input class="form-control {{ $errors->has('jumlah') ? 'is-invalid' : '' }}" type="number"
                        name="jumlah" id="jumlah" value="{{ old('jumlah', '') }}">
                    @if ($errors->has('jumlah'))
                        <div class="invalid-feedback">
                            {{ $errors->first('jumlah') }}
                        </div>
                    @endif
                </div>
                <div class="form-group mb-3">
                    <label for="ket">Keterangan</label>
                    <input class="form-control {{ $errors->has('ket') ? 'is-invalid' : '' }}" type="text"
                        name="ket" id="ket" value="{{ old('ket', '') }}">
                    @if ($errors->has('ket'))
                        <div class="invalid-feedback">
                            {{ $errors->first('ket') }}
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
<script src="{{ asset('js/autocomplete.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('js/autocomplete.css') }}">
    <script>
        new Autocomplete('#autocomplete', {
            search: input => {
                const url = "{{ url('admin/supplier/api?q=') }}" + `${escape(input)}`;
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
            getResultValue: result => result.perusahaan ? result.nama + ' - ' + result.perusahaan : result.nama,
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
            getResultValue: result => result.varian ? result.kategori + ' - ' + result.nama + ' - ' + result.varian : result.kategori + ' - ' + result.nama,
            onSubmit: result => {
                let idProduk = document.getElementById('produkId');
                idProduk.value = result.id;

                //set harga
                let harga = document.getElementById("harga");
                harga.value = result.harga;

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
            let auto = document.querySelector(".produk");
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

        .autocomplete-input {
            width: 600px !important;
            margin-right: 10px;
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
