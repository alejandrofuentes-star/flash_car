@extends('layout.layouts')

@section('title', 'Detalle Renta - Flash Car')

@section('content')
@include('layout.header')
<div class="bg-light">
    <div class="main_principal">
        <div class="space_principal">
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        <div class="container">

            <div class="col-12 d-flex align-items-center justify-content-start flex-wrap rounded cont_base my-2">
                <div class="col-12 d-flex align-items-center justify-content-between flex-wrap p-2 bg_gris_8">
                    <h1 class="fs-6 text_uppcase m-0">Renta #{{ $renta->id }}</h1>
                    <div class="d-flex gap-2">
                        <a class="boton_link_xxl rounded link_decoration_none display_flex_center_center" style="width:auto; padding:0 14px;" href="{{ route('rentas.index') }}">← Volver</a>
                        <a class="boton_link_xxl rounded link_decoration_none display_flex_center_center" style="width:auto; padding:0 14px;" href="{{ route('rentas.edit', $renta->id) }}">
                            <i class="bi bi-pencil-fill me-1"></i> Editar
                        </a>
                    </div>
                </div>

                {{-- Estado --}}
                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start p-2">
                    <p class="text-dark fs-6 m-0"><b>Estado de la Solicitud</b></p>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-2">
                    <div class="col-12 col-md-6 fila_form_f_b py-2">
                        <span class="label_form_f_b fs-6 p-1"><b>Estado actual:</b></span>
                        <span class="input_form_f_b fs-6 p-1">
                            @if($renta->estado == 'pendiente')
                                <span class="badge bg-warning text-dark fs-6">Pendiente</span>
                            @elseif($renta->estado == 'confirmada')
                                <span class="badge bg-success fs-6">Confirmada</span>
                            @elseif($renta->estado == 'cancelada')
                                <span class="badge bg-danger fs-6">Cancelada</span>
                            @elseif($renta->estado == 'completada')
                                <span class="badge bg-secondary fs-6">Completada</span>
                            @endif
                        </span>
                    </div>
                    <div class="col-12 col-md-6 fila_form_f_b py-2">
                        <form class="col-12" method="POST" action="{{ route('rentas.estado', $renta->id) }}" class="d-flex align-items-center gap-2">
                            @csrf
                            @method('PUT')
                            <select class="input_form_f_b bg_danger fs-6 p-1" name="estado">
                                <option value="pendiente" {{ $renta->estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="confirmada" {{ $renta->estado == 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                                <option value="cancelada" {{ $renta->estado == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                                <option value="completada" {{ $renta->estado == 'completada' ? 'selected' : '' }}>Completada</option>
                            </select>
                            <button type="submit" class="boton_link_xxl my-3 b_sm rounded">Actualizar</button>
                        </form>
                    </div>
                </div>

                {{-- Datos del cliente --}}
                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start p-2">
                    <p class="text-dark fs-6 m-0"><b>Datos del Cliente</b></p>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-1">
                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Nombre Completo</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $renta->nombre_completo }}" readonly>
                    </div>
                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Teléfono</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $renta->telefono }}" readonly>
                    </div>
                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Correo</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $renta->correo }}" readonly>
                    </div>
                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Ciudad</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $renta->ciudad }}" readonly>
                    </div>
                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>No. Pasajeros</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $renta->num_pasajeros }}" readonly>
                    </div>
                </div>

                {{-- Vehículo --}}
                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start p-2">
                    <p class="text-dark fs-6 m-0"><b>Vehículo</b></p>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-1">
                    <div class="col-12 col-md-3 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Vehículo</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $renta->vehicle->name ?? '—' }}" readonly>
                    </div>
                    <div class="col-12 col-md-3 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Categoría</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $renta->vehicle->category->name ?? '—' }}" readonly>
                    </div>
                </div>

                {{-- Entrega --}}
                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start p-2">
                    <p class="text-dark fs-6 m-0"><b>Entrega</b></p>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-1">
                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Fecha de Entrega</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $renta->fecha_entrega->format('d/m/Y') }}" readonly>
                    </div>
                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Hora de Entrega</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $renta->hora_entrega }}" readonly>
                    </div>
                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Lugar de Entrega</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $renta->lugar_entrega }}" readonly>
                    </div>
                </div>

                {{-- Devolución --}}
                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start p-2">
                    <p class="text-dark fs-6 m-0"><b>Devolución</b></p>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-1">
                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Fecha de Devolución</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $renta->fecha_devolucion->format('d/m/Y') }}" readonly>
                    </div>
                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Hora de Devolución</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $renta->hora_devolucion }}" readonly>
                    </div>
                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Lugar de Devolución</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $renta->lugar_devolucion }}" readonly>
                    </div>
                </div>

                {{-- Resumen de costo --}}
                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start p-2">
                    <p class="text-dark fs-6 m-0"><b>Resumen de Costo</b></p>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-2">
                    <div class="col-6 col-md-3 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Total de Días</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $renta->total_dias }} días" readonly>
                    </div>
                    <div class="col-6 col-md-3 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Costo Total</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="${{ number_format($renta->costo_total, 2) }}" readonly style="font-weight:700; color:var(--primary);">
                    </div>
                </div>

                {{-- Registro --}}
                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start p-2">
                    <p class="text-dark fs-6 m-0"><b>Información de Registro</b></p>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-1">
                    <div class="col-6 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Fecha de Solicitud</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $renta->created_at->timezone('America/Mexico_City')->format('d/m/Y H:i:s') }}" readonly>
                    </div>
                    <div class="col-6 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Última Actualización</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $renta->updated_at->timezone('America/Mexico_City')->format('d/m/Y H:i:s') }}" readonly>
                    </div>
                </div>
                @if(session('success'))
                    <div class="messenger_alert">
                        <div class="dialog_alert messenger py-2 px-4 rounded">
                            <div class="fs-6 text-white"><b>{{ session('success') }}</b></div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        </div>
    </div>
</div>
@endsection