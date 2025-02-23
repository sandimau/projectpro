@extends('layouts.app')

@section('title')
    Laba Kotor
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <form action="{{ route('laporan.labakotor') }}" method="get" class="d-flex gap-2 align-items-center">
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
                                <th>Kategori Utama</th>
                                <th>Kategori</th>
                                <th>Omzet</th>
                                <th>HPP</th>
                                <th>Opname</th>
                                <th>Laba Kotor</th>
                                <th>Persen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalOmzet = 0;
                                $totalHpp = 0;
                                $totalOpname = 0;
                                $totalLabaKotor = 0;
                                $currentKategoriUtama = '';
                            @endphp

                            @foreach($data as $item)
                                @if($currentKategoriUtama != $item->kategori_utama)
                                    @if(!$loop->first)
                                        <tr class="table-secondary">
                                            <td colspan="2"><strong>Sub Total {{ $currentKategoriUtama }}</strong></td>
                                            <td><strong>{{ number_format($subTotalOmzet, 0, ',', '.') }}</strong></td>
                                            <td><strong>{{ number_format($subTotalHpp, 0, ',', '.') }}</strong></td>
                                            <td><strong>{{ number_format($subTotalOpname, 0, ',', '.') }}</strong></td>
                                            <td><strong>{{ number_format($subTotalLabaKotor, 0, ',', '.') }}</strong></td>
                                            <td><strong>{{ $subTotalOmzet > 0 ? number_format(($subTotalLabaKotor/$subTotalOmzet)*100, 0, ',', '.') : 0 }}%</strong></td>
                                        </tr>
                                    @endif
                                    @php
                                        $currentKategoriUtama = $item->kategori_utama;
                                        $subTotalOmzet = 0;
                                        $subTotalHpp = 0;
                                        $subTotalOpname = 0;
                                        $subTotalLabaKotor = 0;
                                    @endphp
                                @endif

                                <tr>
                                    <td>{{ $item->kategori_utama }}</td>
                                    <td>
                                        <a href="{{ url('admin/labakotordetail') }}?bulan={{ request('bulan') ?? date('Y-m') }}&kategori={{ $item->kategori_id }}">
                                            {{ $item->kategori }}
                                        </a>
                                    </td>
                                    <td>{{ number_format($item->omzet, 0, ',', '.') }}</td>
                                    <td>{{ number_format($item->hpp, 0, ',', '.') }}</td>
                                    <td>{{ number_format($item->opname, 0, ',', '.') }}</td>
                                    <td>{{ number_format($item->laba_kotor, 0, ',', '.') }}</td>
                                    <td>{{ number_format($item->persen, 0, ',', '.') }}%</td>
                                </tr>

                                @php
                                    $totalOmzet += $item->omzet;
                                    $totalHpp += $item->hpp;
                                    $totalOpname += $item->opname;
                                    $totalLabaKotor += $item->laba_kotor;
                                    $subTotalOmzet += $item->omzet;
                                    $subTotalHpp += $item->hpp;
                                    $subTotalOpname += $item->opname;
                                    $subTotalLabaKotor += $item->laba_kotor;
                                @endphp

                                @if($loop->last)
                                    <tr class="table-secondary">
                                        <td colspan="2"><strong>Sub Total {{ $currentKategoriUtama }}</strong></td>
                                        <td><strong>{{ number_format($subTotalOmzet, 0, ',', '.') }}</strong></td>
                                        <td><strong>{{ number_format($subTotalHpp, 0, ',', '.') }}</strong></td>
                                        <td><strong>{{ number_format($subTotalOpname, 0, ',', '.') }}</strong></td>
                                        <td><strong>{{ number_format($subTotalLabaKotor, 0, ',', '.') }}</strong></td>
                                        <td><strong>{{ $subTotalOmzet > 0 ? number_format(($subTotalLabaKotor/$subTotalOmzet)*100, 0, ',', '.') : 0 }}%</strong></td>
                                    </tr>
                                @endif
                            @endforeach

                            <tr class="table-primary">
                                <td colspan="2"><strong>Total Keseluruhan</strong></td>
                                <td><strong>{{ number_format($totalOmzet, 0, ',', '.') }}</strong></td>
                                <td><strong>{{ number_format($totalHpp, 0, ',', '.') }}</strong></td>
                                <td><strong>{{ number_format($totalOpname, 0, ',', '.') }}</strong></td>
                                <td><strong>{{ number_format($totalLabaKotor, 0, ',', '.') }}</strong></td>
                                <td><strong>{{ $totalOmzet > 0 ? number_format(($totalLabaKotor/$totalOmzet)*100, 0, ',', '.') : 0 }}%</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
    <script>
        $(document).ready(function() {
            $('#bulan').on('change', function() {
                $(this).closest('form').submit();
            });
        });
    </script>
@endpush
