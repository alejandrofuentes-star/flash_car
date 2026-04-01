@extends('layout.layouts')

@section('title', 'Estados y Puntos de Entrega - Flash Car')

@section('content')
@include('layout.header')
<div class="bg-light">
    <div class="main_principal">
        <div class="space_principal">
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        <div class="container">

            {{-- AGREGAR ESTADO --}}
            <div class="col-12 d-flex align-items-center justify-content-start flex-wrap rounded cont_base w_150_movil my-2">
                <div class="col-12 d-flex align-items-center justify-content-between flex-wrap p-2 bg_gris_8">
                    <h1 class="fs-6 text_uppcase m-0">Estados y Puntos de Entrega</h1>
                </div>
                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start p-2">
                    <p class="text-dark fs-6 m-0"><b>Agregar nuevo Estado</b></p>
                </div>
                <form method="POST" action="{{ route('states.store') }}" class="col-12 d-flex align-items-center justify-content-start flex-wrap p-2">
                    @csrf
                    <div class="col-12 col-md-8 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Nombre del Estado *</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" name="name" placeholder="Ej: Querétaro, CDMX, Cancún" required>
                    </div>
                    <div class="col-12 col-md-4 d-flex align-items-end py-2 px-1">
                        <button type="submit" class="boton_link_xxl rounded">+ Agregar</button>
                    </div>
                </form>
            </div>

            {{-- LISTA DE ESTADOS --}}
            @forelse($states as $state)
            <div class="col-12 d-flex align-items-center justify-content-start flex-wrap rounded cont_base w_150_movil my-2">

                {{-- Encabezado del estado --}}
                <div class="col-12 d-flex align-items-center justify-content-between flex-wrap p-2 bg_gris_8">
                    <div class="d-flex align-items-center gap-2">
                        @if($state->active)
                            <span class="cuenta_activa">Activo</span>
                        @else
                            <span class="cuenta_inactiva">Inactivo</span>
                        @endif
                        <h2 class="fs-6 text_uppcase m-0">{{ $state->name }}</h2>
                        <small class="text-muted">({{ $state->deliveryPoints->count() }} punto(s))</small>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        {{-- Activar/Desactivar estado --}}
                        <form method="POST" action="{{ route('states.toggle', $state->id) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="boton_link_lg b_sm rounded">
                                {{ $state->active ? 'Desactivar' : 'Activar' }}
                            </button>
                        </form>
                        {{-- Eliminar estado --}}
                        <form method="POST" action="{{ route('states.destroy', $state->id) }}"
                            onsubmit="return confirm('¿Eliminar el estado {{ $state->name }} y todos sus puntos?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="boton_link_lg b_sm bg_rojo_2 rounded">Eliminar</button>
                        </form>
                    </div>
                </div>

                {{-- Lista de puntos --}}
                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start">
                    <p class="col-1 border_right px-1 text-dark my-1"><b>#</b></p>
                    <p class="col-7 border_right px-1 text-dark my-1"><b>Nombre del Punto</b></p>
                    <p class="col-2 border_right px-1 text-dark my-1 "><b>Estado</b></p>
                    <p class="col-2 px-1 text-dark my-1"><b>Acciones</b></p>
                </div>

                @forelse($state->deliveryPoints as $point)
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap border_gris_2_buttom">
                    <p class="col-1 border_right_dato px-1 my-1">{{ $loop->iteration }}</p>
                    <p class="col-7 border_right_dato px-1 my-1"><b>{{ $point->name }}</b></p>
                    <div class="col-2 border_right_dato px-1 my-1">
                        @if($point->active)
                            <span class="cuenta_activa">Activo</span>
                        @else
                            <span class="cuenta_inactiva">Inactivo</span>
                        @endif
                    </div>
                    <div class="col-2 d-flex align-items-center gap-1 px-1 my-1">
                        {{-- Activar/Desactivar punto --}}
                        <form method="POST" action="{{ route('states.points.toggle', $point->id) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="boton_link_sm b_sm rounded">
                                {{ $point->active ? '🔴' : '🟢' }}
                            </button>
                        </form>
                        {{-- Eliminar punto --}}
                        <form method="POST" action="{{ route('states.points.destroy', $point->id) }}"
                            onsubmit="return confirm('¿Eliminar este punto de entrega?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="boton_link_sm b_sm bg_rojo_2 rounded">✕</button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="col-12 d-flex align-items-center justify-content-center">
                    <p class="fs-6 py-2 m-0 text-muted">No hay puntos de entrega registrados para este estado.</p>
                </div>
                @endforelse

                {{-- Agregar punto --}}
                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start p-2 mt-2">
                    <p class="text-dark fs-6 m-0"><b>+ Agregar Punto de Entrega</b></p>
                </div>
                <form method="POST" action="{{ route('states.points.store', $state->id) }}" class="col-12 d-flex align-items-center justify-content-start flex-wrap p-2">
                    @csrf
                    <div class="col-12 col-md-6 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Nombre del Punto *</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" name="name" placeholder="Ej: Aeropuerto, Oficina Centro" required>
                    </div>
                    <div class="col-12 col-md-6 d-flex align-items-center justify-content-center py-2 px-1">
                        <button type="submit" class="boton_link_lg rounded">Agregar</button>
                    </div>
                </form>

            </div>
            @empty
            <div class="col-12 d-flex align-items-center justify-content-center cont_base my-2 p-4">
                <p class="fs-6 text-muted m-0">No hay estados registrados aún.</p>
            </div>
            @endforelse

            @if(session('success'))
                <div class="messenger_alert">
                    <div class="dialog_alert messenger py-2 px-4 rounded">
                        <div class="fs-6 text-white"><b>{{ session('success') }}</b></div>
                    </div>
                </div>
            @endif

        </div>
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        </div>
    </div>
</div>
@endsection