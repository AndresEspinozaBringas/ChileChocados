<?php
/**
 * Script de debug: Verificar datos de pagos pendientes
 */

require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/config/database.php';

$db = getDB();

echo "=== DEBUG: Pagos Pendientes ===\n\n";

// Obtener pagos pendientes
$stmt = $db->prepare("
    SELECT 
        pf.id,
        pf.estado,
        pf.monto,
        pf.tipo,
        pf.flow_orden,
        pf.fecha_creacion,
        p.id as publicacion_id,
        p.titulo as publicacion_titulo,
        p.foto_principal
    FROM pagos_flow pf
    INNER JOIN publicaciones p ON pf.publicacion_id = p.id
    WHERE pf.estado IN ('pendiente', 'en_proceso')
    ORDER BY pf.fecha_creacion DESC
    LIMIT 5
");
$stmt->execute();
$pagos = $stmt->fetchAll(PDO::FETCH_OBJ);

if (empty($pagos)) {
    echo "❌ No hay pagos pendientes en la base de datos\n";
    exit;
}

echo "✅ Encontrados " . count($pagos) . " pagos pendientes\n\n";

foreach ($pagos as $pago) {
    echo "--- Pago ID: {$pago->id} ---\n";
    echo "Estado: {$pago->estado}\n";
    echo "Monto: \${$pago->monto}\n";
    echo "Tipo: {$pago->tipo}\n";
    echo "Orden: {$pago->flow_orden}\n";
    echo "Publicación ID: {$pago->publicacion_id}\n";
    echo "Publicación: {$pago->publicacion_titulo}\n";
    echo "Foto principal: " . ($pago->foto_principal ?? 'NULL') . "\n";
    
    if ($pago->foto_principal) {
        $rutaFoto = __DIR__ . '/public/uploads/publicaciones/' . $pago->foto_principal;
        if (file_exists($rutaFoto)) {
            echo "✅ Archivo existe: $rutaFoto\n";
            echo "   Tamaño: " . filesize($rutaFoto) . " bytes\n";
        } else {
            echo "❌ Archivo NO existe: $rutaFoto\n";
        }
    }
    
    echo "\n";
}

// Verificar estructura de uploads
echo "=== Verificando estructura de uploads ===\n";
$uploadsDir = __DIR__ . '/public/uploads/publicaciones';
if (is_dir($uploadsDir)) {
    echo "✅ Directorio existe: $uploadsDir\n";
    
    // Listar subdirectorios
    $subdirs = glob($uploadsDir . '/*', GLOB_ONLYDIR);
    echo "Subdirectorios encontrados: " . count($subdirs) . "\n";
    foreach ($subdirs as $dir) {
        $files = glob($dir . '/*');
        echo "  - " . basename($dir) . ": " . count($files) . " archivos\n";
    }
} else {
    echo "❌ Directorio NO existe: $uploadsDir\n";
}
