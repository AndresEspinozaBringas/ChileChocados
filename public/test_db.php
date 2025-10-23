<?php
// Cargar configuración .env
$env = parse_ini_file('../.env');

$host = $env['DB_HOST'];
$port = $env['DB_PORT'];
$dbname = $env['DB_NAME'];
$user = $env['DB_USER'];
$pass = $env['DB_PASS'];

try {
    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
    
    echo "<h1>✅ Conexión exitosa a MySQL DreamHost</h1>";
    echo "<p><strong>Host:</strong> {$host}</p>";
    echo "<p><strong>Base de datos:</strong> {$dbname}</p>";
    echo "<p><strong>Usuario:</strong> {$user}</p>";
    
    // Probar consulta
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h2>Tablas en la base de datos:</h2>";
    if (count($tables) > 0) {
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>{$table}</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No hay tablas creadas aún.</p>";
    }
    
} catch (PDOException $e) {
    echo "<h1>❌ Error de conexión</h1>";
    echo "<p><strong>Mensaje:</strong> " . $e->getMessage() . "</p>";
}
?>
