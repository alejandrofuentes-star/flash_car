@extends('layout.layouts')

@section('title', 'Flash Car')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/styles_pagina_principal.css') }}?v=1.6">
@endpush

@section('content')
@include('layout.header_user')
<!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
<!--inicio-->
<section>

<div class="col-12 d-flex align-items-center justify-content-center" id="inicio">
    <div class="slider_container">

        {{-- IMÁGENES ESCRITORIO (ocultas en móvil) --}}
        <div class="slider_track d-none d-md-block">
            @forelse($sliderDesktop as $i => $slide)
                <div class="slide {{ $i === 0 ? 'active' : '' }}">
                    <img src="{{ Storage::url($slide->image_path) }}" alt="Slide {{ $i + 1 }}">
                </div>
            @empty
                <div class="slide active" style="background:#000; width:100%; height:100vh; display:block;"></div>
            @endforelse
        </div>

        {{-- IMÁGENES MÓVIL (ocultas en escritorio) --}}
        <div class="slider_track d-block d-md-none">
            @forelse($sliderMobile as $i => $slide)
                <div class="slide {{ $i === 0 ? 'active' : '' }}">
                    <img src="{{ Storage::url($slide->image_path) }}" alt="Slide {{ $i + 1 }}">
                </div>
            @empty
                <div class="slide active" style="background:#000; width:100%; height:100vh; display:block;"></div>
            @endforelse
        </div>

        {{-- Flechas --}}
        <button class="slider_btn slider_prev" onclick="moveSlide(-1)">&#10094;</button>
        <button class="slider_btn slider_next" onclick="moveSlide(1)">&#10095;</button>

        {{-- Puntos indicadores --}}
        <div class="slider_dots">
        </div>

    </div>
    <div class="col-12 d-flex align-items-center justify-content-center cont_principal_slider">
        <div class="col-12 d-flex align-items-center justify-content-center flex-wrap">
            <div class="col-11 col-sm-10 col-md-10 col-lg-9 col-xl-8 p-3">
                <div class="hero_form_card">
                    <h1 class="hero_titulo">{{ __('hero.title') }}</h1>
                    <p class="hero_subtitulo">{{ __('hero.subtitle') }}</p>
                    <form action="{{ route('catalogo.buscar') }}#catalogo" method="GET" class="hero_search_form">
                        <div class="hero_input_wrap hero_field_city">
                            <i class="bi bi-geo-alt-fill hero_input_icon"></i>
                            <select class="hero_input" name="city">
                                <option value="">{{ __('hero.city') }}</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city }}">{{ $city }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="hero_input_wrap hero_date_field">
                            <i class="bi bi-calendar-event hero_input_icon"></i>
                            <span class="hero_date_label">Fecha entrega</span>
                            <input class="hero_input hero_date_input" type="date" name="fecha_entrega">
                        </div>
                        <div class="hero_input_wrap hero_date_field">
                            <i class="bi bi-calendar-check hero_input_icon"></i>
                            <span class="hero_date_label">Fecha devolución</span>
                            <input class="hero_input hero_date_input" type="date" name="fecha_devolucion">
                        </div>
                        <button type="submit" class="hero_btn hero_btn_inline">
                            <i class="bi bi-search"></i> {{ __('hero.search') }}
                        </button>
                    </form>
                    <div class="hero_badges">
                        <span class="hero_badge"><i class="bi bi-airplane-fill"></i> {{ __('hero.airport') }}</span>
                        <span class="hero_badge"><i class="bi bi-shield-check-fill"></i> {{ __('hero.insurance') }}</span>
                        <span class="hero_badge"><i class="bi bi-lightning-charge-fill"></i> {{ __('hero.no_lines') }}</span>
                        <span class="hero_badge hero_stat_badge">
                            <span style="width:8px;height:8px;border-radius:50%;background:#28a745;display:inline-block;flex-shrink:0;animation:pulso_verde 1.5s infinite;"></span>
                            <span id="contador_visitantes" style="font-size:0.78rem;color:rgba(255,255,255,0.85);">— visitando ahora</span>
                        </span>
                        <span class="hero_badge hero_stat_badge">
                            <i class="bi bi-calendar-check-fill" style="font-size:0.78rem;color:rgba(255,255,255,0.7);"></i>
                            <span style="font-size:0.78rem;color:rgba(255,255,255,0.85);"><b>{{ number_format($totalReservaciones) }}</b> reservaciones realizadas</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</section>
