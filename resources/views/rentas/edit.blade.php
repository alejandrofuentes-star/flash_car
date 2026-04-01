@extends('layout.layouts')

@section('title', 'Editar Renta #' . $renta->id)

@section('content')
@include('layout.header')
<div class="bg-light">
    <div class="main_principal">
        <div class="space_principal">
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        <div class="container">

            <div class="col-12 d-flex align-items-center justify-content-start flex-wrap rounded cont_base my-2">
                <div class="col-12 d-flex align-items-start justify-content-center flex-column p-2 bg_gris_8">
                    <h1 class="fs-6 text_uppcase">Editar Renta #{{ $renta->id }}</h1>
                    <div class="col-4 col-sm-4 col-md-2 col-lg-1">
                        <a class="boton_forms b_sm rounded link_decoration_none display_flex_center_center" href="{{ route('rentas.show', $renta->id) }}">← Volver</a>
                    </div>
                </div>

                <form method="POST" action="{{ route('rentas.update', $renta->id) }}" class="col-12 d-flex align-items-center justify-content-start flex-wrap">
                @csrf
                @method('PUT')

                {{-- Estado --}}
                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start p-2">
                    <p class="text-dark fs-6 m-0"><b>Estado de la Solicitud</b></p>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-1">
                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Estado *</b></label>
                        <select class="input_form_f_b fs-6 p-1" name="estado" required>
                            <option value="pendiente"   {{ old('estado', $renta->estado) == 'pendiente'   ? 'selected' : '' }}>Pendiente</option>
                            <option value="confirmada"  {{ old('estado', $renta->estado) == 'confirmada'  ? 'selected' : '' }}>Confirmada</option>
                            <option value="cancelada"   {{ old('estado', $renta->estado) == 'cancelada'   ? 'selected' : '' }}>Cancelada</option>
                            <option value="completada"  {{ old('estado', $renta->estado) == 'completada'  ? 'selected' : '' }}>Completada</option>
                        </select>
                    </div>
                </div>

                {{-- Datos del cliente --}}
                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start p-2">
                    <p class="text-dark fs-6 m-0"><b>Datos del Cliente</b></p>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-1">
                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Nombre Completo *</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" name="nombre_completo" value="{{ old('nombre_completo', $renta->nombre_completo) }}" required>
                    </div>
                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Teléfono *</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="tel" name="telefono" value="{{ old('telefono', $renta->telefono) }}" required>
                    </div>
                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Correo *</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="email" name="correo" value="{{ old('correo', $renta->correo) }}" required>
                    </div>
                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Ciudad *</b></label>
                        <select class="input_form_f_b fs-6 p-1" name="ciudad" id="select_estado" required>
                            <option value="">-- Selecciona una ciudad --</option>
                            @foreach($states as $state)
                                <option value="{{ $state->name }}" {{ old('ciudad', $renta->ciudad) == $state->name ? 'selected' : '' }}>
                                    {{ $state->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>No. Pasajeros *</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="number" name="num_pasajeros" value="{{ old('num_pasajeros', $renta->num_pasajeros) }}" min="1" required>
                    </div>
                </div>

                {{-- Vehículo --}}
                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start p-2">
                    <p class="text-dark fs-6 m-0"><b>Vehículo</b></p>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-1">
                    <div class="col-12 col-md-3 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Vehículo *</b></label>
                        <select class="input_form_f_b fs-6 p-1" name="vehicle_id" required>
                            <option value="">-- Selecciona un vehículo --</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $renta->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-3 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Categoría</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $renta->vehicle->category->name ?? '—' }}" readonly>
                    </div>
                </div>

                {{-- Entrega --}}
                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start p-2">
                    <p class="text-dark fs-6 m-0"><b>Entrega</b></p>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-1">
                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Fecha de Entrega *</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="date" name="fecha_entrega" value="{{ old('fecha_entrega', $renta->fecha_entrega->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Hora de Entrega *</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="time" name="hora_entrega" value="{{ old('hora_entrega', $renta->hora_entrega) }}" required>
                    </div>
                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Lugar de Entrega *</b></label>
                        <select class="input_form_f_b fs-6 p-1" name="lugar_entrega" id="select_entrega" required>
                            <option value="{{ $renta->lugar_entrega }}" selected>{{ $renta->lugar_entrega }}</option>
                        </select>
                    </div>
                </div>

                {{-- Devolución --}}
                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start p-2">
                    <p class="text-dark fs-6 m-0"><b>Devolución</b></p>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-1">
                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Fecha de Devolución *</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="date" name="fecha_devolucion" value="{{ old('fecha_devolucion', $renta->fecha_devolucion->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Hora de Devolución *</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="time" name="hora_devolucion" value="{{ old('hora_devolucion', $renta->hora_devolucion) }}" required>
                    </div>
                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Lugar de Devolución *</b></label>
                        <select class="input_form_f_b fs-6 p-1" name="lugar_devolucion" id="select_devolucion" required>
                            <option value="{{ $renta->lugar_devolucion }}" selected>{{ $renta->lugar_devolucion }}</option>
                        </select>
                    </div>
                </div>

                {{-- Resumen de costo --}}
                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start p-2">
                    <p class="text-dark fs-6 m-0"><b>Resumen de Costo</b></p>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-1">
                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Total de Días *</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="number" name="total_dias" value="{{ old('total_dias', $renta->total_dias) }}" min="1" required>
                    </div>
                    <div class="col-12 col-md-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Costo Total *</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="number" name="costo_total" value="{{ old('costo_total', $renta->costo_total) }}" step="0.01" min="0" required>
                    </div>
                </div>

                {{-- Botones --}}
                <div class="col-12 py-3 d-flex align-items-center justify-content-center">
                    <a href="{{ route('rentas.show', $renta->id) }}" class="boton_link_lg rounded">Cancelar</a>
                    <button type="submit" class="boton_link_xxl rounded">Actualizar</button>
                </div>

                </form>

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
<script>
    const fechaEntrega   = document.querySelector('input[name="fecha_entrega"]');
    const fechaDevolucion = document.querySelector('input[name="fecha_devolucion"]');
    const precioDia      = {{ $renta->vehicle->category->price_per_day ?? 0 }};

    function calcularCosto() {
        if (!fechaEntrega.value || !fechaDevolucion.value) return;

        const inicio = new Date(fechaEntrega.value);
        const fin    = new Date(fechaDevolucion.value);
        const dias   = Math.ceil((fin - inicio) / (1000 * 60 * 60 * 24));

        if (dias <= 0) return;

        const costo = dias * precioDia;

        document.querySelector('input[name="total_dias"]').value  = dias;
        document.querySelector('input[name="costo_total"]').value = costo.toFixed(2);
    }

    fechaEntrega.addEventListener('change', calcularCosto);
    fechaDevolucion.addEventListener('change', calcularCosto);
</script>
<script>
    const statesData = @json($states->map(fn($s) => [
        'name'   => $s->name,
        'points' => $s->deliveryPoints->where('active', true)->map(fn($p) => $p->name)->values()
    ]));

    // Cargar puntos del estado actual al entrar a la vista
    window.addEventListener('DOMContentLoaded', function() {
        const estadoActual  = document.getElementById('select_estado').value;
        const entregaActual = '{{ $renta->lugar_entrega }}';
        const devActual     = '{{ $renta->lugar_devolucion }}';

        if (estadoActual) {
            const state  = statesData.find(s => s.name === estadoActual);
            const points = state ? state.points : [];

            ['select_entrega', 'select_devolucion'].forEach((id, i) => {
                const sel     = document.getElementById(id);
                const current = i === 0 ? entregaActual : devActual;
                sel.innerHTML = '<option value="">-- Selecciona un punto --</option>';
                points.forEach(p => {
                    const opt      = document.createElement('option');
                    opt.value      = p;
                    opt.textContent = p;
                    if (p === current) opt.selected = true;
                    sel.appendChild(opt);
                });
            });
        }
    });

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