<?php
/**
 * ChileChocados - Home Controller
 * Controlador para la página principal
 * 
 * @version 2.0 - Actualizado con conexión a Base de Datos
 * @date 2025-10-25
 */

namespace App\Controllers;

use App\Models\Publicacion;
use App\Models\Categoria;
use Exception;

class HomeController {
    
    /**
     * Página principal
     * MEJORA UX: Redirige automáticamente a admin si es admin y no viene de una acción específica
     */
    public function index() {
        // Si es admin y accede directamente al home (sin parámetro de vista pública)
        // redirigir al panel admin
        if (isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === 'admin') {
            // Permitir ver el home si viene con parámetro ?view=public
            if (!isset($_GET['view']) || $_GET['view'] !== 'public') {
                header('Location: /admin');
                exit;
            }
        }
        
        $pageTitle = 'ChileChocados – Marketplace de bienes siniestrados';
        
        try {
            $publicacionModel = new Publicacion();
            
            // Obtener publicaciones destacadas activas (incluyendo vendidas)
            $publicacionesDestacadas = $publicacionModel->getDestacadas(8);
            
            // Obtener publicaciones recientes aprobadas (incluyendo vendidas)
            $sql = "SELECT p.*, cp.nombre as categoria_nombre, r.nombre as region_nombre
                    FROM publicaciones p
                    INNER JOIN categorias_padre cp ON p.categoria_padre_id = cp.id
                    INNER JOIN regiones r ON p.region_id = r.id
                    WHERE p.estado IN ('aprobada', 'vendida')
                    ORDER BY p.fecha_publicacion DESC
                    LIMIT 12";
            $publicacionesRecientes = $publicacionModel->query($sql);
            
            // Obtener categorías con conteo de publicaciones de la BD
            $categoriaModel = new Categoria();
            $categorias = $categoriaModel->getConConteoPublicaciones();
            
        } catch (Exception $e) {
            // En caso de error, usar arrays vacíos y log del error
            error_log("Error en HomeController::index() - " . $e->getMessage());
            $publicacionesDestacadas = [];
            $publicacionesRecientes = [];
            $categorias = [];
        }
        
        // Cargar vista
        require_once APP_PATH . '/views/pages/home.php';
    }
}