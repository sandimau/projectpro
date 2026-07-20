@extends('layouts.app')

@section('title')
Create Hasil Produksi
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Tambah Hasil Produksi</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("produksi.hasilProduksiStore", $produksi->id) }}" enctype="multipart/form-data">
            @csrf
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
                <label for="jumlah" class="mb-2">Hasil Produksi</label>
                <input class="form-control {{ $errors->has('jumlah') ? 'is-invalid' : '' }}" type="number" name="jumlah" id="jumlah" value="{{ old('jumlah', '') }}">
                @if($errors->has('jumlah'))
                    <div class="invalid-feedback">
                        {{ $errors->first('jumlah') }}
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
        new Autocomplete('#autocompleteProduk', {
            search: input => {
                const url = "{{ url('admin/produkProduksi/api?q=') }}" + `${escape(input)}`;
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

                let btn = document.getElementById("closeBrgProduk");
                btn.style.display = "block";
                btn.innerHTML =
                    `<button onclick="clearProduk()" type="button" class="btnClose btn-warning"><i class='bx bx-x-circle' ></i></button>`;
            },
        })

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
        #autocompleteProduk {
            max-width: 600px;
        }

        #closeBrgProduk {
            position: relative;
        }

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
