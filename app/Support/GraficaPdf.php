<?php

namespace App\Support;

/**
 * Gráficas de líneas dibujadas en el servidor con GD para el PDF de Reportes.
 *
 * Dompdf no ejecuta JavaScript (Chart.js), y mandar las gráficas desde el navegador requería un POST
 * pesado que en el servidor llegaba convertido en GET. Aquí se dibujan con los mismos datos de
 * ReporteController::construirSeries() y se devuelven como PNG en base64 (data URI) para <img>.
 *
 * Serie: ['etiqueta' => string, 'datos' => [float|null], 'color' => '#rrggbb',
 *         'punteada' => bool, 'relleno' => bool, 'puntos' => bool]. Los null dejan hueco en la línea.
 * Opciones: 'max' (tope fijo del eje Y, ej. 100 para %), 'formato' (callable para las etiquetas del eje Y),
 *           'leyenda' (bool, default true).
 */
class GraficaPdf
{
    private const ANCHO  = 560;  // tamaño lógico; se dibuja a ESCALA para que se vea nítida en el PDF
    private const ALTO   = 250;
    private const ESCALA = 2;

    private const FUENTE = 'vendor/dompdf/dompdf/lib/fonts/DejaVuSans.ttf';

    private \GdImage $img;
    private ?string $fuente;

    public static function linea(array $etiquetas, array $series, array $opciones = []): ?string
    {
        if (!function_exists('imagecreatetruecolor') || empty($etiquetas)) {
            return null;
        }

        return (new self())->dibujar($etiquetas, $series, $opciones);
    }

    private function dibujar(array $etiquetas, array $series, array $opciones): string
    {
        $s = self::ESCALA;
        $w = self::ANCHO * $s;
        $h = self::ALTO * $s;

        $this->img    = imagecreatetruecolor($w, $h);
        $rutaFuente   = base_path(self::FUENTE);
        $this->fuente = function_exists('imagettftext') && is_file($rutaFuente) ? $rutaFuente : null;

        imagefill($this->img, 0, 0, $this->color('#ffffff'));

        $formato  = $opciones['formato'] ?? fn ($v) => number_format($v);
        $leyenda  = ($opciones['leyenda'] ?? true) && count($series) > 0;
        $tamTexto = 7.5 * $s;

        // ===== Escala del eje Y =====
        $valores = [];
        foreach ($series as $serie) {
            foreach ($serie['datos'] as $v) {
                if ($v !== null) {
                    $valores[] = (float) $v;
                }
            }
        }
        $maxDatos = $valores ? max($valores) : 0;
        [$maxEje, $paso] = isset($opciones['max'])
            ? [(float) $opciones['max'], $opciones['max'] / 5]
            : $this->escalaBonita($maxDatos);

        $etiquetasY = [];
        for ($v = 0; $v <= $maxEje + 1e-9; $v += $paso) {
            $etiquetasY[] = [$v, $formato($v)];
        }
        $anchoEtiquetaY = max(array_map(fn ($e) => $this->anchoTexto($e[1], $tamTexto), $etiquetasY));

        // ===== Área de trazo =====
        $izq    = (int) ($anchoEtiquetaY + 10 * $s);
        $der    = $w - 12 * $s;
        $arriba = 10 * $s;
        $abajo  = $h - ($leyenda ? 42 : 24) * $s;

        $gris      = $this->color('#e9ecef');
        $grisTexto = $this->color('#6c757d');

        foreach ($etiquetasY as [$valor, $texto]) {
            $y = (int) round($abajo - ($valor / $maxEje) * ($abajo - $arriba));
            imageline($this->img, $izq, $y, $der, $y, $gris);
            $this->texto($texto, $tamTexto, $izq - 6 * $s - $this->anchoTexto($texto, $tamTexto), $y + (int) ($tamTexto / 2), $grisTexto);
        }

        $n    = count($etiquetas);
        $xDe  = fn (int $i) => $n === 1 ? ($izq + $der) / 2 : $izq + $i * ($der - $izq) / ($n - 1);
        $yDe  = fn (float $v) => $abajo - (min($v, $maxEje) / $maxEje) * ($abajo - $arriba);

        // Etiquetas del eje X: máximo ~10 para que no se encimen
        $cada = max(1, (int) ceil($n / 10));
        for ($i = 0; $i < $n; $i += $cada) {
            $texto = (string) $etiquetas[$i];
            $ancho = $this->anchoTexto($texto, $tamTexto);
            // Centrada en su punto, sin salirse de la imagen (la primera y la última se cortaban)
            $x = (int) max(2 * $s, min($w - $ancho - 2 * $s, $xDe($i) - $ancho / 2));
            $this->texto($texto, $tamTexto, $x, $abajo + 14 * $s, $grisTexto);
        }
        imageline($this->img, $izq, $abajo, $der, $abajo, $this->color('#ced4da'));

        // ===== Series (relleno primero, luego líneas, para que las líneas queden encima) =====
        foreach ($series as $serie) {
            if (!empty($serie['relleno'])) {
                foreach ($this->tramos($serie['datos']) as $tramo) {
                    if (count($tramo) < 2) {
                        continue;
                    }
                    $puntos = [];
                    foreach ($tramo as $i => $v) {
                        $puntos[] = (int) round($xDe($i));
                        $puntos[] = (int) round($yDe($v));
                    }
                    $ultimo = array_key_last($tramo);
                    $primero = array_key_first($tramo);
                    array_push($puntos, (int) round($xDe($ultimo)), (int) $abajo, (int) round($xDe($primero)), (int) $abajo);
                    imagefilledpolygon($this->img, $puntos, $this->color($serie['color'], 105));
                }
            }
        }

        foreach ($series as $serie) {
            $color  = $this->color($serie['color']);
            $grosor = (int) (($serie['grosor'] ?? 2) * $s);

            foreach ($this->tramos($serie['datos']) as $tramo) {
                $indices = array_keys($tramo);
                for ($k = 1; $k < count($indices); $k++) {
                    $i0 = $indices[$k - 1];
                    $i1 = $indices[$k];
                    $this->segmento($xDe($i0), $yDe($tramo[$i0]), $xDe($i1), $yDe($tramo[$i1]), $color, $grosor, !empty($serie['punteada']));
                }
                if ($serie['puntos'] ?? true) {
                    foreach ($tramo as $i => $v) {
                        imagefilledellipse($this->img, (int) round($xDe($i)), (int) round($yDe($v)), 3 * $s, 3 * $s, $color);
                    }
                }
            }
        }

        // ===== Leyenda =====
        if ($leyenda) {
            $items = array_map(fn ($serie) => [$serie['etiqueta'], $this->color($serie['color']), !empty($serie['punteada'])], $series);
            $anchos = array_map(fn ($it) => 16 * $s + $this->anchoTexto($it[0], $tamTexto) + 14 * $s, $items);
            $x = (int) (($w - array_sum($anchos)) / 2);
            $y = $h - 12 * $s;
            foreach ($items as $k => [$texto, $color, $punteada]) {
                $this->segmento($x, $y - 3 * $s, $x + 12 * $s, $y - 3 * $s, $color, 2 * $s, $punteada);
                $this->texto($texto, $tamTexto, $x + 16 * $s, $y, $this->color('#495057'));
                $x += $anchos[$k];
            }
        }

        ob_start();
        imagepng($this->img, null, 6);
        $png = ob_get_clean();
        imagedestroy($this->img);

        return 'data:image/png;base64,' . base64_encode($png);
    }

