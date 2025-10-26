<?php
/**
 * Test Email - ChileChocados
 * Script para probar configuración SMTP/Gmail
 * 
 * Uso: php test_email.php
 */

require_once __DIR__ . '/app/helpers/Email.php';

use App\Helpers\Email;

echo "========================================\n";
echo "   TEST EMAIL - CHILECHOCADOS\n";
echo "========================================\n\n";

// Cargar variables de .env
if (file_exists('.env')) {
    $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($key, $value) = explode('=', $line, 2);
        putenv(trim($key) . '=' . trim($value));
    }
    echo "✓ Archivo .env cargado\n\n";
} else {
    echo "✗ ERROR: Archivo .env no encontrado\n";
    echo "  Copia .env.example a .env y configura tus credenciales\n";
    exit(1);
}

// Verificar configuración
echo "Verificando configuración SMTP:\n";
echo "-------------------------------\n";

$mailHost = getenv('MAIL_HOST');
$mailPort = getenv('MAIL_PORT');
$mailUsername = getenv('MAIL_USERNAME');
$mailPassword = getenv('MAIL_PASSWORD');
$mailFrom = getenv('MAIL_FROM_ADDRESS');
$appUrl = getenv('APP_URL');

if (empty($mailHost) || empty($mailUsername) || empty($mailPassword)) {
    echo "✗ ERROR: Faltan configuraciones SMTP en .env\n";
    echo "\nRevisa que .env contenga:\n";
    echo "  MAIL_HOST=smtp.gmail.com\n";
    echo "  MAIL_PORT=587\n";
    echo "  MAIL_USERNAME=tu_email@gmail.com\n";
    echo "  MAIL_PASSWORD=tu_app_password\n";
    echo "  MAIL_FROM_ADDRESS=noreply@chilechocados.cl\n";
    exit(1);
}

echo "  Host: $mailHost\n";
echo "  Port: $mailPort\n";
echo "  User: $mailUsername\n";
echo "  From: $mailFrom\n\n";

// Solicitar email de destino
echo "Ingresa el email de destino para la prueba:\n";
echo "(presiona Enter para usar $mailUsername): ";
$testEmail = trim(fgets(STDIN));

if (empty($testEmail)) {
    $testEmail = $mailUsername;
}

echo "\n";
echo "========================================\n";
echo "  ENVIANDO EMAIL DE PRUEBA\n";
echo "========================================\n\n";

echo "Destinatario: $testEmail\n";
echo "Enviando...\n\n";

// Test 1: Email de verificación
echo "1. Probando email de verificación...\n";

$result1 = Email::send(
    $testEmail,
    'Test: Verifica tu cuenta - ChileChocados',
    'verify-email',
    [
        'nombre' => 'Usuario de Prueba',
        'verify_link' => $appUrl . '/verificar-email/test-token-123'
    ]
);

if ($result1) {
    echo "   ✓ Email de verificación enviado exitosamente\n\n";
} else {
    echo "   ✗ Error al enviar email de verificación\n\n";
}

// Test 2: Email de recuperación
echo "2. Probando email de recuperación de contraseña...\n";

$result2 = Email::send(
    $testEmail,
    'Test: Recuperar contraseña - ChileChocados',
    'reset-password',
    [
        'nombre' => 'Usuario de Prueba',
        'reset_link' => $appUrl . '/reset-password/test-token-456'
    ]
);

if ($result2) {
    echo "   ✓ Email de recuperación enviado exitosamente\n\n";
} else {
    echo "   ✗ Error al enviar email de recuperación\n\n";
}

// Resultados
echo "========================================\n";
echo "  RESULTADOS\n";
echo "========================================\n\n";

if ($result1 && $result2) {
    echo "✓ ÉXITO: Todos los emails fueron enviados correctamente\n";
    echo "\nRevisa tu bandeja de entrada en: $testEmail\n";
    echo "(Si no los ves, revisa la carpeta de spam)\n\n";
    echo "✓ Configuración SMTP correcta\n";
    echo "✓ El sistema de emails está listo para usar\n";
} else {
    echo "✗ ERROR: Algunos emails no pudieron ser enviados\n\n";
    
    echo "Posibles causas:\n";
    echo "  1. App Password incorrecto o expirado\n";
    echo "  2. Verificación en 2 pasos no activada en Gmail\n";
    echo "  3. Cuenta Gmail bloqueada por seguridad\n";
    echo "  4. Firewall bloqueando conexión SMTP\n\n";
    
    echo "Soluciones:\n";
    echo "  1. Genera un nuevo App Password en:\n";
    echo "     https://myaccount.google.com/apppasswords\n";
    echo "  2. Verifica que 2FA esté activo en:\n";
    echo "     https://myaccount.google.com/security\n";
    echo "  3. Revisa el log de emails en: logs/email.log\n\n";
}

echo "========================================\n";
echo "  FIN DEL TEST\n";
echo "========================================\n";
