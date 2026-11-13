# Flash Car — Contexto del Proyecto

## Stack
- **Framework**: Laravel 12, PHP 8.2+
- **Frontend**: Blade + Bootstrap 5.3 + Bootstrap Icons (`bi bi-*`)
- **CSS propio**: dos archivos separados (ver sección CSS)
- **Base de datos**: MySQL — base `flash_car`
- **Imágenes**: Intervention Image v3 (compresión a WebP)
- **Correo**: SMTP Neubox — host `mail.rentadeautos.site`, puerto 465, scheme `smtps`
- **Storage**: `FILESYSTEM_DISK=local`, imágenes públicas via `Storage::url()`

## Descripción
Plataforma de renta de autos 100% queretana. Tiene dos lados:
- **Público**: catálogo, detalle de vehículo, formulario de renta, FAQs
- **Admin**: dashboard, vehículos, categorías, rentas, usuarios, slider, estados/puntos de entrega

## Roles de usuario
| Rol | Acceso |
|---|---|
| `super_admin` | Todo, incluyendo caché, settings, super-admin panel |
| `admin` | Dashboard, vehículos, rentas, slider, usuarios, estados, reportes |
| *(autenticado)* | Dashboard, ver rentas/vehículos (sin eliminar) |

Middleware de roles: `role:admin,super_admin` — definido en `app/Http/Middleware/`.

## Modelos principales

### `Vehicle`
- Campos: `name`, `brand`, `model`, `year`, `transmission` (enum: manual/automatic), `passengers`, `fuel_capacity`, `available` (bool), `image_path`, `category_id`, `active`, `mileage`, `next_verification`, `plate_number`, `proveedor` (nombre del proveedor, texto libre, opcional), `fecha_pago_proveedor` (date, opcional), `costo_por_dia` (decimal 10,2, opcional — costo diario del auto con el proveedor), `slug`
- Relaciones: `belongsTo(Category)`, `hasMany(VehicleImage)`, `hasMany(Renta)`, `belongsToMany(State)` via pivot `state_vehicle`
- Accessor: `formatted_fuel_capacity`
- Las ciudades donde opera un vehículo se gestionan con la pivot `state_vehicle` (no con el campo `city` que queda en desuso)
- **Disponibilidad por fechas** (sesión 2026-09-25): `available` es solo el interruptor manual. Un auto solo se bloquea en el rango `fecha_entrega+hora_entrega` → `fecha_devolucion+hora_devolucion` de sus rentas con estado en `Vehicle::ESTADOS_OCUPAN` (`reserva_confirmada`, `proxima_entrega`, `pendiente_pago`, `contrato_abierto`). Horas en `Vehicle::TZ_OPERACION` (America/Mexico_City; la app corre en UTC). Después de cada devolución el auto sigue bloqueado `Vehicle::HORAS_PREPARACION` (8 h) para revisión; aplica en ambos sentidos (una renta nueva también debe terminar 8 h antes de la siguiente entrega) y `ocupado_hasta` ya incluye esas horas.
  - `scopeLibreEntre($entrega, $devolucion, $exceptoRentaId)` / `estaLibreEntre()` (formato 'Y-m-d H:i'): activo + available + sin renta que se cruce. Se usa al asignar auto por categoría y al validar en `createPaymentIntent()`, `store()`, webhook de Stripe y `update()` de rentas; `RentaController::rangoRenta()` arma el rango.
  - `rentasEnCurso()` (rentas cuyo rango cubre el momento actual), `scopeDisponibleAhora()`, accessors `ocupado_hasta` / `disponible_ahora` → etiqueta admin "No disponible hasta dd/mm/aaaa HH:mm" mientras la renta está en curso. Eager-load `rentasEnCurso` al listar.
  - Catálogo público: `catalogo()` y `buscar()` pasan por `VehicleController::autosDelCatalogo()`: por cada categoría activa se muestran los autos disponibles con `mostrar_en_catalogo = true`; si ninguno marcado está disponible, se muestra el primero (por nombre) de la categoría. `catalogo()` toma autos `disponibleAhora()`; `buscar()` activos + available sin cruces por fecha. Filtros de categoría/pasajeros se arman con las tarjetas mostradas.
  - `mostrar_en_catalogo` (bool, migración `2026_09_25_000004` marca por default el primer auto activo+disponible de cada categoría): checkbox en el formulario de vehículo y en las tarjetas de `por_categoria` (PATCH `vehiculos.catalogo` → `toggleCatalogo()`, solo admin).
  - Con pago con tarjeta, `store()` NO rechaza un auto ocupado (el cobro ya se hizo); la validación se hace antes, en `createPaymentIntent()`. Sin tarjeta, `store()` sí rechaza.

