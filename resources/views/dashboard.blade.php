@extends('layout.layouts')

@section('title', 'Dashboard - Flash Car')

@section('content')
@include('layout.header')
<div class="bg-light">
    <div class="main_principal">
        <div class="space_principal">
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->

        {{-- ENCABEZADO --}}
        <div class="col-12 d-flex align-items-center justify-content-between flex-wrap px-1 py-3">
            <div>
                <h1 class="fs-4 fw-bold m-0">Dashboard</h1>
                <p class="text-muted fs-6 m-0">{{ \Carbon\Carbon::now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</p>
            </div>
            <a href="{{ route('rentas.index') }}" class="boton_link_xxl b_sm rounded" style="width:auto; padding:0 14px; white-space:nowrap;">
                <i class="bi bi-list-ul me-1"></i> Ver todas las rentas
            </a>
        </div>

        {{-- ===== KPIs ===== --}}
        <div class="col-12 d-flex align-items-stretch justify-content-start flex-wrap">

            {{-- Pendientes --}}
            <div class="col-12 col-sm-6 col-md-3 p-1">
                <div class="cont_base rounded p-2 d-flex align-items-center justify-content-between h-100"
                     style="border-left: 4px solid var(--amarillo_fuerte);">
                    <div>
                        <p class="text-muted m-0 mb-1" style="font-size:0.78rem;">Solicitudes pendientes</p>
                        <p class="fs-4 fw-bold m-0">{{ $pendientes }}</p>
                        <a href="{{ route('rentas.index') }}" class="text-muted" style="font-size:0.75rem;">Ver solicitudes →</a>
                    </div>
                    <div style="width:36px;height:36px;border-radius:50%;background:var(--amarillo_bajo);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-clock-history" style="color:#7a5c00;font-size:1rem;"></i>
                    </div>
                </div>
            </div>

            {{-- Confirmadas --}}
            <div class="col-12 col-sm-6 col-md-3 p-1">
                <div class="cont_base rounded p-2 d-flex align-items-center justify-content-between h-100"
                     style="border-left: 4px solid #28a745;">
                    <div>
                        <p class="text-muted m-0 mb-1" style="font-size:0.78rem;">Rentas confirmadas</p>
                        <p class="fs-4 fw-bold m-0">{{ $confirmadas }}</p>
                        <span class="text-muted" style="font-size:0.75rem;">Activas actualmente</span>
                    </div>
                    <div style="width:36px;height:36px;border-radius:50%;background:#d4edda;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-check-circle-fill" style="color:#28a745;font-size:1rem;"></i>
                    </div>
                </div>
            </div>

            {{-- Flota --}}
            <div class="col-12 col-sm-6 col-md-3 p-1">
                <div class="cont_base rounded p-2 d-flex align-items-center justify-content-between h-100"
                     style="border-left: 4px solid #0d6efd;">
                    <div>
                        <p class="text-muted m-0 mb-1" style="font-size:0.78rem;">Flota disponible</p>
                        <p class="fs-4 fw-bold m-0">{{ $vehiculos_disponibles }} <span class="fs-6 fw-normal text-muted">/ {{ $vehiculos_total }}</span></p>
                        <a href="{{ route('vehiculos.index') }}" class="text-muted" style="font-size:0.75rem;">Ver vehículos →</a>
                    </div>
                    <div style="width:36px;height:36px;border-radius:50%;background:#cfe2ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-car-front-fill" style="color:#0d6efd;font-size:1rem;"></i>
                    </div>
                </div>
            </div>

            {{-- Ingresos --}}
            <div class="col-12 col-sm-6 col-md-3 p-1">
                <div class="cont_base rounded p-2 d-flex align-items-center justify-content-between h-100"
                     style="border-left: 4px solid #6f42c1;">
                    <div>
                        <p class="text-muted m-0 mb-1" style="font-size:0.78rem;">Ingresos del mes</p>
                        <p class="fs-5 fw-bold m-0">${{ number_format($ingresos_mes, 0) }}<span class="fs-6 fw-normal text-muted"> MXN</span></p>
                        <span class="text-muted" style="font-size:0.75rem;">Confirmadas + completadas</span>
                    </div>
                    <div style="width:36px;height:36px;border-radius:50%;background:#e8d5ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-cash-stack" style="color:#6f42c1;font-size:1rem;"></i>
                    </div>
                </div>
            </div>

        </div>

        {{-- ===== FILA PRINCIPAL ===== --}}
        <div class="col-12 d-flex align-items-start justify-content-start flex-wrap mt-1">

            {{-- SOLICITUDES RECIENTES --}}
            <div class="col-12 col-sm-12 col-md-12 col-lg-8 p-1">
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap rounded cont_base">

                    {{-- Cabecera --}}
                    <div class="col-12 d-flex align-items-center justify-content-between p-2 bg_gris_8">
                        <h2 class="fs-6 fw-bold m-0"><i class="bi bi-receipt me-2"></i>Solicitudes recientes</h2>
                        <a href="{{ route('rentas.index') }}" class="text-muted" style="font-size:0.8rem;">Ver todas →</a>
                    </div>

                    {{-- Encabezado columnas --}}
                    <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start">
                        <p class="col-4 col-sm-4 col-md-4 col-lg-3 border_right px-1 text-dark my-1"><b># Cliente</b></p>
                        <p class="col-2 col-sm-2 col-md-2 col-lg-2 border_right px-1 text-dark my-1"><b>Vehículo</b></p>
                        <p class="col-3 col-sm-3 col-md-3 col-lg-2 border_right px-1 text-dark my-1"><b>Entrega</b></p>
                        <p class="col-3 col-sm-3 col-md-3 col-lg-2 border_right px-1 text-dark my-1"><b>Costo</b></p>
                        <p class="col-6 col-sm-6 col-md-6 col-lg-2 border_right px-1 text-dark my-1 box_hidden_movil"><b>Estado</b></p>
                        <p class="col-6 col-sm-6 col-md-6 col-lg-1 px-1 text-dark my-1 box_hidden_movil"><b>Acción</b></p>
                    </div>

                    {{-- Filas --}}
                    @forelse($recientes as $renta)
                    <div class="col-12 d-flex align-items-center justify-content-start flex-wrap border_gris_2_buttom">
                        <div class="col-4 col-sm-4 col-md-4 col-lg-3 display_flex_center_start border_right_dato px-1 my-1">
                            <span class="text-muted me-1" style="font-size:0.78rem;">{{ $renta->id }}</span>
                            <div class="px-1 display_flex_start_center_column over_hidden">
                                <b style="font-size:0.85rem;">{{ $renta->nombre_completo }}</b>
                                <span class="text-muted" style="font-size:0.75rem;">{{ $renta->correo }}</span>
                            </div>
                        </div>
                        <p class="col-2 col-sm-2 col-md-2 col-lg-2 border_right_dato px-1 my-1 m-0" style="font-size:0.85rem;">{{ $renta->vehicle->name ?? '—' }}</p>
                        <p class="col-3 col-sm-3 col-md-3 col-lg-2 border_right_dato px-1 my-1 m-0" style="font-size:0.85rem;">{{ \Carbon\Carbon::parse($renta->fecha_entrega)->format('d/m/Y') }}</p>
                        <p class="col-3 col-sm-3 col-md-3 col-lg-2 border_right_dato px-1 my-1 m-0 fw-semibold" style="font-size:0.85rem;">${{ number_format($renta->costo_total, 0) }}</p>
                        <div class="col-6 col-sm-6 col-md-6 col-lg-2 border_right_dato px-1 my-1 box_hidden_movil">
                            @php
                                $badge = match($renta->estado) {
                                    'pendiente'  => 'warning text-dark',
                                    'confirmada' => 'success',
                                    'cancelada'  => 'danger',
                                    'completada' => 'secondary',
                                    default      => 'light text-dark',
                                };
                            @endphp
                            <span class="badge bg-{{ $badge }}" style="font-size:0.72rem;">
                                {{ ucfirst($renta->estado) }}
                            </span>
                        </div>
                        <div class="col-6 col-sm-6 col-md-6 col-lg-1 px-1 my-1 d-flex align-items-center justify-content-start box_hidden_movil">
                            <a href="{{ route('rentas.show', $renta->id) }}" class="boton_link_sm b_sm rounded link_decoration_none display_flex_center_center">
                                <i class="bi bi-eye-fill"></i>
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 d-flex align-items-center justify-content-center">
                        <p class="fs-6 py-4 m-0 text-center text-muted">No hay solicitudes registradas.</p>
                    </div>
                    @endforelse

                </div>
            </div>

            {{-- ENTREGAS PRÓXIMAS --}}
            <div class="col-12 col-sm-12 col-md-12 col-lg-4 p-1">
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap rounded cont_base">

                    {{-- Cabecera --}}
                    <div class="col-12 d-flex align-items-center justify-content-between p-2 bg_gris_8">
                        <h2 class="fs-6 fw-bold m-0"><i class="bi bi-calendar-event me-2"></i>Entregas próximas</h2>
                        <span class="badge bg-secondary" style="font-size:0.72rem;">Hoy + 3 días</span>
                    </div>

                    {{-- Lista --}}
                    <div class="col-12 p-2">
                        @forelse($proximas as $renta)
                        <a href="{{ route('rentas.show', $renta->id) }}" class="text-decoration-none">
                            <div class="col-12 d-flex align-items-center justify-content-start border_gris_2_buttom py-2">
                                {{-- Fecha --}}
                                <div class="me-2" style="min-width:42px;background:{{ \Carbon\Carbon::parse($renta->fecha_entrega)->isToday() ? 'var(--amarillo_fuerte)' : '#111' }};color:{{ \Carbon\Carbon::parse($renta->fecha_entrega)->isToday() ? '#111' : '#fff' }};border-radius:6px;padding:4px;text-align:center;flex-shrink:0;">
                                    <p class="m-0 fw-bold" style="font-size:1rem;line-height:1;">{{ \Carbon\Carbon::parse($renta->fecha_entrega)->format('d') }}</p>
                                    <p class="m-0" style="font-size:0.62rem;text-transform:uppercase;">{{ \Carbon\Carbon::parse($renta->fecha_entrega)->format('M') }}</p>
                                </div>
                                {{-- Info --}}
                                <div class="over_hidden">
                                    <p class="m-0 fw-semibold text-dark" style="font-size:0.82rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $renta->nombre_completo }}</p>
                                    <p class="m-0 text-muted" style="font-size:0.75rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $renta->vehicle->name ?? '—' }}</p>
                                    <p class="m-0 text-muted" style="font-size:0.72rem;">
                                        <i class="bi bi-clock"></i> {{ substr($renta->hora_entrega, 0, 5) }}
                                        · {{ $renta->lugar_entrega }}
                                    </p>
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="col-12 d-flex align-items-center justify-content-center py-4">
                            <div class="text-center text-muted">
                                <i class="bi bi-calendar-check fs-3 d-block mb-2"></i>
                                <p class="fs-6 m-0">Sin entregas en los próximos 3 días.</p>
                            </div>
                        </div>
                        @endforelse
                    </div>

                </div>
            </div>

        </div>

        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        </div>
    </div>
</div>
@endsection
