@extends('layout.layouts')
@section('title', 'Configuración - Flash Car')
@section('content')
@include('layout.header')
<div class="bg-light">
    <div class="main_principal">
        <div class="space_principal">

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
            <div class="cont_base rounded">

                <div class="col-12 d-flex align-items-center justify-content-start p-2 bg_gris_8">
                    <h2 class="fs-6 fw-bold m-0"><i class="bi bi-telephone-fill me-2"></i>Contacto</h2>
                </div>

                <form method="POST" action="{{ route('settings.update') }}" class="col-12 p-3">
                    @csrf

                    <div class="col-12 d-flex align-items-center justify-content-start flex-wrap" style="gap:12px;">
                    @foreach($settings->where('key', '!=', 'admin_notification_email') as $setting)
                    <div class="d-flex align-items-center" style="gap:8px;">
                        <label style="font-size:0.82rem;font-weight:600;white-space:nowrap;margin:0;">{{ $setting->label }}</label>
                        <input type="text"
                               name="settings[{{ $setting->key }}]"
                               value="{{ old('settings.' . $setting->key, $setting->value) }}"
                               class="rounded"
                               style="height:30px;font-size:0.82rem;padding:0 8px;border:1px solid #ced4da;width:160px;">
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
        </div>

        <div class="col-12 px-1 mt-3">
            <div class="cont_base rounded">

                <div class="col-12 d-flex align-items-center justify-content-start p-2 bg_gris_8">
                    <h2 class="fs-6 fw-bold m-0"><i class="bi bi-bell-fill me-2"></i>Notificaciones</h2>
                </div>

                <form method="POST" action="{{ route('settings.update') }}" class="col-12 p-3">
                    @csrf
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
        </div>

        </div>
    </div>
</div>
@endsection
