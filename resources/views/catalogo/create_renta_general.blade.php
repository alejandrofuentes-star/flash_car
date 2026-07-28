@extends('layout.layouts')

@section('title', __('nav.book') . ' — Flash Car')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/styles_pagina_principal.css') }}?v={{ \App\Models\SiteSetting::get('asset_v_publico', '1.7') }}">
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
                        @if($pagoTarjetaActivo)
                        <div class="col-12 col-sm-12 col-md-4 col-lg-3 d-flex align-items-center justify-content-start py-1">
                            <div class="mx-2 etapa_circulo" id="circulo_3">3</div>
                            <span class="fs-6 text-muted" id="label_3">{{ __('form.step3') }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- FORM --}}
                <form method="POST" action="{{ route('rentas.store') }}" id="formRenta" class="col-12" novalidate>
                @csrf
                <input type="hidden" name="category_id" id="category_id_hidden" value="">
                <input type="hidden" name="total_dias"  id="total_dias"          value="0">
                <input type="hidden" name="costo_total" id="costo_total_input"   value="0">
                <input type="hidden" id="precio_dia"    value="0">
                <input type="hidden" id="precio_semana" value="0">
                <input type="hidden" id="precio_mes"    value="0">

                    <div class="row g-0 p-2 align-items-start w-100">

                        {{-- COLUMNA IZQUIERDA --}}
                        <div class="col-12 col-md-8 pe-md-3">

                            {{-- ETAPA 1 --}}
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
                                        <small id="tel_hint" class="text-muted px-1" style="font-size:0.75rem;"></small>
                                        <small id="tel_error" class="text-danger px-1" style="font-size:0.75rem; display:none;"></small>
                                    </div>
                                    <div class="col-12 col-md-6 fila_form_f_b py-2">
                                        <label class="label_form_f_b fs-6 p-1"><b>{{ __('form.email') }}</b></label>
                                        <input class="input_form_f_b fs-6 p-1" type="email" name="correo" value="{{ old('correo') }}" placeholder="{{ __('form.email_ph') }}" required>
                                    </div>
                                    <div class="col-12 col-md-6 fila_form_f_b py-2">
                                        <label class="label_form_f_b fs-6 p-1"><b>{{ __('form.passengers') }}</b></label>
                                        <input class="input_form_f_b fs-6 p-1" type="number" name="num_pasajeros" id="num_pasajeros" value="{{ old('num_pasajeros') }}" min="1" max="9" placeholder="Máx: —" required>
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

                                    {{-- SELECTOR DE CATEGORÍA --}}
                                    <div class="col-12 col-md-6 fila_form_f_b py-2">
                                        <label class="label_form_f_b fs-6 p-1"><b>{{ __('form.category_req') }}</b></label>
                                        <select class="input_form_f_b fs-6 p-1" id="select_categoria">
                                            <option value="">{{ __('form.category_select_ph') }}</option>
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat->id }}">
                                                    {{ $cat->name }} — {{ $cat->formatted_price_per_day }}/{{ __('catalog.per_day') }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end p-2">
                                        <a class="boton_link_xxl b_sm rounded link_decoration_none display_flex_center_center" href="{{ route('catalogo.index') }}#catalogo">{{ __('form.back') }}</a>
                                        <button type="button" class="boton_link_xxl rounded" onclick="irEtapa(2)">{{ __('form.next') }}</button>
                                    </div>
                                </div>
                            </div>

                            {{-- ETAPA 2 --}}
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
                                        @if($pagoTarjetaActivo)
                                        <button type="button" class="boton_link_xxl rounded" style="width:auto; padding:0 14px;" onclick="irEtapa(3)">{{ __('form.next') }}</button>
                                        @else
                                        <button type="submit" class="boton_link_xxl rounded" style="width:auto; padding:0 14px;">{{ __('form.submit') }}</button>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if($pagoTarjetaActivo)
                            <div id="etapa_3" style="display:none;">
                                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start p-2 rounded-top">
                                    <p class="text-dark fs-6 m-0"><b>{{ __('form.step3_header') }}</b></p>
                                </div>
                                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-1 border rounded-bottom mb-3">
                                    <div class="col-12 p-2">
                                        <input type="hidden" name="metodo_pago" value="tarjeta">
                                        <input type="hidden" name="payment_intent_id" id="payment_intent_id">
                                        <div class="d-flex align-items-center gap-2 mb-3">
                                            <span class="badge bg_amarillo text-dark fs-6 px-3 py-2">{{ __('form.card_payment_badge') }}</span>
                                        </div>
                                        <div class="col-12 fila_form_f_b py-1">
                                            <label class="label_form_f_b fs-6 p-1"><b>{{ __('form.card_number') }}</b></label>
                                            <div id="card-number-element" class="input_form_f_b p-2" style="min-height:42px;"></div>
                                            <div id="card-number-errors" class="text-danger px-1" style="font-size:0.82rem; display:none;"></div>
                                        </div>
                                        <div class="col-12 d-flex flex-wrap">
                                            <div class="col-12 col-md-6 fila_form_f_b py-1 pe-md-2">
                                                <label class="label_form_f_b fs-6 p-1"><b>{{ __('form.card_expiry') }}</b></label>
                                                <div id="card-expiry-element" class="input_form_f_b p-2" style="min-height:42px;"></div>
                                                <div id="card-expiry-errors" class="text-danger px-1" style="font-size:0.82rem; display:none;"></div>
                                            </div>
                                            <div class="col-12 col-md-6 fila_form_f_b py-1">
                                                <label class="label_form_f_b fs-6 p-1"><b>{{ __('form.card_cvc') }}</b></label>
                                                <div id="card-cvc-element" class="input_form_f_b p-2" style="min-height:42px;"></div>
                                                <div id="card-cvc-errors" class="text-danger px-1" style="font-size:0.82rem; display:none;"></div>
                                            </div>
                                        </div>
                                        <div id="card-errors" class="text-danger px-1 mt-1" style="font-size:0.82rem; display:none;"></div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-between p-2 mt-2">
                                        <button type="button" class="boton_link_xxl rounded" onclick="irEtapa(2)" id="btn_anterior_pago">{{ __('form.previous') }}</button>
                                        <button type="submit" class="boton_link_xxl rounded" id="btn_pagar" style="width:auto; padding:0 14px;">{{ __('form.pay_submit') }}</button>
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- RECORDATORIO DOCUMENTACIÓN --}}
                            <div class="col-12 d-flex align-items-start justify-content-start flex-wrap rounded border border-warning bg-warning bg-opacity-10 p-3">
                                <div class="col-12 mb-2">
                                    <p class="fs-6 m-0 fw-bold"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>{{ __('form.docs_title') }}</p>
                                </div>
                                <div class="col-12 d-flex align-items-start justify-content-start flex-wrap">
                                    <div class="col-12 col-sm-6 d-flex align-items-start py-1 px-2">
                                        <i class="bi bi-circle-fill me-2 mt-1" style="font-size:0.45rem; color:var(--amarillo_fuerte);"></i>
                                        <span class="fs-6">{{ __('form.docs_ine') }}</span>
                                    </div>
                                    <div class="col-12 col-sm-6 d-flex align-items-start py-1 px-2">
                                        <i class="bi bi-circle-fill me-2 mt-1" style="font-size:0.45rem; color:var(--amarillo_fuerte);"></i>
                                        <span class="fs-6">{{ __('form.docs_license') }}</span>
                                    </div>
                                    <div class="col-12 col-sm-6 d-flex align-items-start py-1 px-2">
                                        <i class="bi bi-circle-fill me-2 mt-1" style="font-size:0.45rem; color:var(--amarillo_fuerte);"></i>
                                        <span class="fs-6">{{ __('form.docs_flight') }}</span>
                                    </div>
                                    <div class="col-12 col-sm-6 d-flex align-items-start py-1 px-2">
                                        <i class="bi bi-circle-fill me-2 mt-1" style="font-size:0.45rem; color:var(--amarillo_fuerte);"></i>
                                        <span class="fs-6">{{ __('form.docs_payment') }}</span>
                                    </div>
                                </div>
                            </div>

                        </div>{{-- fin col-md-8 --}}

                        {{-- COLUMNA DERECHA: Sidebar --}}
                        <div class="col-12 col-md-4">
                            <div class="rounded border shadow-sm p-3" style="top:80px;">

                                {{-- Placeholder: sin categoría seleccionada --}}
                                <div id="sidebar_placeholder" class="text-center py-4">
                                    <i class="bi bi-grid-1x2" style="font-size:3rem; color:#ccc;"></i>
                                    <p class="text-muted mt-2 fs-6">{{ __('form.sidebar_cat_placeholder') }}</p>
                                </div>

                                {{-- Info de la categoría (se muestra al seleccionar) --}}
                                <div id="sidebar_categoria_info" style="display:none;">
                                    <img id="sidebar_cat_img" src="" alt="" width="100%" class="rounded mb-2" style="object-fit:contain; background:#fff; padding:8px; display:none;">
                                    <span class="badge bg_amarillo text-dark fs-6 px-3 py-2 mb-3 d-inline-block" id="sidebar_cat_nombre"></span>

                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between py-1 border-bottom">
                                            <span class="text-muted fs-6">{{ __('form.price_per_day') }}</span>
                                            <b class="fs-6" id="sidebar_precio_dia">—</b>
                                        </div>
                                        <div class="d-flex justify-content-between py-1 border-bottom">
                                            <span class="text-muted fs-6">{{ __('form.price_per_week') }}</span>
                                            <b class="fs-6" id="sidebar_precio_semana">—</b>
                                        </div>
                                        <div class="d-flex justify-content-between py-1 border-bottom">
                                            <span class="text-muted fs-6">{{ __('form.price_per_month') }}</span>
                                            <b class="fs-6" id="sidebar_precio_mes">—</b>
                                        </div>
                                        <div class="d-flex justify-content-between py-1">
                                            <span class="text-muted fs-6">{{ __('detail.warranty') }}</span>
                                            <b class="fs-6" id="sidebar_garantia_val">—</b>
                                        </div>
                                    </div>

                                    <div class="border-top pt-3 mt-2">
                                        <p class="fs-6 m-0 mb-2"><b>{{ __('form.cost_summary') }}</b></p>
                                        <div class="d-flex justify-content-between py-1 border-bottom">
                                            <span class="text-muted fs-6">{{ __('form.price_per_day') }}</span>
                                            <b class="fs-6" id="sidebar_precio_dia_calc">—</b>
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

                                    @if($pagoTarjetaActivo && $anticipoMonto > 0)
                                    <div class="border-top pt-2 mt-1">
                                        <p class="fs-6 m-0 mb-1"><b><i class="bi bi-credit-card-fill me-1"></i>{{ __('form.advance_title') }}</b></p>
                                        <div class="d-flex justify-content-between py-1 border-bottom">
                                            <span class="text-muted fs-6">{{ __('form.advance_now') }}</span>
                                            <b class="fs-6" id="anticipo_cobro_display" style="color:var(--primary);">—</b>
                                        </div>
                                        <div class="d-flex justify-content-between py-1">
                                            <span class="text-muted fs-6">{{ __('form.balance_delivery') }}</span>
                                            <b class="fs-6 text-muted" id="anticipo_resto_display">—</b>
                                        </div>
                                    </div>
                                    @endif
                                </div>

                                <div id="btn_confirmar" class="mt-3" style="display:none;">
                                    <button type="submit" class="boton_link_xxl rounded w-100" style="padding:0 14px;">{{ __('form.submit') }}</button>
                                </div>

                            </div>
                        </div>{{-- fin col-md-4 --}}

                    </div>{{-- fin row --}}

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

