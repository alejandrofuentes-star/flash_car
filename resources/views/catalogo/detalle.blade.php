@extends('layout.layouts')

@section('title', $vehicle->name . ' - Flash Car')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/styles_pagina_principal.css') }}">
@endpush

@section('content')
@include('layout.header_user')
<div class="bg-light">
    <div class="main_principal">
        <div class="space_principal">
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        <div class="container">
            <div class="col-12 d-flex align-items-center justify-content-start flex-wrap rounded cont_base my-2">

            {{-- CUERPO PRINCIPAL --}}
            <div class="col-12 d-flex align-items-start justify-content-start flex-wrap p-2">

                {{-- COLUMNA IZQUIERDA: Imagen + disponibilidad + precios --}}
                <div class="col-12 col-md-4 p-2 d-flex flex-column align-items-center">

                    {{-- Imagen --}}
                    <div class="position-relative w-100">
                        @if($vehicle->image_path)
                            <img id="imgPrincipal" src="{{ Storage::url($vehicle->image_path) }}" height="300px" alt="{{ $vehicle->name }}" width="100%" class="rounded shadow-sm" style="object-fit:contain; background:#fff;">
                        @else
                            <img id="imgPrincipal" src="{{ asset('./img/sin_url_auto.png') }}" alt="Sin imagen" height="300px" width="100%" class="rounded shadow-sm" style="object-fit:contain; background:#fff;">
                        @endif
                        <button type="button"
                            onclick="abrirModal()"
                            title="Ver en grande"
                            style="position:absolute; bottom:6px; right:6px; background:rgba(0,0,0,0.5); border:none; border-radius:6px; color:#fff; width:30px; height:30px; display:flex; align-items:center; justify-content:center; cursor:pointer;">
                            <i class="bi bi-zoom-in" style="font-size:0.9rem;"></i>
                        </button>
                    </div>

                    {{-- Miniaturas galería --}}
                    @if($vehicle->images->count() > 0)
                    <div class="d-flex flex-wrap gap-1 mt-2 w-100">
                        @foreach($vehicle->images as $image)
                        <img src="{{ Storage::url($image->image_path) }}"
                            alt="Miniatura {{ $loop->iteration }}"
                            class="rounded miniatura_galeria"
                            id="mini_{{ $loop->index }}"
                            style="width:70px; height:55px; object-fit:cover; cursor:pointer;
                                    border:3px solid {{ $loop->first ? '#f5c518' : 'transparent' }};"
                            onclick="seleccionarFoto({{ $loop->index }})">
                        @endforeach
                    </div>
                    @endif

                    {{-- Badge disponibilidad --}}
                    <div class="mt-2 w-100 text-center">
                        @if($vehicle->available)
                            <span class="badge bg-success fs-6 w-100 py-2">{{ __('detail.available') }}</span>
                        @else
                            <span class="badge bg-danger fs-6 w-100 py-2">{{ __('detail.not_available') }}</span>
                        @endif
                    </div>

                    {{-- Precios --}}
                    @if($vehicle->category)
                    <div class="col-12 mt-3 rounded border p-2 shadow-sm">
                        <p class="fs-6 m-0 mb-2"><b>{{ __('detail.prices') }}</b></p>
                        <div class="d-flex justify-content-between border-bottom py-1">
                            <span class="text-muted fs-6">{{ __('detail.per_day') }}</span>
                            <b class="fs-6">{{ $vehicle->category->formatted_price_per_day }}</b>
                        </div>
                        <div class="d-flex justify-content-between border-bottom py-1">
                            <span class="text-muted fs-6">{{ __('detail.per_week') }}</span>
                            <b class="fs-6">{{ $vehicle->category->formatted_price_per_week }}</b>
                        </div>
                        <div class="d-flex justify-content-between border-bottom py-1">
                            <span class="text-muted fs-6">{{ __('detail.per_month') }}</span>
                            <b class="fs-6">{{ $vehicle->category->formatted_price_per_month }}</b>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted fs-6">{{ __('detail.warranty') }}</span>
                            <b class="fs-6">{{ $vehicle->category->formatted_warranty }}</b>
                        </div>
                    </div>
                    @endif

                    {{-- Botón rentar --}}
                    <div class="col-12 mt-3">
                        @if($vehicle->available)
                            <a href="{{ route('reservaciones.create', $vehicle->id) }}"
                                class="boton_link_xxl rounded link_decoration_none display_flex_center_center w-100">
                                {{ __('detail.rent') }}
                            </a>
                        @else
                            <button class="boton_link_xxl rounded w-100" disabled style="opacity:0.5; cursor:not-allowed;">
                                {{ __('detail.not_available') }}
                            </button>
                        @endif
                        <div class="col-12">
                            <a class="boton_forms b_sm rounded link_decoration_none display_flex_center_center" href="{{ route('inicio') }}#catalogo">{{ __('detail.back') }}</a>
                        </div>
                    </div>
                </div>

                {{-- COLUMNA DERECHA: Info del vehículo --}}
                <div class="col-12 col-md-8 p-2">

                    {{-- Nombre, marca, modelo --}}
                    <h2 class="fs-3 mb-0"><b>{{ $vehicle->name }}</b></h2>
                    <p class="text-muted fs-6 mb-2">
                        {{ $vehicle->brand }} {{ $vehicle->model }}
                        @if($vehicle->year) — {{ $vehicle->year }} @endif
                        {{ __('detail.or_similar') }}
                    </p>

                    @if($vehicle->category)
                    <p class="fs-6 mb-3">
                        <span class="badge bg_amarillo text-dark">{{ $vehicle->category->name }}</span>
                    </p>
                    @endif

                    {{-- Características --}}
                    <div class="col-12 rounded border p-3 shadow-sm mb-3">
                        <p class="fs-6 m-0 mb-2"><b>{{ __('detail.features') }}</b></p>
                        <div class="row g-2">
                            <div class="col-6 col-md-4">
                                <div class="rounded border p-2 text-center">
                                    <p class="text-muted mb-0" style="font-size:0.75rem;">{{ __('detail.transmission') }}</p>
                                    <b class="fs-6">{{ $vehicle->transmission == 'automatic' ? __('detail.automatic') : __('detail.manual') }}</b>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="rounded border p-2 text-center">
                                    <p class="text-muted mb-0" style="font-size:0.75rem;">{{ __('detail.passengers') }}</p>
                                    <b class="fs-6">{{ $vehicle->passengers }}</b>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="rounded border p-2 text-center">
                                    <p class="text-muted mb-0" style="font-size:0.75rem;">{{ __('detail.fuel') }}</p>
                                    <b class="fs-6">{{ $vehicle->formatted_fuel_capacity }}</b>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="rounded border p-2 text-center">
                                    <p class="text-muted mb-0" style="font-size:0.75rem;">{{ __('detail.brand') }}</p>
                                    <b class="fs-6">{{ $vehicle->brand ?? '—' }}</b>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="rounded border p-2 text-center">
                                    <p class="text-muted mb-0" style="font-size:0.75rem;">{{ __('detail.model') }}</p>
                                    <b class="fs-6">{{ $vehicle->model ?? '—' }}</b>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="rounded border p-2 text-center">
                                    <p class="text-muted mb-0" style="font-size:0.75rem;">{{ __('detail.year') }}</p>
                                    <b class="fs-6">{{ $vehicle->year ?? '—' }}</b>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Incluye --}}
                    <div class="col-12 rounded border p-3 shadow-sm">
                        <p class="fs-6 m-0 mb-2"><b>{{ __('detail.includes') }}</b></p>
                        <div class="row g-0">
                            <div class="col-12 col-sm-6">
                                <ul class="list-unstyled mb-0 fs-6">
                                    <li>{{ __('detail.insurance') }}</li>
                                    <li>{{ __('detail.airport') }}</li>
                                    <li>{{ __('detail.no_lines') }}</li>
                                    <li>{{ __('detail.guarantee') }}
                                        @if($vehicle->category)
                                            {{ $vehicle->category->formatted_warranty }}
                                        @else — @endif
                                    </li>
                                    <li>{{ __('detail.collision') }}</li>
                                </ul>
                            </div>
                            <div class="col-12 col-sm-6">
                                <ul class="list-unstyled mb-0 fs-6">
                                    <li>{{ __('detail.third_party') }}</li>
                                    <li>{{ __('detail.civil_liability') }}</li>
                                    <li>{{ __('detail.unlimited_km') }}</li>
                                    <li>{{ __('detail.roadside') }}</li>
                                </ul>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
        </div>
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        </div>
    </div>
