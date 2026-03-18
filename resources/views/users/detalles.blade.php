@extends('layout.layouts')

@section('title', 'Detalles - Flash Car')

@section('content')
 @include('layout.header')
<div class="bg-light h_100_vh d-flex align-items-center justify-content-center">
    <div class="main_principal">
        <div class="space_principal">
            <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
            <div class="container">    
                    <div class="col-12 d-flex align-items-center justify-content-start flex-wrap rounded cont_base">
                        <div class="col-12 d-flex align-items-start justify-content-center flex-column p-2 bg_gris_8">
                            <h1 class="fs-6 text_uppcase">Detalle de Usuario</h1>
                            <div class="col-4 col-sm-4 col-md-2 col-lg-1">
                                <a class="boton_forms b_sm rounded link_decoration_none display_flex_center_center" href="{{ route('users.index') }}" class="btn btn-secondary"> ← Volver</a>
                            </div>
                        </div>
                        <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start flex-wrap p-2">
                            <p class="text-dark fs-6 m-0"><b>Información Personal</b></p>
                        </div>
                        <div class="col-12 d-flex align-items-center justify-content-center flex-wrap">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-1 p-2 d-flex align-items-center justify-content-center">
                                <div class="circulo_perfil display_flex_center_center">
                                    <p class="m-0 fs-4 text_rojo_2">{{ strtoupper(substr($user->name, 0, 1)) }}</p>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-11 p-2 d-flex align-items-center justify-content-start flex-wrap">
                                <div class="col-6 fila_form_f_b py-2 ">
                                    <label class="label_form_f_b fs-6 py-1" for="Nombre"><b>Nombre</b></label>
                                    <input class="input_form_f_b fs-6 py-1" type="text" value="{{ $user->name }}" readonly>
                                </div>
                                <div class="col-6 fila_form_f_b py-2">
                                    <label class="label_form_f_b fs-6 py-1" for="Nombre"><b>Email</b></label>
                                    <input class="input_form_f_b fs-6 py-1" type="text" value="{{ $user->email }}" readonly>
                                </div>
                                <div class="col-6 fila_form_f_b py-2">
                                    <label class="label_form_f_b fs-6 py-1" for="Nombre"><b>Rol</b></label>
                                    <div>
                                        @if($user->isSuperAdmin())
                                            <span class="fs-6 etiqueta_super_admin">Super Admin</span>
                                        @elseif($user->isAdmin())
                                            <span class="fs-6 etiqueta_admin">Admin</span>
                                        @else
                                            <span class="fs-6 etiqueta_user">Usuario</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start flex-wrap p-2">
                            <p class="text-dark fs-6 m-0"><b>Información de Cuenta</b></p>
                        </div>
                        <div class="col-11 p-2 d-flex align-items-center justify-content-start flex-wrap">
                            <div class="col-12 col-sm-6 col-md-6 col-lg-4 fila_form_f_b py-2">
                                <label class="label_form_f_b fs-6 py-1" for="Nombre"><b>Fecha de Registro</b></label>
                                <input class="input_form_f_b fs-6 py-1" type="text" value="{{ $user->created_at->format('d/m/Y H:i:s') }}" readonly>
                            </div>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-4 fila_form_f_b py-2">
                                <label class="label_form_f_b fs-6 py-1" for="Nombre"><b>Última Actualización</b></label>
                                <input class="input_form_f_b fs-6 py-1" type="text" value="{{ $user->updated_at->format('d/m/Y H:i:s') }}" readonly>
                            </div>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-4 fila_form_f_b py-2">
                                <label class="label_form_f_b fs-6 py-1" for="Nombre"><b>Estado de la Cuenta</b></label>
                                <span class="fs-6 cuenta_activa">Activa</span>
                            </div>
                        </div>
                        <div class="col-12 py-2 d-flex align-items-center justify-content-center">
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
            </div>
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        </div>
    </div>
</div>
@endsection