<script>window.flashCarUltimaEtapa = {{ $pagoTarjetaActivo ? 3 : 2 }};</script>
@if($pagoTarjetaActivo)
<script>
window.flashCarAnticipo = {
    tipo:  '{{ $anticipoTipo }}',
    monto: {{ $anticipoMonto }},
};
</script>
<script src="https://js.stripe.com/v3/"></script>
<script>
(function () {
    const stripeKey = '{{ config('services.stripe.key') }}';
    if (!stripeKey || stripeKey.includes('REEMPLAZA')) return;

    const stripe   = Stripe(stripeKey);
    const elements = stripe.elements({ locale: 'es' });
    const cardStyle = {
        base: { fontSize: '15px', color: '#32325d', fontFamily: 'inherit', '::placeholder': { color: '#aab7c4' } },
        invalid: { color: '#dc3545' },
    };

    const cardNumber = elements.create('cardNumber', { style: cardStyle, showIcon: true });
    const cardExpiry = elements.create('cardExpiry', { style: cardStyle });
    const cardCvc    = elements.create('cardCvc',    { style: cardStyle });

    cardNumber.mount('#card-number-element');
    cardExpiry.mount('#card-expiry-element');
    cardCvc.mount('#card-cvc-element');

    [[cardNumber,'card-number-errors'],[cardExpiry,'card-expiry-errors'],[cardCvc,'card-cvc-errors']].forEach(function([el,errId]) {
        el.addEventListener('change', function(e) {
            const div = document.getElementById(errId);
            div.textContent   = e.error ? e.error.message : '';
            div.style.display = e.error ? 'block' : 'none';
        });
    });

    let pagoConfirmado = false;

    document.getElementById('formRenta').addEventListener('submit', async function (e) {
        if (window.flashCarUltimaEtapa !== 3 || etapaActual !== 3) return;
        if (pagoConfirmado) return;
        e.preventDefault();

        const btnPagar        = document.getElementById('btn_pagar');
        const btnAnterior     = document.getElementById('btn_anterior_pago');
        const errDiv          = document.getElementById('card-errors');
        const procesandoModal = new bootstrap.Modal(document.getElementById('modalProcesando'));

        btnPagar.disabled    = true;
        btnAnterior.disabled = true;
        errDiv.style.display = 'none';
        procesandoModal.show();

        const costoTotal = parseFloat(document.getElementById('costo_total_input').value) || 0;
        const _acfg      = window.flashCarAnticipo;
        const amount     = (_acfg && _acfg.monto > 0)
            ? (_acfg.tipo === 'porcentaje' ? Math.round(costoTotal * _acfg.monto) / 100 : Math.min(_acfg.monto, costoTotal))
            : costoTotal;
        const nombre = document.querySelector('input[name="nombre_completo"]').value;
        const correo = document.querySelector('input[name="correo"]').value;
        const csrf   = document.querySelector('meta[name="csrf-token"]').content;

        // Datos completos de la renta: viajan como metadata del PaymentIntent para que,
        // si el navegador nunca llega a completar el submit final, el webhook de Stripe
        // pueda recuperar la renta en vez de perder el registro por completo.
        const camposRenta = ['category_id', 'nombre_completo', 'telefono', 'correo', 'ciudad',
            'fecha_entrega', 'hora_entrega', 'lugar_entrega', 'fecha_devolucion', 'hora_devolucion',
            'lugar_devolucion', 'num_pasajeros', 'total_dias', 'costo_total'];
        const formData  = new FormData(this);
        const datosRenta = {};
        camposRenta.forEach(campo => { if (formData.has(campo)) datosRenta[campo] = formData.get(campo); });

        let clientSecret, intentId;
        try {
            const res  = await fetch('{{ route('rentas.paymentIntent') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ amount, ...datosRenta }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Error al iniciar el pago.');
            clientSecret = data.client_secret;
            intentId     = data.payment_intent_id;
        } catch (err) {
            procesandoModal.hide();
            errDiv.textContent   = err.message;
            errDiv.style.display = 'block';
            btnPagar.disabled    = false;
            btnAnterior.disabled = false;
            return;
        }

        const { error } = await stripe.confirmCardPayment(clientSecret, {
            payment_method: { card: cardNumber, billing_details: { name: nombre, email: correo } },
        });

        if (error) {
            procesandoModal.hide();
            errDiv.textContent   = error.message;
            errDiv.style.display = 'block';
            btnPagar.disabled    = false;
            btnAnterior.disabled = false;
            return;
        }

        document.getElementById('payment_intent_id').value = intentId;
        pagoConfirmado = true;
        this.submit();
    });
})();
</script>
<div class="modal fade" id="modalProcesando" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow rounded-3">
            <div class="modal-body text-center py-4 px-4">
                <div class="spinner-border mb-3" role="status" style="width:2.5rem;height:2.5rem;color:var(--amarillo_fuerte);">
                    <span class="visually-hidden">{{ __('form.processing') }}</span>
                </div>
                <h6 class="fw-bold mb-1">{{ __('form.processing') }}</h6>
                <p class="text-muted mb-0" style="font-size:0.82rem;">{{ __('form.processing_wait') }}</p>
            </div>
        </div>
    </div>
