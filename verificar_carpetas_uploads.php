<?php
/**
 * SCRIPT: Verificar y crear carpetas de uploads
 * Ejecutar desde navegador: https://tudominio.com/verificar_carpetas_uploads.php
 * O desde CLI: php verificar_carpetas_uploads.php
 */

// Solo permitir ejecución desde CLI o localhost
if (php_sapi_name() !== 'cli' && $_SERVER['REMOTE_ADDR'] !== '127.0.0.1' && $_SERVER['REMOTE_ADDR'] !== '::1') {
    // En producción, comentar esta línea después de ejecutar
    // die('Acceso denegado. Este script solo puede ejecutarse desde localhost o CLI.');
}

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Verificación de Carpetas</title>";
echo "<style>body{font-family:Arial,sans-serif;max-width:800px;margin:50px auto;padding:20px;}";
echo ".success{color:#10B981;}.error{color:#EF4444;}.warning{color:#F59E0B;}";
echo "pre{background:#f5f5f5;padding:15px;border-radius:5px;overflow-x:auto;}</style></head><body>";

echo "<h1>🔍 Verificación de Carpetas de Uploads</h1>";

$baseDir = __DIR__;
$publicDir = $baseDir . '/public';
$uploadsDir = $publicDir . '/uploads';
$avatarsDir = $uploadsDir . '/avatars';
$publicacionesDir = $uploadsDir . '/publicaciones';

$results = [];

// Función para verificar/crear carpeta
function checkAndCreateDir($path, $name) {
    $result = [
        'name' => $name,
        'path' => $path,
        'exists' => false,
        'writable' => false,
        'permissions' => '',
        'created' => false,
        'error' => null
    ];
    
    try {
        // Verificar si existe
        if (is_dir($path)) {
            $result['exists'] = true;
            $result['writable'] = is_writable($path);
            $result['permissions'] = substr(sprintf('%o', fileperms($path)), -4);
        } else {
            // Intentar crear
            if (mkdir($path, 0777, true)) {
                $result['created'] = true;
                $result['exists'] = true;
                $result['writable'] = is_writable($path);
                $result['permissions'] = substr(sprintf('%o', fileperms($path)), -4);
            } else {
                $result['error'] = "No se pudo crear la carpeta";
            }
        }
        
        // Intentar cambiar permisos si existe pero no es escribible
        if ($result['exists'] && !$result['writable']) {
            if (@chmod($path, 0777)) {
                $result['writable'] = true;
                $result['permissions'] = '0777';
            }
        }
        
    } catch (Exception $e) {
        $result['error'] = $e->getMessage();
    }
    
    return $result;
}

// Verificar carpetas
echo "<h2>📁 Verificando Estructura de Carpetas</h2>";

$folders = [
    'public' => $publicDir,
    'uploads' => $uploadsDir,
    'avatars' => $avatarsDir,
    'publicaciones' => $publicacionesDir
];

foreach ($folders as $name => $path) {
    $results[$name] = checkAndCreateDir($path, $name);
}

// Mostrar resultados
echo "<table border='1' cellpadding='10' cellspacing='0' style='width:100%;border-collapse:collapse;'>";
echo "<tr style='background:#f0f0f0;'>";
echo "<th>Carpeta</th><th>Estado</th><th>Escribible</th><th>Permisos</th><th>Ruta</th>";
echo "</tr>";

foreach ($results as $result) {
    echo "<tr>";
    echo "<td><strong>{$result['name']}</strong></td>";
    
    // Estado
    if ($result['exists']) {
        if ($result['created']) {
            echo "<td class='success'>✓ Creada</td>";
        } else {
            echo "<td class='success'>✓ Existe</td>";
        }
    } else {
        echo "<td class='error'>✗ No existe</td>";
    }
    
    // Escribible
    if ($result['writable']) {
        echo "<td class='success'>✓ Sí</td>";
    } else {
        echo "<td class='error'>✗ No</td>";
    }
    
    // Permisos
    echo "<td>{$result['permissions']}</td>";
    
    // Ruta
    echo "<td><small>{$result['path']}</small></td>";
    
    echo "</tr>";
    
    if ($result['error']) {
        echo "<tr><td colspan='5' class='error'>Error: {$result['error']}</td></tr>";
    }
}

echo "</table>";

