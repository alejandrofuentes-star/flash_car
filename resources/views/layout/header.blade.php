@auth
<header>
    <nav class="header_principal shadow-sm d-flex align-items-center justify-content-center">
        <div class="col-4 d-flex align-items-center justify-content-center">
            <a href="{{ route('dashboard') }}"><img src="{{ asset('./img/logo.webp') }}" width="120px" alt="logo HD rent Car"></a>
        </div>
        <div class="col-8 d-flex align-items-center justify-content-end px-5">
            <div class="circulo_img_perfil" id="boton_menu">
                <p class="letra_perfil_submenu">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</p>
            </div>
        </div>
    </nav>
    <div class="submenu p-2" id="submenu">
        <p class="nombre_perfil fs-5 m-0"><b>{{ Auth::user()->name }}</b></p>
        <a class="link_submenu py-1 fs-6 border_link_bottom" href="{{ route('dashboard') }}"><i class="bi bi-house-fill"></i><b> Inicio</b></a>
        <a class="link_submenu py-1 fs-6 border_link_bottom" href="{{ route('users.index') }}"><i class="bi bi-people-fill"></i> Usuarios</a>
        <a class="link_submenu py-1 fs-6 border_link_bottom" href="{{ route('vehiculos.index') }}"><i class="bi bi-car-front-fill"></i> Autos</a>
        <a class="link_submenu py-1 fs-6 border_link_bottom" href="{{ route('states.index') }}"><i class="bi bi-geo-alt-fill"></i> Ubicaciones</a>
        <a class="link_submenu py-1 fs-6 border_link_bottom" href="{{ route('rentas.index') }}"><i class="bi bi-clipboard-check-fill"></i> Rentas</a>
        @if(Auth::user()->hasAdminAccess())
        <a class="link_submenu py-1 fs-6 border_link_bottom" href="{{ route('slider.index') }}"><i class="bi bi-images"></i> Slider</a>
        <a class="link_submenu py-1 fs-6 border_link_bottom" href="{{ route('settings.index') }}"><i class="bi bi-sliders"></i> Configuración</a>
        @endif

        {{-- Solo visible para super_admin --}}
        @if(Auth::user()->role === 'super_admin')
            <a class="link_submenu py-1 fs-6 border_link_bottom" href="{{ route('system.cache') }}">
                <i class="bi bi-tools"></i> Sistema
            </a>
        @endif

        <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
            @csrf
            <button class="link_submenu py-1 fs-6 border_link_bottom" type="submit"><i class="bi bi-door-open-fill"></i> Cerrar sesión</button>
        </form>
    </div>
    <div class="click_any_side"></div>
</header>
@endauth