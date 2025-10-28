<?php
/**
 * ChileChocados - Front Controller
 * Enrutamiento principal de la aplicación
 * MODIFICADO: /admin/login redirige a /login
 */

// Cargar configuración y archivos esenciales (config.php maneja la sesión)
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';

// Cargar helpers
require_once APP_PATH . '/helpers/Session.php';
require_once APP_PATH . '/helpers/Auth.php';

// Log de debugging POST
$logFile = __DIR__ . '/../logs/post_debug.log';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    file_put_contents($logFile, "\n\n=== POST REQUEST ===\n", FILE_APPEND);
    file_put_contents($logFile, "Time: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    file_put_contents($logFile, "GET url param: " . ($_GET['url'] ?? 'NO URL') . "\n", FILE_APPEND);
}

// Obtener la URL solicitada
$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

// DEBUG: Log de la URL parseada
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    file_put_contents($logFile, "URL array: " . print_r($url, true) . "\n", FILE_APPEND);
}

// Determinar controlador y acción
$controllerName = !empty($url[0]) ? ucfirst($url[0]) . 'Controller' : 'HomeController';
$method = $url[1] ?? 'index';
$params = array_slice($url, 2);

// ====================================
// MAPA DE RUTAS ESPECIALES
// ====================================

$specialRoutes = [
    // Rutas de autenticación
    'registro' => ['controller' => 'AuthController', 'method' => 'register'],
    'login' => ['controller' => 'AuthController', 'method' => 'login'],
    'logout' => ['controller' => 'AuthController', 'method' => 'logout'],
    'recuperar-contrasena' => ['controller' => 'AuthController', 'method' => 'forgotPassword'],
    'reset-password' => ['controller' => 'AuthController', 'method' => 'resetPassword'],
    'verificar-email' => ['controller' => 'AuthController', 'method' => 'verifyEmail'],
    // Rutas legales
    'terminos' => ['controller' => 'LegalController', 'method' => 'terminos'],
    'privacidad' => ['controller' => 'LegalController', 'method' => 'privacidad'],
    'cookies' => ['controller' => 'LegalController', 'method' => 'cookies'],
    'denuncias' => ['controller' => 'LegalController', 'method' => 'denuncias'],
    // Ruta de contacto
    'contacto' => ['controller' => 'ContactoController', 'method' => 'index'],
    
    // Rutas API
    'api' => ['controller' => 'ApiController', 'method' => 'index'],
    
    // ====================================
    // NUEVAS RUTAS - PUBLICACIONES
    // ====================================
    'publicaciones' => ['controller' => 'PublicacionController', 'method' => 'index'],
    'publicacion' => ['controller' => 'PublicacionController', 'method' => 'show'],
    'detalle' => ['controller' => 'PublicacionController', 'method' => 'show'],
    'publicar' => ['controller' => 'PublicacionController', 'method' => 'create'],
    'vender' => ['controller' => 'PublicacionController', 'method' => 'sell'],
    'listado' => ['controller' => 'PublicacionController', 'method' => 'index'],
    // ====================================
    // NUEVAS RUTAS - CATEGORÍAS
    // ====================================
    'categorias' => ['controller' => 'CategoriaController', 'method' => 'index'],
    'categoria' => ['controller' => 'CategoriaController', 'method' => 'show'],
    
    // ====================================
    // RUTAS ADMIN
    // ====================================
    'admin' => ['controller' => 'AdminController', 'method' => 'index'],
];

// Aplicar rutas especiales si coincide
if (!empty($url[0]) && isset($specialRoutes[$url[0]])) {
    $controllerName = $specialRoutes[$url[0]]['controller'];
    $method = $specialRoutes[$url[0]]['method'];
    $params = array_slice($url, 1);  // Parámetros después de la ruta
}

// ====================================
// MANEJO DE RUTAS API
// ====================================
if (!empty($url[0]) && $url[0] === 'api') {
    if (!empty($url[1]) && $url[1] === 'comunas') {
        $controllerName = 'PublicacionController';
        $method = 'getComunas';
        $params = [];
    }
}

