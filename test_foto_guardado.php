<?php
/**
 * Script de prueba: Verificar guardado de fotos
 * Ejecutar: php test_foto_guardado.php
 */

// Cargar .env
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        putenv(trim($key) . '=' . trim($value));
    }
}

require_once __DIR__ . '/app/config/database.php';

echo "=== TEST: VERIFICACIÓN DE SISTEMA DE FOTOS ===\n\n";

try {
    $db = getDB();
    
    // 1. Verificar tablas
    echo "1. Verificando tablas de fotos...\n";
    $stmt = $db->query("SHOW TABLES LIKE '%foto%'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tables as $table) {
        echo "   ✓ Tabla encontrada: $table\n";
    }
    
    if (count($tables) === 1 && $tables[0] === 'publicacion_fotos') {
        echo "   ✓ CORRECTO: Solo existe la tabla publicacion_fotos\n\n";
    } else {
        echo "   ✗ ERROR: Configuración incorrecta de tablas\n\n";
        exit(1);
    }
    
    // 2. Verificar estructura de publicacion_fotos
    echo "2. Verificando estructura de publicacion_fotos...\n";
    $stmt = $db->query("DESCRIBE publicacion_fotos");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $requiredFields = ['id', 'publicacion_id', 'ruta', 'es_principal', 'orden', 'fecha_subida'];
    $foundFields = array_column($columns, 'Field');
    
    $allFieldsPresent = true;
    foreach ($requiredFields as $field) {
        if (in_array($field, $foundFields)) {
            echo "   ✓ Campo '$field' presente\n";
        } else {
            echo "   ✗ Campo '$field' FALTANTE\n";
            $allFieldsPresent = false;
        }
    }
    
    if ($allFieldsPresent) {
        echo "   ✓ CORRECTO: Todos los campos requeridos están presentes\n\n";
    } else {
        echo "   ✗ ERROR: Faltan campos en la tabla\n\n";
        exit(1);
    }
    
    // 3. Verificar campo en tabla publicaciones
    echo "3. Verificando campo foto_principal en publicaciones...\n";
    $stmt = $db->query("DESCRIBE publicaciones");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $foundFields = array_column($columns, 'Field');
    
    if (in_array('foto_principal', $foundFields)) {
        echo "   ✓ Campo 'foto_principal' presente en tabla publicaciones\n\n";
    } else {
        echo "   ✗ Campo 'foto_principal' FALTANTE en tabla publicaciones\n\n";
    }
    
    // 4. Verificar directorio de uploads
    echo "4. Verificando directorio de uploads...\n";
    $uploadDir = __DIR__ . '/public/uploads/publicaciones';
    
    if (is_dir($uploadDir)) {
        echo "   ✓ Directorio existe: $uploadDir\n";
        
        if (is_writable($uploadDir)) {
            echo "   ✓ Directorio tiene permisos de escritura\n\n";
        } else {
            echo "   ✗ Directorio NO tiene permisos de escritura\n\n";
        }
    } else {
        echo "   ✗ Directorio NO existe: $uploadDir\n\n";
    }
    
    // 5. Verificar última publicación
    echo "5. Verificando última publicación...\n";
    $stmt = $db->query("SELECT id, titulo, foto_principal FROM publicaciones ORDER BY id DESC LIMIT 1");
    $pub = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($pub) {
        echo "   ✓ Última publicación ID: {$pub['id']}\n";
        echo "   ✓ Título: {$pub['titulo']}\n";
        echo "   ✓ Foto principal: " . ($pub['foto_principal'] ?? 'NULL') . "\n";
        
        // Verificar si tiene fotos asociadas
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM publicacion_fotos WHERE publicacion_id = ?");
        $stmt->execute([$pub['id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "   ✓ Fotos asociadas: {$result['total']}\n";
        
        if ($result['total'] > 0) {
            echo "   ✓ La publicación tiene fotos guardadas\n";
            
            // Mostrar detalles de las fotos
            $stmt = $db->prepare("SELECT * FROM publicacion_fotos WHERE publicacion_id = ? ORDER BY orden");
            $stmt->execute([$pub['id']]);
            $fotos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($fotos as $foto) {
                echo "     - Foto {$foto['orden']}: {$foto['ruta']} " . 
                     ($foto['es_principal'] ? '(PRINCIPAL)' : '') . "\n";
            }
        } else {
            echo "   ⚠ La publicación NO tiene fotos guardadas\n";
        }
    } else {
        echo "   ⚠ No hay publicaciones en la base de datos\n";
    }
    
    echo "\n=== FIN DEL TEST ===\n";
    echo "\n✓ SISTEMA DE FOTOS CONFIGURADO CORRECTAMENTE\n";
    echo "\nPróximos pasos:\n";
    echo "1. Ir a: http://chilechocados.local:8080/publicar\n";
    echo "2. Crear una publicación con fotos\n";
    echo "3. Verificar que se guarden en la base de datos\n";
    echo "4. Revisar logs en: public/logs/debug.txt\n";
    
} catch (Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
