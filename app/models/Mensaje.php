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
                    conv.publicacion_id,
                    conv.publicacion_titulo,
                    conv.foto_principal,
                    conv.otro_usuario_id,
                    conv.otro_usuario_nombre,
                    conv.otro_usuario_avatar,
                    conv.otro_usuario_tipo,
                    conv.ultimo_mensaje_fecha,
                    (SELECT mensaje FROM mensajes 
                     WHERE publicacion_id = conv.publicacion_id 
                     AND ((remitente_id = ? AND destinatario_id = conv.otro_usuario_id) 
                          OR (remitente_id = conv.otro_usuario_id AND destinatario_id = ?))
                     ORDER BY fecha_envio DESC LIMIT 1) as ultimo_mensaje,
                    (SELECT COUNT(*) FROM mensajes 
                     WHERE publicacion_id = conv.publicacion_id 
                     AND remitente_id = conv.otro_usuario_id
                     AND destinatario_id = ? 
                     AND leido = 0) as mensajes_no_leidos
                FROM (
                    SELECT 
                        p.id as publicacion_id,
                        p.titulo as publicacion_titulo,
                        p.foto_principal,
                        IF(m.remitente_id = ?, m.destinatario_id, m.remitente_id) as otro_usuario_id,
                        IF(m.remitente_id = ?, 
                           CONCAT(COALESCE(ud.nombre, ''), ' ', COALESCE(ud.apellido, '')),
                           CONCAT(COALESCE(ur.nombre, ''), ' ', COALESCE(ur.apellido, ''))
                        ) as otro_usuario_nombre,
                        IF(m.remitente_id = ?, ud.avatar, ur.avatar) as otro_usuario_avatar,
                        IF(m.remitente_id = ?, ud.rol, ur.rol) as otro_usuario_tipo,
                        MAX(m.fecha_envio) as ultimo_mensaje_fecha
                    FROM {$this->table} m
                    INNER JOIN publicaciones p ON m.publicacion_id = p.id
                    INNER JOIN usuarios ur ON m.remitente_id = ur.id
                    INNER JOIN usuarios ud ON m.destinatario_id = ud.id
                    WHERE m.remitente_id = ? OR m.destinatario_id = ?
                    GROUP BY p.id, otro_usuario_id, otro_usuario_nombre, otro_usuario_avatar, otro_usuario_tipo
                ) as conv
                ORDER BY conv.ultimo_mensaje_fecha DESC";
        
        return $this->query($sql, [
            $usuarioId, $usuarioId, $usuarioId,
            $usuarioId, $usuarioId, $usuarioId, $usuarioId,
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
     * Contar TODOS los mensajes no leídos del sistema (para admin)
     */
    public function contarTodosNoLeidos()
    {
        $sql = "SELECT COUNT(*) as total 
                FROM {$this->table} 
                WHERE leido = 0";
        
        $stmt = $this->db->query($sql);
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
    
    /**
     * Obtener todas las conversaciones del sistema (para admin)
     * Agrupa por publicación y par de usuarios únicos
     */
    public function getTodasLasConversaciones()
    {
        // Primero obtener las conversaciones únicas
        $sql = "SELECT 
                    p.id as publicacion_id,
                    p.titulo as publicacion_titulo,
                    p.foto_principal,
                    LEAST(m.remitente_id, m.destinatario_id) as usuario1_id,
                    GREATEST(m.remitente_id, m.destinatario_id) as usuario2_id,
                    MAX(m.fecha_envio) as ultimo_mensaje_fecha
                FROM {$this->table} m
                INNER JOIN publicaciones p ON m.publicacion_id = p.id
                GROUP BY 
                    p.id, 
                    p.titulo, 
                    p.foto_principal,
                    LEAST(m.remitente_id, m.destinatario_id),
                    GREATEST(m.remitente_id, m.destinatario_id)
                ORDER BY ultimo_mensaje_fecha DESC";
        
        $conversaciones = $this->query($sql);
        
        // Enriquecer cada conversación con datos adicionales
        foreach ($conversaciones as &$conv) {
            // Obtener datos del usuario 1
            $stmt = $this->db->prepare("SELECT CONCAT(COALESCE(nombre, ''), ' ', COALESCE(apellido, '')) as nombre, rol FROM usuarios WHERE id = ?");
            $stmt->execute([$conv->usuario1_id]);
            $usuario1 = $stmt->fetch(PDO::FETCH_OBJ);
            $conv->usuario1_nombre = $usuario1 ? $usuario1->nombre : '';
            $conv->usuario1_rol = $usuario1 ? $usuario1->rol : '';
            
            // Obtener datos del usuario 2
            $stmt = $this->db->prepare("SELECT CONCAT(COALESCE(nombre, ''), ' ', COALESCE(apellido, '')) as nombre, rol FROM usuarios WHERE id = ?");
            $stmt->execute([$conv->usuario2_id]);
            $usuario2 = $stmt->fetch(PDO::FETCH_OBJ);
            $conv->usuario2_nombre = $usuario2 ? $usuario2->nombre : '';
            $conv->usuario2_rol = $usuario2 ? $usuario2->rol : '';
            
            // Generar clave de conversación
            $conv->conversacion_key = $conv->publicacion_id . '-' . $conv->usuario1_id . '-' . $conv->usuario2_id;
            
            // Obtener último mensaje
            $stmt = $this->db->prepare("SELECT mensaje FROM mensajes 
                                        WHERE publicacion_id = ? 
                                        AND ((remitente_id = ? AND destinatario_id = ?) 
                                             OR (remitente_id = ? AND destinatario_id = ?))
                                        ORDER BY fecha_envio DESC LIMIT 1");
            $stmt->execute([$conv->publicacion_id, $conv->usuario1_id, $conv->usuario2_id, $conv->usuario2_id, $conv->usuario1_id]);
            $ultimoMensaje = $stmt->fetch(PDO::FETCH_OBJ);
            $conv->ultimo_mensaje = $ultimoMensaje ? $ultimoMensaje->mensaje : '';
            
            // Contar mensajes no leídos
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM mensajes 
                                        WHERE publicacion_id = ? 
                                        AND ((remitente_id = ? AND destinatario_id = ?) 
                                             OR (remitente_id = ? AND destinatario_id = ?))
                                        AND leido = 0");
            $stmt->execute([$conv->publicacion_id, $conv->usuario1_id, $conv->usuario2_id, $conv->usuario2_id, $conv->usuario1_id]);
            $noLeidos = $stmt->fetch(PDO::FETCH_OBJ);
            $conv->mensajes_no_leidos = $noLeidos ? $noLeidos->total : 0;
        }
        
        return $conversaciones;
    }
    
    /**
     * Obtener conversación específica por clave (para admin)
     * Formato de clave: publicacion_id-usuario1_id-usuario2_id
     */
    public function getConversacionPorClave($conversacionKey)
    {
        $partes = explode('-', $conversacionKey);
        if (count($partes) !== 3) {
            return null;
        }
        
        list($publicacionId, $usuario1Id, $usuario2Id) = $partes;
        
        // Obtener mensajes de la conversación
        $sql = "SELECT 
                    m.*,
                    ur.nombre as remitente_nombre,
                    ur.apellido as remitente_apellido,
                    ur.avatar as remitente_avatar,
                    ur.rol as remitente_rol,
                    ud.nombre as destinatario_nombre,
                    ud.apellido as destinatario_apellido,
                    ud.avatar as destinatario_avatar,
                    ud.rol as destinatario_rol
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
}
