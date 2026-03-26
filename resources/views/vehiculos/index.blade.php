@extends('layout.layouts')

@section('title', 'Vehículos - Flash Car')

@section('content')
 @include('layout.header')
<div class="bg-light h_100_vh d-flex align-items-center justify-content-center">
    <div class="main_principal">
        <div class="space_principal">
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        <div class="col-12 d-flex align-items-start justify-content-start flex-wrap">

            {{-- ===== SECCIÓN VEHÍCULOS ===== --}}
            <div class="col-12 col-sm-12 col-md-12 col-lg-8 d-flex align-items-center justify-content-center p-1">
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap rounded cont_base my-2">
                    <div class="col-12 d-flex align-items-start justify-content-center flex-column p-2 bg_gris_8">
                        <h1 class="fs-6 text_uppcase">Vehículos</h1>
                        @if(Auth::user()->hasAdminAccess())
                        <div class="col-8 col-sm-8 col-md-6 col-lg-3">
                            <a class="boton_forms b_sm rounded link_decoration_none display_flex_center_center" href="{{ route('vehiculos.create') }}">+ Nuevo Vehículo</a>
                        </div>
                        @endif
                    </div>
                    <div class="col-12 over_max_height_70 d-flex align-items-center justify-content-center flex-wrap">
                        @forelse($vehicles as $vehicle)
                        <div class="col-6 col-sm-6 col-md-4 col-lg-4 d-flex align-items-center justify-content-center p-1">
                            <div class="col-12 d-flex align-items-center justify-content-center flex-column rounded cont_base">
                                @if($vehicle->image_path)
                                    <img src="{{ Storage::url($vehicle->image_path) }}" alt="{{ $vehicle->name }}" width="80%" class="rounded">
                                @else
                                    <img src="{{ asset('./img/sin_url_auto.png') }}" alt="Sin imagen" width="80%" class="rounded">
                                @endif
                                <p class="fs-6 m-0"><b>{{ $vehicle->name }}</b></p>
                                <div class="col-12 d-flex align-items-center justify-content-center flex-wrap">
                                    @if($vehicle->category)
                                        <span class="categoria_etiqueta">{{ $vehicle->category->name }}</span>
                                    @else
                                        <span class="sin_categoria_etiqueta">Sin categoría</span>
                                    @endif
                                    @if($vehicle->available)
                                        <span class="cuenta_activa">Disponible</span>
                                    @else
                                        <span class="cuenta_inactiva">No disponible</span>
                                    @endif
                                    @if($vehicle->active)
                                        <span class="cuenta_activa">Activo</span>
                                    @else
                                        <span class="cuenta_inactiva">Inactivo</span>
                                    @endif
                                </div>
                                <div class="col-12 d-flex align-items-center justify-content-center py-2">

                                    {{-- Ver: todos pueden --}}
                                    <a href="{{ route('vehicles.show', $vehicle->id) }}" class="boton_link_sm rounded">Ver</a>

                                    {{-- Editar: solo admin y super_admin --}}
                                    @if(Auth::user()->hasAdminAccess())
                                        <a href="{{ route('vehiculos.edit', $vehicle->id) }}" class="boton_link_md rounded">Editar</a>
                                    @endif

                                    {{-- Eliminar: solo admin y super_admin --}}
                                    @if(Auth::user()->hasAdminAccess())
                                        <form method="POST" action="{{ route('vehiculos.destroy', $vehicle->id) }}" style="display:inline;" onsubmit="return confirm('¿Estás seguro de eliminar la categoría {{ $vehicle->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="boton_link_lg bg_rojo_2 rounded">Eliminar</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                            <div class="col-12 display_flex_center_center">
                                <p colspan="6" class="fs-6 text-center m-0 py-4 text-muted">No hay vehículos registrados.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- ===== SECCIÓN CATEGORÍAS ===== --}}
            <div class="col-12 col-sm-12 col-md-12 col-lg-4 d-flex align-items-center justify-content-center p-1">
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap rounded cont_base my-2">
                    <div class="col-12 d-flex align-items-start justify-content-center flex-column p-2 bg_gris_8">
                        <h1 class="fs-6 text_uppcase">Categorías</h1>
                        @if(Auth::user()->hasAdminAccess())
                        <div class="col-8 col-sm-8 col-md-6 col-lg-5">
                            <a class="boton_forms b_sm rounded link_decoration_none display_flex_center_center" href="{{ route('categoria.create_categoria') }}">+ Nueva Categoría</a>
                        </div>
                        @endif
                    </div>

                    <div class="col-12">
                        <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start">
                            <p class="col-3 col-sm-3 col-md-3 col-lg-3 border_right px-1 text-dark my-1"><b>Nombre</b></p>
                            <p class="col-3 col-sm-3 col-md-3 col-lg-3 border_right px-1 text-dark my-1"><b>Estado</b></p>
                            <p class="col-6 col-sm-6 col-md-6 col-lg-6 border_right px-1 text-dark my-1"><b></b></p>
                        </div>
                        <div class="col-12 over_max_height">
                            @forelse($categories as $category)
                            <div class="col-12 d-flex align-items-center justify-content-start flex-wrap border_gris_2_buttom">
                                <p class="col-3 col-sm-3 col-md-3 col-lg-3 border_right_dato px-1 my-1">{{ $category->name }}</p>
                                <div class="col-3 col-sm-3 col-md-3 col-lg-3 display_flex_center_center border_right_dato px-1 my-1">
                                    @if($category->active)
                                        <span class="cuenta_activa">Activa</span>
                                    @else
                                        <span class="cuenta_inactiva">Inactiva</span>
                                    @endif
                                </div>
                                <div class="col-6 col-sm-6 col-md-6 col-lg-6 d-flex align-items-center justify-content-center">

                                    {{-- Ver: todos pueden --}}
                                    <a href="{{ route('categorias.show', $category->id) }}" class="boton_link_sm rounded">Ver</a>

                                    {{-- Editar: solo admin y super_admin --}}
                                    @if(Auth::user()->hasAdminAccess())
                                        <a href="{{ route('categorias.edit', $category->id) }}" class="boton_link_md rounded">Editar</a>
                                    @endif

                                    {{-- Eliminar: solo admin y super_admin --}}
                                    @if(Auth::user()->hasAdminAccess())
                                        <form method="POST" action="{{ route('categorias.destroy', $category->id) }}" style="display:inline;" onsubmit="return confirm('¿Estás seguro de eliminar la categoría {{ $category->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="boton_link_lg bg_rojo_2 rounded">Eliminar</button>
                                        </form>
                                    @endif

                                </div>
                            </div>
                            @empty
                            <div class="col-12 display_flex_center_center">
                                <p colspan="3" class="fs-6 py-4 m-0 text-center text-muted">No hay categorías registradas.</p>
                            </div>
                            @endforelse
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