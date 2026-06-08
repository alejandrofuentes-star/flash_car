@extends('layout.layouts')
@section('title', 'Sistema - Flash Car')
@section('content')
@include('layout.header')
<div class="bg-light">
    <div class="main_principal">
        <div class="space_principal">
        <div class="container">

            @if(session('success'))
                <div class="messenger_alert">
                    <div class="dialog_alert messenger py-2 px-4 rounded">
                        <div class="fs-6 text-white">{!! session('success') !!}</div>
                    </div>
                </div>
            @endif
            @if(session('error'))
                <div class="messenger_alert">
                    <div class="dialog_alert danger py-2 px-4 rounded">
                        <div class="fs-6 text-white"><b>{{ session('error') }}</b></div>
                    </div>
                </div>
            @endif

            <div class="col-12 d-flex align-items-center justify-content-start flex-wrap rounded cont_base my-2">

                {{-- ENCABEZADO --}}
                <div class="col-12 d-flex align-items-center justify-content-between flex-wrap p-2 bg_gris_8">
                    <h1 class="fs-6 text_uppcase m-0"><i class="bi bi-tools me-1"></i> Sistema</h1>
                </div>

                {{-- TABS --}}
                <div class="col-12 d-flex align-items-center bg_gris_8" style="gap:2px; padding:8px 8px 0;">
                    <button type="button" class="sistema-tab" data-tab="cache">
                        <i class="bi bi-trash3-fill me-1"></i>Caché
                    </button>
                    <button type="button" class="sistema-tab" data-tab="migraciones">
                        <i class="bi bi-database-gear me-1"></i>Migraciones
                        @if($pendingCount > 0)
                            <span class="badge bg-warning text-dark ms-1" style="font-size:0.65rem;">{{ $pendingCount }}</span>
                        @endif
                    </button>
                    <button type="button" class="sistema-tab" data-tab="mantenimiento">
                        <i class="bi bi-tools me-1"></i>Mantenimiento
                        @if($maintenanceActive)
                            <span class="badge bg-danger ms-1" style="font-size:0.65rem;">Activo</span>
                        @endif
                    </button>
                    <button type="button" class="sistema-tab" data-tab="assets">
                        <i class="bi bi-file-earmark-code me-1"></i>Assets
                    </button>
                </div>

                {{-- PANEL: Caché --}}
                <div id="tab-cache" class="tab-panel col-12">
                    <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start p-2">
                        <p class="text-dark fs-6 m-0"><b>Acciones disponibles</b></p>
                    </div>
                    <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-2 gap-2">

                        <div class="col-12 col-md-5 border rounded p-3 bg-white shadow-sm">
                            <p class="fs-6 mb-1"><b>🧹 Limpiar Todo</b></p>
                            <p class="text-muted mb-2" style="font-size:0.85rem;">Ejecuta <code>optimize:clear</code> — limpia caché, config, rutas y vistas.</p>
                            <form method="POST" action="{{ route('system.clearAll') }}">
                                @csrf
                                <button type="submit" class="boton_link_xxl rounded">Limpiar Todo</button>
                            </form>
                        </div>

                        <div class="col-12 col-md-5 border rounded p-3 bg-white shadow-sm">
                            <p class="fs-6 mb-1"><b>🗑️ Caché de Aplicación</b></p>
                            <p class="text-muted mb-2" style="font-size:0.85rem;">Ejecuta <code>cache:clear</code></p>
                            <form method="POST" action="{{ route('system.clearCache') }}">
                                @csrf
                                <button type="submit" class="boton_link_xxl rounded">Limpiar Caché</button>
                            </form>
                        </div>

                        <div class="col-12 col-md-5 border rounded p-3 bg-white shadow-sm">
                            <p class="fs-6 mb-1"><b>⚙️ Caché de Configuración</b></p>
                            <p class="text-muted mb-2" style="font-size:0.85rem;">Ejecuta <code>config:clear</code></p>
                            <form method="POST" action="{{ route('system.clearConfig') }}">
                                @csrf
                                <button type="submit" class="boton_link_xxl rounded">Limpiar Config</button>
                            </form>
                        </div>

                        <div class="col-12 col-md-5 border rounded p-3 bg-white shadow-sm">
                            <p class="fs-6 mb-1"><b>🛣️ Caché de Rutas</b></p>
                            <p class="text-muted mb-2" style="font-size:0.85rem;">Ejecuta <code>route:clear</code></p>
                            <form method="POST" action="{{ route('system.clearRoutes') }}">
                                @csrf
                                <button type="submit" class="boton_link_xxl rounded">Limpiar Rutas</button>
                            </form>
                        </div>

                        <div class="col-12 col-md-5 border rounded p-3 bg-white shadow-sm">
                            <p class="fs-6 mb-1"><b>👁️ Caché de Vistas</b></p>
                            <p class="text-muted mb-2" style="font-size:0.85rem;">Ejecuta <code>view:clear</code></p>
                            <form method="POST" action="{{ route('system.clearViews') }}">
                                @csrf
                                <button type="submit" class="boton_link_xxl rounded">Limpiar Vistas</button>
                            </form>
                        </div>

                    </div>
                </div>

                {{-- PANEL: Migraciones --}}
                <div id="tab-migraciones" class="tab-panel col-12">

                    <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start p-2">
                        <p class="text-dark fs-6 m-0"><b>Acciones</b></p>
                    </div>
                    <div class="col-12 d-flex align-items-start justify-content-start flex-wrap p-2 gap-2">

                        <div class="col-12 col-md-5 border rounded p-3 bg-white shadow-sm">
                            <p class="fs-6 mb-1"><b><i class="bi bi-play-circle-fill"></i> Ejecutar Migraciones Pendientes</b></p>
                            <p class="text-muted mb-2" style="font-size:0.85rem;">
                                Ejecuta <code>php artisan migrate --force</code>.
                                @if($pendingCount > 0)
                                    <span class="badge bg-warning text-dark ms-1">{{ $pendingCount }} pendiente(s)</span>
                                @else
                                    <span class="badge bg-success ms-1">Todo al día</span>
                                @endif
                            </p>
                            <form method="POST" action="{{ route('system.migrations.run') }}"
                                  onsubmit="return confirm('¿Ejecutar todas las migraciones pendientes? Esta acción modifica la base de datos.')">
                                @csrf
                                <button type="submit" class="boton_link_xxl rounded"
                                    {{ $pendingCount === 0 ? 'disabled' : '' }}
                                    style="width:auto;padding:0 14px;{{ $pendingCount === 0 ? 'opacity:0.5;cursor:not-allowed;' : '' }}">
                                    Ejecutar Pendientes
                                </button>
                            </form>
                        </div>

                        <div class="col-12 col-md-5 border rounded p-3 bg-white shadow-sm">
                            <p class="fs-6 mb-1"><b><i class="bi bi-upload"></i> Subir Migración</b></p>
                            <p class="text-muted mb-2" style="font-size:0.85rem;">
                                Sube un archivo <code>.php</code> con formato <code>YYYY_MM_DD_HHMMSS_nombre.php</code>.
                            </p>
                            <form method="POST" action="{{ route('system.migrations.upload') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="fila_form_f_b col-12 mb-2">
                                    <input class="input_form_f_b fs-6 p-1" type="file" name="migration_file" accept=".php" required>
                                </div>
                                <button type="submit" class="boton_link_xxl rounded" style="width:auto;padding:0 14px;">Subir Archivo</button>
                            </form>
                        </div>

                    </div>

                    <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start p-2">
                        <p class="text-dark fs-6 m-0"><b>Estado de Migraciones</b></p>
                    </div>
                    <div class="col-12 p-2">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered bg-white" style="font-size:0.85rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th class="px-3 py-2">Migración</th>
                                        <th class="px-3 py-2 text-center" style="width:130px;">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($migrations as $migration)
                                    <tr>
                                        <td class="px-3 py-2 font-monospace">{{ $migration['name'] }}</td>
                                        <td class="px-3 py-2 text-center">
                                            @if($migration['status'] === 'ran')
                                                <span class="badge bg-success">Ejecutada</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Pendiente</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>{{-- fin tab-migraciones --}}

                {{-- PANEL: Mantenimiento --}}
                <div id="tab-mantenimiento" class="tab-panel col-12">
                    <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start p-2">
                        <p class="text-dark fs-6 m-0"><b>Modo mantenimiento</b></p>
                    </div>
                    <div class="col-12 p-3">
                        <div class="col-12 col-md-6 border rounded p-3 bg-white shadow-sm">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div style="width:48px;height:48px;border-radius:50%;background:{{ $maintenanceActive ? '#ffc107' : '#e9ecef' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="bi bi-tools fs-5" style="color:{{ $maintenanceActive ? '#5c3d00' : '#6c757d' }};"></i>
                                </div>
                                <div>
                                    <p class="m-0 fw-bold fs-6" style="color:{{ $maintenanceActive ? '#5c3d00' : '#495057' }};">
                                        Estado actual:
                                        <span style="color:{{ $maintenanceActive ? '#a05c00' : '#198754' }};">
                                            {{ $maintenanceActive ? 'Activado' : 'Desactivado' }}
                                        </span>
                                    </p>
                                    <p class="m-0 text-muted" style="font-size:0.82rem;">
                                        {{ $maintenanceActive ? 'El sitio público muestra la página de mantenimiento. Solo los administradores pueden acceder.' : 'El sitio es accesible para todos los visitantes.' }}
                                    </p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('maintenance.toggle') }}">
                                @csrf
                                <button type="submit" class="boton_link_xxl b_sm rounded"
                                        style="width:auto;padding:0 18px;{{ $maintenanceActive ? 'background:#198754;color:#fff;border:none;' : 'background:#dc3545;color:#fff;border:none;' }}">
                                    <i class="bi bi-{{ $maintenanceActive ? 'check-circle' : 'exclamation-triangle' }} me-1"></i>
                                    {{ $maintenanceActive ? 'Desactivar mantenimiento' : 'Activar mantenimiento' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- PANEL: Assets --}}
                <div id="tab-assets" class="tab-panel col-12">
                    <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start p-2">
                        <p class="text-dark fs-6 m-0"><b>Versiones de CSS y JS</b></p>
                    </div>
                    <div class="col-12 p-3">
                        <p class="text-muted fs-6 mb-3">
                            Incrementa el número de versión de un archivo para forzar que todos los navegadores descarguen la versión más reciente en lugar de usar su caché.
                        </p>
                        <form method="POST" action="{{ route('settings.update') }}">
                            @csrf
                            @php
                                $assets = [
                                    ['key' => 'asset_v_general',    'label' => 'styles_general.css',          'desc'  => 'Estilos globales — afecta todas las páginas'],
                                    ['key' => 'asset_v_publico',    'label' => 'styles_pagina_principal.css', 'desc'  => 'Estilos del sitio público'],
                                    ['key' => 'asset_v_formulario', 'label' => 'formulario_renta.js',         'desc'  => 'JS del formulario de renta'],
                                ];
                            @endphp

                            <div class="col-12 d-flex align-items-start flex-wrap" style="gap:12px;">
                            @foreach($assets as $asset)
                            @php $current = \App\Models\SiteSetting::get($asset['key'], '1'); @endphp
                            <div class="col-12 col-md-5 border rounded p-3 bg-white shadow-sm">
                                <p class="fs-6 mb-0 fw-bold font-monospace">{{ $asset['label'] }}</p>
                                <p class="text-muted mb-2" style="font-size:0.82rem;">{{ $asset['desc'] }}</p>
                                <div class="d-flex align-items-center" style="gap:8px;">
                                    <label class="text-muted mb-0" style="font-size:0.82rem; white-space:nowrap;">v=</label>
                                    <input type="text"
                                           name="settings[{{ $asset['key'] }}]"
                                           id="input_{{ $asset['key'] }}"
                                           value="{{ $current }}"
                                           class="input_form_f_b fs-6 p-1"
                                           style="width:100px;">
                                    <button type="button"
                                            class="boton_link_sm b_sm rounded"
                                            title="Incrementar versión automáticamente"
                                            onclick="bumpVersion('{{ $asset['key'] }}')">
                                        +1
                                    </button>
                                </div>
                            </div>
                            @endforeach
                            </div>

                            <div class="col-12 d-flex align-items-center justify-content-end mt-3">
                                <button type="submit" class="boton_link_xxl b_sm rounded" style="width:auto;padding:0 18px;">
                                    <i class="bi bi-check-lg me-1"></i> Guardar versiones
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
        </div>
    </div>
