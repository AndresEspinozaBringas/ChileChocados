<?php
/**
 * Script para limpiar el caché de OPcache desde web
 * Acceder desde: http://localhost/clear_cache.php
 */

header('Content-Type: text/plain; charset=utf-8');

echo "=== LIMPIEZA DE CACHÉ ===\n\n";

// Limpiar OPcache
if (function_exists('opcache_reset')) {
    if (opcache_reset()) {
        echo "✓ OPcache limpiado exitosamente\n";
    } else {
        echo "✗ Error al limpiar OPcache\n";
    }
    
    // Mostrar estado de OPcache
    if (function_exists('opcache_get_status')) {
        $status = opcache_get_status();
        echo "\nEstado de OPcache:\n";
        echo "  - Habilitado: " . ($status['opcache_enabled'] ? 'SÍ' : 'NO') . "\n";
        echo "  - Scripts en caché: " . $status['opcache_statistics']['num_cached_scripts'] . "\n";
        echo "  - Memoria usada: " . number_format($status['memory_usage']['used_memory'] / 1024 / 1024, 2) . " MB\n";
    }
} else {
    echo "OPcache no está disponible\n";
}

// Limpiar caché de realpath
clearstatcache(true);
echo "\n✓ Caché de realpath limpiado\n";

echo "\n=== CACHÉ LIMPIADO ===\n";
