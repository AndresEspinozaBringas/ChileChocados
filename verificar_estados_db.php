<?php
/**
 * Script de verificación: Estados de publicaciones en la base de datos
 */

define('APP_PATH', __DIR__ . '/app');
require_once APP_PATH . '/config/database.php';

try {
    $db = getDB();
    
    echo "===========================================\n";
    echo "VERIFICACIÓN DE ESTADOS EN BASE DE DATOS\n";
    echo "===========================================\n\n";
    
    // Consulta 1: Contar por estado
    echo "1. CONTEO POR ESTADO:\n";
    echo str_repeat('-', 40) . "\n";
    $stmt = $db->query("
        SELECT estado, COUNT(*) as total 
        FROM publicaciones 
        GROUP BY estado 
        ORDER BY estado
    ");
    
    $totales = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $totales[$row['estado']] = $row['total'];
        printf("%-15s: %d\n", ucfirst($row['estado']), $row['total']);
    }
    
    echo "\n";
    
    // Consulta 2: Lo que devuelve el controlador
    echo "2. CONSULTA DEL CONTROLADOR:\n";
    echo str_repeat('-', 40) . "\n";
    $stmt = $db->query("
        SELECT 
            SUM(CASE WHEN estado = 'aprobada' THEN 1 ELSE 0 END) as aprobadas,
            SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
            SUM(CASE WHEN estado = 'rechazada' THEN 1 ELSE 0 END) as rechazadas,
            SUM(CASE WHEN estado = 'borrador' THEN 1 ELSE 0 END) as borradores,
            SUM(CASE WHEN estado = 'vendida' THEN 1 ELSE 0 END) as vendidas,
            SUM(CASE WHEN estado = 'archivada' THEN 1 ELSE 0 END) as archivadas
        FROM publicaciones
    ");
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    foreach ($result as $estado => $total) {
        printf("%-15s: %d\n", ucfirst($estado), $total);
    }
    
    echo "\n";
    
    // Consulta 3: Total general
    $stmt = $db->query("SELECT COUNT(*) as total FROM publicaciones");
    $totalGeneral = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    echo "3. TOTALES:\n";
    echo str_repeat('-', 40) . "\n";
    echo "Total en BD: $totalGeneral\n";
    echo "Suma estados: " . array_sum($result) . "\n";
    
    echo "\n";
    
    // Verificar consistencia
    if ($totalGeneral == array_sum($result)) {
        echo "✓ Los datos son consistentes\n";
    } else {
        echo "✗ INCONSISTENCIA DETECTADA\n";
        echo "  Diferencia: " . ($totalGeneral - array_sum($result)) . "\n";
    }
    
    echo "\n===========================================\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
