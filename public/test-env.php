<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/vendor/autoload.php';

try {
    $dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
    $dotenv->load();
    
    echo "✓ .env cargado correctamente<br>";
    echo "DB_HOST: " . getenv('DB_HOST') . "<br>";
    echo "DB_NAME: " . getenv('DB_NAME') . "<br>";
    echo "DB_USER: " . getenv('DB_USER') . "<br>";
    
    // Probar conexión
    $host = getenv('DB_HOST');
    $port = getenv('DB_PORT') ?: 3306;
    $dbname = getenv('DB_NAME');
    $username = getenv('DB_USER');
    $password = getenv('DB_PASS');
    
    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    
    echo "✓ Conexión a base de datos exitosa<br>";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage();
}
