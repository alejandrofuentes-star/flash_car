@extends('layout.layouts')

@section('title','Preguntas - Flash Car')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/styles_pagina_principal.css') }}?v=1">
@endpush

@section('content')
@include('layout.header_user')
<div class="bg-light">
    <div class="main_principal">
        <div class="space_principal">
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        <!--primera parte-->
        <div class="col-12 d-flex align-items-center justify-content-center flex-column h_cuatom_faqs">
            <h1 class="fs-50 h1 text-white text-center">FAQ's</h1>
            <p class="fs-4 text-white"><span>{{ __('faqs.breadcrumb') }}</span><span class="fs-4 c_text_amarillo">></span> FAQ's</p>
        </div>
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        <!--segunda parte-->
        <section class="bg-white">

        <div class="container">
            <div class="row py-5">
                <div class="col-12 d-flex align-items-start justify-content-center flex-column py-3">
                    <h2 class="fs-3">{{ __('faqs.help') }}</h2>
                    <div class="linea_amarilla_bottom"></div>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-center flex-column p-2">
                    <div class="col-12 d-flex align-items-center justify-content-center flex-wrap border_faq rounded p-3">
                        <div class="col-6 col-sm-6 col-md-1 col-lg-1 d-flex align-items-center justify-content-center">
                            <img src="./img/icono_faqs.png" width="90%" alt="Faqs icono">
                        </div>
                        <div class="col-12 col-sm-12 col-md-11 col-lg-11 d-flex align-items-start justify-content-center flex-column p-2">
                            <h3 class="fs-5">{{ __('faqs.q1') }}</h3>
                            <p class="fs-6 color_text_gris">{{ __('faqs.a1') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-center flex-column p-2">
                    <div class="col-12 d-flex align-items-center justify-content-center flex-wrap border_faq rounded p-3">
                        <div class="col-6 col-sm-6 col-md-1 col-lg-1 d-flex align-items-center justify-content-center">
                            <img src="./img/icono_faqs.png" width="90%" alt="Faqs icono">
                        </div>
                        <div class="col-12 col-sm-12 col-md-11 col-lg-11 d-flex align-items-start justify-content-center flex-column p-2">
                            <h3 class="fs-5">{{ __('faqs.q2') }}</h3>
                            <p class="fs-6 color_text_gris">{{ __('faqs.a2') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-center flex-column p-2">
                    <div class="col-12 d-flex align-items-center justify-content-center flex-wrap border_faq rounded p-3">
                        <div class="col-6 col-sm-6 col-md-1 col-lg-1 d-flex align-items-center justify-content-center">
                            <img src="./img/icono_faqs.png" width="90%" alt="Faqs icono">
                        </div>
                        <div class="col-12 col-sm-12 col-md-11 col-lg-11 d-flex align-items-start justify-content-center flex-column p-2">
                            <h3 class="fs-5">{{ __('faqs.q3') }}</h3>
                            <p class="fs-6 color_text_gris">{{ __('faqs.a3') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-center flex-column p-2">
                    <div class="col-12 d-flex align-items-center justify-content-center flex-wrap border_faq rounded p-3">
                        <div class="col-6 col-sm-6 col-md-1 col-lg-1 d-flex align-items-center justify-content-center">
                            <img src="./img/icono_faqs.png" width="90%" alt="Faqs icono">
                        </div>
                        <div class="col-12 col-sm-12 col-md-11 col-lg-11 d-flex align-items-start justify-content-center flex-column p-2">
                            <h3 class="fs-5">{{ __('faqs.q4') }}</h3>
                            <p class="fs-6 color_text_gris">{{ __('faqs.a4') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        </section>

        <section>
            <div class="py-5 bg-light" id="contacto">
                <div class="container">
                    <div class="row">
                        <div class="col-12 d-flex align-items-start justify-content-center flex-wrap">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-6 align-items-start justify-content-center flex-column">
                                <img src="{{ asset('./img/logo.webp') }}" height="60px" alt="logo flash car">
                                <p class="fs-6">{{ __('contact.about') }}</p>
                                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap">
                                    <a class="col-6 col-sm-6 col-md-4 link_contactos px-3 m-1 py-1 fs-6 rounded shadow" href="tel:+52{{ App\Models\SiteSetting::get('telefono') }}"><i class="bi bi-telephone-fill"></i>{{ App\Models\SiteSetting::get('telefono') }}</a>
                                    <a class="col-6 col-sm-6 col-md-4 link_contactos px-3 m-1 py-1 fs-6 rounded shadow" href="https://wa.me/+52{{ App\Models\SiteSetting::get('whatsapp') }}?text=Me interesa conocer más sobre sus rentas" target="_blank"><i class="bi bi-whatsapp"></i>{{ App\Models\SiteSetting::get('whatsapp') }}</a>
                                    <a class="col-6 col-sm-6 col-md-4 link_contactos px-3 m-1 py-1 fs-6 rounded shadow" href="{{ App\Models\SiteSetting::get('facebook') }}" target="_blank"><i class="bi bi-facebook"></i>Flash Car</a>
                                    <a class="col-6 col-sm-6 col-md-4 link_contactos px-3 m-1 py-1 fs-6 rounded shadow" href="{{ App\Models\SiteSetting::get('instagram') }}" target="_blank"><i class="bi bi-instagram"></i>Flash Car</a>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-6 py-3 d-flex align-items-center justify-content-center">
                                <img class="border_img_custom shadow rounded" src="{{ asset('./img/bg-parallax.jpg') }}" width="90%" alt="contacto flash car">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        </div>
    </div>
</div>
@include('layout.burbujas')
@include('layout.footer')
@endsection
