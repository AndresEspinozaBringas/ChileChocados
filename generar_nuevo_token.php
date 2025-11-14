<?php
/**
 * Generar nuevo token de verificación para un usuario
 */

require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/models/Usuario.php';
require_once __DIR__ . '/app/helpers/Email.php';

use App\Models\Usuario;
use App\Helpers\Email;

$email = 'feasypuppy@gmail.com';

echo "===========================================\n";
echo "GENERAR NUEVO TOKEN DE VERIFICACIÓN\n";
echo "===========================================\n\n";

$usuarioModel = new Usuario();
$user = $usuarioModel->findByEmail($email);

if (!$user) {
    echo "❌ Usuario no encontrado: {$email}\n";
    exit(1);
}

echo "Usuario encontrado:\n";
echo "ID: {$user->id}\n";
echo "Email: {$user->email}\n";
echo "Nombre: {$user->nombre}\n";
echo "Verificado: " . ($user->verificado ? 'Sí' : 'No') . "\n\n";

if ($user->verificado) {
    echo "⚠️  Este usuario ya está verificado\n\n";
}

// Generar nuevo token
$newToken = bin2hex(random_bytes(32));
$newExpiry = date('Y-m-d H:i:s', strtotime('+24 hours'));

// Actualizar en BD
$usuarioModel->update($user->id, [
    'token_recuperacion' => $newToken,
    'token_expira' => $newExpiry
]);

echo "✅ Nuevo token generado:\n";
echo "Token: {$newToken}\n";
echo "Expira: {$newExpiry}\n\n";

$verifyLink = getenv('APP_URL') . '/verificar-email/' . $newToken;
echo "Link de verificación:\n";
echo "{$verifyLink}\n\n";

// Enviar email con el nuevo token
echo "===========================================\n";
echo "ENVIANDO EMAIL...\n";
echo "===========================================\n\n";

$emailSent = Email::send(
    $user->email,
    'Verifica tu cuenta en ChileChocados',
    'verify-email',
    [
        'nombre' => $user->nombre,
        'verify_link' => $verifyLink
    ]
);

if ($emailSent) {
    echo "✅ Email enviado exitosamente a {$user->email}\n\n";
} else {
    echo "❌ Error al enviar el email\n\n";
}

echo "También puedes copiar este link y abrirlo en tu navegador:\n";
echo "{$verifyLink}\n\n";


