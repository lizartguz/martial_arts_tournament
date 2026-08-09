<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer contraseña</title>
</head>
<body style="margin:0;padding:0;background:#eef7f1;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#eef7f1;margin:0;padding:28px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:620px;background:#ffffff;border-radius:22px;overflow:hidden;box-shadow:0 18px 45px rgba(22,101,52,0.16);">
                    <tr>
                        <td style="background:linear-gradient(135deg,#0f5132,#18a058);padding:34px 34px 30px;text-align:center;">
                            <div style="display:inline-block;background:#ffffff;border-radius:18px;padding:12px 20px;box-shadow:0 12px 30px rgba(15,81,50,0.22);">
                                <img src="{{ asset('images/logo_rectangular.png') }}" alt="Senvatec" width="172" style="display:block;border:0;max-width:172px;height:auto;">
                            </div>
                            <div style="margin-top:16px;display:inline-block;background:rgba(255,255,255,0.14);border:1px solid rgba(255,255,255,0.28);border-radius:999px;padding:7px 16px;color:#dcfce7;font-size:12px;font-weight:bold;letter-spacing:.08em;text-transform:uppercase;">
                                Seguridad de cuenta
                            </div>
                            <h1 style="margin:16px 0 8px;color:#ffffff;font-size:27px;line-height:1.25;font-weight:800;">
                                Restablece tu contraseña
                            </h1>
                            <p style="margin:0;color:#d1fae5;font-size:15px;line-height:1.6;">
                                Acceso seguro para continuar monitoreando tus estaciones, datos y avisos.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:34px;">
                            <p style="margin:0 0 18px;font-size:17px;line-height:1.65;color:#111827;">
                                Hola{{ $userName ? ' ' . $userName : '' }},
                            </p>

                            <p style="margin:0 0 22px;font-size:16px;line-height:1.75;color:#4b5563;">
                                Recibimos una solicitud para restablecer la contraseña de tu cuenta. Pulsa el botón verde para crear una nueva contraseña de forma segura.
                            </p>

                            <table role="presentation" cellspacing="0" cellpadding="0" style="margin:30px auto;">
                                <tr>
                                    <td align="center" style="border-radius:14px;background:#16a34a;box-shadow:0 10px 24px rgba(22,163,74,0.32);">
                                        <a href="{{ $resetUrl }}" target="_blank" rel="noopener" style="display:inline-block;padding:15px 26px;color:#ffffff;text-decoration:none;font-size:15px;font-weight:700;border-radius:14px;">
                                            Restablecer contraseña
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:16px;padding:16px 18px;margin:26px 0;">
                                <p style="margin:0;color:#166534;font-size:14px;line-height:1.65;">
                                    Si no solicitaste este cambio, puedes ignorar este correo. Tu contraseña actual seguirá siendo válida.
                                </p>
                            </div>

                            <p style="margin:0 0 10px;font-size:14px;line-height:1.7;color:#6b7280;">
                                Si el botón no funciona, copia y pega este enlace en tu navegador:
                            </p>
                            <p style="margin:0;word-break:break-all;font-size:13px;line-height:1.6;">
                                <a href="{{ $resetUrl }}" target="_blank" rel="noopener" style="color:#15803d;text-decoration:underline;">
                                    {{ $resetUrl }}
                                </a>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:22px 34px 30px;background:#f8fafc;border-top:1px solid #e5e7eb;text-align:center;">
                            <p style="margin:0;color:#64748b;font-size:13px;line-height:1.6;">
                                Atentamente,<br>
                                <strong style="color:#166534;">El equipo de Senvatec</strong>
                            </p>
                            <p style="margin:14px 0 0;color:#94a3b8;font-size:12px;">
                                © {{ date('Y') }} Senvatec. Todos los derechos reservados.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
