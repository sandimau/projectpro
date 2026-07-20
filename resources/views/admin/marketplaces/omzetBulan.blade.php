@extends('layouts.app')

@section('title')
    Omzet Marketplace per Bulan
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Omzet Marketplace Tahun {{ $tahun_skr }}</h5>
                <form action="{{ route('marketplaces.omzetBulan') }}" method="GET" class="d-flex align-items-center">
                    <label for="tahun" class="me-2 mb-0">Tahun:</label>
                    <select name="tahun" id="tahun" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                        @foreach ($listTahun as $tahun)
                            <option value="{{ $tahun }}" {{ $tahun == $tahun_skr ? 'selected' : '' }}>{{ $tahun }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
            <div class="card-body">
                <div id="omzetChart" style="height:600px;"></div>

                <div class="table-responsive mt-4">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">Bulan</th>
                                @foreach ($marketplaces as $marketplace)
                                    <th scope="col" class="text-end">{{ $marketplace->nama }}</th>
                                @endforeach
                                <th scope="col" class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $bulanData)
                                <tr>
                                    <td>{{ $bulanData['nama'] }}</td>
                                    @foreach ($marketplaces as $marketplace)
                                        <td class="text-end">
                                            {{ number_format($bulanData['omzet'][$marketplace->id] ?? 0, 0, ',', '.') }}
                                        </td>
                                    @endforeach
                                    <td class="text-end fw-bold">
                                        {{ number_format($totalsPerBulan[$loop->index] ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <td>Total</td>
                                @php $grandTotal = 0; @endphp
                                @foreach ($marketplaces as $marketplace)
                                    @php
                                        $mpTotal = $totalsPerMarketplace[$marketplace->id] ?? 0;
                                        $grandTotal += $mpTotal;
                                    @endphp
                                    <td class="text-end">{{ number_format($mpTotal, 0, ',', '.') }}</td>
                                @endforeach
                                <td class="text-end">{{ number_format($grandTotal, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const labels = @json($labels);
            const series = @json($chartSeries);

            function formatRupiahJuta(value) {
                const juta = value / 1_000_000;

                return juta.toLocaleString('id-ID', {
                    minimumFractionDigits: juta % 1 === 0 ? 0 : 2,
                    maximumFractionDigits: 2
                });
            }

            const options = {
                chart: {
                    type: 'bar',
                    height: 600,
                    stacked: true,
                    toolbar: {
                        show: true
                    }
                },
                series: series,
                xaxis: {
                    categories: labels
                },
                yaxis: {
                    labels: {
                        formatter: function(value) {
                            return formatRupiahJuta(value);
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(value) {
                            return formatRupiahJuta(value);
                        }
                    }
                },
                dataLabels: {
                    enabled: false
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        borderRadius: 4
                    }
                },
                legend: {
                    position: 'top'
                }
            };

            const chart = new ApexCharts(
                document.querySelector("#omzetChart"),
                options
            );

            chart.render();
        });
    </script>
@endpush
