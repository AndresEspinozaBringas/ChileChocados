<?php
/**
 * Modelo Categoria
 * Gestiona categorías padre y subcategorías
 */

namespace App\Models;

use PDO;

class Categoria extends Model
{
    protected $table = 'categorias_padre';
    
    protected $fillable = [
        'nombre',
        'slug',
        'icono',
        'descripcion',
        'activo',
        'orden'
    ];
    
    /**
     * Obtener categorías activas
     */
    public function getActivas()
    {
        return $this->where('activo', '=', 1);
    }
    
    /**
     * Obtener categoría por slug
     */
    public function findBySlug($slug)
    {
        return $this->first('slug', '=', $slug);
    }
    
    /**
     * Obtener categoría con subcategorías
     */
    public function getConSubcategorias($id = null)
    {
        $sql = "SELECT 
                    cp.*,
                    (SELECT COUNT(*) FROM subcategorias WHERE categoria_padre_id = cp.id AND activo = 1) as total_subcategorias
                FROM {$this->table} cp
                WHERE cp.activo = 1";
        
        $params = [];
        
        if ($id) {
            $sql .= " AND cp.id = ?";
            $params[] = $id;
        }
        
        $sql .= " ORDER BY cp.orden ASC";
        
        $categorias = $this->query($sql, $params);
        
        // Cargar subcategorías para cada categoría
        foreach ($categorias as &$categoria) {
            $categoria->subcategorias = $this->getSubcategorias($categoria->id);
        }
        
        return $id && !empty($categorias) ? $categorias[0] : $categorias;
    }
    
    /**
     * Obtener subcategorías de una categoría
     */
    public function getSubcategorias($categoriaPadreId)
    {
        $sql = "SELECT * FROM subcategorias 
                WHERE categoria_padre_id = ? 
                AND activo = 1 
                ORDER BY orden ASC";
        
        return $this->query($sql, [$categoriaPadreId]);
    }
    
    /**
     * Obtener todas las categorías con conteo de publicaciones
     * 
     * TODO: CORREGIR - Actualmente cuenta TODAS las publicaciones sin filtrar por estado
     * Debería contar solo publicaciones con estado = 'aprobada'
     * Cambiar: COUNT(p.id) por COUNT(CASE WHEN p.estado = 'aprobada' THEN 1 END)
     * O agregar: AND p.estado = 'aprobada' en el LEFT JOIN
     */
    public function getConConteoPublicaciones()
    {
        $sql = "SELECT 
                    cp.*,
                    COUNT(p.id) as total_publicaciones,
                    COUNT(CASE WHEN p.estado = 'aprobada' THEN 1 END) as publicaciones_activas
                FROM {$this->table} cp
                LEFT JOIN publicaciones p ON p.categoria_padre_id = cp.id
                WHERE cp.activo = 1
                GROUP BY cp.id
                ORDER BY cp.orden ASC";
        
        return $this->query($sql);
    }
    
    /**
     * Crear subcategoría
     */
    public function createSubcategoria($categoriaPadreId, $nombre, $slug)
    {
        $sql = "INSERT INTO subcategorias (categoria_padre_id, nombre, slug, activo, orden) 
                VALUES (?, ?, ?, 1, 0)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$categoriaPadreId, $nombre, $slug]);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Actualizar subcategoría
     */
    public function updateSubcategoria($id, $nombre, $slug)
    {
        $sql = "UPDATE subcategorias SET nombre = ?, slug = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nombre, $slug, $id]);
    }
    
    /**
     * Eliminar subcategoría
     */
    public function deleteSubcategoria($id)
    {
        $sql = "DELETE FROM subcategorias WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    /**
     * Verificar si slug existe
     */
    public function slugExists($slug, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE slug = ?";
        $params = [$slug];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        
        return $result->total > 0;
    }
    
    /**
     * Generar slug único
     */
    public function generarSlugUnico($texto, $excludeId = null)
    {
        $slug = $this->generarSlug($texto);
        $slugOriginal = $slug;
        $contador = 1;
        
        while ($this->slugExists($slug, $excludeId)) {
            $slug = $slugOriginal . '-' . $contador;
            $contador++;
        }
        
        return $slug;
    }
    
    /**
     * Generar slug desde texto
     */
    private function generarSlug($texto)
    {
        // Convertir a minúsculas
        $slug = mb_strtolower($texto, 'UTF-8');
        
        // Reemplazar acentos
        $slug = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ñ'],
            ['a', 'e', 'i', 'o', 'u', 'n'],
            $slug
        );
        
        // Reemplazar espacios y caracteres especiales
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        
        // Eliminar guiones al inicio y final
        $slug = trim($slug, '-');
        
        return $slug;
    }
}
