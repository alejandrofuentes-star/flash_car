@extends('layout.layouts')

@section('title', 'Reportes - Flash Car')

@section('content')
@include('layout.header')
<div class="bg-light min-vh-100">
    <div class="main_principal">
        <div class="space_principal">
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->

        @if(session('error'))
            <div class="messenger_alert">
                <div class="dialog_alert messenger py-2 px-4 rounded" style="background:#dc3545;">
                    <div class="fs-6 text-white"><b>{{ session('error') }}</b></div>
                </div>
            </div>
        @endif

        {{-- ===== FILTROS ===== --}}
        <div class="col-12 d-flex align-items-center justify-content-start flex-wrap rounded cont_base my-2">
            <div class="col-12 d-flex align-items-center justify-content-between p-2 bg_gris_8">
                <h1 class="fs-6 text_uppcase m-0"><i class="bi bi-graph-up me-2"></i>Reportes</h1>
                <span class="text-muted" style="font-size:0.78rem;">
                    {{ $desde->format('d/m/Y') }} — {{ $hasta->format('d/m/Y') }}
                    @if($categoriaFiltro) · {{ $categorias->firstWhere('id', $categoriaFiltro)->name }} @endif
                </span>
            </div>
            <form method="GET" action="{{ route('reportes.index') }}" class="row align-items-end p-2 w-100" id="formFiltrosReportes">
                <div class="col-12 col-sm-6 col-md-2 px-1 my-1">
                    <label class="label_form_f_b" style="padding-left:5px;">Periodo</label>
                    <select name="periodo" id="select_periodo" class="input_form_f_b w-100">
                        <option value="7d" {{ $periodo === '7d' ? 'selected' : '' }}>Últimos 7 días</option>
                        <option value="30d" {{ $periodo === '30d' ? 'selected' : '' }}>Últimos 30 días</option>
                        <option value="mes" {{ $periodo === 'mes' ? 'selected' : '' }}>Mes</option>
                        <option value="anio" {{ $periodo === 'anio' ? 'selected' : '' }}>Año</option>
                        <option value="personalizado" {{ $periodo === 'personalizado' ? 'selected' : '' }}>Rango personalizado</option>
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-2 px-1 my-1 campo_periodo campo_periodo_mes" style="display:none;">
                    <label class="label_form_f_b">Mes</label>
                    <input type="month" name="mes_valor" value="{{ request('mes_valor', now()->format('Y-m')) }}" class="input_form_f_b w-100">
                </div>

                <div class="col-12 col-sm-6 col-md-2 px-1 my-1 campo_periodo campo_periodo_anio" style="display:none;">
                    <label class="label_form_f_b">Año</label>
                    <input type="number" name="anio_valor" value="{{ request('anio_valor', now()->year) }}" min="2020" max="{{ now()->year }}" class="input_form_f_b w-100">
                </div>

                <div class="col-12 col-sm-6 col-md-2 px-1 my-1 campo_periodo campo_periodo_personalizado" style="display:none;">
                    <label class="label_form_f_b">Desde</label>
                    <input type="date" name="desde" value="{{ request('desde') }}" class="input_form_f_b w-100">
                </div>

                <div class="col-12 col-sm-6 col-md-2 px-1 my-1 campo_periodo campo_periodo_personalizado" style="display:none;">
                    <label class="label_form_f_b">Hasta</label>
                    <input type="date" name="hasta" value="{{ request('hasta') }}" class="input_form_f_b w-100">
                </div>

                <div class="col-12 col-sm-6 col-md-2 px-1 my-1">
                    <label class="label_form_f_b">Categoría</label>
                    <select name="categoria" class="input_form_f_b w-100">
                        <option value="">Todas las categorías</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}" {{ $categoriaFiltro === $categoria->id ? 'selected' : '' }}>{{ $categoria->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-2 px-1 my-1 d-flex align-items-center" style="gap:8px;">
                    <button type="submit" class="boton_link_lg rounded" title="Aplicar filtros"><i class="bi bi-funnel-fill me-1"></i>Aplicar</button>
                    @if($periodo !== '30d' || $categoriaFiltro)
                        <a href="{{ route('reportes.index') }}" class="boton_link_sm b_sm rounded link_decoration_none display_flex_center_center" title="Limpiar filtros"><i class="bi bi-x-lg"></i></a>
                    @endif
                </div>
            </form>

            {{-- Descarga en PDF con los filtros ya aplicados (query string); las gráficas se mandan como imagen desde JS --}}
            <form method="POST" action="{{ route('reportes.pdf', request()->query(), false) }}" id="formReportePdf" class="col-12 d-flex justify-content-end px-2 pb-2">
                @csrf
                @foreach(['ocupacion', 'utilizacion', 'ingresos', 'demanda'] as $clave)
                    <input type="hidden" name="graficas[{{ $clave }}]" id="pdf_grafica_{{ $clave }}">
                @endforeach
                <button type="submit" class="boton_link_lg rounded" id="boton_reporte_pdf" style="width:auto; padding:0 14px;" title="Descargar el reporte con los filtros aplicados">
                    <i class="bi bi-file-earmark-pdf-fill me-1"></i>Descargar PDF
                </button>
            </form>
        </div>

        {{-- ===== KPIs (izquierda) + TOP 5 (derecha) ===== --}}
        <div class="col-12 d-flex align-items-stretch justify-content-start flex-wrap">

            {{-- Columna izquierda: todas las métricas --}}
            @php
                $dinero = fn ($monto, $decimales = 0) => $monto === null ? '—' : '$' . number_format($monto, $decimales);
                $tituloSeccion = 'col-12 px-1 pt-1 m-0 text-muted fw-bold text-uppercase';
            @endphp
            <div class="col-12 col-md-8 d-flex align-items-stretch justify-content-start flex-wrap align-content-start p-1">

                {{-- Flota y ocupación --}}
                <p class="{{ $tituloSeccion }}" style="font-size:0.7rem;"><i class="bi bi-car-front-fill me-1"></i>Flota y ocupación</p>

                @include('reportes.partials.kpi', [
                    'titulo'  => 'Flota activa',
                    'valor'   => $vehiculosActivos,
                    'unidad'  => 'de ' . $totalVehiculos . ' registrados',
                    'detalle' => $flotaDisponibleHoy . ' disponibles para rentar ahora',
                    'color'   => '#0d6efd',
                    'ayuda'   => 'Vehículos activos hoy. Ocupación y utilización usan la flota de cada día según el historial de disponibilidad.',
                ])

                @include('reportes.partials.kpi', [
                    'titulo'  => 'Ocupación del último día',
                    'valor'   => $ocupacionUltimoDia['porcentaje'] . '%',
                    'detalle' => $ocupacionUltimoDia['rentados'] . ' de ' . $ocupacionUltimoDia['flota'] . ' autos'
                        . ($ocupacionUltimoDia['fecha'] ? ' · ' . $ocupacionUltimoDia['fecha']->format('d/m/Y') : ' · el periodo aún no empieza'),
                    'color'   => '#0d6efd',
                    'ayuda'   => 'Vehículos rentados ese día / flota disponible ese día x 100',
                ])

                @include('reportes.partials.kpi', [
                    'titulo'       => 'Utilización del periodo',
                    'valor'        => $porcentajeUtilizacion . '%',
                    'detalle'      => $variacionUtilizacion !== null ? abs($variacionUtilizacion) . ' pts vs. periodo anterior' : 'El periodo aún no empieza',
                    'detalleClase' => $variacionUtilizacion === null ? 'text-muted' : ($variacionUtilizacion >= 0 ? 'text-success' : 'text-danger'),
                    'detalleIcono' => $variacionUtilizacion === null ? null : 'bi-arrow-' . ($variacionUtilizacion >= 0 ? 'up' : 'down'),
                    'color'        => '#28a745',
                    'ayuda'        => 'Vehículo-días rentados / vehículo-días disponibles x 100',
                ])

                @include('reportes.partials.kpi', [
                    'titulo'  => 'Capacidad del periodo',
                    'valor'   => number_format($diasDisponibles),
                    'unidad'  => 'vehículo-días',
                    'detalle' => 'promedio ' . $flotaPromedio . ' autos x ' . $diasTranscurridos . ' días'
                        . ($diasTranscurridos < $diasPeriodo ? ' (hasta hoy)' : ''),
                    'ayuda'   => 'Suma de los autos en flota de cada día del rango que ya ocurrió (historial de disponibilidad); los días futuros no suman capacidad',
                ])

                @include('reportes.partials.kpi', [
                    'titulo' => 'Días rentados',
                    'valor'  => number_format($diasRentados),
                    'unidad' => 'vehículo-días',
                    'ayuda'  => 'Solo la parte de cada contrato que cae dentro del periodo; máximo 1 por auto y fecha',
                ])

                @include('reportes.partials.kpi', [
                    'titulo'  => 'Vehículos utilizados',
                    'valor'   => $vehiculosUtilizados,
                    'unidad'  => 'de ' . $flotaDelPeriodo,
                    'detalle' => 'con al menos 1 día rentado',
                    'ayuda'   => 'Autos distintos con al menos un día rentado dentro del periodo',
                ])

                {{-- Ingresos --}}
                <p class="{{ $tituloSeccion }}" style="font-size:0.7rem;">
                    <i class="bi bi-cash-stack me-1"></i>Ingresos
                    <span class="fw-normal text-lowercase">· importes tal como se cobran (el sistema no desglosa IVA)</span>
                </p>

                @include('reportes.partials.kpi', [
                    'titulo'       => 'Ingreso devengado',
                    'valor'        => $dinero($ingresos),
                    'unidad'       => 'MXN',
                    'detalle'      => $variacionIngresos !== null
                        ? abs($variacionIngresos) . '% vs. ' . ($diasTranscurridos < $diasPeriodo ? 'mismos días del periodo anterior' : 'periodo anterior')
                        : 'Sin datos del periodo anterior',
                    'detalleClase' => $variacionIngresos === null ? 'text-muted' : ($variacionIngresos >= 0 ? 'text-success' : 'text-danger'),
                    'detalleIcono' => $variacionIngresos === null ? null : 'bi-arrow-' . ($variacionIngresos >= 0 ? 'up' : 'down'),
                    'color'        => '#6f42c1',
                    'ayuda'        => 'Total generado de cada renta efectiva repartido entre sus días; al periodo le toca lo de los días rentados dentro del rango (hasta hoy)',
                ])

                @include('reportes.partials.kpi', [
                    'titulo'  => 'Ventas del periodo',
                    'valor'   => $dinero($ventasPeriodo),
                    'unidad'  => 'MXN',
                    'detalle' => $rentasConcretadas . ' ' . ($rentasConcretadas === 1 ? 'contrato' : 'contratos') . ' · por fecha de reserva',
                    'color'   => '#6f42c1',
                    'ayuda'   => 'Total generado de las rentas efectivas creadas (reservadas) en el periodo, completo aunque el contrato siga después',
                ])

                @include('reportes.partials.kpi', [
                    'titulo' => 'Ticket promedio',
                    'valor'  => $dinero($ticketPromedio, 2),
                    'unidad' => $ticketPromedio !== null ? 'por contrato' : '',
                    'color'  => '#6f42c1',
                    'ayuda'  => 'Ventas del periodo / contratos concretados',
                ])

                @include('reportes.partials.kpi', [
                    'titulo' => 'Ingreso por día rentado',
                    'valor'  => $dinero($ingresoPorDiaRentado, 2),
                    'unidad' => $ingresoPorDiaRentado !== null ? 'por vehículo-día' : '',
                    'color'  => '#6f42c1',
                    'ayuda'  => 'Ingreso devengado / vehículo-días rentados',
                ])

                @include('reportes.partials.kpi', [
                    'titulo' => 'Ingreso por vehículo disponible',
                    'valor'  => $dinero($ingresoPorVehiculo, 2),
                    'unidad' => $ingresoPorVehiculo !== null ? 'por auto' : '',
                    'color'  => '#6f42c1',
                    'ayuda'  => 'Ingreso devengado / flota promedio del periodo (capacidad / días transcurridos)',
                ])

                {{-- Comercial --}}
                <p class="{{ $tituloSeccion }}" style="font-size:0.7rem;"><i class="bi bi-people-fill me-1"></i>Comercial</p>

                @include('reportes.partials.kpi', [
                    'titulo'  => 'Demanda recibida',
                    'valor'   => $demandaRecibida,
                    'unidad'  => 'reservas',
                    'detalle' => $solicitudesNoAtendidas > 0 ? 'Incluye ' . $solicitudesNoAtendidas . ' solicitudes no atendidas' : 'Todos los estados',
                    'color'   => 'var(--amarillo_fuerte)',
                    'ayuda'   => 'Reservas creadas en el periodo (cualquier estado) + solicitudes rechazadas por falta de auto',
                ])

                @include('reportes.partials.kpi', [
                    'titulo' => 'Demanda en días',
                    'valor'  => number_format($demandaDias),
                    'unidad' => 'días solicitados',
                    'color'  => 'var(--amarillo_fuerte)',
                    'ayuda'  => 'Días solicitados por las reservas creadas en el periodo + las solicitudes no atendidas',
                ])

                @include('reportes.partials.kpi', [
                    'titulo' => 'Rentas concretadas',
                    'valor'  => $rentasConcretadas,
                    'unidad' => 'contratos',
                    'color'  => '#28a745',
                    'ayuda'  => 'Reservas creadas en el periodo que llegaron a En renta, Finalizado, Devuelto, Daño/Faltante o Garantía pendiente',
                ])

                @include('reportes.partials.kpi', [
                    'titulo' => 'Tasa de conversión',
                    'valor'  => $tasaConversion !== null ? $tasaConversion . '%' : '—',
                    'color'  => '#28a745',
                    'ayuda'  => 'Rentas concretadas / demanda recibida x 100',
                ])

                @include('reportes.partials.kpi', [
                    'titulo'  => 'Demanda no atendida',
                    'valor'   => $demandaNoAtendida,
                    'unidad'  => 'solicitudes · ' . number_format($diasNoAtendidos) . ' días',
                    'detalle' => $solicitudesNoAtendidas . ' rechazadas sin auto · ' . $canceladasSinAuto . ' canceladas por disponibilidad',
                    'color'   => '#dc3545',
                    'ayuda'   => 'Solicitudes rechazadas por falta de auto libre + rentas canceladas con motivo "falta de disponibilidad"',
                ])

                @include('reportes.partials.kpi', [
                    'titulo' => 'Clientes únicos',
                    'valor'  => $clientesUnicos,
                    'ayuda'  => 'Clientes distintos (por correo, o teléfono si no hay correo) con renta concretada',
                ])

                @include('reportes.partials.kpi', [
                    'titulo' => 'Rentas por cliente',
                    'valor'  => $rentasPorCliente ?? '—',
                    'ayuda'  => 'Contratos concretados / clientes únicos',
                ])

                @include('reportes.partials.kpi', [
                    'titulo' => 'Duración promedio',
                    'valor'  => $duracionPromedio ?? '—',
                    'unidad' => $duracionPromedio !== null ? 'días por contrato' : '',
                    'ayuda'  => 'Días de los contratos concretados / contratos concretados',
                ])

                @include('reportes.partials.kpi', [
                    'titulo'  => 'Categoría más rentada',
                    'valor'   => $categoriaMasRentada['nombre'] ?? '—',
                    'detalle' => $categoriaMasRentada ? $categoriaMasRentada['dias'] . ' días rentados' : null,
                    'ayuda'   => 'Categoría con más vehículo-días rentados dentro del periodo',
                ])

            </div>

            {{-- Columna derecha: Top 5 de vehículos / categorías, alternable por criterio --}}
            @php
                $formatoRanking = [
                    'dias'        => fn ($i) => $i['dias'] . ' ' . ($i['dias'] === 1 ? 'día' : 'días'),
                    'utilizacion' => fn ($i) => $i['utilizacion'] . '%',
                    'ingreso'     => fn ($i) => '$' . number_format($i['ingreso'], 0),
                    'contratos'   => fn ($i) => $i['contratos'] . ' ' . ($i['contratos'] === 1 ? 'contrato' : 'contratos'),
                    'no_atendida' => fn ($i) => $i['no_atendida'] . ' ' . ($i['no_atendida'] === 1 ? 'día' : 'días'),
                ];
                $titulosTipo = ['vehiculos' => 'vehículos', 'categorias' => 'categorías'];
            @endphp
            <div class="col-12 col-md-4 p-1">
                <div class="col-12 d-flex align-items-start justify-content-start flex-wrap align-content-start rounded cont_base h-100">
                    <div class="col-12 d-flex align-items-center justify-content-between flex-wrap p-2 bg_gris_8">
                        <h2 class="fs-6 fw-bold m-0"><i class="bi bi-trophy-fill me-2"></i>Top 5 <span id="top_titulo_tipo">vehículos</span></h2>
                        <div class="btn-group btn-group-sm" role="group" aria-label="Tipo de Top 5">
                            <button type="button" class="btn btn-dark boton_top_tipo" data-tipo="vehiculos">Vehículos</button>
                            <button type="button" class="btn btn-outline-dark boton_top_tipo" data-tipo="categorias">Categorías</button>
                        </div>
                    </div>
                    <div class="col-12 px-2 pt-2">
                        <select id="select_top_criterio" class="form-select form-select-sm" aria-label="Ordenar Top 5 por">
                            @foreach($criteriosRanking as $clave => $etiqueta)
                                <option value="{{ $clave }}">{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 p-2">
                        @foreach($rankings as $tipo => $porCriterio)
                            @foreach($porCriterio as $criterio => $items)
                                <div class="lista_top" data-tipo="{{ $tipo }}" data-criterio="{{ $criterio }}"
                                     @if(!($tipo === 'vehiculos' && $criterio === 'dias')) style="display:none;" @endif>
                                    @forelse($items as $item)
                                        <div class="col-12 d-flex align-items-center justify-content-between border_gris_2_buttom py-2">
                                            <span class="d-flex align-items-center" style="min-width:0;">
                                                @if($tipo === 'vehiculos')
                                                    <img src="{{ $item['imagen'] ?? asset('./img/sin_url_auto.png') }}"
                                                         alt="{{ $item['nombre'] }}" class="rounded border flex-shrink-0 me-2"
                                                         style="width:56px; height:38px; object-fit:contain; background:#fff; padding:2px;">
                                                @else
                                                    <span class="rounded border flex-shrink-0 me-2 d-flex align-items-center justify-content-center bg-white"
                                                          style="width:56px; height:38px;"><i class="bi bi-tags-fill text-muted"></i></span>
                                                @endif
                                                <span class="d-flex flex-column" style="min-width:0;">
                                                    <span class="fs-6 text-truncate">{{ $item['nombre'] }}</span>
                                                    @if($item['detalle'])
                                                        <span class="text-muted text-truncate" style="font-size:0.68rem;">{{ $item['detalle'] }}</span>
                                                    @endif
                                                </span>
                                            </span>
                                            <b class="fs-6 text-nowrap ms-2">{{ $formatoRanking[$criterio]($item) }}</b>
                                        </div>
                                    @empty
                                        <p class="text-muted text-center py-3 m-0">Sin datos de {{ $titulosTipo[$tipo] }} en el periodo seleccionado.</p>
                                    @endforelse
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>

        </div>

        {{-- ===== GRÁFICAS DE TENDENCIA ===== --}}
        <div class="col-12 d-flex align-items-stretch justify-content-start flex-wrap mt-1">
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 p-1">
                <div class="cont_base rounded p-2 h-100">
                    <p class="fs-6 fw-bold mb-0">Ocupación diaria (%)</p>
                    <p class="text-muted mb-2" style="font-size:0.7rem;">
                        {{ $series['mensual'] ? 'Promedio diario de cada mes' : 'Autos rentados ese día' }} / flota disponible ese día
                    </p>
                    <canvas id="graficaOcupacion" height="220"></canvas>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 p-1">
                <div class="cont_base rounded p-2 h-100">
                    <p class="fs-6 fw-bold mb-0">Utilización acumulada (%)</p>
                    <p class="text-muted mb-2" style="font-size:0.7rem;">Vehículo-días rentados / vehículo-días disponibles desde el inicio del periodo</p>
                    <canvas id="graficaUtilizacion" height="220"></canvas>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 p-1">
                <div class="cont_base rounded p-2 h-100">
                    <p class="fs-6 fw-bold mb-0">Ingresos (MXN)</p>
                    <p class="text-muted mb-2" style="font-size:0.7rem;">Devengado por día rentado vs. ventas por fecha de reserva</p>
                    <canvas id="graficaIngresos" height="220"></canvas>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 p-1">
                <div class="cont_base rounded p-2 h-100">
                    <p class="fs-6 fw-bold mb-0">Demanda: recibida, concretada y no atendida</p>
                    <p class="text-muted mb-2" style="font-size:0.7rem;">Reservas y solicitudes por fecha de creación</p>
                    <canvas id="graficaDemanda" height="220"></canvas>
                </div>
            </div>
        </div>

        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
(function () {
    const selectPeriodo = document.getElementById('select_periodo');

    function actualizarCamposPeriodo() {
        document.querySelectorAll('.campo_periodo').forEach(el => el.style.display = 'none');
        const valor = selectPeriodo.value;
        if (valor === 'mes') document.querySelector('.campo_periodo_mes').style.display = 'block';
        if (valor === 'anio') document.querySelector('.campo_periodo_anio').style.display = 'block';
        if (valor === 'personalizado') document.querySelectorAll('.campo_periodo_personalizado').forEach(el => el.style.display = 'block');
    }

    selectPeriodo.addEventListener('change', actualizarCamposPeriodo);
    actualizarCamposPeriodo();

    const etiquetas   = @json($series['etiquetas']);
    const ocupacion   = @json($series['ocupacion']);
    const rentados    = @json($series['rentados']);
    const mensual     = @json($series['mensual']);
    const flota       = @json($series['flota']);
    const noAtendida  = @json($series['noAtendida']);
    const ocupacionPromedio    = @json($series['ocupacionPromedio']);
    const utilizacionAcumulada = @json($series['utilizacionAcumulada']);
    const rentadosAcumulados   = @json($series['rentadosAcumulados']);
    const capacidadAcumulada   = @json($series['capacidadAcumulada']);
    const formatoNumero = new Intl.NumberFormat('es-MX');
    const ingresos    = @json($series['ingresos']);
    const ventas      = @json($series['ventas']);
    const formatoMoneda = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN', maximumFractionDigits: 0 });
    const demanda     = @json($series['demanda']);
    const concretadas = @json($series['concretadas']);

    const estiloLinea = {
        borderColor: '#f0b429',
        backgroundColor: 'rgba(240, 180, 41, 0.15)',
        borderWidth: 2,
        tension: 0.3,
        fill: true,
        pointRadius: 2,
    };

    const opciones = {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } },
    };

    const ejePorcentaje = { y: { beginAtZero: true, min: 0, max: 100, ticks: { callback: (v) => v + '%' } } };
    const leyendaAbajo  = { display: true, position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } };

    const graficaOcupacion = new Chart(document.getElementById('graficaOcupacion'), {
        type: 'line',
        data: {
            labels: etiquetas,
            datasets: [
                { label: 'Ocupación', data: ocupacion, ...estiloLinea },
                {
                    label: `Promedio del periodo (${ocupacionPromedio}%)`,
                    data: etiquetas.map(() => ocupacionPromedio),
                    borderColor: '#6c757d',
                    borderWidth: 1.5,
                    borderDash: [6, 4],
                    pointRadius: 0,
                    pointHoverRadius: 0,
                    fill: false,
                },
            ],
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            scales: ejePorcentaje,
            plugins: {
                legend: leyendaAbajo,
                tooltip: {
                    callbacks: {
                        label: (ctx) => {
                            if (ctx.datasetIndex === 1) return `Promedio del periodo: ${ocupacionPromedio}%`;
                            const autos = mensual
                                ? `promedio ${rentados[ctx.dataIndex]} de ${flota[ctx.dataIndex]} autos por día`
                                : `${rentados[ctx.dataIndex]} de ${flota[ctx.dataIndex]} autos rentados`;
                            return `Ocupación: ${ctx.parsed.y}% (${autos})`;
                        },
                    },
                },
            },
        },
    });

    const graficaUtilizacion = new Chart(document.getElementById('graficaUtilizacion'), {
        type: 'line',
        data: {
            labels: etiquetas,
            datasets: [{
                data: utilizacionAcumulada,
                ...estiloLinea,
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.12)',
            }],
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            scales: ejePorcentaje,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => `Utilización acumulada: ${ctx.parsed.y}%`,
                        afterLabel: (ctx) => `${formatoNumero.format(rentadosAcumulados[ctx.dataIndex])} de ${formatoNumero.format(capacidadAcumulada[ctx.dataIndex])} vehículo-días`,
                    },
                },
            },
        },
    });

    const graficaIngresos = new Chart(document.getElementById('graficaIngresos'), {
        type: 'line',
        data: {
            labels: etiquetas,
            datasets: [
                { label: 'Ingreso devengado', data: ingresos, ...estiloLinea, borderColor: '#6f42c1', backgroundColor: 'rgba(111, 66, 193, 0.12)' },
                {
                    label: 'Ventas (por fecha de reserva)',
                    data: ventas,
                    borderColor: '#f0b429',
                    borderWidth: 2,
                    borderDash: [5, 3],
                    tension: 0.3,
                    fill: false,
                    pointRadius: 2,
                },
            ],
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            scales: { y: { beginAtZero: true, ticks: { callback: (v) => formatoMoneda.format(v) } } },
            plugins: {
                legend: { display: true, position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } },
                tooltip: { callbacks: { label: (ctx) => `${ctx.dataset.label}: ${formatoMoneda.format(ctx.parsed.y ?? 0)}` } },
            },
        },
    });

    const graficaDemanda = new Chart(document.getElementById('graficaDemanda'), {
        type: 'line',
        data: {
            labels: etiquetas,
            datasets: [
                {
                    label: 'Demanda recibida',
                    data: demanda,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: false,
                    pointRadius: 2,
                },
                {
                    label: 'Rentas concretadas',
                    data: concretadas,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: false,
                    pointRadius: 2,
                },
                {
                    label: 'No atendida',
                    data: noAtendida,
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    borderWidth: 2,
                    borderDash: [5, 3],
                    tension: 0.3,
                    fill: false,
                    pointRadius: 2,
                },
            ],
        },
        options: {
            responsive: true,
            plugins: { legend: { display: true, position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
        },
    });

    // PDF: cada gráfica se manda como JPEG con fondo blanco (Dompdf no ejecuta JavaScript).
    // Se reduce a máx. 1000 px de ancho y JPEG para que el envío pese poco: si el POST rebasa el
    // límite del servidor, PHP descarta el formulario completo (incluido el token CSRF).
    const graficasPdf = { ocupacion: graficaOcupacion, utilizacion: graficaUtilizacion, ingresos: graficaIngresos, demanda: graficaDemanda };

    function graficaComoImagen(grafica) {
        const origen = grafica.canvas;
        const escala = Math.min(1, 1000 / origen.width);
        const lienzo = document.createElement('canvas');
        lienzo.width  = Math.round(origen.width * escala);
        lienzo.height = Math.round(origen.height * escala);
        const ctx = lienzo.getContext('2d');
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, lienzo.width, lienzo.height);
        ctx.drawImage(origen, 0, 0, lienzo.width, lienzo.height);
        return lienzo.toDataURL('image/jpeg', 0.9);
    }

    document.getElementById('formReportePdf').addEventListener('submit', () => {
        Object.entries(graficasPdf).forEach(([clave, grafica]) => {
            try {
                document.getElementById('pdf_grafica_' + clave).value = graficaComoImagen(grafica);
            } catch (e) {
                document.getElementById('pdf_grafica_' + clave).value = '';
            }
        });
        // La respuesta es una descarga (la página no cambia): se bloquea el botón unos segundos
        const boton = document.getElementById('boton_reporte_pdf');
        boton.disabled = true;
        boton.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Generando…';
        setTimeout(() => {
            boton.disabled = false;
            boton.innerHTML = '<i class="bi bi-file-earmark-pdf-fill me-1"></i>Descargar PDF';
        }, 6000);
    });

    // Top 5: alternar vehículos/categorías y criterio sin recargar
    const selectCriterio = document.getElementById('select_top_criterio');
    const titulosTipo    = { vehiculos: 'vehículos', categorias: 'categorías' };
    let tipoTop = 'vehiculos';

    function mostrarTop() {
        document.querySelectorAll('.lista_top').forEach(el => {
            el.style.display = (el.dataset.tipo === tipoTop && el.dataset.criterio === selectCriterio.value) ? '' : 'none';
        });
        document.querySelectorAll('.boton_top_tipo').forEach(boton => {
            const activo = boton.dataset.tipo === tipoTop;
            boton.classList.toggle('btn-dark', activo);
            boton.classList.toggle('btn-outline-dark', !activo);
        });
        document.getElementById('top_titulo_tipo').textContent = titulosTipo[tipoTop];
    }

    document.querySelectorAll('.boton_top_tipo').forEach(boton => {
        boton.addEventListener('click', () => { tipoTop = boton.dataset.tipo; mostrarTop(); });
    });
    selectCriterio.addEventListener('change', mostrarTop);
})();
</script>
@endpush
@endsection