<!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
<!--contador de mundial-->
<section>
    <div class="py-5 bg_amarillo img_fondo_mundial">
        <div class="container">
            <div class="row">
                <div class="col-12 d-flex align-items-center justify-content-center">
                    <h2 class="fs-2 pb-2 text-center text_uppcase">{{ __('countdown.title') }}</h2>
                </div>
                <div class="linea_degradada_mundial"></div>
                <div class="col-12 d-flex align-items-center justify-content-center py-2">
                    <div class="col-2 d-flex align-items-center justify-content-center flex-column">
                        <p class="fs-1 m-0"><b id="meses">0</b></p>
                        <p class="fs-4 m-0">{{ __('countdown.months') }}</p>
                    </div>
                    <div class="col-2 d-flex align-items-center justify-content-center">
                        <img src="{{ asset('./img/balon.png') }}" width="30%" alt="balon flash car">
                    </div>
                    <div class="col-2 d-flex align-items-center justify-content-center flex-column">
                        <p class="fs-1 m-0"><b id="dias">0</b></p>
                        <p class="fs-4 m-0">{{ __('countdown.days') }}</p>
                    </div>
                    <div class="col-2 d-flex align-items-center justify-content-center">
                        <img src="{{ asset('./img/balon.png') }}" width="30%" alt="balon flash car">
                    </div>
                    <div class="col-2 d-flex align-items-center justify-content-center flex-column">
                        <p class="fs-1 m-0"><b id="horas">0</b></p>
                        <p class="fs-4 m-0">{{ __('countdown.hours') }}</p>
                    </div>
                </div>
                <div class="linea_degradada_mundial"></div>
            </div>
        </div>
    </div>
</section>
<!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
<!--segunda sección - pasos-->
<section>
    <div class="py-5 bg-white">
        <div class="container">
            <div class="col-12 d-flex align-items-center justify-content-center flex-column mb-4">
                <h2 class="fs-3 fw-bold text-center">{{ __('steps.title') }}</h2>
                <div class="linea_degradada my-2"></div>
            </div>
            <div class="col-12 d-flex align-items-center justify-content-center flex-wrap">

                <div class="paso_item">
                    <div class="paso_numero">1</div>
                    <div class="paso_icono">
                        <img src="{{ asset('./img/icono_reservar.png') }}" height="110px" alt="reservar">
                    </div>
                    <p class="paso_titulo">{{ __('steps.1.title') }}</p>
                    <p class="paso_desc">{{ __('steps.1.desc') }}</p>
                </div>

                <div class="paso_flecha d-none d-md-flex">&#10132;</div>

                <div class="paso_item">
                    <div class="paso_numero">2</div>
                    <div class="paso_icono">
                        <img src="{{ asset('./img/icono_contrato.png') }}" height="110px" alt="recoge">
                    </div>
                    <p class="paso_titulo">{{ __('steps.2.title') }}</p>
                    <p class="paso_desc">{{ __('steps.2.desc') }}</p>
                </div>

                <div class="paso_flecha d-none d-md-flex">&#10132;</div>

                <div class="paso_item">
                    <div class="paso_numero">3</div>
                    <div class="paso_icono">
                        <img src="{{ asset('./img/icono_disfruta.png') }}" height="110px" alt="disfruta">
                    </div>
                    <p class="paso_titulo">{{ __('steps.3.title') }}</p>
                    <p class="paso_desc">{{ __('steps.3.desc') }}</p>
                </div>

            </div>
        </div>
    </div>
