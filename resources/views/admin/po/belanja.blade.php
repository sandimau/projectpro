@extends('layouts.app')

@section('title')
    Create Produk Belanja
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title">Po > Detail</h5>
                </div>
                <a href="{{ route('po.show', $po->id) }}" class="btn btn-success ">back</a>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('po.belanja.store', $po->id) }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <div class="input-group mb-3"><span class="input-group-text" id="basic-addon3">no nota</span>
                                <input class="form-control" id="basic-url" type="text" name="nota"
                                    aria-describedby="basic-addon3">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <input class="form-control {{ $errors->has('tanggal_beli') ? 'is-invalid' : '' }}"
                                type="date" name="tanggal_beli" id="tanggal_beli" value="<?php echo date('Y-m-d'); ?>">
                            @if ($errors->has('tanggal_beli'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('tanggal_beli') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="table-responsive m-t-15">
                        <table class="table" id="table-barang">
                            <thead>
                                <tr style="background-color: #e9ecef">
                                    <th style="width:300px">Barang</th>
                                    <th>keterangan</th>
                                    <th>jumlah</th>
                                    <th>harga</th>
                                    <th>subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="table_body">
                                @foreach ($po->poDetail as $item)
                                    <tr>
                                        <td>
                                            <input id="produk{{ $loop->index }}"class="form-control" type="text"
                                                value="{{ $item->produk->namaLengkap }}" disabled />
                                            <input type="hidden" name="produk[]" value="{{ $item->produk_id }}">
                                            <input type="hidden" name="poDetail[]" value="{{ $item->id }}">
                                        </td>
                                        <td>
                                            <input id="ket{{ $loop->index }}" name="keterangan[]" class="form-control"
                                                type="text" value="{{ old('keterangan.' . $loop->index) }}"
                                                {{ ($item->jumlah - $item->jumlahKedatangan) <= 0 ? 'disabled' : '' }} />
                                        </td>
                                        <td>
                                            <input id="jumlah{{ $loop->index }}" name="jumlah[]" step=".01"
                                                class="form-control" type="number"
                                                value="{{ $item->jumlah - $item->jumlahKedatangan }}"
                                                onchange="calculateSubTotal({{ $loop->index }})"
                                                {{ ($item->jumlah - $item->jumlahKedatangan) <= 0 ? 'disabled' : '' }} />
                                        </td>
                                        <td>
                                            <input id="harga{{ $loop->index }}" name="harga[]" class="form-control"
                                                step=".01" type="number" value="{{ old('harga.' . $loop->index) }}"
                                                onchange="calculateSubTotal({{ $loop->index }})"
                                                {{ ($item->jumlah - $item->jumlahKedatangan) <= 0 ? 'disabled' : '' }} />
                                        </td>
                                        <td>
                                            <input id="subtotal{{ $loop->index }}" disabled
                                                class="form-control text-right" type="number" />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <hr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right">
                                        <span>diskon</span><br>
                                    </td>
                                    <td class="text-right">
                                        <input onchange="updateTotal()" id="diskon" name="diskon"
                                            class="form-control text-right" type="number" />
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right">
                                        <b><span>Total</span></b> <br>
                                    </td>
                                    <td class="text-right">
                                        <input id="total" name="total" class="form-control text-right" type="number"
                                            readonly />
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right">
                                        <b><span>Deposit</span></b> <br>
                                        <small class="text-muted">Maksimal sesuai total belanja</small>
                                    </td>
                                    <td class="text-right">
                                        <input id="deposit" name="deposit" onchange="updatePembayaran()"
                                            class="form-control text-right {{ $errors->has('deposit') ? 'is-invalid' : '' }}"
                                            type="number" value="{{ $deposit }}" max="{{ $deposit }}" />
                                        @if ($errors->has('deposit'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('deposit') }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right">
                                        <b><span>Pembayaran</span></b> <br>
                                    </td>
                                    <td class="text-right">
                                        <input id="pembayaran" name="pembayaran" onchange="updatePembayaran()"
                                            class="form-control text-right {{ $errors->has('pembayaran') ? 'is-invalid' : '' }}"
                                            type="number" value="0" />
                                        @if ($errors->has('pembayaran'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('pembayaran') }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right">
                                        <b><span>kas</span></b> <br>
                                    </td>
                                    <td>
                                        <div class="form-group mb-3" id="kasContainer" style="display:none;">
                                            <select
                                                class="form-select {{ $errors->has('akun_detail_id') ? 'is-invalid' : '' }}"
                                                name="akun_detail_id" id="akun_detail_id">
                                                @foreach ($kas as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ old('akun_detail_id') == $item->id ? 'selected' : '' }}>
                                                        {{ $item->nama }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('akun_detail_id'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('akun_detail_id') }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
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
    <style>
        .form-control:disabled {
            background-color: #f8f9fa;
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
    <script>
        let pembayaranDiinputManual = false;

        // Fungsi untuk menghitung subtotal per baris (PASTIKAN DI LUAR DOMContentLoaded)
        function calculateSubTotal(rowIndex) {
            const jumlahInput = document.getElementById(`jumlah${rowIndex}`);
            const hargaInput = document.getElementById(`harga${rowIndex}`);
            const subtotalInput = document.getElementById(`subtotal${rowIndex}`);

            // Pastikan semua elemen ada
            if (!jumlahInput || !hargaInput || !subtotalInput) {
                console.log(`Element tidak ditemukan untuk row ${rowIndex}`);
                return;
            }

            // Jika input dinonaktifkan, set subtotal ke 0
            if (jumlahInput.disabled || hargaInput.disabled) {
                subtotalInput.value = 0;
                console.log(`Row ${rowIndex}: Input disabled, subtotal = 0`);
                updateTotal();
                return;
            }

            const jumlah = parseFloat(jumlahInput.value) || 0;
            const harga = parseFloat(hargaInput.value) || 0;
            const subtotal = jumlah * harga;

            subtotalInput.value = Math.round(subtotal);
            console.log(`Row ${rowIndex}: Jumlah=${jumlah}, Harga=${harga}, Subtotal=${Math.round(subtotal)}`);
            updateTotal();
        }

        // Fungsi untuk menghitung total keseluruhan
        function updateTotal() {
            let totalSum = 0;
            const subtotalInputs = document.querySelectorAll('[id^="subtotal"]');

            subtotalInputs.forEach((input, index) => {
                const value = parseFloat(input.value) || 0;
                totalSum += value;
                console.log(`Subtotal ${index}: ${value}`);
            });

            const diskon = parseFloat(document.getElementById('diskon').value) || 0;
            const total = totalSum - diskon;

            const totalInput = document.getElementById('total');
            if (totalInput) {
                totalInput.value = Math.round(total);
            }

            console.log(`Total Sum: ${totalSum}, Diskon: ${diskon}, Total: ${Math.round(total)}`);

            // Tambahkan ini agar pembayaran ikut terupdate
            updatePembayaran();
        }

        // Fungsi untuk update pembayaran (jika diperlukan)
        function updatePembayaran() {
            const total = parseFloat(document.getElementById('total').value) || 0;
            const depositInput = document.getElementById('deposit');
            const deposit = parseFloat(depositInput.value) || 0;
            const pembayaranInput = document.getElementById('pembayaran');
            const pembayaranSaran = total - deposit;
            const pembayaranLama = parseFloat(pembayaranInput.value) || 0;

            // Hanya update otomatis jika user belum input manual
            if (!pembayaranDiinputManual) {
                pembayaranInput.value = pembayaranSaran > 0 ? Math.round(pembayaranSaran) : 0;
            }
            pembayaranInput.disabled = false;

            // Tampilkan kas jika pembayaran > 0
            const kasContainer = document.getElementById('kasContainer');
            if (parseFloat(pembayaranInput.value) > 0) {
                kasContainer.style.display = 'block';
            } else {
                kasContainer.style.display = 'none';
            }
        }

        // Fungsi updateSubTotal yang sudah ada (untuk kompatibilitas)
        function updateSubTotal() {
            updateTotal();
        }

        // Event listeners untuk setiap baris
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM Content Loaded - Starting initialization');
            // Tambahkan event listener untuk setiap input jumlah dan harga
            const rows = document.querySelectorAll('#table_body tr');
            console.log(`Found ${rows.length} rows in table`);

            // Set nilai awal untuk input yang dinonaktifkan terlebih dahulu
            rows.forEach((row, index) => {
                const jumlahInput = document.getElementById(`jumlah${index}`);
                const hargaInput = document.getElementById(`harga${index}`);
                const subtotalInput = document.getElementById(`subtotal${index}`);

                if (jumlahInput && jumlahInput.disabled) {
                    jumlahInput.value = 0;
                }
                if (hargaInput && hargaInput.disabled) {
                    hargaInput.value = 0;
                }
                if (subtotalInput && (jumlahInput.disabled || hargaInput.disabled)) {
                    subtotalInput.value = 0;
                }
            });

            // Tambahkan event listener untuk semua input (enabled dan disabled)
            rows.forEach((row, index) => {
                const jumlahInput = document.getElementById(`jumlah${index}`);
                const hargaInput = document.getElementById(`harga${index}`);

                console.log(`Row ${index}: jumlahInput=${!!jumlahInput}, hargaInput=${!!hargaInput}`);

                if (jumlahInput) {
                    jumlahInput.addEventListener('input', () => {
                        console.log(`Jumlah input changed for row ${index}`);
                        calculateSubTotal(index);
                    });
                    jumlahInput.addEventListener('change', () => {
                        console.log(`Jumlah change event for row ${index}`);
                        calculateSubTotal(index);
                    });
                }

                if (hargaInput) {
                    hargaInput.addEventListener('input', () => {
                        console.log(`Harga input changed for row ${index}`);
                        calculateSubTotal(index);
                    });
                    hargaInput.addEventListener('change', () => {
                        console.log(`Harga change event for row ${index}`);
                        calculateSubTotal(index);
                    });
                }
            });

            // Event listener untuk diskon
            const diskonInput = document.getElementById('diskon');
            if (diskonInput) {
                diskonInput.addEventListener('input', updateTotal);
                diskonInput.addEventListener('change', updateTotal);
            }

            // Event listener untuk pembayaran
            const pembayaranInput = document.getElementById('pembayaran');
            if (pembayaranInput) {
                pembayaranInput.addEventListener('input', function() {
                    pembayaranDiinputManual = true;
                    // Tampilkan kas jika pembayaran > 0
                    const kasContainer = document.getElementById('kasContainer');
                    if (parseFloat(pembayaranInput.value) > 0) {
                        kasContainer.style.display = 'block';
                    } else {
                        kasContainer.style.display = 'none';
                    }
                });
            }

            // Event listener untuk deposit
            // HANYA deklarasi depositInput SEKALI di scope ini
            const depositInput = document.getElementById('deposit');
            if (depositInput) {
                depositInput.addEventListener('input', function() {
                    updatePembayaran();
                });

                // Set max deposit berdasarkan total yang tersedia
                const totalInput = document.getElementById('total');
                if (totalInput) {
                    totalInput.addEventListener('input', function() {
                        const total = parseFloat(totalInput.value) || 0;
                        const availableDeposit = parseFloat(depositInput.getAttribute('max')) || 0;
                        depositInput.max = Math.min(total, availableDeposit);
                    });
                }
            }

            // Hitung subtotal awal untuk setiap baris
            console.log('Calculating initial subtotals...');
            rows.forEach((row, index) => {
                console.log(`Calculating initial subtotal for row ${index}`);
                calculateSubTotal(index);
            });

            // Set max deposit awal
            const totalInput = document.getElementById('total');
            // depositInput sudah dideklarasikan di atas
            if (totalInput && depositInput) {
                const total = parseFloat(totalInput.value) || 0;
                const availableDeposit = parseFloat(depositInput.getAttribute('max')) || 0;
                if (total > 0) {
                    depositInput.max = Math.min(total, availableDeposit);
                } else {
                    depositInput.max = availableDeposit;
                }
            }
        });
    </script>
@endpush
