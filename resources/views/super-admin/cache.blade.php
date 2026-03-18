@extends('layout.layouts')

@section('title', 'Sistema - Flash Car')

@section('content')
 @include('layout.header')
<div class="bg-light">
    <div class="main_principal">
        <div class="space_principal">
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        <div class="container">

            <div class="col-12 d-flex align-items-center justify-content-start flex-wrap rounded cont_base my-2">
                <div class="col-12 d-flex align-items-start justify-content-center flex-column p-2 bg_gris_8">
                    <h1 class="fs-6 text_uppcase">Sistema - Limpieza de Caché</h1>
                </div>

                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start p-2">
                    <p class="text-dark fs-6 m-0"><b>Acciones disponibles</b></p>
                </div>

                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap p-2 gap-2">

                    <div class="col-12 col-md-5 border rounded p-3 bg-white shadow-sm">
                        <p class="fs-6 mb-1"><b>🧹 Limpiar Todo</b></p>
                        <p class="text-muted mb-2" style="font-size:0.85rem;">Ejecuta optimize:clear — limpia caché, config, rutas y vistas.</p>
                        <form method="POST" action="{{ route('system.clearAll') }}">
                            @csrf
                            <button type="submit" class="boton_link_xxl rounded">Limpiar Todo</button>
                        </form>
                    </div>

                    <div class="col-12 col-md-5 border rounded p-3 bg-white shadow-sm">
                        <p class="fs-6 mb-1"><b>🗑️ Caché de Aplicación</b></p>
                        <p class="text-muted mb-2" style="font-size:0.85rem;">Ejecuta cache:clear</p>
                        <form method="POST" action="{{ route('system.clearCache') }}">
                            @csrf
                            <button type="submit" class="boton_link_xxl rounded">Limpiar Caché</button>
                        </form>
                    </div>

                    <div class="col-12 col-md-5 border rounded p-3 bg-white shadow-sm">
                        <p class="fs-6 mb-1"><b>⚙️ Caché de Configuración</b></p>
                        <p class="text-muted mb-2" style="font-size:0.85rem;">Ejecuta config:clear</p>
                        <form method="POST" action="{{ route('system.clearConfig') }}">
                            @csrf
                            <button type="submit" class="boton_link_xxl rounded">Limpiar Config</button>
                        </form>
                    </div>

                    <div class="col-12 col-md-5 border rounded p-3 bg-white shadow-sm">
                        <p class="fs-6 mb-1"><b>🛣️ Caché de Rutas</b></p>
                        <p class="text-muted mb-2" style="font-size:0.85rem;">Ejecuta route:clear</p>
                        <form method="POST" action="{{ route('system.clearRoutes') }}">
                            @csrf
                            <button type="submit" class="boton_link_xxl rounded">Limpiar Rutas</button>
                        </form>
                    </div>

                    <div class="col-12 col-md-5 border rounded p-3 bg-white shadow-sm">
                        <p class="fs-6 mb-1"><b>👁️ Caché de Vistas</b></p>
                        <p class="text-muted mb-2" style="font-size:0.85rem;">Ejecuta view:clear</p>
                        <form method="POST" action="{{ route('system.clearViews') }}">
                            @csrf
                            <button type="submit" class="boton_link_xxl rounded">Limpiar Vistas</button>
                        </form>
                    </div>

                </div>
                @if(session('success'))
                    <div class="messenger_alert">
                        <div class="dialog_alert messenger py-2 px-4 rounded">
                            <div class="fs-6 text-white"><b>{{ session('success') }}</b></div>
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