</section>
<!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
<!--carrucel infinito-->
<div class="carrucel_infinito_wrap">
    <button class="carrucel_btn carrucel_prev" id="carrucel_prev" aria-label="Anterior"><i class="bi bi-chevron-left"></i></button>
    <div class="carrucel_viewport" id="carrucel_viewport">
        <div class="carrucel_infinito_track" id="carrucel_track">
            {{-- 3 clones del final (para loop hacia atrás) --}}
            <img src="{{ asset('./img/carrucel/flash3.jpg') }}" alt="Flash Car" class="carrucel_infinito_img">
            <img src="{{ asset('./img/carrucel/flash4.jpg') }}" alt="Flash Car" class="carrucel_infinito_img">
            <img src="{{ asset('./img/carrucel/flash5.jpg') }}" alt="Flash Car" class="carrucel_infinito_img">
            {{-- imágenes reales --}}
            <img src="{{ asset('./img/carrucel/flash1.gif') }}" alt="Flash Car" class="carrucel_infinito_img">
            <img src="{{ asset('./img/carrucel/flash2.jpg') }}"  alt="Flash Car" class="carrucel_infinito_img">
            <img src="{{ asset('./img/carrucel/flash3.jpg') }}"  alt="Flash Car" class="carrucel_infinito_img">
            <img src="{{ asset('./img/carrucel/flash4.jpg') }}"  alt="Flash Car" class="carrucel_infinito_img">
            <img src="{{ asset('./img/carrucel/flash5.jpg') }}"  alt="Flash Car" class="carrucel_infinito_img">
            {{-- 3 clones del inicio (para loop hacia adelante) --}}
            <img src="{{ asset('./img/carrucel/flash1.gif') }}" alt="Flash Car" class="carrucel_infinito_img">
            <img src="{{ asset('./img/carrucel/flash2.jpg') }}" alt="Flash Car" class="carrucel_infinito_img">
            <img src="{{ asset('./img/carrucel/flash3.jpg') }}" alt="Flash Car" class="carrucel_infinito_img">
        </div>
    </div>
    <button class="carrucel_btn carrucel_next" id="carrucel_next" aria-label="Siguiente"><i class="bi bi-chevron-right"></i></button>
