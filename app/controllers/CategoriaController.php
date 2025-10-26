<?php
/**
 * CategoriaController
 * Controlador para gestionar categorías y subcategorías
 * 
 * @author ToroDigital
 * @date 2025-10-25
 */

namespace App\Controllers;

use App\Models\Categoria;
use App\Models\Publicacion;
use PDO;

class CategoriaController {
    
    private $categoriaModel;
    private $publicacionModel;
    
    public function __construct() {
        $this->categoriaModel = new Categoria();
        $this->publicacionModel = new Publicacion();
    }
    
    /**
     * Muestra listado completo de categorías
     * Ruta: GET /categorias
     */
    public function index() {
        // Obtener categorías con subcategorías y conteo de publicaciones
        $categorias = $this->categoriaModel->getConConteoPublicaciones();
        
        // Para cada categoría, obtener sus subcategorías con conteo
        foreach ($categorias as $categoria) {
            $categoria->subcategorias = $this->getSubcategoriasConConteo($categoria->id);
        }
        
        // Calcular totales generales
        $total_categorias = count($categorias);
        $total_publicaciones = 0;
        foreach ($categorias as $cat) {
            $total_publicaciones += $cat->total_publicaciones ?? 0;
        }
        
        // Preparar datos para la vista
        $pageTitle = 'Categorías de Vehículos - ChileChocados';
        $pageDescription = 'Explora nuestras categorías de vehículos siniestrados: autos, camionetas, motos, comerciales y más';
        
        // Cargar vista (las variables ya están disponibles en el scope)
        require_once __DIR__ . '/../views/pages/categorias.php';
    }
    
    /**
     * Muestra publicaciones de una categoría específica
     * Ruta: GET /categoria/{id} o GET /categoria/{slug}
     */
    public function show($identificador) {
        // Intentar obtener por ID o por slug
        if (is_numeric($identificador)) {
            $categoria = $this->categoriaModel->find($identificador);
        } else {
            $categoria = $this->categoriaModel->findBySlug($identificador);
        }
        
        // Verificar que existe y está activa
        if (!$categoria || $categoria->activo != 1) {
            header('Location: ' . BASE_URL . '/404');
            exit;
        }
        
        // Obtener subcategorías
        $subcategorias = $this->categoriaModel->getSubcategorias($categoria->id);
        
        // Obtener parámetros de filtrado adicionales
        $filtros = [
            'categoria_id' => $categoria->id,
            'subcategoria_id' => $_GET['subcategoria'] ?? null,
            'tipo_venta' => $_GET['tipo'] ?? null,
            'precio_min' => $_GET['precio_min'] ?? null,
            'precio_max' => $_GET['precio_max'] ?? null,
            'region_id' => $_GET['region'] ?? null,
            'orden' => $_GET['orden'] ?? 'recientes',
            'page' => $_GET['page'] ?? 1
        ];
        
        // Obtener publicaciones de la categoría con filtros
        $resultado = $this->publicacionModel->listarPorCategoria($filtros);
        
        $data = [
            'title' => $categoria->nombre . ' - ChileChocados',
            'meta_description' => $categoria->descripcion ?? 'Encuentra ' . $categoria->nombre . ' siniestrados en Chile',
            'categoria' => $categoria,
            'subcategorias' => $subcategorias,
            'publicaciones' => $resultado['publicaciones'] ?? [],
            'total' => $resultado['total'] ?? 0,
            'pagina_actual' => (int)$filtros['page'],
            'total_paginas' => $resultado['total_paginas'] ?? 1,
            'filtros_aplicados' => $filtros
        ];
        
        // Cargar vista
        require_once __DIR__ . '/../views/pages/categorias/show.php';
    }
    
    /**
     * API: Obtiene subcategorías de una categoría (AJAX)
     * Ruta: GET /api/categorias/{id}/subcategorias
     */
    public function getSubcategorias($categoria_id) {
        header('Content-Type: application/json');
        
        $subcategorias = $this->categoriaModel->getSubcategorias($categoria_id);
        
        echo json_encode([
            'success' => true,
            'data' => $subcategorias
        ]);
        exit;
    }
    
    /**
     * API: Búsqueda de categorías (AJAX)
     * Ruta: GET /api/categorias/buscar?q={termino}
     */
    public function buscar() {
        header('Content-Type: application/json');
        
        $termino = $_GET['q'] ?? '';
        
        if (strlen($termino) < 2) {
            echo json_encode([
                'success' => false,
                'message' => 'El término de búsqueda debe tener al menos 2 caracteres'
            ]);
            exit;
        }
        
        $db = getDB();
        $stmt = $db->prepare("
            SELECT id, nombre, slug, icono
            FROM categorias_padre
            WHERE activo = 1
            AND nombre LIKE ?
            ORDER BY nombre ASC
            LIMIT 10
        ");
        
        $stmt->execute(['%' . $termino . '%']);
        $categorias = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        echo json_encode([
            'success' => true,
            'data' => $categorias
        ]);
        exit;
    }
    
    // ==================== MÉTODOS PRIVADOS ====================
    
    /**
     * Obtiene subcategorías con conteo de publicaciones
     * CORREGIDO: Ahora cuenta correctamente las publicaciones aprobadas
     * 
     * TODO: Verificar que el conteo de categorías principales también filtre por estado 'aprobada'
     * Ver: Categoria::getConConteoPublicaciones()
     */
    private function getSubcategoriasConConteo($categoria_padre_id) {
        $db = getDB();
        
        $stmt = $db->prepare("
            SELECT 
                s.*,
                COUNT(p.id) as total_publicaciones
            FROM subcategorias s
            LEFT JOIN publicaciones p ON p.subcategoria_id = s.id 
                AND p.estado = 'aprobada'
            WHERE s.categoria_padre_id = ?
            AND s.activo = 1
            GROUP BY s.id
            ORDER BY s.orden ASC, s.nombre ASC
        ");
        
        $stmt->execute([$categoria_padre_id]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}