// Crear archivo de prueba
echo "<h2>🧪 Prueba de Escritura</h2>";

$testFile = $avatarsDir . '/test_write.txt';
$canWrite = false;

if (is_dir($avatarsDir)) {
    try {
        $canWrite = @file_put_contents($testFile, 'Test: ' . date('Y-m-d H:i:s'));
        
        if ($canWrite) {
            echo "<p class='success'>✓ Se puede escribir en la carpeta avatars</p>";
            @unlink($testFile); // Eliminar archivo de prueba
        } else {
            echo "<p class='error'>✗ No se puede escribir en la carpeta avatars</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>✗ Error al intentar escribir: {$e->getMessage()}</p>";
    }
} else {
    echo "<p class='error'>✗ La carpeta avatars no existe</p>";
}

// Verificar archivos existentes
echo "<h2>📄 Archivos en Avatars</h2>";

if (is_dir($avatarsDir)) {
    $files = @scandir($avatarsDir);
    if ($files && count($files) > 2) { // Más que . y ..
        echo "<ul>";
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $avatarsDir . '/' . $file;
                $size = filesize($filePath);
                $perms = substr(sprintf('%o', fileperms($filePath)), -4);
                echo "<li><strong>$file</strong> - " . number_format($size / 1024, 2) . " KB - Permisos: $perms</li>";
            }
        }
        echo "</ul>";
    } else {
        echo "<p class='warning'>⚠ No hay archivos en la carpeta avatars</p>";
    }
} else {
    echo "<p class='error'>✗ No se puede leer la carpeta avatars</p>";
}

// Comandos para ejecutar manualmente
echo "<h2>🔧 Comandos para Ejecutar Manualmente (SSH)</h2>";
echo "<pre>";
echo "# Crear carpetas\n";
echo "mkdir -p public/uploads/avatars\n";
echo "mkdir -p public/uploads/publicaciones\n\n";

echo "# Dar permisos\n";
echo "chmod 777 public/uploads\n";
echo "chmod 777 public/uploads/avatars\n";
echo "chmod 777 public/uploads/publicaciones\n\n";

echo "# Verificar permisos\n";
echo "ls -la public/uploads/\n";
echo "</pre>";

// Información del servidor
echo "<h2>ℹ️ Información del Servidor</h2>";
echo "<pre>";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Sistema Operativo: " . PHP_OS . "\n";
echo "Usuario PHP: " . get_current_user() . "\n";
echo "Directorio actual: " . getcwd() . "\n";
echo "Directorio base: " . $baseDir . "\n";
echo "open_basedir: " . (ini_get('open_basedir') ?: 'No configurado') . "\n";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "</pre>";

// Resumen
echo "<h2>📊 Resumen</h2>";

$allOk = true;
foreach ($results as $result) {
    if (!$result['exists'] || !$result['writable']) {
        $allOk = false;
        break;
    }
}

if ($allOk && $canWrite) {
    echo "<div style='background:#D1FAE5;border:2px solid #10B981;padding:20px;border-radius:8px;'>";
    echo "<h3 class='success'>✅ TODO CORRECTO</h3>";
    echo "<p>Todas las carpetas existen y tienen permisos de escritura.</p>";
    echo "<p><strong>Puedes eliminar este archivo ahora:</strong> verificar_carpetas_uploads.php</p>";
    echo "</div>";
} else {
    echo "<div style='background:#FEE2E2;border:2px solid #EF4444;padding:20px;border-radius:8px;'>";
    echo "<h3 class='error'>❌ ACCIÓN REQUERIDA</h3>";
    echo "<p>Algunas carpetas no existen o no tienen permisos correctos.</p>";
    echo "<p><strong>Soluciones:</strong></p>";
    echo "<ol>";
    echo "<li>Ejecuta los comandos SSH mostrados arriba</li>";
    echo "<li>O contacta a tu proveedor de hosting para que configure los permisos</li>";
    echo "<li>Asegúrate de que el usuario de PHP tenga permisos de escritura</li>";
    echo "</ol>";
    echo "</div>";
}

echo "<hr>";
echo "<p><small>Script ejecutado el: " . date('Y-m-d H:i:s') . "</small></p>";
echo "<p><small><strong>IMPORTANTE:</strong> Elimina este archivo después de verificar que todo funciona correctamente.</small></p>";

echo "</body></html>";
?>
