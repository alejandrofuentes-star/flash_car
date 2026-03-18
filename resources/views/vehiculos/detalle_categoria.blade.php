@extends('layout.layouts')

@section('title', 'Detalles Categoría - Flash Car')

@section('content')
 @include('layout.header')
<div class="bg-light">
    <div class="main_principal">
        <div class="space_principal">
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        <div class="container">
            <div class="col-12 d-flex align-items-center justify-content-start flex-wrap rounded cont_base my-2">
                <div class="col-12 d-flex align-items-start justify-content-center flex-column p-2 bg_gris_8">
                    <h1 class="fs-6 text_uppcase">Detalles de Categoría</h1>
                    <div class="col-4 col-sm-4 col-md-2 col-lg-1">
                        <a class="boton_forms b_sm rounded link_decoration_none display_flex_center_center" href="{{ route('vehiculos.index') }}">← Volver</a>
                    </div>
                </div>

                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start flex-wrap p-2">
                    <p class="text-dark fs-6 m-0"><b>Información General</b></p>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-1">
                    <div class="col-6 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Nombre</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $category->name }}" readonly>
                    </div>
                    <div class="col-6 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Estado</b></label>
                        <div>
                            @if($category->active)
                                <span class="cuenta_activa">Activa</span>
                            @else
                                <span class="cuenta_inactiva">Inactiva</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start flex-wrap p-2">
                    <p class="text-dark fs-6 m-0"><b>Precios y Garantía</b></p>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-2">
                    <div class="col-12 col-sm-12 col-md-3 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Precio por Día</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $category->formatted_price_per_day }}" readonly style="font-weight:600; color:var(--primary);">
                    </div>
                    <div class="col-12 col-sm-12 col-md-3 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Precio por Semana</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $category->formatted_price_per_week }}" readonly style="font-weight:600; color:var(--primary);">
                    </div>
                    <div class="col-12 col-sm-12 col-md-3 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Precio por Mes</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $category->formatted_price_per_month }}" readonly style="font-weight:600; color:var(--primary);">
                    </div>
                    <div class="col-12 col-sm-12 col-md-3 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Garantía</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $category->formatted_warranty }}" readonly style="font-weight:600; color:var(--warning);">
                    </div>
                </div>

                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start flex-wrap p-2">
                    <p class="text-dark fs-6 m-0"><b>Información de Registro</b></p>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-2">
                    <div class="col-12 col-sm-6 col-md-6 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Fecha de Registro</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $category->created_at->format('d/m/Y H:i:s') }}" readonly>
                    </div>
                    <div class="col-12 col-sm-6 col-md-6 fila_form_f_b py-2">
                        <label class="label_form_f_b fs-6 p-1"><b>Última Actualización</b></label>
                        <input class="input_form_f_b fs-6 p-1" type="text" value="{{ $category->updated_at->format('d/m/Y H:i:s') }}" readonly>
                    </div>
                </div>

                <div class="col-12 py-2 d-flex align-items-center justify-content-center">
                    @if(Auth::user()->hasAdminAccess())
                    <a class="boton_link_md rounded" href="{{ route('categorias.edit', $category->id) }}">Editar</a>
                    <form method="POST" action="{{ route('categorias.destroy', $category->id) }}" onsubmit="return confirm('¿Estás seguro de eliminar esta categoría?\n\nEsta acción no se puede deshacer.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="boton_link_lg bg_rojo_2 rounded">Eliminar</button>
                    </form>
                    @endif
                </div>

            </div>
        </div>
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        </div>
    </div>
</div>
@endsection