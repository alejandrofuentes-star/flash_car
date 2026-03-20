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
                <div class="col-12 d-flex align-items-start justify-content-center flex-column p-2 bg_gris_8">
                    <h1 class="fs-6 text_uppcase">{{ $vehicle->name }}</h1>
                    <div class="col-4 col-sm-4 col-md-2 col-lg-1">
                        <a class="boton_forms b_sm rounded link_decoration_none display_flex_center_center" href="{{ route('inicio') }}">← Volver</a>
                    </div>
                </div>
                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start p-2">
                    <p class="text-dark fs-6 m-0"><b>Información del Vehículo</b></p>
                </div>
                <div class="col-12 d-flex align-items-start justify-content-start flex-wrap p-2">
                    {{-- Imagen --}}
                    <div class="col-12 col-md-4 p-1">
                        @if($vehicle->image_path)
                            <img src="{{ Storage::url($vehicle->image_path) }}" alt="{{ $vehicle->name }}" width="100%" class="rounded">
                        @else
                            <img src="{{ asset('./img/sin_url_auto.png') }}" alt="Sin imagen" width="100%" class="rounded">
                        @endif
                        <div class="mt-2 text-center">
                            @if($vehicle->available)
                                <span class="badge bg-success fs-6">Disponible</span>
                            @else
                                <span class="badge bg-danger fs-6">No disponible</span>
                            @endif
                        </div>
                    </div>
                    {{-- Detalles --}}
                    <div class="col-12 col-md-8 d-flex align-items-start justify-content-start flex-wrap p-1">
                        <div class="col-6 col-sm-6 col-md-4 fila_form_f_b py-2">
                            <span class="label_form_f_b fs-6 p-1"><b>Marca</b></span>
                            <span class="input_form_f_b fs-6 p-1">{{ $vehicle->brand ?? '—' }}</span>
                        </div>
                        <div class="col-6 col-sm-6 col-md-4 fila_form_f_b py-2">
                            <span class="label_form_f_b fs-6 p-1"><b>Modelo</b></span>
                            <span class="input_form_f_b fs-6 p-1">{{ $vehicle->model ?? '—' }}</span>
                        </div>
                        <div class="col-6 col-sm-6 col-md-4 fila_form_f_b py-2">
                            <span class="label_form_f_b fs-6 p-1"><b>Año</b></span>
                            <span class="input_form_f_b fs-6 p-1">{{ $vehicle->year ?? '—' }}</span>
                        </div>
                        <div class="col-6 col-sm-6 col-md-4 fila_form_f_b py-2">
                            <span class="label_form_f_b fs-6 p-1"><b>Transmisión</b></span>
                            <span class="input_form_f_b fs-6 p-1">{{ $vehicle->transmission == 'automatic' ? 'Automático' : 'Manual' }}</span>
                        </div>
                        <div class="col-6 col-sm-6 col-md-4 fila_form_f_b py-2">
                            <span class="label_form_f_b fs-6 p-1"><b>Pasajeros</b></span>
                            <span class="input_form_f_b fs-6 p-1">{{ $vehicle->passengers }}</span>
                        </div>
                        <div class="col-6 col-sm-6 col-md-4 fila_form_f_b py-2">
                            <span class="label_form_f_b fs-6 p-1"><b>Combustible</b></span>
                            <span class="input_form_f_b fs-6 p-1">{{ $vehicle->formatted_fuel_capacity }}</span>
                        </div>
                    </div>
                </div>
                @if($vehicle->category)
                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start p-2 mb-1 mt-2">
                    <p class="text-dark fs-6 m-0"><b>Precios</b></p>
                </div>
                <div class="col-6 col-sm-6 col-md-3 fila_form_f_b py-2">
                    <span class="label_form_f_b fs-6 p-1"><b>Por día</b></span>
                    <span class="input_form_f_b fs-6 p-1">{{ $vehicle->category->formatted_price_per_day }}</span>
                </div>
                <div class="col-6 col-sm-6 col-md-3 fila_form_f_b py-2">
                    <span class="label_form_f_b fs-6 p-1"><b>Por semana</b></span>
                    <span class="input_form_f_b fs-6 p-1">{{ $vehicle->category->formatted_price_per_week }}</span>
                </div>
                <div class="col-6 col-sm-6 col-md-3 fila_form_f_b py-2">
                    <span class="label_form_f_b fs-6 p-1"><b>Por mes</b></span>
                    <span class="input_form_f_b fs-6 p-1">{{ $vehicle->category->formatted_price_per_month }}</span>
                </div>
                <div class="col-6 col-sm-6 col-md-3 fila_form_f_b py-2">
                    <span class="label_form_f_b fs-6 p-1"><b>Garantía</b></span>
                    <span class="input_form_f_b fs-6 p-1">{{ $vehicle->category->formatted_warranty }}</span>
                </div>
                @endif
                {{-- Botón rentar --}}
                <div class="col-12 py-3 d-flex align-items-center justify-content-center">
                    @if($vehicle->available)
                        <a href="{{ route('reservaciones.create', $vehicle->id) }}" class="boton_link_xxl rounded link_decoration_none display_flex_center_center">
                            Rentar vehículo
                        </a>
                    @else
                        <button class="boton_link_xxl rounded" disabled style="opacity:0.5; cursor:not-allowed;">
                            No disponible
                        </button>
                    @endif
                </div>
            </div>
        </div>
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        </div>
    </div>
</div>
@include('layout.footer')
@endsection