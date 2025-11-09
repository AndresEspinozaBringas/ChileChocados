<?php
/**
 * Migración: Agregar campo avatar a tabla usuarios
 * Ejecutar: php add_avatar_field.php
 */

// Cargar configuración del proyecto
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/config/database.php';

try {
    // Usar la conexión del proyecto
    $db = \App\Core\Database::getInstance()->getConnection();
    
    echo "=== AGREGAR CAMPO AVATAR ===\n\n";
    echo "✓ Conectado a la base de datos\n";
    
    // Verificar si el campo ya existe
    $stmt = $db->prepare("
        SELECT COUNT(*) 
        FROM information_schema.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'usuarios' 
        AND COLUMN_NAME = 'avatar'
    ");
    $stmt->execute();
    $exists = $stmt->fetchColumn() > 0;
    
    if ($exists) {
        echo "⚠ El campo 'avatar' ya existe en la tabla usuarios\n";
    } else {
        echo "Agregando campo 'avatar'...\n";
        $db->exec("
            ALTER TABLE usuarios 
            ADD COLUMN avatar VARCHAR(255) NULL COMMENT 'Nombre del archivo de avatar (thumbnail 200x200)'
        ");
        echo "✓ Campo 'avatar' agregado exitosamente\n";
    }
    
    // Verificar estructura
    echo "\n=== VERIFICACIÓN ===\n";
    $stmt = $db->query("SHOW COLUMNS FROM usuarios LIKE 'avatar'");
    $column = $stmt->fetch();
    
    if ($column) {
        echo "Campo: {$column['Field']}\n";
        echo "Tipo: {$column['Type']}\n";
        echo "Nulo: {$column['Null']}\n";
        echo "Default: " . ($column['Default'] ?? 'NULL') . "\n";
    }
    
    echo "\n✅ Migración completada!\n";
    
} catch (PDOException $e) {
    echo "\n❌ Error de base de datos:\n";
    echo $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "\n❌ Error:\n";
    echo $e->getMessage() . "\n";
    exit(1);
}
