@php
    use App\Auth\Permissions;

    $navOpen = fn(...$patterns) => collect($patterns)->contains(fn($p) => request()->is($p));

    $orderKeuanganPaths = ['admin/order/belumLunas*'];
    $orderProduksiExcluded = [
        'admin/order/dashboard*',
        'admin/order/marketplace*',
        'admin/order/belumLunas*',
        'admin/order/omzet*',
        'admin/order/arsip*',
    ];

    $activeOrderProses = request()->is('admin/order/dashboard*');
    $activeOrderArsip =
        request()->is('admin/order') || (request()->is('admin/order/*') && !request()->is(...$orderProduksiExcluded));
    $activeOrderOnline = request()->is('admin/order/marketplace*');
    $activeBelumLunas = request()->is(...$orderKeuanganPaths);
    $activeOmzetTahunan = request()->is('admin/order/omzet') || request()->is('admin/order/omzet/*');
    $activeOmzetBulanan = request()->is('admin/order/omzetBulan*');
    $activeOmzetMarketplace = request()->is('admin/marketplace/omzetBulan*');

    $activeMarketplaceAnalisa = request()->is('admin/analisaMarketplace*');
    $activeMarketplaceSyncStok = request()->is('admin/marketplaceSyncStok*');
    $activeAnalisaBeban = request()->is('admin/analisa/beban*');
    $activeAnalisaOperasional = request()->is('admin/analisa/operasional*');
    $activeAnalisaStok = request()->is('admin/analisa/stok*');

    $openProduksiOrder = $activeOrderProses || $activeOrderArsip || $activeOrderOnline;
    $openData = $navOpen(
        'admin/kontaks*',
        'admin/members*',
        'admin/freelance*',
        'admin/nonaktif*',
        'admin/ars*',
    );
    $openAbsensi = $navOpen('admin/absensi*', 'absensi*');
    $openKeuangan =
        $navOpen('admin/akunKategoris*', 'admin/akunDetails*', 'admin/belanja*', 'admin/hutang*', 'admin/kas') ||
        $activeBelumLunas;
    $openMarketplace =
        $navOpen('admin/projectmp*', 'admin/marketplaceProduk*', 'admin/marketplaces*', 'admin/marketplaceSyncStok*') ||
        $activeMarketplaceAnalisa;
    $openInventory = $navOpen('admin/produk-kategori-utama*', 'admin/pemakaian*', 'admin/opnames*', 'admin/po*');
    $openProduksiFactory = $navOpen('admin/produksi*', 'admin/produkProduksi*') && !$navOpen('admin/produksis*');
    $openAnalisa = $activeAnalisaBeban || $activeAnalisaOperasional || $activeAnalisaStok;
    $openLaporan = $navOpen(
        'admin/neraca*',
        'admin/labarugi*',
        'admin/labakotor*',
        'admin/tunjangan*',
        'admin/penggajian*',
        'admin/operasional*',
    );
    $openOmzet =
        $activeOmzetTahunan ||
        $activeOmzetBulanan ||
        $activeOmzetMarketplace ||
        $navOpen('admin/aset*', 'admin/produk/omzet*');
    $openUserMgmt = $navOpen('users*', 'admin/level*', 'admin/bagian*');
    $openConfig = $navOpen(
        'roles*',
        'permissions*',
        'admin/produksis*',
        'admin/speks*',
        'admin/pemproses*',
        'admin/sistem*',
        'admin/companies*',
        'admin/linkPages*',
    );

    $user = auth()->user();
    $navReads = Permissions::navGroupReads();

    $showProduksiOrder = $user->hasAnyPermission($navReads['proses_order']);
    $showData = $user->hasAnyPermission($navReads['data']);
    $showAbsensi = $user->hasAnyPermission($navReads['absensi']);
    $showKeuangan = $user->hasAnyPermission($navReads['keuangan']);
    $showMarketplace = $user->hasAnyPermission($navReads['marketplace']);
    $showInventory = $user->hasAnyPermission($navReads['inventory']);
    $showProduksiFactory = $user->hasAnyPermission($navReads['produksi']);
    $showAnalisa = $user->hasAnyPermission($navReads['analisa']);
    $showLaporan = $user->hasAnyPermission($navReads['laporan']);
    $showOmzet = $user->hasAnyPermission($navReads['omzet']);
    $showUserMgmt = $user->hasAnyPermission($navReads['user_mgmt']);
    $showConfig = $user->hasAnyPermission($navReads['config']) || $user->hasRole('super');
