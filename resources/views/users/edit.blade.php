@extends('layout.layouts')

@section('title', 'Editar - Flash Car')

@section('content')
 @include('layout.header')
<div class="bg-light h_100_vh d-flex align-items-center justify-content-center">
    <div class="main_principal">
        <div class="space_principal">
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->  
            <div class="container">
                <div class="col-12 my-3 d-flex align-items-center justify-content-start flex-wrap rounded cont_base">
                    <div class="col-12 d-flex align-items-start justify-content-center flex-column p-2 bg_gris_8">
                        <h1 class="fs-6 text_uppcase">Detalle de Usuario</h1>
                        <div class="col-4 col-sm-4 col-md-2 col-lg-1">
                            <a class="boton_forms b_sm rounded link_decoration_none display_flex_center_center" href="{{ route('users.index') }}" class="btn btn-secondary"> ← Volver</a>
                        </div>
                    </div>
                    <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start flex-wrap p-2">
                        <p class="text-dark fs-6 m-0"><b>Información Personal</b></p>
                    </div>
                    <form class="col-12 d-flex align-items-center justify-content-start flex-wrap" method="POST" action="{{ route('users.update', $user->id) }}">
                        @csrf
                        @method('PUT')    
                            <div class="col-12 col-sm-12 col-md-6 fila_form_f_b py-2">
                                <label class="label_form_f_b fs-6 py-1 px-1" for="Nombre"><b>Nombre completo *</b></label>
                                <input class="input_form_f_b fs-6 py-1 px-1" type="text" id="name" name="name" value="{{ old('name', $user->name) }}"  autofocus>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 fila_form_f_b py-2">
                                <label class="label_form_f_b fs-6 py-1 px-1" for="Nombre"><b>Correo *</b></label>
                                <input class="input_form_f_b fs-6 py-1 px-1" type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 fila_form_f_b py-2">
                                <label class="label_form_f_b fs-6 py-1" for="Nombre"><b>Rol *</b></label>
                                <select class="input_form_f_b fs-6 py-1" id="role" name="role" required>
                                @if(Auth::user()->isSuperAdmin())
                                    <option value="super_admin" {{ old('role', $user->role) == 'super_admin' ? 'selected' : '' }}>
                                        Super Admin
                                    </option>
                                @endif
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>
                                    Admin
                                </option>
                                <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>
                                    Usuario
                                </option>
                            </select>
                            <small style="color: var(--text-light); font-size: 13px; display: block; margin-top: 5px;">
                                @if(Auth::user()->isAdmin())
                                    Los Admins no pueden asignar el rol de Super Admin
                                @endif
                            </small>
                        </div>
                        <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start flex-wrap p-2">
                            <p class="text-dark fs-6 m-0"><b>Cambiar Contraseña</b></p>
                        </div>
                        <div class="col-11 p-2 d-flex align-items-center justify-content-start flex-wrap">
                            <p class="fs-6 label_form_f_b">Deja en blanco si no deseas cambiar la contraseña</p>
                            <div class="col-12 col-sm-12 col-md-6 fila_form_f_b py-2">
                                <label class="label_form_f_b fs-6 py-1 px-1" for="Nombre"><b>Nueva contraseña</b></label>
                                <input class="input_form_f_b fs-6 py-1 px-1" type="password" id="password" name="password" placeholder="Mínimo 8 caracteres">
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 fila_form_f_b py-2">
                                <label class="label_form_f_b fs-6 py-1 px-1" for="Nombre"><b>Confirmar nueva contraseña</b></label>
                                <input class="input_form_f_b fs-6 py-1 px-1" type="password" id="password_confirmation" name="password_confirmation" placeholder="Repite la contraseña">
                            </div>
                        </div>
                        <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start flex-wrap p-2">
                            <p class="text-dark fs-6 m-0"><b>Información de Cuenta</b></p>
                        </div>
                        <div class="col-11 p-2 d-flex align-items-center justify-content-start flex-wrap">
                            <div class="col-12 col-sm-12 col-md-6 fila_form_f_b py-2">
                                <label class="label_form_f_b fs-6 py-1 px-1" for="Nombre"><b>Fecha de Registro</b></label>
                                <input class="input_form_f_b fs-6 py-1 px-1" type="text" value="{{ $user->created_at->format('d/m/Y H:i:s') }}" readonly>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 fila_form_f_b py-2">
                                <label class="label_form_f_b fs-6 py-1 px-1" for="Nombre"><b>Última Actualización</b></label>
                                <input class="input_form_f_b fs-6 py-1 px-1" type="text" value="{{ $user->updated_at->format('d/m/Y H:i:s') }}" readonly>
                            </div>
                        </div>
                        <div class="col-12 py-2 d-flex align-items-center justify-content-center">
                            <a href="{{ route('users.index') }}" class="boton_link_lg rounded">
                                Cancelar
                            </a>
                            <button type="submit" class="boton_link_lg rounded">
                                    Guardar
                            </button>
                        </div>
                    </form>
                    @error('name')
                    <div class="messenger_alert">
                        <div class="dialog_alert danger py-2 px-4 rounded">
                            <div class="fs-6 text-white"><b>{{ $message }}</b></div>
                        </div>
                    </div>
                    @enderror
                    @error('email')
                    <div class="messenger_alert">
                        <div class="dialog_alert danger py-2 px-4 rounded">
                            <div class="fs-6 text-white"><b>{{ $message }}</b></div>
                        </div>
                    </div>
                    @enderror
                    @error('rol')
                    <div class="messenger_alert">
                        <div class="dialog_alert danger py-2 px-4 rounded">
                            <div class="fs-6 text-white"><b>{{ $message }}</b></div>
                        </div>
                    </div>
                    @enderror
                    @error('password')
                    <div class="messenger_alert">
                        <div class="dialog_alert danger py-2 px-4 rounded">
                            <div class="fs-6 text-white"><b>{{ $message }}</b></div>
                        </div>
                    </div>
                    @enderror
                    @if ($errors->any())
                        <div class="messenger_alert">
                            <div class="dialog_alert danger py-2 px-4 rounded">
                                <strong class="fs-6 text-white">Error: </strong>
                                @foreach ($errors->all() as $error)
                                    <div class="fs-6 text-white"><b>{{ $error }}</b></div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        </div>
    </div>
</div>
@endsection