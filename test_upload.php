<?php
/**
 * Test de subida de archivos
 */

echo "=== TEST DE SUBIDA DE ARCHIVOS ===\n\n";

// 1. Verificar configuración PHP
echo "1. Configuración PHP:\n";
echo "   upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "   post_max_size: " . ini_get('post_max_size') . "\n";
echo "   max_file_uploads: " . ini_get('max_file_uploads') . "\n";
echo "   file_uploads: " . (ini_get('file_uploads') ? 'Habilitado' : 'Deshabilitado') . "\n\n";

// 2. Verificar directorio de uploads
$uploadDir = __DIR__ . '/public/uploads/publicaciones/2025/10/';
echo "2. Directorio de uploads:\n";
echo "   Ruta: $uploadDir\n";
echo "   Existe: " . (is_dir($uploadDir) ? 'SÍ' : 'NO') . "\n";
echo "   Escribible: " . (is_writable($uploadDir) ? 'SÍ' : 'NO') . "\n";
echo "   Permisos: " . substr(sprintf('%o', fileperms($uploadDir)), -4) . "\n\n";

// 3. Test de escritura
echo "3. Test de escritura:\n";
$testFile = $uploadDir . 'test_' . time() . '.txt';
$content = "Test de escritura - " . date('Y-m-d H:i:s');

if (file_put_contents($testFile, $content)) {
    echo "   ✓ Escritura exitosa: $testFile\n";
    
    // Verificar que se creó
    if (file_exists($testFile)) {
        echo "   ✓ Archivo existe\n";
        echo "   Contenido: " . file_get_contents($testFile) . "\n";
        
        // Eliminar archivo de prueba
        unlink($testFile);
        echo "   ✓ Archivo de prueba eliminado\n";
    }
} else {
    echo "   ✗ ERROR al escribir archivo\n";
    $error = error_get_last();
    echo "   Error: " . print_r($error, true) . "\n";
}

echo "\n";

// 4. Simular move_uploaded_file
echo "4. Test de move_uploaded_file:\n";

// Crear archivo temporal simulado
$tempFile = sys_get_temp_dir() . '/test_upload_' . time() . '.txt';
file_put_contents($tempFile, "Contenido de prueba");

$destFile = $uploadDir . 'test_move_' . time() . '.txt';

echo "   Archivo temporal: $tempFile\n";
echo "   Destino: $destFile\n";
echo "   Temp existe: " . (file_exists($tempFile) ? 'SÍ' : 'NO') . "\n";

// Intentar mover (usando copy porque move_uploaded_file solo funciona con archivos subidos)
if (copy($tempFile, $destFile)) {
    echo "   ✓ Copia exitosa\n";
    
    // Limpiar
    unlink($tempFile);
    unlink($destFile);
    echo "   ✓ Archivos de prueba eliminados\n";
} else {
    echo "   ✗ ERROR al copiar archivo\n";
}

echo "\n";

// 5. Verificar usuario/grupo del proceso PHP
echo "5. Usuario del proceso PHP:\n";
if (function_exists('posix_getpwuid') && function_exists('posix_geteuid')) {
    $processUser = posix_getpwuid(posix_geteuid());
    echo "   Usuario: " . $processUser['name'] . "\n";
    echo "   UID: " . $processUser['uid'] . "\n";
    echo "   GID: " . $processUser['gid'] . "\n";
} else {
    echo "   Funciones POSIX no disponibles\n";
}

echo "\n";

// 6. Verificar propietario del directorio
echo "6. Propietario del directorio:\n";
$stat = stat($uploadDir);
if (function_exists('posix_getpwuid')) {
    $owner = posix_getpwuid($stat['uid']);
    $group = posix_getgrgid($stat['gid']);
    echo "   Propietario: " . $owner['name'] . " (UID: {$stat['uid']})\n";
    echo "   Grupo: " . $group['name'] . " (GID: {$stat['gid']})\n";
} else {
    echo "   UID: {$stat['uid']}\n";
    echo "   GID: {$stat['gid']}\n";
}

echo "\n=== FIN DEL TEST ===\n";
