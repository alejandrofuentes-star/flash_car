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
- `mail_enviado`: bool — indica si el correo de confirmación se envió sin errores
- `mail_enviado_at`: timestamp del envío exitoso
- Relación: `belongsTo(Vehicle)`

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
| `RentaController` | `/rentas`, `/rentar` | `edit()`: select de categoría (solo filtra, no se guarda) + select de vehículo filtrado por JS a los disponibles (`active` y `available`) de esa categoría, siempre incluye el vehículo actual; `update()` rechaza cambiar a un vehículo no disponible; si cambian fechas/horas o la categoría del auto recalcula `total_dias`, `costo_total` y precios con `RentaPricingCalculator::calcular()` (precios guardados de la renta; los de la nueva categoría si cambió), lo que actualiza desglose, saldo y `total_general`. No recibe costo por día ni descuento (se editan en el detalle). El desglose al crear queda fijo en `desglose_original`; `store()` envía correo y guarda `mail_enviado`; `reenviarCorreo()` para reenvío manual |
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
- Alerta actual: pago a proveedor — `Vehicle::scopePagoProveedorProximo()` = `fecha_pago_proveedor` entre hoy y hoy + `Vehicle::DIAS_AVISO_PAGO_PROVEEDOR` (3) en hora local; urgente (rojo) si es hoy o mañana; enlaza a `vehicles.show`.

## Módulo de Reportes (`ReporteController` / `reportes/index.blade.php`)
Dashboard ejecutivo con filtros de periodo (`7d`, `30d`, `mes`, `anio`, `personalizado`) y filtro de estado. Basado en el manual funcional `reportes.md` (referencia, no eliminar — documenta fórmulas de KPIs de renta de autos en general, solo una parte está implementada).

**Principio del documento:** no mezclar conceptos que son matemáticamente distintos — **ocupación** (foto diaria, 0–100%), **utilización** (acumulado del periodo, vehículo-días), **demanda** (solicitudes recibidas vs. concretadas) y **productividad de flota** (ingresos) son 4 cosas separadas. Si la demanda supera la capacidad, se reporta como "demanda no atendida", nunca como ocupación >100%.

**Estados de `Renta` considerados "renta efectiva" (venta lograda):** `contrato_abierto`, `contrato_finalizado`, `devolucion_exitosa` — constante `ReporteController::ESTADOS_INGRESO`. Se usa tanto para ingresos como para "rentas concretadas" en la gráfica de demanda. Si se agregan más KPIs del documento, reusar esta misma constante (no definir otra lista de estados en otro lado).

**Ingreso = `rentas.total_general`** (sesión 2026-09-30), no `costo_total`: KPI "Ingresos del periodo", comparativo contra el periodo anterior, gráfica de Ingresos e "Ingresos del mes" del dashboard. Se toma de la constante `ReporteController::CAMPO_INGRESO`; si se agrega otro KPI de dinero, usar esa constante.

**Gráfica de Ocupación (corregida sesión 2026-09-18):**
- Antes sumaba `total_dias` de rentas *creadas* ese día / vehículos activos → podía dar 150%, 240%, etc. (bug descrito en el documento).
- Ahora: `calcularOcupacionDiaria()` cuenta vehículos **únicos** (solo flota `active=true`) con una renta no cancelada cuyo rango `fecha_entrega`–`fecha_devolucion` cubre ese día, dividido entre el total de flota activa. Numerador y denominador restringidos a la misma flota activa actual → nunca puede superar 100% (el chart además fija el eje Y en 0–100%).
- No hay historial de disponibilidad por fecha en el sistema (el documento lo pide en la sección 1) — se usa la flota activa **actual** como aproximación tanto para el día de hoy como para fechas pasadas. Si se necesita precisión histórica real habría que guardar snapshots de disponibilidad por fecha.
- En rangos largos (>62 días, ej. filtro "año") el bucket mensual es el **promedio** de la ocupación diaria de ese mes, no una fórmula distinta.

**Gráfica de Demanda (corregida sesión 2026-09-18):**
- Antes era una sola línea, etiquetada "reservas concretadas" pero en realidad contaba reservas no-canceladas creadas ese día (ni era demanda total ni eran solo las concretadas).
- Ahora son dos líneas basadas en `created_at`: **Demanda recibida** (todas las rentas creadas ese día/mes, cualquier estado) y **Rentas concretadas** (estado en `ESTADOS_INGRESO`).

**Pendiente del documento `reportes.md` (no implementado, fuera de alcance de la sesión 2026-09-18):**
- Utilización de flota, Ingresos, Días rentados y Top 5 vehículos siguen calculándose sobre `total_dias`/`costo_total` de rentas filtradas por `created_at`, sin recortar la porción del contrato que cae fuera del rango filtrado (mismo tipo de bug ya corregido en Ocupación, pero en otros KPIs — ver secciones 2, 6, 7 y 8 del documento).
- Demanda no atendida (sección 5 del documento): no existe forma de distinguir una reserva cancelada por falta de disponibilidad de una cancelada por otro motivo. Requeriría agregar un campo/motivo de cancelación a `Renta`.
- Tasa de conversión, ticket promedio, duración promedio, clientes únicos (secciones 4, 6, 7): no implementados.

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
