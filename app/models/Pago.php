<?php
/**
 * Modelo Pago
 * Gestiona pagos Flow (destacados y banners)
 */

namespace App\Models;

use PDO;

class Pago extends Model
{
    protected $table = 'pagos_flow';
    
    protected $fillable = [
        'publicacion_id',
        'usuario_id',
        'tipo',
        'monto',
        'flow_token',
        'flow_orden',
        'estado',
        'respuesta_flow',
        'fecha_pago'
    ];
    
    /**
     * Tipos de pago
     */
    const TIPO_DESTACADO_15 = 'destacado_15';
    const TIPO_DESTACADO_30 = 'destacado_30';
    const TIPO_BANNER = 'banner';
    
    /**
     * Estados de pago
     */
    const ESTADO_PENDIENTE = 'pendiente';
    const ESTADO_APROBADO = 'aprobado';
    const ESTADO_RECHAZADO = 'rechazado';
    const ESTADO_EXPIRADO = 'expirado';
    
    /**
     * Crear orden de pago
     */
    public function crearOrden($publicacionId, $usuarioId, $tipo, $monto)
    {
        $flowOrden = 'ORD-' . time() . '-' . rand(1000, 9999);
        
        return $this->create([
            'publicacion_id' => $publicacionId,
            'usuario_id' => $usuarioId,
            'tipo' => $tipo,
            'monto' => $monto,
            'flow_orden' => $flowOrden,
            'estado' => self::ESTADO_PENDIENTE
        ]);
    }
    
    /**
     * Buscar por orden Flow
     */
    public function findByFlowOrden($flowOrden)
    {
        return $this->first('flow_orden', '=', $flowOrden);
    }
    
    /**
     * Aprobar pago
     */
    public function aprobar($id, $flowToken, $respuestaFlow = null)
    {
        $data = [
            'estado' => self::ESTADO_APROBADO,
            'flow_token' => $flowToken,
            'fecha_pago' => date('Y-m-d H:i:s')
        ];
        
        if ($respuestaFlow) {
            $data['respuesta_flow'] = json_encode($respuestaFlow);
        }
        
        return $this->update($id, $data);
    }
    
    /**
     * Rechazar pago
     */
    public function rechazar($id, $respuestaFlow = null)
    {
        $data = [
            'estado' => self::ESTADO_RECHAZADO
        ];
        
        if ($respuestaFlow) {
            $data['respuesta_flow'] = json_encode($respuestaFlow);
        }
        
        return $this->update($id, $data);
    }
    
    /**
     * Obtener pagos del usuario
     */
    public function getByUsuario($usuarioId, $estado = null)
    {
        $sql = "SELECT 
                    pf.*,
                    p.titulo as publicacion_titulo
                FROM {$this->table} pf
                INNER JOIN publicaciones p ON pf.publicacion_id = p.id
                WHERE pf.usuario_id = ?";
        
        $params = [$usuarioId];
        
        if ($estado) {
            $sql .= " AND pf.estado = ?";
            $params[] = $estado;
        }
        
        $sql .= " ORDER BY pf.fecha_creacion DESC";
        
        return $this->query($sql, $params);
    }
    
    /**
     * Obtener pagos de una publicación
     */
    public function getByPublicacion($publicacionId)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE publicacion_id = ? 
                ORDER BY fecha_creacion DESC";
        
        return $this->query($sql, [$publicacionId]);
    }
    
    /**
     * Obtener estadísticas de pagos
     */
    public function getEstadisticas($periodo = null)
    {
        $sql = "SELECT 
                    COUNT(*) as total_ordenes,
                    SUM(CASE WHEN estado = 'aprobado' THEN 1 ELSE 0 END) as aprobados,
                    SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN estado = 'rechazado' THEN 1 ELSE 0 END) as rechazados,
                    SUM(CASE WHEN estado = 'aprobado' THEN monto ELSE 0 END) as ingresos_totales,
                    AVG(CASE WHEN estado = 'aprobado' THEN monto ELSE NULL END) as ticket_promedio,
                    COUNT(DISTINCT usuario_id) as usuarios_pagadores
                FROM {$this->table}";
        
        $params = [];
        
        if ($periodo) {
            $sql .= " WHERE fecha_creacion >= ?";
            $params[] = $periodo;
        }
        
        $result = $this->query($sql, $params);
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Obtener ingresos por tipo de pago
     */
    public function getIngresosPorTipo($periodo = null)
    {
        $sql = "SELECT 
                    tipo,
                    COUNT(*) as cantidad,
                    SUM(monto) as ingresos_totales
                FROM {$this->table}
                WHERE estado = 'aprobado'";
        
        $params = [];
        
        if ($periodo) {
            $sql .= " AND fecha_creacion >= ?";
            $params[] = $periodo;
        }
        
        $sql .= " GROUP BY tipo";
        
        return $this->query($sql, $params);
    }
    
    /**
     * Obtener últimos pagos aprobados
     */
    public function getUltimosAprobados($limit = 10)
    {
        $sql = "SELECT 
                    pf.*,
                    u.nombre as usuario_nombre,
                    u.email as usuario_email,
                    p.titulo as publicacion_titulo
                FROM {$this->table} pf
                INNER JOIN usuarios u ON pf.usuario_id = u.id
                INNER JOIN publicaciones p ON pf.publicacion_id = p.id
                WHERE pf.estado = ?
                ORDER BY pf.fecha_pago DESC
                LIMIT {$limit}";
        
        return $this->query($sql, [self::ESTADO_APROBADO]);
    }
    
    /**
     * Obtener montos según configuración
     */
    public function getMontos()
    {
        $sql = "SELECT clave, valor FROM configuraciones 
                WHERE clave IN ('precio_destacado_15_dias', 'precio_destacado_30_dias')";
        
        $result = $this->query($sql);
        $montos = [];
        
        foreach ($result as $config) {
            $montos[$config->clave] = (float) $config->valor;
        }
        
        return $montos;
    }
    
    /**
     * Verificar si publicación tiene pago aprobado
     */
    public function tienePagoAprobado($publicacionId, $tipo = null)
    {
        $sql = "SELECT COUNT(*) as total 
                FROM {$this->table} 
                WHERE publicacion_id = ? 
                AND estado = ?";
        
        $params = [$publicacionId, self::ESTADO_APROBADO];
        
        if ($tipo) {
            $sql .= " AND tipo = ?";
            $params[] = $tipo;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        
        return $result && $result->total > 0;
    }
    
    /**
     * Marcar pagos pendientes como expirados (después de 24h)
     */
    public function marcarExpirados()
    {
        $sql = "UPDATE {$this->table} 
                SET estado = ?
                WHERE estado = ?
                AND fecha_creacion < DATE_SUB(NOW(), INTERVAL 24 HOUR)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([self::ESTADO_EXPIRADO, self::ESTADO_PENDIENTE]);
    }
    
    /**
     * Reporte de ventas por periodo
     */
    public function getReporteVentas($fechaInicio, $fechaFin)
    {
        $sql = "SELECT 
                    DATE(fecha_pago) as fecha,
                    COUNT(*) as total_ventas,
                    SUM(monto) as ingresos,
                    tipo
                FROM {$this->table}
                WHERE estado = ?
                AND fecha_pago BETWEEN ? AND ?
                GROUP BY DATE(fecha_pago), tipo
                ORDER BY fecha DESC";
        
        return $this->query($sql, [self::ESTADO_APROBADO, $fechaInicio, $fechaFin]);
    }
}
