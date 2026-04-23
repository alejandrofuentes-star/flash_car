@extends('layout.layouts')

@section('title', 'Flash Car')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/styles_pagina_principal.css') }}">
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
            <div class="col-12 col-sm-12 col-md-12 col-lg-5 p-3">
                <div class="hero_form_card">
                    <h1 class="hero_titulo">{{ __('hero.title') }}</h1>
                    <p class="hero_subtitulo">{{ __('hero.subtitle') }}</p>
                    <form action="{{ route('catalogo.buscar') }}#catalogo" method="GET">
                        <div class="hero_input_wrap mb-2">
                            <i class="bi bi-geo-alt-fill hero_input_icon"></i>
                            <select class="hero_input" name="city">
                                <option value="">{{ __('hero.city') }}</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city }}">{{ $city }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex gap-2 mb-2">
                            <div class="hero_input_wrap flex-1">
                                <i class="bi bi-calendar-event hero_input_icon"></i>
                                <input class="hero_input" type="date" name="fecha_entrega">
                            </div>
                            <div class="hero_input_wrap flex-1">
                                <i class="bi bi-calendar-check hero_input_icon"></i>
                                <input class="hero_input" type="date" name="fecha_devolucion">
                            </div>
                        </div>
                        <button type="submit" class="hero_btn">
                            <i class="bi bi-search me-2"></i> {{ __('hero.search') }}
                        </button>
                    </form>
                    <div class="hero_badges">
                        <span class="hero_badge"><i class="bi bi-airplane-fill"></i> {{ __('hero.airport') }}</span>
                        <span class="hero_badge"><i class="bi bi-shield-check-fill"></i> {{ __('hero.insurance') }}</span>
                        <span class="hero_badge"><i class="bi bi-lightning-charge-fill"></i> {{ __('hero.no_lines') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-7"></div>
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
            <select class="shadow-sm input_busqueda rounded px-2" style="height:40px; min-width:160px;" id="filtro_transmission">
                <option value="">{{ __('catalog.transmission') }}</option>
                <option value="manual">{{ __('catalog.manual') }}</option>
                <option value="automatic">{{ __('catalog.automatic') }}</option>
            </select>
        </div>

        {{-- GRID DE TARJETAS HORIZONTAL --}}
        <div id="catalogo_grid" class="row row-cols-1 row-cols-md-2 g-3 my-1">

            @forelse($vehicles as $vehicle)
            <div class="col tarjeta_vehiculo" data-category="{{ strtolower($vehicle->category->name ?? '') }}" data-transmission="{{ $vehicle->transmission }}">
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

<section>
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
                            <a class="col-6 col-sm-6 col-md-4 link_contactos px-3 m-1 py-1 fs-6 rounded shadow" href=""><i class="bi bi-telephone-fill"></i>1122334455</a>
                            <a class="col-6 col-sm-6 col-md-4 link_contactos px-3 m-1 py-1 fs-6 rounded shadow" href=""><i class="bi bi-whatsapp"></i>1122334455</a>
                            <a class="col-6 col-sm-6 col-md-4 link_contactos px-3 m-1 py-1 fs-6 rounded shadow" href=""><i class="bi bi-facebook"></i>Flash Car</a>
                            <a class="col-6 col-sm-6 col-md-4 link_contactos px-3 m-1 py-1 fs-6 rounded shadow" href=""><i class="bi bi-instagram"></i>Flash Car</a>
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

@if(session('success'))
    <div class="messenger_alert">
        <div class="dialog_alert messenger py-2 px-3 rounded">
            <div class="fs-6 text-white"><b>{{ session('success') }}</b><i id="close_messenger" class="m_izq bi bi-x-lg"></i></div>
        </div>
    </div>
@endif
@include('layout.footer')
<script src="{{ asset('./js/slider_principal.js') }}"></script>
<script src="{{ asset('./js/contador_mundial.js') }}"></script>
@endsection
