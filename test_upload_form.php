<?php
/**
 * Formulario de prueba para subir archivos
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['test_file'])) {
    echo "<h2>Resultado de la subida:</h2>";
    echo "<pre>";
    
    $file = $_FILES['test_file'];
    echo "Información del archivo:\n";
    print_r($file);
    
    $uploadDir = __DIR__ . '/public/uploads/publicaciones/2025/10/';
    $fileName = 'test_real_upload_' . time() . '_' . basename($file['name']);
    $targetPath = $uploadDir . $fileName;
    
    echo "\nIntentando mover a: $targetPath\n";
    echo "Archivo temporal existe: " . (file_exists($file['tmp_name']) ? 'SÍ' : 'NO') . "\n";
    echo "Directorio destino existe: " . (is_dir($uploadDir) ? 'SÍ' : 'NO') . "\n";
    echo "Directorio escribible: " . (is_writable($uploadDir) ? 'SÍ' : 'NO') . "\n\n";
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        echo "✓ ÉXITO: Archivo movido correctamente\n";
        echo "Archivo existe: " . (file_exists($targetPath) ? 'SÍ' : 'NO') . "\n";
        echo "Tamaño: " . filesize($targetPath) . " bytes\n";
    } else {
        echo "✗ ERROR: No se pudo mover el archivo\n";
        $error = error_get_last();
        echo "Error PHP: " . print_r($error, true) . "\n";
    }
    
    echo "</pre>";
    echo "<hr>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Test de Subida de Archivos</title>
</head>
<body>
    <h1>Test de Subida de Archivos</h1>
    <form method="POST" enctype="multipart/form-data">
        <p>
            <label>Selecciona una imagen:</label><br>
            <input type="file" name="test_file" accept="image/*" required>
        </p>
        <p>
            <button type="submit">Subir Archivo</button>
        </p>
    </form>
    
    <hr>
    <h2>Archivos en el directorio:</h2>
    <pre>
<?php
$uploadDir = __DIR__ . '/public/uploads/publicaciones/2025/10/';
if (is_dir($uploadDir)) {
    $files = scandir($uploadDir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && $file !== '.DS_Store') {
            $size = filesize($uploadDir . $file);
            echo "$file (" . number_format($size / 1024, 2) . " KB)\n";
        }
    }
}
?>
    </pre>
</body>
</html>
