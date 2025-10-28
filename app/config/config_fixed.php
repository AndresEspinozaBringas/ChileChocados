<?php
/**
 * ChileChocados - Configuración Principal (FIXED)
 * Versión corregida con mejor manejo de errores
 */

// Habilitar reporte de errores en desarrollo
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// Definir constantes de rutas
define('ROOT_PATH', dirname(dirname(__DIR__)));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');
define('LOGS_PATH', ROOT_PATH . '/logs');

// Configurar log de errores
ini_set('error_log', LOGS_PATH . '/php_errors.log');

// Cargar variables de entorno desde .env
function loadEnv($path)
{
    if (!file_exists($path)) {
        error_log("ERROR: Archivo .env no encontrado en: $path");
        die('Error de configuración. Contacte al administrador.');
    }

    try {
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            throw new Exception("No se pudo leer el archivo .env");
        }

        foreach ($lines as $line) {
            $line = trim($line);
            
            // Ignorar comentarios y líneas vacías
            if (empty($line) || strpos($line, '#') === 0) {
                continue;
            }

            // Verificar que la línea tenga el formato correcto
            if (strpos($line, '=') === false) {
                continue;
            }

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            // Remover comillas si existen
            $value = trim($value, '"\'');

            if (!empty($name) && !array_key_exists($name, $_ENV)) {
                putenv("$name=$value");
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    } catch (Exception $e) {
        error_log("ERROR al cargar .env: " . $e->getMessage());
        die('Error de configuración. Contacte al administrador.');
    }
}

// Cargar variables de entorno
loadEnv(ROOT_PATH . '/.env');

// Verificar variables críticas
$required_vars = ['DB_HOST', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'];
foreach ($required_vars as $var) {
    if (!isset($_ENV[$var]) || empty($_ENV[$var])) {
        error_log("ERROR: Variable de entorno requerida no definida: $var");
        die('Error de configuración. Contacte al administrador.');
    }
}

// Configuración de la aplicación
define('APP_NAME', $_ENV['APP_NAME'] ?? 'ChileChocados');
define('APP_ENV', $_ENV['APP_ENV'] ?? 'production');
define('APP_DEBUG', filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN));
define('APP_URL', $_ENV['APP_URL'] ?? 'https://chilechocados.cl');
define('BASE_URL', APP_URL);

// Configuración de base de datos
define('DB_HOST', $_ENV['DB_HOST']);
define('DB_PORT', $_ENV['DB_PORT'] ?? '3306');
define('DB_DATABASE', $_ENV['DB_DATABASE']);
define('DB_USERNAME', $_ENV['DB_USERNAME']);
define('DB_PASSWORD', $_ENV['DB_PASSWORD']);
define('DB_CHARSET', $_ENV['DB_CHARSET'] ?? 'utf8mb4');

// Configuración de sesión
define('SESSION_LIFETIME', (int)($_ENV['SESSION_LIFETIME'] ?? 120));
define('SESSION_SECURE', filter_var($_ENV['SESSION_SECURE'] ?? true, FILTER_VALIDATE_BOOLEAN));
define('SESSION_HTTPONLY', filter_var($_ENV['SESSION_HTTPONLY'] ?? true, FILTER_VALIDATE_BOOLEAN));

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME * 60,
        'path' => '/',
        'domain' => parse_url(APP_URL, PHP_URL_HOST),
        'secure' => SESSION_SECURE,
        'httponly' => SESSION_HTTPONLY,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// Configuración de zona horaria
date_default_timezone_set('America/Santiago');

// Autoload de Composer
$autoload = ROOT_PATH . '/vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
} else {
    error_log("WARNING: vendor/autoload.php no encontrado");
}

// Log de inicio exitoso
error_log("ChileChocados iniciado correctamente - " . date('Y-m-d H:i:s'));
