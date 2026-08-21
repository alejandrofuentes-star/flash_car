<?php

namespace App\Http\Controllers;

use App\Models\Renta;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ReporteController extends Controller
{
    private const ESTADOS_INGRESO = ['contrato_abierto', 'contrato_finalizado', 'devolucion_exitosa'];

    public const ESTADOS_RENTA = [
        'reserva_confirmada'  => 'Reservada',
        'proxima_entrega'     => 'Próx. entrega',
        'pendiente_pago'      => 'Pendiente de pago',
        'contrato_abierto'    => 'En renta',
        'contrato_finalizado' => 'Finalizado',
        'devolucion_exitosa'  => 'Devuelto',
        'dano_faltante'       => 'Daño/Faltante',
        'garantia_pendiente'  => 'Garantía pendiente',
        'cancelada'           => 'Cancelada',
    ];

    public function index(Request $request)
    {
        $periodo      = $request->query('periodo', '30d');
        $estadoFiltro = $request->query('estado');

        [$desde, $hasta, $desdeAnterior, $hastaAnterior] = $this->resolverRango($periodo, $request);

        $totalVehiculos   = Vehicle::count();
        $vehiculosActivos = Vehicle::where('active', true)->count();

        $rentasPeriodo = Renta::with('vehicle.category')
            ->whereBetween('created_at', [$desde, $hasta])
            ->get();

        $totalReservas = $estadoFiltro
            ? $rentasPeriodo->where('estado', $estadoFiltro)->count()
            : $rentasPeriodo->count();

        $rentasConcretadas = $rentasPeriodo->where('estado', '!=', 'cancelada');

        $diasRentados          = (int) $rentasConcretadas->sum('total_dias');
        $diasPeriodo           = $this->contarDias($desde, $hasta);
        $diasDisponibles       = $vehiculosActivos * $diasPeriodo;
        $porcentajeUtilizacion = $diasDisponibles > 0 ? round(($diasRentados / $diasDisponibles) * 100, 1) : 0.0;

        $categoriaMasRentada = $rentasConcretadas
            ->filter(fn ($r) => $r->vehicle && $r->vehicle->category)
            ->groupBy(fn ($r) => $r->vehicle->category->id)
            ->map(fn ($grupo) => [
                'nombre' => $grupo->first()->vehicle->category->name,
                'dias'   => (int) $grupo->sum('total_dias'),
            ])
            ->sortByDesc('dias')
            ->first();

        $topVehiculos = $rentasConcretadas
            ->filter(fn ($r) => $r->vehicle)
            ->groupBy('vehicle_id')
            ->map(fn ($grupo) => [
                'vehiculo' => $grupo->first()->vehicle,
                'dias'     => (int) $grupo->sum('total_dias'),
            ])
            ->sortByDesc('dias')
            ->take(5)
            ->values();

        $ingresos = (float) $rentasPeriodo->whereIn('estado', self::ESTADOS_INGRESO)->sum('costo_total');

        // Comparativo histórico (ingresos y % de utilización) contra el periodo anterior equivalente
        $rentasAnterior       = Renta::whereBetween('created_at', [$desdeAnterior, $hastaAnterior])->get(['estado', 'total_dias', 'costo_total']);
        $ingresosAnterior     = (float) $rentasAnterior->whereIn('estado', self::ESTADOS_INGRESO)->sum('costo_total');
        $diasRentadosAnterior = (int) $rentasAnterior->where('estado', '!=', 'cancelada')->sum('total_dias');
        $diasPeriodoAnterior  = $this->contarDias($desdeAnterior, $hastaAnterior);
        $diasDisponiblesAnterior = $vehiculosActivos * $diasPeriodoAnterior;
        $utilizacionAnterior     = $diasDisponiblesAnterior > 0 ? round(($diasRentadosAnterior / $diasDisponiblesAnterior) * 100, 1) : 0.0;

        $variacionIngresos    = $ingresosAnterior > 0 ? round((($ingresos - $ingresosAnterior) / $ingresosAnterior) * 100, 1) : null;
        $variacionUtilizacion = round($porcentajeUtilizacion - $utilizacionAnterior, 1);

        $series = $this->construirSeries($rentasPeriodo, $desde, $hasta, $vehiculosActivos);

        return view('reportes.index', [
            'periodo'              => $periodo,
            'estadoFiltro'         => $estadoFiltro,
            'estadosDisponibles'   => self::ESTADOS_RENTA,
            'desde'                => $desde,
            'hasta'                => $hasta,
            'totalVehiculos'       => $totalVehiculos,
            'totalReservas'        => $totalReservas,
            'diasRentados'         => $diasRentados,
            'diasDisponibles'      => $diasDisponibles,
            'porcentajeUtilizacion' => $porcentajeUtilizacion,
            'categoriaMasRentada'  => $categoriaMasRentada,
            'topVehiculos'         => $topVehiculos,
            'ingresos'             => $ingresos,
            'variacionIngresos'    => $variacionIngresos,
            'utilizacionAnterior'  => $utilizacionAnterior,
            'variacionUtilizacion' => $variacionUtilizacion,
            'series'               => $series,
        ]);
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
     * Series de tendencia (ocupación, ingresos, demanda) para las gráficas de líneas.
     * Diaria si el periodo cabe en ~2 meses; mensual si es más largo (ej. filtro "año").
     */
    private function construirSeries(Collection $rentasPeriodo, Carbon $desde, Carbon $hasta, int $vehiculosActivos): array
    {
        $duracionDias = $this->contarDias($desde, $hasta);
        $mensual      = $duracionDias > 62;

        $etiquetas = [];
        $ocupacion = [];
        $ingresos  = [];
        $demanda   = [];

        if (!$mensual) {
            $cursor = $desde->copy()->startOfDay();
            while ($cursor->lte($hasta)) {
                $dia    = $cursor->copy();
                $delDia = $rentasPeriodo->filter(fn ($r) => $r->created_at->isSameDay($dia));

                $diasRentadosDia = $delDia->where('estado', '!=', 'cancelada')->sum('total_dias');

                $etiquetas[] = $dia->format('d/m');
                $ocupacion[] = $vehiculosActivos > 0 ? round(($diasRentadosDia / $vehiculosActivos) * 100, 1) : 0;
                $ingresos[]  = (float) $delDia->whereIn('estado', self::ESTADOS_INGRESO)->sum('costo_total');
                $demanda[]   = $delDia->where('estado', '!=', 'cancelada')->count();

                $cursor->addDay();
            }
        } else {
            $cursor = $desde->copy()->startOfMonth();
            while ($cursor->lte($hasta)) {
                $mes    = $cursor->copy();
                $delMes = $rentasPeriodo->filter(fn ($r) => $r->created_at->isSameMonth($mes));

                $diasDisponiblesMes = $vehiculosActivos * $mes->daysInMonth;
                $diasRentadosMes    = $delMes->where('estado', '!=', 'cancelada')->sum('total_dias');

                $etiquetas[] = ucfirst($mes->translatedFormat('M Y'));
                $ocupacion[] = $diasDisponiblesMes > 0 ? round(($diasRentadosMes / $diasDisponiblesMes) * 100, 1) : 0;
                $ingresos[]  = (float) $delMes->whereIn('estado', self::ESTADOS_INGRESO)->sum('costo_total');
                $demanda[]   = $delMes->where('estado', '!=', 'cancelada')->count();

                $cursor->addMonth();
            }
        }

        return compact('etiquetas', 'ocupacion', 'ingresos', 'demanda');
    }
}
