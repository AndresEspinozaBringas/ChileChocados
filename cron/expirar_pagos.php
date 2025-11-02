#!/usr/bin/env php
<?php
/**
 * Script Cron: Expirar Pagos Pendientes
 * 
 * Este script debe ejecutarse cada hora para marcar como expirados
 * los pagos que llevan más de 48 horas sin completarse.
 * 
 * Configuración en crontab (cada hora):
 * 0 ASTERISK ASTERISK ASTERISK ASTERISK php /path/to/chilechocados/cron/expirar_pagos.php
 * 
 * O cada 6 horas:
 * 0 ASTERISK/6 ASTERISK ASTERISK ASTERISK php /path/to/chilechocados/cron/expirar_pagos.php
 * 
 * Nota: Reemplazar ASTERISK con el símbolo * en crontab
 */

// Cargar configuración
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';

// Función para log
function cronLog($message, $type = 'INFO') {
    $timestamp = date('Y-m-d H:i:s');
    echo "[$timestamp] [$type] $message\n";
}

cronLog("=== INICIO: Expiración de Pagos Pendientes ===");

try {
    $db = getDB();
    
    // 1. Obtener pagos pendientes o en proceso que han expirado
    $stmt = $db->query("
        SELECT 
            id,
            publicacion_id,
            usuario_id,
            flow_orden,
            monto,
            fecha_creacion,
            COALESCE(
                fecha_expiracion,
                DATE_ADD(fecha_creacion, INTERVAL 48 HOUR)
            ) as fecha_expiracion_calculada
        FROM pagos_flow
        WHERE estado IN ('pendiente', 'en_proceso')
        AND COALESCE(
            fecha_expiracion,
            DATE_ADD(fecha_creacion, INTERVAL 48 HOUR)
        ) < NOW()
    ");
    
    $pagosExpirados = $stmt->fetchAll(PDO::FETCH_OBJ);
    $totalExpirados = count($pagosExpirados);
    
    cronLog("Pagos encontrados para expirar: $totalExpirados");
    
    if ($totalExpirados === 0) {
        cronLog("No hay pagos para expirar");
        cronLog("=== FIN: Expiración de Pagos Pendientes ===");
        exit(0);
    }
    
    // 2. Marcar como expirados
    $stmt = $db->prepare("
        UPDATE pagos_flow 
        SET estado = 'expirado',
            notas = CONCAT(
                COALESCE(notas, ''), 
                '\nExpirado automáticamente: ', 
                NOW()
            )
        WHERE id = ?
    ");
    
    $expiradosExitosos = 0;
    $errores = 0;
    
    foreach ($pagosExpirados as $pago) {
        try {
            $stmt->execute([$pago->id]);
            $expiradosExitosos++;
            
            cronLog(
                "Pago #{$pago->id} expirado - Orden: {$pago->flow_orden} - " .
                "Publicación: {$pago->publicacion_id} - " .
                "Monto: \${$pago->monto}"
            );
            
        } catch (Exception $e) {
            $errores++;
            cronLog(
                "Error al expirar pago #{$pago->id}: " . $e->getMessage(),
                'ERROR'
            );
        }
    }
    
    // 3. Resumen
    cronLog("=== RESUMEN ===");
    cronLog("Total procesados: $totalExpirados");
    cronLog("Expirados exitosamente: $expiradosExitosos");
    cronLog("Errores: $errores");
    
    // 4. Opcional: Enviar notificaciones por email a los usuarios
    // TODO: Implementar envío de emails
    
    // 5. Estadísticas adicionales
    $stmt = $db->query("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
            SUM(CASE WHEN estado = 'en_proceso' THEN 1 ELSE 0 END) as en_proceso,
            SUM(CASE WHEN estado = 'expirado' THEN 1 ELSE 0 END) as expirados,
            SUM(CASE WHEN estado = 'aprobado' THEN 1 ELSE 0 END) as aprobados
        FROM pagos_flow
    ");
    $stats = $stmt->fetch(PDO::FETCH_OBJ);
    
    cronLog("=== ESTADÍSTICAS GENERALES ===");
    cronLog("Total pagos: {$stats->total}");
    cronLog("Pendientes: {$stats->pendientes}");
    cronLog("En proceso: {$stats->en_proceso}");
    cronLog("Expirados: {$stats->expirados}");
    cronLog("Aprobados: {$stats->aprobados}");
    
    cronLog("=== FIN: Expiración de Pagos Pendientes ===");
    
    exit(0);
    
} catch (Exception $e) {
    cronLog("ERROR CRÍTICO: " . $e->getMessage(), 'ERROR');
    cronLog("Stack trace: " . $e->getTraceAsString(), 'ERROR');
    exit(1);
}