### `Category`
- Campos: `name`, `price_per_day`, `price_per_week`, `price_per_month`, `warranty`, `active`
- Accessors: `formatted_price_per_day`, `formatted_price_per_week`, `formatted_price_per_month`, `formatted_warranty`

### `Renta`
- Campos: `vehicle_id`, `nombre_completo`, `telefono`, `correo`, `ciudad`, `fecha_entrega`, `hora_entrega`, `lugar_entrega`, `fecha_devolucion`, `hora_devolucion`, `lugar_devolucion`, `num_pasajeros`, `total_dias`, `costo_total`, `estado`, `mail_enviado`, `mail_enviado_at`
- `precio_dia` / `precio_semana` / `precio_mes` (decimal, nullable, migración `2026_09_29_000002`): precios de la categoría con que se calculó `costo_total`. Los devuelve `RentaPricingCalculator::calcular()['precios']` y se guardan en `store()` y el webhook (vía metadata del PaymentIntent). Rentas anteriores quedan en null → el detalle usa precios actuales de la categoría.
- Desglose del costo en `rentas/show` (primer cuadro de "Resumen de Costo"): `RentaPricingCalculator::desglose()` (misma regla que el total: meses de 30, semanas de 7, días sueltos); avisa si no coincide con `costo_total`. Con día extra (tolerancia 2 h) muestra subtotal de días base + "1 día extra" a precio diario + "Ajuste por tarifa semanal/mensual" si al sumar el extra se completa semana/mes, para que la suma dé el total.
- `costo_por_dia` (decimal, nullable, migración `2026_09_29_000003`): costo por día del auto para esa renta; si es null se muestra el `costo_por_dia` del vehículo ("Tomado del vehículo"). Nunca modifica el vehículo. En el cuadro de desglose, debajo del total, se muestra "Descuentos" (siempre, aunque sea $0), "Monto real" (`costo_total` − `descuento`; también es la tarjeta "Monto real" que sustituyó a "Costo total de renta"), luego `total_dias × costo_por_dia` ("Costo por día del auto") y al final "Total generado" = `costo_total` (registrado) − costo del auto − `descuento` (rojo si es negativo).
- `descuento` (decimal, nullable, migración `2026_09_29_000004`): monto de descuentos; no puede superar `costo_total`. Se resta en el "Total generado" (y por lo tanto en los ingresos de reportes); NO modifica `costo_total`. En `rentas/show` el "Saldo pendiente" = monto real (`costo_total` − `descuento`) − `monto_anticipo`.
- `total_general` (decimal, nullable, migración `2026_09_30_000001`, que también llena las rentas existentes; en pantalla se etiqueta "Total generado"): se guarda como `costo_total − total_dias × costoPorDiaAplicado() − descuento`. Lo calcula `Renta::calcularTotalGeneral()` en el evento `saving`, así que se actualiza solo en store/update/webhook/edición rápida. Si cambia el `costo_por_dia` de un vehículo, `Vehicle::updated` recalcula las rentas de ese auto que no tienen costo propio (sin mover `updated_at`). No usar `->update()` masivo por query builder sobre esos campos, porque se saltaría el recálculo.
- `desglose_original` (json, nullable, migración `2026_10_19_000001`): foto fija (historial) de fechas/horas, días, costo, vehículo/categoría y desglose al generar la renta. La llena `Renta::armarDesgloseOriginal()` en el evento `creating` (store y webhook) y nunca se vuelve a tocar; se muestra en `rentas/show` como cuadro "Desglose original" en la primera fila de la calculadora (columna derecha). Las rentas anteriores se llenaron en la migración con sus datos de ese momento y llevan `reconstruido: true` (pudieron haberse editado antes). El cálculo del desglose vive en `RentaPricingCalculator::desgloseDeRenta()` (lo usa también `show()`).
- Tarjeta "Garantía" en `rentas/show` (después de "Saldo pendiente"): `warranty` de la categoría actual del vehículo rentado (no se guarda en la renta).
- Edición rápida en `rentas/show` (tarjetas con lápiz del Resumen de Costo): partial `rentas/partials/tarjeta_editable.blade.php` + `PATCH /rentas/{id}/campo/{campo}` (`rentas.campoRapido` → `updateCampoRapido()`). Campos permitidos en `RentaController::CAMPOS_RAPIDOS` (la ruta rechaza otros con 404); para agregar uno: columna + fillable + entrada en esa constante + `@include` del partial. Vacío = null.
- `estado` valores: `reserva_confirmada` | `proxima_entrega` | `pendiente_pago` | `contrato_abierto` | `contrato_finalizado` | `devolucion_exitosa` | `dano_faltante` | `garantia_pendiente` | `cancelada`
- Estado inicial al crear una renta: `reserva_confirmada` (default en BD)
- `motivo_cancelacion` (string, nullable, migración `2026_11_12_000001`): obligatorio al pasar a `cancelada` (`updateEstado()` y `update()`, `required_if`); valores en `Renta::MOTIVOS_CANCELACION` (`falta_disponibilidad`, `cliente_desistio`, `pago_no_completado`, `otro`). El evento `saving` lo limpia si el estado no es `cancelada`. Selector en el partial `rentas/partials/motivo_cancelacion.blade.php` (incluido en el form de estado de `rentas/show` y en `rentas/edit`; se muestra solo con estado cancelada). `falta_disponibilidad` cuenta como demanda no atendida en Reportes. Rentas canceladas antes de la migración quedan sin motivo.
- `mail_enviado`: bool — indica si el correo de confirmación se envió sin errores
- `mail_enviado_at`: timestamp del envío exitoso
- Relación: `belongsTo(Vehicle)`

