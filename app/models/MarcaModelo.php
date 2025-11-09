<?php

namespace App\Models;

use PDO;

class MarcaModelo extends Model
{
    protected $table = 'marcas_modelos_pendientes';

    /**
     * Obtener marcas/modelos pendientes de aprobación
     */
    public function getPendientes()
    {
        $sql = "SELECT 
                    mmp.*,
                    p.titulo as publicacion_titulo,
                    p.estado as publicacion_estado,
                    u.nombre as usuario_nombre,
                    u.email as usuario_email
                FROM {$this->table} mmp
                INNER JOIN publicaciones p ON mmp.publicacion_id = p.id
                INNER JOIN usuarios u ON p.usuario_id = u.id
                WHERE mmp.estado = 'pendiente'
                ORDER BY mmp.fecha_creacion ASC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Obtener todas las solicitudes (para historial)
     */
    public function getTodas($limit = 50)
    {
        $sql = "SELECT 
                    mmp.*,
                    p.titulo as publicacion_titulo,
                    u.nombre as usuario_nombre,
                    a.nombre as admin_nombre
                FROM {$this->table} mmp
                INNER JOIN publicaciones p ON mmp.publicacion_id = p.id
                INNER JOIN usuarios u ON p.usuario_id = u.id
                LEFT JOIN usuarios a ON mmp.admin_id = a.id
                ORDER BY mmp.fecha_creacion DESC
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Aprobar marca/modelo
     */
    public function aprobar($id, $marcaSugerida, $modeloSugerido, $notas, $adminId)
    {
        // Obtener registro
        $registro = $this->find($id);
        if (!$registro) return false;

        // Actualizar publicación
        $publicacionModel = new Publicacion();
        $publicacionModel->update($registro->publicacion_id, [
            'marca' => $marcaSugerida ?? $registro->marca_ingresada,
            'modelo' => $modeloSugerido ?? $registro->modelo_ingresado,
            'marca_personalizada' => 1,
            'modelo_personalizado' => 1,
            'marca_modelo_aprobado' => 1,
            'estado' => 'pendiente' // Cambiar de borrador a pendiente para revisión normal
        ]);

        // Actualizar registro de aprobación
        $sql = "UPDATE {$this->table} 
                SET estado = ?,
                    marca_sugerida = ?,
                    modelo_sugerido = ?,
                    notas_admin = ?,
                    fecha_revision = NOW(),
                    admin_id = ?
                WHERE id = ?";
        
        $estado = ($marcaSugerida || $modeloSugerido) ? 'modificado' : 'aprobado';
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            $estado,
            $marcaSugerida,
            $modeloSugerido,
            $notas,
            $adminId,
            $id
        ]);
    }

    /**
     * Rechazar marca/modelo
     */
    public function rechazar($id, $motivo, $adminId)
    {
        // Obtener registro
        $registro = $this->find($id);
        if (!$registro) return false;

        // Actualizar publicación (mantener como borrador)
        $publicacionModel = new Publicacion();
        $publicacionModel->update($registro->publicacion_id, [
            'motivo_rechazo' => $motivo
        ]);

        // Actualizar registro
        $sql = "UPDATE {$this->table} 
                SET estado = 'rechazado',
                    notas_admin = ?,
                    fecha_revision = NOW(),
                    admin_id = ?
                WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$motivo, $adminId, $id]);
    }

    /**
     * Crear solicitud de marca/modelo personalizado
     */
    public function crearSolicitud($publicacionId, $marca, $modelo)
    {
        $sql = "INSERT INTO {$this->table} 
                (publicacion_id, marca_ingresada, modelo_ingresado, estado, fecha_creacion)
                VALUES (?, ?, ?, 'pendiente', NOW())";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$publicacionId, $marca, $modelo]);
    }

    /**
     * Verificar si una marca existe en el catálogo (BD)
     */
    public function marcaExisteEnCatalogo($marca)
    {
        $sql = "SELECT COUNT(*) FROM marcas WHERE nombre = ? AND activa = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$marca]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Verificar si un modelo existe para una marca en el catálogo (BD)
     */
    public function modeloExisteEnCatalogo($marca, $modelo)
    {
        $sql = "SELECT COUNT(*) 
                FROM modelos mo
                INNER JOIN marcas ma ON mo.marca_id = ma.id
                WHERE ma.nombre = ? AND mo.nombre = ? AND mo.activo = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$marca, $modelo]);
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Obtener todas las marcas activas
     */
    public function getMarcas()
    {
        $sql = "SELECT id, nombre, cantidad_modelos 
                FROM marcas 
                WHERE activa = 1 
                ORDER BY nombre ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    /**
     * Obtener modelos de una marca
     */
    public function getModelosPorMarca($marcaId)
    {
        $sql = "SELECT id, nombre 
                FROM modelos 
                WHERE marca_id = ? AND activo = 1 
                ORDER BY nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$marcaId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    /**
     * Obtener modelos de una marca por nombre
     */
    public function getModelosPorNombreMarca($nombreMarca)
    {
        $sql = "SELECT mo.id, mo.nombre 
                FROM modelos mo
                INNER JOIN marcas ma ON mo.marca_id = ma.id
                WHERE ma.nombre = ? AND mo.activo = 1 
                ORDER BY mo.nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$nombreMarca]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
