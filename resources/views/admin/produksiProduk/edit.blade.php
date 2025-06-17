@extends('layouts.app')

@section('title')
Edit Produksi
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Edit Produksi</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("produksi.update", $produksi->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="form-group mb-3">
                <label for="nama" class="mb-2">Produk</label>
                <div id="autocompleteProduk" class="autocomplete">
                    <input class="autocomplete-input produk {{ $errors->has('produk_id') ? 'invalid' : '' }}"
                        placeholder="cari produk"
                        aria-label="cari produk"
                        role="combobox"
                        autocomplete="off"
                        autocapitalize="off"
                        spellcheck="false"
                        aria-autocomplete="list"
                        aria-haspopup="listbox"
                        aria-expanded="false"
                        aria-owns="autocomplete-result-list-1"
                        aria-activedescendant=""
                        value="{{ old('produk_nama', $produksi->produk->namaLengkap ) }}">
                    <span id="closeBrgProduk"></span>
                    <ul class="autocomplete-result-list"></ul>
                    <input type="hidden" id="produkId" name="produk_id" value="{{ old('produk_id', $produksi->produk_id) }}">
                </div>
                @if ($errors->has('produk_id'))
                    <div class="invalid-feedback z-10">
                        {{ $errors->first('produk_id') }}
                    </div>
                @endif
            </div>
            <div class="form-group mb-3">
                <label for="target" class="mb-2">Target</label>
                <input class="form-control {{ $errors->has('target') ? 'is-invalid' : '' }}" type="number" name="target" id="target" value="{{ old('target', $produksi->target) }}">
                @if($errors->has('target'))
                    <div class="invalid-feedback">
                        {{ $errors->first('target') }}
                    </div>
                @endif
            </div>
            <div class="form-group mb-3">
                <label for="keterangan" class="mb-2">Keterangan</label>
                <textarea class="form-control {{ $errors->has('ket') ? 'is-invalid' : '' }}" name="ket" id="ket" rows="3">{{ old('ket', $produksi->ket) }}</textarea>
                @if($errors->has('ket'))
                    <div class="invalid-feedback">
                        {{ $errors->first('ket') }}
                    </div>
                @endif
            </div>
            <div class="form-group">
                <button class="btn btn-primary mt-4" type="submit">
                    Update
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
        document.addEventListener('DOMContentLoaded', function() {
            const produkInput = document.querySelector(".produk");
            const produkId = document.getElementById('produkId');

            // Set tombol close jika ada nilai awal
            if (produkInput.value && produkId.value) {
                const btn = document.getElementById("closeBrgProduk");
                btn.style.display = "block";
                btn.innerHTML = `<button onclick="clearProduk()" type="button" class="btnClose btn-warning"><i class='bx bx-x-circle' ></i></button>`;
            }

            const autocomplete = new Autocomplete('#autocompleteProduk', {
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
            });
        });

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