### `SolicitudNoAtendida` (tabla `solicitudes_no_atendidas`, migración `2026_11_12_000002`)
- Solicitud de renta rechazada por falta de auto libre (nunca llega a ser `Renta`): `category_id`, `vehicle_id` (solo si pidió un auto específico), fechas/horas, `total_dias` (con `RentaPricingCalculator`), `nombre_completo`, `correo`, `origen` (`SolicitudNoAtendida::ORIGENES`: `pago_categoria`, `pago_vehiculo`, `formulario`), `intentos`.
- La llena `RentaController::registrarNoAtendida()` en los rechazos de `createPaymentIntent()` (categoría sin autos libres / auto elegido ocupado) y de `store()` (sin autos en la categoría / sin tarjeta y auto ocupado). Reintentos del mismo correo + categoría + auto + fechas el mismo día suman `intentos`. Nunca rompe el flujo (errores solo al log). El webhook de Stripe no rechaza rentas pagadas, así que no registra.

### `VehiculoDisponibilidad` (tabla `vehiculo_disponibilidad`, migración `2026_11_12_000003`)
- Historial de la flota: `vehicle_id` (sin FK a propósito, se conserva si se borra el auto), `active`, `available`, `desde` (UTC), `reconstruido`. Lo llenan eventos de `Vehicle`: `created`, `updated` (si cambia `active`/`available`) y `deleted` (registra baja). El estado de un auto en una fecha = su último registro con `desde` ≤ cierre del día (hora de operación).
- La migración sembró un registro por auto existente con su estado actual desde `min(created_at, primera fecha_entrega)` y `reconstruido = true` (aproximación: antes no había historial).
- No actualizar `active`/`available` con `->update()` masivo por query builder: se saltaría el historial.

### `SliderImage`
- Campos: `type` (enum: desktop/mobile), `image_path`, `orden`, `active`
- Scopes: `scopeDesktop`, `scopeMobile`, `scopeActive`
- Desktop: comprimido a WebP 2000×1100 (`cover`)
- Móvil: comprimido a WebP 1200×2000 (`cover`)

### `State` / `DeliveryPoint`
- Estado tiene nombre y `active`, con múltiples puntos de entrega (`hasMany(DeliveryPoint)`)
- Puntos de entrega usados en el formulario de renta (select anidado)
- Relación: `belongsToMany(Vehicle)` via pivot `state_vehicle` — permite asociar un estado a múltiples vehículos

