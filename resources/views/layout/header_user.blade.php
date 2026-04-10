<header>
    <nav class="header_principal_usuarios shadow-sm d-flex align-items-center justify-content-center">
        <div class="col-6 col-sm-6 col-md-4 col-lg-2 d-flex align-items-center justify-content-center">
            <a href="{{ route('inicio') }}"><img src="{{ asset('./img/logo.webp') }}" width="120px" alt="logo HD rent Car"></a>
        </div>
        <div class="col-6 col-sm-6 col-md-8 col-lg-10 d-flex align-items-center justify-content-end px-5">
            <div class="px-2 display_flex_center_center" id="cont_menu_desktop">
                <a class="fs-6 link_submenu_user" href="{{ route('inicio') }}#inicio"><b>Inicio</b></a>
                <a class="fs-6 link_submenu_user" href="{{ route('inicio') }}#catalogo">Catálogo</a>
                <a class="fs-6 link_submenu_user" href="{{ route('faqs') }}">FAQ's</a>
                <a class="fs-6 link_submenu_user" href="{{ route('inicio') }}#contacto">Contacto</a>
                <a class="fs-6 boton_link_md rounded" href="{{ route('login') }}">Login</a>
            </div>
            <div class="boton_submenu rounded" id="boton_submenu">
                <i class="bi bi-list"></i>
            </div>
        </div>
    </nav>
    <div class="submenu_clientes p-2" id="submenu_clientes">
        <a class="link_submenu_clientes py-1 fs-6 border_link_bottom" href="{{ route('inicio') }}#inicio"><b>Inicio</b></a>
        <a class="link_submenu_clientes py-1 fs-6 border_link_bottom" href="{{ route('inicio') }}#catalogo">Catálogo</a>
        <a class="link_submenu_clientes py-1 fs-6 border_link_bottom" href="{{ route('faqs') }}">FAQ's</a>
        <a class="link_submenu_clientes py-1 fs-6 border_link_bottom" href="{{ route('inicio') }}#contacto">Contacto</a>
        <a class="link_submenu_clientes py-1 fs-6 border_link_bottom" href="{{ route('login') }}">Login</a>
    </div>
    <div class="click_any_side"></div>
</header>