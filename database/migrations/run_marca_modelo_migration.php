<?php
/**
 * Script para ejecutar la migración de marcas y modelos personalizados
 * Ejecutar desde línea de comandos: php run_marca_modelo_migration.php
 */

// Cargar configuración
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/config/database.php';

try {
    // Conectar a la base de datos usando la clase Database
    $db = \App\Core\Database::getInstance()->getConnection();

    echo "Conectado a la base de datos...\n";

    // Leer el archivo SQL
    $sql = file_get_contents(__DIR__ . '/add_marca_modelo_personalizado.sql');

    // Dividir en statements individuales
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            return !empty($stmt) && !preg_match('/^--/', $stmt);
        }
    );

    echo "Ejecutando migración...\n\n";

    foreach ($statements as $statement) {
        if (empty(trim($statement))) continue;
        
        try {
            $db->exec($statement);
            echo "✓ Statement ejecutado exitosamente\n";
        } catch (PDOException $e) {
            // Si el error es por columna/tabla ya existente, continuar
            if (strpos($e->getMessage(), 'Duplicate column') !== false || 
                strpos($e->getMessage(), 'already exists') !== false) {
                echo "⚠ Ya existe (saltando): " . substr($statement, 0, 50) . "...\n";
            } else {
                throw $e;
            }
        }
    }

    echo "\n✅ Migración completada exitosamente!\n";
    echo "\nCambios aplicados:\n";
    echo "- Agregados campos a tabla 'publicaciones':\n";
    echo "  * marca_personalizada\n";
    echo "  * modelo_personalizado\n";
    echo "  * marca_original\n";
    echo "  * modelo_original\n";
    echo "  * marca_modelo_aprobado\n";
    echo "- Creada tabla 'marcas_modelos_pendientes'\n";

} catch (PDOException $e) {
    echo "\n❌ Error en la migración:\n";
    echo $e->getMessage() . "\n";
    exit(1);
}
