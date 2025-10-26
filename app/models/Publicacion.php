<?php
/**
 * Modelo Publicacion
 * Gestiona publicaciones de vehículos siniestrados
 */

namespace App\Models;

use PDO;

class Publicacion extends Model
{
    protected $table = 'publicaciones';
    
    protected $fillable = [
        'usuario_id',
        'categoria_padre_id',
        'subcategoria_id',
        'titulo',
        'tipificacion',
        'marca',
        'modelo',
        'anio',
        'descripcion',
        'tipo_venta',
        'precio',
        'region_id',
        'comuna_id',
        'estado',
        'es_destacada',
        'fecha_destacada_inicio',
        'fecha_destacada_fin',
        'foto_principal',
        'motivo_rechazo',
        'fecha_publicacion',
        'fecha_venta'
    ];
    
    /**
     * Estados de publicación
     */
    const ESTADO_BORRADOR = 'borrador';
    const ESTADO_PENDIENTE = 'pendiente';
    const ESTADO_APROBADA = 'aprobada';
    const ESTADO_RECHAZADA = 'rechazada';
    const ESTADO_VENDIDA = 'vendida';
    const ESTADO_ARCHIVADA = 'archivada';
    
    /**
     * Tipos de venta
     */
    const TIPO_COMPLETO = 'completo';
    const TIPO_DESARME = 'desarme';
    