</div>
<script>
(function () {
    const viewport      = document.getElementById('carrucel_viewport');
    const track         = document.getElementById('carrucel_track');
    const itemsPerView  = 3;
    const totalReal     = 5;
    let current         = itemsPerView; // empieza en la primera imagen real
    let transitioning   = false;

    function itemWidth() { return viewport.offsetWidth / itemsPerView; }

    function moveTo(index, animate) {
        track.style.transition = animate ? 'transform 0.5s ease' : 'none';
        track.style.transform  = `translateX(-${index * itemWidth()}px)`;
    }

    function next() {
        if (transitioning) return;
        transitioning = true;
        current++;
        moveTo(current, true);
    }

    function prev() {
        if (transitioning) return;
        transitioning = true;
        current--;
        moveTo(current, true);
    }

    track.addEventListener('transitionend', function () {
        if (current > totalReal + itemsPerView - 1) { current -= totalReal; moveTo(current, false); }
        if (current < itemsPerView)                 { current += totalReal; moveTo(current, false); }
        transitioning = false;
    });

    moveTo(current, false);

    let auto       = 0;
    let reanudar   = 0;

    function startAuto() { clearInterval(auto); auto = setInterval(next, 5000); }
    function stopAuto()  { clearInterval(auto); clearTimeout(reanudar); }
    function scheduleResume() { reanudar = setTimeout(startAuto, 4000); }

    startAuto();

    ['carrucel_prev', 'carrucel_next'].forEach(id => {
        const btn    = document.getElementById(id);
        const action = id === 'carrucel_prev' ? prev : next;
        btn.addEventListener('click',      () => { stopAuto(); action(); scheduleResume(); });
        btn.addEventListener('touchstart', () => { stopAuto(); action(); scheduleResume(); }, { passive: true });
    });
    window.addEventListener('resize', () => moveTo(current, false));
})();
</script>
<!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
<!--catálogo-->
<div class="py-5 bg-light" id="catalogo">
    <div class="container">
        {{-- ENCABEZADO SECCIÓN --}}
        <div class="col-12 d-flex align-items-center justify-content-center flex-column mb-3">
            <h2 class="fs-2 text-center"><b>{{ __('catalog.title') }}</b></h2>
            <div class="linea_degradada my-1"></div>
        </div>

        {{-- FILTROS --}}
        <div class="col-12 d-flex align-items-center justify-content-center flex-wrap py-2 mb-2" style="gap:8px;">
            <select class="shadow-sm input_busqueda rounded px-2" style="height:40px; min-width:160px;" id="filtro_category">
                <option value="">{{ __('catalog.category') }}</option>
                @foreach($categories as $category)
                    <option value="{{ strtolower($category->name) }}">{{ $category->name }}</option>
                @endforeach
            </select>
            <select class="shadow-sm input_busqueda rounded px-2" style="height:40px; min-width:160px;" id="filtro_passengers">
                <option value="">{{ __('catalog.passengers') }}</option>
                @foreach($passengers as $p)
                    <option value="{{ $p }}">{{ $p }} pax</option>
                @endforeach
            </select>
        </div>

        {{-- GRID DE TARJETAS HORIZONTAL --}}
        <div id="catalogo_grid" class="row row-cols-1 row-cols-md-2 g-3 my-1">

            @forelse($vehicles as $vehicle)
            <div class="col tarjeta_vehiculo" data-category="{{ strtolower($vehicle->category->name ?? '') }}" data-passengers="{{ $vehicle->passengers }}">
                <div class="tarjeta_catalogo">

                    {{-- IMAGEN --}}
                    <div class="tarjeta_catalogo_img">
                        @if($vehicle->image_path)
                            <img src="{{ Storage::url($vehicle->image_path) }}" alt="{{ $vehicle->name }}">
                        @else
                            <img src="{{ asset('./img/sin_url_auto.png') }}" alt="Sin imagen">
                        @endif
                    </div>

                    {{-- CONTENIDO --}}
                    <div class="tarjeta_catalogo_body">
                        <div>
                            {{-- Disponibilidad --}}
                            @if($vehicle->available)
                                <span class="badge bg-success badge_disponible">{{ __('catalog.available') }}</span>
                            @else
                                <span class="badge bg-danger badge_disponible">{{ __('catalog.not_available') }}</span>
                            @endif

                            {{-- Nombre --}}
                            <h5 class="fw-bold mt-1 mb-0 fs-5">{{ $vehicle->name }}</h5>
                            <p class="text-muted mb-2" style="font-size:0.8rem;">
                                {{ $vehicle->brand }} {{ $vehicle->model }}{{ $vehicle->year ? ' '.$vehicle->year : '' }} {{ __('catalog.or_similar') }}
                            </p>

                            {{-- Info --}}
                            <p class="mb-1" style="font-size:0.8rem;">
                                <i class="icon_target bi bi-tag-fill"></i> {{ $vehicle->category->name ?? '—' }}
                                &nbsp;·&nbsp;
                                <i class="icon_target bi bi-gear-wide"></i> {{ $vehicle->transmission == 'automatic' ? __('catalog.automatic') : __('catalog.manual') }}
                            </p>
                        </div>

                        <div>
                            {{-- Precio --}}
                            @if($vehicle->category)
                            <p class="tarjeta_precio mb-2">
                                {{ $vehicle->category->formatted_price_per_day }}
                                <span>{{ __('catalog.per_day') }}</span>
                            </p>
                            @endif

                            {{-- Botón --}}
                            <a href="{{ route('catalogo.detalle', $vehicle->id) }}" class="boton_reservar">
                                {{ __('catalog.book_now') }}
                            </a>
                        </div>
                    </div>

                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted fs-5">{{ __('catalog.empty') }}</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
