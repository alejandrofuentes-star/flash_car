@extends('layout.layouts')

@section('title', 'Rentas - Flash Car')

@section('content')
@include('layout.header')
<div class="bg-light">
    <div class="main_principal">
        <div class="space_principal">
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        <div class="container">

            <div class="col-12 d-flex align-items-center justify-content-start flex-wrap rounded cont_base my-2">
                <div class="col-12 d-flex align-items-center justify-content-between flex-wrap p-2 bg_gris_8">
                    <h1 class="fs-6 text_uppcase m-0">Solicitudes de Renta</h1>
                </div>
                <div class="w_150_movil" style="overflow-x:auto; width:100%">
                <div class="bg_amarillo d-flex align-items-center justify-content-start" style="min-width:980px">
                    <p class="col-3 border_right px-1 text-dark my-1"><b># Cliente</b></p>
                    <p class="col-2 border_right px-1 text-dark my-1 text_break"><b>Vehículo</b></p>
                    <p class="col-2 border_right px-1 text-dark my-1"><b>Fecha entrega</b></p>
                    <p class="col-2 border_right px-1 text-dark my-1 text_break"><b>Fecha Devolución</b></p>
                    <p class="col-1 border_right px-1 text-dark my-1"><b>Estado</b></p>
                    <p class="col-2 px-1 text-dark my-1"><b>Acciones</b></p>
                </div>
                 @forelse($rentas as $renta)
                <div class="d-flex align-items-center justify-content-start border_gris_2_buttom" style="min-width:980px">
                    <div class="col-3 display_flex_center_start border_right_dato px-1 my-1">
                        {{ $renta->id }}
                        <div class="px-2 display_flex_start_center_column over_hidden">
                            <b>{{ $renta->nombre_completo }}</b>
                            {{ $renta->correo }}
                        </div>
                    </div>
                    <p class="col-2 border_right_dato px-1 my-1">{{ $renta->vehicle->name ?? '—' }}</p>
                    <p class="col-2 border_right_dato px-1 my-1">{{ $renta->fecha_entrega->format('d/m/Y') }}</p>
                    <p class="col-2 border_right_dato px-1 my-1">{{ $renta->fecha_devolucion->format('d/m/Y') }}</p>
                    <div class="col-1 px-1 my-1 d-flex align-items-center">
                        @if($renta->estado == 'pendiente')
                            <span class="badge bg-warning text-dark" style="font-size:0.65rem">Pendiente</span>
                        @elseif($renta->estado == 'confirmada')
                            <span class="badge bg-success" style="font-size:0.65rem">Confirmada</span>
                        @elseif($renta->estado == 'cancelada')
                            <span class="badge bg-danger" style="font-size:0.65rem">Cancelada</span>
                        @elseif($renta->estado == 'completada')
                            <span class="badge bg-secondary" style="font-size:0.65rem">Completada</span>
                        @endif
                    </div>
                    <div class="col-2 px-1 my-1 d-flex align-items-center justify-content-start">
                        <form method="POST" action="{{ route('rentas.reenviarCorreo', $renta->id) }}" class="d-inline">
                            @csrf
                            @if($renta->mail_enviado)
                                <button type="submit" class="boton_link_sm b_sm rounded border-0 bg-transparent"
                                        title="Enviado el {{ $renta->mail_enviado_at?->timezone('America/Mexico_City')->format('d/m/Y H:i') }} — clic para reenviar">
                                    <i class="bi bi-envelope-check-fill text-success fs-5"></i>
                                </button>
                            @else
                                <button type="submit" class="boton_link_sm b_sm rounded border-0 bg-transparent" title="Reenviar correo a {{ $renta->correo }}">
                                    <i class="bi bi-envelope-x-fill text-danger fs-5"></i>
                                </button>
                            @endif
                        </form>
                        <a href="{{ route('rentas.show', $renta->id) }}" class="boton_link_sm b_sm rounded link_decoration_none display_flex_center_center">Ver</a>
                        <a href="{{ route('rentas.edit', $renta->id) }}" class="boton_link_sm b_sm rounded link_decoration_none display_flex_center_center" title="Editar"><i class="bi bi-pencil-fill"></i></a>
                        @if(Auth::user()->hasAdminAccess())
                        <button type="button"
                                class="boton_link_sm b_sm rounded border-0 bg-danger text-white"
                                data-bs-toggle="modal" data-bs-target="#modalEliminarRenta"
                                data-action="{{ route('rentas.destroy', $renta->id) }}"
                                data-id="{{ $renta->id }}"
                                data-nombre="{{ $renta->nombre_completo }}">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                        @endif
                    </div>
                </div>
                @empty
                    <div class="col-12 d-flex align-items-center justify-content-center">
                        <p colspan="9" class="fs-6 py-4 m-0 text-center text-muted">No hay solicitudes de renta registradas.</p>
                    </div>
                @endforelse
                </div>{{-- fin overflow-x --}}
                @if($rentas->hasPages())
                <div class="col-12 d-flex align-items-center justify-content-between flex-wrap px-2 py-2 border-top">
                    <p class="text-muted fs-6 m-0">
                        Mostrando {{ $rentas->firstItem() }}–{{ $rentas->lastItem() }} de {{ $rentas->total() }} solicitudes
                    </p>
                    {{ $rentas->links('pagination::bootstrap-5') }}
                </div>
                @endif

                @if(session('success'))
                    <div class="messenger_alert">
                        <div class="dialog_alert messenger py-2 px-4 rounded">
                            <div class="fs-6 text-white"><b>{{ session('success') }}</b></div>
                        </div>
                    </div>
                @endif
                @if(session('error'))
                    <div class="messenger_alert">
                        <div class="dialog_alert messenger py-2 px-4 rounded" style="background:#dc3545;">
                            <div class="fs-6 text-white"><b>{{ session('error') }}</b></div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        </div>
    </div>
</div>
{{-- Modal eliminar renta --}}
<div class="modal fade" id="modalEliminarRenta" tabindex="-1" aria-labelledby="modalEliminarRentaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg_gris_8 border-0">
                <h5 class="modal-title fs-6 text-white" id="modalEliminarRentaLabel">
                    <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Eliminar renta
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-4">
                <p class="mb-1 fs-6">¿Estás seguro de eliminar la renta <strong id="modalRentaInfo"></strong>?</p>
                <p class="text-muted small mb-0">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="boton_link_lg rounded" data-bs-dismiss="modal">Cancelar</button>
                <form id="formEliminarRenta" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="boton_link_lg bg_rojo_2 rounded">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('modalEliminarRenta').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    document.getElementById('modalRentaInfo').textContent = '#' + btn.dataset.id + ' — ' + btn.dataset.nombre;
    document.getElementById('formEliminarRenta').action = btn.dataset.action;
});
</script>
@endpush

@endsection