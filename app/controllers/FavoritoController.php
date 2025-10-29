<?php
/**
 * FavoritoController
 * Gestiona el sistema de favoritos de publicaciones
 */

class FavoritoController
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Muestra la lista de favoritos del usuario
     * Ruta: GET /favoritos
     */
    public function index()
    {
        // Verificar que el usuario esté autenticado
        if (!isset($_SESSION['user_id'])) {
            Session::flash('error', 'Debes iniciar sesión para ver tus favoritos');
            header('Location: /login');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        
        // Obtener publicaciones favoritas del usuario
        $stmt = $this->db->prepare("
            SELECT 
                p.id,
                p.titulo,
                p.descripcion,
                p.precio,
                p.foto_principal,
                p.fecha_publicacion,
                p.visitas,
                p.estado,
                cp.nombre as categoria_nombre,
                cp.slug as categoria_slug,
                sc.nombre as subcategoria_nombre,
                r.nombre as region_nombre,
                c.nombre as comuna_nombre,
                f.fecha_agregado as fecha_favorito
            FROM favoritos f
            INNER JOIN publicaciones p ON f.publicacion_id = p.id
            INNER JOIN categorias_padre cp ON p.categoria_padre_id = cp.id
            LEFT JOIN subcategorias sc ON p.subcategoria_id = sc.id
            INNER JOIN regiones r ON p.region_id = r.id
            INNER JOIN comunas c ON p.comuna_id = c.id
            WHERE f.usuario_id = ? AND p.estado = 'aprobada'
            ORDER BY f.fecha_agregado DESC
        ");
        
        $stmt->execute([$userId]);
        $favoritos = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        // Formatear datos para la vista
        foreach ($favoritos as $favorito) {
            // Formatear precio
            $favorito->precio_formateado = number_format($favorito->precio, 0, ',', '.');
            
            // Calcular días desde que se agregó a favoritos
            $fecha = new DateTime($favorito->fecha_favorito);
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
            Session::flash('error', 'Debes iniciar sesión');
            header('Location: /login');
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
            Session::flash('error', 'ID de publicación inválido');
            header('Location: /favoritos');
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
                Session::flash('error', 'Publicación no encontrada en favoritos');
                header('Location: /favoritos');
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
                Session::flash('success', 'Publicación eliminada de favoritos');
                header('Location: /favoritos');
                exit;
            }
            
        } catch (PDOException $e) {
            error_log('Error al eliminar favorito: ' . $e->getMessage());
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error al eliminar']);
            } else {
                Session::flash('error', 'Error al eliminar de favoritos');
                header('Location: /favoritos');
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
