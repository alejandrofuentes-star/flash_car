<?php
$rentaLabel = isset($renta) && isset($renta->id) ? 'Renta #' . str_pad($renta->id, 5, '0', STR_PAD_LEFT) : 'Reserva confirmada';
$mail->Subject = 'Reservaciones Flash Car';
$mail->Body= '
                <!DOCTYPE html>
                <html lang="es">
                <head>
                    <meta charset="UTF-8"> 
                </head>
                    <body>
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#e5e5e5;">
                            <tr>
                                <td align="center" style="padding:24px;"> 
                                    <table role="presentation" width="640" cellspacing="0" cellpadding="0" border="0" style="max-width:640px; width:100%; background:#111418; border radius:16px; overflow:hidden; border:1px solid #22262b;"> 
                                        <!-- Hero --> 
                                          <tr> 
                                            <td align="center" style="padding:28px 24px; background:#111418;"> 
                                              <!-- Banda gris clara ajustada al tamaño del logo --> 
                                              <div style="display:inline-block; background:#fafbfc; background-image:linear gradient(180deg,#ffffff,#f3f4f6); padding:8px 12px; border-radius:14px;"> 
                                                <img src="https://flashcar.rentadeautos.site/img/logo_correo.jpg" alt="Flash Car" width="220" style="display:block; border:0; outline:none; text-decoration:none; height:auto;"> 
                                              </div> 
                                              <div style="margin-top:16px; color:#ffffff; font-family:Arial,Helvetica,sans-serif; font-size:22px; font-weight:700; letter-spacing: 0.3px;">Confirmación de Renta de Vehículo</div> 
                                              <div style="margin-top:8px; display:inline-block; padding:6px 12px; border-radius:999px; background:#ffd60a; color:#111; font-weight:700; font-size:12px; font-family:Arial,Helvetica,sans-serif;">'.$rentaLabel.'</div> 
                                            </td> 
                                          </tr> 
                                        <!-- Intro --> 
                                        <tr>
                                            <td style="background:#ffffff; padding:24px;"> 
                                                <p style="margin:0 0 16px 0; color:#3b4046; font-family:Arial,Helvetica,sans serif; font-size:14px;">Tu reserva ha sido confirmada correctamente. Aquí están los detalles:</p>
                                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"> 
                                                
                                                     '.$filas.'
                                                    
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
                                                            <div style="margin:0 0 8px 0; color:#6b7280; font-family:Arial,Helvetica,sansserif; font-size:14px;">Aceptamos pagos 100% digitales. Métodos disponibles:</div> 
                                                            <div> 
                                                                <span style="display:inline-block; padding:6px 10px; margin:4px 8px 4px 0; border-radius:999px; background:#111418; color:#fff; font-family:Arial,Helvetica,sans-serif; font-size:12px; font-weight:700;">VISA</span> 
                                                                <span style="display:inline-block; padding:6px 10px; margin:4px 8px 4px 0; border-radius:999px; background:#e11d48; color:#fff; font-family:Arial,Helvetica,sans-serif; font-size:12px; fontweight:700;">MASTERCARD</span> 
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
                                                <tr> 
                                                    <td align="center" style="padding:18px; color:#9ca3af; font-size:12px; font-family:Arial,Helvetica,sans-serif; background:#111418;">© 2025 Flash Car.</td> 
                                                </tr>
                                            </td> 
                                        </tr>
                                    </table> 
                                </td>
                            <tr>
                        </table>
                    </body>
                </html>
            '; 
?>
