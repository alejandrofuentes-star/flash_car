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