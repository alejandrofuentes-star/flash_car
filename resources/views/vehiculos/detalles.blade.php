@extends('layout.layouts')

@section('title', 'Detalles Vehículo - Flash Car')

@section('content')
 @include('layout.header')
<div class="bg-light">
    <div class="main_principal">
        <div class="space_principal">
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        <div class="container">
            <div class="col-12 d-flex align-items-center justify-content-start flex-wrap rounded cont_base my-2">
                <div class="col-12 d-flex align-items-start justify-content-center flex-column p-2 bg_gris_8">
                    <h1 class="fs-6 text_uppcase">Detalles del Vehículo</h1>
                    <div class="col-4 col-sm-4 col-md-2 col-lg-1">
                        <a class="boton_forms b_sm rounded link_decoration_none display_flex_center_center" href="{{ route('vehiculos.index') }}">← Volver</a>
                    </div>
                </div>

                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start flex-wrap p-2">
                    <p class="text-dark fs-6 m-0"><b>Información del Vehículo</b></p>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-1">
                    <div class="col-12 col-sm-12 col-md-4 col-lg-3 d-flex align-items-center justify-content-center">
                        @if($vehicle->image_path)
                            <img src="{{ Storage::url($vehicle->image_path) }}" alt="{{ $vehicle->name }}" width="100%">
                        @else
                            <img src="{{ asset('./img/sin_url_auto.png') }}" alt="Sin imagen" width="100%">
                        @endif
                    </div>
                    <div class="col-12 col-sm-12 col-md-8 col-lg-9 d-flex align-items-center justify-content-center flex-wrap">
                        <div class="col-12 col-sm-12 col-md-6 fila_form_f_b py-2">
                            <label class="label_form_f_b fs-6 p-1"><b>Id</b></label>
                            <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $vehicle->id }}" readonly>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 fila_form_f_b py-2">
                            <label class="label_form_f_b fs-6 p-1"><b>Nombre del Vehículo</b></label>
                            <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $vehicle->name }}" readonly>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 fila_form_f_b py-2">
                            <label class="label_form_f_b fs-6 p-1"><b>Categoría</b></label>
                            @if($vehicle->category)
                                <p class="fs-6 m-0 text-center categoria_etiqueta">{{ $vehicle->category->name }}</p>
                            @else
                                <p class="fs-6 m-0 text-center sin_categoria_etiqueta">Sin categoría</p>
                            @endif
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 fila_form_f_b py-2">
                            <label class="label_form_f_b fs-6 p-1"><b>Número de Pasajeros</b></label>
                            <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $vehicle->passengers }}" readonly>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 fila_form_f_b py-2">
                            <label class="label_form_f_b fs-6 p-1"><b>Capacidad de Combustible</b></label>
                            <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $vehicle->formatted_fuel_capacity }}" readonly>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 fila_form_f_b py-2">
                            <label class="label_form_f_b fs-6 p-1"><b>Transmisión</b></label>
                            <span class="fs-6">{{ $vehicle->transmission === 'automatic' ? 'Automático' : 'Manual' }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start flex-wrap p-2">
                    <p class="text-dark fs-6 m-0"><b>Especificaciones</b></p>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-1">
                    <div class="col-6 col-sm-6 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Marca</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $vehicle->brand ?? 'No especificado' }}" readonly>
                    </div>
                    <div class="col-6 col-sm-6 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Modelo</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $vehicle->model ?? 'No especificado' }}" readonly>
                    </div>
                    <div class="col-6 col-sm-6 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Año</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $vehicle->year ?? 'No especificado' }}" readonly>
                    </div>
                    <div class="col-6 col-sm-6 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Número de Placa</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $vehicle->plate_number ?? 'No especificado' }}" readonly>
                    </div>
                    <div class="col-6 col-sm-6 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Ciudades</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $vehicle->states->pluck('name')->join(', ') ?: 'No especificado' }}" readonly>
                    </div>
                    <div class="col-6 col-sm-6 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Kilometraje</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $vehicle->mileage ? number_format($vehicle->mileage) . ' km' : 'No especificado' }}" readonly>
                    </div>
                    <div class="col-6 col-sm-6 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Próxima Verificación</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $vehicle->next_verification ? \Carbon\Carbon::parse($vehicle->next_verification)->format('d/m/Y') : 'No especificado' }}" readonly>
                    </div>
                    <div class="col-6 col-sm-6 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Estado de Disponibilidad</b></label>
                        @if($vehicle->available)
                            <span class="cuenta_activa">Disponible para rentar</span>
                        @else
                            <span class="cuenta_inactiva">No disponible</span>
                        @endif
                    </div>
                    <div class="col-6 col-sm-6 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Estado del Sistema</b></label>
                        @if($vehicle->active)
                            <span class="cuenta_activa">Activo</span>
                        @else
                            <span class="cuenta_inactiva">Inactivo</span>
                        @endif
                    </div>
                </div>

                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start flex-wrap p-2">
                    <p class="text-dark fs-6 m-0"><b>Información de Precios</b></p>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-1">
                    @if($vehicle->category)
                    <div class="col-12 col-sm-6 col-md-3 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Precio por Día</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $vehicle->category->formatted_price_per_day }}" readonly>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Precio por Semana</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $vehicle->category->formatted_price_per_week }}" readonly>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Precio por Mes</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $vehicle->category->formatted_price_per_month }}" readonly>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Garantía</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $vehicle->category->formatted_warranty }}" readonly>
                    </div>
                    @endif
                </div>

                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start flex-wrap p-2">
                    <p class="text-dark fs-6 m-0"><b>Información de Registro</b></p>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-1">
                    <div class="col-6 col-sm-6 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Fecha de Creación</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $vehicle->created_at->format('d/m/Y H:i:s') }}" readonly>
                    </div>
                    <div class="col-6 col-sm-6 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Última Actualización</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $vehicle->updated_at->format('d/m/Y H:i:s') }}" readonly>
                    </div>
                </div>

                <div class="col-12 py-2 d-flex align-items-center justify-content-center">
                    @if(Auth::user()->hasAdminAccess())
                    <a class="boton_link_md rounded" href="{{ route('vehiculos.edit', $vehicle->id) }}">Editar</a>
                    <form method="POST" action="{{ route('vehiculos.destroy', $vehicle->id) }}" onsubmit="return confirm('¿Estás seguro de eliminar este vehículo?\n\nEsta acción no se puede deshacer.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="boton_link_lg bg_rojo_2 rounded">Eliminar</button>
                    </form>
                    @endif
                </div>

                {{-- GALERÍA DE FOTOS --}}
                @if($vehicle->images->count() > 0)
                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start flex-wrap p-2">
                    <p class="text-dark fs-6 m-0"><b>Galería de Fotos</b></p>
                    <small class="text-dark ms-2">({{ $vehicle->images->count() }} foto(s))</small>
                </div>
                <div class="col-12 d-flex align-items-start justify-content-start flex-wrap p-2">
                    @foreach($vehicle->images as $image)
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2 p-1">
                        <img src="{{ Storage::url($image->image_path) }}"
                            alt="Foto galería"
                            width="100%"
                            class="rounded shadow-sm"
                            style="cursor:pointer;"
                            onclick="abrirFoto('{{ Storage::url($image->image_path) }}')">
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Modal para ver foto ampliada --}}
                <div id="modalFoto" onclick="cerrarFoto()"
                    style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
                        background:rgba(0,0,0,0.85); z-index:9999;
                        align-items:center; justify-content:center; cursor:pointer;">
                    <img id="modalFotoImg" src="" alt="Foto ampliada"
                        style="max-width:90%; max-height:90%; border-radius:8px; box-shadow:0 0 30px rgba(0,0,0,0.5);">
                </div>
            </div>
        </div>
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        </div>
    </div>
</div>
<script src="{{ asset('js/fotos_galeria.js') }}"></script>
@endsection