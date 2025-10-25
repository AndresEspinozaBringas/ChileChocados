<?php
/**
 * LegalController
 * Maneja las páginas legales: Términos y Condiciones, Política de Privacidad
 */

class LegalController {
    
    /**
     * Muestra la página de Términos y Condiciones
     */
    public function terminos() {
        $data = [
            'title' => 'Términos y Condiciones - ChileChocados',
            'meta_description' => 'Términos y condiciones de uso de ChileChocados, el marketplace de vehículos siniestrados en Chile'
        ];
        
        require_once APP_PATH . '/views/pages/legal/terminos.php';
    }
    
    /**
     * Muestra la página de Política de Privacidad
     */
    public function privacidad() {
        $data = [
            'title' => 'Política de Privacidad - ChileChocados',
            'meta_description' => 'Política de privacidad y tratamiento de datos personales en ChileChocados'
        ];
        
        require_once APP_PATH . '/views/pages/legal/privacidad.php';
    }
    
    /**
     * Muestra la página de Política de Cookies
     */
    public function cookies() {
        $data = [
            'title' => 'Política de Cookies - ChileChocados',
            'meta_description' => 'Información sobre el uso de cookies en ChileChocados'
        ];
        
        require_once APP_PATH . '/views/pages/legal/cookies.php';
    }
    
    /**
     * Muestra la página de Canal de Denuncias
     */
    public function denuncias() {
        $data = [
            'title' => 'Canal de Denuncias - ChileChocados',
            'meta_description' => 'Canal de denuncias y reporte de irregularidades en ChileChocados'
        ];
        
        require_once APP_PATH . '/views/pages/legal/denuncias.php';
    }
}
