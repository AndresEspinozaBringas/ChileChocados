<?php
echo "INDEX.PHP FUNCIONA!<br>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Script Filename: " . $_SERVER['SCRIPT_FILENAME'] . "<br>";
echo "Request URI: " . $_SERVER['REQUEST_URI'] . "<br>";

// Probar carga de config
echo "<br><strong>Probando carga de config.php...</strong><br>";
try {
    require_once __DIR__ . '/../app/config/config.php';
    echo "✓ config.php cargado<br>";
    echo "DB_HOST: " . DB_HOST . "<br>";
    echo "APP_URL: " . APP_URL . "<br>";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
}
