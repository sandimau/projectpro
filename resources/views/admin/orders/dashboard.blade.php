@extends('layouts.app')

@section('title')
    Proses Produksi
@endsection

@section('content')
    <header class="header mb-4">
        <div class="container-fluid">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb my-0 ms-2">
                    <li class="breadcrumb-item">
                        <b>Dashboard</b>
                    </li>
                </ol>
            </nav>
            @can('order_create')
                <a href="{{ route('order.create') }}" class="btn btn-primary rounded-pill text-white">Tambah Orders</a>
            @endcan
        </div>
    </header>
    <div class="bg-light rounded">
        @include('layouts.includes.messages')
        <div class="row">
            <div class="col-12">
                <ul class="nav nav-tabs" id="orderTab" role="tablist">
                    @foreach ($produksi as $item)
                        @if ($item->nama != 'finish' && $item->nama != 'batal')
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $loop->first ? 'active' : '' }} nav-nonaktif" id="{{ $item->nama }}-tab"
                                    data-bs-toggle="tab" data-bs-target="#{{ $item->nama }}" type="button" role="tab"
                                    aria-controls="{{ $item->nama }}" aria-selected="false">
                                    {{ $item->nama }}
                                    <span class="badge bg-success rounded-pill">{{ $item->orderDetail()->count() }}</span>
                                </button>
                            </li>
                        @endif
                    @endforeach
                </ul>
                <div class="tab-content" id="orderTabContent">
                    @foreach ($produksi as $item)
                        @if ($item->nama != 'finish' && $item->nama != 'batal')
                            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $item->nama }}"
                                role="tabpanel" aria-labelledby="{{ $item->nama }}-tab">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        @if ($item->orderDetail)
                                            @php
                                                $hasil = [];
                                                $tampilan = '';
                                                $order_id = 0;

                                                foreach ($item->orderDetail()->orderBy('order_id')->get() as $detail) {
                                                    // Filter: skip jika order_id tidak ada atau null
                                                    if (!$detail->order_id) {
                                                        continue;
                                                    }

                                                    if ($order_id != $detail->order_id) {
                                                        if ($order_id != 0) {
                                                            $tampilan .= '<div class=pull-right></div></a>';
                                                        }

                                                        $warna = '';
                                                        $nominal = '';
                                                        $order = $detail->order;

                                                        if ($order) {
                                                            $total = $order->total;
                                                            if ($total < 1000000) {
                                                                $warna = 'black';
                                                                if ($total == 0) {
                                                                    $nominal = 0;
                                                                } else {
                                                                    $nominal = floor($total / 1000) . 'rb';
                                                                }
                                                            } else {
                                                                if ($total <= 5000000) {
                                                                    $warna = 'green';
                                                                } elseif ($total <= 10000000) {
                                                                    $warna = '#FAA814';
                                                                } else {
                                                                    $warna = '#D93007';
                                                                }

                                                                $nominal = round($total, -5) / 1000000 . 'jt';
                                                            }

                                                            $konsumen = $order->kontak;
                                                            $konsumen_detail = $order->konsumen_detail;
                                                            $model_ar = $konsumen->ar ?? null;
                                                            $kode = $model_ar ? $model_ar->kode : '';
                                                            $test = $model_ar ? $model_ar->warna : '';
                                                            $tampilan .=
                                                                "<a class='popup d-flex'  href='" .
                                                                url('admin/order/' . $detail->order_id . '/detail') .
                                                                "' ><p style='font-weight:600' class='text-default'>";

                                                            $tampilan .=
                                                                " <span class='label label-rounded' style='background-color: " .
                                                                $test .
                                                                "'> " .
                                                                $kode .
                                                                '  </span>';

                                                            $tampilan .=
                                                                " <span class='label label-rounded mr-1' style='background-color: " .
                                                                $warna .
                                                                "'> " .
                                                                $nominal .
                                                                '  </span> ';

                                                            $tampilan .=
                                                                $konsumen->nama .
                                                                ' <span style="color:#222222">' .
                                                                $konsumen_detail .
                                                                '</span></p>';
                                                        }
                                                    }

                                                    $proses = '';
                                                    if (!empty($detail->process)) {
                                                        $proses =
                                                            "<span class='label label-info  label-rounded' style='background-color: " .
                                                            '#' .
                                                            $detail->process->warna .
                                                            ";'>" .
                                                            $detail->process->nama .
                                                            '</span>';
                                                    }

                                                    $nama_produk = '';
                                                    $nama_produk .= $detail->produk->namaLengkap;

                                                    $jadwalx = '';
                                                    if ($detail->deathline) {
                                                        $time1 = new DateTime(date('Y-m-d'));
                                                        $time2 = new DateTime($detail->deathline);
                                                        $interval = $time1->diff($time2)->format('%r%a');

                                                        $hasil = $interval;
                                                        if ($interval == 0) {
                                                            $hasil = ' hari ini';
                                                            $class = 'warning';
                                                        }
                                                        if ($interval == 1) {
                                                            $hasil = ' besok';
                                                            $class = 'info';
                                                        }
                                                        if ($interval > 1) {
                                                            $hasil = $interval . ' hari lagi';
                                                            $class = 'success';
                                                        }
                                                        if ($interval < 0) {
                                                            $hasil = $interval . ' hari';
                                                            $class = 'danger';
                                                        }

                                                        $jadwalx =
                                                            " <small> <span class='badge text-white text-bg-" .
                                                            $class .
                                                            "''>" .
                                                            $hasil .
                                                            '</span></small>';
                                                    }

                                                    $tampilan .=
                                                        "<span style='color:#636363; padding-right:5px;'> " .
                                                        $nama_produk .
                                                        ', ' .
                                                        $proses .
                                                        $jadwalx .
                                                        '</span> ';

                                                    $order_id = $detail->order_id;
                                                }

                                                if ($order_id != 0) {
                                                    $tampilan .= '<div class=pull-right></div></a>';
                                                }

                                                echo $tampilan;
                                            @endphp
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Detail Order -->
    <div class="modal fade" id="detailOrderModal" tabindex="-1" aria-labelledby="detailOrderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-lg-down modal-dialog-scrollable modal-dialog-centered modal-xxl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailOrderModalLabel">Detail Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detailOrderBody">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
    <script>
        let table = new DataTable('#myTable');

        (function() {
            const modalEl = document.getElementById('detailOrderModal');
            const modalBody = document.getElementById('detailOrderBody');
            const modalTitle = document.getElementById('detailOrderModalLabel');
            const bsModal = new bootstrap.Modal(modalEl);

            const spinner = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>`;

            document.addEventListener('click', function(e) {
                const link = e.target.closest('a.popup');
                if (!link) return;

                e.preventDefault();
                const url = link.getAttribute('href');

                modalBody.innerHTML = spinner;
                modalTitle.textContent = 'Detail Order';
                bsModal.show();

                fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(function(res) {
                        if (!res.ok) throw new Error('Gagal memuat (' + res.status + ')');
                        return res.text();
                    })
                    .then(function(html) {
                        const doc = new DOMParser().parseFromString(html, 'text/html');
                        // Ambil hanya konten utama dari halaman detail
                        const content = doc.querySelector('.body .container-fluid .mb-4') ||
                            doc.querySelector('.body .container-fluid') ||
                            doc.querySelector('.body');

                        modalBody.innerHTML = content ? content.innerHTML : html;
                    })
                    .catch(function(err) {
                        modalBody.innerHTML =
                            '<div class="alert alert-danger">' + err.message + '</div>';
                    });
            });
        })();
    </script>
    <style>
        @media (min-width: 992px) {
            .modal-xxl {
                max-width: 96%;
            }

            .modal-xxl.modal-dialog-scrollable {
                height: calc(100% - 2rem);
            }
        }

        a {
            text-decoration: none;
        }

        .text-default {
            font-weight: 700 !important;
            margin: 0;
            padding: 10px 5px;
            color: #398bf7 !important;
        }

        .label {
            font-weight: 400;
            font-size: 13px;
            color: #ffffff;
            padding: 2px 5px;
            border-radius: 5px;
            margin-right: 8px;
        }

        .popup {
            align-items: center;
            border-bottom: 1px solid #e9e9e9;
        }

        .popup:hover {
            background-color: #e0e0e0;
            border-radius: 6px;
        }
    </style>
@endpush
