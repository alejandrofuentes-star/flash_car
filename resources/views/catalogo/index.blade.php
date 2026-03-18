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
</div>
<!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
<!--catálogo-->
<div class="py-5">
    <div class="container">
        {{-- GRID DE TARJETAS --}}
        <div id="catalogo" class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3 my-2">

            @forelse($vehicles as $vehicle)
            <div class="col">
                <div class="card h-100 shadow-sm rounded">

                    {{-- IMAGEN --}}
                    <div style="height: 160px; overflow: hidden;">
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
                        <h5 class="card-title fs-6 mb-1">{{ $vehicle->name }}</h5>

                        {{-- Marca, Modelo, Año --}}
                        <p class="card-text text-muted mb-1" style="font-size: 0.85rem;">
                            {{ $vehicle->brand }} {{ $vehicle->model }}
                            @if($vehicle->year) — {{ $vehicle->year }} @endif
                        </p>

                        {{-- Detalles --}}
                        <ul class="list-unstyled mb-2" style="font-size: 0.85rem;">
                            <li>👥 Pasajeros: <b>{{ $vehicle->passengers }}</b></li>
                            <li>⚙️ Transmisión: <b>{{ $vehicle->transmission == 'automatic' ? 'Automático' : 'Manual' }}</b></li>
                            @if($vehicle->category)
                            <li>💲 Precio/día: <b>{{ $vehicle->category->formatted_price_per_day }}</b></li>
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