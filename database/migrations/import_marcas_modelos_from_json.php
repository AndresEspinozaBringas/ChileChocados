<?php
/**
 * Script para importar marcas y modelos desde JSON a la base de datos
 * Ejecutar: php import_marcas_modelos_from_json.php
 */

// Cargar configuración
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/config/database.php';

try {
    // Conectar a la base de datos
    $db = \App\Core\Database::getInstance()->getConnection();
    
    echo "=== IMPORTACIÓN DE MARCAS Y MODELOS ===\n\n";
    echo "Conectado a la base de datos...\n";
    
    // Leer el archivo JSON
    $jsonPath = __DIR__ . '/../../chileautos_marcas_modelos.json';
    
    if (!file_exists($jsonPath)) {
        throw new Exception("Archivo JSON no encontrado: $jsonPath");
    }
    
    echo "Leyendo archivo JSON...\n";
    $jsonContent = file_get_contents($jsonPath);
    $data = json_decode($jsonContent, true);
    
    if (!$data || !isset($data['marcas'])) {
        throw new Exception("Error al parsear JSON o estructura inválida");
    }
    
    $totalMarcas = count($data['marcas']);
    echo "Encontradas $totalMarcas marcas en el JSON\n\n";
    
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
        
        // Obtener ID de la marca (recién insertada o existente)
        $marcaId = $db->lastInsertId();
        if (!$marcaId) {
            // Si no hay lastInsertId, buscar por nombre
            $stmt = $db->prepare("SELECT id FROM marcas WHERE nombre = ?");
            $stmt->execute([$nombreMarca]);
            $marcaId = $stmt->fetchColumn();
        }
        
        $marcasImportadas++;
        echo "✓ Marca: $nombreMarca (ID: $marcaId) - $cantidadModelos modelos\n";
        
        // Insertar modelos de esta marca
        if (isset($marca['modelos']) && is_array($marca['modelos'])) {
            foreach ($marca['modelos'] as $modelo) {
                $nombreModelo = $modelo['nombre'];
                
                try {
                    $stmtModelo->execute([$marcaId, $nombreModelo]);
                    $modelosImportados++;
                } catch (PDOException $e) {
                    // Si es error de duplicado, continuar
                    if ($e->getCode() != 23000) {
                        throw $e;
                    }
                }
            }
        }
    }
    
    // Commit de la transacción
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
    
    // Mostrar algunas marcas de ejemplo
    echo "\n=== EJEMPLOS ===\n";
    $stmt = $db->query("
        SELECT m.nombre as marca, COUNT(mo.id) as modelos
        FROM marcas m
        LEFT JOIN modelos mo ON m.id = mo.marca_id
        GROUP BY m.id
        ORDER BY modelos DESC
        LIMIT 5
    ");
    
    echo "Top 5 marcas con más modelos:\n";
    while ($row = $stmt->fetch()) {
        echo "  - {$row['marca']}: {$row['modelos']} modelos\n";
    }
    
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
