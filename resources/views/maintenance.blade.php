<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>En Mantenimiento — Flash Car</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: #fff;
            color: #111;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', system-ui, sans-serif;
            padding: 2rem 1.5rem;
        }
        .logo {
            height: 48px;
            margin-bottom: 2.5rem;
        }
        .icon-ring {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            background: #fff8e1;
            border: 2px solid #FFD600;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.75rem;
        }
        .icon-ring i {
            font-size: 2.5rem;
            color: #e6a800;
        }
        h1 {
            font-size: 1.75rem;
            font-weight: 700;
            letter-spacing: -.3px;
            margin-bottom: .75rem;
        }
        .divider {
            width: 48px;
            height: 3px;
            background: #FFD600;
            border-radius: 2px;
            margin: 1.25rem auto;
        }
        p {
            color: #666;
            font-size: 1rem;
            line-height: 1.65;
        }
        .badge-503 {
            display: inline-block;
            background: #f5f5f5;
            border: 1px solid #e0e0e0;
            color: #999;
            font-size: .72rem;
            letter-spacing: .5px;
            padding: .35rem .75rem;
            border-radius: 100px;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <div style="max-width:480px; text-align:center;">

        <img src="{{ asset('img/logo.webp') }}" alt="Flash Car" class="logo">

        <div class="icon-ring">
            <i class="bi bi-tools"></i>
        </div>

        <h1>Sitio en Mantenimiento</h1>
        <div class="divider"></div>

        <p>
            Estamos trabajando para mejorar tu experiencia.<br>
            Por favor regresa en unos momentos.
        </p>

        <span class="badge-503">503 — Servicio temporalmente no disponible</span>

    </div>
</body>
</html>
