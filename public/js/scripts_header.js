$("#submenu").hide();
$(".click_any_side").hide();
var a_c_menu = false;

$('#boton_menu').click(function()
{
    if(!a_c_menu)
    {
        $("#submenu").fadeIn(300);
        $(".click_any_side").fadeIn(300);
    }
    else
    {
        $("#submenu").fadeOut(300);
        $(".click_any_side").fadeOut(300);
    }
    a_c_menu = !a_c_menu;
});

$('.link_submenu').click(function()
{
    $("#submenu").fadeOut(300);
    $(".click_any_side").fadeOut(300);
    a_c_menu = !a_c_menu;
});

$('.click_any_side').click(function()
{
    $("#submenu").fadeOut(300);
    $(".click_any_side").fadeOut(300);
    a_c_menu = !a_c_menu;
});

$("#submenu").css("opacity","1");
$(".click_any_side").css("opacity","1");

//menu y submenu de los clientes
$("#submenu_clientes").hide();
$(".click_any_side").hide();
var a_c_menu_cliente = false;

$('#boton_submenu').click(function()
{
    if(!a_c_menu_cliente)
    {
        $("#submenu_clientes").fadeIn(300);
        $(".click_any_side").fadeIn(300);
    }
    else
    {
        $("#submenu_clientes").fadeOut(300);
        $(".click_any_side").fadeOut(300);
    }
    a_c_menu_cliente = !a_c_menu_cliente;
});

$('.link_submenu_clientes').click(function()
{
    $("#submenu_clientes").fadeOut(300);
    $(".click_any_side").fadeOut(300);
    a_c_menu_cliente = !a_c_menu_cliente;
});

$('.click_any_side').click(function()
{
    $("#submenu_clientes").fadeOut(300);
    $(".click_any_side").fadeOut(300);
    a_c_menu_cliente = !a_c_menu_cliente;
});

$("#submenu_clientes").css("opacity","1");
$(".click_any_side").css("opacity","1");
//campana de notificaciones (se cierra al hacer clic fuera, sin usar .click_any_side)
$("#panel_notificaciones").hide();

$('#boton_notificaciones').click(function(e)
{
    e.stopPropagation();
    if(a_c_menu)
    {
        $("#submenu").fadeOut(300);
        $(".click_any_side").fadeOut(300);
        a_c_menu = false;
    }
    $("#panel_notificaciones").fadeToggle(300);
});

$('#panel_notificaciones').click(function(e)
{
    e.stopPropagation();
});

$(document).click(function()
{
    $("#panel_notificaciones").fadeOut(300);
});

//marcar notificaciones como leídas
function marcarNotificacionesLeidas(claves)
{
    if(!claves.length) return;
    fetch($('#panel_notificaciones').data('url-leidas'), {
        method: 'POST',
        keepalive: true, // para que se guarde aunque la página navegue al detalle
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        body: JSON.stringify({ claves: claves })
    });

    claves.forEach(function(clave)
    {
        $('.item_notificacion[data-clave="' + clave + '"]').addClass('leida');
    });

    var restantes = $('.item_notificacion:not(.leida)').length;
    $('#badge_notificaciones').text(restantes).toggleClass('d-none', restantes === 0);
    $('#boton_marcar_todas').toggleClass('d-none', restantes === 0);
}

$('.item_notificacion').click(function()
{
    if(!$(this).hasClass('leida'))
    {
        marcarNotificacionesLeidas([$(this).data('clave')]);
    }
});

$('#boton_marcar_todas').click(function()
{
    var claves = $('.item_notificacion:not(.leida)').map(function(){ return $(this).data('clave'); }).get();
    marcarNotificacionesLeidas(claves);
});
