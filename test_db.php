<?php
/**
 * Script de testing para verificar almacenamiento de imágenes
 */

require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/config/database.php';

echo "=== TEST DE ALMACENAMIENTO DE IMÁGENES ===\n\n";

try {
    $db = getDB();
    
    // 1. Verificar tabla publicacion_fotos
    echo "1. Verificando tabla publicacion_fotos:\n";
    $stmt = $db->query("SELECT COUNT(*) as total FROM publicacion_fotos");
    $result = $stmt->fetch(PDO::FETCH_OBJ);
    echo "   Total de registros: " . $result->total . "\n\n";
    
    // 2. Últimas 5 fotos
    echo "2. Últimas 5 fotos guardadas:\n";
    $stmt = $db->query("SELECT * FROM publicacion_fotos ORDER BY id DESC LIMIT 5");
    $fotos = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    if (empty($fotos)) {
        echo "   ❌ NO HAY FOTOS EN LA BASE DE DATOS\n\n";
    } else {
        foreach ($fotos as $foto) {
            echo "   - ID: {$foto->id}\n";
            echo "     Publicación: {$foto->publicacion_id}\n";
            echo "     Ruta: {$foto->ruta}\n";
            echo "     Orden: {$foto->orden}\n";
            echo "     Principal: " . ($foto->es_principal ? 'SÍ' : 'NO') . "\n";
            echo "     Fecha: {$foto->fecha_subida}\n\n";
        }
    }
    
    // 3. Verificar publicaciones con foto_principal
    echo "3. Publicaciones con foto_principal:\n";
    $stmt = $db->query("SELECT id, titulo, foto_principal FROM publicaciones WHERE foto_principal IS NOT NULL ORDER BY id DESC LIMIT 5");
    $pubs = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    if (empty($pubs)) {
        echo "   ❌ NO HAY PUBLICACIONES CON FOTO_PRINCIPAL\n\n";
    } else {
        foreach ($pubs as $pub) {
            echo "   - ID: {$pub->id}\n";
            echo "     Título: {$pub->titulo}\n";
            echo "     Foto principal: {$pub->foto_principal}\n\n";
        }
    }
    
    // 4. Verificar última publicación creada
    echo "4. Última publicación creada:\n";
    $stmt = $db->query("SELECT * FROM publicaciones ORDER BY id DESC LIMIT 1");
    $pub = $stmt->fetch(PDO::FETCH_OBJ);
    
    if ($pub) {
        echo "   ID: {$pub->id}\n";
        echo "   Título: {$pub->titulo}\n";
        echo "   Estado: {$pub->estado}\n";
        echo "   Foto principal: " . ($pub->foto_principal ?? 'NULL') . "\n";
        echo "   Es destacada: " . ($pub->es_destacada ?? 0) . "\n";
        echo "   Fecha creación: {$pub->fecha_creacion}\n\n";
        
        // Verificar si tiene fotos asociadas
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM publicacion_fotos WHERE publicacion_id = ?");
        $stmt->execute([$pub->id]);
        $count = $stmt->fetch(PDO::FETCH_OBJ);
        echo "   Fotos asociadas: {$count->total}\n\n";
    }
    
    // 5. Verificar archivos físicos
    echo "5. Verificando archivos físicos:\n";
    $uploadDir = __DIR__ . '/public/uploads/publicaciones/2025/10/';
    if (is_dir($uploadDir)) {
        $files = scandir($uploadDir);
        $files = array_diff($files, ['.', '..', '.DS_Store']);
        echo "   Archivos en {$uploadDir}:\n";
        foreach ($files as $file) {
            $size = filesize($uploadDir . $file);
            echo "   - {$file} (" . number_format($size / 1024, 2) . " KB)\n";
        }
    } else {
        echo "   ❌ Directorio no existe: {$uploadDir}\n";
    }
    
    echo "\n=== FIN DEL TEST ===\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
