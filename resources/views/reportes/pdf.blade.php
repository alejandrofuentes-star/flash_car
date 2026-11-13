{{--
    Reporte ejecutivo en PDF (Dompdf). Mismos datos que reportes/index (ReporteController::datosReporte()).
    Dompdf no soporta flex/grid ni Bootstrap: el acomodo es con tablas y CSS simple.
    Las gráficas llegan como imágenes PNG en base64 desde el navegador ($graficas[clave]).
--}}
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reporte Flash Car {{ $desde->format('d/m/Y') }} - {{ $hasta->format('d/m/Y') }}</title>
<style>
    @page { margin: 90px 32px 50px 32px; }
    * { font-family: 'DejaVu Sans', sans-serif; }
    body { font-size: 9px; color: #212529; margin: 0; }

    header { position: fixed; top: -72px; left: 0; right: 0; height: 60px; border-bottom: 2px solid #f0b429; }
    header table { width: 100%; border-collapse: collapse; }
    header td { vertical-align: middle; padding: 0; }
    header .titulo { font-size: 14px; font-weight: bold; margin: 0; }
    header .subtitulo { font-size: 9px; color: #6c757d; margin: 2px 0 0 0; }

    footer { position: fixed; bottom: -34px; left: 0; right: 0; height: 20px; border-top: 1px solid #dee2e6;
             font-size: 7.5px; color: #6c757d; padding-top: 4px; }

    h2 { font-size: 10px; text-transform: uppercase; color: #495057; margin: 10px 0 4px 0; padding-bottom: 2px;
         border-bottom: 1px solid #dee2e6; }
    h2 .nota { font-size: 7.5px; text-transform: none; font-weight: normal; color: #6c757d; }

    table.kpis { width: 100%; border-collapse: separate; border-spacing: 4px; margin: 0 -4px; }
    table.kpis td.kpi { width: 33.33%; vertical-align: top; background: #f8f9fa; border: 1px solid #e9ecef;
                        border-left-width: 4px; padding: 5px 6px; }
    .kpi_titulo { font-size: 7.5px; color: #6c757d; margin: 0; }
    .kpi_valor { font-size: 13px; font-weight: bold; margin: 1px 0; }
    .kpi_unidad { font-size: 7.5px; font-weight: normal; color: #6c757d; }
    .kpi_detalle { font-size: 7.5px; margin: 0; }
    .kpi_ayuda { font-size: 6.5px; color: #adb5bd; margin: 2px 0 0 0; }
    .verde { color: #198754; }
    .rojo { color: #dc3545; }
    .gris { color: #6c757d; }

    table.graficas { width: 100%; border-collapse: separate; border-spacing: 4px; margin: 0 -4px; }
    table.graficas td { width: 50%; vertical-align: top; border: 1px solid #e9ecef; padding: 5px; }
    .grafica_titulo { font-size: 9px; font-weight: bold; margin: 0; }
    .grafica_nota { font-size: 7px; color: #6c757d; margin: 0 0 4px 0; }
    .grafica_img { width: 100%; }
    .sin_grafica { color: #adb5bd; text-align: center; padding: 40px 0; }

    table.tops { width: 100%; border-collapse: separate; border-spacing: 4px; margin: 0 -4px; }
    table.tops > tbody > tr > td { width: 50%; vertical-align: top; }
    table.ranking { width: 100%; border-collapse: collapse; border: 1px solid #e9ecef; }
    table.ranking th { background: #343a40; color: #fff; font-size: 8px; text-align: left; padding: 4px 5px; }
    table.ranking td { border-top: 1px solid #e9ecef; padding: 3px 5px; font-size: 8px; }
    table.ranking td.pos { width: 12px; color: #6c757d; }
    table.ranking td.valor { text-align: right; font-weight: bold; white-space: nowrap; }
    table.ranking .det { color: #6c757d; font-size: 7px; }
    .vacio { color: #adb5bd; text-align: center; }

    .salto { page-break-before: always; }
    .sin_corte { page-break-inside: avoid; }
</style>
</head>
<body>

@php
    $dinero = fn ($monto, $decimales = 0) => $monto === null ? '—' : '$' . number_format($monto, $decimales);
    $nombresPeriodo = ['7d' => 'Últimos 7 días', '30d' => 'Últimos 30 días', 'mes' => 'Mes', 'anio' => 'Año', 'personalizado' => 'Rango personalizado'];
    $claseVariacion = fn ($v) => $v === null ? 'gris' : ($v >= 0 ? 'verde' : 'rojo');
    $flecha = fn ($v) => $v === null ? '' : ($v >= 0 ? '▲ ' : '▼ ');

    // Mismos KPIs y fórmulas que reportes/index (ahí la fórmula es un tooltip; aquí va escrita)
    $secciones = [
        ['titulo' => 'Flota y ocupación', 'nota' => null, 'kpis' => [
            ['titulo' => 'Flota activa', 'valor' => $vehiculosActivos, 'unidad' => 'de ' . $totalVehiculos . ' registrados',
             'detalle' => $flotaDisponibleHoy . ' disponibles para rentar ahora', 'color' => '#0d6efd',
             'ayuda' => 'Vehículos activos hoy. Ocupación y utilización usan la flota de cada día según el historial de disponibilidad.'],
            ['titulo' => 'Ocupación del último día', 'valor' => $ocupacionUltimoDia['porcentaje'] . '%',
             'detalle' => $ocupacionUltimoDia['rentados'] . ' de ' . $ocupacionUltimoDia['flota'] . ' autos'
                 . ($ocupacionUltimoDia['fecha'] ? ' · ' . $ocupacionUltimoDia['fecha']->format('d/m/Y') : ' · el periodo aún no empieza'),
             'color' => '#0d6efd', 'ayuda' => 'Vehículos rentados ese día / flota disponible ese día x 100'],
            ['titulo' => 'Utilización del periodo', 'valor' => $porcentajeUtilizacion . '%',
             'detalle' => $variacionUtilizacion !== null ? $flecha($variacionUtilizacion) . abs($variacionUtilizacion) . ' pts vs. periodo anterior (' . $utilizacionAnterior . '%)' : 'El periodo aún no empieza',
             'detalleClase' => $claseVariacion($variacionUtilizacion), 'color' => '#28a745',
             'ayuda' => 'Vehículo-días rentados / vehículo-días disponibles x 100'],
            ['titulo' => 'Capacidad del periodo', 'valor' => number_format($diasDisponibles), 'unidad' => 'vehículo-días',
             'detalle' => 'promedio ' . $flotaPromedio . ' autos x ' . $diasTranscurridos . ' días' . ($diasTranscurridos < $diasPeriodo ? ' (hasta hoy)' : ''),
             'ayuda' => 'Suma de los autos en flota de cada día del rango que ya ocurrió; los días futuros no suman capacidad'],
            ['titulo' => 'Días rentados', 'valor' => number_format($diasRentados), 'unidad' => 'vehículo-días',
             'ayuda' => 'Solo la parte de cada contrato que cae dentro del periodo; máximo 1 por auto y fecha'],
            ['titulo' => 'Vehículos utilizados', 'valor' => $vehiculosUtilizados, 'unidad' => 'de ' . $flotaDelPeriodo,
             'detalle' => 'con al menos 1 día rentado', 'ayuda' => 'Autos distintos con al menos un día rentado dentro del periodo'],
        ]],
        ['titulo' => 'Ingresos', 'nota' => 'importes tal como se cobran (el sistema no desglosa IVA)', 'kpis' => [
            ['titulo' => 'Ingreso devengado', 'valor' => $dinero($ingresos), 'unidad' => 'MXN',
             'detalle' => $variacionIngresos !== null
                 ? $flecha($variacionIngresos) . abs($variacionIngresos) . '% vs. ' . ($diasTranscurridos < $diasPeriodo ? 'mismos días del periodo anterior' : 'periodo anterior')
                 : 'Sin datos del periodo anterior',
             'detalleClase' => $claseVariacion($variacionIngresos), 'color' => '#6f42c1',
             'ayuda' => 'Total generado de cada renta efectiva repartido entre sus días; al periodo le toca lo de los días rentados dentro del rango (hasta hoy)'],
            ['titulo' => 'Ventas del periodo', 'valor' => $dinero($ventasPeriodo), 'unidad' => 'MXN',
             'detalle' => $rentasConcretadas . ' ' . ($rentasConcretadas === 1 ? 'contrato' : 'contratos') . ' · por fecha de reserva', 'color' => '#6f42c1',
             'ayuda' => 'Total generado de las rentas efectivas creadas (reservadas) en el periodo'],
            ['titulo' => 'Ticket promedio', 'valor' => $dinero($ticketPromedio, 2), 'unidad' => $ticketPromedio !== null ? 'por contrato' : '',
             'color' => '#6f42c1', 'ayuda' => 'Ventas del periodo / contratos concretados'],
            ['titulo' => 'Ingreso por día rentado', 'valor' => $dinero($ingresoPorDiaRentado, 2), 'unidad' => $ingresoPorDiaRentado !== null ? 'por vehículo-día' : '',
             'color' => '#6f42c1', 'ayuda' => 'Ingreso devengado / vehículo-días rentados'],
            ['titulo' => 'Ingreso por vehículo disponible', 'valor' => $dinero($ingresoPorVehiculo, 2), 'unidad' => $ingresoPorVehiculo !== null ? 'por auto' : '',
             'color' => '#6f42c1', 'ayuda' => 'Ingreso devengado / flota promedio del periodo (capacidad / días transcurridos)'],
        ]],
        ['titulo' => 'Comercial', 'nota' => null, 'kpis' => [
            ['titulo' => 'Demanda recibida', 'valor' => $demandaRecibida, 'unidad' => 'reservas',
             'detalle' => $solicitudesNoAtendidas > 0 ? 'Incluye ' . $solicitudesNoAtendidas . ' solicitudes no atendidas' : 'Todos los estados',
             'color' => '#d39e00', 'ayuda' => 'Reservas creadas en el periodo (cualquier estado) + solicitudes rechazadas por falta de auto'],
            ['titulo' => 'Demanda en días', 'valor' => number_format($demandaDias), 'unidad' => 'días solicitados', 'color' => '#d39e00',
             'ayuda' => 'Días solicitados por las reservas creadas en el periodo + las solicitudes no atendidas'],
            ['titulo' => 'Rentas concretadas', 'valor' => $rentasConcretadas, 'unidad' => 'contratos', 'color' => '#28a745',
             'ayuda' => 'Reservas creadas en el periodo que llegaron a En renta, Finalizado, Devuelto, Daño/Faltante o Garantía pendiente'],
            ['titulo' => 'Tasa de conversión', 'valor' => $tasaConversion !== null ? $tasaConversion . '%' : '—', 'color' => '#28a745',
             'ayuda' => 'Rentas concretadas / demanda recibida x 100'],
            ['titulo' => 'Demanda no atendida', 'valor' => $demandaNoAtendida, 'unidad' => 'solicitudes · ' . number_format($diasNoAtendidos) . ' días',
             'detalle' => $solicitudesNoAtendidas . ' rechazadas sin auto · ' . $canceladasSinAuto . ' canceladas por disponibilidad', 'color' => '#dc3545',
             'ayuda' => 'Solicitudes rechazadas por falta de auto libre + rentas canceladas con motivo "falta de disponibilidad"'],
            ['titulo' => 'Clientes únicos', 'valor' => $clientesUnicos,
             'ayuda' => 'Clientes distintos (por correo, o teléfono si no hay correo) con renta concretada'],
            ['titulo' => 'Rentas por cliente', 'valor' => $rentasPorCliente ?? '—', 'ayuda' => 'Contratos concretados / clientes únicos'],
            ['titulo' => 'Duración promedio', 'valor' => $duracionPromedio ?? '—', 'unidad' => $duracionPromedio !== null ? 'días por contrato' : '',
             'ayuda' => 'Días de los contratos concretados / contratos concretados'],
            ['titulo' => 'Categoría más rentada', 'valor' => $categoriaMasRentada['nombre'] ?? '—',
             'detalle' => $categoriaMasRentada ? $categoriaMasRentada['dias'] . ' días rentados' : null,
             'ayuda' => 'Categoría con más vehículo-días rentados dentro del periodo'],
        ]],
    ];

    $tarjetasGraficas = [
        'ocupacion'   => ['Ocupación diaria (%)', ($series['mensual'] ? 'Promedio diario de cada mes' : 'Autos rentados ese día') . ' / flota disponible ese día'],
        'utilizacion' => ['Utilización acumulada (%)', 'Vehículo-días rentados / vehículo-días disponibles desde el inicio del periodo'],
        'ingresos'    => ['Ingresos (MXN)', 'Devengado por día rentado vs. ventas por fecha de reserva'],
        'demanda'     => ['Demanda: recibida, concretada y no atendida', 'Reservas y solicitudes por fecha de creación'],
    ];

    $formatoRanking = [
        'dias'        => fn ($i) => $i['dias'] . ' ' . ($i['dias'] === 1 ? 'día' : 'días'),
        'utilizacion' => fn ($i) => $i['utilizacion'] . '%',
        'ingreso'     => fn ($i) => '$' . number_format($i['ingreso'], 0),
        'contratos'   => fn ($i) => $i['contratos'] . ' ' . ($i['contratos'] === 1 ? 'contrato' : 'contratos'),
        'no_atendida' => fn ($i) => $i['no_atendida'] . ' ' . ($i['no_atendida'] === 1 ? 'día' : 'días'),
    ];
    $titulosTipo = ['vehiculos' => 'vehículos', 'categorias' => 'categorías'];
@endphp

<header>
    <table>
        <tr>
            <td style="width:130px;">
                @if($logo)
                    <img src="{{ $logo }}" style="width:115px;" alt="Flash Car">
                @else
                    <span style="font-size:16px; font-weight:bold;">Flash Car</span>
                @endif
            </td>
            <td>
                <p class="titulo">Reporte ejecutivo</p>
                <p class="subtitulo">
                    {{ $nombresPeriodo[$periodo] ?? 'Periodo' }}: {{ $desde->format('d/m/Y') }} — {{ $hasta->format('d/m/Y') }}
                    · {{ $nombreCategoria ? 'Categoría: ' . $nombreCategoria : 'Todas las categorías' }}
                </p>
            </td>
            <td style="text-align:right; width:170px;">
                <p class="subtitulo">Generado el {{ $generadoEn->format('d/m/Y H:i') }}</p>
                @if($generadoPor)<p class="subtitulo">por {{ $generadoPor }}</p>@endif
            </td>
        </tr>
    </table>
</header>

<footer>
    <span>Flash Car · Reporte ejecutivo · {{ $desde->format('d/m/Y') }} — {{ $hasta->format('d/m/Y') }}</span>
</footer>

{{-- ===== KPIs ===== --}}
@foreach($secciones as $seccion)
    <div class="sin_corte">
        <h2>{{ $seccion['titulo'] }} @if($seccion['nota'])<span class="nota">· {{ $seccion['nota'] }}</span>@endif</h2>
        <table class="kpis">
            @foreach(array_chunk($seccion['kpis'], 3) as $fila)
                <tr>
                    @foreach($fila as $kpi)
                        <td class="kpi" style="border-left-color: {{ $kpi['color'] ?? '#adb5bd' }};">
                            <p class="kpi_titulo">{{ $kpi['titulo'] }}</p>
                            <p class="kpi_valor">
                                {{ $kpi['valor'] }}
                                @if(!empty($kpi['unidad']))<span class="kpi_unidad">{{ $kpi['unidad'] }}</span>@endif
                            </p>
                            @if(!empty($kpi['detalle']))
                                <p class="kpi_detalle {{ $kpi['detalleClase'] ?? 'gris' }}">{{ $kpi['detalle'] }}</p>
                            @endif
                            <p class="kpi_ayuda">{{ $kpi['ayuda'] }}</p>
                        </td>
                    @endforeach
                    @for($i = count($fila); $i < 3; $i++)
                        <td></td>
                    @endfor
                </tr>
            @endforeach
        </table>
    </div>
@endforeach

{{-- ===== GRÁFICAS ===== --}}
<div class="salto"></div>
<h2>Gráficas de tendencia</h2>
<table class="graficas">
    @foreach(array_chunk($tarjetasGraficas, 2, true) as $fila)
        <tr>
            @foreach($fila as $clave => [$titulo, $nota])
                <td>
                    <p class="grafica_titulo">{{ $titulo }}</p>
                    <p class="grafica_nota">{{ $nota }}</p>
                    @if(!empty($graficas[$clave]))
                        <img src="{{ $graficas[$clave] }}" class="grafica_img" alt="{{ $titulo }}">
                    @else
                        <p class="sin_grafica">Gráfica no disponible</p>
                    @endif
                </td>
            @endforeach
        </tr>
    @endforeach
</table>

{{-- ===== TOP 5 (todos los criterios) ===== --}}
@foreach($rankings as $tipo => $porCriterio)
    <h2 @if($loop->first) style="margin-top:14px;" @endif>Top 5 {{ $titulosTipo[$tipo] }}</h2>
    <table class="tops">
        @foreach(array_chunk($porCriterio, 2, true) as $fila)
            <tr>
                @foreach($fila as $criterio => $items)
                    <td>
                        <table class="ranking sin_corte">
                            <tr><th colspan="3">{{ $criteriosRanking[$criterio] ?? $criterio }}</th></tr>
                            @forelse($items as $item)
                                <tr>
                                    <td class="pos">{{ $loop->iteration }}</td>
                                    <td>
                                        {{ $item['nombre'] }}
                                        @if($item['detalle'])<br><span class="det">{{ $item['detalle'] }}</span>@endif
                                    </td>
                                    <td class="valor">{{ $formatoRanking[$criterio]($item) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="vacio">Sin datos en el periodo</td></tr>
                            @endforelse
                        </table>
                    </td>
                @endforeach
                @if(count($fila) < 2)<td></td>@endif
            </tr>
        @endforeach
    </table>
@endforeach

</body>
</html>
