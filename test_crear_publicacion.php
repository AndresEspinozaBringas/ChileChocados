<?php
/**
 * Test automatizado de creación de publicación con fotos
 */

require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/config/database.php';
require_once __DIR__ . '/includes/helpers.php';

echo "=== TEST DE CREACIÓN DE PUBLICACIÓN CON FOTOS ===\n\n";

// Simular sesión de usuario
session_start();
$_SESSION['user_id'] = 1; // Usuario de prueba
$_SESSION['user_nombre'] = 'Test User';
$_SESSION['user_email'] = 'test@test.com';
$_SESSION['user_rol'] = 'vendedor';

// Crear imagen de prueba
$testImagePath = sys_get_temp_dir() . '/test_image_' . time() . '.jpg';
$imageData = imagecreatetruecolor(800, 600);
$bgColor = imagecolorallocate($imageData, 255, 0, 0);
imagefill($imageData, 0, 0, $bgColor);
imagejpeg($imageData, $testImagePath, 90);
imagedestroy($imageData);

echo "1. Imagen de prueba creada: $testImagePath\n";
echo "   Tamaño: " . filesize($testImagePath) . " bytes\n\n";

// Simular $_FILES
$_FILES = [
    'fotos' => [
        'name' => ['test_foto_1.jpg', '', '', '', '', ''],
        'type' => ['image/jpeg', '', '', '', '', ''],
        'tmp_name' => [$testImagePath, '', '', '', '', ''],
        'error' => [UPLOAD_ERR_OK, UPLOAD_ERR_NO_FILE, UPLOAD_ERR_NO_FILE, UPLOAD_ERR_NO_FILE, UPLOAD_ERR_NO_FILE, UPLOAD_ERR_NO_FILE],
        'size' => [filesize($testImagePath), 0, 0, 0, 0, 0]
    ]
];

// Simular $_POST
$_POST = [
    'csrf_token' => generateCsrfToken(),
    'tipificacion' => 'chocado',
    'tipo_venta' => 'completo',
    'marca' => 'Toyota',
    'modelo' => 'Corolla',
    'anio' => 2020,
    'descripcion' => 'Test de publicación con foto',
    'categoria_padre_id' => 1,
    'subcategoria_id' => 1,
    'region_id' => 1,
    'comuna_id' => 1,
    'precio' => 5000000,
    'promocion' => 'normal',
    'foto_principal' => 1
];

echo "2. Datos de prueba preparados\n\n";

// Cargar controlador
require_once APP_PATH . '/controllers/PublicacionController.php';
require_once APP_PATH . '/models/Publicacion.php';
require_once APP_PATH . '/models/Categoria.php';

use App\Controllers\PublicacionController;

echo "3. Ejecutando PublicacionController::store()...\n\n";

try {
    // Capturar la salida
    ob_start();
    
    $controller = new PublicacionController();
    
    // Usar reflexión para llamar al método store
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('store');
    $method->setAccessible(true);
    
    // Ejecutar
    $method->invoke($controller);
    
    $output = ob_get_clean();
    
    echo "4. Método ejecutado\n\n";
    
} catch (Exception $e) {
    ob_end_clean();
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n\n";
}

// Verificar resultado
echo "5. Verificando resultado en BD:\n";

try {
    $db = getDB();
    
    // Última publicación
    $stmt = $db->query("SELECT * FROM publicaciones ORDER BY id DESC LIMIT 1");
    $pub = $stmt->fetch(PDO::FETCH_OBJ);
    
    if ($pub) {
        echo "   ✓ Publicación creada:\n";
        echo "     ID: {$pub->id}\n";
        echo "     Título: {$pub->titulo}\n";
        echo "     Estado: {$pub->estado}\n";
        echo "     Foto principal: " . ($pub->foto_principal ?? 'NULL') . "\n\n";
        
        // Verificar fotos
        $stmt = $db->prepare("SELECT * FROM publicacion_fotos WHERE publicacion_id = ?");
        $stmt->execute([$pub->id]);
        $fotos = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        echo "   Fotos guardadas: " . count($fotos) . "\n";
        foreach ($fotos as $foto) {
            echo "     - Ruta: {$foto->ruta}\n";
            echo "       Principal: " . ($foto->es_principal ? 'SÍ' : 'NO') . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "   ❌ ERROR al verificar: " . $e->getMessage() . "\n";
}

echo "\n6. Verificando archivos físicos:\n";
$uploadDir = UPLOAD_PATH . '/publicaciones/' . date('Y') . '/' . date('m') . '/';
if (is_dir($uploadDir)) {
    $files = scandir($uploadDir);
    $files = array_diff($files, ['.', '..', '.DS_Store']);
    echo "   Archivos en $uploadDir:\n";
    foreach ($files as $file) {
        if (strpos($file, 'pub_') === 0) {
            $size = filesize($uploadDir . $file);
            echo "   - $file (" . number_format($size / 1024, 2) . " KB)\n";
        }
    }
}

// Limpiar
if (file_exists($testImagePath)) {
    unlink($testImagePath);
}

echo "\n=== FIN DEL TEST ===\n";