@endphp

<ul class="sidebar-nav compact" data-coreui="navigation" data-simplebar>
    @if ($showProduksiOrder)
        <li class="nav-group{{ $openProduksiOrder ? ' show' : '' }}"
            aria-expanded="{{ $openProduksiOrder ? 'true' : 'false' }}">
            <a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                    <use xlink:href="{{ asset('icons/coreui.svg#cil-industry') }}"></use>
                </svg>
                Proses Order
            </a>
            <ul class="nav-group-items">
                @can('order_proses_access')
                    <li class="nav-item">
                        <a class="nav-link {{ $activeOrderProses ? 'active' : '' }}" href="{{ route('order.dashboard') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-task') }}"></use>
                            </svg>
                            Proses
                        </a>
                    </li>
                @endcan
                @can('order_offline_access')
                    <li class="nav-item">
                        <a class="nav-link {{ $activeOrderArsip ? 'active' : '' }}" href="{{ route('order.index') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-folder-open') }}"></use>
                            </svg>
                            Arsip Offline
                        </a>
                    </li>
                @endcan
                @can('order_online_access')
                    <li class="nav-item">
                        <a class="nav-link {{ $activeOrderOnline ? 'active' : '' }}"
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
    @endif

    @if ($showMarketplace)
        <li class="nav-group{{ $openMarketplace ? ' show' : '' }}"
            aria-expanded="{{ $openMarketplace ? 'true' : 'false' }}">
            <a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                    <use xlink:href="{{ asset('icons/coreui.svg#cil-basket') }}"></use>
                </svg>
                Marketplace
            </a>
            <ul class="nav-group-items">
                @can('mp_custom_access')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/projectmp/dashboard*') ? 'active' : '' }}"
                            href="{{ route('projectmp.dashboard') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-speedometer') }}"></use>
                            </svg>
                            {{ __('Proses Custom') }}
                        </a>
                    </li>
                @endcan
                @can('mp_packing_access')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/projectmp/packing*') ? 'active' : '' }}"
                            href="{{ route('projectmp.packing') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-truck') }}"></use>
                            </svg>
                            {{ __('Proses Packing') }}
                        </a>
                    </li>
                @endcan
                @can('mp_arsip_access')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/projectmp/index*') ? 'active' : '' }}"
                            href="{{ route('projectmp.index') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-folder-open') }}"></use>
                            </svg>
                            {{ __('Arsip Order') }}
                        </a>
                    </li>
                @endcan
                @can('mp_produk_access')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/marketplaceProduk*') ? 'active' : '' }}"
                            href="{{ route('marketplaces.produk') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-tags') }}"></use>
                            </svg>
                            {{ __('Produk') }}
                        </a>
                    </li>
                @endcan
                @can('mp_config_access')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('marketplaces*') ? 'active' : '' }}"
                            href="{{ route('marketplaces.index') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-settings') }}"></use>
                            </svg>
                            {{ __('Config') }}
                        </a>
                    </li>
                @endcan
                @can('mp_sync_access')
                    <li class="nav-item">
                        <a class="nav-link {{ $activeMarketplaceSyncStok ? 'active' : '' }}"
                            href="{{ route('marketplaces.syncStokStatus') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-sync') }}"></use>
                            </svg>
                            {{ __('Sync Stok Shopee') }}
                        </a>
                    </li>
                @endcan
                @can('mp_analisa_access')
                    <li class="nav-item">
                        <a class="nav-link {{ $activeMarketplaceAnalisa ? 'active' : '' }}"
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
    @endif

    @if ($showData)
        <li class="nav-group{{ $openData ? ' show' : '' }}" aria-expanded="{{ $openData ? 'true' : 'false' }}">
            <a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                    <use xlink:href="{{ asset('icons/coreui.svg#cil-storage') }}"></use>
                </svg>
                Data
            </a>
            <ul class="nav-group-items">
                @can('kontak_access')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('kontaks*') || request()->is('admin/kontaks*') ? 'active' : '' }}"
                            href="{{ route('kontaks.index') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-user') }}"></use>
                            </svg>
                            {{ __('Kontak') }}
                        </a>
                    </li>
                @endcan
                @canany(['member_access', 'freelance_access'])
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/members*') || request()->is('admin/freelance*') || request()->is('admin/nonaktif*') ? 'active' : '' }}"
                            href="{{ auth()->user()->can('member_access') ? route('members.index') : route('members.freelance') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-people') }}"></use>
                            </svg>
                            Members
                        </a>
                    </li>
                @endcanany
                @can('ar_access')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/ars*') || request()->is('ars*') ? 'active' : '' }}"
                            href="{{ route('ars.index') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-headphones') }}"></use>
                            </svg>
                            {{ __('cs') }}
                        </a>
                    </li>
                @endcan
            </ul>
        </li>
    @endif

    @if ($showAbsensi)
        <li class="nav-group{{ $openAbsensi ? ' show' : '' }}" aria-expanded="{{ $openAbsensi ? 'true' : 'false' }}">
            <a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                    <use xlink:href="{{ asset('icons/coreui.svg#cil-calendar') }}"></use>
                </svg>
                Absensi
            </a>
            <ul class="nav-group-items">
                @can('absensi_scan')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('absensi/scan') ? 'active' : '' }}"
                            href="{{ route('absensi.scan') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-camera') }}"></use>
                            </svg>
                            Scan Absensi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('absensi/riwayat') ? 'active' : '' }}"
                            href="{{ route('absensi.riwayat') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-list') }}"></use>
                            </svg>
                            Riwayat
                        </a>
                    </li>
                @endcan
                @can('absensi_access')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/absensi') || request()->is('admin/absensi/create') ? 'active' : '' }}"
                            href="{{ route('absensi.index') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-spreadsheet') }}"></use>
                            </svg>
                            Data Absensi
                        </a>
                    </li>
                @endcan
                @can('absensi_edit')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/absensi/settings*') ? 'active' : '' }}"
                            href="{{ route('absensi.settings') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-settings') }}"></use>
                            </svg>
                            Pengaturan
                        </a>
                    </li>
                @endcan
            </ul>
        </li>
    @endif

    @if ($showKeuangan)
        <li class="nav-group{{ $openKeuangan ? ' show' : '' }}"
            aria-expanded="{{ $openKeuangan ? 'true' : 'false' }}">
            <a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                    <use xlink:href="{{ asset('icons/coreui.svg#cil-dollar') }}"></use>
                </svg>
                Keuangan
            </a>
            <ul class="nav-group-items">
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
                @can('kas_access')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('akunDetails*') ? 'active' : '' }}"
                            href="{{ route('akunDetail.kas') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-wallet') }}"></use>
                            </svg>
                            {{ __('kas') }}
                        </a>
                    </li>
                @endcan
                @can('belum_lunas_access')
                    <li class="nav-item">
                        <a class="nav-link {{ $activeBelumLunas ? 'active' : '' }}" href="{{ route('order.unpaid') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-credit-card') }}"></use>
                            </svg>
                            {{ __('belum lunas') }}
                        </a>
                    </li>
                @endcan
                @can('belanja_access')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('belanjas*') ? 'active' : '' }}"
                            href="{{ route('belanja.index') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-cart') }}"></use>
                            </svg>
                            Belanja
                        </a>
                    </li>
                @endcan
                @can('hutang_access')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('hutang*') ? 'active' : '' }}"
                            href="{{ route('hutang.index') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-file') }}"></use>
                            </svg>
                            Hutang/Piutang
                        </a>
                    </li>
                @endcan
            </ul>
        </li>
    @endif

    @if ($showInventory)
        <li class="nav-group{{ $openInventory ? ' show' : '' }}"
            aria-expanded="{{ $openInventory ? 'true' : 'false' }}">
            <a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                    <use xlink:href="{{ asset('icons/coreui.svg#cil-inbox') }}"></use>
                </svg>
                Inventory
            </a>
            <ul class="nav-group-items">
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
                @endcan
                @can('pemakaian_access')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('pemakaian*') ? 'active' : '' }}"
                            href="{{ route('pemakaian.index') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-basket') }}"></use>
                            </svg>
                            Pemakaian
                        </a>
                    </li>
                @endcan
                @can('opname_access')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('opnames*') ? 'active' : '' }}"
                            href="{{ route('opnames.index') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-clipboard') }}"></use>
                            </svg>
                            {{ __('Opname') }}
                        </a>
                    </li>
                @endcan
                @can('po_access')
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
    @endif

    @if ($showProduksiFactory)
        <li class="nav-group{{ $openProduksiFactory ? ' show' : '' }}"
            aria-expanded="{{ $openProduksiFactory ? 'true' : 'false' }}">
            <a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                    <use xlink:href="{{ asset('icons/coreui.svg#cil-inbox') }}"></use>
                </svg>
                Produksi
            </a>
            <ul class="nav-group-items">
                @can('produksi_proses_access')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('produksi*') ? 'active' : '' }}"
                            href="{{ route('produksi.index') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-factory') }}"></use>
                            </svg>
                            Proses
                        </a>
                    </li>
                @endcan
                @can('produksi_produk_access')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('produkProduksi*') ? 'active' : '' }}"
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
    @endif

    @if ($showAnalisa)
        <li class="nav-group{{ $openAnalisa ? ' show' : '' }}"
            aria-expanded="{{ $openAnalisa ? 'true' : 'false' }}">
            <a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                    <use xlink:href="{{ asset('icons/coreui.svg#cil-chart') }}"></use>
                </svg>
                Analisa
            </a>
            <ul class="nav-group-items">
                @can('analisa_beban_access')
                    <li class="nav-item">
                        <a class="nav-link {{ $activeAnalisaBeban ? 'active' : '' }}"
                            href="{{ route('analisa.beban') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-chart-line') }}"></use>
                            </svg>
                            Analisa Beban
                        </a>
                    </li>
                @endcan
                @can('analisa_operasional_access')
                    <li class="nav-item">
                        <a class="nav-link {{ $activeAnalisaOperasional ? 'active' : '' }}"
                            href="{{ route('analisa.operasional') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-bar-chart') }}"></use>
                            </svg>
                            Analisa Operasional
                        </a>
                    </li>
                @endcan
                @can('analisa_stok_access')
                    <li class="nav-item">
                        <a class="nav-link {{ $activeAnalisaStok ? 'active' : '' }}"
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
    @endif

    @if ($showLaporan)
        <li class="nav-group{{ $openLaporan ? ' show' : '' }}"
            aria-expanded="{{ $openLaporan ? 'true' : 'false' }}">
            <a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                    <use xlink:href="{{ asset('icons/coreui.svg#cil-description') }}"></use>
                </svg>
                Laporan
            </a>
            <ul class="nav-group-items">
                @can('laporan_tunjangan_access')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('laporan*') ? 'active' : '' }}"
                            href="{{ route('laporan.tunjangan') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-dollar') }}"></use>
                            </svg>
                            {{ __('Tunjangan') }}
                        </a>
                    </li>
                @endcan
                @can('laporan_penggajian_access')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('laporan*') ? 'active' : '' }}"
                            href="{{ route('laporan.penggajian') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-wallet') }}"></use>
                            </svg>
                            {{ __('Penggajian') }}
                        </a>
                    </li>
                @endcan
                @can('laporan_neraca_access')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('laporan*') ? 'active' : '' }}"
                            href="{{ route('laporan.neraca') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-balance-scale') }}"></use>
                            </svg>
                            {{ __('Neraca') }}
                        </a>
                    </li>
                @endcan
                @can('laporan_labarugi_access')
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
    @endif

    @if ($showOmzet)
        <li class="nav-group{{ $openOmzet ? ' show' : '' }}" aria-expanded="{{ $openOmzet ? 'true' : 'false' }}">
            <a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                    <use xlink:href="{{ asset('icons/coreui.svg#cil-graph') }}"></use>
                </svg>
                Omzet
            </a>
            <ul class="nav-group-items">
                @can('omzet_tahunan_access')
                    <li class="nav-item">
                        <a class="nav-link {{ $activeOmzetTahunan ? 'active' : '' }}"
                            href="{{ route('order.omzet') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-calendar') }}"></use>
                            </svg>
                            {{ __('Tahunan') }}
                        </a>
                    </li>
                @endcan
                @can('omzet_bulanan_access')
                    <li class="nav-item">
                        <a class="nav-link {{ $activeOmzetBulanan ? 'active' : '' }}"
                            href="{{ route('order.omzetBulan') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-calendar') }}"></use>
                            </svg>
                            {{ __('Bulanan') }}
                        </a>
                    </li>
                @endcan
                @can('omzet_marketplace_access')
                    <li class="nav-item">
                        <a class="nav-link {{ $activeOmzetMarketplace ? 'active' : '' }}"
                            href="{{ route('marketplaces.omzetBulan') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-basket') }}"></use>
                            </svg>
                            {{ __('Marketplace') }}
                        </a>
                    </li>
                @endcan
                @can('omzet_aset_access')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('produk*') ? 'active' : '' }}"
                            href="{{ route('produk.aset') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-building') }}"></use>
                            </svg>
                            {{ __('Aset') }}
                        </a>
                    </li>
                @endcan
                @can('omzet_produk_access')
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
    @endif

    @if ($showUserMgmt)
        <li class="nav-group{{ $openUserMgmt ? ' show' : '' }}"
            aria-expanded="{{ $openUserMgmt ? 'true' : 'false' }}">
            <a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                    <use xlink:href="{{ asset('icons/coreui.svg#cil-people') }}"></use>
                </svg>
                Setting
            </a>
            <ul class="nav-group-items">
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
    @endif

    @if ($showConfig)
        <li class="nav-group{{ $openConfig ? ' show' : '' }}"
            aria-expanded="{{ $openConfig ? 'true' : 'false' }}">
            <a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                    <use xlink:href="{{ asset('icons/coreui.svg#cil-cog') }}"></use>
                </svg>
                Config
            </a>
            <ul class="nav-group-items">
                @can('rbac.manage')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('roles*') || request()->is('permissions*') ? 'active' : '' }}"
                            href="{{ route('permissions.index') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-lock-locked') }}"></use>
                            </svg>
                            Roles & Akses
                        </a>
                    </li>
                @endcan
                @can('setup_produksi_access')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('produksis*') ? 'active' : '' }}"
                            href="{{ route('produksis.index') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-wrench') }}"></use>
                            </svg>
                            {{ __('Setup Produksi') }}
                        </a>
                    </li>
                @endcan
                @can('spek_access')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('speks*') ? 'active' : '' }}"
                            href="{{ route('speks.index') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-list') }}"></use>
                            </svg>
                            {{ __('Spek Produk') }}
                        </a>
                    </li>
                @endcan
                @can('pemproses_access')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('pemproses*') ? 'active' : '' }}"
                            href="{{ route('pemproses.index') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-factory') }}"></use>
                            </svg>
                            {{ __('Pemproses') }}
                        </a>
                    </li>
                @endcan
                @can('sistem_access')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('sistems*') || request()->is('admin/sistem*') ? 'active' : '' }}"
                            href="{{ route('sistem.index') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-settings') }}"></use>
                            </svg>
                            {{ __('Sistem') }}
                        </a>
                    </li>
                @endcan
                @can('company_access')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/companies*') ? 'active' : '' }}"
                            href="{{ route('companies.index') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-building') }}"></use>
                            </svg>
                            {{ __('Company') }}
                        </a>
                    </li>
                @endcan
                @can('link_page_access')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/linkPages*') ? 'active' : '' }}"
                            href="{{ route('linkPages.index') }}">
                            <svg class="nav-icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-link') }}"></use>
                            </svg>
                            {{ __('Link Pages') }}
                        </a>
                    </li>
                @endcan
            </ul>
        </li>
    @endif
</ul>

@push('after-scripts')
    <script>
        window.addEventListener('load', function() {
            document.querySelectorAll('.sidebar-nav .nav-group').forEach(function(group) {
                if (!group.querySelector('.nav-group-items .nav-link.active')) {
                    return;
                }

                group.classList.add('show');
                group.setAttribute('aria-expanded', 'true');

                var items = group.querySelector('.nav-group-items');
                if (items) {
                    items.style.removeProperty('height');
                }
            });
        });
    </script>
@endpush
