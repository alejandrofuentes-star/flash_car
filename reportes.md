# FlashCar — Dashboard de Renta de Autos: KPIs y Fórmulas

> Documento de referencia funcional para el desarrollo del dashboard de FlashCar (para Eruc Technologies).
> Fuente: Manual funcional del Dashboard de Renta de Autos - KPIs, fórmulas y gráficas v2 (septiembre 2026).

## Principio general

**No usar una sola métrica para representar conceptos diferentes.** El error detectado en el dashboard actual es que la gráfica "Ocupación (%)" mostraba valores de 150%, 240% o 380%, lo cual es matemáticamente imposible: una ocupación de flota es una proporción y debe permanecer entre 0% y 100%.

Los 4 conceptos que deben separarse:
- **Ocupación** → ¿qué % de los autos disponibles está rentado en una fecha/momento?
- **Utilización** → ¿qué % de toda la capacidad de renta disponible del periodo se aprovechó?
- **Demanda** → ¿cuántas solicitudes de renta recibió FlashCar y cuántos días/unidades solicitaron?
- **Productividad de flota** → ingresos y rendimiento por vehículo/categoría.

Si existe demanda superior a la capacidad, debe mostrarse como **demanda excedente/no atendida**, nunca como ocupación >100%.

---

## 1. Ocupación de flota (foto diaria)

Mide cuántos vehículos están efectivamente rentados respecto a los disponibles en una fecha determinada.

```
OCUPACIÓN DEL DÍA (%) = vehículos rentados ese día / vehículos disponibles ese día × 100
```

- Ejemplo: 20 disponibles, 10 rentados → 50% de ocupación.
- "Vehículos disponibles" no es igual a todos los registrados: excluir autos dados de baja, no incorporados, en mantenimiento o marcados como no disponibles, según regla operativa de FlashCar.
- El sistema debe conservar **historial de disponibilidad por fecha**.
- **Gráfica:** eje X = fecha; eje Y = 0%–100%. Línea diaria + línea horizontal opcional con promedio del periodo. Tooltip: vehículos rentados / disponibles + %.

---

## 2. Utilización de flota (acumulado del periodo)

Convierte la flota completa en vehículo-días disponibles y compara cuántos de esos días fueron rentados.

```
CAPACIDAD DEL PERIODO = Σ vehículo-días disponibles
UTILIZACIÓN (%) = vehículo-días rentados / vehículo-días disponibles × 100
```

- Ejemplo base: 20 autos × 30 días = 600 vehículo-días. 390 rentados → 390/600 = **65%**.
- **Cálculo robusto (recomendado para programación):** día por día → `Σ vehículos rentados por día / Σ vehículos disponibles por día`. Funciona aunque la flota cambie durante el mes.
- Ejemplo con cambio de flota: días 1–15 con 20 autos (300 veh-día) + días 16–30 con 22 autos (330 veh-día) = capacidad real **630**, no 660. Si se rentaron 410 veh-día → utilización = 65.08%.
- Una renta de 30 días consume 30 vehículo-días (a diferencia de la ocupación diaria, que cuenta ese auto como 1 ocupado cada uno de esos 30 días).

---

## 3. Ocupación vs. Utilización — tabla comparativa

| Concepto | Pregunta que responde | Unidad | Ejemplo |
|---|---|---|---|
| Ocupación diaria | ¿Cuántos autos están rentados hoy? | % de autos | 10 de 20 = 50% |
| Utilización del periodo | ¿Cuánta capacidad del mes se aprovechó? | % vehículo-días | 390 de 600 = 65% |
| Vehículos utilizados | ¿Cuántos autos distintos tuvieron ≥1 renta? | autos únicos | 17 de 20 |
| Días rentados | ¿Cuántos vehículo-días se vendieron? | vehículo-días | 390 días |

Para un periodo completo, el promedio ponderado de ocupación diaria y la utilización basada en vehículo-días **convergen matemáticamente** si ambas usan la misma disponibilidad diaria. La diferencia es de presentación: ocupación → serie diaria; utilización → KPI acumulado del rango.

---

## 4. Demanda (no confundir con rentas concretadas)

Una reserva concretada mide ventas logradas, **no** toda la demanda recibida.

```
DEMANDA EN RESERVAS   = # solicitudes/reservas válidas creadas en el periodo
DEMANDA EN DÍAS        = Σ días solicitados por todas las reservas válidas
RENTAS CONCRETADAS     = contratos/reservas que llegan al estado "renta efectiva"
TASA DE CONVERSIÓN (%) = rentas concretadas / reservas válidas recibidas × 100
```

- Ejemplo: 60 solicitudes; 43 concretadas; 10 canceladas; 4 no atendidas (sin auto); 3 incompletas → demanda = 60, concretadas = 43, conversión = **71.67%**.
- Para planeación de flota, medir también **demanda en vehículo-días solicitados**: una solicitud de 30 días presiona mucho más la capacidad que una de 1 día, aunque ambas cuenten como una reserva.

---

## 5. Demanda no atendida y presión sobre capacidad

```
DEMANDA NO ATENDIDA        = reservas válidas rechazadas/no concretadas específicamente por falta de disponibilidad
DÍAS DE DEMANDA NO ATENDIDA = Σ vehículo-días solicitados que no pudieron asignarse por falta de disponibilidad
```

Permite detectar, por ejemplo, que una categoría (ej. SUV) alcanza 95% de utilización **y además** acumula 80 días de demanda no atendida — dato clave para decidir crecimiento de flota, en vez de permitir que la ocupación rebase 100%.

---

## 6. Ingresos y ticket promedio

