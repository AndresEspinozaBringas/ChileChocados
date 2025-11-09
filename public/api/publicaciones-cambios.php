<?php
/**
 * API: Verificar cambios en publicaciones del usuario
 * GET /api/publicaciones/verificar-cambios?timestamp=123456789
 */

require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/config/database.php';

header('Content-Type: application/json');

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

$userId = $_SESSION['user_id'];
$timestamp = isset($_GET['timestamp']) ? (int)$_GET['timestamp'] : 0;

try {
    $db = \App\Core\Database::getInstance()->getConnection();
    
    // Convertir timestamp de JavaScript (milisegundos) a fecha MySQL
    $fecha = date('Y-m-d H:i:s', $timestamp / 1000);
    
    // Buscar publicaciones del usuario que hayan cambiado después del timestamp
    $sql = "SELECT 
                id, 
                titulo, 
                estado,
                UNIX_TIMESTAMP(fecha_actualizacion) * 1000 as timestamp_actualizacion
            FROM publicaciones 
            WHERE usuario_id = ? 
            AND fecha_actualizacion > ?
            ORDER BY fecha_actualizacion DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([$userId, $fecha]);
    $cambios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $hay_cambios = count($cambios) > 0;
    
    echo json_encode([
        'hay_cambios' => $hay_cambios,
        'cambios' => $cambios,
        'timestamp_servidor' => time() * 1000
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error del servidor']);
    error_log('Error en verificar-cambios: ' . $e->getMessage());
}
