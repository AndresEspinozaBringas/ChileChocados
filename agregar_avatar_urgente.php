<?php
/**
 * SCRIPT URGENTE: Agregar campo avatar a usuarios
 * Ejecutar desde navegador: https://tudominio.com/agregar_avatar_urgente.php
 * O desde línea de comandos: php agregar_avatar_urgente.php
 */

// Cargar configuración
require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/config/database.php';

// Solo permitir ejecución desde CLI o localhost
if (php_sapi_name() !== 'cli' && $_SERVER['REMOTE_ADDR'] !== '127.0.0.1' && $_SERVER['REMOTE_ADDR'] !== '::1') {
    die('Acceso denegado. Este script solo puede ejecutarse desde localhost o CLI.');
}

try {
    // Conectar a la base de datos
    $db = \App\Core\Database::getInstance()->getConnection();
    
    echo "<h2>MIGRACIÓN: Agregar campo avatar</h2>";
    echo "<pre>";
    
    echo "✓ Conectado a la base de datos\n\n";
    
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
        echo "No es necesario ejecutar la migración.\n";
    } else {
        echo "Agregando campo 'avatar' a tabla usuarios...\n";
        
        $db->exec("
            ALTER TABLE usuarios 
            ADD COLUMN avatar VARCHAR(255) NULL 
            COMMENT 'Nombre del archivo de avatar (thumbnail 200x200)'
        ");
        
        echo "✓ Campo 'avatar' agregado exitosamente\n\n";
    }
    
    // Verificar estructura
    echo "=== VERIFICACIÓN ===\n";
    $stmt = $db->query("SHOW COLUMNS FROM usuarios LIKE 'avatar'");
    $column = $stmt->fetch();
    
    if ($column) {
        echo "Campo: {$column['Field']}\n";
        echo "Tipo: {$column['Type']}\n";
        echo "Nulo: {$column['Null']}\n";
        echo "Default: " . ($column['Default'] ?? 'NULL') . "\n";
        echo "\n✅ MIGRACIÓN COMPLETADA EXITOSAMENTE!\n";
        echo "\nAhora puedes subir avatares de perfil.\n";
    } else {
        echo "❌ ERROR: El campo no se pudo agregar.\n";
    }
    
    echo "</pre>";
    
    // Crear carpeta de avatares si no existe
    $avatarDir = __DIR__ . '/public/uploads/avatars';
    if (!is_dir($avatarDir)) {
        mkdir($avatarDir, 0777, true);
        echo "<p>✓ Carpeta de avatares creada: /public/uploads/avatars</p>";
    }
    
    echo "<p><strong>Siguiente paso:</strong> Elimina este archivo (agregar_avatar_urgente.php) por seguridad.</p>";
    
} catch (PDOException $e) {
    echo "<pre>";
    echo "❌ Error de base de datos:\n";
    echo $e->getMessage() . "\n";
    echo "</pre>";
    exit(1);
} catch (Exception $e) {
    echo "<pre>";
    echo "❌ Error:\n";
    echo $e->getMessage() . "\n";
    echo "</pre>";
    exit(1);
}
