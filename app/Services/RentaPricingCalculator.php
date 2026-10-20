<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Renta;
use Carbon\Carbon;
use InvalidArgumentException;

/**
 * Cálculo autoritativo (servidor) de total_dias y costo_total de una renta.
 *
 * Replica exactamente la regla usada en los calculadores JS del formulario público
 * (public/js/formulario_renta.js) y del panel admin (resources/views/rentas/edit.blade.php):
 * - Días base = diferencia de días de calendario entre fecha_entrega y fecha_devolucion.
 * - Tolerancia de 2 horas: si la hora de devolución supera la hora de entrega por más
 *   de 2 horas, se cobra un día completo adicional.
 *
 * El backend nunca debe confiar en total_dias/costo_total enviados por el navegador —
 * este cálculo es la única fuente de verdad. Si se ajusta la regla de negocio, hacerlo
 * aquí y replicar el cambio en ambos archivos JS (o viceversa).
 */
class RentaPricingCalculator
{
    /** Minutos de tolerancia tras la hora de entrega antes de cobrar un día extra. */
    public const TOLERANCIA_MINUTOS = 120;

    /**
     * @return array{total_dias:int, costo_total:float, cargo_extra:bool, precios:array{precio_dia:float, precio_semana:float, precio_mes:float}}
     * @throws InvalidArgumentException si la fecha/hora de devolución no es posterior a la de entrega
     */
    public static function calcular(
        string $fechaEntrega,
        string $horaEntrega,
        string $fechaDevolucion,
        string $horaDevolucion,
        Category $category
    ): array {
        $entrega    = Carbon::parse($fechaEntrega . ' ' . $horaEntrega);
        $devolucion = Carbon::parse($fechaDevolucion . ' ' . $horaDevolucion);

        if ($devolucion->lessThanOrEqualTo($entrega)) {
            throw new InvalidArgumentException('La fecha y hora de devolución deben ser posteriores a las de entrega.');
        }

        $diasBase = (int) Carbon::parse($fechaEntrega)->startOfDay()
            ->diffInDays(Carbon::parse($fechaDevolucion)->startOfDay());

        $minutosEntrega = self::minutosDelDia($horaEntrega);
        $minutosDevol   = self::minutosDelDia($horaDevolucion);
        $cargoExtra     = ($minutosDevol - $minutosEntrega) > self::TOLERANCIA_MINUTOS;

        $dias = $diasBase === 0 ? 1 : ($cargoExtra ? $diasBase + 1 : $diasBase);

        return [
            'total_dias'  => $dias,
            'costo_total' => self::costoPorDias($dias, $category),
            'cargo_extra' => $cargoExtra,
            // Se guardan en la renta para poder mostrar el desglose aunque la categoría cambie de precio
            'precios'     => [
                'precio_dia'    => (float) $category->price_per_day,
                'precio_semana' => (float) $category->price_per_week,
                'precio_mes'    => (float) $category->price_per_month,
            ],
        ];
    }

    private static function costoPorDias(int $dias, Category $category): float
    {
        return round(array_sum(array_column(self::desglose($dias, $category), 'subtotal')), 2);
    }

    /**
     * Partidas con las que se arma el costo: meses (30 días), semanas (7) y días sueltos.
     * Misma regla que costoPorDias(); se usa para mostrar el desglose en el detalle de la renta.
     *
     * @return array<int, array{concepto:string, cantidad:int, precio:float, subtotal:float}>
     */
    public static function desglose(int $dias, Category $category): array
    {
        $precioDia    = (float) $category->price_per_day;
        $precioSemana = (float) $category->price_per_week;
        $precioMes    = (float) $category->price_per_month;

        $meses = $semanas = 0;
        $diasSueltos = $dias;

        if ($dias >= 30) {
            $meses         = intdiv($dias, 30);
            $diasRestantes = $dias % 30;
            $semanas       = intdiv($diasRestantes, 7);
            $diasSueltos   = $diasRestantes % 7;
        } elseif ($dias >= 7) {
            $semanas     = intdiv($dias, 7);
            $diasSueltos = $dias % 7;
        }

        $partidas = [];
        foreach ([['mes', $meses, $precioMes], ['semana', $semanas, $precioSemana], ['dia', $diasSueltos, $precioDia]] as [$concepto, $cantidad, $precio]) {
            if ($cantidad > 0) {
                $partidas[] = [
                    'concepto' => $concepto,
                    'cantidad' => $cantidad,
                    'precio'   => $precio,
                    'subtotal' => round($cantidad * $precio, 2),
                ];
            }
        }

        return $partidas;
    }

    /**
     * Desglose de una renta ya registrada (lo que se muestra en rentas/show). Usa los precios guardados
     * en la renta; las rentas anteriores a 2026-09-29 no los tienen y se usan los de la categoría.
     *
     * @return array{partidas:array, dias_base:int, subtotal:float, cargo_extra:bool, dia_extra:float, ajuste:float, calculado:float, coincide:bool, guardados:bool}
     */
    public static function desgloseDeRenta(Renta $renta, Category $category): array
    {
        $preciosGuardados = $renta->precio_dia !== null;
        $precios = $preciosGuardados
            ? (new Category)->forceFill([
                'price_per_day'   => $renta->precio_dia,
                'price_per_week'  => $renta->precio_semana,
                'price_per_month' => $renta->precio_mes,
            ])
            : $category;

        try {
            $cargoExtra = self::calcular(
                $renta->fecha_entrega->format('Y-m-d'), (string) $renta->hora_entrega,
                $renta->fecha_devolucion->format('Y-m-d'), (string) $renta->hora_devolucion,
                $category
            )['cargo_extra'];
        } catch (InvalidArgumentException $e) {
            $cargoExtra = false;
        }

        // Solo se cobra el día extra si la renta abarca más de un día de calendario
        $totalDias  = (int) $renta->total_dias;
        $cargoExtra = $cargoExtra && $totalDias > 1
            && $renta->fecha_entrega->diffInDays($renta->fecha_devolucion) > 0;

        // Con día extra: tarifa de los días base + 1 día a precio diario. Como el total se calcula
        // sobre todos los días juntos, si al sumar el extra se completa una semana/mes la diferencia
        // aparece como ajuste por tarifa (así la suma siempre da el total).
        $diasBase  = $cargoExtra ? $totalDias - 1 : $totalDias;
        $partidas  = self::desglose($diasBase, $precios);
        $subtotal  = round(array_sum(array_column($partidas, 'subtotal')), 2);
        $calculado = round(array_sum(array_column(self::desglose($totalDias, $precios), 'subtotal')), 2);
        $diaExtra  = $cargoExtra ? (float) $precios->price_per_day : 0.0;

        return [
            'partidas'    => $partidas,
            'dias_base'   => $diasBase,
            'subtotal'    => $subtotal,
            'cargo_extra' => $cargoExtra,
            'dia_extra'   => $diaExtra,
            'ajuste'      => round($calculado - $subtotal - $diaExtra, 2),
            'calculado'   => $calculado,
            'coincide'    => abs($calculado - (float) $renta->costo_total) < 0.01,
            'guardados'   => $preciosGuardados,
        ];
    }

    private static function minutosDelDia(string $hora): int
    {
        [$horas, $minutos] = array_pad(explode(':', $hora), 2, 0);
        return ((int) $horas * 60) + (int) $minutos;
    }
}
