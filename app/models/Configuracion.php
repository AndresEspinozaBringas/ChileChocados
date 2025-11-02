<?php
/**
 * Modelo Configuracion
 * Gestiona las configuraciones del sistema
 */

namespace App\Models;

use PDO;

class Configuracion extends Model
{
    protected $table = 'configuraciones';
    
    protected $fillable = [
        'clave',
        'valor',
        'tipo',
        'descripcion'
    ];
    
    /**
     * Obtener valor de una configuración por clave
     */
    public static function get($clave, $default = null)
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT valor FROM configuraciones WHERE clave = ?");
        $stmt->execute([$clave]);
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        
        return $result ? $result->valor : $default;
    }
    
    /**
     * Establecer valor de una configuración
     */
    public static function set($clave, $valor, $tipo = 'string', $descripcion = '')
    {
        $db = getDB();
        
        // Verificar si existe
        $stmt = $db->prepare("SELECT id FROM configuraciones WHERE clave = ?");
        $stmt->execute([$clave]);
        $existe = $stmt->fetch(PDO::FETCH_OBJ);
        
        if ($existe) {
            // Actualizar
            $stmt = $db->prepare("
                UPDATE configuraciones 
                SET valor = ?, tipo = ?, descripcion = ?, fecha_actualizacion = NOW() 
                WHERE clave = ?
            ");
            return $stmt->execute([$valor, $tipo, $descripcion, $clave]);
        } else {
            // Insertar
            $stmt = $db->prepare("
                INSERT INTO configuraciones (clave, valor, tipo, descripcion) 
                VALUES (?, ?, ?, ?)
            ");
            return $stmt->execute([$clave, $valor, $tipo, $descripcion]);
        }
    }
    
    /**
     * Obtener todas las configuraciones como array asociativo
     */
    public static function getAll()
    {
        $db = getDB();
        $stmt = $db->query("SELECT clave, valor FROM configuraciones");
        $rows = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        $config = [];
        foreach ($rows as $row) {
            $config[$row->clave] = $row->valor;
        }
        
        return $config;
    }
    
    /**
     * Obtener configuraciones con valores por defecto
     */
    public static function getAllWithDefaults()
    {
        $config = self::getAll();
        
        // Valores por defecto
        $defaults = [
            'precio_destacado_15_dias' => 15000,
            'precio_destacado_30_dias' => 25000,
            'minimo_fotos' => 1,
            'maximo_fotos' => 6,
            'tamano_maximo_imagen_mb' => 5,
            'tamano_maximo_adjunto_mb' => 10
        ];
        
        foreach ($defaults as $key => $value) {
            if (!isset($config[$key])) {
                $config[$key] = $value;
            }
        }
        
        return $config;
    }
    
    /**
     * Obtener precio de destacado según días
     */
    public static function getPrecioDestacado($dias)
    {
        if ($dias == 15) {
            return (int)self::get('precio_destacado_15_dias', 15000);
        } elseif ($dias == 30) {
            return (int)self::get('precio_destacado_30_dias', 25000);
        }
        
        return 0;
    }
    
    /**
     * Obtener límites de fotos
     */
    public static function getLimitesFotos()
    {
        return [
            'minimo' => (int)self::get('minimo_fotos', 1),
            'maximo' => (int)self::get('maximo_fotos', 6)
        ];
    }
    
    /**
     * Obtener tamaños máximos de archivos
     */
    public static function getTamanosMaximos()
    {
        return [
            'imagen_mb' => (float)self::get('tamano_maximo_imagen_mb', 5),
            'adjunto_mb' => (float)self::get('tamano_maximo_adjunto_mb', 10)
        ];
    }
}
