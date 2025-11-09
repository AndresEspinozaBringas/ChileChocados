<?php

namespace App\Models;

use PDO;

class Notificacion extends Model
{
    protected $table = 'notificaciones';
    
    const TIPO_PUBLICACION_APROBADA = 'publicacion_aprobada';
    const TIPO_PUBLICACION_RECHAZADA = 'publicacion_rechazada';
    const TIPO_MENSAJE_NUEVO = 'mensaje_nuevo';
    const TIPO_FAVORITO = 'favorito';
    
    /**
     * Crear una nueva notificación
     */
    public function crear($usuarioId, $tipo, $titulo, $mensaje, $enlace = null, $publicacionId = null)
    {
        try {
            $sql = "INSERT INTO {$this->table} 
                    (usuario_id, tipo, titulo, mensaje, enlace, publicacion_id, leida, fecha_creacion)
                    VALUES (?, ?, ?, ?, ?, ?, 0, NOW())";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$usuarioId, $tipo, $titulo, $mensaje, $enlace, $publicacionId]);
        } catch (\PDOException $e) {
            error_log("Error al crear notificación: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener notificaciones de un usuario
     */
    public function getByUsuario($usuarioId, $limit = 10, $soloNoLeidas = false)
    {
        try {
            $sql = "SELECT * FROM {$this->table}
                    WHERE usuario_id = ?";
            
            if ($soloNoLeidas) {
                $sql .= " AND leida = 0";
            }
            
            $sql .= " ORDER BY fecha_creacion DESC
                      LIMIT {$limit}";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$usuarioId]);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            error_log("Error al obtener notificaciones: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener notificaciones nuevas desde un ID específico
     */
    public function getNotificacionesNuevas($usuarioId, $desdeId)
    {
        try {
            $sql = "SELECT * FROM {$this->table}
                    WHERE usuario_id = ?
                    AND id > ?
                    ORDER BY fecha_creacion DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$usuarioId, $desdeId]);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            error_log("Error al obtener notificaciones nuevas: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Contar notificaciones no leídas
     */
    public function contarNoLeidas($usuarioId)
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM {$this->table}
                    WHERE usuario_id = ? AND leida = 0";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$usuarioId]);
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            return $result ? $result->total : 0;
        } catch (\PDOException $e) {
            error_log("Error al contar notificaciones: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Marcar notificación como leída
     */
    public function marcarComoLeida($id)
    {
        try {
            $sql = "UPDATE {$this->table}
                    SET leida = 1, fecha_lectura = NOW()
                    WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            error_log("Error al marcar notificación como leída: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Marcar todas las notificaciones de un usuario como leídas
     */
    public function marcarTodasComoLeidas($usuarioId)
    {
        try {
            $sql = "UPDATE {$this->table}
                    SET leida = 1, fecha_lectura = NOW()
                    WHERE usuario_id = ? AND leida = 0";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$usuarioId]);
        } catch (\PDOException $e) {
            error_log("Error al marcar todas las notificaciones como leídas: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Eliminar notificación
     */
    public function eliminar($id)
    {
        try {
            $sql = "DELETE FROM {$this->table} WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            error_log("Error al eliminar notificación: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Notificar aprobación de publicación
     */
    public function notificarAprobacion($publicacionId, $usuarioId, $tituloPublicacion)
    {
        $titulo = "¡Publicación aprobada!";
        $mensaje = "Tu publicación '{$tituloPublicacion}' ha sido aprobada y ya está visible.";
        $enlace = "/publicacion/{$publicacionId}";
        
        return $this->crear($usuarioId, self::TIPO_PUBLICACION_APROBADA, $titulo, $mensaje, $enlace, $publicacionId);
    }
    
    /**
     * Notificar rechazo de publicación
     */
    public function notificarRechazo($publicacionId, $usuarioId, $tituloPublicacion, $motivo = null)
    {
        $titulo = "Publicación rechazada";
        $mensaje = "Tu publicación '{$tituloPublicacion}' ha sido rechazada.";
        if ($motivo) {
            $mensaje .= " Motivo: {$motivo}";
        }
        $enlace = "/mis-publicaciones";
        
        return $this->crear($usuarioId, self::TIPO_PUBLICACION_RECHAZADA, $titulo, $mensaje, $enlace, $publicacionId);
    }
    
    /**
     * Contar notificaciones pendientes para admin (publicaciones pendientes)
     * Esto no son notificaciones reales, sino un contador de tareas pendientes
     */
    public function contarPendientesAdmin()
    {
        try {
            $sql = "SELECT COUNT(*) as total 
                    FROM publicaciones 
                    WHERE estado = 'pendiente'";
            
            $stmt = $this->db->query($sql);
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            
            return $result ? $result->total : 0;
        } catch (\PDOException $e) {
            error_log("Error al contar publicaciones pendientes: " . $e->getMessage());
            return 0;
        }
    }
}
