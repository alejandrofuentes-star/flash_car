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
                        <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start">
                            <p class="col-2 col-sm-2 col-md-2 col-lg-1 px-1 text-dark my-1"></p>
                            <p class="col-3 col-sm-3 col-md-3 col-lg-2 border_left border_right px-1 text-dark my-1"><b>Usuario</b></p>
                            <p class="col-5 col-sm-5 col-md-5 col-lg-4 border_right px-1 text-dark my-1"><b>Email</b></p>
                            <p class="col-2 col-sm-2 col-md-2 col-lg-2 border_right px-1 text-dark my-1"><b>Rol</b></p>
                        </div>
                        @if($users->count() > 0)
                        @foreach($users as $user)
                            <div class="col-12 d-flex align-items-center justify-content-start flex-wrap border_gris_2_buttom">
                                <div class="col-2 col-sm-2 col-md-2 col-lg-1 py-1 d-flex align-items-center justify-content-center">
                                    <div class="circulo_perfil display_flex_center_center">
                                        <p class="m-0 fs-4 text_rojo_2">{{ strtoupper(substr($user->name, 0, 1)) }}</p>
                                    </div>
                                </div>
                                <p class="col-3 col-sm-3 col-md-3 col-lg-2 border_left_dato border_right_dato px-1 my-1">{{ $user->name }}</p>
                                <p class="col-5 col-sm-5 col-md-5 col-lg-4 border_right_dato px-1 my-1 text_break">{{ $user->email }}</p>
                                <p class="col-2 col-sm-2 col-md-2 col-lg-2 border_right_dato px-1 my-1">
                                    @if($user->isSuperAdmin())
                                        <span class="fs-6">Super Admin</span>
                                    @elseif($user->isAdmin())
                                        <span class="fs-6">Admin</span>
                                    @else
                                        <span class="fs-6">Usuario</span>
                                    @endif
                                </p>
                                <div class="col-12 col-sm-12 col-md-12 col-lg-3 d-flex align-items-center justify-content-start px-1 my-1">
                                    <a href="{{ route('users.detalles', $user->id) }}" class="boton_link_sm rounded">Ver</a>
                                    
                                    @if(Auth::user()->isSuperAdmin())
                                    <a href="{{ route('users.edit', $user->id) }}" class="boton_link_md rounded">Editar</a>
                                    @elseif(Auth::user()->isAdmin())
                                        @if(!$user->isSuperAdmin())
                                        <a href="{{ route('users.edit', $user->id) }}" class="boton_link_md rounded">Editar</a>
                                        @endif
                                    @endif    

                                    @if(Auth::user()->isSuperAdmin())
                                    {{-- Super Admin puede eliminar a todos excepto a sí mismo --}}
                                        @if($user->id !== Auth::user()->id)
                                        <form method="POST" action="{{ route('users.destroy', $user->id) }}" style="display: inline;" onsubmit="return confirm('¿Estás seguro de eliminar al usuario {{ $user->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="boton_link_lg bg_rojo_2 rounded">
                                                Eliminar
                                            </button>
                                        </form>
                                        @endif
                                    @elseif(Auth::user()->isAdmin())
                                    {{-- Admin solo puede eliminar usuarios normales --}}
                                            @if($user->isUser())
                                            <form method="POST" action="{{ route('users.destroy', $user->id) }}" style="display: inline;" onsubmit="return confirm('¿Estás seguro de eliminar al usuario {{ $user->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="boton_link_lg bg_rojo_2 rounded">
                                                    Eliminar
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