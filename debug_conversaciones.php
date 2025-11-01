<?php
/**
 * Debug: Ver conversaciones del vendedor (usuario ID 2)
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/models/Model.php';
require_once __DIR__ . '/app/models/Mensaje.php';

use App\Models\Mensaje;

$mensajeModel = new Mensaje();

echo "<h1>Debug Conversaciones - Usuario ID 2 (Vendedor)</h1>";

// Obtener conversaciones del vendedor
$conversaciones = $mensajeModel->getConversacionesUsuario(2);

echo "<h2>Conversaciones encontradas: " . count($conversaciones) . "</h2>";

if (empty($conversaciones)) {
    echo "<p style='color: red;'>❌ No se encontraron conversaciones</p>";
    
    // Verificar mensajes directamente en la BD
    echo "<h3>Verificando mensajes en la BD donde usuario 2 es remitente o destinatario:</h3>";
    
    $db = getDB();
    $stmt = $db->query("SELECT * FROM mensajes WHERE remitente_id = 2 OR destinatario_id = 2");
    $mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<pre>";
    print_r($mensajes);
    echo "</pre>";
} else {
    echo "<pre>";
    print_r($conversaciones);
    echo "</pre>";
}
