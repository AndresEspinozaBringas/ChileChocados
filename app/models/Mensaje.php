<?php
/**
 * Modelo Mensaje
 * Gestiona la mensajería interna entre usuarios
 */

namespace App\Models;

use PDO;

class Mensaje extends Model
{
    protected $table = 'mensajes';
    
    protected $fillable = [
        'publicacion_id',
        'remitente_id',
        'destinatario_id',
        'mensaje',
        'archivo_adjunto',
        'leido',
        'fecha_lectura'
    ];
    
    /**
     * Crear mensaje
     */
    public function enviar($publicacionId, $remitenteId, $destinatarioId, $mensaje, $archivoAdjunto = null)
    {
        return $this->create([
            'publicacion_id' => $publicacionId,
            'remitente_id' => $remitenteId,
            'destinatario_id' => $destinatarioId,
            'mensaje' => $mensaje,
            'archivo_adjunto' => $archivoAdjunto
        ]);
    }
    
    /**
     * Obtener conversación entre dos usuarios sobre una publicación
     */
    public function getConversacion($publicacionId, $usuario1Id, $usuario2Id)
    {
        $sql = "SELECT 
                    m.*,
                    ur.nombre as remitente_nombre,
                    ur.foto_perfil as remitente_foto,
                    ud.nombre as destinatario_nombre,
                    ud.foto_perfil as destinatario_foto
                FROM {$this->table} m
                INNER JOIN usuarios ur ON m.remitente_id = ur.id
                INNER JOIN usuarios ud ON m.destinatario_id = ud.id
                WHERE m.publicacion_id = ?
                AND (
                    (m.remitente_id = ? AND m.destinatario_id = ?)
                    OR
                    (m.remitente_id = ? AND m.destinatario_id = ?)
                )
                ORDER BY m.fecha_envio ASC";
        
        return $this->query($sql, [$publicacionId, $usuario1Id, $usuario2Id, $usuario2Id, $usuario1Id]);
    }
    
    /**
     * Obtener conversaciones de un usuario
     */
    public function getConversacionesUsuario($usuarioId)
    {
        $sql = "SELECT 
                    p.id as publicacion_id,
                    p.titulo as publicacion_titulo,
                    p.foto_principal,
                    CASE 
                        WHEN m.remitente_id = ? THEN m.destinatario_id
                        ELSE m.remitente_id
                    END as otro_usuario_id,
                    CASE 
                        WHEN m.remitente_id = ? THEN ud.nombre
                        ELSE ur.nombre
                    END as otro_usuario_nombre,
                    CASE 
                        WHEN m.remitente_id = ? THEN ud.foto_perfil
                        ELSE ur.foto_perfil
                    END as otro_usuario_foto,
                    MAX(m.fecha_envio) as ultimo_mensaje_fecha,
                    (SELECT mensaje FROM mensajes 
                     WHERE publicacion_id = p.id 
                     AND (remitente_id = ? OR destinatario_id = ?)
                     ORDER BY fecha_envio DESC LIMIT 1) as ultimo_mensaje,
                    (SELECT COUNT(*) FROM mensajes 
                     WHERE publicacion_id = p.id 
                     AND destinatario_id = ? 
                     AND leido = 0) as mensajes_no_leidos
                FROM {$this->table} m
                INNER JOIN publicaciones p ON m.publicacion_id = p.id
                INNER JOIN usuarios ur ON m.remitente_id = ur.id
                INNER JOIN usuarios ud ON m.destinatario_id = ud.id
                WHERE m.remitente_id = ? OR m.destinatario_id = ?
                GROUP BY p.id, otro_usuario_id
                ORDER BY ultimo_mensaje_fecha DESC";
        
        return $this->query($sql, [
            $usuarioId, $usuarioId, $usuarioId, 
            $usuarioId, $usuarioId, $usuarioId,
            $usuarioId, $usuarioId
        ]);
    }
    
    /**
     * Marcar mensaje como leído
     */
    public function marcarComoLeido($mensajeId)
    {
        return $this->update($mensajeId, [
            'leido' => 1,
            'fecha_lectura' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Marcar todos los mensajes de una conversación como leídos
     */
    public function marcarConversacionLeida($publicacionId, $destinatarioId)
    {
        $sql = "UPDATE {$this->table} 
                SET leido = 1, fecha_lectura = NOW()
                WHERE publicacion_id = ? 
                AND destinatario_id = ? 
                AND leido = 0";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$publicacionId, $destinatarioId]);
    }
    
    /**
     * Contar mensajes no leídos del usuario
     */
    public function contarNoLeidos($usuarioId)
    {
        $sql = "SELECT COUNT(*) as total 
                FROM {$this->table} 
                WHERE destinatario_id = ? 
                AND leido = 0";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$usuarioId]);
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        
        return $result ? $result->total : 0;
    }
    
    /**
     * Obtener mensajes recientes para el usuario
     */
    public function getRecientes($usuarioId, $limit = 5)
    {
        $sql = "SELECT 
                    m.*,
                    ur.nombre as remitente_nombre,
                    p.titulo as publicacion_titulo
                FROM {$this->table} m
                INNER JOIN usuarios ur ON m.remitente_id = ur.id
                INNER JOIN publicaciones p ON m.publicacion_id = p.id
                WHERE m.destinatario_id = ?
                ORDER BY m.fecha_envio DESC
                LIMIT {$limit}";
        
        return $this->query($sql, [$usuarioId]);
    }
    
    /**
     * Eliminar mensajes de una publicación
     */
    public function eliminarPorPublicacion($publicacionId)
    {
        $sql = "DELETE FROM {$this->table} WHERE publicacion_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$publicacionId]);
    }
    
    /**
     * Obtener estadísticas de mensajes
     */
    public function getEstadisticas()
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN leido = 1 THEN 1 ELSE 0 END) as leidos,
                    SUM(CASE WHEN leido = 0 THEN 1 ELSE 0 END) as no_leidos,
                    COUNT(DISTINCT publicacion_id) as publicaciones_con_mensajes,
                    COUNT(DISTINCT remitente_id) as usuarios_activos
                FROM {$this->table}";
        
        $result = $this->query($sql);
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Verificar si usuario puede acceder a la conversación
     */
    public function puedeAcceder($publicacionId, $usuarioId)
    {
        $sql = "SELECT COUNT(*) as total
                FROM {$this->table}
                WHERE publicacion_id = ?
                AND (remitente_id = ? OR destinatario_id = ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$publicacionId, $usuarioId, $usuarioId]);
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        
        return $result && $result->total > 0;
    }
}
