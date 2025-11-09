<?php
/**
 * API de Notificaciones
 * Maneja las peticiones AJAX para el sistema de notificaciones
 */

// Solo cargar config si no está cargado
if (!defined('APP_PATH')) {
    require_once __DIR__ . '/../../config/config.php';
}
require_once APP_PATH . '/models/Notificacion.php';

header('Content-Type: application/json');

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit;
}

$notificacionModel = new \App\Models\Notificacion();
$usuarioId = $_SESSION['user_id'];

// Determinar la acción
$requestUri = $_SERVER['REQUEST_URI'];
$action = '';

if (strpos($requestUri, '/api/notificaciones/marcar-leida') !== false) {
    $action = 'marcar-leida';
} elseif (strpos($requestUri, '/api/notificaciones/marcar-todas-leidas') !== false) {
    $action = 'marcar-todas-leidas';
} elseif (strpos($requestUri, '/api/notificaciones/contar') !== false) {
    $action = 'contar';
} else {
    $action = 'listar';
}

switch ($action) {
    case 'listar':
        // Obtener notificaciones del usuario
        $limite = isset($_GET['limite']) ? (int)$_GET['limite'] : 20;
        $desdeId = isset($_GET['desde']) ? (int)$_GET['desde'] : 0;
        
        // Si se especifica 'desde', obtener solo notificaciones nuevas
        if ($desdeId > 0) {
            $notificaciones = $notificacionModel->getNotificacionesNuevas($usuarioId, $desdeId);
        } else {
            $notificaciones = $notificacionModel->getByUsuario($usuarioId, $limite);
        }
        
        echo json_encode([
            'success' => true,
            'notificaciones' => $notificaciones
        ]);
        break;
        
    case 'contar':
        // Contar notificaciones no leídas
        // Si es admin, contar publicaciones pendientes
        if (isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === 'admin') {
            $count = $notificacionModel->contarPendientesAdmin();
        } else {
            $count = $notificacionModel->contarNoLeidas($usuarioId);
        }
        
        echo json_encode([
            'success' => true,
            'count' => $count
        ]);
        break;
        
    case 'marcar-leida':
        // Marcar una notificación como leída
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;
        
        if ($id) {
            $notificacionModel->marcarComoLeida($id);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
        }
        break;
        
    case 'marcar-todas-leidas':
        // Marcar todas las notificaciones como leídas
        $notificacionModel->marcarTodasComoLeidas($usuarioId);
        echo json_encode(['success' => true]);
        break;
        
    default:
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Acción no encontrada']);
        break;
}
