@extends('layouts.app')

@section('title')
    Marketplace Analisa
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Analisa Marketplace Tahun {{ date('Y') }}</h5>
            </div>
            <div class="card-body">
                <div class="mt-2">
                    @include('layouts.includes.messages')
                </div>
                @foreach ($marketplaces as $marketplace)
                    <div class="mb-4">
                        <h4>{{ $marketplace->nama }}</h4>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">Bulan</th>
                                        <th scope="col">Omzet Total</th>
                                        <th scope="col">Sudah Dibayar</th>
                                        <th scope="col">HPP</th>
                                        <th scope="col">Potongan</th>
                                        <th scope="col">Biaya Iklan</th>
                                        <th scope="col">Total Biaya</th>
                                        <th scope="col">Keuntungan</th>
                                        <th scope="col">Margin</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $bulan => $bulanData)
                                        <tr>
                                            <td>{{ $bulanData['nama'] }}</td>
                                            <td>
                                                @php
                                                    $omzetValue = $bulanData['omzet'][$marketplace->kontak->id] ?? 0;
                                                    $bayarValue = $bulanData['bayar'][$marketplace->kontak->id] ?? 0;
                                                    $bulanNumber = $bulanData['bulan'] ?? date('n');
                                                    $tahun = date('Y');
                                                    $tanggalAwal = $tahun . '-' . str_pad($bulanNumber, 2, '0', STR_PAD_LEFT) . '-01';
                                                    $tanggalAkhir = date('Y-m-t', strtotime($tanggalAwal));
                                                @endphp
                                                @if($omzetValue > 0)
                                                    <a href="{{ route('order.marketplace', [
                                                        'nota' => '',
                                                        'kontak_id' => $marketplace->kontak->id,
                                                        'produk_id' => '',
                                                        'dari' => $tanggalAwal,
                                                        'sampai' => $tanggalAkhir
                                                    ]) }}" class="text-decoration-none text-primary fw-bold" title="Lihat detail order {{ $bulanData['nama'] }} {{ $marketplace->nama }}">
                                                        {{ number_format($omzetValue, 0, ',', '.') }}
                                                    </a>
                                                @else
                                                    {{ number_format($omzetValue, 0, ',', '.') }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($bayarValue > 0)
                                                    <a href="{{ route('order.marketplace', [
                                                        'nota' => '',
                                                        'kontak_id' => $marketplace->kontak->id,
                                                        'pembayaran' => 1,
                                                        'produk_id' => '',
                                                        'dari' => $tanggalAwal,
                                                        'sampai' => $tanggalAkhir
                                                    ]) }}" class="text-decoration-none text-success fw-bold" title="Lihat detail order sudah dibayar {{ $bulanData['nama'] }} {{ $marketplace->nama }}">
                                                        {{ number_format($bayarValue, 0, ',', '.') }}
                                                    </a>
                                                @else
                                                    {{ number_format($bayarValue, 0, ',', '.') }}
                                                @endif
                                            </td>
                                            <td>{{ number_format($bulanData['hpp'][$marketplace->kontak->id] ?? 0, 0, ',', '.') }}</td>
                                            @php
                                                $potongan = ($bulanData['total'][$marketplace->kontak->id] ?? 0) - ($bulanData['bayar'][$marketplace->kontak->id] ?? 0);
                                                $totalBiaya = ($potongan + ($bulanData['iklan'][$marketplace->kontak->id] ?? 0));
                                                $keuntungan = ($bulanData['bayar'][$marketplace->kontak->id] ?? 0) - ($bulanData['hpp'][$marketplace->kontak->id] ?? 0) - ($totalBiaya);
                                            @endphp
                                            <td>{{ number_format($potongan, 0, ',', '.') }}</td>
                                            <td>{{ number_format($bulanData['iklan'][$marketplace->kontak->id] ?? 0, 0, ',', '.') }}</td>
                                            <td>{{ number_format($totalBiaya, 0, ',', '.') }}</td>
                                            <td>{{ number_format($keuntungan, 0, ',', '.') }}</td>
                                            <td>{{ ($bulanData['bayar'][$marketplace->kontak->id] ?? 0) > 0 ? number_format($keuntungan / ($bulanData['bayar'][$marketplace->kontak->id] ?? 0) * 100, 2, ',', '.') . '%' : '0%' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