    /** Tope del eje y paso "redondos" (1, 2, 2.5, 5 x 10^n) con 4–5 divisiones. */
    private function escalaBonita(float $max): array
    {
        if ($max <= 0) {
            return [5, 1];
        }
        $pasoCrudo = $max / 5;
        $potencia  = 10 ** floor(log10($pasoCrudo));
        foreach ([1, 2, 2.5, 5, 10] as $factor) {
            $paso = $factor * $potencia;
            if ($paso >= $pasoCrudo) {
                break;
            }
        }
        // Valores enteros (contratos, solicitudes): el paso no baja de 1
        $paso = max($paso, $max >= 1 ? 1 : $paso);

        return [ceil($max / $paso) * $paso, $paso];
    }

    /** Divide una serie en tramos continuos (sin null): [[indice => valor, ...], ...]. */
    private function tramos(array $datos): array
    {
        $tramos = [];
        $actual = [];
        foreach (array_values($datos) as $i => $v) {
            if ($v === null) {
                if ($actual) {
                    $tramos[] = $actual;
                }
                $actual = [];
                continue;
            }
            $actual[$i] = (float) $v;
        }
        if ($actual) {
            $tramos[] = $actual;
        }

        return $tramos;
    }

    /** Línea con grosor; punteada = trazos de 6 px con espacios de 4 px (a escala). */
    private function segmento(float $x1, float $y1, float $x2, float $y2, int $color, int $grosor, bool $punteada): void
    {
        imagesetthickness($this->img, max(1, $grosor));
        if (!$punteada) {
            imageline($this->img, (int) round($x1), (int) round($y1), (int) round($x2), (int) round($y2), $color);
        } else {
            $largo = hypot($x2 - $x1, $y2 - $y1);
            $trazo = 6 * self::ESCALA;
            $hueco = 4 * self::ESCALA;
            for ($d = 0; $d < $largo; $d += $trazo + $hueco) {
                $fin = min($d + $trazo, $largo);
                $t0  = $largo > 0 ? $d / $largo : 0;
                $t1  = $largo > 0 ? $fin / $largo : 0;
                imageline(
                    $this->img,
                    (int) round($x1 + ($x2 - $x1) * $t0), (int) round($y1 + ($y2 - $y1) * $t0),
                    (int) round($x1 + ($x2 - $x1) * $t1), (int) round($y1 + ($y2 - $y1) * $t1),
                    $color
                );
            }
        }
        imagesetthickness($this->img, 1);
    }

    private function texto(string $texto, float $tam, int $x, int $y, int $color): void
    {
        if ($this->fuente) {
            imagettftext($this->img, $tam, 0, $x, $y, $color, $this->fuente, $texto);
        } else {
            // Sin FreeType: fuente interna de GD (sin acentos), alineada por la parte de arriba
            imagestring($this->img, 3, $x, $y - 12, iconv('UTF-8', 'ASCII//TRANSLIT', $texto) ?: $texto, $color);
        }
    }

    private function anchoTexto(string $texto, float $tam): int
    {
        if ($this->fuente) {
            $caja = imagettfbbox($tam, 0, $this->fuente, $texto);
            return abs($caja[2] - $caja[0]);
        }

        return strlen($texto) * imagefontwidth(3);
    }

    /** Color desde '#rrggbb'; $alfa 0 (opaco) a 127 (transparente). */
    private function color(string $hex, int $alfa = 0): int
    {
        $hex = ltrim($hex, '#');

        return imagecolorallocatealpha($this->img, hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2)), $alfa);
    }
}