</div>

<style>
.sistema-tab {
    background: transparent;
    border: none;
    border-radius: 6px 6px 0 0;
    padding: 6px 16px;
    font-size: 0.82rem;
    font-weight: 600;
    color: #222;
    cursor: pointer;
    opacity: 0.55;
    transition: opacity 0.15s, background 0.15s;
}
.sistema-tab:hover { opacity: 0.8; }
.sistema-tab.active {
    background: var(--amarillo);
    color: #222;
    opacity: 1;
}
.tab-panel { display: none; }
.tab-panel.active { display: block; }
</style>

<script>
(function () {
    const STORAGE_KEY = 'sistema_active_tab';
    const tabs        = document.querySelectorAll('.sistema-tab');
    const panels      = document.querySelectorAll('.tab-panel');

    function activate(tabName) {
        tabs.forEach(function (btn) {
            btn.classList.toggle('active', btn.dataset.tab === tabName);
        });
        panels.forEach(function (panel) {
            panel.classList.toggle('active', panel.id === 'tab-' + tabName);
        });
        localStorage.setItem(STORAGE_KEY, tabName);
    }

    tabs.forEach(function (btn) {
        btn.addEventListener('click', function () { activate(btn.dataset.tab); });
    });

    // Prioridad: query param ?tab=X → localStorage → 'cache'
    const urlTab = new URLSearchParams(window.location.search).get('tab');
    activate(urlTab || localStorage.getItem(STORAGE_KEY) || 'cache');
})();

function bumpVersion(key) {
    const input = document.getElementById('input_' + key);
    if (!input) return;
    const parts = input.value.split('.');
    parts[parts.length - 1] = parseInt(parts[parts.length - 1] || 0, 10) + 1;
    input.value = parts.join('.');
}
</script>
@endsection
