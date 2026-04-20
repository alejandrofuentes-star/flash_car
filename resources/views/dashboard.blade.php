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
                <p class="text-muted fs-6 m-0">{{ now()->format('l, d \d\e F \d\e Y') }}</p>
            </div>
            <a href="{{ route('rentas.index') }}" class="boton_link_xxl b_sm rounded" style="width:auto; padding:0 14px; white-space:nowrap;">
                <i class="bi bi-list-ul me-1"></i> Ver todas las rentas
            </a>
        </div>

        {{-- ===== KPIs ===== --}}
        <div class="col-12 d-flex align-items-stretch justify-content-start flex-wrap">

            {{-- Pendientes --}}
            <div class="col-12 col-sm-6 col-md-3 p-1">
                <div class="cont_base rounded p-3 d-flex align-items-center justify-content-between h-100"
                     style="border-left: 4px solid #f5c400;">
                    <div>
                        <p class="text-muted fs-6 m-0 mb-1">Solicitudes pendientes</p>
                        <p class="fs-2 fw-bold m-0">{{ $pendientes }}</p>
                        <a href="{{ route('rentas.index') }}" class="text-muted" style="font-size:0.78rem;">Ver solicitudes →</a>
                    </div>
                    <div style="width:48px;height:48px;border-radius:50%;background:#fff8dc;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-clock-history fs-4" style="color:#f5c400;"></i>
                    </div>
                </div>
            </div>

            {{-- Confirmadas --}}
            <div class="col-12 col-sm-6 col-md-3 p-1">
                <div class="cont_base rounded p-3 d-flex align-items-center justify-content-between h-100"
                     style="border-left: 4px solid #28a745;">
                    <div>
                        <p class="text-muted fs-6 m-0 mb-1">Rentas confirmadas</p>
                        <p class="fs-2 fw-bold m-0">{{ $confirmadas }}</p>
                        <span class="text-muted" style="font-size:0.78rem;">Activas actualmente</span>
                    </div>
                    <div style="width:48px;height:48px;border-radius:50%;background:#d4edda;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-check-circle-fill fs-4" style="color:#28a745;"></i>
                    </div>
                </div>
            </div>

            {{-- Flota --}}
            <div class="col-12 col-sm-6 col-md-3 p-1">
                <div class="cont_base rounded p-3 d-flex align-items-center justify-content-between h-100"
                     style="border-left: 4px solid #0d6efd;">
                    <div>
                        <p class="text-muted fs-6 m-0 mb-1">Flota disponible</p>
                        <p class="fs-2 fw-bold m-0">{{ $vehiculos_disponibles }} <span class="fs-6 fw-normal text-muted">/ {{ $vehiculos_total }}</span></p>
                        <a href="{{ route('vehiculos.index') }}" class="text-muted" style="font-size:0.78rem;">Ver vehículos →</a>
                    </div>
                    <div style="width:48px;height:48px;border-radius:50%;background:#cfe2ff;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-car-front-fill fs-4" style="color:#0d6efd;"></i>
                    </div>
                </div>
            </div>

            {{-- Ingresos --}}
            <div class="col-12 col-sm-6 col-md-3 p-1">
                <div class="cont_base rounded p-3 d-flex align-items-center justify-content-between h-100"
                     style="border-left: 4px solid #6f42c1;">
                    <div>
                        <p class="text-muted fs-6 m-0 mb-1">Ingresos del mes</p>
                        <p class="fs-4 fw-bold m-0">${{ number_format($ingresos_mes, 0) }}<span class="fs-6 fw-normal text-muted"> MXN</span></p>
                        <span class="text-muted" style="font-size:0.78rem;">Confirmadas + completadas</span>
                    </div>
                    <div style="width:48px;height:48px;border-radius:50%;background:#e8d5ff;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-cash-stack fs-4" style="color:#6f42c1;"></i>
                    </div>
                </div>
            </div>

        </div>

        {{-- ===== FILA PRINCIPAL ===== --}}
        <div class="col-12 d-flex align-items-start justify-content-start flex-wrap mt-1">

            {{-- SOLICITUDES RECIENTES --}}
            <div class="col-12 col-lg-8 p-1">
                <div class="cont_base rounded">
                    <div class="col-12 d-flex align-items-center justify-content-between p-3 bg_gris_8">
                        <h2 class="fs-6 fw-bold m-0"><i class="bi bi-receipt me-2"></i>Solicitudes recientes</h2>
                        <a href="{{ route('rentas.index') }}" class="text-muted" style="font-size:0.8rem;">Ver todas →</a>
                    </div>
                    <div class="col-12" style="overflow-x:auto;">
                        <table class="table table-hover mb-0" style="font-size:0.85rem;">
                            <thead style="background:#f8f9fa;">
                                <tr>
                                    <th class="px-3 py-2 fw-semibold text-muted">#</th>
                                    <th class="px-3 py-2 fw-semibold text-muted">Cliente</th>
                                    <th class="px-3 py-2 fw-semibold text-muted">Vehículo</th>
                                    <th class="px-3 py-2 fw-semibold text-muted">Entrega</th>
                                    <th class="px-3 py-2 fw-semibold text-muted">Costo</th>
                                    <th class="px-3 py-2 fw-semibold text-muted">Estado</th>
                                    <th class="px-3 py-2"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recientes as $renta)
                                <tr>
                                    <td class="px-3 py-2 text-muted">{{ $renta->id }}</td>
                                    <td class="px-3 py-2">
                                        <p class="m-0 fw-semibold">{{ $renta->nombre_completo }}</p>
                                        <p class="m-0 text-muted" style="font-size:0.75rem;">{{ $renta->correo }}</p>
                                    </td>
                                    <td class="px-3 py-2">{{ $renta->vehicle->name ?? '—' }}</td>
                                    <td class="px-3 py-2 text-nowrap">{{ \Carbon\Carbon::parse($renta->fecha_entrega)->format('d/m/Y') }}</td>
                                    <td class="px-3 py-2 fw-semibold">${{ number_format($renta->costo_total, 0) }}</td>
                                    <td class="px-3 py-2">
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
                                    </td>
                                    <td class="px-3 py-2">
                                        <a href="{{ route('rentas.show', $renta->id) }}" class="boton_link_sm b_sm rounded">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4 fs-6">No hay solicitudes registradas.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ENTREGAS PRÓXIMAS --}}
            <div class="col-12 col-lg-4 p-1">
                <div class="cont_base rounded h-100">
                    <div class="col-12 d-flex align-items-center justify-content-between p-3 bg_gris_8">
                        <h2 class="fs-6 fw-bold m-0"><i class="bi bi-calendar-event me-2"></i>Entregas próximas</h2>
                        <span class="badge bg-secondary" style="font-size:0.72rem;">Hoy + 3 días</span>
                    </div>
                    <div class="p-2">
                        @forelse($proximas as $renta)
                        <a href="{{ route('rentas.show', $renta->id) }}" class="text-decoration-none">
                            <div class="d-flex align-items-start gap-2 p-2 rounded mb-1"
                                 style="background:#f8f9fa; transition:background 0.2s;"
                                 onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='#f8f9fa'">
                                {{-- Fecha --}}
                                <div style="min-width:42px;background:{{ \Carbon\Carbon::parse($renta->fecha_entrega)->isToday() ? '#f5c400' : '#111' }};color:{{ \Carbon\Carbon::parse($renta->fecha_entrega)->isToday() ? '#111' : '#fff' }};border-radius:6px;padding:4px;text-align:center;">
                                    <p class="m-0 fw-bold" style="font-size:1rem;line-height:1;">{{ \Carbon\Carbon::parse($renta->fecha_entrega)->format('d') }}</p>
                                    <p class="m-0" style="font-size:0.62rem;text-transform:uppercase;">{{ \Carbon\Carbon::parse($renta->fecha_entrega)->format('M') }}</p>
                                </div>
                                {{-- Info --}}
                                <div style="overflow:hidden;">
                                    <p class="m-0 fw-semibold text-dark" style="font-size:0.82rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                        {{ $renta->nombre_completo }}
                                    </p>
                                    <p class="m-0 text-muted" style="font-size:0.75rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                        {{ $renta->vehicle->name ?? '—' }}
                                    </p>
                                    <p class="m-0 text-muted" style="font-size:0.72rem;">
                                        <i class="bi bi-clock"></i> {{ substr($renta->hora_entrega, 0, 5) }}
                                        · {{ $renta->lugar_entrega }}
                                    </p>
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="p-3 text-center text-muted fs-6">
                            <i class="bi bi-calendar-check fs-3 d-block mb-2"></i>
                            Sin entregas en los próximos 3 días.
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
