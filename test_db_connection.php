<?php
/**
 * Script de prueba de conexión a la base de datos
 */

// Cargar configuración
require_once __DIR__ . '/app/config/config.php';

// Cargar clase Database
require_once __DIR__ . '/app/core/Database.php';

use App\Core\Database;

echo "<h1>Test de Conexión a Base de Datos</h1>";

try {
    echo "<p>Intentando conectar a la base de datos...</p>";
    
    // Obtener instancia de Database
    $db = Database::getInstance();
    echo "<p style='color: green;'>✓ Instancia de Database creada correctamente</p>";
    
    // Obtener conexión PDO
    $connection = $db->getConnection();
    echo "<p style='color: green;'>✓ Conexión PDO obtenida correctamente</p>";
    
    // Probar una consulta simple
    $stmt = $connection->query("SELECT 1 as test");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['test'] == 1) {
        echo "<p style='color: green;'>✓ Consulta de prueba ejecutada correctamente</p>";
    }
    
    // Mostrar información de la base de datos
    $stmt = $connection->query("SELECT DATABASE() as db_name");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p><strong>Base de datos actual:</strong> " . $result['db_name'] . "</p>";
    
    // Listar tablas
    $stmt = $connection->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<p><strong>Tablas en la base de datos:</strong></p>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>" . htmlspecialchars($table) . "</li>";
    }
    echo "</ul>";
    
    echo "<h2 style='color: green;'>✓ Conexión exitosa</h2>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>✗ Error de conexión</h2>";
    echo "<p style='color: red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
