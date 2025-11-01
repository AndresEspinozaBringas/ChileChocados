<?php
/**
 * FavoritoController
 * Gestiona el sistema de favoritos de publicaciones
 */

namespace App\Controllers;

use PDO;
use PDOException;
use DateTime;

class FavoritoController
{
    private $db;
    
    public function __construct()
    {
        $this->db = getDB();
    }
    
    /**
     * Muestra la lista de favoritos del usuario
     * Ruta: GET /favoritos
     */
    public function index()
    {
        // Verificar que el usuario esté autenticado
        if (!isAuthenticated()) {
            setFlash('error', 'Debes iniciar sesión para ver tus favoritos');
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        
        // Obtener favoritos desde la BD
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    f.id as favorito_id,
                    f.fecha_agregado,
                    p.*,
                    cat.nombre as categoria_nombre,
                    subcat.nombre as subcategoria_nombre,
                    com.nombre as comuna_nombre,
                    COALESCE(
                        (SELECT ruta FROM publicacion_fotos WHERE publicacion_id = p.id AND es_principal = 1 LIMIT 1),
                        (SELECT ruta FROM publicacion_fotos WHERE publicacion_id = p.id ORDER BY orden LIMIT 1),
                        p.foto_principal
                    ) as foto_principal
                FROM favoritos f
                INNER JOIN publicaciones p ON f.publicacion_id = p.id
                LEFT JOIN categorias_padre cat ON p.categoria_padre_id = cat.id
                LEFT JOIN subcategorias subcat ON p.subcategoria_id = subcat.id
                LEFT JOIN comunas com ON p.comuna_id = com.id
                WHERE f.usuario_id = ?
                ORDER BY f.fecha_agregado DESC
            ");
            $stmt->execute([$userId]);
            $favoritos = $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log('Error al obtener favoritos: ' . $e->getMessage());
            $favoritos = [];
        }
        
        // Formatear datos para la vista
        foreach ($favoritos as $favorito) {
            // Formatear precio
            $favorito->precio_formateado = number_format($favorito->precio ?? 0, 0, ',', '.');
            
            // Calcular días desde que se agregó a favoritos
            $fecha = new DateTime($favorito->fecha_agregado);
            $ahora = new DateTime();
            $diferencia = $ahora->diff($fecha);
            
            if ($diferencia->days == 0) {
                $favorito->tiempo_favorito = 'Hoy';
            } elseif ($diferencia->days == 1) {
                $favorito->tiempo_favorito = 'Ayer';
            } elseif ($diferencia->days < 7) {
                $favorito->tiempo_favorito = 'Hace ' . $diferencia->days . ' días';
            } elseif ($diferencia->days < 30) {
                $semanas = floor($diferencia->days / 7);
                $favorito->tiempo_favorito = 'Hace ' . $semanas . ' semana' . ($semanas > 1 ? 's' : '');
            } else {
                $meses = floor($diferencia->days / 30);
                $favorito->tiempo_favorito = 'Hace ' . $meses . ' mes' . ($meses > 1 ? 'es' : '');
            }
            
            // Asegurar valores por defecto si no hay datos
            $favorito->categoria_nombre = $favorito->categoria_nombre ?? 'Sin categoría';
            $favorito->subcategoria_nombre = $favorito->subcategoria_nombre ?? '';
            $favorito->comuna_nombre = $favorito->comuna_nombre ?? 'Sin ubicación';
        }
        
        // Datos para la vista
        $data = [
            'title' => 'Mis Favoritos - ChileChocados',
            'meta_description' => 'Gestiona tus publicaciones favoritas',
            'favoritos' => $favoritos,
            'total' => count($favoritos)
        ];
        
        // Renderizar vista
        require_once APP_PATH . '/views/pages/usuarios/favoritos.php';
    }
    
    /**
     * Agrega una publicación a favoritos (AJAX)
     * Ruta: POST /favoritos/agregar
     */
    public function agregar()
    {
        // Verificar método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        // Verificar autenticación
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión']);
            return;
        }
        
        $userId = $_SESSION['user_id'];
        $publicacionId = filter_input(INPUT_POST, 'publicacion_id', FILTER_VALIDATE_INT);
        
        if (!$publicacionId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID de publicación inválido']);
            return;
        }
        