```
INGRESOS DEL PERIODO            = Σ cargos reconocidos como ingreso de renta y servicios adicionales
TICKET PROMEDIO                 = ingresos / contratos concretados
INGRESO PROMEDIO POR DÍA RENTADO = ingresos / vehículo-días rentados
```

- Ejemplo: $422,780 / 43 = **$9,832.09** por contrato.
- Los **depósitos/garantías reembolsables NO son ingreso** — excluir siempre.
- Definir si el dashboard trabaja con importes con o sin IVA, y mantener la regla consistente en todo el sistema.
- El ingreso por día rentado permite comparar contratos largos y cortos sin que el ticket total distorsione la lectura.

---

## 7. Duración promedio, clientes y vehículos

```
DURACIÓN PROMEDIO         = vehículo-días rentados / contratos concretados
VEHÍCULOS UTILIZADOS      = COUNT DISTINCT vehicle_id con ≥1 día rentado en el periodo
CLIENTES ÚNICOS           = COUNT DISTINCT customer_id con renta concretada en el periodo
RENTAS POR CLIENTE        = contratos concretados / clientes únicos
INGRESO POR VEHÍCULO DISPONIBLE = ingresos / vehículos disponibles equivalentes del periodo
```

- Ejemplo duración: 272 días rentados / 43 contratos = **6.33 días** por contrato.
- "Vehículos utilizados" ≠ "contratos": un mismo auto puede generar varias rentas.
- "Clientes únicos" evita contar dos veces al mismo cliente con más de un contrato.

---

## 8. Top 5 de vehículos — bug a corregir

**Problema detectado:** aparecen valores como "65 días" o "61 días" para un vehículo individual en un filtro de un solo mes, lo cual es imposible (un mes tiene máx. 31 días). Causa probable: se están sumando días completos de contratos que se extienden fuera del rango, o hay registros duplicados.

**Corrección:**
```
DÍAS RENTADOS DEL VEHÍCULO EN EL PERIODO = Σ intersección entre cada renta y el rango filtrado
```
Ejemplo: contrato del 20 de julio al 10 de agosto, filtro = agosto → solo contar los días que caen en agosto, no toda la duración del contrato.

El Top 5 debe poder alternar entre: **más días rentados**, **mayor utilización %**, **mayor ingreso**, **mayor número de contratos**. Dos contratos superpuestos del mismo vehículo **no** pueden generar dos días de utilización para el mismo día.

---

## 9. Rediseño de las 3 gráficas actuales

| Gráfica actual | Qué debería mostrar | Eje / fórmula |
|---|---|---|
| Ocupación (%) | Ocupación diaria real; nunca >100% | X = fecha; Y = rentados/disponibles × 100 |
| Ingresos (MXN) | Ingreso diario o devengado (según regla contable elegida) | X = fecha; Y = MXN; definir fecha de reconocimiento |
| Demanda (reservas concretadas) | Separar demanda recibida y rentas concretadas | X = fecha; Y = # reservas; opcional días solicitados |

Agregar además: utilización del periodo, capacidad vehículo-día, días rentados, demanda total, demanda no atendida, conversión, ticket promedio y duración promedio.

---

## 10. Reglas de programación (obligatorias)

1. Todos los KPIs deben respetar exactamente el rango de fechas seleccionado.
2. Contratos que cruzan el inicio/fin del rango: contar únicamente la porción dentro del periodo.
3. Un vehículo solo puede aportar como máximo **1 vehículo-día rentado por fecha** (evitar doble conteo por contratos superpuestos).
4. La ocupación diaria **no puede superar 100%**.
5. No usar el número actual de autos para reconstruir meses históricos; usar disponibilidad histórica diaria.
6. Separar siempre: reservas creadas, reservas canceladas, demanda no atendida y contratos concretados.
7. Definir qué estados cuentan como "renta efectiva" y mantenerlos constantes en todo el sistema.
8. Excluir depósitos reembolsables de ingresos y ticket promedio.
9. Definir si los días se calculan por días calendario o bloques de 24 horas — usar una regla uniforme por fecha operativa.
10. Evitar doble conteo en extensiones: una extensión amplía el rango de la renta, no duplica los mismos días.

---

## 11. Caso de validación integral (para pruebas)

**Datos de entrada:** periodo de 30 días, 20 autos disponibles todo el periodo (600 vehículo-días de capacidad), 390 vehículo-días rentados, 60 reservas recibidas, 43 contratos concretados, 38 clientes únicos, $422,780 de ingresos.

| Indicador | Cálculo | Resultado |
|---|---|---|
| Utilización | 390 / 600 | **65.00%** |
| Conversión | 43 / 60 | **71.67%** |
| Ticket promedio | $422,780 / 43 | **$9,832.09** |
| Duración promedio | 390 / 43 | **9.07 días** |
| Ingreso por día rentado | $422,780 / 390 | **$1,084.05** |

**Validación visual:** si en un día específico hay 10 de 20 autos rentados, la gráfica de ocupación debe marcar 50% ese día. El KPI acumulado de utilización del periodo puede, simultáneamente, marcar 65%. No hay contradicción: uno es una fotografía diaria, el otro un resumen acumulado del rango.

---

## 12. Estructura sugerida del dashboard

**Fila 1 — KPIs:**
Flota disponible · Ocupación actual/último día · Utilización del periodo · Capacidad vehículo-día · Días rentados · Ingresos · Ticket promedio.

**Fila 2 — Comercial:**
Demanda recibida · Rentas concretadas · Conversión · Demanda no atendida · Clientes únicos · Duración promedio.

**Gráficas:**
Ocupación diaria (%) · Utilización acumulada (%) · Ingresos diarios · Demanda vs. rentas concretadas · Demanda no atendida · Top vehículos/categorías.

---

*Fuente: FlashCar Rental México · Documento funcional para Eruc Technologies · Septiembre 2026*
