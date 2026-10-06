@extends('layout.layouts')

@section('title', 'Editar Vehículo - Flash Car')

@section('content')
 @include('layout.header')
<div class="bg-light">
    <div class="main_principal">
        <div class="space_principal">
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        <div class="container">
            <div class="col-12 d-flex align-items-center justify-content-start flex-wrap rounded cont_base my-2">
                <div class="col-12 d-flex align-items-start justify-content-center flex-column p-2 bg_gris_8">
                    <h1 class="fs-6 text_uppcase">Editar Vehículo: {{ $vehicle->name }}</h1>
                    <div class="col-4 col-sm-4 col-md-2 col-lg-1">
                        <a class="boton_forms b_sm rounded link_decoration_none display_flex_center_center" href="{{ route('vehiculos.categoria', $vehicle->category_id ?? 'sin-categoria') }}">← Volver</a>
                    </div>
                </div>

                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start flex-wrap p-2">
                    <p class="text-dark fs-6 m-0"><b>Información Básica</b></p>
                </div>

                <form method="POST" action="{{ route('vehicles.update', $vehicle->id) }}" enctype="multipart/form-data" class="col-12 d-flex align-items-center justify-content-start flex-wrap">
                @csrf
                @method('PUT')

                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-1">

                    {{-- Imagen --}}
                    <div class="col-12 col-sm-12 col-md-4 col-lg-3 d-flex align-items-center justify-content-center flex-column p-2">
                        <div id="imagePreview">
                            <img id="preview"
                                 src="{{ $vehicle->image_path ? Storage::url($vehicle->image_path) : asset('./img/sin_url_auto.png') }}"
                                 alt="Vista previa" width="100%">
                        </div>
                        <div class="fila_form_f_b py-2 col-12">
                            <label class="label_form_f_b fs-6 p-1"><b>Cambiar Imagen</b></label>
                            <input class="input_form_f_b fs-6 p-1" type="file" id="image" name="image" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                            <small class="text-muted">Formatos: JPG, PNG, GIF, WEBP. Máx: 2MB</small>
                        </div>
                    </div>

                    <div class="col-12 col-sm-12 col-md-8 col-lg-9 d-flex align-items-center justify-content-start flex-wrap">
                        <div class="col-12 col-sm-12 col-md-6 col-lg-4 fila_form_f_b py-2">
                            <label class="label_form_f_b fs-6 p-1"><b>Nombre del Vehículo *</b></label>
                            <input class="input_form_f_b fs-6 p-1" type="text" name="name" value="{{ old('name', $vehicle->name) }}" placeholder="Ej: Toyota Corolla 2024" required autofocus>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-4 fila_form_f_b py-2">
                            <label class="label_form_f_b fs-6 p-1"><b>Categoría *</b></label>
                            <select class="input_form_f_b fs-6 p-1" name="category_id" required>
                                <option value="">-- Selecciona una categoría --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $vehicle->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-4 fila_form_f_b py-2">
                            <label class="label_form_f_b fs-6 p-1"><b>Pasajeros *</b></label>
                            <input class="input_form_f_b fs-6 p-1" type="number" name="passengers" value="{{ old('passengers', $vehicle->passengers) }}" min="1" max="50" required>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-4 fila_form_f_b py-2">
                            <label class="label_form_f_b fs-6 p-1"><b>Combustible (L) *</b></label>
                            <input class="input_form_f_b fs-6 p-1" type="number" name="fuel_capacity" value="{{ old('fuel_capacity', $vehicle->fuel_capacity) }}" step="0.1" min="0" required>
                        </div>
                    </div>
                </div>

                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start flex-wrap p-2">
                    <p class="text-dark fs-6 m-0"><b>Detalles del Vehículo</b></p>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-1">
                    <div class="col-12 col-sm-12 col-md-6 col-lg-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Marca</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" name="brand" value="{{ old('brand', $vehicle->brand) }}" placeholder="Ej: Toyota, Honda, Ford">
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 col-lg-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Modelo</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" name="model" value="{{ old('model', $vehicle->model) }}" placeholder="Ej: Corolla, Civic, Mustang">
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 col-lg-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Año</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="number" name="year" value="{{ old('year', $vehicle->year) }}" min="1900" max="{{ date('Y') + 1 }}">
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 col-lg-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Número de Placa</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" name="plate_number" value="{{ old('plate_number', $vehicle->plate_number) }}" placeholder="ABC-1234">
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 col-lg-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Proveedor</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" name="proveedor" value="{{ old('proveedor', $vehicle->proveedor) }}" maxlength="255" placeholder="Nombre del proveedor">
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 col-lg-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Fecha de pago a proveedor</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="date" name="fecha_pago_proveedor" value="{{ old('fecha_pago_proveedor', $vehicle->fecha_pago_proveedor ?? '') }}">
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 col-lg-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Costo por día</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="number" name="costo_por_dia" value="{{ old('costo_por_dia', $vehicle->costo_por_dia) }}" min="0" step="0.01" placeholder="0.00">
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 col-lg-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Transmisión *</b></label>
                        <select class="input_form_f_b fs-6 p-1" name="transmission" required>
                            <option value="automatic" {{ old('transmission', $vehicle->transmission) == 'automatic' ? 'selected' : '' }}>Automático</option>
                            <option value="manual" {{ old('transmission', $vehicle->transmission) == 'manual' ? 'selected' : '' }}>Manual</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 col-lg-4col-sm-12 col-md-6 col-lg-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Kilometraje</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="number" name="mileage" value="{{ old('mileage', $vehicle->mileage ?? '') }}" placeholder="Ej: 15000" min="0">
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 col-lg-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Próxima Verificación</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="date" name="next_verification" value="{{ old('next_verification', $vehicle->next_verification ?? '') }}">
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 col-lg-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Ciudad disponible</b></label>
                        <select class="input_form_f_b fs-6 p-1" name="state_id">
                            <option value="">-- Selecciona una ciudad --</option>
                            @foreach($states as $state)
                                <option value="{{ $state->id }}" {{ old('state_id', $vehicle->states->first()?->id) == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                            @endforeach
                        </select>
                        @if($states->isEmpty())
                            <small class="text-muted">No hay ciudades registradas</small>
                        @endif
                    </div>
                </div>

                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start flex-wrap p-2">
                    <p class="text-dark fs-6 m-0"><b>Estado del Vehículo</b></p>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-1">
                    <div class="col-12 col-sm-12 col-md-3 fila_form_f_b py-2">
                        <label class="label_form_f_b d-flex align-items-center justify-content-start fs-6 py-1 px-1">
                            <input class="check_box_form" type="checkbox" name="available" value="1" {{ old('available', $vehicle->available) ? 'checked' : '' }}>
                            <b class="mx-2">Disponible para rentar</b>
                        </label>
                    </div>
                    <div class="col-12 col-sm-12 col-md-3 fila_form_f_b py-2">
                        <label class="label_form_f_b d-flex align-items-center justify-content-start fs-6 py-1 px-1">
                            <input class="check_box_form" type="checkbox" name="mostrar_en_catalogo" value="1" {{ old('mostrar_en_catalogo', $vehicle->mostrar_en_catalogo) ? 'checked' : '' }}>
                            <b class="mx-2">Mostrar en catálogo público</b>
                        </label>
                    </div>
                    <div class="col-12 col-sm-12 col-md-3 fila_form_f_b py-2">
                        <label class="label_form_f_b d-flex align-items-center justify-content-start fs-6 py-1 px-1">
                            <input class="check_box_form" type="checkbox" id="chk_active" name="active" value="1" {{ old('active', $vehicle->active) ? 'checked' : '' }}>
                            <b class="mx-2">Vehículo activo</b>
                        </label>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 fila_form_f_b py-2" id="bloque_motivo" style="{{ old('active', $vehicle->active) ? 'display:none' : 'display:flex' }}">
                        <span class="label_form_f_b fs-6 p-1"><b>Motivo de inactividad</b></span>
                        <div class="input_form_f_b p-2 d-flex align-items-center" style="gap:18px; flex-wrap:wrap;">
                            @foreach(['Servicio', 'Mtto Mayor', 'Traslado'] as $motivo)
                            <label class="d-flex align-items-center fs-6" style="gap:6px; cursor:pointer; white-space:nowrap;">
                                <input type="radio" name="motivo_inactivo" value="{{ $motivo }}"
                                       {{ old('motivo_inactivo', $vehicle->motivo_inactivo) === $motivo ? 'checked' : '' }}>
                                {{ $motivo }}
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start flex-wrap p-2">
                    <p class="text-dark fs-6 m-0"><b>Información de Registro</b></p>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-1">
                    <div class="col-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Fecha de Creación</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $vehicle->created_at->format('d/m/Y H:i:s') }}" readonly>
                    </div>
                    <div class="col-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Última Actualización</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $vehicle->updated_at->format('d/m/Y H:i:s') }}" readonly>
                    </div>
                </div>

                <div class="col-12 py-2 d-flex align-items-center justify-content-center">
                    <a href="{{ route('vehiculos.categoria', $vehicle->category_id ?? 'sin-categoria') }}" class="boton_link_lg rounded">Cancelar</a>
                    <button type="submit" class="boton_link_lg rounded">Guardar</button>
                </div>

                </form>

                {{-- SECCIÓN GALERÍA --}}
                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start flex-wrap p-2 mt-2">
                    <p class="text-dark fs-6 m-0"><b>Galería de Fotos</b></p>
                    <small class="text-dark ms-2">(máximo 5 fotos — {{ $vehicle->images->count() }}/5)</small>
                </div>

                {{-- Fotos actuales --}}
                <div id="galeria_fotos" class="col-12 d-flex align-items-start justify-content-start flex-wrap p-2">
                    @forelse($vehicle->images as $image)
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2 p-1 position-relative">
                        <img src="{{ Storage::url($image->image_path) }}" alt="Foto galería" width="100%" class="rounded shadow-sm">
                        <form method="POST" action="{{ route('vehiculos.images.delete', $image->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="position-absolute top-0 end-0 bg-danger text-white border-0 rounded px-2 py-1 m-1"
                                style="font-size:0.75rem; cursor:pointer;"
                                onclick="return confirm('¿Eliminar esta foto?')">✕</button>
                        </form>
                    </div>
                    @empty
                        <p id="galeria_vacia" class="text-muted fs-6 p-2">No hay fotos en la galería aún.</p>
                    @endforelse
                </div>

                {{-- Subir nuevas fotos --}}
                @if($vehicle->images->count() < 5)
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-2">
                    <form method="POST" action="{{ route('vehiculos.images.upload', $vehicle->id) }}" enctype="multipart/form-data" class="col-12 d-flex align-items-end justify-content-start flex-wrap">
                        @csrf
                        <div class="col-12 col-md-6 fila_form_f_b py-2">
                            <label class="label_form_f_b fs-6 p-1"><b>Agregar fotos</b></label>
                            <input class="input_form_f_b fs-6 p-1" type="file" id="input_galeria" name="images[]" multiple
                                data-disponibles="{{ 5 - $vehicle->images->count() }}"
                                accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                            <small class="text-muted">Puedes subir hasta {{ 5 - $vehicle->images->count() }} foto(s) más. Formatos: JPG, PNG, WEBP. Máx: 2MB c/u</small>
                        </div>
                        <div class="col-12 col-md-2 py-2 px-1">
                            <button type="submit" id="btn_subir_fotos" class="boton_link_lg rounded w-100" disabled>Subir fotos</button>
                        </div>
                    </form>
                </div>
                @else
                <div class="col-12 p-2">
                    <p class="text-muted fs-6">Ya tienes el máximo de 5 fotos. Elimina alguna para agregar nuevas.</p>
                </div>
                @endif

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
@push('scripts')
<script>
document.getElementById('chk_active').addEventListener('change', function () {
    document.getElementById('bloque_motivo').style.display = this.checked ? 'none' : 'flex';
    if (this.checked) {
        document.querySelectorAll('input[name="motivo_inactivo"]').forEach(r => r.checked = false);
    }
});

// Vista previa de la imagen principal
document.getElementById('image').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => document.getElementById('preview').src = e.target.result;
    reader.readAsDataURL(file);
});