</div>
@endif

<script src="{{ asset('js/formulario_renta.js') }}?v={{ \App\Models\SiteSetting::get('asset_v_formulario', '1.1') }}"></script>

{{-- Datos de categorías para actualización dinámica --}}
@php
$categoriasJson = $categories->map(function($c) use ($firstVehicleImages) {
    $imgPath = $firstVehicleImages[$c->id] ?? null;
    return [
        'id'            => $c->id,
        'name'          => $c->name,
        'precio_dia'    => (float) $c->price_per_day,
        'precio_semana' => (float) $c->price_per_week,
        'precio_mes'    => (float) $c->price_per_month,
        'precio_dia_fmt'    => $c->formatted_price_per_day,
        'precio_semana_fmt' => $c->formatted_price_per_week,
        'precio_mes_fmt'    => $c->formatted_price_per_month,
        'garantia'      => $c->formatted_warranty,
        'image_url'     => $imgPath ? \Illuminate\Support\Facades\Storage::url($imgPath) : null,
    ];
});
@endphp
<script>
const categoriasData = @json($categoriasJson);

function fmtPrecio(n) {
    return '$' + parseFloat(n).toLocaleString('es-MX', {minimumFractionDigits: 2});
}

document.getElementById('select_categoria').addEventListener('change', function () {
    const id  = parseInt(this.value);
    const cat = categoriasData.find(x => x.id === id);
    const ph  = document.getElementById('sidebar_placeholder');
    const cdv = document.getElementById('sidebar_categoria_info');

    if (!cat) {
        ph.style.display  = '';
        cdv.style.display = 'none';
        document.getElementById('category_id_hidden').value = '';
        document.getElementById('precio_dia').value    = 0;
        document.getElementById('precio_semana').value = 0;
        document.getElementById('precio_mes').value    = 0;
        return;
    }

    // Actualizar hidden inputs para la calculadora
    document.getElementById('category_id_hidden').value = cat.id;
    document.getElementById('precio_dia').value         = cat.precio_dia;
    document.getElementById('precio_semana').value      = cat.precio_semana;
    document.getElementById('precio_mes').value         = cat.precio_mes;

    // Actualizar imagen del vehículo de ejemplo
    const catImg = document.getElementById('sidebar_cat_img');
    if (cat.image_url) {
        catImg.src          = cat.image_url;
        catImg.alt          = cat.name;
        catImg.style.display = '';
    } else {
        catImg.style.display = 'none';
    }

    // Actualizar sidebar
    document.getElementById('sidebar_cat_nombre').textContent      = cat.name;
    document.getElementById('sidebar_precio_dia').textContent      = cat.precio_dia_fmt;
    document.getElementById('sidebar_precio_semana').textContent   = cat.precio_semana_fmt;
    document.getElementById('sidebar_precio_mes').textContent      = cat.precio_mes_fmt;
    document.getElementById('sidebar_precio_dia_calc').textContent = cat.precio_dia_fmt;
    document.getElementById('sidebar_garantia_val').textContent    = cat.garantia;

    ph.style.display  = 'none';
    cdv.style.display = '';

    // Recalcular costo si ya hay fechas
    if (typeof calcularCosto === 'function') calcularCosto();
});
</script>

