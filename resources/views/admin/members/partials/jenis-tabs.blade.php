@php
    $jenisAktif = $jenis ?? 'karyawan';
@endphp

<ul class="nav nav-tabs mb-3" role="tablist">
    @can('member_access')
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $jenisAktif === 'karyawan' ? 'active' : '' }}"
                href="{{ route('members.index') }}">Karyawan</a>
        </li>
    @endcan
    @can('freelance_access')
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $jenisAktif === 'freelance' ? 'active' : '' }}"
                href="{{ route('members.freelance') }}">Freelance</a>
        </li>
    @endcan
    @canany(['member_access', 'freelance_access'])
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $jenisAktif === 'nonaktif' ? 'active' : '' }}"
                href="{{ route('members.nonaktif') }}">Nonaktif</a>
        </li>
    @endcanany
</ul>