### `User`
- Campos estándar Laravel + `role` (string)

## Controladores

| Controlador | Ruta base | Notas |
|---|---|---|
| `DashboardController` | `/dashboard` | KPIs, rentas recientes, entregas próximas |
| `VehicleController` | `/vehiculos`, `/catalogo` | `index()` solo lista categorías (con conteo de autos + botón "Ver autos"); `porCategoria($id)` en `/vehiculos/categoria/{id}` muestra las tarjetas de autos (`sin-categoria` = `category_id` null); store/update/destroy redirigen a la categoría del auto; `catalogo()` y `buscar()` pasan `$sliderDesktop` y `$sliderMobile`; ciudades via `State::whereHas('vehicles')`; búsqueda via `whereHas('states')` |
| `RentaController` | `/rentas`, `/rentar` | `edit()`: select de categoría (solo filtra, no se guarda) + select de vehículo filtrado por JS a todos los autos de esa categoría; los que no están libres en las fechas de la renta (`libreEntre`) salen deshabilitados con el motivo (inactivo / no disponible / ocupado en esas fechas), el vehículo actual siempre es seleccionable; `update()` rechaza cambiar a un vehículo no disponible; si cambian fechas/horas o la categoría del auto recalcula `total_dias`, `costo_total` y precios con `RentaPricingCalculator::calcular()` (precios guardados de la renta; los de la nueva categoría si cambió), lo que actualiza desglose, saldo y `total_general`. No recibe costo por día ni descuento (se editan en el detalle). El desglose al crear queda fijo en `desglose_original`; `store()` envía correo y guarda `mail_enviado`; `reenviarCorreo()` para reenvío manual |
| `SliderController` | `/slider` | upload, toggle active, reorder, destroy |
| `CategoryController` | `/categorias` | CRUD categorías |
| `StateController` | `/states` | CRUD estados + puntos de entrega |
| `SystemController` | `/system/cache`, `/system/migrations` | Limpieza de caché y gestión de migraciones (solo `super_admin`) |
| `AuthController` | `/login`, `/logout` | — |
| `UserController` | `/users` | CRUD usuarios |
| `ReporteController` | `/reportes` | Dashboard ejecutivo (ver sección Módulo de Reportes) — acceso `admin` y `super_admin` |

## Correo
- **Mailable**: `app/Mail/RentaSolicitada.php`
- **Template**: `resources/views/emails/renta_solicitada.blade.php`
- Se envía al cliente al hacer submit del formulario de renta
- Si falla, se loguea en `storage/logs/laravel.log` y `mail_enviado` queda en `false`
- Desde la lista de rentas, el ícono rojo (`bi-envelope-x-fill`) es clickeable para reenviar

## Notificaciones (campana del header admin)
- Campana en `layout/header.blade.php` (`#boton_notificaciones`, badge `#badge_notificaciones`, panel `#panel_notificaciones`); JS en `public/js/scripts_header.js` (se cierra con clic fuera, no usa `.click_any_side`).
- Datos: view composer de `layout.header` en `AppServiceProvider::boot()` → `$notificaciones` (colección de `['icono','titulo','detalle','urgente','url']`). Solo se llena para `hasAdminAccess()`.
- Se calculan al vuelo en cada carga. Para agregar otra alerta, sumar elementos a esa colección con una `clave` única (`tipo:id:fecha`); incluir la fecha hace que la alerta vuelva a salir cuando cambia (ej. siguiente pago).
- Leído/no leído: tabla `notificaciones_leidas` (`user_id`, `clave`, único por par) — modelo `NotificacionLeida`, por usuario. `POST /notificaciones/leidas` (`notificaciones.leidas`, `NotificacionController::marcarLeidas`, recibe `claves[]`) se llama por fetch al hacer clic en una alerta o en "Marcar todas como leídas"; borra marcas de más de 60 días. El badge cuenta solo las no leídas; las leídas se muestran al final con opacidad.
- Alerta: pago a proveedor — `Vehicle::scopePagoProveedorProximo()` = `fecha_pago_proveedor` entre hoy y hoy + `Vehicle::DIAS_AVISO_PAGO_PROVEEDOR` (3) en hora local; urgente (rojo) si es hoy o mañana; enlaza a `vehicles.show`.
- Alerta: devolución vencida sin cerrar — `Renta::scopeDevolucionVencida()` = estado en `Renta::ESTADOS_SIN_CERRAR` (`reserva_confirmada`, `proxima_entrega`, `contrato_abierto`) con fecha+hora de devolución ya pasada (hora local); clave `renta_vencida:id:fecha:estado`; urgente si lleva ≥1 día; enlaza a `rentas.show`. Sirve para que el personal cierre los estados y los reportes cuenten bien.

