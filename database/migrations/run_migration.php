<?php
/**
 * Script para ejecutar la migración de publicaciones_fotos
 * Ejecutar desde la línea de comandos: php database/migrations/run_migration.php
 */

// Cargar configuración
require_once __DIR__ . '/../../app/config/database.php';

try {
    // Conectar a la base de datos
    $db = getDB();
    
    echo "Ejecutando migración...\n\n";
    
    // Leer archivo SQL
    $sql = file_get_contents(__DIR__ . '/fix_publicaciones_fotos.sql');
    
    // Dividir en statements individuales
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            return !empty($stmt) && !preg_match('/^--/', $stmt);
        }
    );
    
    // Ejecutar cada statement
    foreach ($statements as $statement) {
        if (empty(trim($statement))) {
            continue;
        }
        
        echo "Ejecutando: " . substr($statement, 0, 80) . "...\n";
        
        try {
            $db->exec($statement);
            echo "✓ OK\n\n";
        } catch (PDOException $e) {
            echo "✗ Error: " . $e->getMessage() . "\n\n";
        }
    }
    
    // Crear directorio de uploads si no existe
    $uploadDir = __DIR__ . '/../../public/uploads/publicaciones';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
        echo "✓ Directorio de uploads creado: $uploadDir\n";
    } else {
        echo "✓ Directorio de uploads ya existe\n";
    }
    
    echo "\n✓ Migración completada exitosamente\n";
    
} catch (Exception $e) {
    echo "✗ Error fatal: " . $e->getMessage() . "\n";
    exit(1);
}