    /**
     * Obtener publicaciones con información relacionada
     */
    public function getConRelaciones($id)
    {
        $sql = "SELECT 
                    p.*,
                    u.nombre as vendedor_nombre,
                    u.apellido as vendedor_apellido,
                    u.email as vendedor_email,
                    u.telefono as vendedor_telefono,
                    u.foto_perfil as vendedor_foto,
                    cp.nombre as categoria_nombre,
                    cp.slug as categoria_slug,
                    sc.nombre as subcategoria_nombre,
                    r.nombre as region_nombre,
                    c.nombre as comuna_nombre
                FROM {$this->table} p
                INNER JOIN usuarios u ON p.usuario_id = u.id
                INNER JOIN categorias_padre cp ON p.categoria_padre_id = cp.id
                LEFT JOIN subcategorias sc ON p.subcategoria_id = sc.id
                INNER JOIN regiones r ON p.region_id = r.id
                LEFT JOIN comunas c ON p.comuna_id = c.id
                WHERE p.id = ?";
        
        $result = $this->query($sql, [$id]);
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Listar publicaciones con filtros
     */
    public function listar($filtros = [], $page = 1, $perPage = 12)
    {
        $sql = "SELECT 
                    p.*,
                    u.nombre as vendedor_nombre,
                    cp.nombre as categoria_nombre,
                    cp.slug as categoria_slug,
                    r.nombre as region_nombre
                FROM {$this->table} p
                INNER JOIN usuarios u ON p.usuario_id = u.id
                INNER JOIN categorias_padre cp ON p.categoria_padre_id = cp.id
                INNER JOIN regiones r ON p.region_id = r.id
                WHERE 1=1";
        
        $params = [];
        
        // Filtro por estado
        if (!empty($filtros['estado'])) {
            $sql .= " AND p.estado = ?";
            $params[] = $filtros['estado'];
        }
        
        // Filtro por categoría
        if (!empty($filtros['categoria_id'])) {
            $sql .= " AND p.categoria_padre_id = ?";
            $params[] = $filtros['categoria_id'];
        }
        
        // Filtro por subcategoría
        if (!empty($filtros['subcategoria_id'])) {
            $sql .= " AND p.subcategoria_id = ?";
            $params[] = $filtros['subcategoria_id'];
        }
        
        // Filtro por región
        if (!empty($filtros['region_id'])) {
            $sql .= " AND p.region_id = ?";
            $params[] = $filtros['region_id'];
        }
        
        // Filtro por tipo de venta
        if (!empty($filtros['tipo_venta'])) {
            $sql .= " AND p.tipo_venta = ?";
            $params[] = $filtros['tipo_venta'];
        }
        
        // Búsqueda por texto
        if (!empty($filtros['q'])) {
            $sql .= " AND (p.titulo LIKE ? OR p.marca LIKE ? OR p.modelo LIKE ? OR p.descripcion LIKE ?)";
            $busqueda = "%{$filtros['q']}%";
            $params[] = $busqueda;
            $params[] = $busqueda;
            $params[] = $busqueda;
            $params[] = $busqueda;
        }
        
        // Filtro por rango de precio
        if (!empty($filtros['precio_min'])) {
            $sql .= " AND p.precio >= ?";
            $params[] = $filtros['precio_min'];
        }
        
        if (!empty($filtros['precio_max'])) {
            $sql .= " AND p.precio <= ?";
            $params[] = $filtros['precio_max'];
        }
        
        // Ordenamiento
        $orderBy = $filtros['order_by'] ?? 'p.fecha_creacion DESC';
        $sql .= " ORDER BY {$orderBy}";
        
        // Paginación
        $offset = ($page - 1) * $perPage;
        $sql .= " LIMIT {$perPage} OFFSET {$offset}";
        
        $data = $this->query($sql, $params);
        
        // Contar total
        $sqlCount = str_replace('SELECT p.*', 'SELECT COUNT(*) as total', $sql);
        $sqlCount = preg_replace('/ORDER BY.*$/', '', $sqlCount);
        $sqlCount = preg_replace('/LIMIT.*$/', '', $sqlCount);
        
        $stmt = $this->db->prepare($sqlCount);
        $stmt->execute($params);
        $totalResult = $stmt->fetch(PDO::FETCH_OBJ);
        $total = $totalResult ? $totalResult->total : 0;
        
        return [
            'data' => $data,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => ceil($total / $perPage)
        ];
    }
    
    /**
     * Obtener publicaciones del usuario
     */
    public function getByUsuario($usuarioId, $estado = null)
    {
        $sql = "SELECT p.*, cp.nombre as categoria_nombre
                FROM {$this->table} p
                INNER JOIN categorias_padre cp ON p.categoria_padre_id = cp.id
                WHERE p.usuario_id = ?";
        
        $params = [$usuarioId];
        
        if ($estado) {
            $sql .= " AND p.estado = ?";
            $params[] = $estado;
        }
        
        $sql .= " ORDER BY p.fecha_creacion DESC";
        
        return $this->query($sql, $params);
    }
    
    /**
     * Obtener publicaciones destacadas
     */
    public function getDestacadas($limit = 8)
    {
        $sql = "SELECT p.*, cp.nombre as categoria_nombre, r.nombre as region_nombre
                FROM {$this->table} p
                INNER JOIN categorias_padre cp ON p.categoria_padre_id = cp.id
                INNER JOIN regiones r ON p.region_id = r.id
                WHERE p.es_destacada = 1
                AND p.estado = ?
                AND p.fecha_destacada_fin > NOW()
                ORDER BY p.fecha_destacada_inicio DESC
                LIMIT {$limit}";
        
        return $this->query($sql, [self::ESTADO_APROBADA]);
    }
    
    /**
     * Aprobar publicación
     */
    public function aprobar($id)
    {
        return $this->update($id, [
            'estado' => self::ESTADO_APROBADA,
            'fecha_publicacion' => date('Y-m-d H:i:s'),
            'motivo_rechazo' => null
        ]);
    }
    
    /**
     * Rechazar publicación
     */
    public function rechazar($id, $motivo)
    {
        return $this->update($id, [
            'estado' => self::ESTADO_RECHAZADA,
            'motivo_rechazo' => $motivo
        ]);
    }
    
    /**
     * Marcar como vendida
     */
    public function marcarVendida($id)
    {
        return $this->update($id, [
            'estado' => self::ESTADO_VENDIDA,
            'fecha_venta' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Archivar publicación
     */
    public function archivar($id)
    {
        return $this->update($id, ['estado' => self::ESTADO_ARCHIVADA]);
    }
    
    /**
     * Activar destacado
     */
    public function activarDestacado($id, $dias)
    {
        $inicio = date('Y-m-d H:i:s');
        $fin = date('Y-m-d H:i:s', strtotime("+{$dias} days"));
        
        return $this->update($id, [
            'es_destacada' => 1,
            'fecha_destacada_inicio' => $inicio,
            'fecha_destacada_fin' => $fin
        ]);
    }
    
    /**
     * Desactivar destacado
     */
    public function desactivarDestacado($id)
    {
        return $this->update($id, [
            'es_destacada' => 0,
            'fecha_destacada_fin' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Incrementar visitas
     */
    public function incrementarVisitas($id)
    {
        $sql = "UPDATE {$this->table} SET visitas = visitas + 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    /**
     * Obtener publicaciones pendientes de moderación
     */
    public function getPendientes()
    {
        $sql = "SELECT p.*, u.nombre as vendedor_nombre, u.email as vendedor_email
                FROM {$this->table} p
                INNER JOIN usuarios u ON p.usuario_id = u.id
                WHERE p.estado = ?
                ORDER BY p.fecha_creacion ASC";
        
        return $this->query($sql, [self::ESTADO_PENDIENTE]);
    }
    
    /**
     * Obtener estadísticas
     */
    public function getEstadisticas()
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'aprobada' THEN 1 ELSE 0 END) as aprobadas,
                    SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN estado = 'vendida' THEN 1 ELSE 0 END) as vendidas,
                    SUM(CASE WHEN es_destacada = 1 THEN 1 ELSE 0 END) as destacadas,
                    AVG(visitas) as visitas_promedio,
                    SUM(visitas) as visitas_total
                FROM {$this->table}";
        
        $result = $this->query($sql);
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Obtener publicaciones más vistas
     */
    public function getMasVistas($limit = 10)
    {
        $sql = "SELECT p.*, cp.nombre as categoria_nombre
                FROM {$this->table} p
                INNER JOIN categorias_padre cp ON p.categoria_padre_id = cp.id
                WHERE p.estado = ?
                ORDER BY p.visitas DESC
                LIMIT {$limit}";
        
        return $this->query($sql, [self::ESTADO_APROBADA]);
    }
    
    /**
     * Verificar destacados expirados y desactivarlos
     */
    public function desactivarDestacadosExpirados()
    {
        $sql = "UPDATE {$this->table} 
                SET es_destacada = 0
                WHERE es_destacada = 1
                AND fecha_destacada_fin < NOW()";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute();
    }
    
    /**
     * Validar datos de publicación
     */
    public function validarDatos(array $data)
    {
        $errores = [];
        
        if (empty($data['titulo'])) {
            $errores['titulo'] = 'El título es obligatorio';
        }
        
        if (empty($data['categoria_padre_id'])) {
            $errores['categoria_padre_id'] = 'La categoría es obligatoria';
        }
        
        if (empty($data['descripcion'])) {
            $errores['descripcion'] = 'La descripción es obligatoria';
        } elseif (strlen($data['descripcion']) < 50) {
            $errores['descripcion'] = 'La descripción debe tener al menos 50 caracteres';
        }
        
        if (empty($data['tipo_venta'])) {
            $errores['tipo_venta'] = 'El tipo de venta es obligatorio';
        }
        
        if ($data['tipo_venta'] === self::TIPO_COMPLETO && empty($data['precio'])) {
            $errores['precio'] = 'El precio es obligatorio para venta completa';
        }
        
        if (empty($data['region_id'])) {
            $errores['region_id'] = 'La región es obligatoria';
        }
        
        return $errores;
    }
    
    /**
     * Agregar imagen a la publicación
     */
    public function agregarImagen($publicacionId, $datos)
    {
        $sql = "INSERT INTO publicacion_fotos (publicacion_id, url, orden, es_principal, fecha_creacion)
                VALUES (?, ?, ?, ?, NOW())";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $publicacionId,
                $datos['url'],
                $datos['orden'] ?? 1,
                $datos['es_principal'] ?? 0
            ]);
        } catch (\PDOException $e) {
            error_log("ERROR al guardar imagen: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener imágenes de una publicación
     */
    public function getImagenes($publicacionId)
    {
        $sql = "SELECT * FROM publicacion_fotos 
                WHERE publicacion_id = ? 
                ORDER BY es_principal DESC, orden ASC";
        
        return $this->query($sql, [$publicacionId]);
    }
    
    /**
     * Eliminar imagen
     */
    public function eliminarImagen($imagenId)
    {
        $sql = "DELETE FROM publicacion_fotos WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$imagenId]);
    }
    
    /**
     * Obtener publicaciones similares
     */
    public function getSimilares($publicacionId, $categoriaId, $limit = 4)
    {
        $sql = "SELECT p.*, cp.nombre as categoria_nombre, r.nombre as region_nombre
                FROM {$this->table} p
                INNER JOIN categorias_padre cp ON p.categoria_padre_id = cp.id
                INNER JOIN regiones r ON p.region_id = r.id
                WHERE p.categoria_padre_id = ?
                AND p.id != ?
                AND p.estado = ?
                ORDER BY RAND()
                LIMIT {$limit}";
        
        return $this->query($sql, [$categoriaId, $publicacionId, self::ESTADO_APROBADA]);
    }
    
    /**
     * Listar con filtros (método mejorado)
     */
    public function listarConFiltros($filtros = [])
    {
        $sql = "SELECT 
                    p.*,
                    u.nombre as vendedor_nombre,
                    cp.nombre as categoria_nombre,
                    cp.slug as categoria_slug,
                    r.nombre as region_nombre,
                    c.nombre as comuna_nombre
                FROM {$this->table} p
                INNER JOIN usuarios u ON p.usuario_id = u.id
                INNER JOIN categorias_padre cp ON p.categoria_padre_id = cp.id
                INNER JOIN regiones r ON p.region_id = r.id
                LEFT JOIN comunas c ON p.comuna_id = c.id
                WHERE p.estado = ?";
        
        $params = [self::ESTADO_APROBADA];
        
        // Aplicar filtros
        if (!empty($filtros['categoria_id'])) {
            $sql .= " AND p.categoria_padre_id = ?";
            $params[] = $filtros['categoria_id'];
        }
        
        if (!empty($filtros['subcategoria_id'])) {
            $sql .= " AND p.subcategoria_id = ?";
            $params[] = $filtros['subcategoria_id'];
        }
        
        if (!empty($filtros['region_id'])) {
            $sql .= " AND p.region_id = ?";
            $params[] = $filtros['region_id'];
        }
        
        if (!empty($filtros['comuna_id'])) {
            $sql .= " AND p.comuna_id = ?";
            $params[] = $filtros['comuna_id'];
        }
        
        if (!empty($filtros['tipo_venta'])) {
            $sql .= " AND p.tipo_venta = ?";
            $params[] = $filtros['tipo_venta'];
        }
        
        if (!empty($filtros['precio_min'])) {
            $sql .= " AND p.precio >= ?";
            $params[] = $filtros['precio_min'];
        }
        
        if (!empty($filtros['precio_max'])) {
            $sql .= " AND p.precio <= ?";
            $params[] = $filtros['precio_max'];
        }
        
        if (!empty($filtros['buscar'])) {
            $sql .= " AND (p.titulo LIKE ? OR p.marca LIKE ? OR p.modelo LIKE ? OR p.descripcion LIKE ?)";
            $busqueda = "%{$filtros['buscar']}%";
            $params[] = $busqueda;
            $params[] = $busqueda;
            $params[] = $busqueda;
            $params[] = $busqueda;
        }
        
        // Ordenamiento
        $orden = $filtros['orden'] ?? 'recientes';
        switch ($orden) {
            case 'precio_asc':
                $sql .= " ORDER BY p.precio ASC";
                break;
            case 'precio_desc':
                $sql .= " ORDER BY p.precio DESC";
                break;
            case 'antiguos':
                $sql .= " ORDER BY p.fecha_publicacion ASC";
                break;
            default:
                $sql .= " ORDER BY p.fecha_publicacion DESC";
        }
        
        // Paginación
        $page = $filtros['page'] ?? 1;
        $perPage = 12;
        $offset = ($page - 1) * $perPage;
        $sql .= " LIMIT {$perPage} OFFSET {$offset}";
        
        $publicaciones = $this->query($sql, $params);
        
        // Contar total
        $sqlCount = "SELECT COUNT(*) as total
                     FROM {$this->table} p
                     WHERE p.estado = ?";
        
        $paramsCount = [self::ESTADO_APROBADA];
        
        if (!empty($filtros['categoria_id'])) {
            $sqlCount .= " AND p.categoria_padre_id = ?";
            $paramsCount[] = $filtros['categoria_id'];
        }
        
        $stmt = $this->db->prepare($sqlCount);
        $stmt->execute($paramsCount);
        $totalResult = $stmt->fetch(PDO::FETCH_OBJ);
        $total = $totalResult ? $totalResult->total : 0;
        
        return [
            'publicaciones' => $publicaciones,
            'total' => $total,
            'total_paginas' => ceil($total / $perPage)
        ];
    }
}
