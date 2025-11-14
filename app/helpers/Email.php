<?php
/**
 * Email Helper - ChileChocados
 * Envío de emails con soporte para Gmail/SMTP y templates HTML
 */

namespace App\Helpers;

class Email
{
    /**
     * Enviar email usando configuración SMTP
     * Usa PHPMailer si está disponible, sino fallback a mail()
     * 
     * @param string $to Email destinatario
     * @param string $subject Asunto
     * @param string $template Nombre del template (sin .php)
     * @param array $data Datos para el template
     * @return bool
     */
    public static function send($to, $subject, $template, $data = [])
    {
        // Intentar con PHPMailer primero
        if (class_exists('\PHPMailer\PHPMailer\PHPMailer')) {
            return self::sendWithMailer($to, $subject, $template, $data);
        }
        
        // Fallback a mail() nativo
        try {
            // Cargar configuración
            $config = self::getConfig();
            
            // Obtener contenido del template
            $htmlContent = self::getTemplate($template, $data);
            
            // Configurar headers para HTML
            $headers = self::buildHeaders($config);
            
            // Enviar email
            $sent = mail($to, $subject, $htmlContent, $headers);
            
            // Log
            self::log($to, $subject, $sent ? 'success' : 'failed');
            
            return $sent;
            
        } catch (\Exception $e) {
            self::log($to, $subject, 'error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Enviar email con PHP Mailer (alternativa más robusta)
     * 
     * @param string $to Email destinatario
     * @param string $subject Asunto
     * @param string $template Nombre del template
     * @param array $data Datos para el template
     * @return bool
     */
    public static function sendWithMailer($to, $subject, $template, $data = [])
    {
        try {
            $config = self::getConfig();
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            
            // Configuración SMTP
            $mail->isSMTP();
            $mail->Host = $config['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['username'];
            $mail->Password = str_replace(' ', '', $config['password']); // Remover espacios del password
            $mail->SMTPSecure = $config['encryption'];
            $mail->Port = $config['port'];
            $mail->CharSet = 'UTF-8';
            
            // Debug (solo en desarrollo)
            if (getenv('APP_DEBUG') === 'true') {
                $mail->SMTPDebug = 0; // 0=off, 1=client, 2=client+server
            }
            
            // Remitente y destinatario
            $mail->setFrom($config['from_address'], $config['from_name']);
            $mail->addAddress($to);
            
            // Contenido
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = self::getTemplate($template, $data);
            
            // Texto alternativo (para clientes que no soportan HTML)
            $mail->AltBody = strip_tags(self::getTemplate($template, $data));
            
            // Enviar
            $sent = $mail->send();
            
            self::log($to, $subject, $sent ? 'success (PHPMailer)' : 'failed (PHPMailer)');
            
            return $sent;
            
        } catch (\Exception $e) {
            self::log($to, $subject, 'error (PHPMailer): ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Construir headers para email HTML
     * 
     * @param array $config Configuración
     * @return string
     */
    private static function buildHeaders($config)
    {
        $headers = [];
        
        // MIME Version
        $headers[] = 'MIME-Version: 1.0';
        
        // Content-Type para HTML
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        
        // From
        $headers[] = "From: {$config['from_name']} <{$config['from_address']}>";
        
        // Reply-To
        $headers[] = "Reply-To: {$config['from_address']}";
        
        // X-Mailer
        $headers[] = 'X-Mailer: PHP/' . phpversion();
        
        return implode("\r\n", $headers);
    }
    
    /**
     * Obtener configuración de email desde archivo config o .env
     * 
     * @return array
     */
    private static function getConfig()
    {
        // Intentar cargar desde archivo de configuración
        $configFile = __DIR__ . '/../config/mail.php';
        
        if (file_exists($configFile)) {
            return require $configFile;
        }
        
        // Fallback a variables de entorno
        return [
            'driver' => getenv('MAIL_DRIVER') ?: 'smtp',
            'host' => getenv('MAIL_HOST') ?: 'smtp.gmail.com',
            'port' => getenv('MAIL_PORT') ?: 587,
            'username' => getenv('MAIL_USERNAME') ?: '',
            'password' => getenv('MAIL_PASSWORD') ?: '',
            'encryption' => getenv('MAIL_ENCRYPTION') ?: 'tls',
            'from_address' => getenv('MAIL_FROM_ADDRESS') ?: 'noreply@chilechocados.cl',
            'from_name' => getenv('MAIL_FROM_NAME') ?: 'ChileChocados',
        ];
    }
    
    /**
     * Obtener template de email y reemplazar variables
     * 
     * @param string $templateName Nombre del template
     * @param array $data Datos para reemplazar
     * @return string HTML del email
     */
    private static function getTemplate($templateName, $data = [])
    {
        // Buscar template en views/emails/
        $templatePath = __DIR__ . "/../views/emails/{$templateName}.php";
        
        if (!file_exists($templatePath)) {
            // Template genérico si no existe
            return self::getGenericTemplate($data);
        }
        
        // Agregar variables globales al data
        $data['app_url'] = getenv('APP_URL') ?: 'http://localhost';
        $data['app_name'] = getenv('APP_NAME') ?: 'ChileChocados';
        
        // Capturar output del template
        ob_start();
        extract($data);
        include $templatePath;
        $html = ob_get_clean();
        
        // Reemplazar variables adicionales
        $html = self::replaceVariables($html, $data);
        
        return $html;
    }
    
    /**
     * Reemplazar variables en el template estilo {{ variable }}
     * 
     * @param string $html HTML del template
     * @param array $data Datos
     * @return string
     */
    private static function replaceVariables($html, $data)
    {
        foreach ($data as $key => $value) {
            // Reemplazar {{ variable }}
            $html = str_replace('{{ ' . $key . ' }}', $value, $html);
            $html = str_replace('{{' . $key . '}}', $value, $html);
        }
        
        return $html;
    }
    
    /**
     * Template genérico para emails
     * 
     * @param array $data Datos
     * @return string
     */
    private static function getGenericTemplate($data)
    {
        $appName = getenv('APP_NAME') ?: 'ChileChocados';
        $appUrl = getenv('APP_URL') ?: 'http://localhost';
        
        $title = $data['title'] ?? 'Notificación';
        $message = $data['message'] ?? '';
        $actionUrl = $data['action_url'] ?? '';
        $actionText = $data['action_text'] ?? 'Ver más';
        
        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { background-color: #E6332A; color: #ffffff; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px; }
        .content h2 { color: #E6332A; margin-top: 0; }
        .button { display: inline-block; padding: 12px 30px; background-color: #E6332A; color: #ffffff; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .button:hover { background-color: #c72a22; }
        .footer { background-color: #f8f8f8; padding: 20px; text-align: center; font-size: 12px; color: #666; }
        .footer a { color: #E6332A; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{$appName}</h1>
        </div>
        <div class="content">
            <h2>{$title}</h2>
            <p>{$message}</p>
HTML;
        
        if ($actionUrl) {
            $html .= '<a href="' . $actionUrl . '" class="button">' . $actionText . '</a>';
        }
        
        $html .= <<<HTML
        </div>
        <div class="footer">
            <p>&copy; 2025 {$appName}. Todos los derechos reservados.</p>
            <p><a href="{$appUrl}">Visitar sitio web</a></p>
        </div>
    </div>
</body>
</html>
HTML;
        
        return $html;
    }
    
    /**
     * Registrar log de email enviado
     * 
     * @param string $to Destinatario
     * @param string $subject Asunto
     * @param string $status Estado (success, failed, error)
     */
    private static function log($to, $subject, $status)
    {
        $logDir = __DIR__ . '/../../logs';
        $logFile = $logDir . '/email.log';
        
        // Crear directorio si no existe
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[{$timestamp}] TO: {$to} | SUBJECT: {$subject} | STATUS: {$status}\n";
        
        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }
    
    /**
     * Enviar email de verificación de cuenta
     * 
     * @param string $to Email destinatario
     * @param string $name Nombre del usuario
     * @param string $token Token de verificación
     * @return bool
     */
    public static function sendVerificationEmail($to, $name, $token)
    {
        $appUrl = getenv('APP_URL') ?: 'http://localhost';
        $verifyUrl = "{$appUrl}/auth/verify-email/{$token}";
        
        $data = [
            'name' => $name,
            'verify_url' => $verifyUrl,
            'token' => $token
        ];
        
        return self::send($to, 'Verifica tu cuenta - ChileChocados', 'verify-email', $data);
    }
    
    /**
     * Enviar email de recuperación de contraseña
     * 
     * @param string $to Email destinatario
     * @param string $name Nombre del usuario
     * @param string $token Token de recuperación
     * @return bool
     */
    public static function sendPasswordResetEmail($to, $name, $token)
    {
        $appUrl = getenv('APP_URL') ?: 'http://localhost';
        $resetUrl = "{$appUrl}/auth/reset-password/{$token}";
        
        $data = [
            'name' => $name,
            'reset_url' => $resetUrl,
            'token' => $token
        ];
        
        return self::send($to, 'Recuperar contraseña - ChileChocados', 'reset-password', $data);
    }
}