        try {
            // Verificar que la publicación existe y está aprobada
            $stmt = $this->db->prepare("
                SELECT id FROM publicaciones 
                WHERE id = ? AND estado = 'aprobada'
            ");
            $stmt->execute([$publicacionId]);
            
            if (!$stmt->fetch()) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Publicación no encontrada']);
                return;
            }
            
            // Verificar si ya está en favoritos
            $stmt = $this->db->prepare("
                SELECT id FROM favoritos 
                WHERE usuario_id = ? AND publicacion_id = ?
            ");
            $stmt->execute([$userId, $publicacionId]);
            
            if ($stmt->fetch()) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Ya está en tus favoritos',
                    'already_exists' => true
                ]);
                return;
            }
            
            // Agregar a favoritos
            $stmt = $this->db->prepare("
                INSERT INTO favoritos (usuario_id, publicacion_id, fecha_agregado)
                VALUES (?, ?, NOW())
            ");
            $stmt->execute([$userId, $publicacionId]);
            
            // Obtener total de favoritos del usuario
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total FROM favoritos WHERE usuario_id = ?
            ");
            $stmt->execute([$userId]);
            $total = $stmt->fetch(PDO::FETCH_OBJ)->total;
            
            echo json_encode([
                'success' => true,
                'message' => 'Agregado a favoritos',
                'total_favoritos' => $total
            ]);
            
        } catch (PDOException $e) {
            error_log('Error al agregar favorito: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al agregar favorito']);
        }
    }
    
    /**
     * Elimina una publicación de favoritos
     * Ruta: POST /favoritos/eliminar o DELETE /favoritos/{id}
     */
    public function eliminar($publicacionId = null)
    {
        // Verificar autenticación
        if (!isset($_SESSION['user_id'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión']);
                return;
            }
            setFlash('error', 'Debes iniciar sesión');
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        
        // Obtener ID de publicación desde POST o parámetro
        if (!$publicacionId) {
            $publicacionId = filter_input(INPUT_POST, 'publicacion_id', FILTER_VALIDATE_INT);
        } else {
            $publicacionId = filter_var($publicacionId, FILTER_VALIDATE_INT);
        }
        
        if (!$publicacionId) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID inválido']);
                return;
            }
            setFlash('error', 'ID de publicación inválido');
            header('Location: ' . BASE_URL . '/favoritos');
            exit;
        }
        
        try {
            // Eliminar de favoritos
            $stmt = $this->db->prepare("
                DELETE FROM favoritos 
                WHERE usuario_id = ? AND publicacion_id = ?
            ");
            $stmt->execute([$userId, $publicacionId]);
            
            if ($stmt->rowCount() === 0) {
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    echo json_encode([
                        'success' => false, 
                        'message' => 'No se encontró en favoritos'
                    ]);
                    return;
                }
                setFlash('error', 'Publicación no encontrada en favoritos');
                header('Location: ' . BASE_URL . '/favoritos');
                exit;
            }
            
            // Obtener total actualizado
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total FROM favoritos WHERE usuario_id = ?
            ");
            $stmt->execute([$userId]);
            $total = $stmt->fetch(PDO::FETCH_OBJ)->total;
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                echo json_encode([
                    'success' => true,
                    'message' => 'Eliminado de favoritos',
                    'total_favoritos' => $total
                ]);
            } else {
                setFlash('success', 'Publicación eliminada de favoritos');
                header('Location: ' . BASE_URL . '/favoritos');
                exit;
            }
            
        } catch (PDOException $e) {
            error_log('Error al eliminar favorito: ' . $e->getMessage());
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error al eliminar']);
            } else {
                setFlash('error', 'Error al eliminar de favoritos');
                header('Location: ' . BASE_URL . '/favoritos');
                exit;
            }
        }
    }
    
    /**
     * Verifica si una publicación está en favoritos (AJAX)
     * Ruta: GET /favoritos/verificar/{id}
     */
    public function verificar($publicacionId)
    {
        // Verificar autenticación
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['en_favoritos' => false, 'autenticado' => false]);
            return;
        }
        
        $userId = $_SESSION['user_id'];
        $publicacionId = filter_var($publicacionId, FILTER_VALIDATE_INT);
        
        if (!$publicacionId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID inválido']);
            return;
        }
        
        try {
            $stmt = $this->db->prepare("
                SELECT id FROM favoritos 
                WHERE usuario_id = ? AND publicacion_id = ?
            ");
            $stmt->execute([$userId, $publicacionId]);
            
            $enFavoritos = $stmt->fetch() !== false;
            
            echo json_encode([
                'success' => true,
                'en_favoritos' => $enFavoritos,
                'autenticado' => true
            ]);
            
        } catch (PDOException $e) {
            error_log('Error al verificar favorito: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al verificar']);
        }
    }
    
    /**
     * Obtiene el total de favoritos del usuario actual (AJAX)
     * Ruta: GET /favoritos/total
     */
    public function total()
    {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['total' => 0, 'autenticado' => false]);
            return;
        }
        
        $userId = $_SESSION['user_id'];
        
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total FROM favoritos WHERE usuario_id = ?
            ");
            $stmt->execute([$userId]);
            $resultado = $stmt->fetch(PDO::FETCH_OBJ);
            
            echo json_encode([
                'success' => true,
                'total' => (int)$resultado->total,
                'autenticado' => true
            ]);
            
        } catch (PDOException $e) {
            error_log('Error al obtener total de favoritos: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error']);
        }
    }
}
