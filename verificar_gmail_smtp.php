<?php
/**
 * Script interactivo para verificar configuración de Gmail SMTP
 * ChileChocados
 */

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║  VERIFICACIÓN DE CREDENCIALES GMAIL SMTP                   ║\n";
echo "║  ChileChocados                                             ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\n";

// Cargar configuración
require_once __DIR__ . '/app/config/config.php';

echo "📋 PASO 1: Verificar archivo .env\n";
echo "═══════════════════════════════════════════════════════════\n";

$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    echo "✅ Archivo .env encontrado\n\n";
} else {
    echo "❌ Archivo .env NO encontrado\n";
    echo "   Copia .env.example a .env y configúralo\n\n";
    exit(1);
}

echo "📧 PASO 2: Leer configuración de correo\n";
echo "═══════════════════════════════════════════════════════════\n";

$config = [
    'host' => getenv('MAIL_HOST'),
    'port' => getenv('MAIL_PORT'),
    'username' => getenv('MAIL_USERNAME'),
    'password' => getenv('MAIL_PASSWORD'),
    'encryption' => getenv('MAIL_ENCRYPTION'),
];

echo "Host:       " . ($config['host'] ?: '❌ NO CONFIGURADO') . "\n";
echo "Port:       " . ($config['port'] ?: '❌ NO CONFIGURADO') . "\n";
echo "Username:   " . ($config['username'] ?: '❌ NO CONFIGURADO') . "\n";
echo "Password:   " . ($config['password'] ? str_repeat('*', strlen($config['password'])) . " (" . strlen($config['password']) . " caracteres)" : '❌ NO CONFIGURADO') . "\n";
echo "Encryption: " . ($config['encryption'] ?: '❌ NO CONFIGURADO') . "\n";
echo "\n";

// Verificar configuración básica
$errors = [];

if (empty($config['host'])) $errors[] = "MAIL_HOST no configurado";
if (empty($config['port'])) $errors[] = "MAIL_PORT no configurado";
if (empty($config['username'])) $errors[] = "MAIL_USERNAME no configurado";
if (empty($config['password'])) $errors[] = "MAIL_PASSWORD no configurado";
if (empty($config['encryption'])) $errors[] = "MAIL_ENCRYPTION no configurado";

if (!empty($errors)) {
    echo "❌ ERRORES DE CONFIGURACIÓN:\n";
    foreach ($errors as $error) {
        echo "   • {$error}\n";
    }
    echo "\n";
    exit(1);
}

echo "🔍 PASO 3: Verificar formato de credenciales\n";
echo "═══════════════════════════════════════════════════════════\n";

// Verificar host
if ($config['host'] !== 'smtp.gmail.com') {
    echo "⚠️  Host incorrecto: {$config['host']}\n";
    echo "   Debe ser: smtp.gmail.com\n";
} else {
    echo "✅ Host correcto: smtp.gmail.com\n";
}

// Verificar puerto
if ($config['port'] != 587 && $config['port'] != 465) {
    echo "⚠️  Puerto inusual: {$config['port']}\n";
    echo "   Recomendado: 587 (TLS) o 465 (SSL)\n";
} else {
    echo "✅ Puerto correcto: {$config['port']}\n";
}

// Verificar email
if (!filter_var($config['username'], FILTER_VALIDATE_EMAIL)) {
    echo "❌ Email inválido: {$config['username']}\n";
} else {
    echo "✅ Email válido: {$config['username']}\n";
}

// Verificar password
$hasSpaces = strpos($config['password'], ' ') !== false;
if ($hasSpaces) {
    echo "❌ Password contiene ESPACIOS\n";
    echo "   Password actual: {$config['password']}\n";
    echo "   Password sin espacios: " . str_replace(' ', '', $config['password']) . "\n";
    echo "\n";
    echo "   ⚠️  ACCIÓN REQUERIDA:\n";
    echo "   1. Abre el archivo .env\n";
    echo "   2. Busca: MAIL_PASSWORD={$config['password']}\n";
    echo "   3. Cámbialo a: MAIL_PASSWORD=" . str_replace(' ', '', $config['password']) . "\n";
    echo "   4. Guarda el archivo\n";
    echo "   5. Ejecuta este script nuevamente\n";
    echo "\n";
    exit(1);
} else {
    echo "✅ Password sin espacios\n";
}

