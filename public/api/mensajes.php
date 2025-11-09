<?php
/**
 * API de Mensajes
 * Maneja las peticiones AJAX para el contador de mensajes
 */

// Solo cargar config si no está cargado
if (!defined('APP_PATH')) {
    require_once __DIR__ . '/../../config/config.php';
}
require_once APP_PATH . '/models/Mensaje.php';

header('Content-Type: application/json');

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit;
}

$mensajeModel = new \App\Models\Mensaje();
$usuarioId = $_SESSION['user_id'];

// Determinar la acción
$requestUri = $_SERVER['REQUEST_URI'];
$action = '';

if (strpos($requestUri, '/api/mensajes/contar') !== false) {
    $action = 'contar';
} else {
    $action = 'contar'; // Por defecto contar
}

switch ($action) {
    case 'contar':
        // Contar mensajes no leídos
        // Si es admin, contar todos los mensajes del sistema
        if (isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === 'admin') {
            $count = $mensajeModel->contarTodosNoLeidos();
        } else {
            $count = $mensajeModel->contarNoLeidos($usuarioId);
        }
        
        echo json_encode([
            'success' => true,
            'count' => $count
        ]);
        break;
        
    default:
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Acción no encontrada']);
        break;
}
