<?php
/**
 * API: Enviar Solicitud de Compra
 * POST /api/solicitud-compra
 */

require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/config/database.php';

header('Content-Type: application/json');

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit;
}

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Obtener datos JSON
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Validar datos
if (empty($data['publicacion_id']) || empty($data['vendedor_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

// Validar CSRF
if (empty($data['csrf_token']) || $data['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Token inválido']);
    exit;
}

$compradorId = $_SESSION['user_id'];
$publicacionId = (int)$data['publicacion_id'];
$vendedorId = (int)$data['vendedor_id'];

try {
    $db = \App\Core\Database::getInstance()->getConnection();
    
    // Verificar que la publicación existe
    $stmt = $db->prepare("SELECT titulo, usuario_id FROM publicaciones WHERE id = ?");
    $stmt->execute([$publicacionId]);
    $publicacion = $stmt->fetch(PDO::FETCH_OBJ);
    
    if (!$publicacion) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Publicación no encontrada']);
        exit;
    }
    
    // Verificar que no seas el dueño
    if ($publicacion->usuario_id == $compradorId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'No puedes comprar tu propia publicación']);
        exit;
    }
    
    // Obtener nombre del comprador
    $stmt = $db->prepare("SELECT nombre FROM usuarios WHERE id = ?");
    $stmt->execute([$compradorId]);
    $comprador = $stmt->fetch(PDO::FETCH_OBJ);
    
    // Verificar si ya existe una notificación similar reciente (últimas 24 horas)
    $stmt = $db->prepare("
        SELECT id FROM notificaciones 
        WHERE usuario_id = ? 
        AND tipo = 'solicitud_compra'
        AND mensaje LIKE ?
        AND fecha_creacion > DATE_SUB(NOW(), INTERVAL 24 HOUR)
        LIMIT 1
    ");
    
    $mensajeBusqueda = "%{$comprador->nombre}%{$publicacion->titulo}%";
    $stmt->execute([$vendedorId, $mensajeBusqueda]);
    $notificacionExistente = $stmt->fetch();
    
    // Solo crear notificación si no existe una similar reciente
    if (!$notificacionExistente) {
        $mensaje = "{$comprador->nombre} está interesado en comprar tu publicación '{$publicacion->titulo}'";
        
        $stmt = $db->prepare("
            INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje, enlace, leida, fecha_creacion)
            VALUES (?, 'solicitud_compra', 'Solicitud de Compra', ?, ?, 0, NOW())
        ");
        
        $enlace = '/mensajes?publicacion=' . $publicacionId;
        $stmt->execute([$vendedorId, $mensaje, $enlace]);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Solicitud enviada exitosamente'
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error del servidor']);
    error_log('Error en solicitud-compra: ' . $e->getMessage());
}
