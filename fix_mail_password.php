<?php
/**
 * Script para corregir el password de Gmail en .env
 * Remueve los espacios del password de aplicación
 */

$envFile = __DIR__ . '/.env';

if (!file_exists($envFile)) {
    die("❌ Archivo .env no encontrado\n");
}

echo "===========================================\n";
echo "CORREGIR PASSWORD DE GMAIL EN .ENV\n";
echo "===========================================\n\n";

// Leer archivo
$content = file_get_contents($envFile);

// Buscar y reemplazar MAIL_PASSWORD con espacios
$pattern = '/MAIL_PASSWORD=([a-z0-9\s]+)/i';

if (preg_match($pattern, $content, $matches)) {
    $oldPassword = $matches[1];
    $newPassword = str_replace(' ', '', $oldPassword);
    
    echo "Password actual: {$oldPassword}\n";
    echo "Password corregido: {$newPassword}\n\n";
    
    // Reemplazar
    $newContent = preg_replace($pattern, 'MAIL_PASSWORD=' . $newPassword, $content);
    
    // Guardar
    file_put_contents($envFile, $newContent);
    
    echo "✅ Archivo .env actualizado correctamente\n\n";
    echo "Ahora ejecuta: php test_email_registro.php\n";
    
} else {
    echo "❌ No se encontró MAIL_PASSWORD en .env\n";
}
