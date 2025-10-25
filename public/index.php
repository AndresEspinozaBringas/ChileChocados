<?php

/**
 * ChileChocados - Front Controller
 * Punto de entrada principal de la aplicación
 */

// Cargar configuración
require_once __DIR__ . '/../app/config/config.php';

// Intentar cargar database si existe
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

// Obtener la URL solicitada
$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

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
];

// Aplicar rutas especiales si coincide
if (!empty($url[0]) && isset($specialRoutes[$url[0]])) {
    $controllerName = $specialRoutes[$url[0]]['controller'];
    $method = $specialRoutes[$url[0]]['method'];
    $params = array_slice($url, 1);  // Parámetros después de la ruta
}

// Manejar métodos POST específicos para autenticación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($url[0])) {
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
}

// Ruta del controlador
$controllerFile = APP_PATH . '/controllers/' . $controllerName . '.php';

// Verificar si existe el controlador
if (file_exists($controllerFile)) {
    require_once $controllerFile;

    // Intentar con namespace primero
    $controllerClass = 'App\\Controllers\\' . $controllerName;
    if (!class_exists($controllerClass)) {
        // Intentar sin namespace (compatibilidad)
        $controllerClass = $controllerName;
    }

    // Instanciar controlador
    if (class_exists($controllerClass)) {
        $controller = new $controllerClass();

        // Verificar si existe el método
        if (method_exists($controller, $method)) {
            call_user_func_array([$controller, $method], $params);
        } else {
            // Método no encontrado
            http_response_code(404);
            $notFoundPage = APP_PATH . '/views/pages/404.php';
            if (file_exists($notFoundPage)) {
                require_once $notFoundPage;
            } else {
                echo "Error 404: Método '$method' no encontrado en $controllerName";
            }
        }
    } else {
        // Clase no encontrada
        http_response_code(404);
        $notFoundPage = APP_PATH . '/views/pages/404.php';
        if (file_exists($notFoundPage)) {
            require_once $notFoundPage;
        } else {
            echo "Error 404: Controlador '$controllerClass' no encontrado";
        }
    }
} else {
    // Controlador no encontrado
    http_response_code(404);
    $notFoundPage = APP_PATH . '/views/pages/404.php';
    if (file_exists($notFoundPage)) {
        require_once $notFoundPage;
    } else {
        echo "Error 404: Archivo de controlador no encontrado: $controllerFile";
    }
}
