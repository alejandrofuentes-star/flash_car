@extends('layout.layouts')

@section('title', 'Usuarios - Flash Car')

@section('content')
 @include('layout.header')
<div class="bg-light h_100_vh d-flex align-items-center justify-content-center">
    <div class="main_principal">
        <div class="space_principal">
            <div class="container">    
                    <div class="col-12 d-flex align-items-center justify-content-start flex-wrap rounded cont_base">
                        <div class="col-12 d-flex align-items-start justify-content-center flex-column p-2 bg_gris_9">
                            <h1 class="fs-6 text_uppcase">Usuario</h1>
                            @if(Auth::user()->isAdmin())
                            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                <a class="boton_forms b_sm rounded link_decoration_none display_flex_center_center" href="{{ route('users.create') }}">+ Nuevo Usuario</a>
                            </div>
                             @endif
                             @if(Auth::user()->isSuperAdmin())
                            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                <a class="boton_forms b_sm rounded link_decoration_none display_flex_center_center" href="{{ route('users.create') }}">+ Nuevo Usuario</a>
                            </div>
                             @endif
                        </div>
                        <div class="tabla_scroll_x">
                        <div class="tabla_scroll_x_inner">
                        <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start flex-wrap">
                            <p class="col-1 px-1 text-dark my-1"></p>
                            <p class="col-2 border_left border_right px-1 text-dark my-1"><b>Usuario</b></p>
                            <p class="{{ Auth::user()->isSuperAdmin() ? 'col-3' : 'col-5' }} border_right px-1 text-dark my-1"><b>Email</b></p>
                            <p class="col-2 border_right px-1 text-dark my-1"><b>Rol</b></p>
                            @if(Auth::user()->isSuperAdmin())
                            <p class="col-2 border_right px-1 text-dark my-1"><b>Último acceso</b></p>
                            @endif
                        </div>
                        @if($users->count() > 0)
                        @foreach($users as $user)
                            <div class="col-12 d-flex align-items-center justify-content-start flex-wrap border_gris_2_buttom">
                                <div class="col-1 py-1 d-flex align-items-center justify-content-center">
                                    <div class="circulo_perfil display_flex_center_center">
                                        <p class="m-0 fs-4 text_rojo_2">{{ strtoupper(substr($user->name, 0, 1)) }}</p>
                                    </div>
                                </div>
                                <p class="col-2 border_left_dato border_right_dato px-1 my-1">{{ $user->name }}</p>
                                <p class="{{ Auth::user()->isSuperAdmin() ? 'col-3' : 'col-5' }} border_right_dato px-1 my-1 text_break">{{ $user->email }}</p>
                                <p class="col-2 border_right_dato px-1 my-1">
                                    @if($user->isSuperAdmin())
                                        <span class="fs-6">Super Admin</span>
                                    @elseif($user->isAdmin())
                                        <span class="fs-6">Admin</span>
                                    @else
                                        <span class="fs-6">Usuario</span>
                                    @endif
                                </p>
                                @if(Auth::user()->isSuperAdmin())
                                <p class="col-2 border_right_dato px-1 my-1">
                                    @if($user->last_login_at)
                                        <span class="fs-6">{{ $user->last_login_at->timezone('America/Mexico_City')->format('d/m/Y H:i') }}</span>
                                    @else
                                        <span class="fs-6 text-muted">Nunca</span>
                                    @endif
                                </p>
                                @endif
                                <div class="col-2 d-flex align-items-center justify-content-end px-1 my-1">
                                    <a href="{{ route('users.detalles', $user->id) }}" class="boton_link_sm b_sm rounded link_decoration_none display_flex_center_center" title="Ver"><i class="bi bi-eye-fill"></i></a>

                                    @if(Auth::user()->isSuperAdmin())
                                    <a href="{{ route('users.edit', $user->id) }}" class="boton_link_sm b_sm rounded link_decoration_none display_flex_center_center" title="Editar"><i class="bi bi-pencil-fill"></i></a>
                                    @elseif(Auth::user()->isAdmin())
                                        @if(!$user->isSuperAdmin())
                                        <a href="{{ route('users.edit', $user->id) }}" class="boton_link_sm b_sm rounded link_decoration_none display_flex_center_center" title="Editar"><i class="bi bi-pencil-fill"></i></a>
                                        @endif
                                    @endif

                                    @if(Auth::user()->isSuperAdmin())
                                    {{-- Super Admin puede eliminar a todos excepto a sí mismo --}}
                                        @if($user->id !== Auth::user()->id)
                                        <form method="POST" action="{{ route('users.destroy', $user->id) }}" style="display: inline;" onsubmit="return confirm('¿Estás seguro de eliminar al usuario {{ $user->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="boton_link_sm b_sm rounded border-0 bg-danger text-white" title="Eliminar">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                        @endif
                                    @elseif(Auth::user()->isAdmin())
                                    {{-- Admin solo puede eliminar usuarios normales --}}
                                            @if($user->isUser())
                                            <form method="POST" action="{{ route('users.destroy', $user->id) }}" style="display: inline;" onsubmit="return confirm('¿Estás seguro de eliminar al usuario {{ $user->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="boton_link_sm b_sm rounded border-0 bg-danger text-white" title="Eliminar">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        @else
                            <div style="text-align: center; padding: 40px; color: var(--text-light);">
                                <p>No hay usuarios registrados</p>
                            </div>
                        @endif
                        </div>
                        </div>
                    </div>
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
        </div>
    </div>
</div>
@endsection