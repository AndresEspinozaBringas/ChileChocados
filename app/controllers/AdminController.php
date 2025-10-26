<?php
/**
 * AdminController
 * Controlador para el panel de administración
 * 
 * @author ToroDigital
 * @date 2025-10-26
 */

namespace App\Controllers;

use PDO;

class AdminController
{
    /**
     * Página principal del admin
     * Ruta: GET /admin
     * 
     * Por ahora muestra directamente el panel de publicaciones
     */
    public function index()
    {
        // TODO: Verificar autenticación de admin
        
        // Mostrar directamente el panel de publicaciones
        $this->publicaciones();
    }
    
    /**
     * Panel de gestión de publicaciones
     * Ruta: GET /admin/publicaciones
     */
    public function publicaciones()
    {
        // TODO: Verificar que el usuario es admin
        /*
        if (!isset($_SESSION['admin_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
        */
        
        // Obtener filtros de la URL
        $estado = $_GET['estado'] ?? 'pendiente';
        $categoria_id = $_GET['categoria'] ?? null;
        $busqueda = $_GET['q'] ?? '';
        $page = $_GET['page'] ?? 1;
        $per_page = 20;
        
        $db = getDB();
        
        // Construir query base
        $sql = "SELECT p.*, 
                       u.nombre as usuario_nombre, 
                       u.apellido as usuario_apellido,
                       u.email as usuario_email,
                       cp.nombre as categoria_nombre,
                       sc.nombre as subcategoria_nombre,
                       r.nombre as region_nombre
                FROM publicaciones p
                INNER JOIN usuarios u ON p.usuario_id = u.id
                LEFT JOIN categorias_padre cp ON p.categoria_padre_id = cp.id
                LEFT JOIN subcategorias sc ON p.subcategoria_id = sc.id
                LEFT JOIN regiones r ON p.region_id = r.id
                WHERE 1=1";
        
        $params = [];
        
        // Aplicar filtro de estado
        if ($estado && $estado !== 'todas') {
            $sql .= " AND p.estado = ?";
            $params[] = $estado;
        }
        
        // Aplicar filtro de categoría
        if ($categoria_id) {
            $sql .= " AND p.categoria_padre_id = ?";
            $params[] = $categoria_id;
        }
        
        // Aplicar búsqueda
        if ($busqueda) {
            $sql .= " AND (p.titulo LIKE ? OR p.marca LIKE ? OR p.modelo LIKE ?)";
            $search_term = "%$busqueda%";
            $params[] = $search_term;
            $params[] = $search_term;
            $params[] = $search_term;
        }
        
        $sql .= " ORDER BY p.fecha_creacion DESC";
        
        // Contar total para paginación
        $count_sql = "SELECT COUNT(*) as total FROM publicaciones p WHERE 1=1";
        if ($estado && $estado !== 'todas') {
            $count_sql .= " AND p.estado = ?";
        }
        if ($categoria_id) {
            $count_sql .= " AND p.categoria_padre_id = ?";
        }
        if ($busqueda) {
            $count_sql .= " AND (p.titulo LIKE ? OR p.marca LIKE ? OR p.modelo LIKE ?)";
        }
        
        $stmt = $db->prepare($count_sql);
        $stmt->execute($params);
        $total_records = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        $total_pages = ceil($total_records / $per_page);
        
        // Agregar LIMIT para paginación
        $offset = ($page - 1) * $per_page;
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = (int)$per_page;
        $params[] = (int)$offset;
        
        // Ejecutar query
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $publicaciones = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        // Obtener estadísticas
        $stats = $this->getEstadisticas();
        
        // Obtener categorías para filtro
        $categorias = $db->query("SELECT id, nombre FROM categorias_padre WHERE activo = 1 ORDER BY orden, nombre")->fetchAll(PDO::FETCH_OBJ);
        
        // Preparar datos para la vista
        $pageTitle = 'Panel de Administración - Publicaciones';
        $currentPage = 'admin'; // Para el nav
        $filtros = [
            'estado' => $estado,
            'categoria_id' => $categoria_id,
            'busqueda' => $busqueda
        ];
        $paginacion = [
            'current_page' => $page,
            'total_pages' => $total_pages,
            'total_records' => $total_records,
            'per_page' => $per_page
        ];
        
        // Cargar vista
        require_once APP_PATH . '/views/pages/admin/publicaciones.php';
    }
    
    /**
     * Obtener estadísticas del panel
     */
    private function getEstadisticas()
    {
        $db = getDB();
        
        $stats = [
            'total' => 0,
            'pendientes' => 0,
            'aprobadas' => 0,
            'rechazadas' => 0,
            'borradores' => 0
        ];
        
        // Total por estado
        $stmt = $db->query("
            SELECT estado, COUNT(*) as total 
            FROM publicaciones 
            GROUP BY estado
        ");
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $estado_key = $row['estado'] === 'borrador' ? 'borradores' : $row['estado'] . 's';
            $stats[$estado_key] = $row['total'];
            $stats['total'] += $row['total'];
        }
        
        return $stats;
    }
}
