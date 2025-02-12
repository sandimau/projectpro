@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Pembayaran {{ ucfirst($hutang->jenis) }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="30%">Kontak</td>
                                        <td>: {{ $hutang->kontak->nama }}</td>
                                    </tr>
                                    <tr>
                                        <td>Tanggal</td>
                                        <td>: {{ $hutang->tanggal->format('d/m/Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Total {{ ucfirst($hutang->jenis) }}</td>
                                        <td>: Rp {{ number_format($hutang->jumlah, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Total Bayar</td>
                                        <td>: Rp {{ number_format($hutang->total_bayar, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Sisa</td>
                                        <td>: Rp {{ number_format($hutang->sisa, 0, ',', '.') }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-8">
                                @if ($hutang->sisa > 0)
                                    <form action="{{ route('hutang.bayarStore') }}" method="POST" class="mt-4">
                                        @csrf
                                        <input type="hidden" name="hutang_id" value="{{ $hutang->id }}">
                                        <input type="hidden" name="jenis" value="{{ $hutang->jenis }}">


                                        <div class="form-group mb-3">
                                            @if ($hutang->jenis == 'hutang')
                                                <label for="akun_detail_id" class="mb-2">Keluar dari Kas</label>
                                            @else
                                                <label for="akun_detail_id" class="mb-2">Masuk ke Kas</label>
                                            @endif
                                            <select name="akun_detail_id" id="akun_detail_id"
                                                class="form-control @error('akun_detail_id') is-invalid @enderror" required>
                                                <option value="">Pilih kas</option>
                                                @foreach ($kas as $k)
                                                    <option value="{{ $k->id }}">{{ $k->nama }}</option>
                                                @endforeach
                                            </select>
                                            @error('akun_detail_id')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="tanggal" class="mb-2">Tanggal</label>
                                            <input type="date" name="tanggal" id="tanggal"
                                                class="form-control @error('tanggal') is-invalid @enderror"
                                                value="{{ old('tanggal', date('Y-m-d')) }}" required>
                                            @error('tanggal')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="jumlah" class="mb-2">Jumlah</label>
                                            <input type="number" name="jumlah" id="jumlah"
                                                class="form-control @error('jumlah') is-invalid @enderror"
                                                value="{{ old('jumlah') }}" max="{{ $hutang->sisa }}" required>
                                            @error('jumlah')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="keterangan" class="mb-2">Keterangan</label>
                                            <textarea name="keterangan" id="keterangan" rows="3"
                                                class="form-control @error('keterangan') is-invalid @enderror">{{ old('keterangan') }}</textarea>
                                            @error('keterangan')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary">Simpan Pembayaran</button>
                                            <a href="{{ route('hutang.index') }}" class="btn btn-secondary">Kembali</a>
                                        </div>
                                    </form>
                                @else
                                    <div class="mt-4">
                                        <a href="{{ route('hutang.index') }}" class="btn btn-secondary">Kembali</a>
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-12 pt-5">
                                <h4>History Pembayaran</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Tanggal</th>
                                                <th>Jumlah</th>
                                                <th>Kas</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($hutang->details as $detail)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $detail->tanggal->format('d/m/Y') }}</td>
                                                    <td>Rp {{ number_format($detail->jumlah, 0, ',', '.') }}</td>
                                                    <td>{{ $detail->akun_detail->nama }}</td>
                                                    <td>{{ $detail->keterangan }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">Belum ada pembayaran</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
