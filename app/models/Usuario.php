<?php
/**
 * Modelo Usuario
 * Gestiona usuarios: admin, vendedor, comprador
 */

namespace App\Models;

use PDO;

class Usuario extends Model
{
    protected $table = 'usuarios';
    
    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'password',
        'telefono',
        'rut',
        'rol',
        'estado',
        'verificado',
        'foto_perfil',
        'redes_sociales',
        'token_recuperacion',
        'token_expira'
    ];
    
    protected $hidden = [
        'password',
        'token_recuperacion',
        'token_expira'
    ];
    
    /**
     * Roles disponibles
     */
    const ROL_ADMIN = 'admin';
    const ROL_VENDEDOR = 'vendedor';
    const ROL_COMPRADOR = 'comprador';
    
    /**
     * Estados disponibles
     */
    const ESTADO_ACTIVO = 'activo';
    const ESTADO_SUSPENDIDO = 'suspendido';
    const ESTADO_ELIMINADO = 'eliminado';
    
    /**
     * Buscar usuario por email
     */
    public function findByEmail($email)
    {
        return $this->first('email', '=', $email);
    }
    
    /**
     * Crear usuario con password hasheado
     */
    public function createUser(array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        
        // Rol por defecto: comprador
        if (!isset($data['rol'])) {
            $data['rol'] = self::ROL_COMPRADOR;
        }
        
        // Estado por defecto: activo
        if (!isset($data['estado'])) {
            $data['estado'] = self::ESTADO_ACTIVO;
        }
        
        return $this->create($data);
    }
    
    /**
     * Verificar password
     */
    public function verifyPassword($plainPassword, $hashedPassword)
    {
        return password_verify($plainPassword, $hashedPassword);
    }
    
    /**
     * Actualizar password
     */
    public function updatePassword($userId, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        return $this->update($userId, ['password' => $hashedPassword]);
    }
    
    /**
     * Verificar si email existe
     */
    public function emailExists($email, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE email = ?";
        $params = [$email];
        
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
     * Generar token de recuperación
     */
    public function generateRecoveryToken($userId)
    {
        $token = bin2hex(random_bytes(32));
        $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $this->update($userId, [
            'token_recuperacion' => $token,
            'token_expira' => $expira
        ]);
        
        return $token;
    }
    
    /**
     * Verificar token de recuperación
     */
    public function verifyRecoveryToken($token)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE token_recuperacion = ? 
                AND token_expira > NOW() 
                AND estado = ?
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$token, self::ESTADO_ACTIVO]);
        
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    
    /**
     * Limpiar token de recuperación
     */
    public function clearRecoveryToken($userId)
    {
        return $this->update($userId, [
            'token_recuperacion' => null,
            'token_expira' => null
        ]);
    }
    
    /**
     * Actualizar última conexión
     */
    public function updateLastConnection($userId)
    {
        $sql = "UPDATE {$this->table} SET ultima_conexion = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId]);
    }
    
    /**
     * Verificar usuario (vendedor)
     */
    public function verificarUsuario($userId)
    {
        return $this->update($userId, ['verificado' => 1]);
    }
    
    /**
     * Suspender usuario
     */
    public function suspender($userId)
    {
        return $this->update($userId, ['estado' => self::ESTADO_SUSPENDIDO]);
    }
    
    /**
     * Activar usuario
     */
    public function activar($userId)
    {
        return $this->update($userId, ['estado' => self::ESTADO_ACTIVO]);
    }
    
    /**
     * Obtener usuarios por rol
     */
    public function getByRol($rol)
    {
        return $this->where('rol', '=', $rol);
    }
    
    /**
     * Obtener vendedores verificados
     */
    public function getVendedoresVerificados()
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE rol = ? 
                AND verificado = 1 
                AND estado = ?
                ORDER BY nombre ASC";
        
        return $this->query($sql, [self::ROL_VENDEDOR, self::ESTADO_ACTIVO]);
    }
    
    /**
     * Obtener estadísticas de usuarios
     */
    public function getEstadisticas()
    {
        $sql = "SELECT 
                    rol,
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'activo' THEN 1 ELSE 0 END) as activos,
                    SUM(CASE WHEN verificado = 1 THEN 1 ELSE 0 END) as verificados
                FROM {$this->table}
                GROUP BY rol";
        
        return $this->query($sql);
    }
    
    /**
     * Buscar usuarios (admin)
     */
    public function buscar($termino, $rol = null, $estado = null)
    {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];
        
        if ($termino) {
            $sql .= " AND (nombre LIKE ? OR apellido LIKE ? OR email LIKE ?)";
            $params[] = "%{$termino}%";
            $params[] = "%{$termino}%";
            $params[] = "%{$termino}%";
        }
        
        if ($rol) {
            $sql .= " AND rol = ?";
            $params[] = $rol;
        }
        
        if ($estado) {
            $sql .= " AND estado = ?";
            $params[] = $estado;
        }
        
        $sql .= " ORDER BY fecha_registro DESC";
        
        return $this->query($sql, $params);
    }
    
    /**
     * Obtener perfil completo del usuario
     */
    public function getPerfilCompleto($userId)
    {
        $sql = "SELECT 
                    u.*,
                    COUNT(DISTINCT p.id) as total_publicaciones,
                    COUNT(DISTINCT CASE WHEN p.estado = 'aprobada' THEN p.id END) as publicaciones_activas,
                    COUNT(DISTINCT CASE WHEN p.estado = 'vendida' THEN p.id END) as ventas_realizadas
                FROM {$this->table} u
                LEFT JOIN publicaciones p ON p.usuario_id = u.id
                WHERE u.id = ?
                GROUP BY u.id";
        
        $result = $this->query($sql, [$userId]);
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Validar datos de registro
     */
    public function validarDatosRegistro(array $data)
    {
        $errores = [];
        
        // Validar nombre
        if (empty($data['nombre'])) {
            $errores['nombre'] = 'El nombre es obligatorio';
        }
        
        // Validar apellido
        if (empty($data['apellido'])) {
            $errores['apellido'] = 'El apellido es obligatorio';
        }
        
        // Validar email
        if (empty($data['email'])) {
            $errores['email'] = 'El email es obligatorio';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errores['email'] = 'El email no es válido';
        } elseif ($this->emailExists($data['email'])) {
            $errores['email'] = 'Este email ya está registrado';
        }
        
        // Validar password
        if (empty($data['password'])) {
            $errores['password'] = 'La contraseña es obligatoria';
        } elseif (strlen($data['password']) < 6) {
            $errores['password'] = 'La contraseña debe tener al menos 6 caracteres';
        }
        
        // Validar teléfono (opcional pero con formato)
        if (!empty($data['telefono']) && !preg_match('/^\+?[0-9\s\-]{9,15}$/', $data['telefono'])) {
            $errores['telefono'] = 'El teléfono no tiene un formato válido';
        }
        
        return $errores;
    }
}
