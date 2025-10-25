<?php
/**
 * Mail Configuration - ChileChocados
 * Configuración para envío de emails vía SMTP (Gmail)
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Mail Driver
    |--------------------------------------------------------------------------
    | Opciones: smtp, mail, sendmail
    */
    'driver' => getenv('MAIL_DRIVER') ?: 'smtp',
    
    /*
    |--------------------------------------------------------------------------
    | SMTP Host
    |--------------------------------------------------------------------------
    | Gmail: smtp.gmail.com
    | Otros: consultar documentación del proveedor
    */
    'host' => getenv('MAIL_HOST') ?: 'smtp.gmail.com',
    
    /*
    |--------------------------------------------------------------------------
    | SMTP Port
    |--------------------------------------------------------------------------
    | Gmail TLS: 587
    | Gmail SSL: 465
    */
    'port' => getenv('MAIL_PORT') ?: 587,
    
    /*
    |--------------------------------------------------------------------------
    | SMTP Username (Email)
    |--------------------------------------------------------------------------
    | Tu email de Gmail completo
    */
    'username' => getenv('MAIL_USERNAME') ?: '',
    
    /*
    |--------------------------------------------------------------------------
    | SMTP Password
    |--------------------------------------------------------------------------
    | IMPORTANTE: Para Gmail usar "App Password" (Contraseña de aplicación)
    | No uses tu contraseña normal de Gmail
    | 
    | Generar App Password:
    | 1. Ir a: https://myaccount.google.com/apppasswords
    | 2. Activar verificación en 2 pasos si no está activada
    | 3. Crear nueva contraseña de aplicación
    | 4. Copiar el código de 16 caracteres
    */
    'password' => getenv('MAIL_PASSWORD') ?: '',
    
    /*
    |--------------------------------------------------------------------------
    | Encryption
    |--------------------------------------------------------------------------
    | Opciones: tls, ssl
    | Gmail recomienda: tls
    */
    'encryption' => getenv('MAIL_ENCRYPTION') ?: 'tls',
    
    /*
    |--------------------------------------------------------------------------
    | From Address
    |--------------------------------------------------------------------------
    | Email que aparecerá como remitente
    */
    'from_address' => getenv('MAIL_FROM_ADDRESS') ?: 'noreply@chilechocados.cl',
    
    /*
    |--------------------------------------------------------------------------
    | From Name
    |--------------------------------------------------------------------------
    | Nombre que aparecerá como remitente
    */
    'from_name' => getenv('MAIL_FROM_NAME') ?: 'ChileChocados',
    
    /*
    |--------------------------------------------------------------------------
    | Charset
    |--------------------------------------------------------------------------
    */
    'charset' => 'UTF-8',
    
    /*
    |--------------------------------------------------------------------------
    | Debug Mode
    |--------------------------------------------------------------------------
    | Habilitar debug SMTP (solo desarrollo)
    | 0 = No debug
    | 1 = Mensajes del cliente
    | 2 = Mensajes del cliente y servidor
    | 3 = 2 + información de conexión
    | 4 = 3 + datos de bajo nivel
    */
    'debug' => getenv('MAIL_DEBUG') ?: 0,
    
    /*
    |--------------------------------------------------------------------------
    | Timeout
    |--------------------------------------------------------------------------
    | Tiempo máximo de espera en segundos
    */
    'timeout' => 30,
];
