@extends('layout.layouts')

@section('title', 'Migraciones - Flash Car')

@section('content')
 @include('layout.header')
<div class="bg-light">
    <div class="main_principal">
        <div class="space_principal">
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        <div class="container">

            <div class="col-12 d-flex align-items-center justify-content-start flex-wrap rounded cont_base my-2">
                <div class="col-12 d-flex align-items-start justify-content-center flex-column p-2 bg_gris_8">
                    <h1 class="fs-6 text_uppcase">Sistema - Migraciones de Base de Datos</h1>
                    <a class="boton_forms b_sm rounded link_decoration_none display_flex_center_center mt-1" href="{{ route('system.cache') }}">← Volver a Sistema</a>
                </div>

                {{-- ACCIONES --}}
                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start p-2">
                    <p class="text-dark fs-6 m-0"><b>Acciones</b></p>
                </div>
                <div class="col-12 d-flex align-items-start justify-content-start flex-wrap p-2">

                    {{-- Ejecutar pendientes --}}
                    <div class="col-12 col-md-5 border rounded p-3 bg-white shadow-sm me-2 mb-2">
                        <p class="fs-6 mb-1"><b><i class="bi bi-play-circle-fill"></i> Ejecutar Migraciones Pendientes</b></p>
                        <p class="text-muted mb-2" style="font-size:0.85rem;">
                            Ejecuta <code>php artisan migrate --force</code> con todas las migraciones que aún no se han corrido.
                            @if($pendingCount > 0)
                                <span class="badge bg-warning text-dark ms-1">{{ $pendingCount }} pendiente(s)</span>
                            @else
                                <span class="badge bg-success ms-1">Todo al día</span>
                            @endif
                        </p>
                        <form method="POST" action="{{ route('system.migrations.run') }}"
                              onsubmit="return confirm('¿Ejecutar todas las migraciones pendientes? Esta acción modifica la base de datos.')">
                            @csrf
                            <button type="submit" class="boton_link_xxl rounded" {{ $pendingCount === 0 ? 'disabled' : '' }}
                                style="width:auto;padding:0 14px;{{ $pendingCount === 0 ? 'opacity:0.5;cursor:not-allowed;' : '' }}">
                                Ejecutar Pendientes
                            </button>
                        </form>
                    </div>

                    {{-- Subir archivo --}}
                    <div class="col-12 col-md-5 border rounded p-3 bg-white shadow-sm mb-2">
                        <p class="fs-6 mb-1"><b><i class="bi bi-upload"></i> Subir Migración</b></p>
                        <p class="text-muted mb-2" style="font-size:0.85rem;">
                            Sube un archivo <code>.php</code> con el formato <code>YYYY_MM_DD_HHMMSS_nombre.php</code>.
                            Después de subirlo, usa el botón de ejecutar.
                        </p>
                        <form method="POST" action="{{ route('system.migrations.upload') }}" enctype="multipart/form-data"
                              class="d-flex align-items-center flex-wrap">
                            @csrf
                            <div class="fila_form_f_b col-12 mb-2">
                                <input class="input_form_f_b fs-6 p-1" type="file" name="migration_file" accept=".php" required>
                            </div>
                            <button type="submit" class="boton_link_xxl rounded" style="width:auto;padding:0 14px;">Subir Archivo</button>
                        </form>
                    </div>

                </div>

                {{-- LISTA DE MIGRACIONES --}}
                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start p-2">
                    <p class="text-dark fs-6 m-0"><b>Estado de Migraciones</b></p>
                </div>
                <div class="col-12 p-2">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered bg-white" style="font-size:0.85rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-3 py-2">Migración</th>
                                    <th class="px-3 py-2 text-center" style="width:130px;">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($migrations as $migration)
                                <tr>
                                    <td class="px-3 py-2 font-monospace">{{ $migration['name'] }}</td>
                                    <td class="px-3 py-2 text-center">
                                        @if($migration['status'] === 'ran')
                                            <span class="badge bg-success">Ejecutada</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Pendiente</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Mensajes --}}
                @if(session('success'))
                    <div class="messenger_alert">
                        <div class="dialog_alert messenger py-2 px-4 rounded">
                            <div class="fs-6 text-white">{!! session('success') !!}</div>
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
        </div>
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        </div>
    </div>
</div>
@endsection
