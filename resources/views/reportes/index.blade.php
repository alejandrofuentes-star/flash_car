@extends('layout.layouts')

@section('title', 'Reportes - Flash Car')

@section('content')
@include('layout.header')
<div class="bg-light min-vh-100">
    <div class="main_principal">
        <div class="space_principal">
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->

        {{-- ===== FILTROS ===== --}}
        <div class="col-12 d-flex align-items-center justify-content-start flex-wrap rounded cont_base my-2">
            <div class="col-12 d-flex align-items-center justify-content-between p-2 bg_gris_8">
                <h1 class="fs-6 text_uppcase m-0"><i class="bi bi-graph-up me-2"></i>Reportes</h1>
                <span class="text-muted" style="font-size:0.78rem;">{{ $desde->format('d/m/Y') }} — {{ $hasta->format('d/m/Y') }}</span>
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
                    <label class="label_form_f_b">Estado (reservas)</label>
                    <select name="estado" class="input_form_f_b w-100">
                        <option value="">Todos los estados</option>
                        @foreach($estadosDisponibles as $valor => $etiqueta)
                            <option value="{{ $valor }}" {{ $estadoFiltro === $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-2 px-1 my-1 d-flex align-items-center" style="gap:8px;">
                    <button type="submit" class="boton_link_lg rounded" title="Aplicar filtros"><i class="bi bi-funnel-fill me-1"></i>Aplicar</button>
                    @if($periodo !== '30d' || $estadoFiltro)
                        <a href="{{ route('reportes.index') }}" class="boton_link_sm b_sm rounded link_decoration_none display_flex_center_center" title="Limpiar filtros"><i class="bi bi-x-lg"></i></a>
                    @endif
                </div>
            </form>
        </div>

        {{-- ===== KPIs (izquierda) + TOP 5 (derecha) ===== --}}
        <div class="col-12 d-flex align-items-stretch justify-content-start flex-wrap">

            {{-- Columna izquierda: todas las métricas --}}
            <div class="col-12 col-md-8 d-flex align-items-stretch justify-content-start flex-wrap p-1">

                <div class="col-12 col-sm-6 col-md-4 p-1">
                    <div class="cont_base rounded p-1 h-100" style="border-left: 4px solid #0d6efd;">
                        <p class="text-muted m-0" style="font-size:0.7rem;">Vehículos registrados</p>
                        <p class="fs-6 fw-bold m-0">{{ $totalVehiculos }}</p>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-4 p-1">
                    <div class="cont_base rounded p-1 h-100" style="border-left: 4px solid var(--amarillo_fuerte);">
                        <p class="text-muted m-0" style="font-size:0.7rem;">Total de reservas</p>
                        <p class="fs-6 fw-bold m-0">{{ $totalReservas }}</p>
                        <span class="text-muted" style="font-size:0.68rem;">{{ $estadoFiltro ? $estadosDisponibles[$estadoFiltro] : 'Todos los estados' }}</span>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-4 p-1">
                    <div class="cont_base rounded p-1 h-100" style="border-left: 4px solid #6f42c1;">
                        <p class="text-muted m-0" style="font-size:0.7rem;">Ingresos del periodo</p>
                        <p class="fs-6 fw-bold m-0">${{ number_format($ingresos, 0) }} <span class="fw-normal text-muted" style="font-size:0.7rem;">MXN</span></p>
                        @if($variacionIngresos !== null)
                            <span class="{{ $variacionIngresos >= 0 ? 'text-success' : 'text-danger' }}" style="font-size:0.68rem;">
                                <i class="bi bi-arrow-{{ $variacionIngresos >= 0 ? 'up' : 'down' }}"></i> {{ abs($variacionIngresos) }}% vs. periodo anterior
                            </span>
                        @else
                            <span class="text-muted" style="font-size:0.68rem;">Sin datos del periodo anterior</span>
                        @endif
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-4 p-1">
                    <div class="cont_base rounded p-1 h-100" style="border-left: 4px solid #28a745;">
                        <p class="text-muted m-0" style="font-size:0.7rem;">Utilización de flota</p>
                        <p class="fs-6 fw-bold m-0">{{ $porcentajeUtilizacion }}%</p>
                        <span class="{{ $variacionUtilizacion >= 0 ? 'text-success' : 'text-danger' }}" style="font-size:0.68rem;">
                            <i class="bi bi-arrow-{{ $variacionUtilizacion >= 0 ? 'up' : 'down' }}"></i> {{ abs($variacionUtilizacion) }} pts vs. periodo anterior
                        </span>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-4 p-1">
                    <div class="cont_base rounded p-1 h-100">
                        <p class="text-muted m-0" style="font-size:0.7rem;">Días rentados / días disponibles</p>
                        <p class="fs-6 fw-bold m-0">{{ $diasRentados }} <span class="fw-normal text-muted" style="font-size:0.7rem;">/ {{ $diasDisponibles }} días</span></p>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-4 p-1">
                    <div class="cont_base rounded p-1 h-100">
                        <p class="text-muted m-0" style="font-size:0.7rem;">Categoría más rentada</p>
                        <p class="fs-6 fw-bold m-0">{{ $categoriaMasRentada['nombre'] ?? '—' }}</p>
                        @if($categoriaMasRentada)
                            <span class="text-muted" style="font-size:0.68rem;">{{ $categoriaMasRentada['dias'] }} días rentados</span>
                        @endif
                    </div>
                </div>

            </div>

            {{-- Columna derecha: Top 5 vehículos más utilizados --}}
            <div class="col-12 col-md-4 p-1">
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap rounded cont_base h-100">
                    <div class="col-12 d-flex align-items-center justify-content-between p-2 bg_gris_8">
                        <h2 class="fs-6 fw-bold m-0"><i class="bi bi-trophy-fill me-2"></i>Top 5 vehículos más utilizados</h2>
                    </div>
                    <div class="col-12 p-2">
                        @forelse($topVehiculos as $item)
                            <div class="col-12 d-flex align-items-center justify-content-between border_gris_2_buttom py-2">
                                <span class="fs-6">{{ $item['vehiculo']->name }}</span>
                                <b class="fs-6">{{ $item['dias'] }} días</b>
                            </div>
                        @empty
                            <p class="text-muted text-center py-3 m-0">Sin rentas en el periodo seleccionado.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

        {{-- ===== GRÁFICAS DE TENDENCIA ===== --}}
        <div class="col-12 d-flex align-items-stretch justify-content-start flex-wrap mt-1">
            <div class="col-12 col-md-4 p-1">
                <div class="cont_base rounded p-2 h-100">
                    <p class="fs-6 fw-bold mb-2">Ocupación (%)</p>
                    <canvas id="graficaOcupacion" height="220"></canvas>
                </div>
            </div>
            <div class="col-12 col-md-4 p-1">
                <div class="cont_base rounded p-2 h-100">
                    <p class="fs-6 fw-bold mb-2">Ingresos (MXN)</p>
                    <canvas id="graficaIngresos" height="220"></canvas>
                </div>
            </div>
            <div class="col-12 col-md-4 p-1">
                <div class="cont_base rounded p-2 h-100">
                    <p class="fs-6 fw-bold mb-2">Demanda (reservas concretadas)</p>
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

    const etiquetas = @json($series['etiquetas']);
    const ocupacion = @json($series['ocupacion']);
    const ingresos  = @json($series['ingresos']);
    const demanda   = @json($series['demanda']);

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

    new Chart(document.getElementById('graficaOcupacion'), {
        type: 'line',
        data: { labels: etiquetas, datasets: [{ data: ocupacion, ...estiloLinea }] },
        options: opciones,
    });

    new Chart(document.getElementById('graficaIngresos'), {
        type: 'line',
        data: { labels: etiquetas, datasets: [{ data: ingresos, ...estiloLinea }] },
        options: opciones,
    });

    new Chart(document.getElementById('graficaDemanda'), {
        type: 'line',
        data: { labels: etiquetas, datasets: [{ data: demanda, ...estiloLinea }] },
        options: opciones,
    });
})();
</script>
@endpush
@endsection
