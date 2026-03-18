@extends('layout.layouts')

@section('title', 'Rentar - ' . $vehicle->name)

@section('content')
@include('layout.header_user')
<div class="bg-light">
    <div class="main_principal">
        <div class="space_principal">
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        <div class="container">

            <div class="col-12 d-flex align-items-center justify-content-start flex-wrap rounded cont_base my-2">

                <div class="col-12 d-flex align-items-start justify-content-center flex-column p-2 bg_gris_8">
                    <h1 class="fs-6 text_uppcase">Solicitud de Renta: {{ $vehicle->name }}</h1>
                    <div class="col-4 col-sm-4 col-md-2 col-lg-1">
                        <a class="boton_forms b_sm rounded link_decoration_none display_flex_center_center" href="{{ route('catalogo.detalle', $vehicle->id) }}">← Volver</a>
                    </div>
                </div>

                {{-- Info del vehículo --}}
                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start p-2">
                    <p class="text-dark fs-6 m-0"><b>Vehículo Seleccionado</b></p>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-2">
                    <div class="col-12 col-md-3 p-1">
                        @if($vehicle->image_path)
                            <img src="{{ Storage::url($vehicle->image_path) }}" alt="{{ $vehicle->name }}" width="100%" class="rounded">
                        @else
                            <img src="{{ asset('./img/sin_url_auto.png') }}" alt="Sin imagen" width="100%" class="rounded">
                        @endif
                    </div>
                    <div class="col-12 col-md-9 d-flex align-items-center justify-content-start flex-wrap p-1">
                        <div class="col-6 fila_form_f_b py-2">
                            <span class="label_form_f_b fs-6 p-1"><b>Vehículo</b></span>
                            <span class="input_form_f_b fs-6 p-1">{{ $vehicle->name }}</span>
                        </div>
                        <div class="col-6 fila_form_f_b py-2">
                            <span class="label_form_f_b fs-6 p-1"><b>Categoría</b></span>
                            <span class="input_form_f_b fs-6 p-1">{{ $vehicle->category->name ?? '—' }}</span>
                        </div>
                        <div class="col-6 fila_form_f_b py-2">
                            <span class="label_form_f_b fs-6 p-1"><b>Precio por día</b></span>
                            <span class="input_form_f_b fs-6 p-1">{{ $vehicle->category->formatted_price_per_day ?? '—' }}</span>
                        </div>
                        <div class="col-6 fila_form_f_b py-2">
                            <span class="label_form_f_b fs-6 p-1"><b>Transmisión</b></span>
                            <span class="input_form_f_b fs-6 p-1">{{ $vehicle->transmission == 'automatic' ? 'Automático' : 'Manual' }}</span>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('rentas.store') }}" class="col-12 d-flex align-items-center justify-content-start flex-wrap">
                @csrf
                <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
                <input type="hidden" name="total_dias" id="total_dias" value="0">
                <input type="hidden" name="costo_total" id="costo_total_input" value="0">
                <input type="hidden" id="precio_dia" value="{{ $vehicle->category->price_per_day ?? 0 }}">

                {{-- Datos del cliente --}}
                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start p-2">
                    <p class="text-dark fs-6 m-0"><b>Datos del Cliente</b></p>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-1">
                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Nombre Completo *</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" name="nombre_completo" value="{{ old('nombre_completo') }}" placeholder="Ej: Juan García López" required>
                    </div>
                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Teléfono *</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="tel" name="telefono" value="{{ old('telefono') }}" placeholder="Ej: 5512345678" required>
                    </div>
                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Correo Electrónico *</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="email" name="correo" value="{{ old('correo') }}" placeholder="Ej: correo@ejemplo.com" required>
                    </div>
                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Ciudad *</b></label>
                        <select class="input_form_f_b fs-6 p-1" name="ciudad" required>
                            <option value="">-- Selecciona una ciudad --</option>
                            <option value="CDMX" {{ old('ciudad') == 'CDMX' ? 'selected' : '' }}>CDMX</option>
                            <option value="Puebla" {{ old('ciudad') == 'Puebla' ? 'selected' : '' }}>Puebla</option>
                            <option value="Querétaro" {{ old('ciudad') == 'Querétaro' ? 'selected' : '' }}>Querétaro</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>No. de Pasajeros *</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="number" name="num_pasajeros" value="{{ old('num_pasajeros') }}" min="1" max="{{ $vehicle->passengers }}" placeholder="Máx: {{ $vehicle->passengers }}" required>
                    </div>
                </div>

                {{-- Entrega --}}
                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start p-2">
                    <p class="text-dark fs-6 m-0"><b>Entrega del Vehículo</b></p>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-1">
                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Fecha de Entrega *</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="date" id="fecha_entrega" name="fecha_entrega" value="{{ old('fecha_entrega') }}" min="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Hora de Entrega *</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="time" name="hora_entrega" value="{{ old('hora_entrega') }}" required>
                    </div>
                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Lugar de Entrega *</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" name="lugar_entrega" value="{{ old('lugar_entrega') }}" placeholder="Ej: Aeropuerto CDMX" required>
                    </div>
                </div>

                {{-- Devolución --}}
                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start p-2">
                    <p class="text-dark fs-6 m-0"><b>Devolución del Vehículo</b></p>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-1">
                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Fecha de Devolución *</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="date" id="fecha_devolucion" name="fecha_devolucion" value="{{ old('fecha_devolucion') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                    </div>
                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Hora de Devolución *</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="time" name="hora_devolucion" value="{{ old('hora_devolucion') }}" required>
                    </div>
                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Lugar de Devolución *</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" name="lugar_devolucion" value="{{ old('lugar_devolucion') }}" placeholder="Ej: Oficina Puebla Centro" required>
                    </div>
                </div>

                {{-- Resumen de costo --}}
                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start p-2">
                    <p class="text-dark fs-6 m-0"><b>Resumen de Costo</b></p>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-2">
                    <div class="col-6 col-md-3 fila_form_f_b py-2">
                        <span class="label_form_f_b fs-6 p-1"><b>Total de días</b></span>
                        <span class="input_form_f_b fs-6 p-1" id="resumen_dias">— días</span>
                    </div>
                    <div class="col-6 col-md-3 fila_form_f_b py-2">
                        <span class="label_form_f_b fs-6 p-1"><b>Precio por día</b></span>
                        <span class="input_form_f_b fs-6 p-1">${{ number_format($vehicle->category->price_per_day ?? 0, 2) }}</span>
                    </div>
                    <div class="col-12 col-md-3 fila_form_f_b py-2">
                        <span class="label_form_f_b fs-6 p-1"><b>Costo Total</b></span>
                        <span class="input_form_f_b fs-6 p-1" id="resumen_costo" style="font-weight:700; color:var(--primary);">$0.00</span>
                    </div>
                </div>

                {{-- Botones --}}
                <div class="col-12 py-3 d-flex align-items-center justify-content-center">
                    <a href="{{ route('catalogo.detalle', $vehicle->id) }}" class="boton_link_lg rounded">Cancelar</a>
                    <button type="submit" class="boton_link_xxl rounded">Enviar Solicitud</button>
                </div>

                </form>

                @if($errors->any())
                    <div class="messenger_alert">
                        <div class="dialog_alert danger py-2 px-4 rounded">
                            @foreach($errors->all() as $error)
                                <div class="fs-6 text-white"><b>{{ $error }}</b></div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </div>
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        </div>
    </div>
</div>
@include('layout.footer')
<script src="{{ asset('js/formulario_renta.js') }}"></script>
@endsection