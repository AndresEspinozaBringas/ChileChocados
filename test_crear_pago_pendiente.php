<?php
/**
 * Script de prueba: Crear pago pendiente de ejemplo
 * Para probar la vista /mis-pagos-pendientes
 */

require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/config/database.php';

$db = getDB();

// Obtener un usuario y una publicación existente
$stmt = $db->query("SELECT id FROM usuarios LIMIT 1");
$usuario = $stmt->fetch(PDO::FETCH_OBJ);

$stmt = $db->query("SELECT id, titulo FROM publicaciones LIMIT 1");
$publicacion = $stmt->fetch(PDO::FETCH_OBJ);

if (!$usuario || !$publicacion) {
    die("Error: No hay usuarios o publicaciones en la base de datos\n");
}

// Crear pago pendiente de prueba
$commerceOrder = 'TEST-' . $publicacion->id . '-' . time();

$stmt = $db->prepare("
    INSERT INTO pagos_flow 
    (publicacion_id, usuario_id, tipo, monto, flow_orden, estado, intentos, fecha_creacion, fecha_expiracion) 
    VALUES (?, ?, 'destacado_15', 15000, ?, 'pendiente', 1, NOW(), DATE_ADD(NOW(), INTERVAL 48 HOUR))
");

$stmt->execute([
    $publicacion->id,
    $usuario->id,
    $commerceOrder
]);

$pagoId = $db->lastInsertId();

echo "✅ Pago pendiente creado exitosamente\n";
echo "ID: $pagoId\n";
echo "Usuario ID: {$usuario->id}\n";
echo "Publicación ID: {$publicacion->id}\n";
echo "Publicación: {$publicacion->titulo}\n";
echo "Orden: $commerceOrder\n";
echo "\nPuedes verlo en: http://chilechocados.local:8080/mis-pagos-pendientes\n";
echo "(Asegúrate de iniciar sesión con el usuario ID: {$usuario->id})\n";