<!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
<!--tercera sección-->
<section>
    <div class="py-5 bg_amarillo">
        <div class="container">
            <div class="row">
                <div class="col-12 d-flex align-items-center justify-content-center flex-wrap">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-6 d-flex align-items-start justify-content-center flex-column">
                        <h2 class="fs-3">{{ __('advantages.title') }}</h2>
                        <p class="fs-5">{{ __('advantages.subtitle') }}</p>
                        <div class="col-12 d-flex align-items-center justify-content-center">
                            <img src="{{ asset('./img/carros.webp') }}" width="100%" alt="flash car catalogo">
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-12 col-lg-6 d-flex align-items-start justify-content-center flex-column">
                        <div class="col-12 d-flex align-items-start justify-content-center flex-wrap">
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4 d-flex align-items-start justify-content-center">
                                <img src="{{ asset('./img/icono_kilometraje.png') }}" width="80px" alt="iconos flash car">
                            </div>
                            <div class="col-12 col-sm-12 col-md-8 col-lg-8 d-flex align-items-start justify-content-center flex-column">
                                <h3 class="fs-5">{{ __('advantages.km.title') }}</h3>
                                <p class="fs-6">{{ __('advantages.km.desc') }}</p>
                            </div>
                        </div>
                        <div class="col-12 d-flex align-items-start justify-content-center flex-wrap">
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4 d-flex align-items-start justify-content-center">
                                <img src="{{ asset('./img/icono_depositos.png') }}" width="80px" alt="iconos flash car">
                            </div>
                            <div class="col-12 col-sm-12 col-md-8 col-lg-8 d-flex align-items-start justify-content-center flex-column">
                                <h3 class="fs-5">{{ __('advantages.deposit.title') }}</h3>
                                <p class="fs-6">{{ __('advantages.deposit.desc') }}</p>
                            </div>
                        </div>
                        <div class="col-12 d-flex align-items-start justify-content-center flex-wrap">
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4 d-flex align-items-start justify-content-center">
                                <img src="{{ asset('./img/icono_marcas.png') }}" width="80px" alt="iconos flash car">
                            </div>
                            <div class="col-12 col-sm-12 col-md-8 col-lg-8 d-flex align-items-start justify-content-center flex-column">
                                <h3 class="fs-5">{{ __('advantages.brands.title') }}</h3>
                                <p class="fs-6">{{ __('advantages.brands.desc') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="ubicaciones">
    <div class="py-5 bg_gris_custom">
        <div class="container">
            <div class="row">
                <div class="col-12 d-flex align-items-start justify-content-center flex-wrap">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-6 d-flex align-items-start justify-content-center">
                        <div class="col-11 col-sm-11 col-md-10 bg_amarillo d-flex align-items-start justify-content-center flex-column rounded shadow-sm p-3">
                            <h2 class="fs-4">{{ __('coverage.title') }}</h2>
                            <p class="fs-5">{{ __('coverage.desc') }}</p>
                            <p class="fs-5"><b>{{ __('coverage.accepts') }}</b></p>
                            <img src="{{ asset('./img/metodos_de_pago.png') }}" height="50px" alt="tarjetas flash car">
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-12 col-lg-6 d-flex align-items-start justify-content-center">
                        <img src="{{ asset('./img/mapa_flash_car.png') }}" width="100%" alt="mapa flash car">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="py-5 bg-light" id="contacto">
        <div class="container">
            <div class="row">
                <div class="col-12 d-flex align-items-start justify-content-center flex-wrap">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-6 align-items-start justify-content-center flex-column">
                        <img src="{{ asset('./img/logo.webp') }}" height="60px" alt="logo flash car">
                        <p class="fs-6">{{ __('contact.about') }}</p>
                        <div class="col-12 d-flex align-items-center justify-content-start flex-wrap">
                            <a class="col-6 col-sm-6 col-md-4 link_contactos px-3 m-1 py-1 fs-6 rounded shadow" href="tel:+52{{ App\Models\SiteSetting::get('telefono') }}"><i class="bi bi-telephone-fill"></i>{{ App\Models\SiteSetting::get('telefono') }}</a>
                            <a class="col-6 col-sm-6 col-md-4 link_contactos px-3 m-1 py-1 fs-6 rounded shadow" href="https://wa.me/+52{{ App\Models\SiteSetting::get('whatsapp') }}?text=Me interesa conocer más sobre sus rentas" target="_blank"><i class="bi bi-whatsapp"></i>{{ App\Models\SiteSetting::get('whatsapp') }}</a>
                            <a class="col-6 col-sm-6 col-md-4 link_contactos px-3 m-1 py-1 fs-6 rounded shadow" href="{{ App\Models\SiteSetting::get('facebook') }}" target="_blank"><i class="bi bi-facebook"></i>Flash Car</a>
                            <a class="col-6 col-sm-6 col-md-4 link_contactos px-3 m-1 py-1 fs-6 rounded shadow" href="{{ App\Models\SiteSetting::get('instagram') }}" target="_blank"><i class="bi bi-instagram"></i>Flash Car</a>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-12 col-lg-6 py-3 d-flex align-items-center justify-content-center">
                        <img class="border_img_custom shadow rounded" src="{{ asset('./img/bg-parallax.jpg') }}" width="90%" alt="contacto flash car">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- TÉRMINOS Y CONDICIONES --}}