// Vista previa de las fotos de galería antes de subirlas
(function () {
    const input = document.getElementById('input_galeria');
    if (!input) return;

    const galeria     = document.getElementById('galeria_fotos');
    const vacia       = document.getElementById('galeria_vacia');
    const btnSubir    = document.getElementById('btn_subir_fotos');
    const disponibles = parseInt(input.dataset.disponibles, 10);
    let seleccionadas = new DataTransfer();

    function render() {
        galeria.querySelectorAll('.foto_nueva').forEach(el => el.remove());

        Array.from(seleccionadas.files).forEach((file, idx) => {
            const cont = document.createElement('div');
            cont.className = 'col-6 col-sm-4 col-md-3 col-lg-2 p-1 position-relative foto_nueva';

            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.onload = () => URL.revokeObjectURL(img.src);
            img.style.width = '100%';
            img.style.outline = '3px dashed var(--amarillo)';
            img.className = 'rounded shadow-sm';

            const badge = document.createElement('span');
            badge.className = 'position-absolute top-0 start-0 bg_amarillo text-dark rounded px-2 m-2';
            badge.style.fontSize = '0.7rem';
            badge.textContent = 'Nueva';

            const quitar = document.createElement('button');
            quitar.type = 'button';
            quitar.className = 'position-absolute top-0 end-0 bg-danger text-white border-0 rounded px-2 py-1 m-1';
            quitar.style.fontSize = '0.75rem';
            quitar.textContent = '✕';
            quitar.title = 'Quitar de la selección';
            quitar.addEventListener('click', () => {
                const dt = new DataTransfer();
                Array.from(seleccionadas.files).forEach((f, i) => { if (i !== idx) dt.items.add(f); });
                seleccionadas = dt;
                input.files = seleccionadas.files;
                render();
            });

            cont.append(img, badge, quitar);
            galeria.appendChild(cont);
        });

        const hay = seleccionadas.files.length > 0;
        if (vacia) vacia.style.display = hay ? 'none' : '';
        btnSubir.disabled = !hay;
    }

    input.addEventListener('change', function () {
        // Acumula con lo ya seleccionado, respetando el límite de 5 fotos
        const dt = new DataTransfer();
        Array.from(seleccionadas.files).forEach(f => dt.items.add(f));
        Array.from(this.files).forEach(f => { if (dt.files.length < disponibles) dt.items.add(f); });

        if (seleccionadas.files.length + this.files.length > disponibles) {
            alert(`Solo puedes agregar ${disponibles} foto(s) más.`);
        }

        seleccionadas = dt;
        input.files = seleccionadas.files;
        render();
    });
})();
</script>
@endpush

@endsection