## Módulo de Reportes (`ReporteController` / `reportes/index.blade.php`)
Dashboard ejecutivo con filtros de periodo (`7d`, `30d`, `mes`, `anio`, `personalizado`) y de **categoría** (`categoria`, sustituyó al filtro de estado el 2026-11-12; aplica a todo el reporte usando la categoría actual de cada auto: flota, rentas, devengado, solicitudes no atendidas y rankings). Basado en el manual funcional `reportes.md` (referencia, no eliminar — documenta fórmulas de KPIs de renta de autos en general).

**Principio del documento:** no mezclar conceptos que son matemáticamente distintos — **ocupación** (foto diaria, 0–100%), **utilización** (acumulado del periodo, vehículo-días), **demanda** (solicitudes recibidas vs. concretadas) y **productividad de flota** (ingresos) son 4 cosas separadas. Si la demanda supera la capacidad, se reporta como "demanda no atendida", nunca como ocupación >100%.

**Estados de `Renta` considerados "renta efectiva" (el auto sí se rentó):** `contrato_abierto`, `contrato_finalizado`, `devolucion_exitosa`, `dano_faltante`, `garantia_pendiente` (los dos últimos agregados el 2026-11-12) — constante pública `ReporteController::ESTADOS_INGRESO`. Base de ingresos, ventas, vehículo-días, ocupación, rentas concretadas y de "Ingresos del mes" del dashboard. Si se agregan más KPIs, reusar esta misma constante (no definir otra lista de estados en otro lado).

**Ingreso = `rentas.total_general`** (sesión 2026-09-30), no `costo_total`; se toma de `ReporteController::CAMPO_INGRESO`. Desde 2026-11-12 hay dos medidas:
- **Ingreso devengado** (`calcularIngresoDevengado()`): el total generado de cada renta efectiva se reparte en partes iguales entre sus noches y al periodo le toca lo de los días dentro del rango que ya ocurrieron. Es el KPI "Ingreso devengado" (con comparativo), la línea principal de la gráfica de Ingresos, ingreso por día rentado, ingreso por vehículo y el Top por ingreso.
- **Ventas** (por `created_at`): total generado de las rentas efectivas creadas en el periodo. Tarjeta "Ventas del periodo", línea punteada de la gráfica, base del ticket promedio, y "Ingresos del mes" del dashboard.
- El comparativo de ingreso en un periodo que no ha terminado usa los mismos días transcurridos del periodo anterior; si el periodo aún no empieza no hay comparativo.
- El sistema no maneja IVA: la sección Ingresos lo aclara ("importes tal como se cobran"). Pendiente que el cliente defina si los precios incluyen IVA.

**Gráfica de Ocupación (corregida sesión 2026-09-18):**
- Antes sumaba `total_dias` de rentas *creadas* ese día / vehículos activos → podía dar 150%, 240%, etc. (bug descrito en el documento).
- Ahora: `calcularOcupacionDiaria()` cuenta vehículos **únicos** (solo flota `active=true`) con una renta en `ESTADOS_INGRESO` cuyo rango `fecha_entrega`–`fecha_devolucion` cubre ese día, dividido entre el total de flota activa (desde 2026-11-12 sale del mapa de `calcularVehiculoDias()`, ver abajo). Numerador y denominador restringidos a la misma flota activa actual → nunca puede superar 100% (el chart además fija el eje Y en 0–100%).
- Desde 2026-11-12 el denominador sale del historial `VehiculoDisponibilidad` (ver "Flota histórica" abajo); antes de esa fecha el historial es reconstruido con el estado actual de cada auto.
- En rangos largos (>62 días, ej. filtro "año") el bucket mensual es el **promedio** de la ocupación diaria de ese mes, no una fórmula distinta.

