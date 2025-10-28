<?php
/**
 * Script de Diagnóstico para ChileChocados
 * Sube este archivo al servidor y accede a: https://chilechocados.cl/diagnostico.php
 */

header('Content-Type: text/plain; charset=utf-8');

echo "========================================\n";
echo "DIAGNÓSTICO - ChileChocados\n";
echo "========================================\n\n";

// 1. Información de PHP
echo "1. VERSIÓN DE PHP\n";
echo "   PHP Version: " . phpversion() . "\n\n";

// 2. Verificar extensiones necesarias
echo "2. EXTENSIONES PHP\n";
$extensions = ['pdo', 'pdo_mysql', 'mbstring', 'json', 'openssl'];
foreach ($extensions as $ext) {
    $loaded = extension_loaded($ext) ? '✓' : '✗';
    echo "   $loaded $ext\n";
}
echo "\n";

// 3. Verificar rutas
echo "3. RUTAS DEL SISTEMA\n";
echo "   Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "   Script Filename: " . $_SERVER['SCRIPT_FILENAME'] . "\n";
echo "   Current Dir: " . __DIR__ . "\n";
echo "   Parent Dir: " . dirname(__DIR__) . "\n\n";

// 4. Verificar archivo .env
echo "4. ARCHIVO .ENV\n";
$env_path = dirname(__DIR__) . '/.env';
if (file_exists($env_path)) {
    echo "   ✓ Archivo .env existe\n";
    echo "   Permisos: " . substr(sprintf('%o', fileperms($env_path)), -4) . "\n";
    echo "   Tamaño: " . filesize($env_path) . " bytes\n";
} else {
    echo "   ✗ Archivo .env NO existe\n";
    echo "   Ruta buscada: $env_path\n";
}
echo "\n";

// 5. Verificar vendor/autoload.php
echo "5. COMPOSER AUTOLOAD\n";
$autoload_path = dirname(__DIR__) . '/vendor/autoload.php';
if (file_exists($autoload_path)) {
    echo "   ✓ vendor/autoload.php existe\n";
} else {
    echo "   ✗ vendor/autoload.php NO existe\n";
    echo "   Ruta buscada: $autoload_path\n";
}
echo "\n";

// 6. Verificar permisos de directorios
echo "6. PERMISOS DE DIRECTORIOS\n";
$dirs = [
    'logs' => dirname(__DIR__) . '/logs',
    'uploads' => __DIR__ . '/uploads',
    'public' => __DIR__
];

foreach ($dirs as $name => $path) {
    if (file_exists($path)) {
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        $writable = is_writable($path) ? '✓' : '✗';
        echo "   $writable $name: $perms\n";
    } else {
        echo "   ✗ $name: NO existe\n";
    }
}
echo "\n";

// 7. Verificar .htaccess
echo "7. ARCHIVO .HTACCESS\n";
$htaccess_root = dirname(__DIR__) . '/.htaccess';
$htaccess_public = __DIR__ . '/.htaccess';

if (file_exists($htaccess_root)) {
    echo "   ✓ .htaccess raíz existe\n";
} else {
    echo "   ✗ .htaccess raíz NO existe\n";
}

if (file_exists($htaccess_public)) {
    echo "   ✓ .htaccess public existe\n";
} else {
    echo "   ✗ .htaccess public NO existe\n";
}
echo "\n";

// 8. Intentar cargar .env
echo "8. PRUEBA DE CARGA .ENV\n";
try {
    if (file_exists($autoload_path)) {
        require_once $autoload_path;
        
        if (class_exists('Dotenv\Dotenv')) {
            echo "   ✓ Clase Dotenv disponible\n";
            
            $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
            $dotenv->load();
            
            echo "   ✓ .env cargado exitosamente\n";
            echo "   DB_HOST: " . ($_ENV['DB_HOST'] ?? 'NO DEFINIDO') . "\n";
            echo "   DB_DATABASE: " . ($_ENV['DB_DATABASE'] ?? 'NO DEFINIDO') . "\n";
        } else {
            echo "   ✗ Clase Dotenv NO disponible\n";
        }
    } else {
        echo "   ✗ No se puede cargar autoload.php\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}
echo "\n";

// 9. Verificar conexión a base de datos
echo "9. PRUEBA DE CONEXIÓN A BD\n";
try {
    if (isset($_ENV['DB_HOST'])) {
        $dsn = "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_DATABASE']};charset=utf8mb4";
        $pdo = new PDO($dsn, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);
        echo "   ✓ Conexión a BD exitosa\n";
        echo "   Base de datos: {$_ENV['DB_DATABASE']}\n";
    } else {
        echo "   ✗ Variables de entorno no cargadas\n";
    }
} catch (PDOException $e) {
    echo "   ✗ Error de conexión: " . $e->getMessage() . "\n";
}
echo "\n";

// 10. Variables de servidor
echo "10. VARIABLES DE SERVIDOR\n";
echo "   SERVER_SOFTWARE: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'N/A') . "\n";
echo "   SERVER_NAME: " . ($_SERVER['SERVER_NAME'] ?? 'N/A') . "\n";
echo "   DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . "\n";
echo "   REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "\n";
echo "\n";

echo "========================================\n";
echo "FIN DEL DIAGNÓSTICO\n";
echo "========================================\n";
