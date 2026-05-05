@push('styles')
<link rel="stylesheet" href="https://css.neruc.app/css/css_whatsapp_n_one.css?v=1.1">
@endpush

@php
    $tel = App\Models\SiteSetting::get('telefono');
    $wa  = App\Models\SiteSetting::get('whatsapp');
@endphp

{{-- Burbuja teléfono --}}
<a href="tel:+52{{ $tel }}">
    <div class="tag_m_telefono" id="tag_m_telefono_boton_llamar">
        <img id="tag_m_telefono_icono_black" src="https://css.neruc.app/img/tag_m_telefono_icono.png" width="20px" height="20px" alt="icono_telefono">
        <img id="tag_m_telefono_icono_white" src="https://css.neruc.app/img/tag_m_telefono_icono_w.png" width="20px" height="20px" alt="icono_telefono">
        <p>{{ $tel }}</p>
    </div>
</a>

{{-- Burbuja WhatsApp --}}
<div class="tag_m_whatsapp_burbuja">
    <div class="tag_m_whatsapp_circ_fijo" id="tag_m_click_whatsapp">
        <img src="https://css.neruc.app/img/tag_m_icono_whatsapp.png" width="35px" height="35px" alt="icono_whatsapp">
        <div class="tag_m_whatsapp_circ_anim">
            <div class="tag_m_whatsapp_circ_anim_2"></div>
        </div>
    </div>
</div>
<div class="tag_m_whatsapp_cuadro_dialogo" id="tag_m_whatsapp_id_cuadro_dialogo">
    <div class="tag_m_whatsapp_perfil">
        <div class="tag_m_whatsapp_foto_perfil">
            <img src="https://css.neruc.app/img/foto_perfil_tag_m_whatsapp.webp" width="40px" height="40px" alt="foto perfil">
        </div>
        <p><b>WhatsApp</b><br>En línea</p>
        <div class="tag_m_whatsapp_cont_cerrar">
            <div class="teg_m_whatsapp_circulo_cerrar" id="tag_m_whatsapp_cerrar_dia">
                <p>X</p>
            </div>
        </div>
    </div>
    <div class="tag_m_whatsapp_cont_text">
        <div class="tag_m_whatsapp_dialogo">
            <img id="img_tag_m_ceja" src="https://css.neruc.app/img/ceja_whatsapp_tag_m.png" width="20px" height="20px" alt="icono_ceja_whatsapp">
            <p>¿En qué podemos ayudarte?</p>
        </div>
        <div class="tag_m_whatsapp_dialogo tag_m_whatsapp_chica">
            <img id="img_tag_m_mano" src="https://css.neruc.app/img/mano_tag_m_whatsapp.png" width="50px" height="auto" alt="icono_mano_whatsapp">
        </div>
        <div class="tag_m_whatsapp_boton_enviar">
            <div class="tag_m_whatsapp_circulo_enviar">
                <a href="https://wa.me/+52{{ $wa }}?text=Me interesa conocer más sobre sus rentas" target="_blank">Chatear con un asesor</a>
            </div>
        </div>
    </div>
</div>

<script src="https://css.neruc.app/js/script_tag_m_whatsapp_telefono.js"></script>
