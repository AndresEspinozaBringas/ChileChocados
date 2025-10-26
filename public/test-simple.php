<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "PHP funciona<br>";

define('BASE_PATH', dirname(__DIR__));
echo "BASE_PATH: " . BASE_PATH . "<br>";

if (file_exists(BASE_PATH . '/vendor/autoload.php')) {
    echo "✓ vendor/autoload.php existe<br>";
    require_once BASE_PATH . '/vendor/autoload.php';
} else {
    die("✗ vendor/autoload.php NO existe");
}

try {
    $dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
    $dotenv->load();
    echo "✓ .env cargado<br>";
    echo "DB_NAME: " . ($_ENV['DB_NAME'] ?? 'NO DEFINIDO') . "<br>";
} catch (Exception $e) {
    echo "✗ Error cargando .env: " . $e->getMessage() . "<br>";
}

echo "✓ Todo OK";
