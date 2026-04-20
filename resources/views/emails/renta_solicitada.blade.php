<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Solicitud de renta recibida — Flash Car</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f4;font-family:Arial,Helvetica,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:30px 0;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.10);">

                {{-- HEADER --}}
                <tr>
                    <td style="background:#f5c400;padding:28px 32px;text-align:center;">
                        <p style="margin:0;font-size:26px;font-weight:900;color:#111;letter-spacing:1px;">⚡ FLASH CAR</p>
                        <p style="margin:6px 0 0;font-size:13px;color:#333;">Renta de autos — Querétaro, México</p>
                    </td>
                </tr>

                {{-- SALUDO --}}
                <tr>
                    <td style="padding:32px 32px 0;">
                        <p style="margin:0;font-size:22px;font-weight:700;color:#111;">¡Hola, {{ $renta->nombre_completo }}!</p>
                        <p style="margin:10px 0 0;font-size:15px;color:#444;line-height:1.6;">
                            Hemos recibido tu solicitud de renta correctamente. En breve uno de nuestros agentes se pondrá en contacto contigo para confirmar los detalles.
                        </p>
                    </td>
                </tr>

                {{-- SEPARADOR --}}
                <tr>
                    <td style="padding:24px 32px 0;">
                        <div style="height:3px;background:linear-gradient(to right,#ffffff00,#f5c400,#ffffff00);border-radius:2px;"></div>
                    </td>
                </tr>

                {{-- VEHÍCULO --}}
                <tr>
                    <td style="padding:20px 32px 0;">
                        <p style="margin:0 0 12px;font-size:14px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:0.5px;">Vehículo solicitado</p>
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="background:#f9f9f9;border-radius:8px;padding:16px;">
                                    <p style="margin:0;font-size:18px;font-weight:700;color:#111;">
                                        {{ $renta->vehicle->name ?? 'Vehículo' }}
                                    </p>
                                    @if($renta->vehicle)
                                    <p style="margin:4px 0 0;font-size:13px;color:#777;">
                                        {{ $renta->vehicle->brand }} {{ $renta->vehicle->model }}
                                        @if($renta->vehicle->year) · {{ $renta->vehicle->year }} @endif
                                        — o similar
                                    </p>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- DETALLES DE LA RENTA --}}
                <tr>
                    <td style="padding:20px 32px 0;">
                        <p style="margin:0 0 12px;font-size:14px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:0.5px;">Detalles de la renta</p>
                        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">

                            <tr style="border-bottom:1px solid #eeeeee;">
                                <td style="padding:10px 0;font-size:14px;color:#666;width:50%;">📅 Fecha de entrega</td>
                                <td style="padding:10px 0;font-size:14px;color:#111;font-weight:600;text-align:right;">
                                    {{ \Carbon\Carbon::parse($renta->fecha_entrega)->format('d/m/Y') }}
                                    · {{ substr($renta->hora_entrega, 0, 5) }}
                                </td>
                            </tr>
                            <tr style="border-bottom:1px solid #eeeeee;">
                                <td style="padding:10px 0;font-size:14px;color:#666;">📍 Lugar de entrega</td>
                                <td style="padding:10px 0;font-size:14px;color:#111;font-weight:600;text-align:right;">{{ $renta->lugar_entrega }}</td>
                            </tr>
                            <tr style="border-bottom:1px solid #eeeeee;">
                                <td style="padding:10px 0;font-size:14px;color:#666;">🔄 Fecha de devolución</td>
                                <td style="padding:10px 0;font-size:14px;color:#111;font-weight:600;text-align:right;">
                                    {{ \Carbon\Carbon::parse($renta->fecha_devolucion)->format('d/m/Y') }}
                                    · {{ substr($renta->hora_devolucion, 0, 5) }}
                                </td>
                            </tr>
                            <tr style="border-bottom:1px solid #eeeeee;">
                                <td style="padding:10px 0;font-size:14px;color:#666;">📍 Lugar de devolución</td>
                                <td style="padding:10px 0;font-size:14px;color:#111;font-weight:600;text-align:right;">{{ $renta->lugar_devolucion }}</td>
                            </tr>
                            <tr style="border-bottom:1px solid #eeeeee;">
                                <td style="padding:10px 0;font-size:14px;color:#666;">👥 Pasajeros</td>
                                <td style="padding:10px 0;font-size:14px;color:#111;font-weight:600;text-align:right;">{{ $renta->num_pasajeros }}</td>
                            </tr>
                            <tr style="border-bottom:1px solid #eeeeee;">
                                <td style="padding:10px 0;font-size:14px;color:#666;">📆 Total de días</td>
                                <td style="padding:10px 0;font-size:14px;color:#111;font-weight:600;text-align:right;">{{ $renta->total_dias }} día{{ $renta->total_dias > 1 ? 's' : '' }}</td>
                            </tr>
                            <tr>
                                <td style="padding:14px 0 4px;font-size:16px;color:#111;font-weight:700;">💰 Costo estimado</td>
                                <td style="padding:14px 0 4px;font-size:20px;color:#111;font-weight:900;text-align:right;">
                                    ${{ number_format($renta->costo_total, 2) }} MXN
                                </td>
                            </tr>

                        </table>
                    </td>
                </tr>

                {{-- SEPARADOR --}}
                <tr>
                    <td style="padding:24px 32px 0;">
                        <div style="height:3px;background:linear-gradient(to right,#ffffff00,#f5c400,#ffffff00);border-radius:2px;"></div>
                    </td>
                </tr>

                {{-- AVISO --}}
                <tr>
                    <td style="padding:20px 32px;">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="background:#fffbe6;border-left:4px solid #f5c400;border-radius:0 6px 6px 0;padding:14px 16px;">
                                    <p style="margin:0;font-size:14px;color:#555;line-height:1.6;">
                                        Esta es una <strong>confirmación de solicitud</strong>, no una reserva definitiva. Un asesor de Flash Car te contactará en las próximas horas para confirmar disponibilidad y coordinar los detalles finales.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- FOOTER --}}
                <tr>
                    <td style="background:#111;padding:20px 32px;text-align:center;">
                        <p style="margin:0;font-size:13px;color:#aaa;">
                            ¿Tienes dudas? Contáctanos en
                            <a href="mailto:contacto@flashcar.mx" style="color:#f5c400;text-decoration:none;">contacto@flashcar.mx</a>
                        </p>
                        <p style="margin:8px 0 0;font-size:12px;color:#666;">© {{ date('Y') }} Flash Car · Querétaro, México</p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
