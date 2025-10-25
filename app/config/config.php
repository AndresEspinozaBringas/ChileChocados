<?php  // phpcs:ignore PSR1.Files.SideEffects.FoundWithSymbols

/**
 * ChileChocados - Configuración Principal
 * Archivo de configuración central de la aplicación
 */

// Definir constantes de rutas
define('ROOT_PATH', dirname(dirname(__DIR__)));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');
define('LOGS_PATH', ROOT_PATH . '/logs');

// Cargar variables de entorno desde .env
function loadEnv($path)
{
    if (!file_exists($path)) {
        die('Archivo .env no encontrado. Copie .env.example a .env y configure sus variables.');
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0)
            continue;

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if (!array_key_exists($name, $_ENV)) {
            putenv("$name=$value");
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Cargar variables de entorno
loadEnv(ROOT_PATH . '/.env');

// Configuración de PHP
ini_set('display_errors', getenv('APP_DEBUG') === 'true' ? 1 : 0);
error_reporting(getenv('APP_DEBUG') === 'true' ? E_ALL : 0);
ini_set('log_errors', 1);
ini_set('error_log', LOGS_PATH . '/php_errors.log');

// Configuración de zona horaria
date_default_timezone_set('America/Santiago');

// Configuración de sesiones
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
session_name(getenv('SESSION_NAME') ?: 'chilechocados_session');

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuración de la aplicación
define('APP_NAME', getenv('APP_NAME') ?: 'ChileChocados');
define('APP_URL', getenv('APP_URL') ?: 'http://localhost');
define('APP_ENV', getenv('APP_ENV') ?: 'development');

// Configuración de uploads
define('UPLOAD_MAX_SIZE', getenv('UPLOAD_MAX_SIZE') ?: 5242880);  // 5MB
define('UPLOAD_ALLOWED_TYPES', explode(',', getenv('UPLOAD_ALLOWED_TYPES') ?: 'jpg,jpeg,png,gif'));

// Autoload con soporte para namespaces
spl_autoload_register(function ($class) {
    // Convertir namespace a ruta de archivo
    // Ej: App\Models\Usuario -> app/models/Usuario.php
    // Ej: App\Core\Database -> app/core/Database.php
    
    // Solo procesar clases con namespace App\
    if (strpos($class, 'App\\') !== 0) {
        return;
    }
    
    // Remover el prefijo App\
    $classPath = substr($class, 4); // Remover "App\"
    
    // Convertir namespace separators a directory separators
    $classPath = str_replace('\\', '/', $classPath);
    
    // Separar directorio y nombre de clase
    $parts = explode('/', $classPath);
    $className = array_pop($parts);
    $directory = strtolower(implode('/', $parts));
    
    // Construir la ruta del archivo
    $file = APP_PATH . '/' . $directory . '/' . $className . '.php';
    
    // Si el archivo existe, cargarlo
    if (file_exists($file)) {
        require_once $file;
        return;
    }
    
    // Log para debugging (solo en desarrollo)
    if (getenv('APP_DEBUG') === 'true') {
        error_log("Autoloader: No se pudo cargar la clase {$class}. Buscado en: {$file}");
    }
});

// Cargar helpers
require_once ROOT_PATH . '/includes/helpers.php';
