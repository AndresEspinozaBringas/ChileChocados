<?php
/**
 * ApiController
 * Controlador para endpoints API del panel de administración
 * 
 * @author ChileChocados
 * @date 2025-10-30
 */

namespace App\Controllers;

use PDO;
use App\Helpers\Auth;

class ApiController
{
    private $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    /**
     * Verificar que el usuario es administrador
     */
    private function requireAdmin()
    {
        if (!Auth::check() || !Auth::isAdmin()) {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            exit;
        }
    }

    /**
     * Obtener notificaciones actualizadas para el admin
     * Ruta: GET /api/admin/notifications
     */
    public function getAdminNotifications()
    {
        $this->requireAdmin();
        
        header('Content-Type: application/json');
        
        try {
            // Obtener contadores actuales
            $stmt = $this->db->query("
                SELECT 
                    (SELECT COUNT(*) FROM publicaciones WHERE estado = 'pendiente') as publicaciones_pendientes,
                    (SELECT COUNT(*) FROM mensajes WHERE leido = 0) as mensajes_sin_leer,
                    (SELECT COUNT(*) FROM usuarios WHERE estado = 'activo') as usuarios_activos,
                    (SELECT COUNT(*) FROM publicaciones WHERE estado = 'aprobada') as publicaciones_activas
            ");
            
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificar si hay nuevas publicaciones pendientes desde la última verificación
            $ultimaVerificacion = $_SESSION['ultima_verificacion_admin'] ?? time();
            
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as nuevas
                FROM publicaciones 
                WHERE estado = 'pendiente' 
                AND fecha_creacion > FROM_UNIXTIME(?)
            ");
            $stmt->execute([$ultimaVerificacion]);
            $nuevas = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Actualizar timestamp de última verificación
            $_SESSION['ultima_verificacion_admin'] = time();
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'publicaciones_pendientes' => (int)$data['publicaciones_pendientes'],
                    'mensajes_sin_leer' => (int)$data['mensajes_sin_leer'],
                    'usuarios_activos' => (int)$data['usuarios_activos'],
                    'publicaciones_activas' => (int)$data['publicaciones_activas'],
                    'nuevas_pendientes' => (int)$nuevas['nuevas'],
                    'timestamp' => time()
                ]
            ]);
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Error al obtener notificaciones'
            ]);
        }
    }

    /**
     * Obtener estadísticas rápidas
     * Ruta: GET /api/admin/stats
     */
    public function getAdminStats()
    {
        $this->requireAdmin();
        
        header('Content-Type: application/json');
        
        try {
            $stmt = $this->db->query("
                SELECT 
                    (SELECT COUNT(*) FROM publicaciones) as total_publicaciones,
                    (SELECT COUNT(*) FROM publicaciones WHERE estado = 'pendiente') as pendientes,
                    (SELECT COUNT(*) FROM publicaciones WHERE estado = 'aprobada') as aprobadas,
                    (SELECT COUNT(*) FROM publicaciones WHERE estado = 'rechazada') as rechazadas,
                    (SELECT COUNT(*) FROM usuarios) as total_usuarios,
                    (SELECT COUNT(*) FROM usuarios WHERE estado = 'activo') as usuarios_activos,
                    (SELECT COUNT(*) FROM mensajes WHERE leido = 0) as mensajes_sin_leer
            ");
            
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => $stats
            ]);
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Error al obtener estadísticas'
            ]);
        }
    }
}
