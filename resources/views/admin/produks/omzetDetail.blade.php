@extends('layouts.app')

@section('title')
    Data Aset dan Omzet Produk
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Aset dan Omzet Produk</h4>
            <form method="get" class="d-flex align-items-center">
                <label for="month" class="me-2">Pilih Bulan:</label>
                <select name="month" id="month" class="form-select me-2" onchange="this.form.submit()">
                    @foreach(range(1, 12) as $month)
                        <option value="{{ $month }}" {{ $month == $selectedMonth ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $month, 1)) }}
                        </option>
                    @endforeach
                </select>
                <label for="year" class="me-2">Pilih Tahun:</label>
                <select name="year" id="year" class="form-select" onchange="this.form.submit()">
                    @foreach($years as $year)
                        <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive"></div>
                <table class="table table-bordered table-sm m-0">
                    <thead>
                        <tr>
                            <th class="text-nowrap sticky-col first-col">Nama Produk</th>
                            <th class="text-nowrap sticky-col second-col">Varian</th>
                            <th class="text-nowrap sticky-col third-col">Stok</th>
                            <th class="text-nowrap sticky-col fourth-col">rata2 <br> penjualan</th>
                            @php
                                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $selectedMonth, $selectedYear);
                            @endphp
                            @for ($i = 1; $i <= $daysInMonth; $i++)
                                <th class="text-center">{{ $i }}</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $currentProduct = null;
                        @endphp
                        @foreach($products as $product)
                            <tr>
                                @if($currentProduct !== $product->nama_produk)
                                    <td class="text-nowrap sticky-col first-col" rowspan="{{ $products->where('nama_produk', $product->nama_produk)->count() }}">
                                        {{ $product->nama_produk }}
                                    </td>
                                    @php
                                        $currentProduct = $product->nama_produk;
                                    @endphp
                                @endif
                                <td class="text-nowrap sticky-col second-col">{{ $product->varian }}</td>
                                <td class="text-nowrap sticky-col third-col">{{ $product->stok }}</td>
                                <td class="text-nowrap sticky-col fourth-col">{{ $product->rata_penjualan }}</td>
                                @for ($i = 1; $i <= $daysInMonth; $i++)
                                    @php
                                        $sale = $product->daily_sales[$i] ?? 0;
                                    @endphp
                                    <td class="text-center @if($sale > 0) text-{{ $sale > 100 ? 'danger' : 'primary' }} @endif">
                                        {{ $sale }}
                                    </td>
                                @endfor
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table td, .table th {
        vertical-align: middle;
        white-space: nowrap;
        padding: 0.5rem;
    }
    .form-select {
        width: auto;
    }
    /* Make the table header sticky */
    .table thead th {
        position: sticky;
        top: 0;
        background: white;
        z-index: 1;
    }
    /* Custom scrollbar for better appearance */
    .table-responsive::-webkit-scrollbar {
        height: 8px;
        width: 8px;
    }
    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    .table-responsive::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    /* Sticky columns */
    .sticky-col {
        position: sticky;
        background: white;
        z-index: 1;
    }

    .first-col {
        left: 0;
    }

    .second-col {
        left: 200px;
    }

    .third-col {
        left: 400px;
    }

    .fourth-col {
        left: 500px;
    }

    /* Add box-shadow for better visual separation */
    .sticky-col::after {
        content: '';
        position: absolute;
        top: 0;
        right: -5px;
        height: 100%;
        width: 5px;
        background: linear-gradient(to right, rgba(0,0,0,0.1), rgba(0,0,0,0));
    }
</style>
@endpush
