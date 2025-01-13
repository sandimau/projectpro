<header class="header header-sticky mb-4">
    <div class="container-fluid">
        <button class="header-toggler px-md-0 me-md-3" type="button"
            onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()">
            <svg class="icon icon-lg">
                <use xlink:href="{{ asset('icons/coreui.svg#cil-menu') }}"></use>
            </svg>
        </button>
        <a class="header-brand d-md-none" href="#">
            @if (session()->has('logo'))
                <img style="height:35px"
                    src="{{ url('storage/logo/' . session('logo')[0] . session('logo')[1] . '/' . session('logo')[2] . session('logo')[3] . '/' . session('logo')) }}"
                    alt="" srcset="">
            @endif
        </a>
        @auth
        <ul class="header-nav ms-auto">
            <li class="nav-item dropdown ms-auto">
                <a class="nav-link py-0" data-coreui-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                    aria-expanded="false">
                    <div class="avatar">
                        <img class="avatar-img" src="{{ asset('img/default-avatar.jpg') }}">
                    </div>
                    {{  Auth::user()->name }}
                </a>
                <div style="z-index: 5" class="dropdown-menu dropdown-menu-end pt-0">
                    <a class="dropdown-item" href="{{ route('whattodo') }}">
                        <svg class="icon me-2">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-airplay') }}"></use>
                        </svg> What To Do
                    </a>

                    <a class="dropdown-item" href="{{ route('profile.cuti', Auth::user()->id) }}">
                        <svg class="icon me-2">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-user') }}"></use>
                        </svg>
                        {{ __('Cuti') }}
                    </a>

                    <a class="dropdown-item" href="{{ route('profile.gaji', Auth::user()->id) }}">
                        <svg class="icon me-2">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-user') }}"></use>
                        </svg>
                        {{ __('Gaji') }}
                    </a>

                    <a class="dropdown-item" href="{{ route('profile.show', Auth::user()->id) }}">
                        <svg class="icon me-2">
                            <use xlink:href="{{ asset('icons/coreui.svg#cil-user') }}"></use>
                        </svg>
                        {{ __('Ganti Password') }}
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            <svg class="icon me-2">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-account-logout') }}"></use>
                            </svg>
                            {{ __('Logout') }}
                        </a>
                    </form>
                </div>
            </li>
        </ul>
        @endauth
    </div>
</header>
