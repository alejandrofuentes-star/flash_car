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
| `admin` | Dashboard, vehículos, rentas, slider, usuarios, estados |
| *(autenticado)* | Dashboard, ver rentas/vehículos (sin eliminar) |

Middleware de roles: `role:admin,super_admin` — definido en `app/Http/Middleware/`.

## Modelos principales

### `Vehicle`
- Campos: `name`, `brand`, `model`, `year`, `transmission` (enum: manual/automatic), `passengers`, `fuel_capacity`, `available` (bool), `image_path`, `category_id`, `active`, `mileage`, `next_verification`, `plate_number`, `slug`
- Relaciones: `belongsTo(Category)`, `hasMany(VehicleImage)`, `hasMany(Renta)`, `belongsToMany(State)` via pivot `state_vehicle`
- Accessor: `formatted_fuel_capacity`
- Las ciudades donde opera un vehículo se gestionan con la pivot `state_vehicle` (no con el campo `city` que queda en desuso)

### `Category`
- Campos: `name`, `price_per_day`, `price_per_week`, `price_per_month`, `warranty`, `active`
- Accessors: `formatted_price_per_day`, `formatted_price_per_week`, `formatted_price_per_month`, `formatted_warranty`

### `Renta`
- Campos: `vehicle_id`, `nombre_completo`, `telefono`, `correo`, `ciudad`, `fecha_entrega`, `hora_entrega`, `lugar_entrega`, `fecha_devolucion`, `hora_devolucion`, `lugar_devolucion`, `num_pasajeros`, `total_dias`, `costo_total`, `estado`, `mail_enviado`, `mail_enviado_at`
- `estado` enum: `pendiente` | `confirmada` | `cancelada` | `completada`
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
| `VehicleController` | `/vehiculos`, `/catalogo` | `catalogo()` y `buscar()` pasan `$sliderDesktop` y `$sliderMobile`; ciudades via `State::whereHas('vehicles')`; búsqueda via `whereHas('states')` |
| `RentaController` | `/rentas`, `/rentar` | `store()` envía correo y guarda `mail_enviado`; `reenviarCorreo()` para reenvío manual |
| `SliderController` | `/slider` | upload, toggle active, reorder, destroy |
| `CategoryController` | `/categorias` | CRUD categorías |
| `StateController` | `/states` | CRUD estados + puntos de entrega |
| `SystemController` | `/system/cache`, `/system/migrations` | Limpieza de caché y gestión de migraciones (solo `super_admin`) |
| `AuthController` | `/login`, `/logout` | — |
| `UserController` | `/users` | CRUD usuarios |

## Correo
- **Mailable**: `app/Mail/RentaSolicitada.php`
- **Template**: `resources/views/emails/renta_solicitada.blade.php`
- Se envía al cliente al hacer submit del formulario de renta
- Si falla, se loguea en `storage/logs/laravel.log` y `mail_enviado` queda en `false`
- Desde la lista de rentas, el ícono rojo (`bi-envelope-x-fill`) es clickeable para reenviar

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
│   ├── show.blade.php
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
