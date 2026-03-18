@extends('layout.layouts')

@section('title', 'Login - Flash Car')

@section('content')
<div class="w-100 img_fondo_login h_100_vh d-flex align-items-center justify-content-center">
    <div class="col-12 h_100 d-flex align-items-center justify-content-center">
        <div class="container">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-6 d-flex align-items-center justify-content-center p-2">
                    <form class="form_base w_80 p-4 rounded" method="POST" action="{{ route('login') }}">
                    @csrf
                        <div class="col-12 d-flex align-items-center justify-content-center">
                            <img src="{{ asset('./img/logo.webp') }}" width="200px" alt="logo">
                        </div>
                        
                        <div class="fila_form py-2 border_buttom">
                            <label class="label_form fs-6 py-1" for="email">Email</label>
                            <input class="input_form fs-6 py-1" type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
                        </div>

                        <div class="fila_form py-2 border_buttom">
                            <label class="label_form fs-6 py-1" for="password">Contraseña</label>
                            <input class="input_form fs-6 py-1" type="password" id="password" name="password" required>
                        </div>

                        <div class="fila_form py-4">
                            <label class="text-dark fs-6"><input class="mx-2" type="checkbox" name="remember">Recordarme</label>
                        </div>

                        <div class="col-12 d-flex align-items-center justify-content-center">
                            <div class="col-8 col-sm-8 col-md-8 col-lg-5">
                                <button type="submit" class="boton_forms rounded">Iniciar sesión</button>
                            </div>
                        </div>
                        
                        @error('email')
                        <div class="messenger_alert">
                            <div class="dialog_alert danger py-2 px-4 rounded">
                                <div class="fs-6 text-white"><b>{{ $message }}</b></div>
                            </div>
                        </div>
                        @enderror
                    </form>
                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-6 d-flex align-items-center justify-content-center p-2">

                </div>
            </div>
        </div>
    </div>
</div>
@endsection