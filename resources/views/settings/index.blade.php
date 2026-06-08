@extends('layout.layouts')
@section('title', 'Configuración - Flash Car')
@section('content')
@include('layout.header')
<div class="bg-light">
    <div class="main_principal">
        <div class="space_principal">

        <div class="container">
        {{-- ENCABEZADO --}}
        <div class="col-12 d-flex align-items-center justify-content-between flex-wrap px-1 py-3">
            <p class="text-muted fs-6 m-0"><i class="bi bi-sliders me-1"></i> Configuración del sitio</p>
        </div>

        @if(session('success'))
        <div class="messenger_alert">
            <div class="dialog_alert messenger py-2 px-3 rounded">
                <div class="fs-6 text-white"><b>{{ session('success') }}</b><i id="close_messenger" class="m_izq bi bi-x-lg"></i></div>
            </div>
        </div>
        @endif

        <div class="col-12 px-1">
            <div class="cont_base rounded overflow-hidden">

                {{-- TABS --}}
                <div class="col-12 d-flex align-items-center bg_gris_8" style="gap:2px; padding:8px 8px 0;">
                    <button type="button" class="settings-tab" data-tab="contacto">
                        <i class="bi bi-telephone-fill me-1"></i>Contacto
                    </button>
                    <button type="button" class="settings-tab" data-tab="notificaciones">
                        <i class="bi bi-bell-fill me-1"></i>Notificaciones
                    </button>
                    <button type="button" class="settings-tab" data-tab="pagos">
                        <i class="bi bi-credit-card-fill me-1"></i>Métodos de pago
                    </button>
                </div>

                {{-- PANEL: Contacto --}}
                <div id="tab-contacto" class="tab-panel">
                    <form method="POST" action="{{ route('settings.update') }}" class="col-12 p-3">
                        @csrf
                        <input type="hidden" name="active_tab" value="contacto">

                        <div class="col-12 d-flex align-items-center justify-content-start flex-wrap">
                        @foreach($settings->whereNotIn('key', [
                            'admin_notification_email',
                            'pago_tarjeta',
                            'anticipo_tipo',
                            'anticipo_monto',
                            'asset_v_general',
                            'asset_v_publico',
                            'asset_v_formulario',
                            'asset_version',
                        ]) as $setting)
                        <div class="col-12 col-md-6 col-lg-4 fila_form_f_b py-2 px-1">
                            <label class="label_form_f_b fs-6 p-1"><b>{{ $setting->label }}</b></label>
                            <input type="text"
                                   name="settings[{{ $setting->key }}]"
                                   value="{{ old('settings.' . $setting->key, $setting->value) }}"
                                   class="input_form_f_b fs-6 p-1"
                                   placeholder="{{ $setting->label }}">
                        </div>
                        @endforeach
                        </div>

                        <div class="col-12 d-flex align-items-center justify-content-end mt-3">
                            <button type="submit" class="boton_link_xxl b_sm rounded" style="width:auto;padding:0 18px;">
                                <i class="bi bi-check-lg me-1"></i> Guardar cambios
                            </button>
                        </div>
                    </form>
                </div>

                {{-- PANEL: Notificaciones --}}
                <div id="tab-notificaciones" class="tab-panel">
                    <form method="POST" action="{{ route('settings.update') }}" class="col-12 p-3">
                        @csrf
                        <input type="hidden" name="active_tab" value="notificaciones">
                        @php $emailSetting = $settings->firstWhere('key', 'admin_notification_email'); @endphp

                        <div class="col-12 col-md-6 fila_form_f_b py-2">
                            <label class="label_form_f_b fs-6 p-1"><b>Correo para recibir notificaciones de nuevas rentas</b></label>
                            <input type="email"
                                   name="settings[admin_notification_email]"
                                   value="{{ old('settings.admin_notification_email', $emailSetting?->value) }}"
                                   class="input_form_f_b fs-6 p-1"
                                   placeholder="correo@ejemplo.com"
                                   required>
                        </div>

                        <div class="col-12 d-flex align-items-center justify-content-end mt-3">
                            <button type="submit" class="boton_link_xxl b_sm rounded" style="width:auto;padding:0 18px;">
                                <i class="bi bi-check-lg me-1"></i> Guardar cambios
                            </button>
                        </div>
                    </form>
                </div>

                {{-- PANEL: Métodos de pago --}}
                <div id="tab-pagos" class="tab-panel">
                    <form method="POST" action="{{ route('settings.update') }}" class="col-12 p-3">
                        @csrf
                        <input type="hidden" name="active_tab" value="pagos">
                        @php
                            $pagoTarjeta = $settings->firstWhere('key', 'pago_tarjeta');
                            $antiTipo    = $settings->firstWhere('key', 'anticipo_tipo');
                            $antiMonto   = $settings->firstWhere('key', 'anticipo_monto');
                        @endphp

                        {{-- Toggle --}}
                        <div class="col-12 py-2">
                            <input type="hidden" name="settings[pago_tarjeta]" value="0">
                            <div class="d-flex align-items-center" style="gap:10px;">
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox"
                                           name="settings[pago_tarjeta]" value="1"
                                           id="toggle_pago_tarjeta"
                                           {{ ($pagoTarjeta?->value ?? '1') === '1' ? 'checked' : '' }}>
                                </div>
                                <label class="fs-6 mb-0" for="toggle_pago_tarjeta" style="cursor:pointer;">
                                    💳 Habilitar pago con tarjeta
                                </label>
                            </div>
                        </div>

                        {{-- Anticipo --}}
                        <div class="col-12 d-flex align-items-start justify-content-start flex-wrap mt-1">
                            <div class="col-12 col-md-4 fila_form_f_b py-2 pe-md-2">
                                <label class="label_form_f_b fs-6 p-1"><b>Tipo de anticipo</b></label>
                                <select name="settings[anticipo_tipo]" class="input_form_f_b fs-6 p-1">
                                    <option value="fijo"       {{ ($antiTipo?->value ?? 'fijo') === 'fijo'       ? 'selected' : '' }}>Monto fijo ($)</option>
                                    <option value="porcentaje" {{ ($antiTipo?->value ?? '') === 'porcentaje' ? 'selected' : '' }}>Porcentaje (%)</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-4 fila_form_f_b py-2">
                                <label class="label_form_f_b fs-6 p-1"><b>Valor del anticipo</b></label>
                                <input type="number" min="0" step="0.01"
                                       name="settings[anticipo_monto]"
                                       value="{{ old('settings.anticipo_monto', $antiMonto?->value ?? '0') }}"
                                       class="input_form_f_b fs-6 p-1"
                                       placeholder="0">
                                <small class="text-muted px-1" style="font-size:0.75rem;">0 = se cobra el total completo</small>
                            </div>
                        </div>

                        <div class="col-12 d-flex align-items-center justify-content-end mt-3">
                            <button type="submit" class="boton_link_xxl b_sm rounded" style="width:auto;padding:0 18px;">
                                <i class="bi bi-check-lg me-1"></i> Guardar cambios
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>

        </div>{{-- fin container --}}
        </div>
    </div>
</div>

<style>
.settings-tab {
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
.settings-tab:hover { opacity: 0.8; }
.settings-tab.active {
    background: var(--amarillo);
    color: #222;
    opacity: 1;
}
.tab-panel { display: none; }
.tab-panel.active { display: block; }
</style>

<script>
(function () {
    const STORAGE_KEY = 'settings_active_tab';
    const tabs        = document.querySelectorAll('.settings-tab');
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

    // Restaurar tab activa: prioridad → campo hidden del form → localStorage → primera
    const fromForm = document.querySelector('input[name="active_tab"]');
    const saved    = fromForm?.value || localStorage.getItem(STORAGE_KEY) || 'contacto';
    activate(saved);
})();
</script>
@endsection
