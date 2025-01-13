@extends('layouts.app')

@section('title')
    Data Omzet
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <canvas id="myChart"></canvas>
        </div>
    </div>
@endsection

@push('after-scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('myChart');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [
                    @php
                        foreach ($orders as $value) {
                            echo $value->year . ',';
                        }
                    @endphp
                ],
                datasets: [{
                    label: 'omzet',
                    data: [
                        @php
                            foreach ($orders as $value) {
                                echo $value->sum . ',';
                            }
                        @endphp
                    ],
                    borderWidth: 1
                }],
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endpush
