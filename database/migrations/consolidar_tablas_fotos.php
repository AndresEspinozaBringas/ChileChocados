<?php
/**
 * Migración: Consolidar tablas de fotos
 * 
 * PROBLEMA: Existen dos tablas (publicacion_fotos y publicaciones_fotos)
 * SOLUCIÓN: Eliminar publicaciones_fotos y usar solo publicacion_fotos con estructura correcta
 * 
 * Fecha: 2025-10-26
 */

// Cargar .env
$envFile = __DIR__ . '/../../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        putenv(trim($key) . '=' . trim($value));
    }
}

require_once __DIR__ . '/../../app/config/database.php';

try {
    $db = getDB();
    
    echo "=== CONSOLIDACIÓN DE TABLAS DE FOTOS ===\n\n";
    
    // 1. Verificar si hay datos en publicaciones_fotos
    echo "1. Verificando datos en publicaciones_fotos...\n";
    $stmt = $db->query("SELECT COUNT(*) as total FROM publicaciones_fotos");
    $result = $stmt->fetch(PDO::FETCH_OBJ);
    $totalPublicacionesFotos = $result->total;
    echo "   Registros encontrados: $totalPublicacionesFotos\n\n";
    
    // 2. Verificar si hay datos en publicacion_fotos
    echo "2. Verificando datos en publicacion_fotos...\n";
    $stmt = $db->query("SELECT COUNT(*) as total FROM publicacion_fotos");
    $result = $stmt->fetch(PDO::FETCH_OBJ);
    $totalPublicacionFotos = $result->total;
    echo "   Registros encontrados: $totalPublicacionFotos\n\n";
    
    // 3. Si hay datos en publicaciones_fotos, migrarlos a publicacion_fotos
    if ($totalPublicacionesFotos > 0) {
        echo "3. Migrando datos de publicaciones_fotos a publicacion_fotos...\n";
        $db->exec("
            INSERT INTO publicacion_fotos (publicacion_id, ruta, es_principal, orden, fecha_subida)
            SELECT publicacion_id, url, es_principal, orden, fecha_creacion
            FROM publicaciones_fotos
        ");
        echo "   ✓ Datos migrados exitosamente\n\n";
    } else {
        echo "3. No hay datos para migrar\n\n";
    }
    
    // 4. Eliminar tabla publicaciones_fotos
    echo "4. Eliminando tabla publicaciones_fotos...\n";
    $db->exec("DROP TABLE IF EXISTS publicaciones_fotos");
    echo "   ✓ Tabla publicaciones_fotos eliminada\n\n";
    
    // 5. Verificar estructura de publicacion_fotos
    echo "5. Verificando estructura de publicacion_fotos...\n";
    $stmt = $db->query("DESCRIBE publicacion_fotos");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $hasRuta = false;
    foreach ($columns as $column) {
        if ($column['Field'] === 'ruta') {
            $hasRuta = true;
            break;
        }
    }
    
    if ($hasRuta) {
        echo "   ✓ Tabla publicacion_fotos tiene estructura correcta\n\n";
    } else {
        echo "   ✗ ERROR: Tabla publicacion_fotos no tiene el campo 'ruta'\n\n";
    }
    
    // 6. Mostrar estructura final
    echo "6. Estructura final de publicacion_fotos:\n";
    foreach ($columns as $column) {
        echo "   - {$column['Field']} ({$column['Type']})\n";
    }
    echo "\n";
    
    echo "✓ CONSOLIDACIÓN COMPLETADA EXITOSAMENTE\n";
    echo "\nRESUMEN:\n";
    echo "- Tabla publicaciones_fotos: ELIMINADA\n";
    echo "- Tabla publicacion_fotos: ACTIVA (tabla única para fotos)\n";
    echo "- Registros migrados: $totalPublicacionesFotos\n";
    
} catch (Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
