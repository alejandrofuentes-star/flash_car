@extends('layout.layouts')

@section('title', 'Rentar - ' . $vehicle->name)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/styles_pagina_principal.css') }}">
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
                            <span class="fs-6 fw-bold" id="label_1">Datos Personales</span>
                            <div class="mx-2 etapa_linea"></div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-4 col-lg-3 d-flex align-items-center justify-content-start py-1">
                            <div class="mx-2 etapa_circulo" id="circulo_2">2</div>
                            <span class="fs-6 text-muted" id="label_2">Fechas y Lugares</span>
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
                                    <p class="text-dark fs-6 m-0"><b>① Datos Personales</b></p>
                                </div>
                                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-1 border rounded-bottom mb-3">
                                    <div class="col-12 col-md-6 fila_form_f_b py-2">
                                        <label class="label_form_f_b fs-6 p-1"><b>Nombre Completo *</b></label>
                                        <input class="input_form_f_b fs-6 p-1" type="text" name="nombre_completo" value="{{ old('nombre_completo') }}" placeholder="Ej: Juan García López" required>
                                    </div>
                                    <div class="col-12 col-md-6 fila_form_f_b py-2">
                                        <label class="label_form_f_b fs-6 p-1"><b>Teléfono *</b></label>
                                        <input type="hidden" name="telefono" id="telefono_completo">
                                        <div class="d-flex" style="gap:4px;">
                                            <select id="codigo_pais" class="input_form_f_b fs-6 p-1" style="width:140px; flex-shrink:0;">
                                                <option value="+52"  data-min="10" data-max="10" data-ph="5512345678">🇲🇽 +52</option>
                                                <option value="+1"   data-min="10" data-max="10" data-ph="2025551234">🇺🇸 +1</option>
                                                <option value="+1-CA" data-min="10" data-max="10" data-ph="4165551234">🇨🇦 +1</option>
                                                <option value="+54"  data-min="10" data-max="10" data-ph="1123456789">🇦🇷 +54</option>
                                                <option value="+55"  data-min="10" data-max="11" data-ph="11987654321">🇧🇷 +55</option>
                                                <option value="+56"  data-min="9"  data-max="9"  data-ph="912345678">🇨🇱 +56</option>
                                                <option value="+57"  data-min="10" data-max="10" data-ph="3001234567">🇨🇴 +57</option>
                                                <option value="+51"  data-min="9"  data-max="9"  data-ph="912345678">🇵🇪 +51</option>
                                                <option value="+58"  data-min="10" data-max="10" data-ph="4121234567">🇻🇪 +58</option>
                                                <option value="+593" data-min="9"  data-max="9"  data-ph="991234567">🇪🇨 +593</option>
                                                <option value="+502" data-min="8"  data-max="8"  data-ph="51234567">🇬🇹 +502</option>
                                                <option value="+503" data-min="8"  data-max="8"  data-ph="71234567">🇸🇻 +503</option>
                                                <option value="+504" data-min="8"  data-max="8"  data-ph="91234567">🇭🇳 +504</option>
                                                <option value="+505" data-min="8"  data-max="8"  data-ph="81234567">🇳🇮 +505</option>
                                                <option value="+506" data-min="8"  data-max="8"  data-ph="81234567">🇨🇷 +506</option>
                                                <option value="+507" data-min="8"  data-max="8"  data-ph="61234567">🇵🇦 +507</option>
                                                <option value="+53"  data-min="8"  data-max="8"  data-ph="51234567">🇨🇺 +53</option>
                                                <option value="+34"  data-min="9"  data-max="9"  data-ph="612345678">🇪🇸 +34</option>
                                                <option value="+44"  data-min="10" data-max="10" data-ph="7911123456">🇬🇧 +44</option>
                                                <option value="+33"  data-min="9"  data-max="9"  data-ph="612345678">🇫🇷 +33</option>
                                                <option value="+49"  data-min="10" data-max="11" data-ph="15123456789">🇩🇪 +49</option>
                                                <option value="+39"  data-min="9"  data-max="10" data-ph="3123456789">🇮🇹 +39</option>
                                            </select>
                                            <input class="input_form_f_b fs-6 p-1" type="tel" id="numero_tel" placeholder="5512345678" style="flex:1;" required>
                                        </div>
                                        <small id="tel_hint" class="text-muted px-1" style="font-size:0.75rem;">10 dígitos requeridos</small>
                                        <small id="tel_error" class="text-danger px-1" style="font-size:0.75rem; display:none;"></small>
                                    </div>
                                    <div class="col-12 col-md-6 fila_form_f_b py-2">
                                        <label class="label_form_f_b fs-6 p-1"><b>Correo Electrónico *</b></label>
                                        <input class="input_form_f_b fs-6 p-1" type="email" name="correo" value="{{ old('correo') }}" placeholder="Ej: correo@ejemplo.com" required>
                                    </div>
                                    <div class="col-12 col-md-6 fila_form_f_b py-2">
                                        <label class="label_form_f_b fs-6 p-1"><b>No. de Pasajeros *</b></label>
                                        <input class="input_form_f_b fs-6 p-1" type="number" name="num_pasajeros" value="{{ old('num_pasajeros') }}" min="1" max="{{ $vehicle->passengers }}" placeholder="Máx: {{ $vehicle->passengers }}" required>
                                    </div>
                                    <div class="col-12 col-md-6 fila_form_f_b py-2">
                                        <label class="label_form_f_b fs-6 p-1"><b>Ciudad *</b></label>
                                        <select class="input_form_f_b fs-6 p-1" name="ciudad" id="select_estado" required>
                                            <option value="">-- Selecciona una ciudad --</option>
                                            @foreach($states as $state)
                                                <option value="{{ $state->name }}" {{ old('ciudad') == $state->name ? 'selected' : '' }}>
                                                    {{ $state->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6 fila_form_f_b py-2">
                                        <label class="label_form_f_b fs-6 p-1"><b>Categoría</b></label>
                                        <span class="input_form_f_b fs-6 p-1">{{ $vehicle->category->name ?? '—' }}</span>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end p-2">
                                        <a class="boton_link_xxl b_sm rounded link_decoration_none display_flex_center_center" href="{{ route('catalogo.detalle', $vehicle->id) }}">← Volver</a>
                                        <button type="button" class="boton_link_xxl rounded" onclick="irEtapa(2)">Siguiente →</button>
                                    </div>
                                </div>
                            </div>

                            {{-- ETAPA 2: Fechas y Lugares --}}
                            <div id="etapa_2" style="display:none;">
                                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start p-2 rounded-top">
                                    <p class="text-dark fs-6 m-0"><b>② Fechas y Lugares</b></p>
                                </div>
                                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-1 border rounded-bottom mb-3">
                                    <div class="col-12 bg_gris_8 p-2 mb-2">
                                        <p class="fs-6 m-0"><b>Entrega del Vehículo</b></p>
                                    </div>
                                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                                        <label class="label_form_f_b fs-6 p-1"><b>Fecha de Entrega *</b></label>
                                        <input class="input_form_f_b fs-6 p-1" type="date" id="fecha_entrega" name="fecha_entrega" value="{{ old('fecha_entrega') }}" required>
                                    </div>
                                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                                        <label class="label_form_f_b fs-6 p-1"><b>Hora de Entrega *</b></label>
                                        <input class="input_form_f_b fs-6 p-1" type="time" name="hora_entrega" value="{{ old('hora_entrega') }}" required>
                                    </div>
                                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                                        <label class="label_form_f_b fs-6 p-1"><b>Lugar de Entrega *</b></label>
                                        <select class="input_form_f_b fs-6 p-1" name="lugar_entrega" id="select_entrega" required>
                                            <option value="">-- Primero selecciona ciudad --</option>
                                        </select>
                                    </div>
                                    <div class="col-12 bg_gris_8 p-2 mb-2 mt-2">
                                        <p class="fs-6 m-0"><b>Devolución del Vehículo</b></p>
                                    </div>
                                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                                        <label class="label_form_f_b fs-6 p-1"><b>Fecha de Devolución *</b></label>
                                        <input class="input_form_f_b fs-6 p-1" type="date" id="fecha_devolucion" name="fecha_devolucion" value="{{ old('fecha_devolucion') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                                    </div>
                                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                                        <label class="label_form_f_b fs-6 p-1"><b>Hora de Devolución *</b></label>
                                        <input class="input_form_f_b fs-6 p-1" type="time" name="hora_devolucion" value="{{ old('hora_devolucion') }}" required>
                                    </div>
                                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                                        <label class="label_form_f_b fs-6 p-1"><b>Lugar de Devolución *</b></label>
                                        <select class="input_form_f_b fs-6 p-1" name="lugar_devolucion" id="select_devolucion" required>
                                            <option value="">-- Primero selecciona ciudad --</option>
                                        </select>
                                    </div>
                                    <div class="col-12 d-flex justify-content-between p-2">
                                        <button type="button" class="boton_link_xxl rounded" onclick="irEtapa(1)">← Anterior</button>
                                        <button type="submit" class="boton_link_xxl rounded">Enviar Solicitud</button>
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
                                    <p class="fs-6 m-0 mb-2"><b>Resumen de Costo</b></p>
                                    <div class="d-flex justify-content-between py-1 border-bottom">
                                        <span class="text-muted fs-6">Precio por día</span>
                                        <b class="fs-6">${{ number_format($vehicle->category->price_per_day ?? 0, 2) }}</b>
                                    </div>
                                    <div class="d-flex justify-content-between py-1 border-bottom">
                                        <span class="text-muted fs-6">Total de días</span>
                                        <b class="fs-6" id="resumen_dias">—</b>
                                    </div>
                                    <div class="d_flex_center_between py-1 border-bottom" id="row_cargo_extra" style="display:none">
                                        <span class="text-muted fs-6">Cargo por hora extra</span>
                                        <b class="fs-6 text-danger" id="texto_cargo_extra"></b>
                                    </div>
                                    <div class="d-flex justify-content-between py-2 mt-1">
                                        <span class="fs-6"><b>Costo Total</b></span>
                                        <b class="fs-4" id="resumen_costo" style="color:var(--primary);">$0.00</b>
                                    </div>
                                </div>

                                @if($vehicle->category)
                                <div class="border-top pt-2 mt-1">
                                    <div class="d-flex justify-content-between py-1">
                                        <span class="text-muted fs-6">Garantía</span>
                                        <b class="fs-6">{{ $vehicle->category->formatted_warranty }}</b>
                                    </div>
                                </div>
                                @endif

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
@include('layout.footer')
<script src="{{ asset('js/formulario_renta.js') }}"></script>
<script>
    const selectPais = document.getElementById('codigo_pais');
    const inputNum   = document.getElementById('numero_tel');
    const telHint    = document.getElementById('tel_hint');
    const telError   = document.getElementById('tel_error');

    function getReglas() {
        const opt = selectPais.options[selectPais.selectedIndex];
        return { min: parseInt(opt.dataset.min), max: parseInt(opt.dataset.max), ph: opt.dataset.ph };
    }

    function actualizarHint() {
        const r = getReglas();
        inputNum.placeholder   = r.ph;
        inputNum.value         = '';
        telError.style.display = 'none';
        telHint.textContent    = r.min === r.max
            ? `${r.min} dígitos requeridos`
            : `${r.min}–${r.max} dígitos requeridos`;
    }

    function validarTelefono() {
        const r      = getReglas();
        const digits = inputNum.value;
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

    selectPais.addEventListener('change', actualizarHint);

    inputNum.addEventListener('input', function () {
        const r = getReglas();
        // Solo dígitos, limitado al máximo del país actual
        this.value = this.value.replace(/\D/g, '').slice(0, r.max);
        telHint.textContent = r.min === r.max
            ? `${this.value.length}/${r.min} dígitos`
            : `${this.value.length}/${r.max} dígitos`;
        if (this.value.length > 0) validarTelefono();
        else telError.style.display = 'none';
    });

    actualizarHint();

    document.getElementById('formRenta').addEventListener('submit', function (e) {
        if (!validarTelefono()) { e.preventDefault(); inputNum.focus(); return; }
        const codigo = selectPais.value.replace('-CA', '');
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
@endsection