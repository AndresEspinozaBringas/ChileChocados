<?php
/**
 * ChileChocados - Front Controller SIMPLIFICADO
 */

// Mostrar errores temporalmente
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    // 1. Cargar configuración
    require_once __DIR__ . '/../app/config/config.php';
    
    // 2. Cargar Database
    if (file_exists(__DIR__ . '/../app/core/Database.php')) {
        require_once __DIR__ . '/../app/core/Database.php';
    }
    
    // 3. Cargar helpers de database
    if (file_exists(__DIR__ . '/../app/config/database.php')) {
        require_once __DIR__ . '/../app/config/database.php';
    }
    
    // 4. Obtener URL
    $url = $_GET['url'] ?? '';
    $url = rtrim($url, '/');
    $url = filter_var($url, FILTER_SANITIZE_URL);
    $url = explode('/', $url);
    
    // 5. Determinar controlador
    $controllerName = !empty($url[0]) ? ucfirst($url[0]) . 'Controller' : 'HomeController';
    $method = $url[1] ?? 'index';
    $params = array_slice($url, 2);
    
    // 6. Rutas especiales
    $routes = [
        'login' => ['AuthController', 'login'],
        'registro' => ['AuthController', 'register'],
        'logout' => ['AuthController', 'logout'],
        'publicaciones' => ['PublicacionController', 'index'],
        'publicacion' => ['PublicacionController', 'show'],
        'publicar' => ['PublicacionController', 'create'],
        'categorias' => ['CategoriaController', 'index'],
        'categoria' => ['CategoriaController', 'show'],
        'contacto' => ['ContactoController', 'index'],
        'terminos' => ['LegalController', 'terminos'],
    ];
    
    if (!empty($url[0]) && isset($routes[$url[0]])) {
        $controllerName = $routes[$url[0]][0];
        $method = $routes[$url[0]][1];
        $params = array_slice($url, 1);
    }
    
    // 7. Admin routes
    if (!empty($url[0]) && $url[0] === 'admin') {
        require_once APP_PATH . '/controllers/AdminController.php';
        $controller = new App\Controllers\AdminController();
        
        if (count($url) === 1) {
            $controller->index();
            exit;
        }
        
        if ($url[1] === 'login') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->authenticate();
            } else {
                $controller->login();
            }
            exit;
        }
        
        if ($url[1] === 'logout') {
            $controller->logout();
            exit;
        }
        
        if ($url[1] === 'publicaciones') {
            if (count($url) === 2) {
                $controller->publicaciones();
                exit;
            }
            
            if (count($url) === 3 && is_numeric($url[2])) {
                $controller->verPublicacion($url[2]);
                exit;
            }
            
            if (count($url) === 4 && is_numeric($url[2])) {
                if ($url[3] === 'aprobar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->aprobarPublicacion($url[2]);
                    exit;
                }
                if ($url[3] === 'rechazar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->rechazarPublicacion($url[2]);
                    exit;
                }
            }
        }
        
        http_response_code(404);
        echo "Ruta admin no encontrada";
        exit;
    }
    
    // 8. Cargar controlador
    $controllerFile = APP_PATH . '/controllers/' . $controllerName . '.php';
    
    if (!file_exists($controllerFile)) {
        throw new Exception("Controlador no encontrado: $controllerName");
    }
    
    require_once $controllerFile;
    
    $controllerClass = 'App\\Controllers\\' . $controllerName;
    if (!class_exists($controllerClass)) {
        $controllerClass = $controllerName;
    }
    
    if (!class_exists($controllerClass)) {
        throw new Exception("Clase no encontrada: $controllerClass");
    }
    
    $controller = new $controllerClass();
    
    if (!method_exists($controller, $method)) {
        throw new Exception("Método no encontrado: $method en $controllerClass");
    }
    
    // 9. Ejecutar
    call_user_func_array([$controller, $method], $params);
    
} catch (Exception $e) {
    error_log("ERROR en index.php: " . $e->getMessage());
    error_log("File: " . $e->getFile() . " Line: " . $e->getLine());
    
    http_response_code(500);
    echo "<!DOCTYPE html>";
    echo "<html><head><title>Error</title></head><body>";
    echo "<h1>Error del Sistema</h1>";
    echo "<p>Ha ocurrido un error. Por favor, contacte al administrador.</p>";
    if (APP_DEBUG) {
        echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    }
    echo "</body></html>";
}
