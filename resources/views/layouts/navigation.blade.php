<ul class="sidebar-nav" data-coreui="navigation" data-simplebar>
    <li class="nav-group" aria-expanded="false">
        <a class="nav-link nav-group-toggle" href="#">
            <svg class="nav-icon">
                <use xlink:href="{{ asset('icons/coreui.svg#cil-industry') }}"></use>
            </svg>
            Produksi
        </a>
        <ul class="nav-group-items" style="height: 0px;">
            @can('order_access')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('order*') ? 'active' : '' }}" href="{{ route('order.dashboard') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-task') }}"></use>
                        </svg>
                        Proses
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('order*') ? 'active' : '' }}" href="{{ route('order.index') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-folder-open') }}"></use>
                        </svg>
                        Arsip Offline
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('order*') ? 'active' : '' }}"
                        href="{{ route('order.marketplace') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-cloud-download') }}"></use>
                        </svg>
                        Arsip Online
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
                <use xlink:href="{{ asset('icons/coreui.svg#cil-dollar') }}"></use>
            </svg>
            Keuangan
        </a>
        <ul class="nav-group-items" style="height: 0px;">
            @role('super')
                @can('akun_access')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('akunKategoris*') ? 'active' : '' }}"
                            href="{{ route('akunDetails.index') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-calculator') }}"></use>
                            </svg>
                            {{ __('akuns') }}
                        </a>
                    </li>
                @endcan
            @endrole
            @can('akun_detail_access')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('akunDetails*') ? 'active' : '' }}"
                        href="{{ route('akunDetail.kas') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-wallet') }}"></use>
                        </svg>
                        {{ __('kas') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('akunDetails*') ? 'active' : '' }}"
                        href="{{ route('order.unpaid') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-credit-card') }}"></use>
                        </svg>
                        {{ __('belum lunas') }}
                    </a>
                </li>
            @endcan
            @can('keuangan')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('belanjas*') ? 'active' : '' }}"
                        href="{{ route('belanja.index') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-cart') }}"></use>
                        </svg>
                        Belanja
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('hutang*') ? 'active' : '' }}" href="{{ route('hutang.index') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-file') }}"></use>
                        </svg>
                        Hutang/Piutang
                    </a>
                </li>
            @endcan
        </ul>
    </li>

    <li class="nav-group" aria-expanded="false">
        <a class="nav-link nav-group-toggle" href="#">
            <svg class="nav-icon">
                <use xlink:href="{{ asset('icons/coreui.svg#cil-basket') }}"></use>
            </svg>
            Marketplace
        </a>
        <ul class="nav-group-items" style="height: 0px;">
            @can('marketplace_access')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('marketplaces*') ? 'active' : '' }}"
                        href="{{ route('marketplaces.index') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-settings') }}"></use>
                        </svg>
                        {{ __('Config') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('marketplaces*') ? 'active' : '' }}"
                        href="{{ route('marketplaces.analisa') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-chart-line') }}"></use>
                        </svg>
                        {{ __('Analisa') }}
                    </a>
                </li>
            @endcan
        </ul>
    </li>

    <li class="nav-group" aria-expanded="false">
        <a class="nav-link nav-group-toggle" href="#">
            <svg class="nav-icon">
                <use xlink:href="{{ asset('icons/coreui.svg#cil-inbox') }}"></use>
            </svg>
            Inventory
        </a>
        <ul class="nav-group-items" style="height: 0px;">
            @can('produk_access')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('produk-kategori-utama*') ? 'active' : '' }}"
                        href="{{ route('produk-kategori-utama.index') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-basket') }}"></use>
                        </svg>
                        {{ __('Produk') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('pemakaian*') ? 'active' : '' }}"
                        href="{{ route('pemakaian.index') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-basket') }}"></use>
                        </svg>
                        Pemakaian
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('opnames*') ? 'active' : '' }}"
                        href="{{ route('opnames.index') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-clipboard') }}"></use>
                        </svg>
                        {{ __('Opname') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('po*') ? 'active' : '' }}" href="{{ route('po.index') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-description') }}"></use>
                        </svg>
                        {{ __('PO') }}
                    </a>
                </li>
            @endcan
        </ul>
    </li>

    <li class="nav-group" aria-expanded="false">
        <a class="nav-link nav-group-toggle" href="#">
            <svg class="nav-icon">
                <use xlink:href="{{ asset('icons/coreui.svg#cil-inbox') }}"></use>
            </svg>
            Produksi
        </a>
        <ul class="nav-group-items" style="height: 0px;">
            @can('produk_access')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('produksi*') ? 'active' : '' }}"
                        href="{{ route('produksi.index') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-factory') }}"></use>
                        </svg>
                        Proses
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('produksi*') ? 'active' : '' }}"
                        href="{{ route('produkProduksi.index') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-factory') }}"></use>
                        </svg>
                        Produk
                    </a>
                </li>
            @endcan
        </ul>
    </li>

    <li class="nav-group" aria-expanded="false">
        <a class="nav-link nav-group-toggle" href="#">
            <svg class="nav-icon">
                <use xlink:href="{{ asset('icons/coreui.svg#cil-people') }}"></use>
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
                        Karyawan
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('members*') ? 'active' : '' }}"
                        href="{{ route('members.freelance') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-user-follow') }}"></use>
                        </svg>
                        Freelance
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('member*') ? 'active' : '' }}"
                        href="{{ route('members.nonaktif') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-user-unfollow') }}"></use>
                        </svg>
                        {{ __('non aktif') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('ars*') ? 'active' : '' }}" href="{{ route('ars.index') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-headphones') }}"></use>
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
                <use xlink:href="{{ asset('icons/coreui.svg#cil-chart') }}"></use>
            </svg>
            Analisa
        </a>
        <ul class="nav-group-items" style="height: 0px;">
            @can('laporan_access')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('laporan*') ? 'active' : '' }}"
                        href="{{ route('analisa.beban') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-chart-line') }}"></use>
                        </svg>
                        Analisa Beban
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('laporan*') ? 'active' : '' }}"
                        href="{{ route('analisa.operasional') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-bar-chart') }}"></use>
                        </svg>
                        Analisa Operasional
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('laporan*') ? 'active' : '' }}"
                        href="{{ route('analisa.stok') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-chart-pie') }}"></use>
                        </svg>
                        Analisa Stok
                    </a>
                </li>
            @endcan
        </ul>
    </li>

    <li class="nav-group" aria-expanded="false">
        <a class="nav-link nav-group-toggle" href="#">
            <svg class="nav-icon">
                <use xlink:href="{{ asset('icons/coreui.svg#cil-description') }}"></use>
            </svg>
            Laporan
        </a>
        <ul class="nav-group-items" style="height: 0px;">
            @can('laporan_access')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('laporan*') ? 'active' : '' }}"
                        href="{{ route('laporan.tunjangan') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-dollar') }}"></use>
                        </svg>
                        {{ __('Tunjangan') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('laporan*') ? 'active' : '' }}"
                        href="{{ route('laporan.penggajian') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-wallet') }}"></use>
                        </svg>
                        {{ __('Penggajian') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('laporan*') ? 'active' : '' }}"
                        href="{{ route('laporan.neraca') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-balance-scale') }}"></use>
                        </svg>
                        {{ __('Neraca') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('laporan*') ? 'active' : '' }}"
                        href="{{ route('laporan.labarugi') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-chart-line') }}"></use>
                        </svg>
                        {{ __('Laba Rugi') }}
                    </a>
                </li>
            @endcan
        </ul>
    </li>

    <li class="nav-group" aria-expanded="false">
        <a class="nav-link nav-group-toggle" href="#">
            <svg class="nav-icon">
                <use xlink:href="{{ asset('icons/coreui.svg#cil-graph') }}"></use>
            </svg>
            Omzet
        </a>
        <ul class="nav-group-items" style="height: 0px;">
            @can('omzet_access')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('order*') ? 'active' : '' }}"
                        href="{{ route('order.omzet') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-calendar') }}"></use>
                        </svg>
                        {{ __('Tahunan') }}
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('order*') ? 'active' : '' }}"
                        href="{{ route('order.omzetBulan') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-calendar') }}"></use>
                        </svg>
                        {{ __('Bulanan') }}
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('produk*') ? 'active' : '' }}"
                        href="{{ route('produk.aset') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-building') }}"></use>
                        </svg>
                        {{ __('Aset') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('produk*') ? 'active' : '' }}"
                        href="{{ route('produk.omzet') }}">
                        <svg class="nav-icon">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-chart-line') }}"></use>
                        </svg>
                        {{ __('Produk Omzet') }}
                    </a>
                </li>
            @endcan
        </ul>
    </li>

    <li class="nav-group" aria-expanded="false">
        <a class="nav-link nav-group-toggle" href="#">
            <svg class="nav-icon">
                <use xlink:href="{{ asset('icons/coreui.svg#cil-people') }}"></use>
            </svg>
            User Management
        </a>
        <ul class="nav-group-items" style="height: 0px;">

            @can('user_access')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('users*') ? 'active' : '' }}"
                        href="{{ route('users.index') }}">
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
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-layers') }}"></use>
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
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-sitemap') }}"></use>
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
                    <a class="nav-link {{ request()->is('roles*') ? 'active' : '' }}"
                        href="{{ route('roles.index') }}">
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
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-lock-locked') }}"></use>
                        </svg>
                        {{ __('Permissions') }}
                    </a>
                </li>
            @endrole

            <li class="nav-item">
                <a class="nav-link {{ request()->is('produksis*') ? 'active' : '' }}"
                    href="{{ route('produksis.index') }}">
                    <svg class="nav-icon">
                        <use xlink:href="{{ asset('icons/coreui.svg#cil-wrench') }}"></use>
                    </svg>
                    {{ __('Setup Produksi') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->is('speks*') ? 'active' : '' }}"
                    href="{{ route('speks.index') }}">
                    <svg class="nav-icon">
                        <use xlink:href="{{ asset('icons/coreui.svg#cil-list') }}"></use>
                    </svg>
                    {{ __('Spek Produk') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->is('sistems*') ? 'active' : '' }}"
                    href="{{ route('sistem.index') }}">
                    <svg class="nav-icon">
                        <use xlink:href="{{ asset('icons/coreui.svg#cil-settings') }}"></use>
                    </svg>
                    {{ __('Sistem') }}
                </a>
            </li>
        </ul>
    </li>
</ul>
