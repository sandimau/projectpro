@extends('layouts.app')

@section('title')
    Analisa Beban
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Analisa Beban</h5>
                        <h6 class="card-subtitle mb-2 text-muted">Analisa beban operasional per bulan</h6>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <label for="tahun">Tahun</label>
                        <select class="form-control" id="tahun" name="tahun">
                            @for ($i = date('Y'); $i >= date('Y') - 1; $i--)
                                <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-primary d-block" id="btnCari">Cari</button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <canvas id="chartBeban" style="height: 400px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    let chartBeban = null;

    function loadData() {
        const tahun = $('#tahun').val();

        $.ajax({
            url: '{{ route('analisa.beban.data') }}',
            method: 'GET',
            data: { tahun: tahun },
            success: function(response) {
                renderChart(response);
            },
            error: function(xhr) {
                console.error('Error loading data:', xhr);
                alert('Gagal memuat data. Silakan coba lagi.');
            }
        });
    }

    function renderChart(data) {
        const labels = data.map(item => item.nama_bulan);
        const operasional = data.map(item => item.operasional);
        const penggajian = data.map(item => item.penggajian);
        const tunjangan = data.map(item => item.tunjangan);
        const pemakaianStok = data.map(item => item.pemakaian_stok);

        const ctx = document.getElementById('chartBeban');

        // Destroy existing chart if it exists
        if (chartBeban) {
            chartBeban.destroy();
        }

        chartBeban = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'operasional',
                        data: operasional,
                        backgroundColor: '#4472C4',
                        borderColor: '#4472C4',
                        borderWidth: 1
                    },
                    {
                        label: 'penggajian',
                        data: penggajian,
                        backgroundColor: '#70AD47',
                        borderColor: '#70AD47',
                        borderWidth: 1
                    },
                    {
                        label: 'tunjangan',
                        data: tunjangan,
                        backgroundColor: '#7030A0',
                        borderColor: '#7030A0',
                        borderWidth: 1
                    },
                    {
                        label: 'pemakaian_stok',
                        data: pemakaianStok,
                        backgroundColor: '#5B9BD5',
                        borderColor: '#5B9BD5',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                onClick: function(evt, activeElements, chart) {
                    if (activeElements.length > 0) {
                        const points = chart.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);

                        if (points.length > 0) {
                            const point = points[0];
                            const dataIndex = point.index;
                            const datasetIndex = point.datasetIndex;
                            const bulan = data[dataIndex].bulan;
                            const tahun = $('#tahun').val();
                            const bulanFormatted = bulan < 10 ? '0' + bulan : bulan;
                            const urlBulan = tahun + '-' + bulanFormatted;

                            // Tentukan URL berdasarkan kategori yang diklik
                            let url = '';
                            const kategori = chart.data.datasets[datasetIndex].label;

                            console.log('Kategori diklik:', kategori, 'Dataset Index:', datasetIndex); // Untuk debugging

                            switch(kategori) {
                                case 'operasional':
                                    url = '{{ url('admin/operasional') }}?bulan=' + urlBulan;
                                    break;
                                case 'penggajian':
                                    url = '{{ url('admin/penggajian') }}?bulan=' + urlBulan;
                                    break;
                                case 'tunjangan':
                                    url = '{{ url('admin/tunjangan') }}?bulan=' + urlBulan;
                                    break;
                                case 'pemakaian_stok':
                                    url = '{{ url('admin/produk-stok') }}?bulan=' + urlBulan;
                                    break;
                                default:
                                    url = '{{ url('admin/operasional') }}?bulan=' + urlBulan;
                            }

                            console.log('URL redirect:', url); // Untuk debugging

                            // Redirect ke halaman sesuai kategori
                            window.location.href = url;
                        } else {
                            // Fallback: gunakan koordinat mouse untuk menentukan segmen yang diklik
                            const canvasPosition = Chart.helpers.getRelativePosition(evt, chart);
                            const x = canvasPosition.x;
                            const y = canvasPosition.y;

                            // Cari bar yang diklik berdasarkan posisi X
                            const meta = chart.getDatasetMeta(0);
                            let clickedDataIndex = -1;

                            for (let i = 0; i < meta.data.length; i++) {
                                const bar = meta.data[i];
                                if (x >= bar.x - bar.width / 2 && x <= bar.x + bar.width / 2) {
                                    clickedDataIndex = i;
                                    break;
                                }
                            }

                            if (clickedDataIndex >= 0) {
                                // Tentukan dataset berdasarkan posisi Y
                                let clickedDatasetIndex = -1;
                                let cumulativeY = chart.scales.y.getPixelForValue(0);

                                for (let i = 0; i < chart.data.datasets.length; i++) {
                                    const dataset = chart.data.datasets[i];
                                    const value = dataset.data[clickedDataIndex];

                                    if (value > 0) {
                                        const meta = chart.getDatasetMeta(i);
                                        const element = meta.data[clickedDataIndex];
                                        const segmentTop = element.y;
                                        const segmentBottom = element.y + element.height;

                                        if (y >= segmentTop && y <= segmentBottom) {
                                            clickedDatasetIndex = i;
                                            break;
                                        }
                                    }
                                }

                                if (clickedDatasetIndex >= 0) {
                                    const bulan = data[clickedDataIndex].bulan;
                                    const tahun = $('#tahun').val();
                                    const bulanFormatted = bulan < 10 ? '0' + bulan : bulan;
                                    const urlBulan = tahun + '-' + bulanFormatted;

                                    const kategori = chart.data.datasets[clickedDatasetIndex].label;
                                    let url = '';

                                    switch(kategori) {
                                        case 'operasional':
                                            url = '{{ url('admin/operasional') }}?bulan=' + urlBulan;
                                            break;
                                        case 'penggajian':
                                            url = '{{ url('admin/penggajian') }}?bulan=' + urlBulan;
                                            break;
                                        case 'tunjangan':
                                            url = '{{ url('admin/tunjangan') }}?bulan=' + urlBulan;
                                            break;
                                        case 'pemakaian_stok':
                                            url = '{{ url('admin/produk-stok') }}?bulan=' + urlBulan;
                                            break;
                                        default:
                                            url = '{{ url('admin/operasional') }}?bulan=' + urlBulan;
                                    }

                                    console.log('URL redirect (fallback):', url);
                                    window.location.href = url;
                                }
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: false,
                            boxWidth: 15,
                            padding: 10
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += new Intl.NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR',
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 0
                                }).format(context.parsed.y);
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        grid: {
                            color: '#e0e0e0'
                        },
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                onHover: function(evt, activeElements) {
                    evt.native.target.style.cursor = activeElements.length > 0 ? 'pointer' : 'default';
                }
            },
            plugins: [{
                id: 'dataLabels',
                afterDatasetsDraw: function(chart) {
                    const ctx = chart.ctx;

                    // Fungsi untuk menyederhanakan angka
                    function formatNumber(num) {
                        if (num >= 1000000) {
                            return (num / 1000000).toFixed(1) + 'M';
                        } else if (num >= 1000) {
                            return (num / 1000).toFixed(0) + 'K';
                        } else {
                            return num.toFixed(0);
                        }
                    }

                    chart.data.datasets.forEach((dataset, i) => {
                        const meta = chart.getDatasetMeta(i);
                        if (!meta.hidden) {
                            meta.data.forEach((element, index) => {
                                const data = dataset.data[index];
                                if (data > 0) {
                                    ctx.fillStyle = 'white';
                                    ctx.font = 'bold 12px Arial';
                                    ctx.textAlign = 'center';
                                    ctx.textBaseline = 'middle';

                                    // Hitung posisi tengah dari setiap segmen bar
                                    const barHeight = element.height;
                                    const y = element.y + (barHeight / 2);
                                    const x = element.x;

                                    // Tampilkan angka yang disederhanakan
                                    ctx.fillText(formatNumber(data), x, y);
                                }
                            });
                        }
                    });
                }
            }]
        });
    }

    $(document).ready(function() {
        loadData();

        $('#btnCari').on('click', function() {
            loadData();
        });

        $('#tahun').on('change', function() {
            loadData();
        });
    });
</script>
@endpush
