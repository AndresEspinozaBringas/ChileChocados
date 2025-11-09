<?php
/**
 * SCRIPT 2 ALTERNATIVO: MODIFICAR TABLA PUBLICACIONES (Versión PHP)
 * Esta versión verifica si las columnas existen antes de agregarlas
 * Ejecutar: php PRODUCCION_2_ALTERNATIVO_modificar_publicaciones.php
 */

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'tu_base_de_datos');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_password');

try {
    $db = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    
    echo "=== MODIFICAR TABLA PUBLICACIONES ===\n\n";
    echo "✓ Conectado a la base de datos\n\n";
    
    // Función para verificar si una columna existe
    function columnExists($db, $table, $column) {
        $stmt = $db->prepare("
            SELECT COUNT(*) 
            FROM information_schema.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = ? 
            AND COLUMN_NAME = ?
        ");
        $stmt->execute([$table, $column]);
        return $stmt->fetchColumn() > 0;
    }
    
    // Función para verificar si un índice existe
    function indexExists($db, $table, $index) {
        $stmt = $db->prepare("
            SELECT COUNT(*) 
            FROM information_schema.STATISTICS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = ? 
            AND INDEX_NAME = ?
        ");
        $stmt->execute([$table, $index]);
        return $stmt->fetchColumn() > 0;
    }
    
    // Columnas a agregar
    $columns = [
        'marca_personalizada' => "TINYINT(1) DEFAULT 0 COMMENT 'Indica si la marca fue ingresada manualmente'",
        'modelo_personalizado' => "TINYINT(1) DEFAULT 0 COMMENT 'Indica si el modelo fue ingresado manualmente'",
        'marca_original' => "VARCHAR(100) NULL COMMENT 'Marca ingresada por usuario antes de aprobación'",
        'modelo_original' => "VARCHAR(100) NULL COMMENT 'Modelo ingresado por usuario antes de aprobación'",
        'marca_modelo_aprobado' => "TINYINT(1) DEFAULT 0 COMMENT 'Indica si admin aprobó marca/modelo personalizado'"
    ];
    
    echo "Agregando columnas...\n";
    foreach ($columns as $columnName => $definition) {
        if (!columnExists($db, 'publicaciones', $columnName)) {
            $sql = "ALTER TABLE publicaciones ADD COLUMN $columnName $definition";
            $db->exec($sql);
            echo "  ✓ Columna '$columnName' agregada\n";
        } else {
            echo "  ⚠ Columna '$columnName' ya existe (saltando)\n";
        }
    }
    
    // Índices a agregar
    $indexes = [
        'idx_marca_personalizada' => 'marca_personalizada',
        'idx_modelo_personalizado' => 'modelo_personalizado',
        'idx_marca_modelo_aprobado' => 'marca_modelo_aprobado'
    ];
    
    echo "\nAgregando índices...\n";
    foreach ($indexes as $indexName => $columnName) {
        if (!indexExists($db, 'publicaciones', $indexName)) {
            $sql = "ALTER TABLE publicaciones ADD INDEX $indexName ($columnName)";
            $db->exec($sql);
            echo "  ✓ Índice '$indexName' agregado\n";
        } else {
            echo "  ⚠ Índice '$indexName' ya existe (saltando)\n";
        }
    }
    
    // Verificar cambios
    echo "\n=== VERIFICACIÓN ===\n";
    $stmt = $db->query("SHOW COLUMNS FROM publicaciones LIKE '%marca%'");
    $marcaColumns = $stmt->fetchAll();
    
    $stmt = $db->query("SHOW COLUMNS FROM publicaciones LIKE '%modelo%'");
    $modeloColumns = $stmt->fetchAll();
    
    echo "\nColumnas relacionadas con 'marca':\n";
    foreach ($marcaColumns as $col) {
        echo "  - {$col['Field']} ({$col['Type']})\n";
    }
    
    echo "\nColumnas relacionadas con 'modelo':\n";
    foreach ($modeloColumns as $col) {
        echo "  - {$col['Field']} ({$col['Type']})\n";
    }
    
    echo "\n✅ Modificación completada exitosamente!\n";
    
} catch (PDOException $e) {
    echo "\n❌ Error de base de datos:\n";
    echo $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "\n❌ Error:\n";
    echo $e->getMessage() . "\n";
    exit(1);
}