</div>
{{-- Modal lightbox --}}
<div class="modal fade" id="modalFoto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark border-0">
            <div class="modal-header border-0 p-2 d-flex justify-content-end">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-2 d-flex align-items-center justify-content-center gap-2">
                <button type="button" onclick="modalNav(-1)" id="btnModalPrev"
                    style="background:rgba(255,255,255,0.15); border:none; border-radius:6px; color:#fff; width:36px; height:36px; flex-shrink:0; display:flex; align-items:center; justify-content:center; cursor:pointer;">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <img id="imgModal" src="" alt="Foto ampliada" style="max-width:100%; max-height:75vh; object-fit:contain; flex:1;">
                <button type="button" onclick="modalNav(1)" id="btnModalNext"
                    style="background:rgba(255,255,255,0.15); border:none; border-radius:6px; color:#fff; width:36px; height:36px; flex-shrink:0; display:flex; align-items:center; justify-content:center; cursor:pointer;">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
            <div class="modal-footer border-0 p-2 justify-content-center">
                <span id="modalContador" style="color:rgba(255,255,255,0.5); font-size:0.8rem;"></span>
            </div>
        </div>
    </div>
</div>

<script>
    @if($vehicle->image_path)
    const fotoBase = "{{ Storage::url($vehicle->image_path) }}";
    @else
    const fotoBase = "{{ asset('./img/sin_url_auto.png') }}";
    @endif
    const fotosGaleria = @json($vehicle->images->map(fn($i) => Storage::url($i->image_path)));
    const todasLasFotos = fotosGaleria.length > 0 ? fotosGaleria : [fotoBase];
    let fotoSeleccionada = 0;
    let fotoModal = 0;

    function seleccionarFoto(index) {
        fotoSeleccionada = index;
        document.getElementById('imgPrincipal').src = todasLasFotos[index];
        document.querySelectorAll('.miniatura_galeria').forEach((m, i) => {
            m.style.border = i === index ? '3px solid #f5c518' : '3px solid transparent';
        });
    }

    function abrirModal() {
        fotoModal = fotoSeleccionada;
        actualizarModal();
        new bootstrap.Modal(document.getElementById('modalFoto')).show();
    }

    function modalNav(dir) {
        fotoModal = (fotoModal + dir + todasLasFotos.length) % todasLasFotos.length;
        actualizarModal();
    }

    function actualizarModal() {
        document.getElementById('imgModal').src = todasLasFotos[fotoModal];
        document.getElementById('modalContador').textContent = todasLasFotos.length > 1
            ? (fotoModal + 1) + ' / ' + todasLasFotos.length : '';
        document.getElementById('btnModalPrev').style.visibility = todasLasFotos.length > 1 ? 'visible' : 'hidden';
        document.getElementById('btnModalNext').style.visibility = todasLasFotos.length > 1 ? 'visible' : 'hidden';
    }

    document.getElementById('modalFoto').addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft') modalNav(-1);
        if (e.key === 'ArrowRight') modalNav(1);
    });
</script>
@include('layout.footer')
@endsection
