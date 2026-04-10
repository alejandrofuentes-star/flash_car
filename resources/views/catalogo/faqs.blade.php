@extends('layout.layouts')

@section('title','Preguntas - Flash Car')

@section('content')
@include('layout.header_user')
<div class="bg-light">
    <div class="main_principal">
        <div class="space_principal">
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        <!--primera parte-->
        <div class="col-12 d-flex align-items-center justify-content-center flex-column h_cuatom_faqs">
            <h1 class="fs-50 h1 text-white text-center">FAQ's</h1>
            <p class="fs-4 text-white"><span data-lang="p_1_pt1">Inicio</span><span class="fs-4 c_text_amarillo">></span> FAQ's</p>
        </div>
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        <!--segunda parte-->
        <section class="bg-white">

        <div class="container">
            <div class="row py-5">
                <div class="col-12 d-flex align-items-start justify-content-center flex-column py-3">
                    <h2 class="fs-3" data-lang="h2_pt2">¿Necesitas Ayuda?</h2>
                    <div class="linea_amarilla_bottom"></div>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-center flex-column p-2">
                    <div class="col-12 d-flex align-items-center justify-content-center flex-wrap border_faq rounded p-3">
                        <div class="col-6 col-sm-6 col-md-1 col-lg-1 d-flex align-items-center justify-content-center">
                            <img src="./img/icono_faqs.png" width="90%" alt="Faqs icono">
                        </div>
                        <div class="col-12 col-sm-12 col-md-11 col-lg-11 d-flex align-items-start justify-content-center flex-column p-2">
                            <h3 class="fs-5" data-lang="h3_pt2_1">¿Por qué si hago mi reservación con anticipación no me entregan 
                                el modelo y marca de auto de mi preferencia?
                            </h3>
                            <p class="fs-6 color_text_gris" data-lang="p_pt2_1">Cuando realizas una reservación lo que 
                                seleccionas es una categoría de auto y no un modelo específico. 
                                Para nosotros, los autos son catalogados de acuerdo a sus características 
                                en grupos. Por ejemplo, al reservar en auto del Grupo A “Subcompacto” 
                                tenemos el compromiso de entregarte alguno de los 3 modelos incluidos 
                                en esa categoría. Cada grupo puede contener varias marcas y modelos 
                                automotrices todos con los estándares de calidad y comodidad a los 
                                que estamos comprometidos con nuestros clientes.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-center flex-column p-2">
                    <div class="col-12 d-flex align-items-center justify-content-center flex-wrap border_faq rounded p-3">
                        <div class="col-6 col-sm-6 col-md-1 col-lg-1 d-flex align-items-center justify-content-center">
                            <img src="./img/icono_faqs.png" width="90%" alt="Faqs icono">
                        </div>
                        <div class="col-12 col-sm-12 col-md-11 col-lg-11 d-flex align-items-start justify-content-center flex-column p-2">
                            <h3 class="fs-5" data-lang="h3_pt2_2">	
                                ¿Es posible prepagar con mi tarjeta de crédito y solicitar que el 
                                vehículo se le entregue a otra persona?
                            </h3>
                            <p class="fs-6 color_text_gris" data-lang="p_pt2_2">Por cuestiones de seguridad, el titular de 
                                la renta debe ser el titular también de la tarjeta de crédito con la que 
                                se realice el prepago.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-center flex-column p-2">
                    <div class="col-12 d-flex align-items-center justify-content-center flex-wrap border_faq rounded p-3">
                        <div class="col-6 col-sm-6 col-md-1 col-lg-1 d-flex align-items-center justify-content-center">
                            <img src="./img/icono_faqs.png" width="90%" alt="Faqs icono">
                        </div>
                        <div class="col-12 col-sm-12 col-md-11 col-lg-11 d-flex align-items-start justify-content-center flex-column p-2">
                            <h3 class="fs-5" data-lang="h3_pt2_3">	
                                ¿Cuánto es el límite de crédito necesario en mi tarjeta para poder rentar un auto?
                            </h3>
                            <p class="fs-6 color_text_gris" data-lang="p_pt2_3">Para proteger el auto que se te entregará 
                                es necesario realizar una pre-autorización a tu tarjeta de crédito y 
                                el monto se determinará según la opción que elijas entre nuestros 
                                relevos de responsabilidad.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-center flex-column p-2">
                    <div class="col-12 d-flex align-items-center justify-content-center flex-wrap border_faq rounded p-3">
                        <div class="col-6 col-sm-6 col-md-1 col-lg-1 d-flex align-items-center justify-content-center">
                            <img src="./img/icono_faqs.png" width="90%" alt="Faqs icono">
                        </div>
                        <div class="col-12 col-sm-12 col-md-11 col-lg-11 d-flex align-items-start justify-content-center flex-column p-2">
                            <h3 class="fs-5" data-lang="h3_pt2_4">	
                                ¿Son obligatorias las protecciones al rentar un auto?
                            </h3>
                            <p class="fs-6 color_text_gris" data-lang="p_pt2_4">Nuestros relevos de responsabilidad y 
                                protecciones son 100% opcionales y adicionales al total de la renta, 
                                dándote la libertad de elegir entre cualquiera de nuestras alternativas. 
                                Aunque es altamente recomendable para tu seguridad y tranquilad adquirir 
                                algún tipo de relevo de responsabilidad, de ninguna manera son obligatorias 
                                ni su compra condiciona la renta del auto...
                            </p>
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
                                <p class="fs-6">En Flash Car, somos una empresa 100% queretana comprometida con ofrecer un servicio de renta de vehículos confiable, accesible y de alta calidad. Contamos con una amplia flotilla para cubrir todas tus necesidades, ya sea por día, semana o mes.</p>
                                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap">
                                    <a class="col-6 col-sm-6 col-md-4 link_contactos px-3 m-1 py-1 fs-6 rounded shadow" href=""><i class="bi bi-telephone-fill"></i>1122334455</a>
                                    <a class="col-6 col-sm-6 col-md-4 link_contactos px-3 m-1 py-1 fs-6 rounded shadow" href=""><i class="bi bi-whatsapp"></i>1122334455</a>
                                    <a class="col-6 col-sm-6 col-md-4 link_contactos px-3 m-1 py-1 fs-6 rounded shadow" href=""><i class="bi bi-facebook"></i>Flash Car</a>
                                    <a class="col-6 col-sm-6 col-md-4 link_contactos px-3 m-1 py-1 fs-6 rounded shadow" href=""><i class="bi bi-instagram"></i>Flash Car</a>
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
@include('layout.footer')
@endsection