<section>
    <div class="py-5" style="background-color:#e9e9e9;">
        <div class="container">

            <div style="font-size:0.75rem; line-height:1.6; color:#222;">

                <p class="mb-2">Servicio de renta de vehículos, sin filas ni mostradores, con un proceso completamente digital diseñado para facilitar cada etapa de la experiencia. Desde la reserva hasta la devolución, todo se realiza de forma autónoma: reservas en línea, verificación remota de documentos y un proceso de entrega y devolución optimizado, sin trámites innecesarios. Nuestro modelo está pensado para que puedas rentar un vehículo de manera fácil, rápida y segura, ya sea por día, semana o mes, con atención 24/7 y acompañamiento en cada paso.</p>

                <p class="fw-bold mb-1">Seguro con cobertura amplia incluido:</p>
                <p class="mb-2">Colisión y Robo al 90%, Daños a terceros, Responsabilidad Civil, Km Ilimitado y Asistencia vial. Adicional a ello en tu primera renta tienes conductor adicional incluido.</p>

                <p class="fw-bold mb-1">Requisitos</p>
                <p class="mb-2">● INE o pasaporte vigente &nbsp;·&nbsp; ● Licencia de conducir &nbsp;·&nbsp; ● Comprobante de domicilio &nbsp;·&nbsp; ● Forma de pago</p>

                <p class="fw-bold mb-1">Misión</p>
                <p class="mb-2">Hacer que rentar un auto sea tan fácil como abrir una app: reservas digitales, verificación remota y hand off sin fricción.</p>

                <p class="fw-bold mb-1">Visión</p>
                <p class="mb-2">Ser la plataforma líder de renta autónoma en LATAM, con robots en aeropuertos clave y experiencia 24/7.</p>

                <p class="fw-bold mb-1">Términos y Condiciones</p>
                <p class="mb-2">La cobertura del seguro es válida únicamente durante el período de alquiler especificado en el contrato. El arrendatario deberá comunicar cualquier accidente, robo o daño dentro de las 24 horas. FlashCar no se hace responsable de las pertenencias personales dejadas en el vehículo. El arrendatario es responsable de cualquier infracción de tráfico o peajes incurridos durante el período de alquiler. El combustible no está incluido, el vehículo debe devolverse con el mismo nivel de combustible. Cualquier uso no autorizado del vehículo anula la cobertura del seguro. Se requiere una licencia de conducir válida y una identificación oficial para alquilar un vehículo. La asistencia en carretera está disponible las 24/7, pero es posible que no cubra áreas remotas o de alto riesgo.</p>

                <div class="pt-2 border-top mt-2">
                    <p class="fw-bold mb-1">Aviso de Confidencialidad</p>
                    <p class="mb-0">Este mensaje de correo electrónico y sus adjuntos pueden contener información confidencial o legalmente privilegiada y está destinado únicamente al uso de los destinatarios. Esta prohibido a las personas o entidades que no sean los destinatarios de este correo cualquier tipo de modificación, copia, distribución, divulgación, retención o uso de la información que contiene. La divulgación no autorizada, difusión, distribución, copia o la adopción de cualquier acción basada en la información aquí contenida, está prohibida. No puede garantizarse que los correos electrónicos estén libres de errores, ya que pueden ser interceptados, enmendados o contener virus. Flash Car no se hace responsable de errores u omisiones en este mensaje y niega cualquier responsabilidad por cualquier daño que surja del uso del correo electrónico y no se responsabiliza por su uso abusivo, contrario a la moral, a las buenas costumbres o a la ley.</p>
                </div>

            </div>

        </div>
    </div>
