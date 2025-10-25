<?php
/**
 * CategoriaController
 * Maneja las categorías y subcategorías de vehículos
 */

require_once APP_PATH . '/models/Categoria.php';

class CategoriaController {
    
    private $categoriaModel;
    
    public function __construct() {
        $this->categoriaModel = new Categoria();
    }
    
    /**
     * Muestra todas las categorías con sus subcategorías
     */
    public function index() {
        // Obtener todas las categorías padre con sus subcategorías
        $categorias = $this->categoriaModel->obtenerCategoriasConSubcategorias();
        
        // Contar publicaciones por categoría
        foreach ($categorias as &$categoria) {
            $categoria['total_publicaciones'] = $this->categoriaModel->contarPublicacionesPorCategoria($categoria['id']);
        }
        
        $data = [
            'title' => 'Categorías de Vehículos - ChileChocados',
            'meta_description' => 'Explora todas las categorías de vehículos siniestrados disponibles en ChileChocados',
            'categorias' => $categorias
        ];
        
        require_once APP_PATH . '/views/pages/categorias.php';
    }
    
    /**
     * Muestra publicaciones de una categoría específica
     * 
     * @param int $id ID de la categoría padre
     * @param int|null $subcategoria_id ID de la subcategoría (opcional)
     */
    public function show($id, $subcategoria_id = null) {
        // Obtener información de la categoría
        $categoria = $this->categoriaModel->obtenerPorId($id);
        
        if (!$categoria) {
            header('Location: ' . BASE_URL . '/categorias');
            exit;
        }
        
        // Obtener publicaciones de la categoría
        $publicaciones = $this->categoriaModel->obtenerPublicacionesPorCategoria(
            $id, 
            $subcategoria_id,
            $_GET['page'] ?? 1
        );
        
        // Si hay subcategoría, obtener su información
        $subcategoria = null;
        if ($subcategoria_id) {
            $subcategoria = $this->categoriaModel->obtenerSubcategoriaPorId($subcategoria_id);
        }
        
        $data = [
            'title' => $categoria['nombre'] . ' - Categorías - ChileChocados',
            'meta_description' => 'Vehículos siniestrados en la categoría ' . $categoria['nombre'],
            'categoria' => $categoria,
            'subcategoria' => $subcategoria,
            'publicaciones' => $publicaciones
        ];
        
        require_once APP_PATH . '/views/pages/categoria-detalle.php';
    }
    
    /**
     * API: Obtiene subcategorías de una categoría padre
     * Para uso con AJAX
     * 
     * @param int $id ID de la categoría padre
     */
    public function subcategorias($id) {
        header('Content-Type: application/json');
        
        $subcategorias = $this->categoriaModel->obtenerSubcategorias($id);
        
        echo json_encode([
            'success' => true,
            'data' => $subcategorias
        ]);
        exit;
    }
}
