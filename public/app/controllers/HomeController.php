<?php
/**
 * ChileChocados - Home Controller
 * Controlador para la página principal
 */

class HomeController {
    
    /**
     * Página principal
     */
    public function index() {
        $pageTitle = 'ChileChocados – Marketplace de bienes siniestrados';
        
        // TODO: Obtener publicaciones destacadas de la BD
        $publicacionesDestacadas = [];
        
        // TODO: Obtener categorías de la BD
        $categorias = $this->getCategoriasMock();
        
        // Cargar vista
        require_once APP_PATH . '/views/pages/home.php';
    }
    
    /**
     * Categorías mock (temporal)
     */
    private function getCategoriasMock() {
        return [
            ['id' => 1, 'nombre' => 'Auto', 'icon' => 'car', 'subcategorias' => 'Sedán • SUV • Deportivo', 'count' => 230],
            ['id' => 2, 'nombre' => 'Moto', 'icon' => 'bike', 'subcategorias' => 'Scooter • Enduro • Touring', 'count' => 84],
            ['id' => 3, 'nombre' => 'Camión', 'icon' => 'truck', 'subcategorias' => 'Ligero • Pesado', 'count' => 45],
            ['id' => 4, 'nombre' => 'Casa Rodante', 'icon' => 'rv', 'subcategorias' => 'RV • Camper', 'count' => 12],
            ['id' => 5, 'nombre' => 'Náutica', 'icon' => 'boat', 'subcategorias' => 'Lanchas • Yates', 'count' => 9],
            ['id' => 6, 'nombre' => 'Bus', 'icon' => 'bus', 'subcategorias' => 'Urbano • Interurbano', 'count' => 21],
            ['id' => 7, 'nombre' => 'Maquinaria', 'icon' => 'gear', 'subcategorias' => 'Retro • Grúa', 'count' => 33],
            ['id' => 8, 'nombre' => 'Aéreos', 'icon' => 'plane', 'subcategorias' => 'Ligera • Drones', 'count' => 5],
        ];
    }
}
