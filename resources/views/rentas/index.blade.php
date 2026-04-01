@extends('layout.layouts')

@section('title', 'Rentas - Flash Car')

@section('content')
@include('layout.header')
<div class="bg-light">
    <div class="main_principal">
        <div class="space_principal">
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        <div class="container">

            <div class="col-12 d-flex align-items-center justify-content-start flex-wrap rounded cont_base w_150_movil my-2">
                <div class="col-12 d-flex align-items-center justify-content-between flex-wrap p-2 bg_gris_8">
                    <h1 class="fs-6 text_uppcase m-0">Solicitudes de Renta</h1>
                </div>
                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start">
                    <p class="col-4 col-sm-4 col-md-4 col-lg-3 border_right px-1 text-dark my-1"><b># Cliente</b></p>
                    <p class="col-2 col-sm-2 col-md-2 col-lg-2 border_right px-1 text-dark my-1 text_break"><b>Vehículo</b></p>
                    <p class="col-3 col-sm-3 col-md-3 col-lg-2 border_right px-1 text-dark my-1"><b>Fecha entrega</b></p>
                    <p class="col-3 col-sm-3 col-md-3 col-lg-2 border_right px-1 text-dark my-1 text_break"><b>Fecha Devolución</b></p>
                    <p class="col-3 col-sm-3 col-md-6 col-lg-1 border_right px-1 text-dark my-1 box_hidden_movil"><b>Estado</b></p>
                    <p class="col-3 col-sm-3 col-md-6 col-lg-2 px-1 text-dark my-1 box_hidden_movil"><b>Acciones</b></p>
                </div>
                 @forelse($rentas as $renta)
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap border_gris_2_buttom">
                    <div class="col-4 col-sm-4 col-md-4 col-lg-3 display_flex_center_start border_right_dato px-1 my-1">
                        {{ $renta->id }} 
                        <div class="px-2 display_flex_start_center_column over_hidden">
                            <b>{{ $renta->nombre_completo }}</b>
                            {{ $renta->correo }}
                        </div>
                    </div>
                    <p class="col-2 col-sm-2 col-md-2 col-lg-2 border_right_dato px-1 my-1">{{ $renta->vehicle->name ?? '—' }}</p>
                    <p class="col-3 col-sm-3 col-md-3 col-lg-2 border_right_dato px-1 my-1">{{ $renta->fecha_entrega->format('d/m/Y') }}</p>
                    <p class="col-3 col-sm-3 col-md-3 col-lg-2 border_right_dato px-1 my-1">{{ $renta->fecha_devolucion->format('d/m/Y') }}</p>
                    <div class="col-6 col-sm-6 col-md-6 col-lg-1 px-1 my-1">
                        @if($renta->estado == 'pendiente')
                            <span class="badge bg-warning text-dark">Pendiente</span>
                        @elseif($renta->estado == 'confirmada')
                            <span class="badge bg-success">Confirmada</span>
                        @elseif($renta->estado == 'cancelada')
                            <span class="badge bg-danger">Cancelada</span>
                        @elseif($renta->estado == 'completada')
                            <span class="badge bg-secondary">Completada</span>
                        @endif
                    </div>
                    <div class="col-6 col-sm-6 col-md-6 col-lg-2 px-1 my-1 d-flex align-items-center justify-content-start">
                        <a href="{{ route('rentas.show', $renta->id) }}" class="boton_link_sm b_sm rounded link_decoration_none display_flex_center_center">Ver</a>
                        <a href="{{ route('rentas.edit', $renta->id) }}" class="boton_link_lg rounded">Editar</a>
                    </div>
                </div>
                @empty
                    <div class="col-12 d-flex align-items-center justify-content-center">
                        <p colspan="9" class="fs-6 py-4 m-0 text-center text-muted">No hay solicitudes de renta registradas.</p>
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
        </div>
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        </div>
    </div>
</div>
@endsection