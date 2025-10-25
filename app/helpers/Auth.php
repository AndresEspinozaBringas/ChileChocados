<?php
/**
 * Auth Helper - ChileChocados
 * Autenticación y autorización de usuarios
 */

namespace App\Helpers;

use App\Models\Usuario;

class Auth
{
    /**
     * Intentar autenticar usuario
     * 
     * @param string $email Email
     * @param string $password Contraseña
     * @param bool $remember Recordar sesión
     * @return bool
     */
    public static function attempt($email, $password, $remember = false)
    {
        $usuario = new Usuario();
        $user = $usuario->findByEmail($email);
        
        if (!$user) {
            return false;
        }
        
        // Verificar contraseña
        if (!password_verify($password, $user['password'])) {
            return false;
        }
        
        // Verificar que la cuenta esté activa
        if ($user['estado'] !== 'activo') {
            return false;
        }
        
        // Iniciar sesión
        self::login($user, $remember);
        
        // Actualizar último acceso
        $usuario->update($user['id'], [
            'ultimo_acceso' => date('Y-m-d H:i:s')
        ]);
        
        return true;
    }
    
    /**
     * Iniciar sesión del usuario
     * 
     * @param array $user Datos del usuario
     * @param bool $remember Recordar sesión
     */
    public static function login($user, $remember = false)
    {
        Session::start();
        Session::regenerate();
        
        // Guardar datos del usuario en sesión
        Session::set('user_id', $user['id']);
        Session::set('user_email', $user['email']);
        Session::set('user_nombre', $user['nombre']);
        Session::set('user_apellido', $user['apellido'] ?? '');
        Session::set('user_rol', $user['rol']);
        Session::set('user_avatar', $user['foto_perfil'] ?? null);
        Session::set('user_verified', $user['verificado']);
        Session::set('authenticated', true);
        
        // Cookie "Remember me" (7 días)
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            Session::set('remember_token', $token);
            
            setcookie(
                'remember_token',
                $token,
                time() + (7 * 24 * 60 * 60), // 7 días
                '/',
                '',
                false, // Cambiar a true en HTTPS
                true   // HttpOnly
            );
        }
    }
    
    /**
     * Cerrar sesión del usuario
     */
    public static function logout()
    {
        // Eliminar cookie "Remember me"
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
        }
        
        Session::destroy();
    }
    
    /**
     * Verificar si el usuario está autenticado
     * 
     * @return bool
     */
    public static function check()
    {
        Session::start();
        return Session::get('authenticated', false) === true;
    }
    
    /**
     * Verificar si el usuario es invitado (no autenticado)
     * 
     * @return bool
     */
    public static function guest()
    {
        return !self::check();
    }
    
    /**
     * Obtener ID del usuario autenticado
     * 
     * @return int|null
     */
    public static function id()
    {
        Session::start();
        return Session::get('user_id');
    }
    
    /**
     * Obtener usuario autenticado
     * 
     * @return array|null
     */
    public static function user()
    {
        if (!self::check()) {
            return null;
        }
        
        Session::start();
        
        return [
            'id' => Session::get('user_id'),
            'email' => Session::get('user_email'),
            'nombre' => Session::get('user_nombre'),
            'apellido' => Session::get('user_apellido'),
            'rol' => Session::get('user_rol'),
            'avatar' => Session::get('user_avatar'),
            'verificado' => Session::get('user_verified')
        ];
    }
    
    /**
     * Verificar si el usuario tiene un rol específico
     * 
     * @param string|array $roles Rol(es) a verificar
     * @return bool
     */
    public static function hasRole($roles)
    {
        if (!self::check()) {
            return false;
        }
        
        $userRole = Session::get('user_rol');
        
        if (is_array($roles)) {
            return in_array($userRole, $roles);
        }
        
        return $userRole === $roles;
    }
    
    /**
     * Verificar si el usuario es administrador
     * 
     * @return bool
     */
    public static function isAdmin()
    {
        return self::hasRole('admin');
    }
    
    /**
     * Verificar si el usuario es vendedor
     * 
     * @return bool
     */
    public static function isVendedor()
    {
        return self::hasRole('vendedor');
    }
    
    /**
     * Verificar si el usuario es comprador
     * 
     * @return bool
     */
    public static function isComprador()
    {
        return self::hasRole('comprador');
    }
    
    /**
     * Verificar si el email del usuario está verificado
     * 
     * @return bool
     */
    public static function isVerified()
    {
        if (!self::check()) {
            return false;
        }
        
        return Session::get('user_verified', 0) == 1;
    }
    
    /**
     * Verificar si el usuario puede realizar una acción
     * 
     * @param string $action Acción a verificar
     * @param mixed $resource Recurso opcional
     * @return bool
     */
    public static function can($action, $resource = null)
    {
        if (!self::check()) {
            return false;
        }
        
        $role = Session::get('user_rol');
        
        // Permisos por rol
        $permissions = [
            'admin' => [
                'manage_users',
                'manage_publicaciones',
                'manage_categorias',
                'manage_mensajes',
                'view_reports',
                'manage_payments',
                'moderate_content'
            ],
            'vendedor' => [
                'create_publicacion',
                'edit_own_publicacion',
                'delete_own_publicacion',
                'view_own_messages',
                'reply_messages',
                'contract_destacado'
            ],
            'comprador' => [
                'view_publicaciones',
                'send_messages',
                'save_favorites',
                'view_own_profile'
            ]
        ];
        
        if (!isset($permissions[$role])) {
            return false;
        }
        
        return in_array($action, $permissions[$role]);
    }
    
    /**
     * Obtener ruta de redirección según rol
     * 
     * @return string
     */
    public static function getRedirectPath()
    {
        if (!self::check()) {
            return '/login';
        }
        
        $role = Session::get('user_rol');
        
        $redirects = [
            'admin' => '/admin/dashboard',
            'vendedor' => '/publicaciones/mis-publicaciones',
            'comprador' => '/publicaciones'
        ];
        
        return $redirects[$role] ?? '/';
    }
    
    /**
     * Generar token de verificación de email
     * 
     * @return string
     */
    public static function generateVerificationToken()
    {
        return bin2hex(random_bytes(32));
    }
    
    /**
     * Generar token de recuperación de contraseña
     * 
     * @return string
     */
    public static function generatePasswordResetToken()
    {
        return bin2hex(random_bytes(32));
    }
    
    /**
     * Verificar token de recuperación de contraseña
     * 
     * @param string $token Token a verificar
     * @return array|null Usuario si el token es válido
     */
    public static function verifyPasswordResetToken($token)
    {
        $usuario = new Usuario();
        
        // Buscar usuario con el token
        $user = $usuario->first('token_recuperacion', '=', $token);
        
        if (!$user) {
            return null;
        }
        
        // Verificar que el token no haya expirado (1 hora)
        if (strtotime($user['token_expira']) < time()) {
            return null;
        }
        
        return $user;
    }
    
    /**
     * Actualizar contraseña del usuario
     * 
     * @param int $userId ID del usuario
     * @param string $newPassword Nueva contraseña
     * @return bool
     */
    public static function updatePassword($userId, $newPassword)
    {
        $usuario = new Usuario();
        return $usuario->updatePassword($userId, $newPassword);
    }
    
    /**
     * Verificar intentos de login fallidos (rate limiting)
     * 
     * @param string $email Email
     * @return bool True si puede intentar, False si está bloqueado
     */
    public static function canAttemptLogin($email)
    {
        Session::start();
        
        $key = 'login_attempts_' . md5($email);
        $attempts = Session::get($key, []);
        
        // Limpiar intentos antiguos (más de 15 minutos)
        $attempts = array_filter($attempts, function($timestamp) {
            return $timestamp > (time() - 900); // 15 minutos
        });
        
        // Máximo 5 intentos en 15 minutos
        if (count($attempts) >= 5) {
            return false;
        }
        
        // Registrar intento
        $attempts[] = time();
        Session::set($key, $attempts);
        
        return true;
    }
    
    /**
     * Limpiar intentos de login después de login exitoso
     * 
     * @param string $email Email
     */
    public static function clearLoginAttempts($email)
    {
        $key = 'login_attempts_' . md5($email);
        Session::remove($key);
    }
    
    /**
     * Requerir autenticación (helper para uso en vistas)
     * Redirige a login si no está autenticado
     */
    public static function require()
    {
        if (!self::check()) {
            Session::flash('error', 'Debes iniciar sesión para acceder a esta página');
            header('Location: /login');
            exit;
        }
    }
    
    /**
     * Requerir rol específico
     * 
     * @param string|array $roles Rol(es) requerido(s)
     */
    public static function requireRole($roles)
    {
        self::require();
        
        if (!self::hasRole($roles)) {
            Session::flash('error', 'No tienes permisos para acceder a esta página');
            header('Location: ' . self::getRedirectPath());
            exit;
        }
    }
    
    /**
     * Requerir verificación de email
     */
    public static function requireVerified()
    {
        self::require();
        
        if (!self::isVerified()) {
            Session::flash('warning', 'Debes verificar tu email para acceder a esta función');
            header('Location: /perfil');
            exit;
        }
    }
}
