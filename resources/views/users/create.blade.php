@extends('layout.layouts')

@section('title', 'Crear usuario - Flash Car')

@section('content')
@include('layout.header')
<div class="bg-light h_100_vh d-flex align-items-center justify-content-center">
    <div class="main_principal">
        <div class="space_principal">
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        <div class="container">
            <div class="col-12 my-2 d-flex align-items-center justify-content-start flex-wrap rounded cont_base">
                <div class="col-12 d-flex align-items-start justify-content-center flex-column p-2 bg_gris_8">
                    <h1 class="fs-6 text_uppcase">Crear Nuevo Usuario</h1>
                    <div class="col-4 col-sm-4 col-md-2 col-lg-1">
                        <a class="boton_forms b_sm rounded link_decoration_none display_flex_center_center" href="{{ route('users.index') }}" class="btn btn-secondary"> ← Volver</a>
                    </div>
                </div>
                <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start flex-wrap p-2">
                    <p class="text-dark fs-6 m-0"><b>Información Personal</b></p>
                </div>
                <form class="col-12 d-flex align-items-center justify-content-center flex-wrap" method="POST" action="{{ route('users.store') }}">
                    @csrf
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 p-2 d-flex align-items-center justify-content-start flex-wrap">
                        <div class="col-12 col-sm-12 col-md-6 fila_form_f_b py-2">
                            <label class="label_form_f_b fs-6 py-1 px-1" for="Nombre"><b>Nombre completo *</b></label>
                            <input class="input_form_f_b fs-6 py-1 px-1" type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Ej: Juan Pérez"required autofocus>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 fila_form_f_b py-2">
                            <label class="label_form_f_b fs-6 py-1 px-1" for="email"><b>Correo electrónico *</b></label>
                            <input class="input_form_f_b fs-6 py-1 px-1" type="email" id="email" name="email" value="{{ old('email') }}" placeholder="usuario@ejemplo.com" required>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 fila_form_f_b py-2">
                            <label class="label_form_f_b fs-6 py-1 px-1" for="role"><b>Rol *</b></label>
                            <select class="input_form_f_b fs-6 py-1 px-1" id="role" name="role" required>
                                <option value="">-- Selecciona un rol --</option>
                                @if(Auth::user()->isSuperAdmin())
                                    <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>
                                        Super Admin
                                    </option>
                                    @endif
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                                    Admin
                                </option>
                                <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>
                                    Usuario
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start flex-wrap p-2">
                        <p class="text-dark fs-6 m-0"><b>Contraseña</b></p>
                    </div>
                    <div class="col-12 d-flex align-items-center justify-content-center flex-wrap">
                        <div class="col-12 col-sm-12 col-md-6 fila_form_f_b py-2">
                            <label class="label_form_f_b fs-6 py-1 px-1" for="Nombre"><b>Contraseña *</b></label>
                            <input class="input_form_f_b fs-6 py-1 px-1" type="password" id="password" name="password" placeholder="Mínimo 8 caracteres" required>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 fila_form_f_b py-2">
                            <label class="label_form_f_b fs-6 py-1 px-1" for="Nombre"><b>Confirmar contraseña *</b></label>
                            <input class="input_form_f_b fs-6 py-1 px-1" type="password" id="password_confirmation" name="password_confirmation" placeholder="Repite la contraseña" required>
                        </div>
                    </div>
                    <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start flex-wrap p-2">
                        <p class="text-dark fs-6 m-0"><b>Información Importante</b></p>
                    </div>
                    <ul class="col-12 d-flex align-items-center justify-content-center flex-column">
                        <li class="label_form_f_b fs-6 py-1 px-1">Todos los campos marcados con (*) son obligatorios</li>
                        <li class="label_form_f_b fs-6 py-1 px-1">El correo electrónico debe ser único en el sistema</li>
                        <li class="label_form_f_b fs-6 py-1 px-1">La contraseña debe tener al menos 8 caracteres</li>
                        <li class="label_form_f_b fs-6 py-1 px-1">El usuario recibirá acceso inmediato al sistema</li>
                    </ul>
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
                @error('role')
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
        </div>
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        </div>
    </div>
</div>
@endsection