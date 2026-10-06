@extends('layout.layouts')

@section('title', 'Vehículos ' . ($category->name ?? 'sin categoría') . ' - Flash Car')

@section('content')
 @include('layout.header')
<div class="bg-light h_100_vh d-flex align-items-center justify-content-center">
    <div class="main_principal">
        <div class="space_principal">
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        <div class="col-12 d-flex align-items-start justify-content-start flex-wrap">

            {{-- ===== VEHÍCULOS DE LA CATEGORÍA ===== --}}
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 d-flex align-items-center justify-content-center p-1">
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap rounded cont_base my-2">
                    <div class="col-12 d-flex align-items-start justify-content-center flex-column p-2 bg_gris_8">
                        <h1 class="fs-6 text_uppcase">
                            Vehículos —
                            @if($category)
                                {{ $category->name }}
                            @else
                                Sin categoría
                            @endif
                            <span class="text-muted">({{ $vehicles->count() }})</span>
                        </h1>
                        <div class="col-12 d-flex align-items-center justify-content-start flex-wrap">
                            <div class="col-6 col-sm-6 col-md-3 col-lg-2 px-1">
                                <a class="boton_forms b_sm rounded link_decoration_none display_flex_center_center" href="{{ route('vehiculos.index') }}"><i class="bi bi-arrow-left me-1"></i>Categorías</a>
                            </div>
                            @if(Auth::user()->hasAdminAccess() && $category)
                            <div class="col-6 col-sm-6 col-md-3 col-lg-2 px-1">
                                <a class="boton_forms b_sm rounded link_decoration_none display_flex_center_center" href="{{ route('vehiculos.create', ['category_id' => $category->id]) }}">+ Nuevo Vehículo</a>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-12 over_max_height_70 d-flex align-items-center justify-content-center flex-wrap">
                        @forelse($vehicles as $vehicle)
                        <div class="col-12 col-sm-12 col-md-6 col-lg-4 d-flex align-items-stretch justify-content-center p-1">
                            <div class="col-12 d-flex align-items-center justify-content-start rounded cont_base overflow-hidden">

                                {{-- Imagen: lado izquierdo --}}
                                <div class="d-flex align-items-center justify-content-center bg-white p-2" style="width:35%; flex-shrink:0;">
                                    @if($vehicle->image_path)
                                        <img src="{{ Storage::url($vehicle->image_path) }}" alt="{{ $vehicle->name }}" class="rounded" style="width:100%; object-fit:contain;">
                                    @else
                                        <img src="{{ asset('./img/sin_url_auto.png') }}" alt="Sin imagen" class="rounded" style="width:100%; object-fit:contain;">
                                    @endif
                                </div>

                                {{-- Nombre, estados y botones: lado derecho --}}
                                <div class="d-flex align-items-start justify-content-center flex-column p-2" style="width:65%;">
                                    <p class="fs-6 m-0 mb-1">
                                        <b>{{ $vehicle->name }}</b>
                                        @if($vehicle->plate_number)
                                            <span class="text-muted ms-1" style="font-size:0.8rem; white-space:nowrap;"><i class="bi bi-credit-card-2-front me-1"></i>{{ $vehicle->plate_number }}</span>
                                        @endif
                                    </p>
                                    <div class="col-12 d-flex align-items-center justify-content-start flex-wrap">
                                        @if($vehicle->disponible_ahora)
                                            <span class="cuenta_activa">Disponible</span>
                                        @elseif($vehicle->ocupado_hasta)
                                            {{-- Móvil: fecha debajo de "Disponible"; desde tablet: ambas en la misma fila --}}
                                            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center flex-nowrap">
                                                <span class="cuenta_inactiva" style="white-space:nowrap; padding:1px 8px;">Disponible</span>
                                                <span class="cuenta_inactiva" style="white-space:nowrap; padding:1px 8px;">Hasta {{ $vehicle->ocupado_hasta->format('d/m/Y H:i') }}</span>
                                            </div>
                                        @else
                                            <span class="cuenta_inactiva">Disponible</span>
                                        @endif
                                    </div>
                                    <div class="col-12 d-flex align-items-center justify-content-start flex-wrap pt-1">
                                        @if($vehicle->active)
                                            <span class="cuenta_activa">Activo</span>
                                        @else
                                            <span class="cuenta_inactiva">Inactivo</span>
                                            @if($vehicle->motivo_inactivo)
                                                <span class="cuenta_inactiva" style="font-size:0.7rem; opacity:0.85;">{{ $vehicle->motivo_inactivo }}</span>
                                            @endif
                                        @endif
                                    </div>
                                    <div class="col-12 d-flex align-items-center justify-content-start flex-wrap pt-2">

                                        {{-- Ver: todos pueden --}}
                                        <a href="{{ route('vehicles.show', $vehicle->id) }}" class="boton_link_sm rounded">Ver</a>

                                        {{-- Editar: solo admin y super_admin --}}
                                        @if(Auth::user()->hasAdminAccess())
                                            <a href="{{ route('vehiculos.edit', $vehicle->id) }}" class="boton_link_md rounded">Editar</a>
                                        @endif

                                        {{-- Eliminar: solo admin y super_admin --}}
                                        @if(Auth::user()->hasAdminAccess())
                                            <form method="POST" action="{{ route('vehiculos.destroy', $vehicle->id) }}" style="display:inline;" onsubmit="return confirm('¿Estás seguro de eliminar el vehículo {{ $vehicle->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="boton_link_lg bg_rojo_2 rounded">Eliminar</button>
                                            </form>
                                        @endif
                                    </div>

                                    {{-- Mostrar en catálogo público: admin lo cambia con el checkbox, los demás solo lo ven --}}
                                    <div class="col-12 d-flex align-items-center justify-content-start pt-2">
                                        @if(Auth::user()->hasAdminAccess())
                                            <form method="POST" action="{{ route('vehiculos.catalogo', $vehicle->id) }}" class="m-0">
                                                @csrf
                                                @method('PATCH')
                                                {{-- Deshabilitado si el auto no está disponible (inactivo, sin "disponible" o rentado ahora) --}}
                                                <label class="d-flex align-items-center m-0" style="font-size:0.85rem; gap:6px; {{ $vehicle->disponible_ahora ? 'cursor:pointer;' : 'cursor:not-allowed; opacity:0.5;' }}"
                                                       @unless($vehicle->disponible_ahora) title="No disponible: no se puede cambiar" @endunless>
                                                    <input type="checkbox" class="check_box_form" onchange="this.form.submit()" {{ $vehicle->mostrar_en_catalogo ? 'checked' : '' }} {{ $vehicle->disponible_ahora ? '' : 'disabled' }}>
                                                    Mostrar en catálogo
                                                </label>
                                            </form>
                                        @elseif($vehicle->mostrar_en_catalogo)
                                            <span style="font-size:0.85rem;"><i class="bi bi-check2-square me-1"></i>En catálogo</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                            <div class="col-12 display_flex_center_center">
                                <p class="fs-6 text-center m-0 py-4 text-muted">No hay vehículos registrados en esta categoría.</p>
                            </div>
                        @endforelse
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
            @if(session('error'))
                <div class="messenger_alert">
                    <div class="dialog_alert danger py-2 px-4 rounded">
                        <div class="fs-6 text-white"><b>{{ session('error') }}</b></div>
                    </div>
                </div>
            @endif
        </div>
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        </div>
    </div>
</div>
@endsection
