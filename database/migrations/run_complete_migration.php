<?php
/**
 * Script maestro para ejecutar la migración completa de marcas y modelos
 * Ejecutar: php run_complete_migration.php
 */

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║  MIGRACIÓN COMPLETA: SISTEMA DE MARCAS Y MODELOS          ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

// Cargar configuración
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/config/database.php';

try {
    $db = \App\Core\Database::getInstance()->getConnection();
    echo "✓ Conectado a la base de datos\n\n";
    
    // PASO 1: Crear tablas
    echo "═══ PASO 1: CREAR TABLAS ═══\n";
    
    $sql = file_get_contents(__DIR__ . '/create_marcas_modelos_tables.sql');
    
    // Dividir en statements individuales
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            return !empty($stmt) && !preg_match('/^--/', $stmt);
        }
    );
    
    foreach ($statements as $statement) {
        if (empty(trim($statement))) continue;
        
        try {
            $db->exec($statement);
            
            // Extraer nombre de tabla del statement
            if (preg_match('/CREATE TABLE.*?`?(\w+)`?/i', $statement, $matches)) {
                echo "  ✓ Tabla '{$matches[1]}' creada/verificada\n";
            } elseif (preg_match('/ALTER TABLE.*?`?(\w+)`?/i', $statement, $matches)) {
                echo "  ✓ Tabla '{$matches[1]}' alterada\n";
            } else {
                echo "  ✓ Statement ejecutado\n";
            }
        } catch (PDOException $e) {
            // Si el error es por columna/tabla ya existente, continuar
            if (strpos($e->getMessage(), 'Duplicate column') !== false || 
                strpos($e->getMessage(), 'already exists') !== false ||
                strpos($e->getMessage(), 'Duplicate key') !== false) {
                echo "  ⚠ Ya existe (saltando)\n";
            } else {
                throw $e;
            }
        }
    }
    
    echo "\n✅ Tablas creadas exitosamente\n\n";
    
    // PASO 2: Importar datos desde JSON
    echo "═══ PASO 2: IMPORTAR DATOS DESDE JSON ═══\n\n";
    
    // Ejecutar el script de importación
    require __DIR__ . '/import_marcas_modelos_from_json.php';
    
    echo "\n\n╔════════════════════════════════════════════════════════════╗\n";
    echo "║  ✅ MIGRACIÓN COMPLETADA EXITOSAMENTE                     ║\n";
    echo "╚════════════════════════════════════════════════════════════╝\n\n";
    
    echo "Cambios aplicados:\n";
    echo "  1. ✓ Tabla 'marcas' creada\n";
    echo "  2. ✓ Tabla 'modelos' creada\n";
    echo "  3. ✓ Tabla 'marcas_modelos_pendientes' creada\n";
    echo "  4. ✓ Campos agregados a 'publicaciones'\n";
    echo "  5. ✓ Datos importados desde JSON\n\n";
    
    echo "Próximos pasos:\n";
    echo "  1. Actualizar el código para usar las tablas de BD\n";
    echo "  2. Probar el autocompletado en /publicar\n";
    echo "  3. Probar el panel de admin\n\n";
    
} catch (PDOException $e) {
    echo "\n╔════════════════════════════════════════════════════════════╗\n";
    echo "║  ❌ ERROR EN LA MIGRACIÓN                                 ║\n";
    echo "╚════════════════════════════════════════════════════════════╝\n\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "Código: " . $e->getCode() . "\n\n";
    exit(1);
} catch (Exception $e) {
    echo "\n╔════════════════════════════════════════════════════════════╗\n";
    echo "║  ❌ ERROR EN LA MIGRACIÓN                                 ║\n";
    echo "╚════════════════════════════════════════════════════════════╝\n\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    exit(1);
}
