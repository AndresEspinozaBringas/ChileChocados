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
     */
    public function index() {
        $pageTitle = 'ChileChocados – Marketplace de bienes siniestrados';
        
        try {
            // Obtener publicaciones destacadas de la BD
            $publicacionModel = new Publicacion();
            $publicacionesDestacadas = $publicacionModel->getDestacadas(8);
            
            // Obtener categorías con conteo de publicaciones de la BD
            $categoriaModel = new Categoria();
            $categorias = $categoriaModel->getConConteoPublicaciones();
            
        } catch (Exception $e) {
            // En caso de error, usar arrays vacíos y log del error
            error_log("Error en HomeController::index() - " . $e->getMessage());
            $publicacionesDestacadas = [];
            $categorias = [];
        }
        
        // Cargar vista
        require_once APP_PATH . '/views/pages/home.php';
    }
}