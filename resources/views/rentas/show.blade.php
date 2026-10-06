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
                            @php
                                [$badgeClass, $badgeLabel] = match($renta->estado) {
                                    'reserva_confirmada'  => ['warning text-dark', 'Reserva Confirmada'],
                                    'proxima_entrega'     => ['info text-dark',    'Próxima Entrega'],
                                    'pendiente_pago'      => ['warning text-dark', 'Pendiente de pago/garantía'],
                                    'contrato_abierto'    => ['success',           'Contrato abierto'],
                                    'contrato_finalizado' => ['secondary',         'Contrato Finalizado'],
                                    'devolucion_exitosa'  => ['primary',           'Devolución exitosa'],
                                    'dano_faltante'       => ['danger',            'Daño o Faltante'],
                                    'garantia_pendiente'  => ['dark',              'Dev. garantía pendiente'],
                                    'cancelada'           => ['danger',            'Cancelada'],
                                    default               => ['light text-dark',   ucfirst($renta->estado)],
                                };
                            @endphp
                            <span class="badge bg-{{ $badgeClass }} fs-6">{{ $badgeLabel }}</span>
                        </span>
                    </div>
                    <div class="col-12 col-md-6 fila_form_f_b py-2">
                        <form class="col-12" method="POST" action="{{ route('rentas.estado', $renta->id) }}" class="d-flex align-items-center gap-2">
                            @csrf
                            @method('PUT')
                            <select class="input_form_f_b fs-6 p-1" name="estado">
                                <option value="reserva_confirmada"  {{ $renta->estado == 'reserva_confirmada'  ? 'selected' : '' }}>Reserva Confirmada</option>
                                <option value="proxima_entrega"     {{ $renta->estado == 'proxima_entrega'     ? 'selected' : '' }}>Próxima Entrega</option>
                                <option value="pendiente_pago"      {{ $renta->estado == 'pendiente_pago'      ? 'selected' : '' }}>Pendiente de pago/garantía</option>
                                <option value="contrato_abierto"    {{ $renta->estado == 'contrato_abierto'    ? 'selected' : '' }}>Contrato abierto</option>
                                <option value="contrato_finalizado" {{ $renta->estado == 'contrato_finalizado' ? 'selected' : '' }}>Contrato Finalizado</option>
                                <option value="devolucion_exitosa"  {{ $renta->estado == 'devolucion_exitosa'  ? 'selected' : '' }}>Devolución exitosa</option>
                                <option value="dano_faltante"       {{ $renta->estado == 'dano_faltante'       ? 'selected' : '' }}>Daño o Faltante</option>
                                <option value="garantia_pendiente"  {{ $renta->estado == 'garantia_pendiente'  ? 'selected' : '' }}>Dev. garantía pendiente</option>
                                <option value="cancelada"           {{ $renta->estado == 'cancelada'           ? 'selected' : '' }}>Cancelada</option>
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
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $renta->vehicle ? $renta->vehicle->name . ($renta->vehicle->plate_number ? ' — ' . $renta->vehicle->plate_number : '') : '—' }}" readonly>
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
                @php
                    $anticipo  = $renta->monto_anticipo ?? 0;
                    $saldo     = max(0, $renta->costo_total - $anticipo);
                    $pagado    = $renta->payment_intent_id && $anticipo > 0;
                @endphp
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-2">
                    <div class="col-6 col-md-3 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Total de Días</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $renta->total_dias }} días" readonly>
                    </div>
                    <div class="col-6 col-md-3 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Método de pago</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text"
                               value="{{ $renta->metodo_pago === 'tarjeta' ? '💳 Tarjeta' : ($renta->metodo_pago ? ucfirst($renta->metodo_pago) : '—') }}"
                               readonly>
                    </div>
                </div>

                {{-- Desglose financiero: desglose a la izquierda, tarjetas (y las que se agreguen) a la derecha --}}
                <div class="col-12 d-flex align-items-start justify-content-start flex-wrap px-2 pb-3">

                    {{-- Desglose del cálculo --}}
                    @php
                        $conceptos = [
                            'mes'    => ['mes', 'meses'],
                            'semana' => ['semana', 'semanas'],
                            'dia'    => ['día', 'días'],
                        ];
                    @endphp
                    <div class="col-12 col-sm-12 col-md-4 col-lg-4 p-1">
                    <div class="rounded p-3" style="background:#f5f6f8; border:1px solid #e0e3e8;">
                        <p class="text-muted fs-6 mb-2 text-center">Desglose del cálculo</p>
                        @if($desglose && count($desglose['partidas']))
                            @php $fila = 'd-flex justify-content-between'; $txt = 'font-size:0.85rem;'; @endphp
                            @foreach($desglose['partidas'] as $partida)
                                <div class="{{ $fila }}" style="{{ $txt }}">
                                    <span>{{ $partida['cantidad'] }} {{ $conceptos[$partida['concepto']][$partida['cantidad'] === 1 ? 0 : 1] }} × ${{ number_format($partida['precio'], 2) }}</span>
                                    <b>${{ number_format($partida['subtotal'], 2) }}</b>
                                </div>
                            @endforeach
                            @if($desglose['cargo_extra'])
                                <div class="{{ $fila }}" style="{{ $txt }}">
                                    <span><i class="bi bi-clock-history me-1"></i>1 día extra</span>
                                    <b>+${{ number_format($desglose['dia_extra'], 2) }}</b>
                                </div>
                                <p class="mb-0" style="font-size:0.72rem; color:#6b7280;">Devolución más de 2 h después de la hora de entrega</p>
                                @if(abs($desglose['ajuste']) >= 0.01)
                                    <div class="{{ $fila }}" style="{{ $txt }}">
                                        <span>Ajuste por tarifa semanal/mensual</span>
                                        <b>{{ $desglose['ajuste'] < 0 ? '-' : '+' }}${{ number_format(abs($desglose['ajuste']), 2) }}</b>
                                    </div>
                                @endif
                            @endif
                            <div class="{{ $fila }} border-top mt-2 pt-1" style="{{ $txt }}">
                                <span>Total {{ $renta->total_dias }} {{ $renta->total_dias == 1 ? 'día' : 'días' }}</span>
                                <b>${{ number_format($desglose['calculado'], 2) }}</b>
                            </div>
                            @if(!$desglose['coincide'])
                                <p class="mb-0 mt-1" style="font-size:0.72rem; color:#b45309;">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $desglose['guardados'] ? 'Precios de la reserva' : 'Precios actuales de la categoría (renta registrada antes de guardar precios)' }}; no coincide con el total registrado.
                                </p>
                            @elseif(!$desglose['guardados'])
                                <p class="mb-0 mt-1" style="font-size:0.72rem; color:#6b7280;">
                                    <i class="bi bi-info-circle me-1"></i>Con precios actuales de la categoría.
                                </p>
                            @endif
                        @else
                            <p class="mb-0 text-center" style="font-size:0.8rem; color:#9ca3af;">Sin categoría asignada, no se puede desglosar.</p>
                        @endif

                        {{-- Costo por día (el de la renta o, si no tiene, el del vehículo) × días de la renta --}}
                        @php
                            $costoDia     = $renta->costoPorDiaAplicado();
                            $costoAuto    = $costoDia !== null ? round($renta->total_dias * $costoDia, 2) : 0;
                            $descuento    = (float) ($renta->descuento ?? 0);
                            $totalGeneral = (float) ($renta->total_general ?? $renta->calcularTotalGeneral());
                        @endphp
                        <div class="border-top mt-3 pt-2">
                            <p class="text-muted mb-1 text-center" style="font-size:0.85rem;">Costo por día del auto</p>
                            @if($costoDia !== null)
                                <div class="d-flex justify-content-between" style="font-size:0.85rem;">
                                    <span>{{ $renta->total_dias }} {{ $renta->total_dias == 1 ? 'día' : 'días' }} × ${{ number_format($costoDia, 2) }}</span>
                                    <b class="text-danger">-${{ number_format($costoAuto, 2) }}</b>
                                </div>
                            @else
                                <p class="mb-0 text-center" style="font-size:0.8rem; color:#9ca3af;">Sin costo por día capturado.</p>
                            @endif
                            @if($descuento > 0)
                                <div class="d-flex justify-content-between" style="font-size:0.85rem;">
                                    <span>Descuentos</span>
                                    <b class="text-danger">-${{ number_format($descuento, 2) }}</b>
                                </div>
                            @endif
                        </div>

                        {{-- Total general: costo total registrado − (días × costo por día del auto) − descuentos --}}
                        @if($costoDia !== null || $descuento > 0)
                            <div class="d-flex justify-content-between border-top mt-2 pt-1 fs-6">
                                <b>Total general</b>
                                <b class="{{ $totalGeneral < 0 ? 'text-danger' : 'text-success' }}">{{ $totalGeneral < 0 ? '-' : '' }}${{ number_format(abs($totalGeneral), 2) }}</b>
                            </div>
                            @if($desglose && !$desglose['coincide'])
                                <p class="mb-0" style="font-size:0.72rem; color:#6b7280;">Sobre el costo total registrado (${{ number_format($renta->costo_total, 2) }})</p>
                            @endif
                        @endif
                    </div>
                    </div>

                    {{-- Tarjetas de montos --}}
                    <div class="col-12 col-sm-12 col-md-8 col-lg-8 d-flex align-items-stretch justify-content-start flex-wrap">

                    {{-- Costo total --}}
                    <div class="col-12 col-sm-12 col-md-4 col-lg-4 p-1">
                    <div class="h-100 rounded p-3 text-center" style="background:#f5f6f8; border:1px solid #e0e3e8;">
                        <p class="text-muted fs-6 mb-1">Costo total de renta</p>
                        <b class="fs-4" style="color:var(--primary);">${{ number_format($renta->costo_total, 2) }}</b>
                    </div>

                    </div>

                    {{-- Anticipo / Pagado --}}
                    <div class="col-12 col-sm-12 col-md-4 col-lg-4 p-1">
                    <div class="h-100 rounded p-3 text-center"
                         style="background:{{ $pagado ? '#d1fae5' : '#f5f6f8' }}; border:1px solid {{ $pagado ? '#6ee7b7' : '#e0e3e8' }};">
                        <p class="text-muted fs-6 mb-1">
                            {{ $pagado ? '✅ Pagado (anticipo)' : 'Anticipo' }}
                        </p>
                        <b class="fs-4" style="color:{{ $pagado ? '#059669' : '#9ca3af' }};">
                            ${{ $pagado ? number_format($anticipo, 2) : '0.00' }}
                        </b>
                        @if($pagado)
                        <p class="mb-0 mt-1" style="font-size:0.72rem; color:#6b7280;">
                            <i class="bi bi-shield-check-fill text-success me-1"></i>Confirmado por Stripe
                        </p>
                        @endif
                    </div>

                    </div>

                    {{-- Saldo pendiente --}}
                    <div class="col-12 col-sm-12 col-md-4 col-lg-4 p-1">
                    <div class="h-100 rounded p-3 text-center"
                         style="background:{{ $saldo > 0 ? '#fef9c3' : '#d1fae5' }}; border:1px solid {{ $saldo > 0 ? '#fde047' : '#6ee7b7' }};">
                        <p class="text-muted fs-6 mb-1">
                            {{ $saldo > 0 ? '⏳ Saldo pendiente' : '✅ Liquidado' }}
                        </p>
                        <b class="fs-4" style="color:{{ $saldo > 0 ? '#b45309' : '#059669' }};">
                            ${{ number_format($saldo, 2) }}
                        </b>
                        @if($saldo > 0)
                        <p class="mb-0 mt-1" style="font-size:0.72rem; color:#6b7280;">
                            <i class="bi bi-clock me-1"></i>A cobrar al momento de la entrega
                        </p>
                        @endif
                    </div>
                    </div>

                    {{-- Costo por día: el de la renta si se editó, si no el del vehículo --}}
                    @include('rentas.partials.tarjeta_editable', [
                        'campo'       => 'costo_por_dia',
                        'titulo'      => 'Costo por día',
                        'valor'       => $renta->costo_por_dia ?? $renta->vehicle?->costo_por_dia,
                        'valorEditar' => $renta->costo_por_dia ?? $renta->vehicle?->costo_por_dia,
                        'nota'        => $renta->costo_por_dia === null ? 'Tomado del vehículo' : null,
                    ])

                    {{-- Descuentos --}}
                    @include('rentas.partials.tarjeta_editable', [
                        'campo'       => 'descuento',
                        'titulo'      => 'Descuentos',
                        'valor'       => $renta->descuento,
                        'valorEditar' => $renta->descuento,
                        'vacio'       => 'Sin descuento',
                    ])

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

@push('scripts')
<script>
    // Edición rápida de las tarjetas de Resumen de Costo (partial rentas.partials.tarjeta_editable)
    document.querySelectorAll('[data-tarjeta-editable]').forEach(function (tarjeta) {
        const boton = tarjeta.querySelector('[data-editar]');
        const vista = tarjeta.querySelector('[data-vista]');
        const form  = tarjeta.querySelector('[data-form]');
        const input = tarjeta.querySelector('[data-input]');
        const valorOriginal = input.defaultValue;

        boton.addEventListener('click', function () {
            boton.classList.add('d-none');
            vista.classList.add('d-none');
            form.classList.remove('d-none');
            input.focus();
            input.select();
        });

        tarjeta.querySelector('[data-cancelar]').addEventListener('click', function () {
            input.value = valorOriginal;
            form.classList.add('d-none');
            vista.classList.remove('d-none');
            boton.classList.remove('d-none');
        });
    });
</script>
@endpush