// ====================================
// MANEJO DE MÉTODOS POST ESPECÍFICOS
// ====================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($url[0])) {
    // Autenticación
    if ($url[0] === 'registro') {
        $controllerName = 'AuthController';
        $method = 'processRegister';
    } elseif ($url[0] === 'login') {
        $controllerName = 'AuthController';
        $method = 'authenticate';
    } elseif ($url[0] === 'recuperar-contrasena') {
        $controllerName = 'AuthController';
        $method = 'sendResetLink';
    } elseif ($url[0] === 'reset-password') {
        $controllerName = 'AuthController';
        $method = 'updatePassword';
    }
    // Publicaciones - POST
    elseif ($url[0] === 'publicar' && isset($url[1]) && $url[1] === 'procesar') {
        $controllerName = 'PublicacionController';
        $method = 'store';
        $params = [];
    }
    elseif ($url[0] === 'publicaciones' && isset($url[1])) {
        $controllerName = 'PublicacionController';
        if ($url[1] === 'store') {
            $method = 'store';
            $params = [];
        } elseif (isset($url[2]) && $url[2] === 'update') {
            $method = 'update';
            $params = [$url[1]];  // ID de la publicación
        } elseif (isset($url[2]) && $url[2] === 'eliminar') {
            $method = 'destroy';
            $params = [$url[1]];  // ID de la publicación
        }
    }
}

// ====================================
// RUTAS ESPECIALES PARA PUBLICACIONES
// ====================================

// Ruta: /publicaciones/approval
if (!empty($url[0]) && $url[0] === 'publicaciones' && isset($url[1]) && $url[1] === 'approval') {
    $controllerName = 'PublicacionController';
    $method = 'approval';
    $params = [];
}

// Ruta: /publicaciones/{id}/editar
if (!empty($url[0]) && $url[0] === 'publicaciones' && isset($url[1]) && is_numeric($url[1]) && isset($url[2]) && $url[2] === 'editar') {
    $controllerName = 'PublicacionController';
    $method = 'edit';
    $params = [$url[1]];  // ID de la publicación
}

// ====================================
// RUTAS ADMIN - SISTEMA DE MODERACIÓN
// MODIFICADO: /admin/login redirige a /login
// ====================================

// Rutas específicas para administración que deben manejarse antes del routing genérico
if (!empty($url[0]) && $url[0] === 'admin') {
    
    // CRÍTICO: Redirigir /admin/login a /login (login unificado)
    if (!empty($url[1]) && $url[1] === 'login') {
        header('Location: /login');
        exit;
    }
    
    require_once APP_PATH . '/controllers/AdminController.php';
    $controller = new App\Controllers\AdminController();
    
    // /admin - Dashboard principal (redirige a publicaciones)
    if (count($url) === 1) {
        $controller->index();
        exit;
    }
    
    // /admin/logout - Cerrar sesión
    if ($url[1] === 'logout') {
        $controller->logout();
        exit;
    }
    
    // /admin/publicaciones - Lista de publicaciones
    if ($url[1] === 'publicaciones' && count($url) === 2) {
        $controller->publicaciones();
        exit;
    }
    
    // /admin/publicaciones/{id} - Ver detalle de publicación
    if ($url[1] === 'publicaciones' && count($url) === 3 && is_numeric($url[2])) {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $controller->verPublicacion($url[2]);
            exit;
        }
    }
    
    // /admin/publicaciones/{id}/aprobar
    if ($url[1] === 'publicaciones' && count($url) === 4 && is_numeric($url[2]) && $url[3] === 'aprobar') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->aprobarPublicacion($url[2]);
            exit;
        }
    }
    
    // /admin/publicaciones/{id}/rechazar
    if ($url[1] === 'publicaciones' && count($url) === 4 && is_numeric($url[2]) && $url[3] === 'rechazar') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->rechazarPublicacion($url[2]);
            exit;
        }
    }
}

// ====================================
// CARGA ESTÁNDAR DE CONTROLADOR
// ====================================

// Cargar controlador
$controllerFile = APP_PATH . '/controllers/' . $controllerName . '.php';

if (!file_exists($controllerFile)) {
    http_response_code(404);
    echo "404 - Página no encontrada";
    exit;
}

require_once $controllerFile;

// Instanciar controlador
$controllerClass = 'App\\Controllers\\' . $controllerName;

if (!class_exists($controllerClass)) {
    http_response_code(500);
    echo "Error: Controlador no encontrado";
    exit;
}

$controller = new $controllerClass();

// Ejecutar método
if (!method_exists($controller, $method)) {
    http_response_code(404);
    echo "404 - Método no encontrado";
    exit;
}

// Llamar al método con los parámetros
call_user_func_array([$controller, $method], $params);