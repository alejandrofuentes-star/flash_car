$(document).ready(function() 
{
    setTimeout(function() 
    {
        $(".messenger_alert").fadeOut(300);
    }, 10000); // 15000 ms = 5 segundos

    $('#close_messenger').click(function()
    {
        $(".messenger_alert").fadeOut(300);
    });
});