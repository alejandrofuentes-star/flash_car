<header>
    <nav class="header_principal_usuarios shadow-sm d-flex align-items-center justify-content-center">
        <div class="col-6 col-sm-6 col-md-4 col-lg-2 d-flex align-items-center justify-content-center">
            <a href="{{ route('inicio') }}"><img src="{{ asset('./img/logo.webp') }}" width="120px" alt="logo Flash Car"></a>
        </div>
        <div class="col-6 col-sm-6 col-md-8 col-lg-10 d-flex align-items-center justify-content-end px-5">
            <div class="px-2 display_flex_center_center" id="cont_menu_desktop">
                <a class="fs-6 link_submenu_user" href="{{ route('inicio') }}#inicio"><b>{{ __('nav.home') }}</b></a>
                <a class="fs-6 link_submenu_user" href="{{ route('inicio') }}#catalogo">{{ __('nav.catalog') }}</a>
                <a class="fs-6 link_submenu_user" href="{{ route('faqs') }}">{{ __('nav.faqs') }}</a>
                <a class="fs-6 link_submenu_user" href="{{ route('inicio') }}#contacto">{{ __('nav.contact') }}</a>
                <a class="fs-6 boton_link_md rounded" href="{{ route('login') }}">Login</a>
                <a href="{{ route('lang.switch', app()->getLocale() === 'es' ? 'en' : 'es') }}"
                   class="ms-2 text-decoration-none d-flex align-items-center justify-content-center rounded"
                   style="width:36px;height:26px;border:1px solid #ddd;overflow:hidden;"
                   title="{{ app()->getLocale() === 'es' ? 'Switch to English' : 'Cambiar a Español' }}">
                    @if(app()->getLocale() === 'es')
                        <img src="https://flagcdn.com/w40/us.png" width="36" height="26" alt="EN" style="object-fit:cover;">
                    @else
                        <img src="https://flagcdn.com/w40/mx.png" width="36" height="26" alt="ES" style="object-fit:cover;">
                    @endif
                </a>
            </div>
            <div class="boton_submenu rounded" id="boton_submenu">
                <i class="bi bi-list"></i>
            </div>
        </div>
    </nav>
    <div class="submenu_clientes p-2" id="submenu_clientes">
        <a class="link_submenu_clientes py-2 fs-6 border_link_bottom" href="{{ route('inicio') }}#inicio"><b>{{ __('nav.home') }}</b></a>
        <a class="link_submenu_clientes py-2 fs-6 border_link_bottom" href="{{ route('inicio') }}#catalogo">{{ __('nav.catalog') }}</a>
        <a class="link_submenu_clientes py-2 fs-6 border_link_bottom" href="{{ route('faqs') }}">{{ __('nav.faqs') }}</a>
        <a class="link_submenu_clientes py-2 fs-6 border_link_bottom" href="{{ route('inicio') }}#contacto">{{ __('nav.contact') }}</a>
        <a class="link_submenu_clientes py-2 fs-6 border_link_bottom" href="{{ route('login') }}">Login</a>
        <a href="{{ route('lang.switch', app()->getLocale() === 'es' ? 'en' : 'es') }}"
           class="py-1 text-decoration-none d-flex align-items-center justify-content-center rounded"
           style="width:36px;height:26px;border:1px solid #ddd;overflow:hidden;"
           title="{{ app()->getLocale() === 'es' ? 'Switch to English' : 'Cambiar a Español' }}">
            @if(app()->getLocale() === 'es')
                <img src="https://flagcdn.com/w40/us.png" width="36" height="26" alt="EN" style="object-fit:cover;">
            @else
                <img src="https://flagcdn.com/w40/mx.png" width="36" height="26" alt="ES" style="object-fit:cover;">
            @endif
        </a>
    </div>
    <div class="click_any_side"></div>
</header>
