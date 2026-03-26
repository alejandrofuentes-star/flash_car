@extends('layout.layouts')

@section('title', $vehicle->name . ' - Flash Car')

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
                    @if($vehicle->image_path)
                        <img id="imgPrincipal" src="{{ Storage::url($vehicle->image_path) }}" height="150px" alt="{{ $vehicle->name }}" width="100%" class="rounded shadow-sm">
                    @else
                        <img src="{{ asset('./img/sin_url_auto.png') }}" alt="Sin imagen" width="100%" class="rounded shadow-sm">
                    @endif

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
                            <span class="badge bg-success fs-6 w-100 py-2">Disponible</span>
                        @else
                            <span class="badge bg-danger fs-6 w-100 py-2">No disponible</span>
                        @endif
                    </div>

                    {{-- Precios --}}
                    @if($vehicle->category)
                    <div class="col-12 mt-3 rounded border p-2 shadow-sm">
                        <p class="fs-6 m-0 mb-2"><b>Precios</b></p>
                        <div class="d-flex justify-content-between border-bottom py-1">
                            <span class="text-muted fs-6">Por día</span>
                            <b class="fs-6">{{ $vehicle->category->formatted_price_per_day }}</b>
                        </div>
                        <div class="d-flex justify-content-between border-bottom py-1">
                            <span class="text-muted fs-6">Por semana</span>
                            <b class="fs-6">{{ $vehicle->category->formatted_price_per_week }}</b>
                        </div>
                        <div class="d-flex justify-content-between border-bottom py-1">
                            <span class="text-muted fs-6">Por mes</span>
                            <b class="fs-6">{{ $vehicle->category->formatted_price_per_month }}</b>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted fs-6">Garantía</span>
                            <b class="fs-6">{{ $vehicle->category->formatted_warranty }}</b>
                        </div>
                    </div>
                    @endif

                    {{-- Botón rentar --}}
                    <div class="col-12 mt-3">
                        @if($vehicle->available)
                            <a href="{{ route('reservaciones.create', $vehicle->id) }}"
                                class="boton_link_xxl rounded link_decoration_none display_flex_center_center w-100">
                                Rentar vehículo
                            </a>
                        @else
                            <button class="boton_link_xxl rounded w-100" disabled style="opacity:0.5; cursor:not-allowed;">
                                No disponible
                            </button>
                        @endif
                        <div class="col-12">
                            <a class="boton_forms b_sm rounded link_decoration_none display_flex_center_center" href="{{ route('inicio') }}#catalogo">← Volver</a>
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
                        — o similar
                    </p>

                    @if($vehicle->category)
                    <p class="fs-6 mb-3">
                        <span class="badge bg_amarillo text-dark">{{ $vehicle->category->name }}</span>
                    </p>
                    @endif

                    {{-- Características --}}
                    <div class="col-12 rounded border p-3 shadow-sm mb-3">
                        <p class="fs-6 m-0 mb-2"><b>Características</b></p>
                        <div class="row g-2">
                            <div class="col-6 col-md-4">
                                <div class="rounded border p-2 text-center">
                                    <p class="text-muted mb-0" style="font-size:0.75rem;">TRANSMISIÓN</p>
                                    <b class="fs-6">{{ $vehicle->transmission == 'automatic' ? 'Automático' : 'Manual' }}</b>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="rounded border p-2 text-center">
                                    <p class="text-muted mb-0" style="font-size:0.75rem;">PASAJEROS</p>
                                    <b class="fs-6">{{ $vehicle->passengers }}</b>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="rounded border p-2 text-center">
                                    <p class="text-muted mb-0" style="font-size:0.75rem;">COMBUSTIBLE</p>
                                    <b class="fs-6">{{ $vehicle->formatted_fuel_capacity }}</b>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="rounded border p-2 text-center">
                                    <p class="text-muted mb-0" style="font-size:0.75rem;">MARCA</p>
                                    <b class="fs-6">{{ $vehicle->brand ?? '—' }}</b>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="rounded border p-2 text-center">
                                    <p class="text-muted mb-0" style="font-size:0.75rem;">MODELO</p>
                                    <b class="fs-6">{{ $vehicle->model ?? '—' }}</b>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="rounded border p-2 text-center">
                                    <p class="text-muted mb-0" style="font-size:0.75rem;">AÑO</p>
                                    <b class="fs-6">{{ $vehicle->year ?? '—' }}</b>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Incluye --}}
                    <div class="col-12 rounded border p-3 shadow-sm">
                        <p class="fs-6 m-0 mb-2"><b>Incluye</b></p>
                        <ul class="list-unstyled mb-0 fs-6">
                            <li>✔ Seguro incluido</li>
                            <li>✔ Entrega en aeropuerto</li>
                            <li>✔ Sin filas</li>
                            <li>✔ Garantía: 
                                @if($vehicle->category)
                                    {{ $vehicle->category->formatted_warranty }}
                                @else — @endif
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
        </div>
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        </div>
    </div>
</div>
<script>
    const fotosGaleria = @json($vehicle->images->map(fn($i) => Storage::url($i->image_path)));
let fotoSeleccionada = 0;

function seleccionarFoto(index) {
    fotoSeleccionada = index;
    document.getElementById('imgPrincipal').src = fotosGaleria[index];
    document.querySelectorAll('.miniatura_galeria').forEach((m, i) => {
        m.style.border = i === index ? '3px solid #f5c518' : '3px solid transparent';
    });
}
</script>
@include('layout.footer')
@endsection