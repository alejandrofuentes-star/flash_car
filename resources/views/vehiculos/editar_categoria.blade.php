@extends('layout.layouts')

@section('title', 'Editar Categoría - Flash Car')

@section('content')
 @include('layout.header')
<div class="bg-light">
    <div class="main_principal">
        <div class="space_principal">
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        <div class="container">
            <div class="col-12 my-2 d-flex align-items-center justify-content-start flex-wrap rounded cont_base">
                <div class="col-12 d-flex align-items-start justify-content-center flex-column p-2 bg_gris_8">
                    <h1 class="fs-6 text_uppcase">Editar Categoría: {{ $category->name }}</h1>
                    <div class="col-4 col-sm-4 col-md-2 col-lg-1">
                        <a class="boton_forms b_sm rounded link_decoration_none display_flex_center_center" href="{{ route('vehiculos.index') }}">← Volver</a>
                    </div>
                </div>

                <form class="col-12 d-flex align-items-center justify-content-start flex-wrap" method="POST" action="{{ route('categorias.update', $category->id) }}">
                @csrf
                @method('PUT')

                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start flex-wrap p-2">
                    <p class="text-dark fs-6 m-0"><b>Información Básica</b></p>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-1">
                    <div class="col-12 col-sm-12 col-md-6 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Nombre de la Categoría *</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" name="name" value="{{ old('name', $category->name) }}" placeholder="Ej: Sedán, SUV, Camioneta" required autofocus>
                    </div>
                </div>

                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start flex-wrap p-2">
                    <p class="text-dark fs-6 m-0"><b>Precios y Garantía</b></p>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-1">
                    <div class="col-12 col-sm-12 col-md-6 col-lg-3 fila_form_f_b py-2 ">
                        <label class="label_form_f_b fs-6 p-1"><b>Precio por Día *</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="number" name="price_per_day" value="{{ old('price_per_day', $category->price_per_day) }}" step="0.01" min="0" required>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 col-lg-3 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Precio por Semana *</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="number" name="price_per_week" value="{{ old('price_per_week', $category->price_per_week) }}" step="0.01" min="0" required>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 col-lg-3 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Precio por Mes *</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="number" name="price_per_month" value="{{ old('price_per_month', $category->price_per_month) }}" step="0.01" min="0" required>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 col-lg-3 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Garantía *</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="number" name="warranty" value="{{ old('warranty', $category->warranty) }}" step="0.01" min="0" required>
                    </div>
                </div>

                <div class="col-12 bg_danger d-flex align-items-center justify-content-start flex-wrap p-2">
                    <p class="text-dark fs-6 m-0"><b>Información de Registro</b></p>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-1">
                    <div class="col-12 col-sm-12 col-md-6 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Fecha de Creación</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $category->created_at->format('d/m/Y H:i:s') }}" readonly>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Última Actualización</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $category->updated_at->format('d/m/Y H:i:s') }}" readonly>
                    </div>
                </div>

                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start flex-wrap p-2">
                    <p class="text-dark fs-6 m-0"><b>Configuración</b></p>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-1">
                    <div class="col-12 col-sm-12 col-md-3 fila_form_f_b py-2">
                        <label class="label_form_f_b d-flex align-items-center justify-content-start fs-6 py-1 px-1">
                            <input class="check_box_form" type="checkbox" name="active" value="1" {{ old('active', $category->active) ? 'checked' : '' }}>
                            <b class="mx-2">Categoría activa</b>
                        </label>
                    </div>
                </div>

                <div class="col-12 py-2 d-flex align-items-center justify-content-center">
                    <a href="{{ route('vehiculos.index') }}" class="boton_link_lg rounded">Cancelar</a>
                    <button type="submit" class="boton_link_lg rounded">Guardar</button>
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
@endsection