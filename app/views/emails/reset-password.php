<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña - ChileChocados</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif; background-color: #f5f5f5;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f5f5f5;">
        <tr>
            <td style="padding: 40px 20px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="padding: 40px 40px 20px; text-align: center; background: linear-gradient(135deg, #E6332A 0%, #c42a23 100%); border-radius: 12px 12px 0 0;">
                            <img src="{{app_url}}/assets/images/logo-white.png" alt="ChileChocados" style="height: 50px; margin-bottom: 20px;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 700;">Recuperar Contraseña</h1>
                        </td>
                    </tr>

                    <!-- Contenido -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="margin: 0 0 20px; color: #2E2E2E; font-size: 16px; line-height: 1.6;">
                                Hola <strong>{{nombre}}</strong>,
                            </p>
                            
                            <p style="margin: 0 0 20px; color: #666; font-size: 16px; line-height: 1.6;">
                                Recibimos una solicitud para restablecer la contraseña de tu cuenta en ChileChocados.
                            </p>

                            <p style="margin: 0 0 30px; color: #666; font-size: 16px; line-height: 1.6;">
                                Si solicitaste este cambio, haz clic en el siguiente botón para crear una nueva contraseña:
                            </p>

                            <!-- Botón -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="text-align: center; padding: 0 0 30px;">
                                        <a href="{{reset_link}}" style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #E6332A 0%, #c42a23 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 12px rgba(230, 51, 42, 0.3);">
                                            Restablecer contraseña
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Link alternativo -->
                            <p style="margin: 0 0 30px; color: #999; font-size: 14px; line-height: 1.6; text-align: center;">
                                Si el botón no funciona, copia y pega este enlace en tu navegador:<br>
                                <a href="{{reset_link}}" style="color: #E6332A; word-break: break-all;">{{reset_link}}</a>
                            </p>

                            <!-- Info box -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #fff3cd; border: 2px solid #ffeaa7; border-radius: 8px; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 10px; color: #2E2E2E; font-size: 14px; font-weight: 600;">
                                            ⏰ Importante:
                                        </p>
                                        <p style="margin: 0; color: #666; font-size: 14px; line-height: 1.6;">
                                            Este enlace de recuperación expirará en <strong>1 hora</strong> por razones de seguridad. Si necesitas más tiempo, solicita un nuevo enlace.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Seguridad -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8d7da; border: 2px solid #f5c6cb; border-radius: 8px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 10px; color: #721c24; font-size: 14px; font-weight: 600;">
                                            🔒 ¿No solicitaste este cambio?
                                        </p>
                                        <p style="margin: 0; color: #721c24; font-size: 14px; line-height: 1.6;">
                                            Si <strong>NO solicitaste</strong> restablecer tu contraseña, <strong>ignora este correo</strong>. Tu cuenta está segura y no se realizarán cambios.
                                        </p>
                                        <p style="margin: 10px 0 0; color: #721c24; font-size: 14px; line-height: 1.6;">
                                            Si crees que alguien está intentando acceder a tu cuenta, contáctanos inmediatamente en <a href="mailto:seguridad@chilechocados.cl" style="color: #E6332A; text-decoration: none;">seguridad@chilechocados.cl</a>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 30px 40px; border-top: 1px solid #e0e0e0; text-align: center;">
                            <p style="margin: 0 0 15px; color: #2E2E2E; font-size: 16px; font-weight: 600;">
                                ¿Necesitas ayuda?
                            </p>
                            <p style="margin: 0 0 20px; color: #666; font-size: 14px;">
                                Contáctanos en <a href="mailto:soporte@chilechocados.cl" style="color: #E6332A; text-decoration: none;">soporte@chilechocados.cl</a>
                            </p>

                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="text-align: center; padding-bottom: 20px;">
                                        <a href="{{app_url}}" style="display: inline-block; margin: 0 10px;">
                                            <img src="{{app_url}}/assets/images/social/facebook.png" alt="Facebook" style="width: 32px; height: 32px;">
                                        </a>
                                        <a href="{{app_url}}" style="display: inline-block; margin: 0 10px;">
                                            <img src="{{app_url}}/assets/images/social/instagram.png" alt="Instagram" style="width: 32px; height: 32px;">
                                        </a>
                                        <a href="{{app_url}}" style="display: inline-block; margin: 0 10px;">
                                            <img src="{{app_url}}/assets/images/social/twitter.png" alt="Twitter" style="width: 32px; height: 32px;">
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0; color: #999; font-size: 12px; line-height: 1.5;">
                                © 2025 ChileChocados. Todos los derechos reservados.<br>
                                Este es un correo automático, por favor no respondas a este mensaje.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
