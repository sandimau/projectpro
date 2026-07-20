@extends('layouts.app')

@section('title')
    Scan Absensi
@endsection

@push('after-styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <style>
        .stats-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            border-radius: 0.75rem;
            background-color: rgba(13, 110, 253, 0.15);
        }
        .stats-icon.green { background-color: rgba(25, 135, 84, 0.15); color: #198754; }
        .stats-icon.orange { background-color: rgba(255, 193, 7, 0.15); color: #ffc107; }
        .stats-icon.red { background-color: rgba(220, 53, 69, 0.15); color: #dc3545; }
        .action-panel {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .clock-time {
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: 2px;
            line-height: 1.2;
        }
        #qr-reader {
            width: 100%;
            border: 2px solid var(--bs-border-color);
            border-radius: 8px;
            overflow: hidden;
        }
        #qr-reader__dashboard_section_csr { display: none !important; }
    </style>
@endpush

@section('content')
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stats-icon green"><i class='bx bx-check-circle fs-4'></i></div>
                    <div>
                        <div class="text-muted small">Hadir Bulan Ini</div>
                        <div class="fw-bold">{{ $stats['hadir'] }} Hari</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stats-icon orange"><i class='bx bx-time fs-4'></i></div>
                    <div>
                        <div class="text-muted small">Terlambat</div>
                        <div class="fw-bold">{{ $stats['terlambat'] }} Kali</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stats-icon red"><i class='bx bx-x-circle fs-4'></i></div>
                    <div>
                        <div class="text-muted small">Tidak Hadir</div>
                        <div class="fw-bold">{{ $stats['absen'] }} Hari</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="action-panel">
            <div>
                <div id="clock" class="clock-time">00:00:00</div>
                <div class="text-muted">{{ now()->translatedFormat('l, d F Y') }}</div>
                <div class="small text-muted mt-1">{{ $member->nama_lengkap }}</div>
            </div>
            <div>
                @if ($clockOutToday)
                    <button class="btn btn-success" disabled>
                        <i class='bx bx-check-circle'></i> Selesai
                    </button>
                @elseif ($clockInToday)
                    <button id="scan-button" class="btn btn-danger">
                        <i class='bx bx-qr-scan'></i> Absen Pulang
                    </button>
                @else
                    <button id="scan-button" class="btn btn-primary">
                        <i class='bx bx-qr-scan'></i> Absen Masuk
                    </button>
                @endif
            </div>
        </div>
        @if ($clockInToday && !$clockOutToday)
            <div class="card-footer pt-0 border-0">
                <p class="text-muted small mb-0">
                    Anda sudah absen masuk pada pukul <strong>{{ \Carbon\Carbon::parse($clockInToday->attendance_time)->format('H:i') }} WIB</strong>.
                </p>
            </div>
        @endif
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Riwayat Terakhir</h5>
            <a href="{{ route('absensi.riwayat') }}" class="btn btn-sm btn-outline-secondary">Lihat Semua</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Tipe</th>
                            <th>Waktu</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($history as $item)
                            <tr>
                                <td>{{ $item->attendance_date->translatedFormat('d F Y') }}</td>
                                <td>
                                    @if ($item->type === 'clock_in')
                                        <span class="badge bg-primary">Masuk</span>
                                    @else
                                        <span class="badge bg-danger">Pulang</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($item->attendance_time)->format('H:i') }} WIB</td>
                                <td>{{ ucfirst($item->status) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada riwayat absensi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="scannerModal" tabindex="-1" aria-labelledby="scannerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="scannerModalLabel">Scan QR Code Absensi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p class="text-muted mb-3">Arahkan kamera ke QR Code yang tersedia di kantor.</p>
                    <div id="qr-reader"></div>
                    <div id="scanner-loading" class="mt-3" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 mb-0">Memproses data...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const clockElement = document.getElementById('clock');
            function updateClock() {
                if (!clockElement) return;
                clockElement.textContent = new Date().toLocaleTimeString('en-US', {
                    hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit'
                });
            }
            setInterval(updateClock, 1000);
            updateClock();

            const scanButton = document.getElementById('scan-button');
            if (!scanButton) return;

            const scannerModalElement = document.getElementById('scannerModal');
            const scannerModal = new bootstrap.Modal(scannerModalElement);
            const scannerLoading = document.getElementById('scanner-loading');
            let html5QrcodeScanner;

            const showToast = (message, isSuccess = true) => {
                Toastify({
                    text: message,
                    duration: isSuccess ? 3000 : 5000,
                    close: true,
                    gravity: 'top',
                    position: 'right',
                    backgroundColor: isSuccess
                        ? 'linear-gradient(to right, #00b09b, #96c93d)'
                        : 'linear-gradient(to right, #ff5f6d, #ffc371)',
                    stopOnFocus: true
                }).showToast();
            };

            const onScanSuccess = async (decodedText) => {
                scannerLoading.style.display = 'block';
                if (html5QrcodeScanner && html5QrcodeScanner.isScanning) {
                    try { await html5QrcodeScanner.stop(); } catch (e) {}
                }

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        position => sendAttendanceData(decodedText, position.coords.latitude, position.coords.longitude),
                        () => {
                            showToast('Gagal mendapatkan lokasi. Pastikan GPS aktif dan izin diberikan.', false);
                            scannerModal.hide();
                        },
                        { enableHighAccuracy: true }
                    );
                } else {
                    showToast('Browser Anda tidak mendukung Geolocation.', false);
                    scannerModal.hide();
                }
            };

            const startScanner = () => {
                scannerLoading.style.display = 'none';
                if (!html5QrcodeScanner) {
                    html5QrcodeScanner = new Html5Qrcode('qr-reader');
                }

                html5QrcodeScanner.start(
                    { facingMode: 'environment' },
                    { fps: 10, qrbox: { width: 250, height: 250 } },
                    onScanSuccess,
                    () => {}
                ).catch((err) => {
                    let message = 'Gagal memulai kamera. Coba muat ulang halaman.';
                    if (err.name === 'NotAllowedError') {
                        message = 'Izin kamera ditolak. Harap aktifkan di pengaturan browser.';
                    } else if (err.name === 'NotFoundError') {
                        message = 'Tidak ada kamera yang ditemukan di perangkat ini.';
                    }
                    showToast(message, false);
                    scannerModal.hide();
                });
            };

            const stopScanner = () => {
                if (html5QrcodeScanner && html5QrcodeScanner.isScanning) {
                    html5QrcodeScanner.stop().catch(err => console.error(err));
                }
            };

            scanButton.addEventListener('click', () => scannerModal.show());
            scannerModalElement.addEventListener('shown.bs.modal', startScanner);
            scannerModalElement.addEventListener('hidden.bs.modal', stopScanner);

            async function getFreshCsrfToken() {
                const response = await fetch('{{ route('api.csrf-token') }}', {
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json' },
                    cache: 'no-store',
                });
                if (!response.ok) throw new Error('CSRF fetch failed');
                const data = await response.json();
                return data.csrf_token;
            }

            async function sendAttendanceData(qrCode, latitude, longitude) {
                try {
                    const freshToken = await getFreshCsrfToken();
                    const response = await fetch('{{ route('attendance.store') }}', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': freshToken
                        },
                        body: JSON.stringify({
                            qr_code_result: qrCode,
                            latitude: latitude,
                            longitude: longitude,
                            device_token: localStorage.getItem('device_token') || ''
                        })
                    });

                    if (response.status === 419) {
                        showToast('Sesi Anda telah berakhir. Halaman akan dimuat ulang.', false);
                        setTimeout(() => window.location.reload(), 2000);
                        return;
                    }

                    const data = await response.json();
                    showToast(data.message, data.success);
                    scannerModal.hide();

                    if (data.success) {
                        setTimeout(() => window.location.reload(), 2000);
                    } else {
                        scannerLoading.style.display = 'none';
                    }
                } catch (error) {
                    console.error(error);
                    showToast('Terjadi kesalahan saat mengirim data ke server.', false);
                    scannerModal.hide();
                    scannerLoading.style.display = 'none';
                }
            }
        });
    </script>
@endpush
