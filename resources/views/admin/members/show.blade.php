@extends('layouts.app')

@section('title')
    Detail Member
@endsection

@section('content')
    <ul class="travel-tab nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="information-tab" data-bs-toggle="tab" data-bs-target="#information"
                type="button" role="tab" aria-controls="information" aria-selected="true">Detail</button>
        </li>
    </ul>

    @include('layouts.includes.messages')

    <div class="tab-content" id="myTabContent">
        <!-- start information -->
        <div class="tab-pane fade show active" id="information" role="tabpanel" aria-labelledby="information-tab">
            <div class="tab-content">
                <div class="card mt-4">
                    <div class="card-header">
                        @if ($member->status == 0)
                            <a class="btn btn-primary" href="{{ route('members.nonaktif') }}" data-modal-skip>
                                <i class='bx bx-arrow-back'></i> back
                            </a>
                        @else
                            <a class="btn btn-primary" href="{{ route('members.index') }}" data-modal-skip>
                                <i class='bx bx-arrow-back'></i> back
                            </a>
                        @endif
                        @can('member_edit')
                            <a class="popup btn btn-warning" href="{{ route('members.edit', $member->id) }}">
                                <i class='bx bxs-edit'></i> edit
                            </a>
                        @endcan
                        @if ($member->user_id && auth()->user()->can('member_edit'))
                            <form action="{{ route('members.reset-device', $member) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Reset perangkat absensi untuk {{ $member->nama_lengkap }}? Karyawan harus login ulang di HP baru.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class='bx bx-mobile'></i> Reset Perangkat Absensi
                                </button>
                            </form>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <tbody>
                                    <tr>
                                        <th>Nama Lengkap</th>
                                        <td>{{ $member->nama_lengkap }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Masuk</th>
                                        <td>{{ $member->tgl_masuk }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Keluar</th>
                                        <td>{{ $member->tgl_keluar }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Lahir</th>
                                        <td>{{ $member->tgl_lahir }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tempat Lahir</th>
                                        <td>{{ $member->tempat_lahir }}</td>
                                    </tr>
                                    <tr>
                                        <th>Alamat</th>
                                        <td>{{ $member->alamat }}</td>
                                    </tr>
                                    <tr>
                                        <th>No Telp</th>
                                        <td>{{ $member->no_telp }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Gajian</th>
                                        <td>{{ date('d', strtotime($member->tgl_gajian)) }}</td>
                                    </tr>
                                    <tr>
                                        <th>No Rek</th>
                                        <td>{{ $member->no_rek }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            {{ $member->status == 1 ? 'aktif' : '' }}
                                            {{ $member->status == 0 ? 'non aktif' : '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>User Name</th>
                                        <td>{{ $member->user->name ?? '' }}</td>
                                    </tr>
                                    @if ($member->user_id)
                                        <tr>
                                            <th>Perangkat Absensi</th>
                                            <td>
                                                @if ($member->user?->userDevice)
                                                    <span class="badge bg-success">Terdaftar</span>
                                                    <small class="text-muted d-block mt-1">{{ \Illuminate\Support\Str::limit($member->user->userDevice->user_agent, 80) }}</small>
                                                @else
                                                    <span class="badge bg-secondary">Belum terdaftar</span>
                                                    <small class="text-muted d-block mt-1">Akan terdaftar saat login pertama kali di HP karyawan.</small>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end information -->

    </div>
@endsection
