{{--
    Tarjeta de KPI del módulo de reportes.
    Parámetros: titulo, valor, unidad (opcional), detalle (opcional), detalleClase (opcional, default text-muted),
    detalleIcono (opcional, clase bi-*), color (opcional, color del borde izquierdo), ayuda (opcional, tooltip con la fórmula).
--}}
<div class="col-12 col-sm-6 col-md-4 col-lg-3 p-1">
    <div class="cont_base rounded p-1 h-100" @isset($color) style="border-left: 4px solid {{ $color }};" @endisset>
        <p class="text-muted m-0" style="font-size:0.7rem;" @isset($ayuda) title="{{ $ayuda }}" @endisset>
            {{ $titulo }}
            @isset($ayuda)<i class="bi bi-info-circle ms-1"></i>@endisset
        </p>
        <p class="fs-6 fw-bold m-0">
            {{ $valor }}
            @if(!empty($unidad))<span class="fw-normal text-muted" style="font-size:0.7rem;">{{ $unidad }}</span>@endif
        </p>
        @if(!empty($detalle))
            <span class="{{ $detalleClase ?? 'text-muted' }}" style="font-size:0.68rem;">
                @if(!empty($detalleIcono))<i class="bi {{ $detalleIcono }}"></i>@endif
                {{ $detalle }}
            </span>
        @endif
    </div>
</div>
