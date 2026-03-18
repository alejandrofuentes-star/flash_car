@extends('layout.layouts')

@section('title', 'Nuevo Vehículo - Flash Car')

@section('content')
 @include('layout.header')
<div class="bg-light">
    <div class="main_principal">
        <div class="space_principal">
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        <div class="container">
            <div class="col-12 d-flex align-items-center justify-content-start flex-wrap rounded cont_base my-2">
                <div class="col-12 d-flex align-items-start justify-content-center flex-column p-2 bg_gris_8">
                    <h1 class="fs-6 text_uppcase">Nuevo Vehículo</h1>
                    <div class="col-4 col-sm-4 col-md-2 col-lg-1">
                        <a class="boton_forms b_sm rounded link_decoration_none display_flex_center_center" href="{{ route('vehiculos.index') }}">← Volver</a>
                    </div>
                </div>

                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start flex-wrap p-2">
                    <p class="text-dark fs-6 m-0"><b>Información Básica</b></p>
                </div>

                <form method="POST" action="{{ route('vehicles.store') }}" enctype="multipart/form-data" class="col-12 d-flex align-items-center justify-content-start flex-wrap">
                @csrf

                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-1">

                    {{-- Imagen --}}
                    <div class="col-12 col-sm-12 col-md-4 col-lg-3 d-flex align-items-center justify-content-center flex-column p-2">
                        <div id="imagePreview">
                            <img id="preview" src="{{ asset('./img/sin_url_auto.png') }}" alt="Vista previa" width="100%">
                        </div>
                        <div class="fila_form_f_b py-2 col-12">
                            <label class="label_form_f_b fs-6 p-1"><b>Imagen del Vehículo</b></label>
                            <input class="input_form_f_b fs-6 p-1" type="file" id="image" name="image" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                            <small class="text-muted">Formatos: JPG, PNG, GIF, WEBP. Máx: 2MB</small>
                        </div>
                    </div>

                    <div class="col-12 col-sm-12 col-md-8 col-lg-9 d-flex align-items-center justify-content-start flex-wrap">
                        <div class="col-6 fila_form_f_b py-2">
                            <label class="label_form_f_b fs-6 p-1"><b>Nombre del Vehículo *</b></label>
                            <input class="input_form_f_b fs-6 p-1" type="text" name="name" value="{{ old('name') }}" placeholder="Ej: Toyota Corolla 2024" required autofocus>
                        </div>
                        <div class="col-6 fila_form_f_b py-2">
                            <label class="label_form_f_b fs-6 p-1"><b>Categoría *</b></label>
                            <select class="input_form_f_b fs-6 p-1" name="category_id" required>
                                <option value="">-- Selecciona una categoría --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 fila_form_f_b py-2">
                            <label class="label_form_f_b fs-6 p-1"><b>Pasajeros *</b></label>
                            <input class="input_form_f_b fs-6 p-1" type="number" name="passengers" value="{{ old('passengers') }}" min="1" max="50" required>
                        </div>
                        <div class="col-6 fila_form_f_b py-2">
                            <label class="label_form_f_b fs-6 p-1"><b>Combustible (L) *</b></label>
                            <input class="input_form_f_b fs-6 p-1" type="number" name="fuel_capacity" value="{{ old('fuel_capacity') }}" step="0.1" min="0" required>
                        </div>
                    </div>
                </div>

                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start flex-wrap p-2">
                    <p class="text-dark fs-6 m-0"><b>Detalles del Vehículo</b></p>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-1">
                    <div class="col-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Marca</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" name="brand" value="{{ old('brand') }}" placeholder="Ej: Toyota, Honda, Ford">
                    </div>
                    <div class="col-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Modelo</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" name="model" value="{{ old('model') }}" placeholder="Ej: Corolla, Civic, Mustang">
                    </div>
                    <div class="col-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Año</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="number" name="year" value="{{ old('year') }}" min="1900" max="{{ date('Y') + 1 }}">
                    </div>
                    <div class="col-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Número de Placa</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" name="plate_number" value="{{ old('plate_number') }}" placeholder="ABC-1234">
                    </div>
                    <div class="col-4 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Transmisión *</b></label>
                        <select class="input_form_f_b fs-6 p-1" name="transmission" required>
                            <option value="automatic" {{ old('transmission') == 'automatic' ? 'selected' : '' }}>Automático</option>
                            <option value="manual" {{ old('transmission') == 'manual' ? 'selected' : '' }}>Manual</option>
                        </select>
                    </div>
                </div>

                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start flex-wrap p-2">
                    <p class="text-dark fs-6 m-0"><b>Estado del Vehículo</b></p>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-1">
                    <div class="col-12 col-sm-12 col-md-3 fila_form_f_b py-2">
                        <label class="label_form_f_b d-flex align-items-center justify-content-start fs-6 py-1 px-1">
                            <input class="check_box_form" type="checkbox" name="available" value="1" {{ old('available', true) ? 'checked' : '' }}>
                            <b class="mx-2">Disponible para rentar</b>
                        </label>
                    </div>
                    <div class="col-12 col-sm-12 col-md-3 fila_form_f_b py-2">
                        <label class="label_form_f_b d-flex align-items-center justify-content-start fs-6 py-1 px-1">
                            <input class="check_box_form" type="checkbox" name="active" value="1" {{ old('active', true) ? 'checked' : '' }}>
                            <b class="mx-2">Vehículo activo</b>
                        </label>
                    </div>
                </div>

                <div class="col-12 py-2 d-flex align-items-center justify-content-center">
                    <a href="{{ route('vehiculos.index') }}" class="boton_link_lg rounded">Cancelar</a>
                    <button type="submit" class="boton_link_lg rounded">Guardar</button>
                </div>

                </form>

                {{-- Errores --}}
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

<script src="{{ asset('js/formulario_renta.js') }}"></script>
@endsection