</section>

@if(session('success'))
    <div class="messenger_alert">
        <div class="dialog_alert messenger py-2 px-3 rounded">
            <div class="fs-6 text-white"><b>{{ session('success') }}</b><i id="close_messenger" class="m_izq bi bi-x-lg"></i></div>
        </div>
    </div>
@endif

@if(session('reserva_ok'))
<div class="modal fade" id="modalReservaOk" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded shadow">
            <div class="modal-body text-center py-5 px-4">
                <i class="bi bi-check-circle-fill text-success" style="font-size:3.5rem;"></i>
                <h4 class="fw-bold mt-3 mb-2">¡Reservación recibida!</h4>
                <p class="fs-6 text-muted mb-4">Gracias por reservar con Flash Car, un agente te contactará.</p>
                <div class="d-flex justify-content-center">
                    <button type="button" class="boton_link_xxl rounded" style="width:auto; padding:0 20px;" data-bs-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    window.addEventListener('load', function () {
        new bootstrap.Modal(document.getElementById('modalReservaOk')).show();
    });
</script>
@endif

@include('layout.burbujas')
@include('layout.footer')
<script src="{{ asset('./js/slider_principal.js') }}"></script>
<script src="{{ asset('./js/contador_mundial.js') }}"></script>
<script>
(function () {
    var el = document.getElementById('contador_visitantes');

    function actualizarContador() {
        fetch('/visitantes')
            .then(function(r){ return r.json(); })
            .then(function(d){
                if (el) el.textContent = d.count + ' ' + (d.count === 1 ? 'persona visitando ahora' : 'personas visitando ahora');
            })
            .catch(function(){});
    }

    function heartbeat() {
        fetch('/heartbeat', { keepalive: true }).catch(function(){});
    }

    function leave() {
        navigator.sendBeacon
            ? navigator.sendBeacon('/visitantes/leave')
            : fetch('/visitantes/leave', { keepalive: true }).catch(function(){});
    }

    // Carga inicial
    actualizarContador();

    // Heartbeat cada 8s para renovar antes del TTL de 10s
    setInterval(heartbeat, 8000);
    // Actualización del contador cada 8s
    setInterval(actualizarContador, 8000);

    // Notificar salida al cerrar o cambiar de pestaña
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'hidden') leave();
        if (document.visibilityState === 'visible') heartbeat();
    });
    window.addEventListener('beforeunload', leave);
})();
</script>
@endsection
