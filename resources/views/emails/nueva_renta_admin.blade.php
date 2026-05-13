<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva reservación</title>
</head>
<body style="margin:0; padding:0; background:#f4f4f4; font-family:Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4; padding:30px 0;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.08);">

                {{-- Header --}}
                <tr>
                    <td style="background:#111111; padding:20px 30px; text-align:center;">
                        <p style="margin:0; font-size:1rem; font-weight:bold; color:#ffffff;">⚡ Flash Car — Panel Admin</p>
                    </td>
                </tr>

                {{-- Alerta --}}
                <tr>
                    <td style="padding:30px 30px 10px; text-align:center;">
                        <p style="font-size:1.4rem; font-weight:bold; color:#111; margin:0;">Nueva reservación recibida</p>
                        <p style="font-size:0.95rem; color:#555; margin:8px 0 0;">Se acaba de registrar una nueva solicitud de renta.</p>
                        <p style="font-size:1rem; font-weight:bold; color:#111; margin:8px 0 0;">Reservación #{{ $renta->id }}</p>
                    </td>
                </tr>

                {{-- Datos del cliente --}}
                <tr>
                    <td style="padding:20px 30px 10px;">
                        <p style="font-size:0.8rem; font-weight:bold; color:#888; text-transform:uppercase; margin:0 0 10px; border-bottom:1px solid #eee; padding-bottom:6px;">Datos del cliente</p>
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="padding:5px 0; font-size:0.9rem; color:#555; width:45%;">Nombre</td>
                                <td style="padding:5px 0; font-size:0.9rem; color:#111; font-weight:bold;">{{ $renta->nombre_completo }}</td>
                            </tr>
                            <tr>
                                <td style="padding:5px 0; font-size:0.9rem; color:#555;">Teléfono</td>
                                <td style="padding:5px 0; font-size:0.9rem; color:#111; font-weight:bold;">{{ $renta->telefono }}</td>
                            </tr>
                            <tr>
                                <td style="padding:5px 0; font-size:0.9rem; color:#555;">Correo</td>
                                <td style="padding:5px 0; font-size:0.9rem; color:#111; font-weight:bold;">{{ $renta->correo }}</td>
                            </tr>
                            <tr>
                                <td style="padding:5px 0; font-size:0.9rem; color:#555;">Ciudad</td>
                                <td style="padding:5px 0; font-size:0.9rem; color:#111; font-weight:bold;">{{ $renta->ciudad }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- Datos de la renta --}}
                <tr>
                    <td style="padding:10px 30px 10px;">
                        <p style="font-size:0.8rem; font-weight:bold; color:#888; text-transform:uppercase; margin:0 0 10px; border-bottom:1px solid #eee; padding-bottom:6px;">Detalles de la reservación</p>
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="padding:5px 0; font-size:0.9rem; color:#555; width:45%;">Vehículo</td>
                                <td style="padding:5px 0; font-size:0.9rem; color:#111; font-weight:bold;">{{ $renta->vehicle->name ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td style="padding:5px 0; font-size:0.9rem; color:#555;">Entrega</td>
                                <td style="padding:5px 0; font-size:0.9rem; color:#111; font-weight:bold;">{{ \Carbon\Carbon::parse($renta->fecha_entrega)->format('d/m/Y') }} — {{ $renta->hora_entrega }}</td>
                            </tr>
                            <tr>
                                <td style="padding:5px 0; font-size:0.9rem; color:#555;">Lugar de entrega</td>
                                <td style="padding:5px 0; font-size:0.9rem; color:#111; font-weight:bold;">{{ $renta->lugar_entrega }}</td>
                            </tr>
                            <tr>
                                <td style="padding:5px 0; font-size:0.9rem; color:#555;">Devolución</td>
                                <td style="padding:5px 0; font-size:0.9rem; color:#111; font-weight:bold;">{{ \Carbon\Carbon::parse($renta->fecha_devolucion)->format('d/m/Y') }} — {{ $renta->hora_devolucion }}</td>
                            </tr>
                            <tr>
                                <td style="padding:5px 0; font-size:0.9rem; color:#555;">Lugar de devolución</td>
                                <td style="padding:5px 0; font-size:0.9rem; color:#111; font-weight:bold;">{{ $renta->lugar_devolucion }}</td>
                            </tr>
                            <tr>
                                <td style="padding:5px 0; font-size:0.9rem; color:#555;">Total de días</td>
                                <td style="padding:5px 0; font-size:0.9rem; color:#111; font-weight:bold;">{{ $renta->total_dias }} día(s)</td>
                            </tr>
                            <tr>
                                <td style="padding:5px 0; font-size:0.9rem; color:#555;">Costo total</td>
                                <td style="padding:5px 0; font-size:1rem; color:#111; font-weight:bold;">${{ number_format($renta->costo_total, 2) }} MXN</td>
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- Botón panel --}}
                <tr>
                    <td style="padding:20px 30px; text-align:center;">
                        <a href="{{ url('/dashboard') }}" style="display:inline-block; background:#f5c518; color:#111; font-weight:bold; font-size:0.95rem; padding:12px 30px; border-radius:6px; text-decoration:none;">Ir al panel de administración</a>
                    </td>
                </tr>

                {{-- Footer --}}
                <tr>
                    <td style="background:#f9f9f9; padding:20px 30px; text-align:center; border-top:1px solid #eee;">
                        <p style="font-size:0.8rem; color:#aaa; margin:0;">Este mensaje es una notificación automática de Flash Car.</p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
