<?php
/**
 * InfoController
 * Maneja las páginas informativas: FAQ, Guías, Consejos de Seguridad
 */

namespace App\Controllers;

class InfoController {
    
    /**
     * Muestra la página de Cómo Funciona
     */
    public function comoFunciona() {
        $data = [
            'title' => 'Cómo Funciona - ChileChocados',
            'meta_description' => 'Descubre cómo funciona ChileChocados, el marketplace de vehículos siniestrados en Chile'
        ];
        
        require_once APP_PATH . '/views/pages/info/como-funciona.php';
    }
    
    /**
     * Muestra la página de Preguntas Frecuentes
     */
    public function preguntasFrecuentes() {
        $data = [
            'title' => 'Preguntas Frecuentes - ChileChocados',
            'meta_description' => 'Respuestas a las preguntas más frecuentes sobre compra y venta de vehículos siniestrados en ChileChocados'
        ];
        
        require_once APP_PATH . '/views/pages/info/preguntas-frecuentes.php';
    }
    
    /**
     * Muestra la página de Guía del Comprador
     */
    public function guiaComprador() {
        $data = [
            'title' => 'Guía del Comprador - ChileChocados',
            'meta_description' => 'Consejos y recomendaciones para comprar vehículos siniestrados de forma segura en ChileChocados'
        ];
        
        require_once APP_PATH . '/views/pages/info/guia-comprador.php';
    }
    
    /**
     * Muestra la página de Guía del Vendedor
     */
    public function guiaVendedor() {
        $data = [
            'title' => 'Guía del Vendedor - ChileChocados',
            'meta_description' => 'Aprende cómo vender tu vehículo siniestrado de manera efectiva en ChileChocados'
        ];
        
        require_once APP_PATH . '/views/pages/info/guia-vendedor.php';
    }
    
    /**
     * Muestra la página de Consejos de Seguridad
     */
    public function seguridad() {
        $data = [
            'title' => 'Consejos de Seguridad - ChileChocados',
            'meta_description' => 'Recomendaciones de seguridad para comprar y vender vehículos siniestrados en ChileChocados'
        ];
        
        require_once APP_PATH . '/views/pages/info/seguridad.php';
    }
}
