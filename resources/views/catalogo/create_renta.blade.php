@extends('layout.layouts')

@section('title', 'Rentar - ' . $vehicle->name)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/styles_pagina_principal.css') }}?v=1">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@23.3.2/build/css/intlTelInput.css">
<style>
    .iti { width: 100%; display: block; background-color: #f5f6f8; border: 1px solid #e0e3e8; border-radius: 5px; box-shadow: inset 0 1px 3px rgba(0,0,0,0.07); padding: 0.25rem; }
    .iti__tel-input { width: 100%; box-sizing: border-box; border: none; background: transparent; color: var(--azul_1); font-size: 1rem; box-shadow: none; padding: 0; }
    .iti__flag-container { top: 50%; transform: translateY(-50%); }
</style>
@endpush

@section('content')
@include('layout.header_user')
<div class="bg-light">
    <div class="main_principal">
        <div class="space_principal">
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        <div class="container">
            <div class="col-12 d-flex align-items-center justify-content-start flex-wrap rounded cont_base my-2">

                {{-- INDICADOR DE ETAPAS --}}
                <div class="col-12 d-flex align-items-center justify-content-center p-3">
                    <div class="col-12 d-flex align-items-center justify-content-center flex-wrap">
                        <div class="col-12 col-sm-12 col-md-4 col-lg-3 d-flex align-items-center justify-content-start py-1">
                            <div class="mx-2 etapa_circulo activa" id="circulo_1">1</div>
                            <span class="fs-6 fw-bold" id="label_1">{{ __('form.step1') }}</span>
                            <div class="mx-2 etapa_linea"></div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-4 col-lg-3 d-flex align-items-center justify-content-start py-1">
                            <div class="mx-2 etapa_circulo" id="circulo_2">2</div>
                            <span class="fs-6 text-muted" id="label_2">{{ __('form.step2') }}</span>
                            <div class="mx-2 etapa_linea"></div>
                        </div>
                        {{-- ETAPA 3 oculta temporalmente hasta integrar pasarela de pagos
                        <div class="col-12 col-sm-12 col-md-4 col-lg-3 d-flex align-items-center justify-content-start py-1">
                            <div class="mx-2 etapa_circulo" id="circulo_3">3</div>
                            <span class="fs-6 text-muted" id="label_3">Pago</span>
                        </div>
                        --}}
                    </div>
                </div>

                {{-- FORM ENVUELVE TODO --}}
                <form method="POST" action="{{ route('rentas.store') }}" id="formRenta" class="col-12" novalidate>
                @csrf
                <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
                <input type="hidden" name="total_dias" id="total_dias" value="0">
                <input type="hidden" name="metodo_pago" value="pendiente">
                <input type="hidden" name="costo_total" id="costo_total_input" value="0">
                <input type="hidden" id="precio_dia" value="{{ $vehicle->category->price_per_day ?? 0 }}">
                <input type="hidden" id="precio_semana"  value="{{ $vehicle->category->price_per_week ?? 0 }}">
                <input type="hidden" id="precio_mes"     value="{{ $vehicle->category->price_per_month ?? 0 }}">

                    {{-- DOS COLUMNAS --}}
                    <div class="col-12 d-flex align-items-start justify-content-start flex-wrap p-2">

                        {{-- COLUMNA IZQUIERDA: Etapas --}}
                        <div class="col-12 col-md-8 pe-md-3">

                            {{-- ETAPA 1: Datos Personales --}}
                            <div id="etapa_1">
                                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start p-2 rounded-top">
                                    <p class="text-dark fs-6 m-0"><b>{{ __('form.step1_header') }}</b></p>
                                </div>
                                <div class="col-12 d-flex align-items-start justify-content-start flex-wrap p-1 border rounded-bottom mb-3">
                                    <div class="col-12 col-md-6 fila_form_f_b py-2">
                                        <label class="label_form_f_b fs-6 p-1"><b>{{ __('form.full_name') }}</b></label>
                                        <input class="input_form_f_b fs-6 p-1" type="text" name="nombre_completo" value="{{ old('nombre_completo') }}" placeholder="{{ __('form.full_name_ph') }}" required>
                                    </div>
                                    <div class="col-12 col-md-6 fila_form_f_b py-2">
                                        <label class="label_form_f_b fs-6 p-1"><b>{{ __('form.phone') }}</b></label>
                                        <input type="hidden" name="telefono" id="telefono_completo">
                                        <input id="numero_tel" type="tel" required>
                                        <small id="tel_hint" class="text-muted px-1" style="font-size:0.75rem;">10 dígitos requeridos</small>
                                        <small id="tel_error" class="text-danger px-1" style="font-size:0.75rem; display:none;"></small>
                                    </div>
                                    <div class="col-12 col-md-6 fila_form_f_b py-2">
                                        <label class="label_form_f_b fs-6 p-1"><b>{{ __('form.email') }}</b></label>
                                        <input class="input_form_f_b fs-6 p-1" type="email" name="correo" value="{{ old('correo') }}" placeholder="{{ __('form.email_ph') }}" required>
                                    </div>
                                    <div class="col-12 col-md-6 fila_form_f_b py-2">
                                        <label class="label_form_f_b fs-6 p-1"><b>{{ __('form.passengers') }}</b></label>
                                        <input class="input_form_f_b fs-6 p-1" type="number" name="num_pasajeros" value="{{ old('num_pasajeros') }}" min="1" max="{{ $vehicle->passengers }}" placeholder="Máx: {{ $vehicle->passengers }}" required>
                                    </div>
                                    <div class="col-12 col-md-6 fila_form_f_b py-2">
                                        <label class="label_form_f_b fs-6 p-1"><b>{{ __('form.city') }}</b></label>
                                        <select class="input_form_f_b fs-6 p-1" name="ciudad" id="select_estado" required>
                                            <option value="">{{ __('form.city_placeholder') }}</option>
                                            @foreach($states as $state)
                                                <option value="{{ $state->name }}" {{ old('ciudad') == $state->name ? 'selected' : '' }}>
                                                    {{ $state->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6 fila_form_f_b py-2">
                                        <label class="label_form_f_b fs-6 p-1"><b>{{ __('form.category') }}</b></label>
                                        <span class="input_form_f_b fs-6 p-1">{{ $vehicle->category->name ?? '—' }}</span>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end p-2">
                                        <a class="boton_link_xxl b_sm rounded link_decoration_none display_flex_center_center" href="{{ route('catalogo.detalle', $vehicle->id) }}">{{ __('form.back') }}</a>
                                        <button type="button" class="boton_link_xxl rounded" onclick="irEtapa(2)">{{ __('form.next') }}</button>
                                    </div>
                                </div>
                            </div>

                            {{-- ETAPA 2: Fechas y Lugares --}}
                            <div id="etapa_2" style="display:none;">
                                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start p-2 rounded-top">
                                    <p class="text-dark fs-6 m-0"><b>{{ __('form.step2_header') }}</b></p>
                                </div>
                                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-1 border rounded-bottom mb-3">
                                    <div class="col-12 bg_gris_8 p-2 mb-2">
                                        <p class="fs-6 m-0"><b>{{ __('form.delivery') }}</b></p>
                                    </div>
                                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                                        <label class="label_form_f_b fs-6 p-1"><b>{{ __('form.delivery_date') }}</b></label>
                                        <input class="input_form_f_b fs-6 p-1" type="date" id="fecha_entrega" name="fecha_entrega" value="{{ old('fecha_entrega') }}" min="{{ date('Y-m-d') }}" required>
                                    </div>
                                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                                        <label class="label_form_f_b fs-6 p-1"><b>{{ __('form.delivery_time') }}</b></label>
                                        <input class="input_form_f_b fs-6 p-1" type="time" name="hora_entrega" value="{{ old('hora_entrega') }}" required>
                                    </div>
                                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                                        <label class="label_form_f_b fs-6 p-1"><b>{{ __('form.delivery_location') }}</b></label>
                                        <select class="input_form_f_b fs-6 p-1" name="lugar_entrega" id="select_entrega" required>
                                            <option value="">{{ __('form.location_placeholder') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-12 bg_gris_8 p-2 mb-2 mt-2">
                                        <p class="fs-6 m-0"><b>{{ __('form.return') }}</b></p>
                                    </div>
                                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                                        <label class="label_form_f_b fs-6 p-1"><b>{{ __('form.return_date') }}</b></label>
                                        <input class="input_form_f_b fs-6 p-1" type="date" id="fecha_devolucion" name="fecha_devolucion" value="{{ old('fecha_devolucion') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                                    </div>
                                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                                        <label class="label_form_f_b fs-6 p-1"><b>{{ __('form.return_time') }}</b></label>
                                        <input class="input_form_f_b fs-6 p-1" type="time" name="hora_devolucion" value="{{ old('hora_devolucion') }}" required>
                                    </div>
                                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                                        <label class="label_form_f_b fs-6 p-1"><b>{{ __('form.return_location') }}</b></label>
                                        <select class="input_form_f_b fs-6 p-1" name="lugar_devolucion" id="select_devolucion" required>
                                            <option value="">{{ __('form.location_placeholder') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-12 d-flex justify-content-between p-2">
                                        <button type="button" class="boton_link_xxl rounded" onclick="irEtapa(1)">{{ __('form.previous') }}</button>
                                        <button type="submit" class="boton_link_xxl rounded" style="width:auto; padding:0 14px;">{{ __('form.submit') }}</button>
                                    </div>
                                </div>
                            </div>

                            {{-- ETAPA 3 oculta temporalmente hasta integrar pasarela de pagos
                            <div id="etapa_3" style="display:none;">
                                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start p-2 rounded-top">
                                    <p class="text-dark fs-6 m-0"><b>③ Método de Pago</b></p>
                                </div>
                                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-1 border rounded-bottom mb-3">
                                    <div class="col-12 p-2">
                                        <label class="label_form_f_b fs-6 p-1"><b>Selecciona tu método de pago *</b></label>
                                        <div class="d-flex flex-wrap gap-3 mt-2">
                                            <label class="d-flex align-items-center gap-2 border rounded p-3 shadow-sm" style="cursor:pointer; min-width:140px;">
                                                <input type="radio" name="metodo_pago" value="efectivo" required>
                                                <span class="fs-6">💵 Efectivo</span>
                                            </label>
                                            <label class="d-flex align-items-center gap-2 border rounded p-3 shadow-sm" style="cursor:pointer; min-width:140px;">
                                                <input type="radio" name="metodo_pago" value="tarjeta">
                                                <span class="fs-6">💳 Tarjeta</span>
                                            </label>
                                            <label class="d-flex align-items-center gap-2 border rounded p-3 shadow-sm" style="cursor:pointer; min-width:140px;">
                                                <input type="radio" name="metodo_pago" value="transferencia">
                                                <span class="fs-6">🏦 Transferencia</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-between p-2 mt-2">
                                        <button type="button" class="boton_link_xxl rounded" onclick="irEtapa(2)">← Anterior</button>
                                        <button type="submit" class="boton_link_xxl rounded">Enviar Solicitud</button>
                                    </div>
                                </div>
                            </div>
                            --}}

                            {{-- RECORDATORIO DOCUMENTACIÓN --}}
                            <div class="col-12 d-flex align-items-start justify-content-start flex-wrap rounded border border-warning bg-warning bg-opacity-10 p-3">
                                <div class="col-12 mb-2">
                                    <p class="fs-6 m-0 fw-bold"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Recuerda presentar tu documentación original y vigente</p>
                                </div>
                                <div class="col-12 d-flex align-items-start justify-content-start flex-wrap">
                                    <div class="col-12 col-sm-6 d-flex align-items-start py-1 px-2">
                                        <i class="bi bi-circle-fill me-2 mt-1" style="font-size:0.45rem; color:var(--amarillo_fuerte);"></i>
                                        <span class="fs-6">INE o pasaporte</span>
                                    </div>
                                    <div class="col-12 col-sm-6 d-flex align-items-start py-1 px-2">
                                        <i class="bi bi-circle-fill me-2 mt-1" style="font-size:0.45rem; color:var(--amarillo_fuerte);"></i>
                                        <span class="fs-6">Licencia de conducir nacional o extranjera</span>
                                    </div>
                                    <div class="col-12 col-sm-6 d-flex align-items-start py-1 px-2">
                                        <i class="bi bi-circle-fill me-2 mt-1" style="font-size:0.45rem; color:var(--amarillo_fuerte);"></i>
                                        <span class="fs-6">Número de vuelo</span>
                                    </div>
                                    <div class="col-12 col-sm-6 d-flex align-items-start py-1 px-2">
                                        <i class="bi bi-circle-fill me-2 mt-1" style="font-size:0.45rem; color:var(--amarillo_fuerte);"></i>
                                        <span class="fs-6">Forma de pago</span>
                                    </div>
                                </div>
                            </div>

                        </div>{{-- fin col-md-8 --}}

                        {{-- COLUMNA DERECHA: Calculadora --}}
                        <div class="col-12 col-md-4">
                            <div class="rounded border shadow-sm p-3" style="top:80px;">

                                @if($vehicle->image_path)
                                    <img src="{{ Storage::url($vehicle->image_path) }}" alt="{{ $vehicle->name }}" width="100%" class="rounded mb-2">
                                @else
                                    <img src="{{ asset('./img/sin_url_auto.png') }}" alt="Sin imagen" width="100%" class="rounded mb-2">
                                @endif

                                <h5 class="fs-5 mb-0"><b>{{ $vehicle->name }}</b></h5>
                                <p class="text-muted fs-6 mb-2">{{ $vehicle->brand }} {{ $vehicle->model }} {{ $vehicle->year }} ó similar</p>

                                @if($vehicle->category)
                                    <span class="badge bg_amarillo text-dark mb-3">{{ $vehicle->category->name }}</span>
                                @endif

                                <div class="border-top pt-3 mt-2">
                                    <p class="fs-6 m-0 mb-2"><b>{{ __('form.cost_summary') }}</b></p>
                                    <div class="d-flex justify-content-between py-1 border-bottom">
                                        <span class="text-muted fs-6">{{ __('form.price_per_day') }}</span>
                                        <b class="fs-6">${{ number_format($vehicle->category->price_per_day ?? 0, 2) }}</b>
                                    </div>
                                    <div class="d-flex justify-content-between py-1 border-bottom">
                                        <span class="text-muted fs-6">{{ __('form.total_days') }}</span>
                                        <b class="fs-6" id="resumen_dias">—</b>
                                    </div>
                                    <div class="d_flex_center_between py-1 border-bottom" id="row_cargo_extra" style="display:none">
                                        <span class="text-muted fs-6">{{ __('form.extra_charge') }}</span>
                                        <b class="fs-6 text-danger" id="texto_cargo_extra"></b>
                                    </div>
                                    <div class="d-flex justify-content-between py-2 mt-1">
                                        <span class="fs-6"><b>{{ __('form.total_cost') }}</b></span>
                                        <b class="fs-4" id="resumen_costo" style="color:var(--primary);">$0.00</b>
                                    </div>
                                </div>

                                @if($vehicle->category)
                                <div class="border-top pt-2 mt-1">
                                    <div class="d-flex justify-content-between py-1">
                                        <span class="text-muted fs-6">{{ __('detail.warranty') }}</span>
                                        <b class="fs-6">{{ $vehicle->category->formatted_warranty }}</b>
                                    </div>
                                </div>
                                @endif

                                <div id="btn_confirmar" class="mt-3" style="display:none;">
                                    <button type="submit" class="boton_link_xxl rounded w-100" style="width:auto; padding:0 14px;">{{ __('form.submit') }}</button>
                                </div>

                            </div>
                        </div>{{-- fin col-md-4 --}}

                    </div>{{-- fin row dos columnas --}}

                </form>{{-- fin form --}}

                @if($errors->any())
                    <div class="messenger_alert">
                        <div class="dialog_alert danger py-2 px-4 rounded">
                            @foreach($errors->all() as $error)
                                <div class="fs-6 text-white"><b>{{ $error }}</b></div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </div>
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        </div>
    </div>
</div>
@include('layout.burbujas')
@include('layout.footer')
<script src="{{ asset('js/formulario_renta.js') }}?v=1.1"></script>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.3.2/build/js/intlTelInput.min.js"></script>
<script>
    const inputNum = document.getElementById('numero_tel');
    const telHint  = document.getElementById('tel_hint');
    const telError = document.getElementById('tel_error');

    const digitosPorPais = {
        mx:{min:10,max:10}, us:{min:10,max:10}, ca:{min:10,max:10},
        ar:{min:10,max:10}, br:{min:10,max:11}, cl:{min:9,max:9},
        co:{min:10,max:10}, pe:{min:9,max:9},   ve:{min:10,max:10},
        ec:{min:9,max:9},   gt:{min:8,max:8},   sv:{min:8,max:8},
        hn:{min:8,max:8},   ni:{min:8,max:8},   cr:{min:8,max:8},
        pa:{min:8,max:8},   cu:{min:8,max:8},   es:{min:9,max:9},
        gb:{min:10,max:10}, fr:{min:9,max:9},   de:{min:10,max:11},
        it:{min:9,max:10},
    };

    const iti = window.intlTelInput(inputNum, {
        initialCountry: 'mx',
        separateDialCode: true,
        preferredCountries: ['mx', 'us', 'ca', 'ar', 'br', 'cl', 'co', 'pe', 'es'],
        loadUtils: () => import('https://cdn.jsdelivr.net/npm/intl-tel-input@23.3.2/build/js/utils.js'),
    });

    function getReglas() {
        const iso2 = iti.getSelectedCountryData().iso2;
        return digitosPorPais[iso2] || { min: 6, max: 15 };
    }

    function actualizarHint() {
        const r = getReglas();
        inputNum.value         = '';
        telError.style.display = 'none';
        telHint.textContent    = r.min === r.max
            ? `${r.min} dígitos requeridos`
            : `${r.min}–${r.max} dígitos requeridos`;
    }

    function validarTelefono() {
        const r      = getReglas();
        const digits = inputNum.value.replace(/\D/g, '');
        if (digits.length < r.min || digits.length > r.max) {
            telError.textContent   = r.min === r.max
                ? `El número debe tener exactamente ${r.min} dígitos`
                : `El número debe tener entre ${r.min} y ${r.max} dígitos`;
            telError.style.display = 'block';
            return false;
        }
        telError.style.display = 'none';
        return true;
    }

    inputNum.addEventListener('input', function () {
        const r = getReglas();
        this.value = this.value.replace(/\D/g, '').slice(0, r.max);
        telHint.textContent = r.min === r.max
            ? `${this.value.length}/${r.min} dígitos`
            : `${this.value.length}/${r.max} dígitos`;
        if (this.value.length > 0) validarTelefono();
        else telError.style.display = 'none';
    });

    inputNum.addEventListener('countrychange', actualizarHint);
    iti.promise.then(actualizarHint);

    document.getElementById('formRenta').addEventListener('submit', function (e) {
        if (!validarTelefono()) { e.preventDefault(); inputNum.focus(); return; }
        const codigo = '+' + iti.getSelectedCountryData().dialCode;
        document.getElementById('telefono_completo').value = codigo + inputNum.value;
    });
</script>
<script>
    const statesData = @json($states->map(fn($s) => [
        'name'   => $s->name,
        'points' => $s->deliveryPoints->where('active', true)->map(fn($p) => $p->name)->values()
    ]));

    document.getElementById('select_estado').addEventListener('change', function() {
        const selected = this.value;
        const state    = statesData.find(s => s.name === selected);
        const points   = state ? state.points : [];

        ['select_entrega', 'select_devolucion'].forEach(id => {
            const sel = document.getElementById(id);
            sel.innerHTML = '<option value="">-- Selecciona un punto --</option>';
            points.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p;
                opt.textContent = p;
                sel.appendChild(opt);
            });
        });
    });
</script>
<div id="fecha_messenger" class="messenger_alert" style="display:none">
    <div class="dialog_alert messenger py-2 px-4 rounded" style="background:#dc3545;">
        <div class="fs-6 text-white"><b id="fecha_messenger_texto"></b></div>
    </div>
</div>

<script>
    const fechaEntrega    = document.getElementById('fecha_entrega');
    const fechaDevolucion = document.getElementById('fecha_devolucion');

    function mostrarErrorFecha(msg) {
        $('#fecha_messenger_texto').text(msg);
        $('#fecha_messenger').stop(true).fadeIn(200);
        setTimeout(function () { $('#fecha_messenger').fadeOut(300); }, 4000);
    }

    function getHoy() {
        const d = new Date();
        return d.getFullYear() + '-' +
            String(d.getMonth() + 1).padStart(2, '0') + '-' +
            String(d.getDate()).padStart(2, '0');
    }

    fechaEntrega.min    = getHoy();
    fechaDevolucion.min = getHoy();

    fechaEntrega.addEventListener('change', function () {
        const hoy = getHoy();
        if (this.value && this.value < hoy) {
            this.value = '';
            mostrarErrorFecha('La fecha de entrega no puede ser anterior a hoy.');
            return;
        }
        if (!this.value) return;
        const siguiente = new Date(this.value + 'T00:00:00');
        siguiente.setDate(siguiente.getDate() + 1);
        const minDev = siguiente.getFullYear() + '-' +
            String(siguiente.getMonth() + 1).padStart(2, '0') + '-' +
            String(siguiente.getDate()).padStart(2, '0');
        fechaDevolucion.min = minDev;
        if (fechaDevolucion.value && fechaDevolucion.value <= this.value) {
            fechaDevolucion.value = '';
            mostrarErrorFecha('La fecha de devolución fue reiniciada, selecciona una fecha posterior a la entrega.');
        }
    });

    fechaDevolucion.addEventListener('change', function () {
        if (!this.value) return;
        if (fechaEntrega.value && this.value <= fechaEntrega.value) {
            this.value = '';
            mostrarErrorFecha('La fecha de devolución debe ser posterior a la fecha de entrega.');
        }
    });
</script>
@endsection