**Gráfica de Demanda (corregida sesión 2026-09-18):**
- Antes era una sola línea, etiquetada "reservas concretadas" pero en realidad contaba reservas no-canceladas creadas ese día (ni era demanda total ni eran solo las concretadas).
- Ahora son dos líneas basadas en `created_at`: **Demanda recibida** (todas las rentas creadas ese día/mes, cualquier estado) y **Rentas concretadas** (estado en `ESTADOS_INGRESO`).

**Vehículo-días (sesión 2026-11-12):** `calcularVehiculoDias($desde, $hasta, $flotaPorDia)` es la base única de Ocupación, Utilización, Días rentados, Top 5, Categoría más rentada y el comparativo de utilización. Devuelve `['Y-m-d' => [vehicle_id => true]]`: solo rentas en `ESTADOS_INGRESO`, días **por noches** (`nochesDeRenta()`: de `fecha_entrega` al día anterior a `fecha_devolucion`; si son el mismo día cuenta 1; coincide con lo cobrado), solo los que caen en el rango, máximo 1 vehículo-día por auto y fecha, y solo si ese día el auto estaba en la flota. Helpers: `sumarVehiculoDias()`, `diasPorVehiculo()`, `rentasQueCruzan()`.

**Días futuros:** `calcularFlotaPorDia()` deja sin flota los días posteriores a hoy (hora local), así no suman capacidad ni utilización; el devengado tampoco cuenta días futuros. En la vista, capacidad usa `diasTranscurridos` y las series quedan en null (hueco) para fechas futuras. "Ocupación del último día" = último día del rango que ya ocurrió.

**KPIs de la sección 13 (sesión 2026-11-12):** la columna izquierda de `reportes/index` tiene 3 bloques (Flota y ocupación · Ingresos · Comercial) armados con el partial `reportes/partials/kpi.blade.php` (titulo, valor, unidad, detalle, detalleClase, detalleIcono, color, ayuda = tooltip con la fórmula). Definiciones en `index()`:
- Flota activa (base de los %) + `Vehicle::disponibleAhora()` como "disponibles para rentar ahora"; ocupación del último día del rango; capacidad = activos × días; vehículos utilizados = autos con ≥1 vehículo-día.
- Demanda recibida / en días = todas las rentas creadas en el periodo (cualquier estado) + solicitudes no atendidas; concretadas = `ESTADOS_INGRESO`; conversión = concretadas / recibidas.
- Clientes únicos = correo en minúsculas (o teléfono si no hay correo) de las concretadas — no existe `customer_id`.
- Ticket = ventas / concretadas; duración promedio = `total_dias` de las concretadas / concretadas (no vehículo-días del rango, para no partir contratos); ingreso por día rentado = devengado / vehículo-días; ingreso por vehículo = devengado / flota promedio (capacidad ÷ días transcurridos).

**Gráficas y Top 5 (sesión 2026-11-12):**
- Gráficas en 2×2: Ocupación diaria (línea punteada = promedio del periodo; tooltip "X de Y autos rentados", en buckets mensuales "promedio X de Y autos por día") · Utilización acumulada (Σ vehículo-días rentados / Σ disponibles desde el inicio del rango; el último punto = KPI "Utilización del periodo") · Ingresos · Demanda. Datos en `construirSeries()`: `rentados`, `ocupacionPromedio`, `flotaActiva`, `utilizacionAcumulada`, `rentadosAcumulados`, `capacidadAcumulada`, `mensual`.
- Top 5 alternable sin recargar (botones Vehículos/Categorías + select de criterio, listas `.lista_top` renderizadas en servidor) con `ReporteController::CRITERIOS_RANKING` (dias, utilizacion, ingreso, contratos, no_atendida), armado en `construirRankings()`. Días = vehículo-días del rango; utilización = días / días en flota (historial) del auto o de los autos de la categoría; ingreso = devengado; contratos = rentas concretadas creadas en el periodo. "Categoría más rentada" = primer lugar de `categorias.dias`.

