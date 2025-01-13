@extends('layouts.app')

@section('title')
    Create Orders
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title">add order</h5>
                </div>
                <a href="{{ route('belanja.index') }}" class="btn btn-success ">back</a>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('belanja.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group mb-3">
                    <label for="nama" class="mb-2">Supplier</label>
                    <div id="autocomplete" class="autocomplete">
                        <input class="autocomplete-input {{ $errors->has('kontak_id') ? 'is-invalid' : '' }}"
                            placeholder="cari supplier" aria-label="cari kontak">
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
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <div class="input-group mb-3"><span class="input-group-text" id="basic-addon3">no nota</span>
                                <input class="form-control" id="basic-url" type="text" name="nota"
                                    aria-describedby="basic-addon3">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <input class="form-control {{ $errors->has('tanggal_beli') ? 'is-invalid' : '' }}"
                                type="date" name="tanggal_beli" id="tanggal_beli" value="<?php echo date('Y-m-d'); ?>">
                            @if ($errors->has('tanggal_beli'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('tanggal_beli') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="table-responsive m-t-15">
                        <table class="table" id="table-barang">
                            <thead>
                                <tr style="background-color: #e9ecef">
                                    <th style="width:300px">Barang</th>
                                    <th>keterangan</th>
                                    <th>satuan</th>
                                    <th>jumlah</th>
                                    <th>harga</th>
                                    <th>subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="table_body">
                                @for ($i = 1; $i < 10; $i++)
                                    <tr>
                                        <td>
                                            <div id="autocomplete{{ $i }}" class="autocomplete">
                                                <input id="hasilInput{{ $i }}" class="autocomplete-input" />
                                                <ul class="autocomplete-result-list"></ul>
                                            </div>
                                            <input type="hidden" name="barang_beli_id[]"
                                                id="dataBarang{{ $i }}">
                                            <span id="closeBarang{{ $i }}"></span>
                                        </td>
                                        <td>
                                            <input id="ket{{ $i }}" name="keterangan[]" class="form-control"
                                                type="text" />
                                        </td>
                                        <td>
                                            <input id="satuan{{ $i }}" readonly class="form-control"
                                                type="text" />
                                        </td>
                                        <td>
                                            <input id="jumlah{{ $i }}" name="jumlah[]" step=".01"
                                                class="form-control" type="number" />
                                        </td>
                                        <td>
                                            <input id="harga{{ $i }}" name="harga[]" class="form-control"
                                                step=".01" type="number" />
                                        </td>
                                        <td>
                                            <input id="subtotal{{ $i }}" readonly
                                                class="form-control text-right" type="number" />
                                        </td>
                                    </tr>
                                @endfor
                            </tbody>
                            <tfoot>
                                <hr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right">
                                        <span>diskon</span><br>
                                    </td>
                                    <td class="text-right">
                                        <input onchange="updateSubTotal()" id="diskon" name="diskon"
                                            class="form-control text-right" type="number" />
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right">
                                        <b><span>Total</span></b> <br>
                                    </td>
                                    <td class="text-right">
                                        <input id="total" name="total" class="form-control text-right" type="number"
                                            readonly />
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right">
                                        <b><span>Pembayaran</span></b> <br>
                                    </td>
                                    <td class="text-right">
                                        <input id="pembayaran" name="pembayaran" class="form-control text-right {{ $errors->has('pembayaran') ? 'is-invalid' : '' }}"
                                            type="number" />
                                        @if ($errors->has('pembayaran'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('pembayaran') }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right">
                                        <b><span>kas</span></b> <br>
                                    </td>
                                    <td>
                                        <div class="form-group mb-3">
                                            <select
                                                class="form-select {{ $errors->has('akun_detail_id') ? 'is-invalid' : '' }}"
                                                name="akun_detail_id" id="akun_detail_id">
                                                @foreach ($kas as $id => $entry)
                                                    <option value="{{ $id }}"
                                                        {{ old('akun_detail_id') == $id ? 'selected' : '' }}>
                                                        {{ $entry }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('akun_detail_id'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('akun_detail_id') }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
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

        function clearData() {
            let btn = document.getElementById("closeBrg");
            btn.style.display = "none";
            let auto = document.querySelector(".autocomplete-input");
            auto.value = null;
            let idProduk = document.getElementById('kontakId');
            idProduk.value = null;
        }

        for (let i = 1; i < 10; i++) {
            new Autocomplete('#autocomplete' + i, {
                search: input => {
                    const url = "{{ url('admin/produkBeli/api?q=') }}" + `${escape(input)}`;
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
                    //insert id barang
                    let dataBarang = document.getElementById('dataBarang' + i);
                    dataBarang.value = result.id;

                    //set satuan
                    let satuan = document.getElementById("satuan" + i);
                    satuan.value = result.satuan;

                    //set ket
                    let ket = document.getElementById("ket" + i);

                    //set jumlah
                    let jumlah = document.getElementById("jumlah" + i);
                    jumlah.value = result.jumlah;

                    //set harga
                    let harga = document.getElementById("harga" + i);
                    if (result.harga_beli) {
                        harga.value = result.harga_beli;
                    } else {
                        harga.value = result.harga;
                    }
                    harga.onchange = function() {
                        updateHarga(i);
                    };

                    //set subtotal
                    let subtotal = document.getElementById("subtotal" + i);
                    jumlah.onchange = function() {
                        subtotal.value = harga.value * jumlah.value;
                        updateSubTotal();
                    };

                    let btn = document.getElementById("closeBarang" + i);
                    btn.style.display = "block";
                    btn.innerHTML =
                        `<button onclick="clearAuto(${i})" type="button" class="btn rounded-pill btn-sm btn-warning"><i class='bx bx-x-circle' ></i></button>`;
                },
            })
        }

        function clearAuto(result) {
            let auto = document.querySelector("#hasilInput" + result);
            auto.value = null;

            //insert id barang
            let dataBarang = document.getElementById('dataBarang' + result);
            dataBarang.value = null;

            //set satuan
            let satuan = document.getElementById("satuan" + result);
            satuan.value = null;

            //set ket
            let ket = document.getElementById("ket" + result);
            ket.value = null;

            //set jumlah
            let jumlah = document.getElementById("jumlah" + result);
            jumlah.value = null;

            //set harga
            let harga = document.getElementById("harga" + result);
            harga.value = null;

            //set subtotal
            let subtotal = document.getElementById("subtotal" + result);
            subtotal.value = null;

            let btn = document.getElementById("closeBarang" + result);
            btn.style.display = "none";
            updateSubTotal();
        }

        function updateHarga(i) {
            //set jumlah
            let jumlah = document.getElementById("jumlah" + i);
            //set harga
            let harga = document.getElementById("harga" + i);

            //set subtotal
            let subtotal = document.getElementById("subtotal" + i);
            subtotal.value = harga.value * jumlah.value;
            updateSubTotal();
        }

        function updateSubTotal() {
            let sum = [];
            for (let i = 1; i < 10; i++) {
                let subtotal = document.getElementById("subtotal" + i);
                if (subtotal.value != '') {
                    sum.push(parseFloat(subtotal.value));
                }
            }
            let total = document.getElementById('total');
            let pembayaran = document.getElementById('pembayaran');
            let diskon = document.getElementById('diskon');
            total.value = sum.reduce((a, b) => a + b, 0) - diskon.value;
            pembayaran.value = sum.reduce((a, b) => a + b, 0) - diskon.value;
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
    @for ($i = 1; $i < 10; $i++)
        <style>
            #closeBarang{{ $i }} {
                position: relative;
            }

            #closeBarang{{ $i }} button {
                position: absolute;
                right: -15px;
                top: -40px;
            }

            .autocomplete-result-list {
                z-index: 50 !important;
            }
        </style>
    @endfor
@endpush
