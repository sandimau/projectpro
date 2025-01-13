@extends('layouts.app')

@section('title')
    Data Omzet
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <canvas id="speedChart"></canvas>
        </div>
    </div>
    @php @endphp
@endsection

@push('after-scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var speedCanvas = document.getElementById("speedChart");

        var myChart = new Chart(speedCanvas, {
            type: 'line',
            data: {
                labels: [
                    @php
                        foreach ($years as $value) {
                            echo '"' . $value->monthname . ' ' . $value->year . '",';
                        }
                    @endphp
                ],
                datasets: [{
                    label: 'omzet', // Name the series
                    data: [
                        @php
                            foreach ($years as $value) {
                                echo $value->omzet . ',';
                            }
                        @endphp
                    ], // Specify the data values array
                    fill: false,
                    borderColor: '#2196f3', // Add custom color border (Line)
                    backgroundColor: '#2196f3', // Add custom color background (Points and Fill)
                    borderWidth: 1 // Specify bar border width
                }]},
            options: {
            responsive: true, // Instruct chart js to respond nicely.
            maintainAspectRatio: false, // Add to prevent default behaviour of full-width/height
            }
        });



        // var dataFirst = {
        //     label: @php
        //         $data = null;
        //         if ($last->first()) {
        //             $data = $last->first()->year;
        //         }
        //     echo '"omzet ' . $data . '"'; @endphp,
        //     data: [
        //         @php
        //             foreach ($last as $value) {
        //                 echo $value->omzet . ',';
        //             }
        //         @endphp
        //     ],
        //     lineTension: 0,
        //     fill: false,
        //     borderColor: 'red'
        // };

        // var dataSecond = {
        //     label: @php echo '"omzet '.$years->first()->year . '"' @endphp,
        //     data: [
        //         @php
        //             foreach ($years as $value) {
        //                 echo $value->omzet . ',';
        //             }
        //         @endphp
        //     ],
        //     lineTension: 0,
        //     fill: false,
        //     borderColor: 'blue'
        // };

        // var speedData = {
        //     labels: [
        //         @php
        //             foreach ($years as $value) {
        //                 echo '"' . $value->monthname . '",';
        //             }
        //         @endphp
        //     ],
        //     datasets: [dataFirst, dataSecond]
        // };

        // var chartOptions = {
        //     legend: {
        //         display: true,
        //         position: 'top',
        //         labels: {
        //             boxWidth: 80,
        //             fontColor: 'black'
        //         }
        //     }
        // };

        // var lineChart = new Chart(speedCanvas, {
        //     type: 'line',
        //     data: speedData,
        //     options: chartOptions
        // });
    </script>
@endpush
