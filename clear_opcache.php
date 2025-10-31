<?php
/**
 * Script para limpiar el caché de OPcache
 */

if (function_exists('opcache_reset')) {
    if (opcache_reset()) {
        echo "✓ OPcache limpiado exitosamente\n";
    } else {
        echo "✗ Error al limpiar OPcache\n";
    }
} else {
    echo "OPcache no está disponible\n";
}

// También limpiar el caché de realpath
clearstatcache(true);
echo "✓ Caché de realpath limpiado\n";