{{-- Parche: validarEtapa1 también exige vehículo --}}
<script>
(function () {
    const _orig = window.validarEtapa1;
    window.validarEtapa1 = function () {
        const sel = document.getElementById('select_categoria');
        let categoriaOk = true;
        if (sel && !sel.value) {
            sel.style.borderColor = '#dc3545';
            sel.style.boxShadow   = 'inset 0 1px 3px rgba(220,53,69,0.18)';
            categoriaOk = false;
        } else if (sel) {
            sel.style.borderColor = '';
            sel.style.boxShadow   = '';
        }
        return _orig() && categoriaOk;
    };
})();
</script>

@if($pagoTarjetaActivo && $anticipoMonto > 0)
<script>
(function () {
    function fmt(n) { return '$' + n.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','); }
    function actualizarDesglose() {
        const total = parseFloat(document.getElementById('costo_total_input').value) || 0;
        const cfg   = window.flashCarAnticipo;
        let cobro   = total;
        if (cfg && cfg.monto > 0) {
            cobro = cfg.tipo === 'porcentaje' ? Math.round(total * cfg.monto) / 100 : Math.min(cfg.monto, total);
        }
        const el = id => document.getElementById(id);
        if (el('anticipo_cobro_display')) el('anticipo_cobro_display').textContent = fmt(cobro);
        if (el('anticipo_resto_display')) el('anticipo_resto_display').textContent = fmt(total - cobro);
    }
    const resumenEl = document.getElementById('resumen_costo');
    if (resumenEl) new MutationObserver(actualizarDesglose).observe(resumenEl, { childList: true, subtree: true, characterData: true });
})();
</script>
@endif

