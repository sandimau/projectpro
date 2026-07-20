@extends('layouts.app')

@section('title')
    Data Member
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Members</h5>
                    </div>
                    @can('member_create')
                        <a href="{{ route('members.create') }}" class="popup btn btn-primary"><i class='bx bx-plus-circle'></i> Add</a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                @include('layouts.includes.messages')

                @include('admin.members.partials.jenis-tabs', ['jenis' => 'karyawan'])

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>nama lengkap</th>
                                <th>cuti</th>
                                <th>ijin</th>
                                <th>kasbon</th>
                                <th>lembur</th>
                                <th>tunjangan</th>
                                <th>umur</th>
                                <th>lama kerja</th>
                                <th>tanggal gajian</th>
                                <th>wfh</th>
                                <th>whattodo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($members as $member)
                                <tr data-entry-id="{{ $member->id }}">
                                    <td>
                                        <a class="popup"
                                            href="{{ route('members.show', $member->id) }}">{{ $member->nama_lengkap ?? '' }}</a>
                                    </td>
                                    <td>
                                        @can('cuti_access')
                                            <a class="popup" href="{{ route('members.cuti', $member->id) }}">{{ $member->countCuti }}</a>
                                        @elsecan('member_access')
                                            {{ $member->countCuti }}
                                        @endcan
                                    </td>
                                    <td>
                                        @can('cuti_access')
                                            <a class="popup" href="{{ route('members.ijin', $member->id) }}">{{ $member->countIjin }}</a>
                                        @elsecan('member_access')
                                            {{ $member->countIjin }}
                                        @endcan
                                    </td>
                                    <td>
                                        @can('kasbon_access')
                                            <a class="popup"
                                                href="{{ route('members.kasbon', $member->id) }}">{{ number_format($member->countKasbon) }}</a>
                                        @elsecan('member_access')
                                            {{ number_format($member->countKasbon) }}
                                        @endcan
                                    </td>
                                    <td>
                                        @can('lembur_access')
                                            <a class="popup" href="{{ route('members.lembur', $member->id) }}">{{ $member->countLembur }}</a>
                                        @elsecan('member_access')
                                            {{ $member->countLembur }}
                                        @endcan
                                    </td>
                                    <td>
                                        @can('tunjangan_access')
                                            <a class="popup"
                                                href="{{ route('members.tunjangan', $member->id) }}">{{ number_format($member->countTunjangan) }}</a>
                                        @elsecan('member_access')
                                            {{ number_format($member->countTunjangan) }}
                                        @endcan
                                    </td>
                                    <td>
                                        {{ $member->umur ?? '' }}
                                    </td>
                                    <td>
                                        {{ $member->lamaKerja ?? '' }}
                                    </td>
                                    <td>
                                        @can('penggajian_access')
                                            <a class="popup"
                                                href="{{ route('members.penggajian', $member->id) }}">{{ $member->tgl_gajian }}</a>
                                        @elsecan('member_access')
                                            {{ $member->tgl_gajian }}
                                        @endcan
                                    </td>
                                    <td>
                                        @if(($member->tipe_kerja ?? 'wfo') === 'wfh')
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <span class="badge bg-info">WFH</span>
                                                @if(isset($absenWfhHariIni[$member->id]))
                                                    <button type="button" class="btn btn-secondary btn-sm py-0 px-2" style="font-size: .75rem" disabled>
                                                        <i class='bx bx-check'></i> Sudah absen
                                                    </button>
                                                @else
                                                    <a class="popup btn btn-success btn-sm py-0 px-2 text-white" style="font-size: .75rem"
                                                        href="{{ route('members.absenWfh', $member->id) }}"><i class='bx bx-calendar-check'></i> Absen</a>
                                                @endif
                                            </div>
                                        @else
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <span class="badge bg-secondary">WFO</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td><a href="{{ route('whattodo.create', ['member_id' => $member->id]) }}"
                                            class="popup btn btn-info btn-sm me-1 text-white"><i class='bx bxs-add-to-queue'></i>
                                            add</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('admin.members.partials.detail-member-modal')
@endsection

@push('after-scripts')
    <script>
        @include('admin.members.partials.detail-member-modal-js')
    </script>
    <style>
        @include('admin.members.partials.detail-member-modal-styles')
    </style>
@endpush
