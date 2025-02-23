@extends('layouts.app')

@section('title')
    Marketplace Analisa
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <form action="{{ route('marketplaces.analisa') }}" method="get" class="d-flex gap-2 align-items-center">
                        <label for="bulan" class="form-label mb-0">Bulan</label>
                        <select name="bulan" id="bulan" class="form-control">
                            @foreach ($bulan as $key => $value)
                                <option value="{{ $key }}" {{ $key == (request('bulan') ?? date('Y-m')) ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="mt-2">
                    @include('layouts.includes.messages')
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">nama</th>
                                <th scope="col">omzet total</th>
                                <th scope="col">sudah dibayar</th>
                                <th scope="col">hpp</th>
                                <th scope="col">potongan</th>
                                <th scope="col">biaya iklan</th>
                                <th scope="col">total biaya</th>
                                <th scope="col">keuntungan</th>
                                <th scope="col">margin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($marketplaces as $marketplace)
                                <tr>
                                    <td>{{ $marketplace->nama }}</td>
                                    <td>{{ number_format($omzet[$marketplace->kontak->id] ?? 0, 0, ',', '.') }}</td>
                                    <td>{{ number_format($bayar[$marketplace->kontak->id] ?? 0, 0, ',', '.') }}</td>
                                    <td>{{ number_format($hpp[$marketplace->kontak->id] ?? 0, 0, ',', '.') }}</td>
                                    @php
                                        $potongan = ($total[$marketplace->kontak->id] ?? 0) - ($bayar[$marketplace->kontak->id] ?? 0);
                                        $totalBiaya = ($potongan + ($iklan[$marketplace->kontak->id] ?? 0));
                                        $keuntungan = ($bayar[$marketplace->kontak->id] ?? 0) - ($hpp[$marketplace->kontak->id] ?? 0) - ($totalBiaya);
                                    @endphp
                                    <td>{{ number_format($potongan, 0, ',', '.') }}</td>
                                    <td>{{ number_format($iklan[$marketplace->kontak->id] ?? 0, 0, ',', '.') }}</td>
                                    <td>{{ number_format($totalBiaya, 0, ',', '.') }}</td>
                                    <td>{{ number_format($keuntungan, 0, ',', '.') }}</td>
                                    <td>{{ number_format($keuntungan / ($bayar[$marketplace->kontak->id] ?? 0) * 100, 2, ',', '.') }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