// Verificar longitud del password
$passwordLength = strlen($config['password']);
if ($passwordLength != 16) {
    echo "⚠️  Longitud del password: {$passwordLength} caracteres\n";
    echo "   Los passwords de aplicación de Gmail tienen 16 caracteres\n";
    echo "   Si es diferente, puede que no sea un password de aplicación válido\n";
} else {
    echo "✅ Longitud del password: 16 caracteres (correcto)\n";
}

// Verificar encryption
if ($config['encryption'] !== 'tls' && $config['encryption'] !== 'ssl') {
    echo "⚠️  Encryption inusual: {$config['encryption']}\n";
    echo "   Recomendado: tls o ssl\n";
} else {
    echo "✅ Encryption correcto: {$config['encryption']}\n";
}

echo "\n";

echo "🔌 PASO 4: Probar conexión al servidor SMTP\n";
echo "═══════════════════════════════════════════════════════════\n";

// Test de conexión
$connection = @fsockopen($config['host'], $config['port'], $errno, $errstr, 10);

if ($connection) {
    echo "✅ Conexión exitosa a {$config['host']}:{$config['port']}\n";
    fclose($connection);
} else {
    echo "❌ No se pudo conectar a {$config['host']}:{$config['port']}\n";
    echo "   Error: {$errstr} ({$errno})\n";
    echo "\n";
    echo "   Posibles causas:\n";
    echo "   • Firewall bloqueando el puerto\n";
    echo "   • Sin conexión a internet\n";
    echo "   • Puerto incorrecto\n";
    echo "\n";
    exit(1);
}

echo "\n";

echo "🔐 PASO 5: Verificar extensiones PHP necesarias\n";
echo "═══════════════════════════════════════════════════════════\n";

// Verificar OpenSSL
if (extension_loaded('openssl')) {
    echo "✅ OpenSSL: Instalado\n";
} else {
    echo "❌ OpenSSL: NO instalado (requerido para TLS/SSL)\n";
}

// Verificar sockets
if (extension_loaded('sockets')) {
    echo "✅ Sockets: Instalado\n";
} else {
    echo "⚠️  Sockets: NO instalado (opcional)\n";
}

echo "\n";

echo "📦 PASO 6: Verificar PHPMailer\n";
echo "═══════════════════════════════════════════════════════════\n";

if (class_exists('\PHPMailer\PHPMailer\PHPMailer')) {
    echo "✅ PHPMailer: Instalado\n";
    $reflection = new ReflectionClass('\PHPMailer\PHPMailer\PHPMailer');
    $version = $reflection->getConstant('VERSION');
    echo "   Versión: {$version}\n";
} else {
    echo "❌ PHPMailer: NO instalado\n";
    echo "   Ejecuta: composer require phpmailer/phpmailer\n";
}

echo "\n";

echo "═══════════════════════════════════════════════════════════\n";
echo "📊 RESUMEN\n";
echo "═══════════════════════════════════════════════════════════\n";

if (empty($errors) && !$hasSpaces && $connection) {
    echo "✅ CONFIGURACIÓN CORRECTA\n\n";
    echo "Tu configuración parece estar correcta.\n";
    echo "Ahora puedes probar el envío de email:\n\n";
    echo "   php test_email_registro.php\n\n";
    
    echo "═══════════════════════════════════════════════════════════\n";
    echo "🔗 ENLACES ÚTILES\n";
    echo "═══════════════════════════════════════════════════════════\n";
    echo "• Seguridad de Google:\n";
    echo "  https://myaccount.google.com/security\n\n";
    echo "• Contraseñas de Aplicación:\n";
    echo "  https://myaccount.google.com/apppasswords\n\n";
    echo "• Guía completa:\n";
    echo "  Ver archivo: GUIA_CONFIGURAR_GMAIL_SMTP.md\n\n";
    
} else {
    echo "⚠️  HAY PROBLEMAS QUE CORREGIR\n\n";
    echo "Revisa los mensajes anteriores y corrige los errores.\n";
    echo "Luego ejecuta este script nuevamente.\n\n";
    
    echo "═══════════════════════════════════════════════════════════\n";
    echo "📖 AYUDA\n";
    echo "═══════════════════════════════════════════════════════════\n";
    echo "Lee la guía completa en:\n";
    echo "   GUIA_CONFIGURAR_GMAIL_SMTP.md\n\n";
    echo "O visita:\n";
    echo "   https://myaccount.google.com/apppasswords\n\n";
}

echo "\n";
