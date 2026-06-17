<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="x-apple-disable-message-reformatting">
</head>
<body style="margin:0; padding:0; background:#e5e5e5; font-family:Arial,Helvetica,sans-serif; font-size:14px; line-height:1.4; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; text-size-adjust:100%;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#e5e5e5; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; text-size-adjust:100%;">
        <tr>
            <td align="center" style="padding:12px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:640px; width:100%; background:#111418; border radius:16px; overflow:hidden; border:1px solid #22262b; font-family:Arial,Helvetica,sans-serif; font-size:14px; line-height:1.4; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; text-size-adjust:100%;">
                    <!-- Hero -->
                    <tr>
                        <td align="center" style="padding:28px 24px; background:#111418;">
                            <!-- Banda gris clara ajustada al tamaño del logo -->
                            <div style="display:inline-block; background:#fafbfc; background-image:linear-gradient(180deg,#ffffff,#f3f4f6); padding:8px 12px; border-radius:14px;">
                                <img src="https://flashcar.rentadeautos.site/img/logo_correo.jpg" alt="Flash Car" width="220" style="display:block; border:0; outline:none; text-decoration:none; height:auto;">
                            </div>
                            <div style="margin-top:16px; color:#ffffff; font-family:Arial,Helvetica,sans-serif; font-size:22px; font-weight:700; line-height:1.2; letter-spacing: 0.3px;">Confirmación de Renta de Vehículo</div>
                            <div style="margin-top:8px; display:inline-block; padding:6px 12px; border-radius:999px; background:#ffd60a; color:#111; font-weight:700; font-size:12px; font-family:Arial,Helvetica,sans-serif;">Renta #{{ str_pad($renta->id, 5, '0', STR_PAD_LEFT) }}</div>
                        </td>
                    </tr>
                    <!-- Intro -->
                    <tr>
                        <td style="background:#ffffff; padding:16px;">
                            <p style="margin:0 0 16px 0; color:#3b4046; font-family:Arial,Helvetica,sans-serif; font-size:14px;">Hola <strong>{{ $renta->nombre_completo }}</strong>, tu solicitud ha sido recibida correctamente. Aquí están los detalles:</p>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="table-layout:fixed; width:100%; max-width:100%;">
                                <tr>
                                    <td valign="top" width="100%" style="padding:4px 0; width:100%;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border:1px solid #e5e7eb; border-radius:12px; background:#fcfcfc; table-layout:fixed; width:100%; max-width:100%;">
                                            <tr>
                                                <td style="padding:10px 12px; vertical-align:top;">
                                                    <div style="font-size:11px; color:#6b7280; text-transform:uppercase; font-family:Arial,Helvetica,sans-serif;">Vehículo</div>
                                                    <div style="margin-top:2px; font-size:14px; color:#111418; font-weight:700; line-height:1.35; font-family:Arial,Helvetica,sans-serif; word-break:break-word; overflow-wrap:break-word; word-wrap:break-word;">
                                                        {{ $renta->vehicle->name ?? 'Vehículo' }}
                                                        @if($renta->vehicle)
                                                            <span style="color:#6b7280; font-weight:400;"> — {{ $renta->vehicle->brand }} {{ $renta->vehicle->model }}@if($renta->vehicle->year) {{ $renta->vehicle->year }}@endif</span>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" width="100%" style="padding:4px 0; width:100%;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border:1px solid #e5e7eb; border-radius:12px; background:#fcfcfc; table-layout:fixed; width:100%; max-width:100%;">
                                            <tr>
                                                <td style="padding:10px 12px; vertical-align:top;">
                                                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="table-layout:auto; border-collapse:collapse; width:100%;">
                                                        <tr>
                                                            <td width="60%" valign="top" style="padding-right:8px; width:60%">
                                                                <div style="font-size:11px; color:#6b7280; text-transform:uppercase; font-family:Arial,Helvetica,sans-serif;">Fecha de entrega</div>
                                                                <div style="margin-top:2px; font-size:14px; color:#111418; font-weight:700; line-height:1.35; font-family:Arial,Helvetica,sans-serif; white-space:nowrap;">{{ \Carbon\Carbon::parse($renta->fecha_entrega)->format('d/m/Y') }}</div>
                                                            </td>
                                                            <td width="40%" valign="top" style="width:40%">
                                                                <div style="font-size:11px; color:#6b7280; text-transform:uppercase; font-family:Arial,Helvetica,sans-serif;">Hora</div>
                                                                <div style="margin-top:2px; font-size:14px; color:#111418; font-weight:700; line-height:1.35; font-family:Arial,Helvetica,sans-serif; white-space:nowrap;">{{ substr($renta->hora_entrega, 0, 5) }}</div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" width="100%" style="padding:4px 0; width:100%;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border:1px solid #e5e7eb; border-radius:12px; background:#fcfcfc; table-layout:fixed; width:100%; max-width:100%;">
                                            <tr>
                                                <td style="padding:10px 12px; vertical-align:top;">
                                                    <div style="font-size:11px; color:#6b7280; text-transform:uppercase; font-family:Arial,Helvetica,sans-serif;">Lugar de entrega</div>
                                                    <div style="margin-top:2px; font-size:14px; color:#111418; font-weight:700; line-height:1.35; font-family:Arial,Helvetica,sans-serif; word-break:break-word; overflow-wrap:break-word; word-wrap:break-word;">{{ $renta->lugar_entrega }}</div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" width="100%" style="padding:4px 0; width:100%;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border:1px solid #e5e7eb; border-radius:12px; background:#fcfcfc; table-layout:fixed; width:100%; max-width:100%;">
                                            <tr>
                                                <td style="padding:10px 12px; vertical-align:top;">
                                                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="table-layout:auto; border-collapse:collapse; width:100%;">
                                                        <tr>
                                                            <td width="60%" valign="top" style="padding-right:8px; width:60%">
                                                                <div style="font-size:11px; color:#6b7280; text-transform:uppercase; font-family:Arial,Helvetica,sans-serif;">Fecha de devolución</div>
                                                                <div style="margin-top:2px; font-size:14px; color:#111418; font-weight:700; line-height:1.35; font-family:Arial,Helvetica,sans-serif; white-space:nowrap;">{{ \Carbon\Carbon::parse($renta->fecha_devolucion)->format('d/m/Y') }}</div>
                                                            </td>
                                                            <td width="40%" valign="top" style="width:40%">
                                                                <div style="font-size:11px; color:#6b7280; text-transform:uppercase; font-family:Arial,Helvetica,sans-serif;">Hora</div>
                                                                <div style="margin-top:2px; font-size:14px; color:#111418; font-weight:700; line-height:1.35; font-family:Arial,Helvetica,sans-serif; white-space:nowrap;">{{ substr($renta->hora_devolucion, 0, 5) }}</div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" width="100%" style="padding:4px 0; width:100%;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border:1px solid #e5e7eb; border-radius:12px; background:#fcfcfc; table-layout:fixed; width:100%; max-width:100%;">
                                            <tr>
                                                <td style="padding:10px 12px; vertical-align:top;">
                                                    <div style="font-size:11px; color:#6b7280; text-transform:uppercase; font-family:Arial,Helvetica,sans-serif;">Lugar de devolución</div>
                                                    <div style="margin-top:2px; font-size:14px; color:#111418; font-weight:700; line-height:1.35; font-family:Arial,Helvetica,sans-serif; word-break:break-word; overflow-wrap:break-word; word-wrap:break-word;">{{ $renta->lugar_devolucion }}</div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" width="100%" style="padding:4px 0; width:100%;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border:1px solid #e5e7eb; border-radius:12px; background:#fcfcfc; table-layout:fixed; width:100%; max-width:100%;">
                                            <tr>
                                                <td style="padding:10px 12px; vertical-align:top;">
                                                    <div style="font-size:11px; color:#6b7280; text-transform:uppercase; font-family:Arial,Helvetica,sans-serif;">Pasajeros</div>
                                                    <div style="margin-top:2px; font-size:14px; color:#111418; font-weight:700; line-height:1.35; font-family:Arial,Helvetica,sans-serif; word-break:break-word; overflow-wrap:break-word; word-wrap:break-word;">{{ $renta->num_pasajeros }}</div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" width="100%" style="padding:4px 0; width:100%;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border:1px solid #e5e7eb; border-radius:12px; background:#fcfcfc; table-layout:fixed; width:100%; max-width:100%;">
                                            <tr>
                                                <td style="padding:10px 12px; vertical-align:top;">
                                                    <div style="font-size:11px; color:#6b7280; text-transform:uppercase; font-family:Arial,Helvetica,sans-serif;">Total de días</div>
                                                    <div style="margin-top:2px; font-size:14px; color:#111418; font-weight:700; line-height:1.35; font-family:Arial,Helvetica,sans-serif; word-break:break-word; overflow-wrap:break-word; word-wrap:break-word;">{{ $renta->total_dias }} día{{ $renta->total_dias > 1 ? 's' : '' }}</div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" width="100%" style="padding:4px 0; width:100%;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border:1px solid #e5e7eb; border-radius:12px; background:#fcfcfc; table-layout:fixed; width:100%; max-width:100%;">
                                            <tr>
                                                <td style="padding:10px 12px; vertical-align:top;">
                                                    <div style="font-size:11px; color:#6b7280; text-transform:uppercase; font-family:Arial,Helvetica,sans-serif;">Costo total de la renta</div>
                                                    <div style="margin-top:2px; font-size:16px; color:#111418; font-weight:900; line-height:1.35; font-family:Arial,Helvetica,sans-serif; word-break:break-word; overflow-wrap:break-word; word-wrap:break-word;">${{ number_format($renta->costo_total, 2) }} MXN</div>
                                                    @if($renta->metodo_pago === 'tarjeta' && $renta->monto_anticipo > 0)
                                                    @php $resto = $renta->costo_total - $renta->monto_anticipo; @endphp
                                                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top:10px; border-top:1px solid #e5e7eb;">
                                                        <tr>
                                                            <td style="padding-top:8px;">
                                                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                                                    <tr>
                                                                        <td style="padding:4px 0;">
                                                                            <span style="font-size:12px; color:#6b7280; font-family:Arial,Helvetica,sans-serif;">Anticipo pagado con tarjeta</span>
                                                                        </td>
                                                                        <td align="right" style="padding:4px 0;">
                                                                            <span style="font-size:13px; color:#16a34a; font-weight:700; font-family:Arial,Helvetica,sans-serif;">−${{ number_format($renta->monto_anticipo, 2) }} MXN</span>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="padding:4px 0;">
                                                                            <span style="font-size:12px; color:#6b7280; font-family:Arial,Helvetica,sans-serif;">Resta por pagar al recibir el vehículo</span>
                                                                        </td>
                                                                        <td align="right" style="padding:4px 0;">
                                                                            <span style="font-size:14px; color:#b45309; font-weight:900; font-family:Arial,Helvetica,sans-serif;">${{ number_format($resto, 2) }} MXN</span>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                @if($renta->vehicle && $renta->vehicle->category)
                                <tr>
                                    <td valign="top" width="100%" style="padding:4px 0; width:100%;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border:1px solid #e5e7eb; border-radius:12px; background:#fcfcfc; table-layout:fixed; width:100%; max-width:100%;">
                                            <tr>
                                                <td style="padding:10px 12px; vertical-align:top;">
                                                    <div style="font-size:11px; color:#6b7280; text-transform:uppercase; font-family:Arial,Helvetica,sans-serif;">Garantía requerida</div>
                                                    <div style="margin-top:2px; font-size:14px; color:#111418; font-weight:700; line-height:1.35; font-family:Arial,Helvetica,sans-serif; word-break:break-word; overflow-wrap:break-word; word-wrap:break-word;">${{ number_format($renta->vehicle->category->warranty, 2) }} MXN</div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                @endif
                            </table>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top:16px;">
                                <tr>
                                    <td style="padding:14px 16px; border:1px solid #e5e7eb; border-radius:12px; background:#fcfcfc;">
                                        <div style="font-size:11px; color:#6b7280; text-transform:uppercase; font-family:Arial,Helvetica,sans-serif;">Detalles</div>
                                        <div style="font-size:14px; color:#111418; font-weight:700; font-family:Arial,Helvetica,sans-serif;">Responde al correo flashcarental@gmail.com: INE o pasaporte, licencia de conducir, reserva de tus vuelos o comprobante de domicilio.</div>
                                        <div style="font-size:14px; color:#111418; font-weight:400; font-family:Arial,Helvetica,sans-serif;">Reply to the email flashcarental@gmail.com with: INE or passport, drivers license, your flight reservation or proof of address.</div>
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin-top:10px;">
                                            <tr>
                                                <!--<td align="center" bgcolor="#ffd60a" style="border-radius:12px;">
                                                    <a href="" style="display:inline-block; padding:10px 16px; font-family:Arial,Helvetica,sans-serif; font-weight:700; font-size:14px; color:#111; text-decoration:none; border:1px solid #b5902a; border-radius:12px;">Responder a Correo</a>
                                                </td>-->
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top:18px;">
                                <tr>
                                    <td style="padding:16px; border-radius:12px; background:#fff7ed; border:1px solid #fed7aa;">
                                        <div style="margin:0 0 6px 0; color:#9a6700; font-weight:700; font-family:Arial,Helvetica,sans-serif;">Pagos y facturación</div>
                                        <div style="margin:0 0 8px 0; color:#6b7280; font-family:Arial,Helvetica,sans-serif; font-size:14px;">Aceptamos pagos 100% digitales. Métodos disponibles:</div>
                                        <div>
                                            <span style="display:inline-block; padding:6px 10px; margin:4px 8px 4px 0; border-radius:999px; background:#111418; color:#fff; font-family:Arial,Helvetica,sans-serif; font-size:12px; font-weight:700;">VISA</span>
                                            <span style="display:inline-block; padding:6px 10px; margin:4px 8px 4px 0; border-radius:999px; background:#e11d48; color:#fff; font-family:Arial,Helvetica,sans-serif; font-size:12px; font-weight:700;">MASTERCARD</span>
                                            <span style="display:inline-block; padding:6px 10px; margin:4px 8px 4px 0; border-radius:999px; background:#2563eb; color:#fff; font-family:Arial,Helvetica,sans-serif; font-size:12px; font-weight:700;">AMEX</span>
                                            <span style="display:inline-block; padding:6px 10px; margin:4px 8px 4px 0; border-radius:999px; background:#16a34a; color:#fff; font-family:Arial,Helvetica,sans-serif; font-size:12px; font-weight:700;">SPEI</span>
                                        </div>
                                        <ul style="margin:10px 0 0 18px; color:#6b7280; font-family:Arial,Helvetica,sans-serif; font-size:13px;">
                                            <li>Factura disponible a solicitud (RFC requerido).</li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>
                            <div style="margin-top:16px; padding:14px; border-radius:12px; background:#f0fdf4; border:1px solid #a7f3d0; color:#065f46; font-weight:700; text-align:center; font-family:Arial,Helvetica,sans-serif;">Todo listo para una experiencia de lujo sobre ruedas.</div>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td align="center" style="padding:18px; color:#9ca3af; font-size:12px; font-family:Arial,Helvetica,sans-serif; background:#111418;">© {{ date('Y') }} Flash Car.</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

