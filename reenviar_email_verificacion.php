<?php
/**
 * Script para reenviar email de verificación
 * ChileChocados
 */

require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/models/Usuario.php';
require_once __DIR__ . '/app/helpers/Email.php';

use App\Models\Usuario;
use App\Helpers\Email;

echo "===========================================\n";
echo "REENVIAR EMAIL DE VERIFICACIÓN\n";
echo "===========================================\n\n";

// Email del usuario
$email = 'feasypuppy@gmail.com';

echo "Buscando usuario: {$email}\n";

// Buscar usuario
$usuarioModel = new Usuario();
$usuario = $usuarioModel->findByEmail($email);

if (!$usuario) {
    echo "❌ Usuario no encontrado\n";
    exit(1);
}

echo "✅ Usuario encontrado:\n";
echo "   ID: {$usuario->id}\n";
echo "   Nombre: {$usuario->nombre} {$usuario->apellido}\n";
echo "   Email: {$usuario->email}\n";
echo "   Verificado: " . ($usuario->verificado ? 'Sí' : 'No') . "\n";
echo "   Token: {$usuario->token_recuperacion}\n";
echo "   Token expira: {$usuario->token_expira}\n\n";

// Verificar si ya está verificado
if ($usuario->verificado) {
    echo "⚠️  Este usuario ya está verificado\n";
    echo "¿Deseas reenviar el email de todas formas? (s/n): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);
    
    if (trim(strtolower($line)) !== 's') {
        echo "\n❌ Operación cancelada.\n";
        exit(0);
    }
}

// Verificar si el token expiró
$tokenExpired = strtotime($usuario->token_expira) < time();
if ($tokenExpired) {
    echo "⚠️  El token ha expirado\n";
    echo "Generando nuevo token...\n";
    
    // Generar nuevo token
    $newToken = bin2hex(random_bytes(32));
    $newExpiry = date('Y-m-d H:i:s', strtotime('+24 hours'));
    
    // Actualizar en BD
    $usuarioModel->update($usuario->id, [
        'token_recuperacion' => $newToken,
        'token_expira' => $newExpiry
    ]);
    
    $usuario->token_recuperacion = $newToken;
    $usuario->token_expira = $newExpiry;
    
    echo "✅ Nuevo token generado\n";
    echo "   Token: {$newToken}\n";
    echo "   Expira: {$newExpiry}\n\n";
}

// Preparar datos del email
$verifyLink = getenv('APP_URL') . '/verificar-email/' . $usuario->token_recuperacion;

echo "===========================================\n";
echo "ENVIANDO EMAIL...\n";
echo "===========================================\n";
echo "Destinatario: {$usuario->email}\n";
echo "Nombre: {$usuario->nombre}\n";
echo "Link: {$verifyLink}\n\n";

// Enviar email
$sent = Email::send(
    $usuario->email,
    'Verifica tu cuenta en ChileChocados',
    'verify-email',
    [
        'nombre' => $usuario->nombre,
        'verify_link' => $verifyLink
    ]
);

if ($sent) {
    echo "✅ EMAIL ENVIADO EXITOSAMENTE!\n\n";
    echo "===========================================\n";
    echo "RESULTADO\n";
    echo "===========================================\n";
    echo "✓ El email fue enviado a: {$usuario->email}\n";
    echo "✓ Revisa tu bandeja de entrada\n";
    echo "✓ Si no lo ves, revisa la carpeta de SPAM\n\n";
    echo "Link de verificación:\n";
    echo "{$verifyLink}\n\n";
} else {
    echo "❌ ERROR: No se pudo enviar el email\n\n";
    echo "Revisa el log de emails:\n";
    echo "   tail -f logs/email.log\n\n";
    echo "Verifica la configuración SMTP:\n";
    echo "   php verificar_gmail_smtp.php\n\n";
    echo "Link de verificación manual:\n";
    echo "{$verifyLink}\n\n";
    echo "Puedes copiar este link y abrirlo en tu navegador para verificar la cuenta.\n\n";
}