**Flota histórica y demanda no atendida (sesión 2026-11-12):**
- `calcularFlotaPorDia($desde, $hasta)` arma `['Y-m-d' => [vehicle_id => true]]` desde `VehiculoDisponibilidad` (un auto cuenta si está activo; si `ReporteController::FLOTA_REQUIERE_AVAILABLE` = true, además debe estar marcado disponible — regla que define FlashCar, hoy false). Es el denominador de ocupación, utilización, capacidad, utilización por vehículo/categoría y del comparativo. `calcularVehiculoDias()` solo cuenta un auto rentado si ese día estaba en la flota → nunca >100%. Los días sin flota quedan en null en la serie de ocupación (hueco), y `ocupacionPromedio` es ponderado (Σ rentados / Σ flota = utilización).
- Demanda no atendida = `SolicitudNoAtendida` creadas en el periodo + rentas creadas en el periodo canceladas con `falta_disponibilidad`. Tarjeta "Demanda no atendida" (solicitudes y días), serie `noAtendida` (3ª línea punteada roja en Demanda), criterio `no_atendida` del Top 5 (categoría: por `category_id`; vehículo: solo cuando se pidió ese auto). "Demanda recibida" / "Demanda en días" (y por lo tanto la conversión) incluyen las solicitudes no atendidas, como el ejemplo de la sección 5 del manual.

**Descarga en PDF (sesión 2026-11-13):** botón "Descargar PDF" en `reportes/index` → `POST /reportes/pdf` (`reportes.pdf` → `ReporteController::pdf()`), paquete `barryvdh/laravel-dompdf`. Los filtros viajan en la query string (mismo `resolverRango()`), y los cálculos salen de `datosReporte()`, que comparte con `index()`; cualquier KPI nuevo se agrega ahí y en las dos vistas. Dompdf no ejecuta JS: al enviar, el navegador manda cada gráfica de Chart.js como PNG base64 (`graficas[ocupacion|utilizacion|ingresos|demanda]`, se validan como `data:image/png;base64`). Vista `reportes/pdf.blade.php`: solo tablas y CSS simple (sin Bootstrap/flex), fuente DejaVu Sans, carta vertical. Trae los KPIs con su fórmula escrita, las 4 gráficas y el Top 5 con todos los criterios (vehículos y categorías). El logo webp se convierte a PNG con Intervention (`logoParaPdf()`); el número de página se escribe con `page_text` porque `counter(pages)` no funciona en Dompdf.

**Pendiente del documento `reportes.md` (no implementado, fuera de alcance de la sesión 2026-09-18):**
- Ingresos siguen tomándose por `created_at` de la renta (falta definir con el cliente la fecha de reconocimiento del ingreso). Utilización, Días rentados, Top 5 y Categoría ya se corrigieron (2026-11-12).
- Serie opcional de días solicitados en Demanda (no se agregó para no usar doble eje).
- Decisiones pendientes con el cliente: si los precios incluyen IVA y si un auto "no disponible" cuenta como capacidad (`FLOTA_REQUIERE_AVAILABLE`, hoy false). Ya aplicado 2026-11-12: días por noches, devengado + ventas, daño/faltante y garantía pendiente como renta efectiva, filtro de categoría, días futuros sin capacidad, aviso de devoluciones vencidas.

## CSS — dos archivos separados

### `public/css/styles_general.css`
Estilos globales del panel admin y toda la plataforma:
- Variables CSS: `--amarillo`, `--amarillo_bajo`, `--amarillo_fuerte`, `--gris_1`…`--gris_8`, `--color_fondo_footer`, etc.
- Clases de botones: `.boton_link_sm` (40×30px), `.boton_link_lg`, `.boton_link_xxl` (120px ancho fijo — usar `width:auto; padding:0 14px` si el texto es largo), `.b_sm` (height:30px)
- Clases de layout: `.main_principal`, `.space_principal`, `.cont_base`, `.bg_amarillo`, `.bg_gris_8`, `.bg_gris_custom`
- Formularios admin: `.fila_form_f_b`, `.label_form_f_b`, `.input_form_f_b`
- Alertas: `.messenger_alert`, `.dialog_alert`, `.messenger`

