@extends('layouts.app')

@section('title')
    Tambah Produk Produksi
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5>Tambah Produk Produksi</h5>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('produkProduksi.store') }}" enctype="multipart/form-data">
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
                    <label class="mb-2">Satuan Perbandingan</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input type="radio" name="satuan" id="radio_berat" value="berat"
                                class="form-check-input {{ $errors->has('satuan') ? 'is-invalid' : '' }}"
                                {{ old('satuan') == 'berat' ? 'checked' : '' }}>
                            <label class="form-check-label" for="radio_berat">Berat</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="satuan" id="radio_luas" value="luas"
                                class="form-check-input {{ $errors->has('satuan') ? 'is-invalid' : '' }}"
                                {{ old('satuan') == 'luas' ? 'checked' : '' }}>
                            <label class="form-check-label" for="radio_luas">Luas</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="satuan" id="radio_persen" value="persen"
                                class="form-check-input {{ $errors->has('satuan') ? 'is-invalid' : '' }}"
                                {{ old('satuan') == 'persen' ? 'checked' : '' }}>
                            <label class="form-check-label" for="radio_persen">Persen</label>
                        </div>
                    </div>
                    @if ($errors->has('satuan'))
                        <div class="invalid-feedback d-block">
                            {{ $errors->first('satuan') }}
                        </div>
                    @endif
                </div>

                <div class="form-group mb-3" id="panjang-group" style="display: none;">
                    <label for="panjang" class="mb-2">Panjang</label>
                    <input type="number" name="panjang" id="panjang" value="{{ old('panjang', '') }}" min="0"
                        class="form-control {{ $errors->has('panjang') ? 'is-invalid' : '' }}">
                    @if ($errors->has('panjang'))
                        <div class="invalid-feedback">
                            {{ $errors->first('panjang') }}
                        </div>
                    @endif
                </div>

                <div class="form-group mb-3" id="lebar-group" style="display: none;">
                    <label for="lebar" class="mb-2">Lebar</label>
                    <input type="number" name="lebar" id="lebar" value="{{ old('lebar', '') }}" min="0"
                        class="form-control {{ $errors->has('lebar') ? 'is-invalid' : '' }}">
                    @if ($errors->has('lebar'))
                        <div class="invalid-feedback">
                            {{ $errors->first('lebar') }}
                        </div>
                    @endif
                </div>

                <div class="form-group mb-3" id="perbandingan-group" style="display: none;">
                    <label for="perbandingan" class="mb-2">Perbandingan</label>
                    <input type="number" name="perbandingan" id="perbandingan" value="{{ old('perbandingan', '') }}"
                        min="0" class="form-control {{ $errors->has('perbandingan') ? 'is-invalid' : '' }}">
                    @if ($errors->has('perbandingan'))
                        <div class="invalid-feedback">
                            {{ $errors->first('perbandingan') }}
                        </div>
                    @endif
                </div>

                <div class="form-group">
                    <button class="btn btn-primary mt-4" type="submit">
                        Simpan
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
            const satuanRadios = document.querySelectorAll('input[name="satuan"]');
            const panjangGroup = document.getElementById('panjang-group');
            const lebarGroup = document.getElementById('lebar-group');
            const perbandinganGroup = document.getElementById('perbandingan-group');

            function toggleFields() {
                const selectedSatuan = document.querySelector('input[name="satuan"]:checked');

                if (!selectedSatuan) {
                    // Jika tidak ada yang dipilih, sembunyikan semua
                    panjangGroup.style.display = 'none';
                    lebarGroup.style.display = 'none';
                    perbandinganGroup.style.display = 'none';
                    return;
                }

                const satuanValue = selectedSatuan.value;

                if (satuanValue === 'luas') {
                    // Jika luas dipilih, tampilkan panjang dan lebar, sembunyikan perbandingan
                    panjangGroup.style.display = 'block';
                    lebarGroup.style.display = 'block';
                    perbandinganGroup.style.display = 'none';
                } else if (satuanValue === 'berat' || satuanValue === 'persen') {
                    // Jika berat atau persen dipilih, tampilkan perbandingan, sembunyikan panjang dan lebar
                    panjangGroup.style.display = 'none';
                    lebarGroup.style.display = 'none';
                    perbandinganGroup.style.display = 'block';
                } else {
                    // Default: sembunyikan semua
                    panjangGroup.style.display = 'none';
                    lebarGroup.style.display = 'none';
                    perbandinganGroup.style.display = 'none';
                }
            }

            satuanRadios.forEach(radio => {
                radio.addEventListener('change', toggleFields);
            });

            // Set initial state
            toggleFields();
        });
        new Autocomplete('#autocompleteProduk', {
            search: input => {
                const url = "{{ url('admin/produksi/api?q=') }}" + `${escape(input)}`;
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
