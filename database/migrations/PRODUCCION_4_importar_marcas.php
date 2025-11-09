<?php
/**
 * SCRIPT 4: IMPORTAR MARCAS Y MODELOS DESDE JSON
 * Ejecutar en servidor: php PRODUCCION_4_importar_marcas.php
 * Fecha: 2025-11-08
 */

// Configuración de la base de datos
// IMPORTANTE: Ajustar estos valores según tu servidor de producción
define('DB_HOST', 'localhost');
define('DB_NAME', 'tu_base_de_datos');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_password');

try {
    // Conectar a la base de datos
    $db = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    
    echo "=== IMPORTACIÓN DE MARCAS Y MODELOS ===\n\n";
    echo "✓ Conectado a la base de datos\n";
    
    // Leer el archivo JSON
    $jsonPath = __DIR__ . '/../../chileautos_marcas_modelos.json';
    
    if (!file_exists($jsonPath)) {
        throw new Exception("Archivo JSON no encontrado: $jsonPath");
    }
    
    echo "✓ Leyendo archivo JSON...\n";
    $jsonContent = file_get_contents($jsonPath);
    $data = json_decode($jsonContent, true);
    
    if (!$data || !isset($data['marcas'])) {
        throw new Exception("Error al parsear JSON o estructura inválida");
    }
    
    $totalMarcas = count($data['marcas']);
    echo "✓ Encontradas $totalMarcas marcas en el JSON\n\n";
    
    // Iniciar transacción
    $db->beginTransaction();
    
    // Preparar statements
    $stmtMarca = $db->prepare("
        INSERT INTO marcas (nombre, cantidad_modelos) 
        VALUES (?, ?)
        ON DUPLICATE KEY UPDATE 
            cantidad_modelos = VALUES(cantidad_modelos),
            fecha_actualizacion = CURRENT_TIMESTAMP
    ");
    
    $stmtModelo = $db->prepare("
        INSERT INTO modelos (marca_id, nombre) 
        VALUES (?, ?)
        ON DUPLICATE KEY UPDATE 
            fecha_actualizacion = CURRENT_TIMESTAMP
    ");
    
    $marcasImportadas = 0;
    $modelosImportados = 0;
    
    // Importar cada marca y sus modelos
    foreach ($data['marcas'] as $marca) {
        $nombreMarca = $marca['nombre'];
        $cantidadModelos = $marca['cantidadModelos'] ?? count($marca['modelos'] ?? []);
        
        // Insertar marca
        $stmtMarca->execute([$nombreMarca, $cantidadModelos]);
        
        // Obtener ID de la marca
        $marcaId = $db->lastInsertId();
        if (!$marcaId) {
            $stmt = $db->prepare("SELECT id FROM marcas WHERE nombre = ?");
            $stmt->execute([$nombreMarca]);
            $marcaId = $stmt->fetchColumn();
        }
        
        $marcasImportadas++;
        echo "✓ Marca: $nombreMarca (ID: $marcaId) - $cantidadModelos modelos\n";
        
        // Insertar modelos
        if (isset($marca['modelos']) && is_array($marca['modelos'])) {
            foreach ($marca['modelos'] as $modelo) {
                $nombreModelo = $modelo['nombre'];
                
                try {
                    $stmtModelo->execute([$marcaId, $nombreModelo]);
                    $modelosImportados++;
                } catch (PDOException $e) {
                    if ($e->getCode() != 23000) { // Ignorar duplicados
                        throw $e;
                    }
                }
            }
        }
    }
    
    // Commit
    $db->commit();
    
    echo "\n=== IMPORTACIÓN COMPLETADA ===\n";
    echo "✅ Marcas importadas: $marcasImportadas\n";
    echo "✅ Modelos importados: $modelosImportados\n";
    
    // Verificar datos
    $stmt = $db->query("SELECT COUNT(*) FROM marcas");
    $totalMarcasDB = $stmt->fetchColumn();
    
    $stmt = $db->query("SELECT COUNT(*) FROM modelos");
    $totalModelosDB = $stmt->fetchColumn();
    
    echo "\n=== VERIFICACIÓN ===\n";
    echo "Total marcas en BD: $totalMarcasDB\n";
    echo "Total modelos en BD: $totalModelosDB\n";
    
    // Mostrar ejemplos
    echo "\n=== TOP 5 MARCAS ===\n";
    $stmt = $db->query("
        SELECT m.nombre as marca, COUNT(mo.id) as modelos
        FROM marcas m
        LEFT JOIN modelos mo ON m.id = mo.marca_id
        GROUP BY m.id
        ORDER BY modelos DESC
        LIMIT 5
    ");
    
    while ($row = $stmt->fetch()) {
        echo "  - {$row['marca']}: {$row['modelos']} modelos\n";
    }
    
    echo "\n✅ Proceso completado exitosamente!\n";
    
} catch (PDOException $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    echo "\n❌ Error de base de datos:\n";
    echo $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    echo "\n❌ Error:\n";
    echo $e->getMessage() . "\n";
    exit(1);
}
