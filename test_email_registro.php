<?php
/**
 * Script de prueba para validar envío de emails de registro
 * ChileChocados
 */

require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/helpers/Email.php';

use App\Helpers\Email;

echo "===========================================\n";
echo "TEST DE ENVÍO DE EMAIL - REGISTRO\n";
echo "===========================================\n\n";

// Datos de prueba
$testEmail = 'feasypuppy@gmail.com';
$testNombre = 'Usuario Prueba';
$testToken = bin2hex(random_bytes(32));

echo "📧 Email destino: {$testEmail}\n";
echo "👤 Nombre: {$testNombre}\n";
echo "🔑 Token generado: " . substr($testToken, 0, 20) . "...\n\n";

// Verificar configuración de correo
echo "===========================================\n";
echo "CONFIGURACIÓN DE CORREO\n";
echo "===========================================\n";
echo "MAIL_HOST: " . (getenv('MAIL_HOST') ?: 'NO CONFIGURADO') . "\n";
echo "MAIL_PORT: " . (getenv('MAIL_PORT') ?: 'NO CONFIGURADO') . "\n";
echo "MAIL_USERNAME: " . (getenv('MAIL_USERNAME') ?: 'NO CONFIGURADO') . "\n";
echo "MAIL_PASSWORD: " . (getenv('MAIL_PASSWORD') ? '****** (configurado)' : 'NO CONFIGURADO') . "\n";
echo "MAIL_ENCRYPTION: " . (getenv('MAIL_ENCRYPTION') ?: 'NO CONFIGURADO') . "\n";
echo "MAIL_FROM_ADDRESS: " . (getenv('MAIL_FROM_ADDRESS') ?: 'NO CONFIGURADO') . "\n";
echo "MAIL_FROM_NAME: " . (getenv('MAIL_FROM_NAME') ?: 'NO CONFIGURADO') . "\n";
echo "APP_URL: " . (getenv('APP_URL') ?: 'NO CONFIGURADO') . "\n\n";

// Verificar que la configuración esté completa
$configOk = true;
$missingConfig = [];

if (!getenv('MAIL_HOST')) {
    $configOk = false;
    $missingConfig[] = 'MAIL_HOST';
}
if (!getenv('MAIL_USERNAME')) {
    $configOk = false;
    $missingConfig[] = 'MAIL_USERNAME';
}
if (!getenv('MAIL_PASSWORD')) {
    $configOk = false;
    $missingConfig[] = 'MAIL_PASSWORD';
}

if (!$configOk) {
    echo "❌ ERROR: Configuración incompleta\n";
    echo "Faltan las siguientes variables en .env:\n";
    foreach ($missingConfig as $var) {
        echo "  - {$var}\n";
    }
    echo "\n";
    exit(1);
}

echo "✅ Configuración de correo completa\n\n";

// Verificar que exista el template de email
$templatePath = __DIR__ . '/app/views/emails/verify-email.php';
echo "===========================================\n";
echo "VERIFICACIÓN DE TEMPLATE\n";
echo "===========================================\n";
echo "Buscando template en: {$templatePath}\n";

if (file_exists($templatePath)) {
    echo "✅ Template encontrado\n\n";
} else {
    echo "⚠️  Template no encontrado, se usará template genérico\n\n";
}

// Preparar datos para el email
$verifyLink = getenv('APP_URL') . '/verificar-email/' . $testToken;

$emailData = [
    'nombre' => $testNombre,
    'verify_link' => $verifyLink,
    'token' => $testToken
];

echo "===========================================\n";
echo "DATOS DEL EMAIL\n";
echo "===========================================\n";
echo "Asunto: Verifica tu cuenta en ChileChocados\n";
echo "Link de verificación: {$verifyLink}\n\n";

// Intentar enviar el email
echo "===========================================\n";
echo "ENVIANDO EMAIL...\n";
echo "===========================================\n\n";

try {
    $sent = Email::send(
        $testEmail,
        'Verifica tu cuenta en ChileChocados',
        'verify-email',
        $emailData
    );
    
    if ($sent) {
        echo "✅ EMAIL ENVIADO EXITOSAMENTE!\n\n";
        echo "===========================================\n";
        echo "RESULTADO\n";
        echo "===========================================\n";
        echo "✓ El email fue enviado a: {$testEmail}\n";
        echo "✓ Revisa tu bandeja de entrada\n";
        echo "✓ Si no lo ves, revisa la carpeta de SPAM\n\n";
        
        // Verificar log
        $logFile = __DIR__ . '/logs/email.log';
        if (file_exists($logFile)) {
            echo "📋 Log de emails:\n";
            $logContent = file_get_contents($logFile);
            $lines = explode("\n", $logContent);
            $lastLines = array_slice($lines, -5);
            foreach ($lastLines as $line) {
                if (!empty(trim($line))) {
                    echo "   {$line}\n";
                }
            }
        }
        
    } else {
        echo "❌ ERROR: No se pudo enviar el email\n\n";
        echo "Posibles causas:\n";
        echo "1. Credenciales de Gmail incorrectas\n";
        echo "2. No has generado una 'Contraseña de aplicación' en Gmail\n";
        echo "3. Verificación en 2 pasos no activada en Gmail\n";
        echo "4. Firewall bloqueando puerto 587\n\n";
        
        echo "Pasos para configurar Gmail:\n";
        echo "1. Ve a: https://myaccount.google.com/security\n";
        echo "2. Activa 'Verificación en 2 pasos'\n";
        echo "3. Ve a: https://myaccount.google.com/apppasswords\n";
        echo "4. Genera una contraseña de aplicación\n";
        echo "5. Copia el código de 16 caracteres a MAIL_PASSWORD en .env\n\n";
        
        // Verificar log de errores
        $logFile = __DIR__ . '/logs/email.log';
        if (file_exists($logFile)) {
            echo "📋 Últimas líneas del log:\n";
            $logContent = file_get_contents($logFile);
            $lines = explode("\n", $logContent);
            $lastLines = array_slice($lines, -5);
            foreach ($lastLines as $line) {
                if (!empty(trim($line))) {
                    echo "   {$line}\n";
                }
            }
        }
    }
    
} catch (Exception $e) {
    echo "❌ EXCEPCIÓN CAPTURADA:\n";
    echo "   " . $e->getMessage() . "\n\n";
    echo "   Archivo: " . $e->getFile() . "\n";
    echo "   Línea: " . $e->getLine() . "\n\n";
}

echo "\n===========================================\n";
echo "FIN DE LA PRUEBA\n";
echo "===========================================\n";
