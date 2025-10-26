<?php
/**
 * Script para aplicar correcciones a la base de datos
 */

require_once __DIR__ . '/../../app/config/database.php';

try {
    $db = getDB();
    
    echo "Aplicando correcciones...\n\n";
    
    // 1. Agregar campo tipificacion
    echo "1. Agregando campo tipificacion...\n";
    try {
        $db->exec("ALTER TABLE publicaciones ADD COLUMN tipificacion VARCHAR(50) DEFAULT NULL COMMENT 'chocado o mecanico' AFTER titulo");
        echo "   ✓ Campo tipificacion agregado\n\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "   ✓ Campo tipificacion ya existe\n\n";
        } else {
            echo "   ✗ Error: " . $e->getMessage() . "\n\n";
        }
    }
    
    // 2. Crear tabla publicaciones_fotos
    echo "2. Creando tabla publicaciones_fotos...\n";
    try {
        $sql = "CREATE TABLE IF NOT EXISTS publicaciones_fotos (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            publicacion_id INT UNSIGNED NOT NULL,
            url VARCHAR(255) NOT NULL COMMENT 'Ruta relativa de la imagen',
            orden TINYINT UNSIGNED DEFAULT 1 COMMENT 'Orden de visualización',
            es_principal TINYINT(1) DEFAULT 0 COMMENT '1 si es la foto principal',
            fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (publicacion_id) REFERENCES publicaciones(id) ON DELETE CASCADE,
            INDEX idx_publicacion (publicacion_id),
            INDEX idx_principal (es_principal)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $db->exec($sql);
        echo "   ✓ Tabla publicaciones_fotos creada\n\n";
    } catch (PDOException $e) {
        echo "   ✗ Error: " . $e->getMessage() . "\n\n";
    }
    
    // 3. Agregar campo foto_principal
    echo "3. Agregando campo foto_principal...\n";
    try {
        $db->exec("ALTER TABLE publicaciones ADD COLUMN foto_principal VARCHAR(255) DEFAULT NULL COMMENT 'URL de la foto principal' AFTER descripcion");
        echo "   ✓ Campo foto_principal agregado\n\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "   ✓ Campo foto_principal ya existe\n\n";
        } else {
            echo "   ✗ Error: " . $e->getMessage() . "\n\n";
        }
    }
    
    echo "✓ Correcciones aplicadas exitosamente\n";
    
} catch (Exception $e) {
    echo "✗ Error fatal: " . $e->getMessage() . "\n";
    exit(1);
}
