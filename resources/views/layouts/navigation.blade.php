<ul class="sidebar-nav" data-coreui="navigation" data-simplebar>
    <li class="nav-group" aria-expanded="false">
        <a class="nav-link nav-group-toggle" href="#">
            <svg class="nav-icon">
                <use xlink:href="{{ asset('icons/coreui.svg#cil-3d') }}"></use>
            </svg>
            Produksi
        </a>
        <ul class="nav-group-items" style="height: 0px;">
            @can('order_access')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('order*') ? 'active' : '' }}" href="{{ route('order.dashboard') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-3d') }}"></use>
                        </svg>
                        Proses
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('order*') ? 'active' : '' }}" href="{{ route('order.index') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-3d') }}"></use>
                        </svg>
                        Arsip Order
                    </a>
                </li>
            @endcan
        </ul>
    </li>

    <li class="nav-group" aria-expanded="false">
        <a class="nav-link nav-group-toggle" href="#">
            <svg class="nav-icon">
                <use xlink:href="{{ asset('icons/coreui.svg#cil-storage') }}"></use>
            </svg>
            Data
        </a>
        <ul class="nav-group-items" style="height: 0px;">
            @can('produk_access')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('kategori*') ? 'active' : '' }}"
                        href="{{ route('kategori.index') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-basket') }}"></use>
                        </svg>
                        {{ __('Produk') }}
                    </a>
                </li>
            @endcan
            @can('kontak_access')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('kontaks*') ? 'active' : '' }}"
                        href="{{ route('kontaks.index') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-user') }}"></use>
                        </svg>
                        {{ __('Kontak') }}
                    </a>
                </li>
            @endcan
        </ul>
    </li>

    <li class="nav-group" aria-expanded="false">
        <a class="nav-link nav-group-toggle" href="#">
            <svg class="nav-icon">
                <use xlink:href="{{ asset('icons/coreui.svg#cil-cash') }}"></use>
            </svg>
            Keuangan
        </a>
        <ul class="nav-group-items" style="height: 0px;">
            @role('super')
                @can('akun_access')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('akuns*') ? 'active' : '' }}" href="{{ route('akuns.index') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-cash') }}"></use>
                            </svg>
                            {{ __('akuns') }}
                        </a>
                    </li>
                @endcan
                @can('akun_kategori_access')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('akunKategoris*') ? 'active' : '' }}"
                            href="{{ route('akunKategoris.index') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-cash') }}"></use>
                            </svg>
                            {{ __('akun kategoris') }}
                        </a>
                    </li>
                @endcan
            @endrole
            @can('akun_detail_access')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('akunDetails*') ? 'active' : '' }}"
                        href="{{ route('akunDetails.index') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-cash') }}"></use>
                        </svg>
                        {{ __('kas') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('akunDetails*') ? 'active' : '' }}"
                        href="{{ route('order.unpaid') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-cash') }}"></use>
                        </svg>
                        {{ __('belum lunas') }}
                    </a>
                </li>
            @endcan
            <li class="nav-item">
                <a class="nav-link {{ request()->is('belanjas*') ? 'active' : '' }}"
                    href="{{ route('belanja.index') }}">
                    <svg class="nav-icon">
                        <use xlink:href="{{ asset('icons/coreui.svg#cil-cash') }}"></use>
                    </svg>
                    Belanja
                </a>
            </li>
        </ul>
    </li>

    <li class="nav-group" aria-expanded="false">
        <a class="nav-link nav-group-toggle" href="#">
            <svg class="nav-icon">
                <use xlink:href="{{ asset('icons/coreui.svg#cil-cash') }}"></use>
            </svg>
            Marketplace
        </a>
        <ul class="nav-group-items" style="height: 0px;">
            @can('marketplace_access')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('marketplaces*') ? 'active' : '' }}"
                        href="{{ route('marketplaces.index') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-cash') }}"></use>
                        </svg>
                        {{ __('Config') }}
                    </a>
                </li>
            @endcan
        </ul>
    </li>

    <li class="nav-group" aria-expanded="false">
        <a class="nav-link nav-group-toggle" href="#">
            <svg class="nav-icon">
                <use xlink:href="{{ asset('icons/coreui.svg#cil-user') }}"></use>
            </svg>
            Pegawai
        </a>
        <ul class="nav-group-items" style="height: 0px;">
            @can('member_access')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('members*') ? 'active' : '' }}"
                        href="{{ route('members.index') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-user') }}"></use>
                        </svg>
                        {{ __('aktif') }}
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('member*') ? 'active' : '' }}"
                        href="{{ route('members.nonaktif') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-user') }}"></use>
                        </svg>
                        {{ __('non aktif') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('ars*') ? 'active' : '' }}" href="{{ route('ars.index') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-user') }}"></use>
                        </svg>
                        {{ __('cs') }}
                    </a>
                </li>
            @endcan
        </ul>
    </li>

    <li class="nav-group" aria-expanded="false">
        <a class="nav-link nav-group-toggle" href="#">
            <svg class="nav-icon">
                <use xlink:href="{{ asset('icons/coreui.svg#cil-user') }}"></use>
            </svg>
            Omzet
        </a>
        <ul class="nav-group-items" style="height: 0px;">
            @can('omzet_access')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('order*') ? 'active' : '' }}"
                        href="{{ route('order.omzet') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-user') }}"></use>
                        </svg>
                        {{ __('Tahunan') }}
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('order*') ? 'active' : '' }}"
                        href="{{ route('order.omzetBulan') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-user') }}"></use>
                        </svg>
                        {{ __('Bulanan') }}
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('produk*') ? 'active' : '' }}"
                        href="{{ route('produk.aset') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-user') }}"></use>
                        </svg>
                        {{ __('Aset') }}
                    </a>
                </li>
            @endcan
        </ul>
    </li>

    <li class="nav-group" aria-expanded="false">
        <a class="nav-link nav-group-toggle" href="#">
            <svg class="nav-icon">
                <use xlink:href="{{ asset('icons/coreui.svg#cil-cog') }}"></use>
            </svg>
            User Management
        </a>
        <ul class="nav-group-items" style="height: 0px;">

            @can('user_access')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('users*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-user') }}"></use>
                        </svg>
                        {{ __('Users') }}
                    </a>
                </li>
            @endcan

            @can('level_access')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('levels*') ? 'active' : '' }}"
                        href="{{ route('level.index') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-user') }}"></use>
                        </svg>
                        {{ __('Levels') }}
                    </a>
                </li>
            @endcan

            @can('bagian_access')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('bagians*') ? 'active' : '' }}"
                        href="{{ route('bagian.index') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-user') }}"></use>
                        </svg>
                        {{ __('Bagians') }}
                    </a>
                </li>
            @endcan
        </ul>
    </li>
    <li class="nav-group" aria-expanded="false">
        <a class="nav-link nav-group-toggle" href="#">
            <svg class="nav-icon">
                <use xlink:href="{{ asset('icons/coreui.svg#cil-cog') }}"></use>
            </svg>
            Config
        </a>
        <ul class="nav-group-items" style="height: 0px;">
            @role('super')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('roles*') ? 'active' : '' }}" href="{{ route('roles.index') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-group') }}"></use>
                        </svg>
                        {{ __('Roles') }}
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('permissions*') ? 'active' : '' }}"
                        href="{{ route('permissions.index') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-room') }}"></use>
                        </svg>
                        {{ __('Permissions') }}
                    </a>
                </li>
            @endrole

            <li class="nav-item">
                <a class="nav-link {{ request()->is('produksis*') ? 'active' : '' }}"
                    href="{{ route('produksis.index') }}">
                    <svg class="nav-icon">
                        <use xlink:href="{{ asset('icons/coreui.svg#cil-room') }}"></use>
                    </svg>
                    {{ __('Setup Produksi') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->is('speks*') ? 'active' : '' }}" href="{{ route('speks.index') }}">
                    <svg class="nav-icon">
                        <use xlink:href="{{ asset('icons/coreui.svg#cil-room') }}"></use>
                    </svg>
                    {{ __('Spek Produk') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->is('sistems*') ? 'active' : '' }}"
                    href="{{ route('sistem.index') }}">
                    <svg class="nav-icon">
                        <use xlink:href="{{ asset('icons/coreui.svg#cil-room') }}"></use>
                    </svg>
                    {{ __('Sistem') }}
                </a>
            </li>
        </ul>
    </li>
</ul>
