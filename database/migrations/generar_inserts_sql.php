<?php
/**
 * Script para generar archivo SQL con INSERT de marcas y modelos
 * Ejecutar: php generar_inserts_sql.php
 * Genera: PRODUCCION_5_inserts_marcas_modelos.sql
 */

echo "=== GENERADOR DE INSERTS SQL ===\n\n";

// Leer el archivo JSON
$jsonPath = __DIR__ . '/../../chileautos_marcas_modelos.json';

if (!file_exists($jsonPath)) {
    die("❌ Error: Archivo JSON no encontrado: $jsonPath\n");
}

echo "✓ Leyendo archivo JSON...\n";
$jsonContent = file_get_contents($jsonPath);
$data = json_decode($jsonContent, true);

if (!$data || !isset($data['marcas'])) {
    die("❌ Error: JSON inválido o estructura incorrecta\n");
}

$totalMarcas = count($data['marcas']);
echo "✓ Encontradas $totalMarcas marcas\n\n";

// Iniciar archivo SQL
$sqlFile = __DIR__ . '/PRODUCCION_5_inserts_marcas_modelos.sql';
$sql = "-- ============================================\n";
$sql .= "-- SCRIPT 5: INSERT DE MARCAS Y MODELOS\n";
$sql .= "-- ============================================\n";
$sql .= "-- Generado automáticamente desde JSON\n";
$sql .= "-- Fecha: " . date('Y-m-d H:i:s') . "\n";
$sql .= "-- Total marcas: $totalMarcas\n";
$sql .= "-- ============================================\n\n";

$sql .= "-- Deshabilitar verificación de claves foráneas temporalmente\n";
$sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

$sql .= "-- Limpiar tablas (opcional - comentar si no quieres borrar datos existentes)\n";
$sql .= "-- TRUNCATE TABLE modelos;\n";
$sql .= "-- TRUNCATE TABLE marcas;\n\n";

$sql .= "-- Habilitar verificación de claves foráneas\n";
$sql .= "SET FOREIGN_KEY_CHECKS = 1;\n\n";

$marcaId = 1;
$totalModelos = 0;

echo "Generando INSERTs...\n";

foreach ($data['marcas'] as $marca) {
    $nombreMarca = addslashes($marca['nombre']);
    $cantidadModelos = $marca['cantidadModelos'] ?? count($marca['modelos'] ?? []);
    
    // INSERT de marca
    $sql .= "-- Marca: {$marca['nombre']} ($cantidadModelos modelos)\n";
    $sql .= "INSERT INTO marcas (id, nombre, cantidad_modelos, activa, fecha_creacion) VALUES\n";
    $sql .= "($marcaId, '$nombreMarca', $cantidadModelos, 1, NOW())\n";
    $sql .= "ON DUPLICATE KEY UPDATE \n";
    $sql .= "    cantidad_modelos = VALUES(cantidad_modelos),\n";
    $sql .= "    fecha_actualizacion = NOW();\n\n";
    
    // INSERT de modelos
    if (isset($marca['modelos']) && is_array($marca['modelos']) && count($marca['modelos']) > 0) {
        $sql .= "-- Modelos de {$marca['nombre']}\n";
        $sql .= "INSERT INTO modelos (marca_id, nombre, activo, fecha_creacion) VALUES\n";
        
        $modelosInserts = [];
        foreach ($marca['modelos'] as $modelo) {
            $nombreModelo = addslashes($modelo['nombre']);
            $modelosInserts[] = "($marcaId, '$nombreModelo', 1, NOW())";
            $totalModelos++;
        }
        
        $sql .= implode(",\n", $modelosInserts);
        $sql .= "\nON DUPLICATE KEY UPDATE \n";
        $sql .= "    activo = VALUES(activo),\n";
        $sql .= "    fecha_actualizacion = NOW();\n\n";
    }
    
    $marcaId++;
    
    if ($marcaId % 10 == 0) {
        echo "  Procesadas $marcaId marcas...\n";
    }
}

$sql .= "-- ============================================\n";
$sql .= "-- VERIFICACIÓN\n";
$sql .= "-- ============================================\n\n";
$sql .= "SELECT 'Importación completada' AS resultado;\n";
$sql .= "SELECT COUNT(*) AS total_marcas FROM marcas;\n";
$sql .= "SELECT COUNT(*) AS total_modelos FROM modelos;\n\n";

$sql .= "-- Top 10 marcas con más modelos\n";
$sql .= "SELECT m.nombre as marca, COUNT(mo.id) as total_modelos\n";
$sql .= "FROM marcas m\n";
$sql .= "LEFT JOIN modelos mo ON m.id = mo.marca_id\n";
$sql .= "GROUP BY m.id\n";
$sql .= "ORDER BY total_modelos DESC\n";
$sql .= "LIMIT 10;\n";

// Guardar archivo
file_put_contents($sqlFile, $sql);

echo "\n=== GENERACIÓN COMPLETADA ===\n";
echo "✅ Archivo generado: PRODUCCION_5_inserts_marcas_modelos.sql\n";
echo "✅ Total marcas: " . ($marcaId - 1) . "\n";
echo "✅ Total modelos: $totalModelos\n";
echo "✅ Tamaño archivo: " . number_format(filesize($sqlFile) / 1024, 2) . " KB\n\n";

echo "Para ejecutar el archivo SQL:\n";
echo "  mysql -u usuario -p base_datos < PRODUCCION_5_inserts_marcas_modelos.sql\n";
echo "\nO desde phpMyAdmin:\n";
echo "  1. Abre phpMyAdmin\n";
echo "  2. Selecciona tu base de datos\n";
echo "  3. Ve a la pestaña 'Importar'\n";
echo "  4. Selecciona el archivo PRODUCCION_5_inserts_marcas_modelos.sql\n";
echo "  5. Ejecuta\n";
