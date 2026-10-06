@extends('layout.layouts')

@section('title', 'Vehículos - Flash Car')

@section('content')
 @include('layout.header')
<div class="bg-light h_100_vh d-flex align-items-center justify-content-center">
    <div class="main_principal">
        <div class="space_principal">
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        <div class="col-12 d-flex align-items-start justify-content-start flex-wrap">

            {{-- ===== SECCIÓN CATEGORÍAS ===== --}}
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 d-flex align-items-center justify-content-center p-1">
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap rounded cont_base my-2">
                    <div class="col-12 d-flex align-items-start justify-content-center flex-column p-2 bg_gris_8">
                        <h1 class="fs-6 text_uppcase">Vehículos por categoría</h1>
                        @if(Auth::user()->hasAdminAccess())
                        <div class="col-12 d-flex align-items-center justify-content-start flex-wrap">
                            <div class="col-6 col-sm-6 col-md-3 col-lg-2 px-1">
                                <a class="boton_forms b_sm rounded link_decoration_none display_flex_center_center" href="{{ route('categoria.create_categoria') }}">+ Nueva Categoría</a>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="col-12">
                        <div class="col-12 over_max_height_70">
                            {{-- Encabezado dentro del contenedor con scroll: así comparte el mismo ancho que las filas --}}
                            <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start" style="position:sticky; top:0; z-index:2;">
                                <p class="col-3 col-sm-3 col-md-3 col-lg-3 border_right px-1 text-dark my-1"><b>Nombre</b></p>
                                <p class="col-1 col-sm-1 col-md-1 col-lg-1 border_right px-1 text-dark my-1"><b>$/día</b></p>
                                <p class="col-1 col-sm-1 col-md-1 col-lg-1 border_right px-1 text-dark my-1"><b>$/semana</b></p>
                                <p class="col-1 col-sm-1 col-md-1 col-lg-1 border_right px-1 text-dark my-1"><b>$/mes</b></p>
                                <p class="col-1 col-sm-1 col-md-1 col-lg-1 border_right px-1 text-dark my-1 text-center"><b>Autos</b></p>
                                <p class="col-1 col-sm-1 col-md-1 col-lg-1 border_right px-1 text-dark my-1 text-center"><b>Estado</b></p>
                                <p class="col-4 col-sm-4 col-md-4 col-lg-4 px-1 text-dark my-1"><b></b></p>
                            </div>
                            @forelse($categories as $category)
                            <div class="col-12 d-flex align-items-center justify-content-start flex-wrap border_gris_2_buttom">
                                <p class="col-3 col-sm-3 col-md-3 col-lg-3 border_right_dato px-1 my-1">{{ $category->name }}</p>
                                <p class="col-1 col-sm-1 col-md-1 col-lg-1 border_right_dato px-1 my-1">{{ $category->formatted_price_per_day }}</p>
                                <p class="col-1 col-sm-1 col-md-1 col-lg-1 border_right_dato px-1 my-1">{{ $category->formatted_price_per_week }}</p>
                                <p class="col-1 col-sm-1 col-md-1 col-lg-1 border_right_dato px-1 my-1">{{ $category->formatted_price_per_month }}</p>
                                <p class="col-1 col-sm-1 col-md-1 col-lg-1 border_right_dato px-1 my-1 text-center">{{ $category->vehicles_count }}</p>
                                <div class="col-1 col-sm-1 col-md-1 col-lg-1 display_flex_center_center border_right_dato px-1 my-1">
                                    @if($category->active)
                                        <span class="cuenta_activa">Activa</span>
                                    @else
                                        <span class="cuenta_inactiva">Inactiva</span>
                                    @endif
                                </div>
                                <div class="col-4 col-sm-4 col-md-4 col-lg-4 d-flex align-items-center justify-content-center flex-wrap">

                                    {{-- Ver: todos pueden --}}
                                    <a href="{{ route('categorias.show', $category->id) }}" class="boton_link_sm rounded" title="Ver"><i class="bi bi-eye-fill"></i></a>

                                    {{-- Editar: solo admin y super_admin --}}
                                    @if(Auth::user()->hasAdminAccess())
                                        <a href="{{ route('categorias.edit', $category->id) }}" class="boton_link_sm rounded" title="Editar"><i class="bi bi-pencil-fill"></i></a>
                                    @endif

                                    {{-- Eliminar: solo admin y super_admin --}}
                                    @if(Auth::user()->hasAdminAccess())
                                        <form method="POST" action="{{ route('categorias.destroy', $category->id) }}" style="display:inline;" onsubmit="return confirm('¿Estás seguro de eliminar la categoría {{ $category->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="boton_link_sm bg_rojo_2 rounded" title="Eliminar"><i class="bi bi-trash-fill"></i></button>
                                        </form>
                                    @endif

                                    {{-- Ver autos de la categoría: todos pueden --}}
                                    <a href="{{ route('vehiculos.categoria', $category->id) }}" class="boton_link_xl rounded"><i class="bi bi-car-front-fill me-1"></i>Ver autos</a>

                                </div>
                            </div>
                            @empty
                            <div class="col-12 display_flex_center_center">
                                <p class="fs-6 py-4 m-0 text-center text-muted">No hay categorías registradas.</p>
                            </div>
                            @endforelse

                            {{-- Vehículos cuya categoría fue eliminada (category_id = null) --}}
                            @if($sinCategoria > 0)
                            <div class="col-12 d-flex align-items-center justify-content-start flex-wrap border_gris_2_buttom">
                                <p class="col-3 col-sm-3 col-md-3 col-lg-3 border_right_dato px-1 my-1"><span class="sin_categoria_etiqueta">Sin categoría</span></p>
                                <p class="col-1 col-sm-1 col-md-1 col-lg-1 border_right_dato px-1 my-1">—</p>
                                <p class="col-1 col-sm-1 col-md-1 col-lg-1 border_right_dato px-1 my-1">—</p>
                                <p class="col-1 col-sm-1 col-md-1 col-lg-1 border_right_dato px-1 my-1">—</p>
                                <p class="col-1 col-sm-1 col-md-1 col-lg-1 border_right_dato px-1 my-1 text-center">{{ $sinCategoria }}</p>
                                <p class="col-1 col-sm-1 col-md-1 col-lg-1 border_right_dato px-1 my-1 text-center">—</p>
                                <div class="col-4 col-sm-4 col-md-4 col-lg-4 d-flex align-items-center justify-content-center flex-wrap">
                                    <a href="{{ route('vehiculos.categoria', 'sin-categoria') }}" class="boton_link_xl rounded"><i class="bi bi-car-front-fill me-1"></i>Ver autos</a>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mensajes de éxito --}}
            @if(session('success'))
                <div class="messenger_alert">
                    <div class="dialog_alert messenger py-2 px-4 rounded">
                        <div class="fs-6 text-white"><b>{{ session('success') }}</b></div>
                    </div>
                </div>
            @endif
        </div>
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        </div>
    </div>
</div>
@endsection