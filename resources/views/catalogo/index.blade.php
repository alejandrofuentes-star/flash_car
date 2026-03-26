@extends('layout.layouts')

@section('title', 'Flash Car')

@section('content')
@include('layout.header_user')
<!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
<!--inicio-->
<div class="col-12 d-flex align-items-center justify-content-center" id="inicio">
    <div class="slider_container">

        {{-- IMÁGENES ESCRITORIO (ocultas en móvil) --}}
        <div class="slider_track d-none d-md-block">
            <div class="slide active">
                <img src="{{ asset('./img/slider/desktop_1.jpg') }}" alt="Slide 1">
            </div>
            <div class="slide">
                <img src="{{ asset('./img/slider/desktop_2.jpg') }}" alt="Slide 2">
            </div>
            <div class="slide">
                <img src="{{ asset('./img/slider/desktop_3.jpg') }}" alt="Slide 3">
            </div>
            <div class="slide">
                <img src="{{ asset('./img/slider/desktop_4.jpg') }}" alt="Slide 4">
            </div>
        </div>

        {{-- IMÁGENES MÓVIL (ocultas en escritorio) --}}
        <div class="slider_track d-block d-md-none">
            <div class="slide active">
                <img src="{{ asset('./img/slider/mobile_1.jpg') }}" alt="Slide 1">
            </div>
            <div class="slide">
                <img src="{{ asset('./img/slider/mobile_2.jpg') }}" alt="Slide 2">
            </div>
            <div class="slide">
                <img src="{{ asset('./img/slider/mobile_3.jpg') }}" alt="Slide 3">
            </div>
            <div class="slide">
                <img src="{{ asset('./img/slider/mobile_4.jpg') }}" alt="Slide 4">
            </div>
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
            <div class="col-12 col-sm-12 col-md-12 col-lg-5 d-flex align-items-center justify-content-center flex-column p-3">
                <div class="col-11 col-sm-11 col-md-8 rounded bg_negro_opa_70 blur_5 p-3">
                    <h1 class="text-white fs-2">Renta de autos en minutos</h1>
                    <p class="text-white m-0 fs-6 pb-3">Entrega en aeropuerto - Seguro incluido - Sin filas</p>
                    <form class="col-12" action="{{ route('catalogo.buscar') }}#catalogo" method="GET">
                        <div class="col-12 d-flex align-items-center justify-content-center p-1">
                            <select class="w_100 input_busqueda rounded px-2" name="city" id="">
                                <option value="">Seleccione Ciudad</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city }}">{{ $city }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 d-flex align-items-center justify-content-center">
                            <div class="col-6 p-1">
                                <input class="w_100 input_busqueda rounded px-2" type="date" name="fecha_entrega" placeholder="Fecha entrega">
                            </div>
                            <div class="col-6 p-1">
                                <input class="w_100 input_busqueda rounded px-2" type="date" name="fecha_devolucion" placeholder="Fecha devolución">
                            </div>
                        </div>
                        <div class="col-12 d-flex align-items-center justify-content-center p-1">
                            <button type="submit" class="boton_clientes_principal my-3">
                                <div class="boton_cliente_interno">
                                    <div class="animacion"></div>
                                </div>
                                <div class="boton_cliente_texto">
                                    Buscar
                                </div>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-7 d-flex align-items-center justify-content-center flex-column">
                
            </div>
        </div>
    </div>
</div>
<!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
<!--catálogo-->
<div class="py-5 bg-light" id="catalogo">
    <div class="container">
        <div class="col-12 d-flex align-items-center justify-content-center flex-column">
            <h2 class="fs-1"><b>Elije tu auto ideal</b></h2>
            <div class="col-12 d-flex align-items-center justify-content-center py-2">
                <select class="shadow-sm input_busqueda w_200_px rounded px-2 m-1" id="filtro_category">
                    <option value="">Categoría</option>
                    @foreach($categories as $category)
                        <option value="{{ strtolower($category->name) }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                <select class="shadow-sm input_busqueda w_200_px rounded px-2 m-1" id="filtro_transmission">
                    <option value="">Transmisión</option>
                    <option value="manual">Manual</option>
                    <option value="automatic">Automatico</option>
                </select>
            </div>
        </div>
        {{-- GRID DE TARJETAS --}}
        <div id="catalogo_grid" class="row row-cols-1 row-cols-sm-2 row-cols-md-2 row-cols-lg-4 g-3 my-2">

            @forelse($vehicles as $vehicle)
            <div class="col tarjeta_vehiculo" data-category="{{ strtolower($vehicle->category->name ?? '') }}" data-transmission="{{ $vehicle->transmission }}">
                
                <div class="card h-100 shadow-sm rounded">

                    {{-- IMAGEN --}}
                    <div style="height: auto; overflow: hidden;">
                        @if($vehicle->image_path)
                            <img src="{{ Storage::url($vehicle->image_path) }}"
                                alt="{{ $vehicle->name }}"
                                class="card-img-top"
                                style="width:100%; height:auto;">
                        @else
                            <img src="{{ asset('./img/sin_url_auto.png') }}"
                                alt="Sin imagen"
                                class="card-img-top"
                                style="width:100%; height:auto;">
                        @endif
                    </div>

                    {{-- CONTENIDO --}}
                    <div class="card-body d-flex flex-column">
                        
                        {{-- Disponibilidad --}}
                        <div class="mb-1">
                            @if($vehicle->available)
                                <span class="badge bg-success">Disponible</span>
                            @else
                                <span class="badge bg-danger">No disponible</span>
                            @endif
                        </div>
                        
                        {{-- Nombre --}}
                        <h5 class="card-title fs-4 mb-1">{{ $vehicle->name }}</h5>
                        
                        {{-- Marca, Modelo, Año --}}
                        <p class="card-text text-muted mb-1 fs-6" style="font-size: 0.85rem;">
                            {{ $vehicle->brand }} {{ $vehicle->model }}
                            @if($vehicle->year) — {{ $vehicle->year }} @endif
                        </p>
                        <p class="fs-6 m-0">o similar</p>
                        
                        {{-- Detalles --}}
                        <ul class="list-unstyled mb-2" style="font-size: 0.85rem;">
                            <li class="fs-6"><i class="icon_target bi bi-tag-fill"></i> Categoría: <b>{{ strtolower($vehicle->category->name ?? '') }}</b></li>
                            <li data-transmission="{{ $vehicle->transmission }}" class="fs-6"><i class="icon_target bi bi-gear-wide"></i> Transmisión: <b>{{ $vehicle->transmission == 'automatic' ? 'Automático' : 'Manual' }}</b></li>
                            @if($vehicle->category)
                            <li><b class="fs-4">{{ $vehicle->category->formatted_price_per_day }} <span class="fs-6"> /día</span></b></li>
                            @endif
                        </ul>
                        {{-- Botón --}}
                        <div class="mt-auto">
                            <a href="{{ route('catalogo.detalle', $vehicle->id) }}"
                                class="boton_forms rounded link_decoration_none display_flex_center_center">
                                Ver más detalles
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted fs-5">No hay vehículos disponibles en este momento.</p>
            </div>
            @endforelse
        </div>
        <div id="contacto"></div>
    </div>
</div>
@if(session('success'))
    <div class="messenger_alert">
        <div class="dialog_alert messenger py-2 px-3 rounded">
            <div class="fs-6 text-white"><b>{{ session('success') }}</b><i id="close_messenger" class="m_izq bi bi-x-lg"></i></div>
        </div>
    </div>
@endif
@include('layout.footer')
<script src="{{ asset('./js/slider_principal.js') }}"></script>
@endsection