<?php

/**
 * ChileChocados - Front Controller
 * Punto de entrada principal de la aplicación
 */

// Cargar configuración
require_once __DIR__ . '/../app/config/config.php';

// Cargar clase Database desde core
$dbCoreFile = __DIR__ . '/../app/core/Database.php';
if (file_exists($dbCoreFile)) {
    require_once $dbCoreFile;
}

// Cargar configuración de database (helpers)
$dbFile = __DIR__ . '/../app/config/database.php';
if (file_exists($dbFile)) {
    require_once $dbFile;
}

// Cargar helpers necesarios para autenticación
$sessionHelper = __DIR__ . '/../app/helpers/Session.php';
if (file_exists($sessionHelper)) {
    require_once $sessionHelper;
}

$authHelper = __DIR__ . '/../app/helpers/Auth.php';
if (file_exists($authHelper)) {
    require_once $authHelper;
}

// Iniciar sesión si la clase existe
if (class_exists('App\Helpers\Session')) {
    \App\Helpers\Session::start();
} elseif (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargar .env si existe
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0)
            continue;
        if (strpos($line, '=') === false)
            continue;
        list($key, $value) = explode('=', $line, 2);
        putenv(trim($key) . '=' . trim($value));
    }
}

// DEBUG: Siempre crear log para POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $logFile = __DIR__ . '/logs/debug.txt';
    @mkdir(__DIR__ . '/logs', 0777, true);
    file_put_contents($logFile, "\n=== " . date('Y-m-d H:i:s') . " ===\n", FILE_APPEND);
    file_put_contents($logFile, "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n", FILE_APPEND);
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
// RUTAS API (AJAX)
// ====================================

if (!empty($url[0]) && $url[0] === 'api') {
    if (isset($url[1]) && $url[1] === 'categorias') {
        $controllerName = 'CategoriaController';

        // /api/categorias/buscar
        if (isset($url[2]) && $url[2] === 'buscar') {
            $method = 'buscar';
            $params = [];
        }
        // /api/categorias/{id}/subcategorias
        elseif (isset($url[2]) && is_numeric($url[2]) && isset($url[3]) && $url[3] === 'subcategorias') {
            $method = 'getSubcategorias';
            $params = [$url[2]];  // ID de la categoría
        }
    }
}

// Ruta del controlador
$controllerFile = APP_PATH . '/controllers/' . $controllerName . '.php';

// DEBUG: Log del controlador y método
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    file_put_contents(__DIR__ . '/logs/debug.txt', "Controller: $controllerName\n", FILE_APPEND);
    file_put_contents(__DIR__ . '/logs/debug.txt', "Method: $method\n", FILE_APPEND);
    file_put_contents(__DIR__ . '/logs/debug.txt', "Params: " . print_r($params, true) . "\n", FILE_APPEND);
    file_put_contents(__DIR__ . '/logs/debug.txt', "Controller file: $controllerFile\n", FILE_APPEND);
    file_put_contents(__DIR__ . '/logs/debug.txt', "File exists: " . (file_exists($controllerFile) ? 'YES' : 'NO') . "\n", FILE_APPEND);
}

// Verificar si existe el controlador
if (file_exists($controllerFile)) {
    require_once $controllerFile;

    // Intentar con namespace primero
    $controllerClass = 'App\\Controllers\\' . $controllerName;

    if (!class_exists($controllerClass)) {
        // Si no existe con namespace, intentar sin namespace
        $controllerClass = $controllerName;
    }

    if (class_exists($controllerClass)) {
        $controller = new $controllerClass();

        if (method_exists($controller, $method)) {
            // DEBUG
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                file_put_contents(__DIR__ . '/logs/debug.txt', "Calling method: $controllerClass::$method\n", FILE_APPEND);
            }
            
            // Llamar al método con parámetros
            call_user_func_array([$controller, $method], $params);
        } else {
            // Método no encontrado - 404
            http_response_code(404);
            if (file_exists(APP_PATH . '/views/pages/404.php')) {
                require_once APP_PATH . '/views/pages/404.php';
            } else {
                echo "404 - Método no encontrado: $method";
            }
        }
    } else {
        // Clase no encontrada - 404
        http_response_code(404);
        if (file_exists(APP_PATH . '/views/pages/404.php')) {
            require_once APP_PATH . '/views/pages/404.php';
        } else {
            echo "404 - Controlador no encontrado: $controllerClass";
        }
    }
} else {
    // Controlador no existe - 404
    http_response_code(404);
    if (file_exists(APP_PATH . '/views/pages/404.php')) {
        require_once APP_PATH . '/views/pages/404.php';
    } else {
        echo '404 - Página no encontrada';
    }
}
