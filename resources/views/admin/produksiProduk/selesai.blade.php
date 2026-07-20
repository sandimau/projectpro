@extends('layouts.app')

@section('title')
Create Produksi
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5>proses produksi</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("produksi.selesaiStore", $produksi->id) }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group mb-3">
                <label for="target" class="mb-2">Target produksi</label>
                <input class="form-control {{ $errors->has('target') ? 'is-invalid' : '' }}" value="{{ $produksi->target }}" disabled>
                @if($errors->has('target'))
                    <div class="invalid-feedback">
                        {{ $errors->first('target') }}
                    </div>
                @endif
            </div>
            <div class="form-group mb-3">
                <label for="biaya" class="mb-2">total biaya</label>
                <input class="form-control {{ $errors->has('biaya') ? 'is-invalid' : '' }}" value="{{ $produksi->biaya }}" disabled>
                @if($errors->has('biaya'))
                    <div class="invalid-feedback">
                        {{ $errors->first('biaya') }}
                    </div>
                @endif
            </div>
            <div class="form-group mb-3">
                <label for="hasil" class="mb-2">total hasil produksi</label>
                <input class="form-control {{ $errors->has('hasil') ? 'is-invalid' : '' }}" value="{{ $produksi->hasil }}" name="hasil">
                @if($errors->has('hasil'))
                    <div class="invalid-feedback">
                        {{ $errors->first('hasil') }}
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