### `public/css/styles_pagina_principal.css`
Solo para páginas públicas (inyectado con `@push('styles')`):
- Slider: `.slider_container`, `.slider_track`, `.slide`, `.slider_btn`, `.slider_dots`, `.dot`
- Hero form: `.hero_form_card`, `.hero_titulo`, `.hero_subtitulo`, `.hero_input_wrap`, `.hero_input_icon`, `.hero_input`, `.hero_btn`, `.hero_badges`, `.hero_badge`
- Sección pasos: `.paso_item`, `.paso_numero`, `.paso_icono` (height:120px), `.paso_titulo`, `.paso_desc`, `.paso_flecha`
- Catálogo tarjetas: `.tarjeta_catalogo` (flex horizontal), `.tarjeta_catalogo_img` (46% ancho, object-fit:contain, bg blanco), `.tarjeta_catalogo_body`, `.tarjeta_precio`, `.boton_reservar`, `.badge_disponible`
- Formulario renta etapas: `.etapa_circulo`, `.etapa_linea`
- Contacto, FAQs, footer

## Vistas — estructura

```
resources/views/
├── layout/
│   ├── layouts.blade.php       ← base, incluye @stack('styles')
│   ├── header.blade.php        ← header admin (con nav de módulos)
│   ├── header_user.blade.php   ← header público
│   └── footer.blade.php
├── dashboard.blade.php         ← KPIs + rentas recientes + entregas próximas
├── catalogo/
│   ├── index.blade.php         ← página principal pública (slider + hero + pasos + catálogo)
│   ├── detalle.blade.php       ← detalle de vehículo
│   ├── create_renta.blade.php  ← formulario multietapa (etapa 1: datos, etapa 2: detalles; etapa 3: pagos OCULTA)
│   └── faqs.blade.php
├── rentas/
│   ├── index.blade.php         ← lista paginada (15/página) con badge mail_enviado
│   ├── show.blade.php          ← como create_renta: datos a la izquierda (col-lg-8), calculadora/Resumen de Costo a la derecha (col-lg-4)
│   └── edit.blade.php
├── vehiculos/                  ← CRUD vehículos y categorías
├── slider/
│   └── index.blade.php         ← admin slider con vista previa JS (header + hero form proporcionales)
├── super-admin/
│   ├── cache.blade.php         ← limpieza de caché (solo super_admin)
│   └── migrations.blade.php   ← gestión de migraciones: lista estado, ejecutar pendientes, subir archivo (solo super_admin)
├── emails/
│   └── renta_solicitada.blade.php  ← template HTML inline-styled
└── users/                      ← CRUD usuarios
```

## Formulario de renta — notas importantes
- Tiene `novalidate` en el `<form>` para evitar error de validación en campos ocultos (etapa 2 con `display:none`)
- Teléfono: selector de país + input separado → se combina en `<input type="hidden" name="telefono">` via JS
- Cada país tiene validación de dígitos mínimos/máximos (ej. Francia=9, México=10, USA=10)
- Etapa 3 (pagos) está comentada con `{{-- --}}` — NO eliminar, se integrará después
- Campo oculto `<input type="hidden" name="metodo_pago" value="pendiente">` cubre la etapa 3

## Slider admin — vista previa JS
El panel del slider (`slider/index.blade.php`) tiene una vista previa en tiempo real al seleccionar imágenes:
- Overlay proporcional del **header** (70px / window.innerHeight × cardH)
- Overlay proporcional del **hero form** con: título, subtítulo, 3 inputs blancos, botón amarillo, badges semitransparentes
- Desktop: form a la izquierda, centrado verticalmente; Móvil: form centrado horizontalmente en la parte inferior

## Convenciones del proyecto
- Layout Bootstrap: siempre usar `col-12 col-sm-12 col-md-X col-lg-X` (nunca saltar breakpoints)
- No usar `gap-*` junto con `col-*` de Bootstrap (rompe el grid); usar `p-1` o `px-1` en su lugar
- Imágenes del catálogo: `object-fit: contain` con fondo blanco y `padding: 8px`
- Paginación: `paginate(15)` + `$rentas->links('pagination::bootstrap-5')`
- CSS de páginas públicas siempre inyectado con `@push('styles')` / `@stack('styles')`
- Mensajes flash: `session('success')` y `session('error')` con clase `.messenger_alert`