<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.3.2/build/js/intlTelInput.min.js"></script>
<script>
    const inputNum = document.getElementById('numero_tel');
    const telHint  = document.getElementById('tel_hint');
    const telError = document.getElementById('tel_error');
    const tTelRequired = @json(__('form.tel_required'));
    const tTelRange    = @json(__('form.tel_range'));
    const tTelCount    = @json(__('form.tel_count'));
    const tTelErrExact = @json(__('form.tel_error_exact'));
    const tTelErrRange = @json(__('form.tel_error_range'));

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

    function t(tpl, vars) {
        return tpl.replace(/:(\w+)/g, function(_, k) { return vars[k] !== undefined ? vars[k] : ':' + k; });
    }

    function getReglas() {
        const iso2 = iti.getSelectedCountryData().iso2;
        return digitosPorPais[iso2] || { min: 6, max: 15 };
    }

    function actualizarHint() {
        const r = getReglas();
        inputNum.value         = '';
        telError.style.display = 'none';
        telHint.textContent    = r.min === r.max ? t(tTelRequired, {n: r.min}) : t(tTelRange, {min: r.min, max: r.max});
    }

    function validarTelefono() {
        const r      = getReglas();
        const digits = inputNum.value.replace(/\D/g, '');
        if (digits.length < r.min || digits.length > r.max) {
            telError.textContent   = r.min === r.max ? t(tTelErrExact, {n: r.min}) : t(tTelErrRange, {min: r.min, max: r.max});
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
            ? t(tTelCount, {cur: this.value.length, max: r.min})
            : t(tTelCount, {cur: this.value.length, max: r.max});
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
    const tSelectPoint = @json(__('form.select_point'));

    document.getElementById('select_estado').addEventListener('change', function() {
        const selected = this.value;
        const state    = statesData.find(s => s.name === selected);
        const points   = state ? state.points : [];

        ['select_entrega', 'select_devolucion'].forEach(id => {
            const sel = document.getElementById(id);
            sel.innerHTML = '<option value="">' + tSelectPoint + '</option>';
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
    const tDatePast  = @json(__('form.date_past'));
    const tDateReset = @json(__('form.date_reset'));
    const tDateOrder = @json(__('form.date_order'));

    function mostrarErrorFecha(msg) {
        $('#fecha_messenger_texto').text(msg);
        $('#fecha_messenger').stop(true).fadeIn(200);
        setTimeout(function () { $('#fecha_messenger').fadeOut(300); }, 4000);
    }

    function getHoy() {
        const d = new Date();
        return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    }

    fechaEntrega.min    = getHoy();
    fechaDevolucion.min = getHoy();

    fechaEntrega.addEventListener('change', function () {
        const hoy = getHoy();
        if (this.value && this.value < hoy) { this.value = ''; mostrarErrorFecha(tDatePast); return; }
        if (!this.value) return;
        const siguiente = new Date(this.value + 'T00:00:00');
        siguiente.setDate(siguiente.getDate() + 1);
        const minDev = siguiente.getFullYear() + '-' + String(siguiente.getMonth() + 1).padStart(2, '0') + '-' + String(siguiente.getDate()).padStart(2, '0');
        fechaDevolucion.min = minDev;
        if (fechaDevolucion.value && fechaDevolucion.value <= this.value) { fechaDevolucion.value = ''; mostrarErrorFecha(tDateReset); }
    });

    fechaDevolucion.addEventListener('change', function () {
        if (!this.value) return;
        if (fechaEntrega.value && this.value <= fechaEntrega.value) { this.value = ''; mostrarErrorFecha(tDateOrder); }
    });
</script>
@endsection
