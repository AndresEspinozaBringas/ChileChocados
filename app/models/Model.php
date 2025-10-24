<?php
/**
 * Clase base Model
 * Proporciona funcionalidad común para todos los modelos
 * Patrón Active Record simplificado
 */

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

abstract class Model
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $hidden = [];
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtener todos los registros
     */
    public function all($orderBy = null)
    {
        try {
            $sql = "SELECT * FROM {$this->table}";
            
            if ($orderBy) {
                $sql .= " ORDER BY {$orderBy}";
            }
            
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error en Model::all() - {$this->table}: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Buscar por ID
     */
    public function find($id)
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error en Model::find() - {$this->table}: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Buscar el primer registro que cumpla condición
     */
    public function where($column, $operator, $value = null)
    {
        try {
            // Si solo hay 2 parámetros, asumir operador '='
            if ($value === null) {
                $value = $operator;
                $operator = '=';
            }
            
            $sql = "SELECT * FROM {$this->table} WHERE {$column} {$operator} ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$value]);
            
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error en Model::where() - {$this->table}: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Buscar primer registro
     */
    public function first($column, $operator, $value = null)
    {
        $results = $this->where($column, $operator, $value);
        return !empty($results) ? $results[0] : null;
    }
    
    /**
     * Crear nuevo registro
     */
    public function create(array $data)
    {
        try {
            // Filtrar solo campos permitidos
            $data = $this->filterFillable($data);
            
            $columns = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));
            
            $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(array_values($data));
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error en Model::create() - {$this->table}: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualizar registro
     */
    public function update($id, array $data)
    {
        try {
            // Filtrar solo campos permitidos
            $data = $this->filterFillable($data);
            
            $sets = [];
            foreach (array_keys($data) as $column) {
                $sets[] = "{$column} = ?";
            }
            
            $sql = "UPDATE {$this->table} SET " . implode(', ', $sets) . " WHERE {$this->primaryKey} = ?";
            $stmt = $this->db->prepare($sql);
            
            $values = array_values($data);
            $values[] = $id;
            
            return $stmt->execute($values);
        } catch (PDOException $e) {
            error_log("Error en Model::update() - {$this->table}: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Eliminar registro
     */
    public function delete($id)
    {
        try {
            $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error en Model::delete() - {$this->table}: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Contar registros
     */
    public function count($where = null, $params = [])
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM {$this->table}";
            
            if ($where) {
                $sql .= " WHERE {$where}";
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            return $result ? $result->total : 0;
        } catch (PDOException $e) {
            error_log("Error en Model::count() - {$this->table}: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Ejecutar query personalizada
     */
    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error en Model::query() - {$this->table}: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Paginación
     */
    public function paginate($page = 1, $perPage = 10, $where = null, $params = [])
    {
        try {
            $offset = ($page - 1) * $perPage;
            
            $sql = "SELECT * FROM {$this->table}";
            
            if ($where) {
                $sql .= " WHERE {$where}";
            }
            
            $sql .= " LIMIT {$perPage} OFFSET {$offset}";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            $data = $stmt->fetchAll(PDO::FETCH_OBJ);
            $total = $this->count($where, $params);
            
            return [
                'data' => $data,
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => ceil($total / $perPage)
            ];
        } catch (PDOException $e) {
            error_log("Error en Model::paginate() - {$this->table}: " . $e->getMessage());
            return [
                'data' => [],
                'current_page' => 1,
                'per_page' => $perPage,
                'total' => 0,
                'last_page' => 0
            ];
        }
    }
    
    /**
     * Filtrar solo campos permitidos (fillable)
     */
    protected function filterFillable(array $data)
    {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }
    
    /**
     * Ocultar campos sensibles (hidden)
     */
    public function hideFields($data)
    {
        if (empty($this->hidden)) {
            return $data;
        }
        
        if (is_array($data)) {
            foreach ($this->hidden as $field) {
                unset($data[$field]);
            }
        } elseif (is_object($data)) {
            foreach ($this->hidden as $field) {
                unset($data->$field);
            }
        }
        
        return $data;
    }
    
    /**
     * Iniciar transacción
     */
    public function beginTransaction()
    {
        return $this->db->beginTransaction();
    }
    
    /**
     * Confirmar transacción
     */
    public function commit()
    {
        return $this->db->commit();
    }
    
    /**
     * Revertir transacción
     */
    public function rollback()
    {
        return $this->db->rollBack();
    }
}
