@extends('layouts.app')

@section('title')
    Data Omzet
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card p-3">
            <div id="omzetChart" style="height:600px;"></div>
        </div>
    </div>
@endsection


@push('after-scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const labels = @json($chartData->map(fn($v) => "{$v->monthname} {$v->year}"));
            const omzet = @json($chartData->map(fn($v) => (float) $v->omzet));
            const omzetMP = @json($chartData->map(fn($v) => (float) $v->omzetMp));

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
                series: [{
                        name: 'Omzet',
                        data: omzet
                    },
                    {
                        name: 'Omzet MP',
                        data: omzetMP
                    }
                ],
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
                    enabled: true,
                    formatter: function(value) {
                        return formatRupiahJuta(value);
                    },
                    style: {
                        fontSize: '12px',
                        fontWeight: 'bold',
                        colors: ['#fff']
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        borderRadius: 4
                    }
                },
                colors: ['#2196f3', '#4caf50'],
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
