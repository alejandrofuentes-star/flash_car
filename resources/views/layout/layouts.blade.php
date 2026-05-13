<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Open Graph -->
    <meta property="og:title" content="@yield('og_title', 'Flash Car - Renta de Autos en Querétaro')">
    <meta property="og:description" content="@yield('og_description', 'Renta de autos 100% queretana. Encuentra el vehículo ideal para tu viaje al mejor precio.')">
    <meta property="og:image" content="{{ asset('img/fb-background.jpg') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="es_MX">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', 'Flash Car - Renta de Autos en Querétaro')">
    <meta name="twitter:description" content="@yield('og_description', 'Renta de autos 100% queretana. Encuentra el vehículo ideal para tu viaje al mejor precio.')">
    <meta name="twitter:image" content="{{ asset('img/fb-background.jpg') }}">

    <link rel="icon" type="image/x-icon" href="{{ asset('./img/icono.png') }}">
    <title>@yield('title', 'Flash Car - Renta de Autos en Querétaro')</title>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-17945512932"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'AW-17945512932');
    </script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css?v=1.2">
    <link rel="stylesheet" href="{{ asset('css/styles_general.css') }}?v=1.2">
    @stack('styles')

</head>
<body>
    <main>
        
    </main>
    @yield('content')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="{{ asset('./js/libreria_jquery.js') }}"></script>
    <script src="{{ asset('./js/scripts_header.js') }}"></script>
    <script src="{{ asset('./js/messenger_alerts.js') }}"></script>
    @stack('scripts')
</body>
</html>