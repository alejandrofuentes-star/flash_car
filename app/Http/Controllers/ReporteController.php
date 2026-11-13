<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Renta;
use App\Models\SolicitudNoAtendida;
use App\Models\Vehicle;
use App\Models\VehiculoDisponibilidad;
use App\Support\GraficaPdf;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ReporteController extends Controller
{
    // Estados que cuentan como "renta efectiva" (el auto sí se rentó): base de ingresos, vehículo-días,
    // ocupación, rentas concretadas, etc. Daño/faltante y garantía pendiente son posteriores a la renta.
    public const ESTADOS_INGRESO = ['contrato_abierto', 'contrato_finalizado', 'devolucion_exitosa', 'dano_faltante', 'garantia_pendiente'];

    // Ingreso real de una renta: costo_total − costo del auto − descuentos (ver Renta::calcularTotalGeneral())
    public const CAMPO_INGRESO = 'total_general';

    // Regla de qué cuenta como flota disponible en una fecha (historial VehiculoDisponibilidad):
    // siempre debe estar activo; si esto es true, además debe estar marcado como disponible
    // (un auto en mantenimiento / "no disponible" no sumaría capacidad). Lo define FlashCar.
    private const FLOTA_REQUIERE_AVAILABLE = false;

    // Criterios del Top 5 (clave => etiqueta del selector)
    public const CRITERIOS_RANKING = [
        'dias'        => 'Más días rentados',
        'utilizacion' => 'Mayor utilización %',
        'ingreso'     => 'Mayor ingreso devengado',
        'contratos'   => 'Más contratos',
        'no_atendida' => 'Más demanda no atendida (días)',
    ];

    public function index(Request $request)
    {
        return view('reportes.index', $this->datosReporte($request));
    }

    /**
     * Descarga el reporte en PDF (GET) con los mismos filtros (periodo y categoría, en la query string)
     * y los mismos cálculos que la pantalla. Dompdf no ejecuta JavaScript, así que las gráficas se
     * dibujan en el servidor con GraficaPdf. Antes se mandaban desde el navegador por POST, pero en
     * el servidor de producción ese POST llegaba convertido en GET.
     */
    public function pdf(Request $request)
    {
        try {
            return $this->generarPdf($request);
        } catch (\Throwable $e) {
            // Se registra en storage/logs/laravel.log y se avisa en pantalla (solo admins ven Reportes)
            Log::error('Error al generar el PDF de reportes', ['error' => $e->getMessage(), 'archivo' => $e->getFile() . ':' . $e->getLine()]);

            return redirect()->route('reportes.index', $request->query())
                ->with('error', 'No se pudo generar el PDF: ' . $e->getMessage());
        }
    }

    /** Arma y descarga el PDF (ver pdf()). */
    private function generarPdf(Request $request)
    {
        $datos = $this->datosReporte($request);

        $datos['graficas']      = $this->graficasParaPdf($datos['series']);
        $datos['logo']          = $this->logoParaPdf();
        $datos['generadoEn']    = now(Vehicle::TZ_OPERACION);
        $datos['generadoPor']   = $request->user()?->name;
        $datos['nombreCategoria'] = $datos['categoriaFiltro'] ? $datos['categorias']->firstWhere('id', $datos['categoriaFiltro'])?->name : null;

        $archivo = 'reporte_flashcar_' . $datos['desde']->format('Y-m-d') . '_' . $datos['hasta']->format('Y-m-d')
            . ($datos['nombreCategoria'] ? '_' . Str::slug($datos['nombreCategoria']) : '') . '.pdf';

        // Dompdf busca base_path('public'), pero en el servidor la carpeta pública está fuera de Laravel
        // (/home/.../flashcar.rentadeautos.site) y truena con "Cannot resolve public path". El PDF no carga
        // archivos (logo y gráficas van en base64), así que basta con una carpeta que exista.
        $carpetaPublica = collect([public_path(), $request->server('DOCUMENT_ROOT'), base_path()])
            ->first(fn ($ruta) => $ruta && realpath($ruta) !== false);
        config(['dompdf.public_path' => $carpetaPublica]);

        $pdf = Pdf::loadView('reportes.pdf', $datos)->setPaper('letter', 'portrait');

        // Número de página en el pie (counter(pages) de CSS no funciona en Dompdf): se escribe al final
        $dompdf = $pdf->getDomPDF();
        $dompdf->render();
        $lienzo = $dompdf->getCanvas();
        $lienzo->page_text(
            $lienzo->get_width() - 76, $lienzo->get_height() - 29,
            'Página {PAGE_NUM} de {PAGE_COUNT}',
            $dompdf->getFontMetrics()->getFont('DejaVu Sans'), 6, [0.42, 0.46, 0.49]
        );

        return $pdf->download($archivo);
    }

    /**
     * Las 4 gráficas de tendencia dibujadas en el servidor (GraficaPdf, GD) con los mismos datos y
     * colores que las de Chart.js en pantalla. Si una falla, el PDF sale con "Gráfica no disponible".
     */
    private function graficasParaPdf(array $series): array
    {
        $porcentaje = fn ($v) => round($v) . '%';
        $moneda     = fn ($v) => '$' . number_format($v);
        $etiquetas  = $series['etiquetas'];

        $definiciones = [
            'ocupacion' => fn () => GraficaPdf::linea($etiquetas, [
                ['etiqueta' => 'Ocupación', 'datos' => $series['ocupacion'], 'color' => '#f0b429', 'relleno' => true],
                ['etiqueta' => 'Promedio del periodo (' . $series['ocupacionPromedio'] . '%)', 'datos' => array_fill(0, count($etiquetas), $series['ocupacionPromedio']),
                 'color' => '#6c757d', 'punteada' => true, 'puntos' => false, 'grosor' => 1.5],
            ], ['max' => 100, 'formato' => $porcentaje]),
            'utilizacion' => fn () => GraficaPdf::linea($etiquetas, [
                ['etiqueta' => 'Utilización acumulada', 'datos' => $series['utilizacionAcumulada'], 'color' => '#28a745', 'relleno' => true],
            ], ['max' => 100, 'formato' => $porcentaje, 'leyenda' => false]),
            'ingresos' => fn () => GraficaPdf::linea($etiquetas, [
                ['etiqueta' => 'Ingreso devengado', 'datos' => $series['ingresos'], 'color' => '#6f42c1', 'relleno' => true],
                ['etiqueta' => 'Ventas (por fecha de reserva)', 'datos' => $series['ventas'], 'color' => '#f0b429', 'punteada' => true],
            ], ['formato' => $moneda]),
            'demanda' => fn () => GraficaPdf::linea($etiquetas, [
                ['etiqueta' => 'Demanda recibida', 'datos' => $series['demanda'], 'color' => '#0d6efd'],
                ['etiqueta' => 'Rentas concretadas', 'datos' => $series['concretadas'], 'color' => '#28a745'],
                ['etiqueta' => 'No atendida', 'datos' => $series['noAtendida'], 'color' => '#dc3545', 'punteada' => true],
            ]),
        ];

        $graficas = [];
        foreach ($definiciones as $clave => $dibujar) {
            try {
                $graficas[$clave] = $dibujar();
            } catch (\Throwable $e) {
                report($e);
                $graficas[$clave] = null;
            }
        }

        return $graficas;
    }

    /** Logo del header (webp) convertido a PNG en base64 para Dompdf; null si no se puede. */
    private function logoParaPdf(): ?string
    {
        try {
            // En el servidor la carpeta pública está fuera de Laravel: se busca también en DOCUMENT_ROOT
            $ruta = collect([public_path('img/logo.webp'), rtrim((string) request()->server('DOCUMENT_ROOT'), '/') . '/img/logo.webp'])
                ->first(fn ($r) => is_file($r));
            if (!$ruta) {
                return null;
            }
            $png = (new ImageManager(new Driver()))->read($ruta)->scaleDown(width: 400)->toPng();

            return 'data:image/png;base64,' . base64_encode((string) $png);
        } catch (\Throwable $e) {
            report($e);
            return null;
        }
    }

    /** Todos los datos del reporte (KPIs, rankings y series) según los filtros de la query string. */
    private function datosReporte(Request $request): array
    {
        $periodo = $request->query('periodo', '30d');

        [$desde, $hasta, $desdeAnterior, $hastaAnterior] = $this->resolverRango($periodo, $request);

        // Filtro de categoría: aplica a todo el reporte (flota, rentas, solicitudes y rankings).
        // Se usa la categoría actual de cada auto.
        $categorias      = Category::orderBy('name')->get(['id', 'name']);
        $categoriaFiltro = $request->filled('categoria') ? (int) $request->query('categoria') : null;
        if ($categoriaFiltro && !$categorias->contains('id', $categoriaFiltro)) {
            $categoriaFiltro = null;
        }
        $idsVehiculos = $categoriaFiltro ? Vehicle::where('category_id', $categoriaFiltro)->pluck('id')->all() : null; // null = todos
        $vehiculos    = fn () => Vehicle::query()->when($categoriaFiltro, fn ($q) => $q->where('category_id', $categoriaFiltro));

        $totalVehiculos   = $vehiculos()->count();
        $vehiculosActivos = $vehiculos()->where('active', true)->count();

        $rentasPeriodo = Renta::with('vehicle.category')
            ->whereBetween('created_at', [$desde, $hasta])
            ->when($idsVehiculos !== null, fn ($q) => $q->whereIn('vehicle_id', $idsVehiculos))
            ->get();

        // Flota de cada día según el historial de disponibilidad (solo hasta hoy) y vehículo-días
        // rentados sobre esa misma flota: noches de cada contrato dentro del rango, máx. 1 por auto y fecha.
        $flotaPorDia  = $this->calcularFlotaPorDia($desde, $hasta, $idsVehiculos);
        $vehiculoDias = $this->calcularVehiculoDias($desde, $hasta, $flotaPorDia);

        $hoy               = now(Vehicle::TZ_OPERACION)->toDateString();
        $diasTranscurridos = count(array_filter(array_keys($flotaPorDia), fn ($fecha) => $fecha <= $hoy));

        $diasRentados          = $this->sumarVehiculoDias($vehiculoDias);
        $diasPeriodo           = $this->contarDias($desde, $hasta);
        $diasDisponibles       = $this->sumarVehiculoDias($flotaPorDia);
        $porcentajeUtilizacion = $diasDisponibles > 0 ? round(($diasRentados / $diasDisponibles) * 100, 1) : 0.0;
        $flotaDelPeriodo       = count($this->diasPorVehiculo($flotaPorDia));
        $flotaPromedio         = $diasTranscurridos > 0 ? round($diasDisponibles / $diasTranscurridos, 1) : 0.0;

        // Ingresos: devengado = total generado de cada renta efectiva repartido entre sus noches; al
        // periodo le toca lo de los días que caen en el rango (hasta hoy). Ventas = total generado de
        // las rentas efectivas creadas en el periodo (por fecha de reserva).
        $rentasEfectivas = $rentasPeriodo->whereIn('estado', self::ESTADOS_INGRESO);
        $devengado       = $this->calcularIngresoDevengado($desde, $hasta, $idsVehiculos);
        $ingresos        = $devengado['total'];
        $ventasPeriodo   = (float) $rentasEfectivas->sum(self::CAMPO_INGRESO);

        // Demanda no atendida: solicitudes rechazadas por falta de auto (nunca llegaron a ser renta)
        // + rentas creadas en el periodo canceladas con motivo "falta de disponibilidad".
        $solicitudesNoAtendidas = SolicitudNoAtendida::whereBetween('created_at', [$desde, $hasta])
            ->when($categoriaFiltro, fn ($q) => $q->where('category_id', $categoriaFiltro))
            ->get();
        $canceladasSinAuto = $rentasPeriodo->where('estado', 'cancelada')->where('motivo_cancelacion', 'falta_disponibilidad');
        $demandaNoAtendida = $solicitudesNoAtendidas->count() + $canceladasSinAuto->count();
        $diasNoAtendidos   = (int) $solicitudesNoAtendidas->sum('total_dias') + (int) $canceladasSinAuto->sum('total_dias');

        $diasPorVehiculo = $this->diasPorVehiculo($vehiculoDias);

        // Top 5 de vehículos y categorías, alternable por días, utilización, ingreso, contratos y no atendida
        $rankings            = $this->construirRankings($diasPorVehiculo, $devengado['porVehiculo'], $rentasEfectivas, $flotaPorDia, $solicitudesNoAtendidas, $canceladasSinAuto);
        $categoriaMasRentada = $rankings['categorias']['dias'][0] ?? null;

        // ===== KPIs de flota, ingresos y comerciales (manual reportes.md, secciones 4–8 y 13) =====
        // Flota: la disponible hoy es la que se puede rentar en este momento (activa, marcada
        // disponible y sin renta en curso); la base de los porcentajes es la flota del historial.
        $flotaDisponibleHoy  = $vehiculos()->disponibleAhora()->count();
        $vehiculosUtilizados = count($diasPorVehiculo);

        // Último día del rango que ya ocurrió (si el rango llega al futuro, hoy)
        $ultimaFecha        = collect(array_keys($flotaPorDia))->filter(fn ($fecha) => $fecha <= $hoy)->last();
        $rentadosUltimoDia  = $ultimaFecha ? count($vehiculoDias[$ultimaFecha]) : 0;
        $flotaUltimoDia     = $ultimaFecha ? count($flotaPorDia[$ultimaFecha]) : 0;
        $ocupacionUltimoDia = [
            'fecha'      => $ultimaFecha ? Carbon::parse($ultimaFecha) : null,
            'rentados'   => $rentadosUltimoDia,
            'flota'      => $flotaUltimoDia,
            'porcentaje' => $flotaUltimoDia > 0 ? min(100.0, round(($rentadosUltimoDia / $flotaUltimoDia) * 100, 1)) : 0.0,
        ];

        // Comercial: demanda = todas las reservas creadas en el periodo (cualquier estado) + las
        // solicitudes no atendidas; concretadas = las que llegaron a renta efectiva (misma lista que ingresos).
        $demandaRecibida   = $rentasPeriodo->count() + $solicitudesNoAtendidas->count();
        $demandaDias       = (int) $rentasPeriodo->sum('total_dias') + (int) $solicitudesNoAtendidas->sum('total_dias');
        $rentasConcretadas = $rentasEfectivas->count();
        $tasaConversion    = $demandaRecibida > 0 ? round(($rentasConcretadas / $demandaRecibida) * 100, 1) : null;

        // Cliente = correo (sin mayúsculas/espacios); si no hay correo, el teléfono. No existe customer_id.
        $clientesUnicos = $rentasEfectivas
            ->map(fn ($r) => mb_strtolower(trim((string) $r->correo)) ?: preg_replace('/\D/', '', (string) $r->telefono))
            ->filter()
            ->unique()
            ->count();
        $rentasPorCliente = $clientesUnicos > 0 ? round($rentasConcretadas / $clientesUnicos, 2) : null;

        // Duración promedio: días de los contratos concretados / contratos (misma base de rentas que
        // ventas y ticket, para que un contrato largo no se parta según el rango filtrado).
        $duracionPromedio = $rentasConcretadas > 0 ? round($rentasEfectivas->sum('total_dias') / $rentasConcretadas, 1) : null;

        // Ticket = ventas / contratos concretados (misma base: rentas creadas en el periodo).
        // Por día rentado y por vehículo usan el devengado, que está en la misma base que los vehículo-días.
        $ticketPromedio       = $rentasConcretadas > 0 ? round($ventasPeriodo / $rentasConcretadas, 2) : null;
        $ingresoPorDiaRentado = $diasRentados > 0 ? round($ingresos / $diasRentados, 2) : null;
        $ingresoPorVehiculo   = $flotaPromedio > 0 ? round($ingresos / $flotaPromedio, 2) : null;

        // Comparativo histórico (ingreso devengado y % de utilización) contra el periodo anterior equivalente.
        // Si el periodo actual no ha terminado, el ingreso se compara contra los mismos días transcurridos
        // del periodo anterior (12 días de este mes vs. los primeros 12 del mes pasado); la utilización ya
        // es un porcentaje y se compara completa.
        $hastaAnteriorIngreso    = $diasTranscurridos < $diasPeriodo
            ? $desdeAnterior->copy()->addDays(max($diasTranscurridos, 1) - 1)->endOfDay()->min($hastaAnterior)
            : $hastaAnterior;
        $ingresosAnterior        = $diasTranscurridos > 0
            ? $this->calcularIngresoDevengado($desdeAnterior, $hastaAnteriorIngreso, $idsVehiculos)['total']
            : 0.0;
        $flotaAnterior           = $this->calcularFlotaPorDia($desdeAnterior, $hastaAnterior, $idsVehiculos);
        $diasRentadosAnterior    = $this->sumarVehiculoDias($this->calcularVehiculoDias($desdeAnterior, $hastaAnterior, $flotaAnterior));
        $diasDisponiblesAnterior = $this->sumarVehiculoDias($flotaAnterior);
        $utilizacionAnterior     = $diasDisponiblesAnterior > 0 ? round(($diasRentadosAnterior / $diasDisponiblesAnterior) * 100, 1) : 0.0;

        $variacionIngresos    = $ingresosAnterior > 0 ? round((($ingresos - $ingresosAnterior) / $ingresosAnterior) * 100, 1) : null;
        $variacionUtilizacion = $diasTranscurridos > 0 ? round($porcentajeUtilizacion - $utilizacionAnterior, 1) : null;

        $series = $this->construirSeries($rentasPeriodo, $desde, $hasta, $flotaPorDia, $vehiculoDias, $solicitudesNoAtendidas, $devengado['porDia'], $hoy);

        return [
            'periodo'              => $periodo,
            'categorias'           => $categorias,
            'categoriaFiltro'      => $categoriaFiltro,
            'desde'                => $desde,
            'hasta'                => $hasta,
            'totalVehiculos'       => $totalVehiculos,
            'vehiculosActivos'     => $vehiculosActivos,
            'flotaDelPeriodo'      => $flotaDelPeriodo,
            'flotaPromedio'        => $flotaPromedio,
            'diasPeriodo'          => $diasPeriodo,
            'diasTranscurridos'    => $diasTranscurridos,
            'flotaDisponibleHoy'   => $flotaDisponibleHoy,
            'vehiculosUtilizados'  => $vehiculosUtilizados,
            'ocupacionUltimoDia'   => $ocupacionUltimoDia,
            'demandaRecibida'      => $demandaRecibida,
            'demandaDias'          => $demandaDias,
            'demandaNoAtendida'    => $demandaNoAtendida,
            'diasNoAtendidos'      => $diasNoAtendidos,
            'solicitudesNoAtendidas' => $solicitudesNoAtendidas->count(),
            'canceladasSinAuto'    => $canceladasSinAuto->count(),
            'rentasConcretadas'    => $rentasConcretadas,
            'tasaConversion'       => $tasaConversion,
            'clientesUnicos'       => $clientesUnicos,
            'rentasPorCliente'     => $rentasPorCliente,
            'duracionPromedio'     => $duracionPromedio,
            'ticketPromedio'       => $ticketPromedio,
            'ingresoPorDiaRentado' => $ingresoPorDiaRentado,
            'ingresoPorVehiculo'   => $ingresoPorVehiculo,
            'diasRentados'         => $diasRentados,
            'diasDisponibles'      => $diasDisponibles,
            'porcentajeUtilizacion' => $porcentajeUtilizacion,
            'categoriaMasRentada'  => $categoriaMasRentada,
            'rankings'             => $rankings,
            'criteriosRanking'     => self::CRITERIOS_RANKING,
            'ingresos'             => $ingresos,
            'ventasPeriodo'        => $ventasPeriodo,
            'variacionIngresos'    => $variacionIngresos,
            'utilizacionAnterior'  => $utilizacionAnterior,
            'variacionUtilizacion' => $variacionUtilizacion,
            'series'               => $series,
        ];
    }

    /**
     * Resuelve [desde, hasta, desdeAnterior, hastaAnterior] según el periodo elegido.
     * El periodo "anterior" es siempre el equivalente inmediato previo (mismos días,
     * mes pasado o año pasado) para alimentar los comparativos históricos.
     */
    private function resolverRango(string $periodo, Request $request): array
    {
        $hoy = Carbon::today();

        switch ($periodo) {
            case '7d':
                $desde         = $hoy->copy()->subDays(6)->startOfDay();
                $hasta         = $hoy->copy()->endOfDay();
                $desdeAnterior = $desde->copy()->subDays(7);
                $hastaAnterior = $desde->copy()->subSecond();
                break;

            case 'anio':
                $anio          = (int) $request->query('anio_valor', $hoy->year);
                $desde         = Carbon::create($anio, 1, 1)->startOfDay();
                $hasta         = Carbon::create($anio, 12, 31)->endOfDay();
                $desdeAnterior = $desde->copy()->subYear();
                $hastaAnterior = $hasta->copy()->subYear();
                break;

            case 'mes':
                $mesValor      = $request->query('mes_valor');
                $base          = $mesValor ? Carbon::createFromFormat('Y-m', $mesValor) : $hoy;
                $desde         = $base->copy()->startOfMonth();
                $hasta         = $base->copy()->endOfMonth();
                $desdeAnterior = $desde->copy()->subMonth()->startOfMonth();
                $hastaAnterior = $desde->copy()->subMonth()->endOfMonth();
                break;

            case 'personalizado':
                $desde = $request->query('desde')
                    ? Carbon::parse($request->query('desde'))->startOfDay()
                    : $hoy->copy()->subDays(29)->startOfDay();
                $hasta = $request->query('hasta')
                    ? Carbon::parse($request->query('hasta'))->endOfDay()
                    : $hoy->copy()->endOfDay();

                $duracionDias  = $this->contarDias($desde, $hasta);
                $hastaAnterior = $desde->copy()->subSecond();
                $desdeAnterior = $hastaAnterior->copy()->subDays($duracionDias - 1)->startOfDay();
                break;

            case '30d':
            default:
                $desde         = $hoy->copy()->subDays(29)->startOfDay();
                $hasta         = $hoy->copy()->endOfDay();
                $desdeAnterior = $desde->copy()->subDays(30);
                $hastaAnterior = $desde->copy()->subSecond();
                break;
        }

        return [$desde, $hasta, $desdeAnterior, $hastaAnterior];
    }

    /**
     * Número de días (entero) entre dos fechas, inclusivo. Compara solo la parte de
     * fecha (sin hora) para evitar que "hasta" en endOfDay (23:59:59.999999) produzca
     * un diffInDays con fracción de microsegundos (ej. 29.999999999988 en vez de 29).
     */
    private function contarDias(Carbon $desde, Carbon $hasta): int
    {
        return $desde->copy()->startOfDay()->diffInDays($hasta->copy()->startOfDay()) + 1;
    }

    /**
     * Series de tendencia para las gráficas de líneas.
     * Diaria si el periodo cabe en ~2 meses; mensual si es más largo (ej. filtro "año").
     * - ocupacion / rentados / flota: % y autos rentados y en flota por día (en buckets mensuales,
     *   el promedio diario del mes). Días sin flota o futuros = null (hueco en la gráfica).
     * - utilizacionAcumulada: Σ vehículo-días rentados / Σ vehículo-días disponibles desde el inicio
     *   del rango hasta el final de cada bucket; el último punto es igual al KPI "Utilización del periodo".
     * - ocupacionPromedio: ocupación promedio ponderada por flota del rango (línea de referencia; es
     *   igual a la utilización del periodo, como indica la sección 4 del manual).
     * - ingresos: ingreso devengado por día; ventas: total generado de las rentas efectivas creadas ese día.
     * - demanda incluye las solicitudes no atendidas; noAtendida = solicitudes rechazadas + rentas
     *   canceladas por falta de disponibilidad (por fecha de creación).
     */
    private function construirSeries(Collection $rentasPeriodo, Carbon $desde, Carbon $hasta, array $flotaPorDia, array $vehiculoDias, Collection $solicitudesNoAtendidas, array $devengadoPorDia, string $hoy): array
    {
        $duracionDias = $this->contarDias($desde, $hasta);
        $mensual      = $duracionDias > 62;

        $ocupacionPorDia = $this->calcularOcupacionDiaria($vehiculoDias, $flotaPorDia);
        $rentadosPorDia  = array_map('count', $vehiculoDias);
        $flotaContada    = array_map('count', $flotaPorDia);

        $esNoAtendida = fn ($r) => $r->estado === 'cancelada' && $r->motivo_cancelacion === 'falta_disponibilidad';

        $etiquetas            = [];
        $ocupacion            = [];
        $rentados             = [];
        $flota                = [];
        $utilizacionAcumulada = [];
        $rentadosAcumulados   = [];
        $capacidadAcumulada   = [];
        $ingresos             = [];
        $ventas               = [];
        $demanda              = [];
        $concretadas          = [];
        $noAtendida           = [];

        $sumaRentados  = 0;
        $sumaCapacidad = 0;
        // Acumula los días ya ocurridos del bucket; si todo el bucket es futuro, el punto queda vacío
        $acumular = function (array $fechas) use (&$sumaRentados, &$sumaCapacidad, &$utilizacionAcumulada, &$rentadosAcumulados, &$capacidadAcumulada, $rentadosPorDia, $flotaContada, $hoy) {
            $pasadas = array_filter($fechas, fn ($f) => $f <= $hoy);
            foreach ($pasadas as $fecha) {
                $sumaRentados  += $rentadosPorDia[$fecha] ?? 0;
                $sumaCapacidad += $flotaContada[$fecha] ?? 0;
            }
            $rentadosAcumulados[]   = $sumaRentados;
            $capacidadAcumulada[]   = $sumaCapacidad;
            $utilizacionAcumulada[] = empty($pasadas) ? null : ($sumaCapacidad > 0 ? round(($sumaRentados / $sumaCapacidad) * 100, 1) : 0.0);
        };
        $promedio = function (array $fechas, array $valores) use ($flotaContada) {
            $conFlota = array_filter($fechas, fn ($f) => ($flotaContada[$f] ?? 0) > 0);
            return count($conFlota) > 0 ? round(array_sum(array_map(fn ($f) => $valores[$f] ?? 0, $conFlota)) / count($conFlota), 1) : null;
        };
        $sumaDevengado = function (array $fechas) use ($devengadoPorDia, $hoy) {
            $pasadas = array_filter($fechas, fn ($f) => $f <= $hoy);
            return empty($pasadas) ? null : round(array_sum(array_map(fn ($f) => $devengadoPorDia[$f] ?? 0, $pasadas)), 2);
        };

        if (!$mensual) {
            $cursor = $desde->copy()->startOfDay();
            while ($cursor->lte($hasta)) {
                $dia    = $cursor->copy();
                $fecha  = $dia->format('Y-m-d');
                $delDia = $rentasPeriodo->filter(fn ($r) => $r->created_at->isSameDay($dia));
                $solDia = $solicitudesNoAtendidas->filter(fn ($s) => $s->created_at->isSameDay($dia))->count();

                $etiquetas[]   = $dia->format('d/m');
                $ocupacion[]   = $ocupacionPorDia[$fecha];
                $rentados[]    = $rentadosPorDia[$fecha];
                $flota[]       = $flotaContada[$fecha];
                $ingresos[]    = $sumaDevengado([$fecha]);
                $ventas[]      = (float) $delDia->whereIn('estado', self::ESTADOS_INGRESO)->sum(self::CAMPO_INGRESO);
                $demanda[]     = $delDia->count() + $solDia;
                $concretadas[] = $delDia->whereIn('estado', self::ESTADOS_INGRESO)->count();
                $noAtendida[]  = $solDia + $delDia->filter($esNoAtendida)->count();
                $acumular([$fecha]);

                $cursor->addDay();
            }
        } else {
            $cursor = $desde->copy()->startOfMonth();
            while ($cursor->lte($hasta)) {
                $mes    = $cursor->copy();
                $delMes = $rentasPeriodo->filter(fn ($r) => $r->created_at->isSameMonth($mes));
                $solMes = $solicitudesNoAtendidas->filter(fn ($s) => $s->created_at->isSameMonth($mes))->count();

                $fechasDelMes = array_values(array_filter(
                    array_keys($ocupacionPorDia),
                    fn ($fecha) => Carbon::parse($fecha)->isSameMonth($mes)
                ));

                $etiquetas[]   = ucfirst($mes->translatedFormat('M Y'));
                $ocupacion[]   = $promedio($fechasDelMes, $ocupacionPorDia);
                $rentados[]    = $promedio($fechasDelMes, $rentadosPorDia);
                $flota[]       = $promedio($fechasDelMes, $flotaContada);
                $ingresos[]    = $sumaDevengado($fechasDelMes);
                $ventas[]      = (float) $delMes->whereIn('estado', self::ESTADOS_INGRESO)->sum(self::CAMPO_INGRESO);
                $demanda[]     = $delMes->count() + $solMes;
                $concretadas[] = $delMes->whereIn('estado', self::ESTADOS_INGRESO)->count();
                $noAtendida[]  = $solMes + $delMes->filter($esNoAtendida)->count();
                $acumular($fechasDelMes);

                $cursor->addMonth();
            }
        }

        // Promedio ponderado por flota (Σ rentados / Σ flota): coincide con la utilización del periodo
        $ocupacionPromedio = $sumaCapacidad > 0 ? round(($sumaRentados / $sumaCapacidad) * 100, 1) : 0.0;

        return compact(
            'mensual', 'etiquetas', 'ocupacion', 'rentados', 'flota', 'ocupacionPromedio',
            'utilizacionAcumulada', 'rentadosAcumulados', 'capacidadAcumulada',
            'ingresos', 'ventas', 'demanda', 'concretadas', 'noAtendida'
        );
    }

    /**
     * Top 5 de vehículos y de categorías por cada criterio de CRITERIOS_RANKING.
     * - dias: vehículo-días rentados dentro del periodo (ver calcularVehiculoDias()).
     * - utilizacion: días rentados / días que el auto (o los autos de la categoría) estuvo en la
     *   flota durante el periodo, según el historial de disponibilidad.
     * - ingreso: ingreso devengado en el periodo (misma base que la tarjeta "Ingreso devengado").
     * - contratos: rentas concretadas creadas en el periodo (misma base que "Rentas concretadas").
     * - no_atendida: días solicitados no atendidos (solicitudes rechazadas + canceladas por falta de
     *   disponibilidad). Para un vehículo solo cuenta cuando se pidió ese auto en específico.
     * Devuelve ['vehiculos' => [criterio => [items]], 'categorias' => [...]]; cada item trae
     * nombre, detalle, imagen (url o null) y el valor de cada criterio. Solo valores > 0.
     */
    private function construirRankings(array $diasPorVehiculo, array $devengadoPorVehiculo, Collection $rentasEfectivas, array $flotaPorDia, Collection $solicitudesNoAtendidas, Collection $canceladasSinAuto): array
    {
        $diasEnFlota          = $this->diasPorVehiculo($flotaPorDia);
        $ingresoPorVehiculo   = $devengadoPorVehiculo;
        $contratosPorVehiculo = $rentasEfectivas->groupBy('vehicle_id')->map->count();

        // Días no atendidos por vehículo (solo si se pidió ese auto) y por categoría
        $noAtendidaPorVehiculo  = [];
        $noAtendidaPorCategoria = [];
        foreach ($solicitudesNoAtendidas as $solicitud) {
            if ($solicitud->vehicle_id) {
                $noAtendidaPorVehiculo[$solicitud->vehicle_id] = ($noAtendidaPorVehiculo[$solicitud->vehicle_id] ?? 0) + $solicitud->total_dias;
            }
            if ($solicitud->category_id) {
                $noAtendidaPorCategoria[$solicitud->category_id] = ($noAtendidaPorCategoria[$solicitud->category_id] ?? 0) + $solicitud->total_dias;
            }
        }
        foreach ($canceladasSinAuto as $renta) {
            $noAtendidaPorVehiculo[$renta->vehicle_id] = ($noAtendidaPorVehiculo[$renta->vehicle_id] ?? 0) + (int) $renta->total_dias;
            if ($renta->vehicle?->category_id) {
                $noAtendidaPorCategoria[$renta->vehicle->category_id] = ($noAtendidaPorCategoria[$renta->vehicle->category_id] ?? 0) + (int) $renta->total_dias;
            }
        }

        $ids = collect(array_keys($diasPorVehiculo))
            ->merge(array_keys($ingresoPorVehiculo))
            ->merge($contratosPorVehiculo->keys())
            ->merge(array_keys($noAtendidaPorVehiculo))
            ->filter()
            ->unique()
            ->all();

        $vehiculos = Vehicle::with('category')->whereIn('id', $ids)->get();

        $itemsVehiculos = $vehiculos->map(function ($vehiculo) use ($diasPorVehiculo, $diasEnFlota, $ingresoPorVehiculo, $contratosPorVehiculo, $noAtendidaPorVehiculo) {
            $dias      = $diasPorVehiculo[$vehiculo->id] ?? 0;
            $capacidad = $diasEnFlota[$vehiculo->id] ?? 0;

            return [
                'nombre'      => $vehiculo->name,
                'detalle'     => $vehiculo->category?->name,
                'imagen'      => $vehiculo->image_path ? Storage::url($vehiculo->image_path) : null,
                'dias'        => $dias,
                'utilizacion' => $capacidad > 0 ? min(100.0, round(($dias / $capacidad) * 100, 1)) : 0.0,
                'ingreso'     => round($ingresoPorVehiculo[$vehiculo->id] ?? 0.0, 2),
                'contratos'   => $contratosPorVehiculo[$vehiculo->id] ?? 0,
                'no_atendida' => $noAtendidaPorVehiculo[$vehiculo->id] ?? 0,
            ];
        });

        // Categorías: capacidad = días en flota de sus autos (categoría actual de cada auto)
        $categoriaDeVehiculo   = Vehicle::whereIn('id', array_keys($diasEnFlota))->pluck('category_id', 'id');
        $capacidadPorCategoria = [];
        $autosPorCategoria     = [];
        foreach ($diasEnFlota as $vehiculoId => $diasFlota) {
            $categoriaId = $categoriaDeVehiculo[$vehiculoId] ?? null;
            if ($categoriaId) {
                $capacidadPorCategoria[$categoriaId] = ($capacidadPorCategoria[$categoriaId] ?? 0) + $diasFlota;
                $autosPorCategoria[$categoriaId]     = ($autosPorCategoria[$categoriaId] ?? 0) + 1;
            }
        }

        $porCategoria = fn (array|Collection $valores) => $vehiculos
            ->filter(fn ($v) => $v->category_id)
            ->groupBy('category_id')
            ->map(fn ($grupo) => $grupo->sum(fn ($v) => $valores[$v->id] ?? 0));

        $diasCategoria      = $porCategoria($diasPorVehiculo);
        $ingresoCategoria   = $porCategoria($ingresoPorVehiculo);
        $contratosCategoria = $porCategoria($contratosPorVehiculo);

        $categoriaIds = $diasCategoria->keys()
            ->merge($ingresoCategoria->keys())
            ->merge(array_keys($noAtendidaPorCategoria))
            ->unique()
            ->all();

        $itemsCategorias = Category::whereIn('id', $categoriaIds)->get()->map(function ($categoria) use ($diasCategoria, $ingresoCategoria, $contratosCategoria, $noAtendidaPorCategoria, $capacidadPorCategoria, $autosPorCategoria) {
            $dias      = (int) ($diasCategoria[$categoria->id] ?? 0);
            $capacidad = $capacidadPorCategoria[$categoria->id] ?? 0;
            $autos     = $autosPorCategoria[$categoria->id] ?? 0;

            return [
                'nombre'      => $categoria->name,
                'detalle'     => $autos . ' ' . ($autos === 1 ? 'auto en flota' : 'autos en flota'),
                'imagen'      => null,
                'dias'        => $dias,
                'utilizacion' => $capacidad > 0 ? min(100.0, round(($dias / $capacidad) * 100, 1)) : 0.0,
                'ingreso'     => round((float) ($ingresoCategoria[$categoria->id] ?? 0.0), 2),
                'contratos'   => (int) ($contratosCategoria[$categoria->id] ?? 0),
                'no_atendida' => (int) ($noAtendidaPorCategoria[$categoria->id] ?? 0),
            ];
        });

        $top = fn (Collection $items) => collect(array_keys(self::CRITERIOS_RANKING))
            ->mapWithKeys(fn ($criterio) => [
                $criterio => $items->filter(fn ($i) => $i[$criterio] > 0)->sortByDesc($criterio)->take(5)->values()->all(),
            ])
            ->all();

        return [
            'vehiculos'  => $top($itemsVehiculos),
            'categorias' => $top($itemsCategorias),
        ];
    }

    /**
     * Flota disponible por fecha: ['Y-m-d' => [vehicle_id => true]] para cada día del rango, según el
     * historial VehiculoDisponibilidad (estado de cada auto al cierre del día, hora de operación).
     * Un auto cuenta si está activo (y marcado disponible si FLOTA_REQUIERE_AVAILABLE). Los autos
     * todavía no registrados o ya dados de baja en esa fecha no suman capacidad. Los días futuros
     * quedan sin flota (no suman capacidad que todavía no ocurre). $idsVehiculos null = todos.
     */
    private function calcularFlotaPorDia(Carbon $desde, Carbon $hasta, ?array $idsVehiculos = null): array
    {
        $hoy     = now(Vehicle::TZ_OPERACION)->toDateString();
        $flota   = [];
        $cierres = [];
        for ($cursor = $desde->copy()->startOfDay(); $cursor->lte($hasta->copy()->startOfDay()); $cursor->addDay()) {
            $fecha         = $cursor->format('Y-m-d');
            $flota[$fecha] = [];
            if ($fecha <= $hoy) {
                $cierres[$fecha] = Carbon::parse($fecha . ' 23:59:59', Vehicle::TZ_OPERACION)->utc();
            }
        }

        if (empty($cierres) || $idsVehiculos === []) {
            return $flota;
        }

        $historiales = VehiculoDisponibilidad::where('desde', '<=', end($cierres))
            ->when($idsVehiculos !== null, fn ($q) => $q->whereIn('vehicle_id', $idsVehiculos))
            ->orderBy('vehicle_id')
            ->orderBy('desde')
            ->orderBy('id')
            ->get(['vehicle_id', 'active', 'available', 'desde'])
            ->groupBy('vehicle_id');

        foreach ($historiales as $vehiculoId => $historial) {
            $historial = $historial->values();
            $total     = $historial->count();
            $indice    = 0;
            $estado    = null;

            foreach ($cierres as $fecha => $cierre) {
                while ($indice < $total && $historial[$indice]->desde->lte($cierre)) {
                    $estado = $historial[$indice++];
                }
                if ($estado && $estado->active && (!self::FLOTA_REQUIERE_AVAILABLE || $estado->available)) {
                    $flota[$fecha][$vehiculoId] = true;
                }
            }
        }

        return $flota;
    }

    /**
     * Días que ocupa una renta en reportes, por noches: desde la fecha de entrega hasta el día anterior
     * a la devolución (renta del lunes al jueves = lunes, martes y miércoles = 3 días, igual que lo
     * cobrado). Si entrega y devolución son el mismo día, cuenta ese día. Devuelve [inicio, fin] o null.
     */
    private function nochesDeRenta(Renta $renta): ?array
    {
        if (!$renta->fecha_entrega || !$renta->fecha_devolucion) {
            return null;
        }

        $inicio = $renta->fecha_entrega->copy()->startOfDay();
        $fin    = $renta->fecha_devolucion->copy()->startOfDay()->subDay();

        return [$inicio, $fin->lt($inicio) ? $inicio->copy() : $fin];
    }

    /** Rentas efectivas cuyo rango de noches puede cruzar el periodo. $idsVehiculos null = todos. */
    private function rentasQueCruzan(Carbon $desde, Carbon $hasta, ?array $idsVehiculos, array $columnas): Collection
    {
        return Renta::whereIn('estado', self::ESTADOS_INGRESO)
            ->when($idsVehiculos !== null, fn ($q) => $q->whereIn('vehicle_id', $idsVehiculos))
            ->whereDate('fecha_entrega', '<=', $hasta->copy()->startOfDay())
            ->whereDate('fecha_devolucion', '>=', $desde->copy()->startOfDay())
            ->get($columnas);
    }

    /**
     * Vehículo-días rentados por fecha: ['Y-m-d' => [vehicle_id => true]] para cada día del rango.
     * - Solo rentas en estado de renta efectiva (ESTADOS_INGRESO).
     * - Días por noches (ver nochesDeRenta()) que caen dentro del rango filtrado.
     * - Un vehículo aporta como máximo 1 vehículo-día por fecha aunque tenga contratos superpuestos.
     * - Solo cuenta si ese día el auto estaba en la flota ($flotaPorDia), así la ocupación nunca pasa de 100%.
     * Es la base única de Ocupación, Utilización, Días rentados, Top 5 y Categoría más rentada.
     */
    private function calcularVehiculoDias(Carbon $desde, Carbon $hasta, array $flotaPorDia): array
    {
        $inicioRango  = $desde->copy()->startOfDay();
        $finRango     = $hasta->copy()->startOfDay();
        $vehiculoDias = array_fill_keys(array_keys($flotaPorDia), []);
        $vehiculosIds = array_keys($this->diasPorVehiculo($flotaPorDia));

        if (empty($vehiculosIds)) {
            return $vehiculoDias;
        }

        foreach ($this->rentasQueCruzan($desde, $hasta, $vehiculosIds, ['vehicle_id', 'fecha_entrega', 'fecha_devolucion']) as $renta) {
            $noches = $this->nochesDeRenta($renta);
            if (!$noches) {
                continue;
            }

            $inicio = $noches[0]->max($inicioRango);
            $fin    = $noches[1]->min($finRango);

            for ($dia = $inicio->copy(); $dia->lte($fin); $dia->addDay()) {
                $fecha = $dia->format('Y-m-d');
                if (isset($flotaPorDia[$fecha][$renta->vehicle_id])) {
                    $vehiculoDias[$fecha][$renta->vehicle_id] = true;
                }
            }
        }

        return $vehiculoDias;
    }

    /**
     * Ingreso devengado: el total generado (CAMPO_INGRESO) de cada renta efectiva se reparte en partes
     * iguales entre sus noches (ver nochesDeRenta()); al periodo le toca lo de las noches que caen en el
     * rango y que ya ocurrieron (no se devenga el futuro). Ej.: renta de 30 noches por $22,500 = $750
     * por día; si 20 noches caen en agosto, agosto devenga $15,000.
     * Devuelve ['total' => float, 'porDia' => ['Y-m-d' => float], 'porVehiculo' => [vehicle_id => float]].
     */
    private function calcularIngresoDevengado(Carbon $desde, Carbon $hasta, ?array $idsVehiculos = null): array
    {
        $resultado = ['total' => 0.0, 'porDia' => [], 'porVehiculo' => []];
        if ($idsVehiculos === []) {
            return $resultado;
        }

        $inicioRango = $desde->copy()->startOfDay();
        $finRango    = $hasta->copy()->startOfDay()->min(Carbon::parse(now(Vehicle::TZ_OPERACION)->toDateString()));

        if ($finRango->lt($inicioRango)) {
            return $resultado;
        }

        foreach ($this->rentasQueCruzan($desde, $hasta, $idsVehiculos, ['vehicle_id', 'fecha_entrega', 'fecha_devolucion', self::CAMPO_INGRESO]) as $renta) {
            $noches = $this->nochesDeRenta($renta);
            if (!$noches) {
                continue;
            }

            $totalNoches = (int) $noches[0]->diffInDays($noches[1]) + 1;
            $montoPorDia = (float) $renta->{self::CAMPO_INGRESO} / $totalNoches;

            $inicio = $noches[0]->copy()->max($inicioRango);
            $fin    = $noches[1]->copy()->min($finRango);

            for ($dia = $inicio->copy(); $dia->lte($fin); $dia->addDay()) {
                $fecha = $dia->format('Y-m-d');
                $resultado['porDia'][$fecha]                     = ($resultado['porDia'][$fecha] ?? 0) + $montoPorDia;
                $resultado['porVehiculo'][$renta->vehicle_id]    = ($resultado['porVehiculo'][$renta->vehicle_id] ?? 0) + $montoPorDia;
                $resultado['total']                             += $montoPorDia;
            }
        }

        $resultado['total'] = round($resultado['total'], 2);

        return $resultado;
    }

    /** Total de vehículo-días de un mapa ['Y-m-d' => [vehicle_id => true]]. */
    private function sumarVehiculoDias(array $vehiculoDias): int
    {
        return array_sum(array_map('count', $vehiculoDias));
    }

    /** Días por vehículo de un mapa ['Y-m-d' => [vehicle_id => true]]: [vehicle_id => días]. */
    private function diasPorVehiculo(array $vehiculoDias): array
    {
        $dias = [];
        foreach ($vehiculoDias as $vehiculos) {
            foreach (array_keys($vehiculos) as $vehiculoId) {
                $dias[$vehiculoId] = ($dias[$vehiculoId] ?? 0) + 1;
            }
        }

        return $dias;
    }

    /**
     * Ocupación por fecha: vehículos distintos rentados ese día / flota de ese día x 100.
     * Devuelve ['Y-m-d' => %]; nunca supera 100% porque solo se cuentan autos que estaban en la flota.
     * Los días sin flota (o futuros) quedan en null (hueco en la gráfica), no en 0%.
     */
    private function calcularOcupacionDiaria(array $vehiculoDias, array $flotaPorDia): array
    {
        $ocupacion = [];
        foreach ($flotaPorDia as $fecha => $flota) {
            $total = count($flota);
            $ocupacion[$fecha] = $total > 0
                ? min(100.0, round((count($vehiculoDias[$fecha] ?? []) / $total) * 100, 1))
                : null;
        }

        return $ocupacion;
    }
}
