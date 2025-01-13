@extends('layouts.app')

@section('title')
    add gambar
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title">add gambar</h5>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('orderDetail.upload') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="order_detail_id" value="{{ $detail->id }}">
                <div class="mb-3">
                    <label for="formFile" class="form-label">gambar</label>
                    <input class="form-control" type="file" id="formFile" name="gambar">
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
