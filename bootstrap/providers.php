<?php

return [
    App\Providers\AppServiceProvider::class,
    // Registrado a mano (además del auto-discovery) porque en el servidor no se corre composer:
    // así el PDF de Reportes funciona aunque bootstrap/cache/packages.php esté desactualizado.
    Barryvdh\DomPDF\ServiceProvider::class,
];
