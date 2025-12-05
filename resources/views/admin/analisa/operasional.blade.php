@extends('layouts.app')

@section('title')
    Analisa Operasional
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Analisa Operasional</h5>
                        <h6 class="card-subtitle mb-2 text-muted">Analisa beban operasional per kategori per bulan</h6>
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
                        <canvas id="chartOperasional" style="height: 400px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    let chartOperasional = null;

    function loadData() {
        const tahun = $('#tahun').val();

        $.ajax({
            url: '{{ route('analisa.operasional.data') }}',
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
        if (!data || data.length === 0) {
            console.warn('No data to render');
            return;
        }

        // Ambil semua kategori yang ada dari data
        const allKategori = new Set();
        data.forEach(item => {
            Object.keys(item).forEach(key => {
                if (key !== 'bulan' && key !== 'nama_bulan') {
                    allKategori.add(key);
                }
            });
        });

        const labels = data.map(item => item.nama_bulan);
        const kategoriArray = Array.from(allKategori);

        // Warna untuk setiap kategori
        const colors = [
            '#4472C4', '#70AD47', '#7030A0', '#5B9BD5', '#FFC000',
            '#C55A11', '#E7E6E6', '#A5A5A5', '#FF0000', '#00B0F0',
            '#92D050', '#0070C0', '#7030A0', '#C00000', '#00B050'
        ];

        const datasets = kategoriArray.map((kategori, index) => {
            return {
                label: kategori.replace(/_/g, ' '),
                data: data.map(item => item[kategori] || 0),
                backgroundColor: colors[index % colors.length],
                borderColor: colors[index % colors.length],
                borderWidth: 1
            };
        });

        const ctx = document.getElementById('chartOperasional');

        // Destroy existing chart if it exists
        if (chartOperasional) {
            chartOperasional.destroy();
        }

        chartOperasional = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: datasets
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
                            const kategori = chart.data.datasets[datasetIndex].label.replace(/\s/g, '_').toLowerCase();

                            // Redirect ke halaman operasional dengan filter bulan dan kategori
                            const url = '{{ url('admin/operasional') }}?bulan=' + urlBulan + '&kategori=' + kategori;
                            console.log('URL redirect:', url);
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
                                    const kategori = chart.data.datasets[clickedDatasetIndex].label.replace(/\s/g, '_').toLowerCase();

                                    const url = '{{ url('admin/operasional') }}?bulan=' + urlBulan + '&kategori=' + kategori;
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
