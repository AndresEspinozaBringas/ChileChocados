<?php
/**
 * ChileChocados - Front Controller
 * Punto de entrada principal de la aplicación
 */

// Cargar configuración
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';

// Obtener la URL solicitada
$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

// Determinar controlador y acción
$controllerName = !empty($url[0]) ? ucfirst($url[0]) . 'Controller' : 'HomeController';
$method = $url[1] ?? 'index';
$params = array_slice($url, 2);

// Ruta del controlador
$controllerFile = APP_PATH . '/controllers/' . $controllerName . '.php';

// Verificar si existe el controlador
if (file_exists($controllerFile)) {
    require_once $controllerFile;
    
    // Instanciar controlador
    if (class_exists($controllerName)) {
        $controller = new $controllerName();
        
        // Verificar si existe el método
        if (method_exists($controller, $method)) {
            call_user_func_array([$controller, $method], $params);
        } else {
            // Método no encontrado
            http_response_code(404);
            require_once APP_PATH . '/views/pages/404.php';
        }
    } else {
        // Clase no encontrada
        http_response_code(404);
        require_once APP_PATH . '/views/pages/404.php';
    }
} else {
    // Controlador no encontrado
    http_response_code(404);
    require_once APP_PATH . '/views/pages/404.php';
}
