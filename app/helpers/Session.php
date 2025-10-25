<?php
/**
 * Session Helper - ChileChocados
 * Gestión segura de sesiones con soporte para flash messages
 */

namespace App\Helpers;

class Session
{
    /**
     * Iniciar sesión de forma segura
     */
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Configuración segura de sesión
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_secure', 0); // Cambiar a 1 en HTTPS
            ini_set('session.cookie_samesite', 'Lax');
            
            session_start();
            
            // Regenerar ID periódicamente (cada 30 minutos)
            if (!isset($_SESSION['CREATED'])) {
                $_SESSION['CREATED'] = time();
            } else if (time() - $_SESSION['CREATED'] > 1800) {
                self::regenerate();
                $_SESSION['CREATED'] = time();
            }
        }
    }
    
    /**
     * Establecer un valor en la sesión
     * 
     * @param string $key Clave
     * @param mixed $value Valor
     */
    public static function set($key, $value)
    {
        self::start();
        $_SESSION[$key] = $value;
    }
    
    /**
     * Obtener un valor de la sesión
     * 
     * @param string $key Clave
     * @param mixed $default Valor por defecto
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }
    
    /**
     * Verificar si existe una clave en la sesión
     * 
     * @param string $key Clave
     * @return bool
     */
    public static function has($key)
    {
        self::start();
        return isset($_SESSION[$key]);
    }
    
    /**
     * Eliminar un valor de la sesión
     * 
     * @param string $key Clave
     */
    public static function remove($key)
    {
        self::start();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
    
    /**
     * Destruir completamente la sesión
     */
    public static function destroy()
    {
        self::start();
        
        // Limpiar variables de sesión
        $_SESSION = [];
        
        // Eliminar cookie de sesión
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        
        // Destruir sesión
        session_destroy();
    }
    
    /**
     * Regenerar ID de sesión (seguridad contra session fixation)
     * 
     * @param bool $deleteOldSession Eliminar sesión anterior
     */
    public static function regenerate($deleteOldSession = true)
    {
        self::start();
        session_regenerate_id($deleteOldSession);
    }
    
    /**
     * Establecer un mensaje flash (disponible solo en la siguiente request)
     * 
     * @param string $key Clave del mensaje
     * @param mixed $value Valor del mensaje
     */
    public static function flash($key, $value)
    {
        self::start();
        $_SESSION['_flash'][$key] = $value;
    }
    
    /**
     * Obtener un mensaje flash y eliminarlo
     * 
     * @param string $key Clave del mensaje
     * @param mixed $default Valor por defecto
     * @return mixed
     */
    public static function getFlash($key, $default = null)
    {
        self::start();
        
        $value = $_SESSION['_flash'][$key] ?? $default;
        
        if (isset($_SESSION['_flash'][$key])) {
            unset($_SESSION['_flash'][$key]);
        }
        
        return $value;
    }
    
    /**
     * Verificar si existe un mensaje flash
     * 
     * @param string $key Clave
     * @return bool
     */
    public static function hasFlash($key)
    {
        self::start();
        return isset($_SESSION['_flash'][$key]);
    }
    
    /**
     * Obtener todos los mensajes flash y limpiarlos
     * 
     * @return array
     */
    public static function getAllFlash()
    {
        self::start();
        
        $flashes = $_SESSION['_flash'] ?? [];
        $_SESSION['_flash'] = [];
        
        return $flashes;
    }
    
    /**
     * Establecer token CSRF
     * 
     * @return string Token generado
     */
    public static function setCSRFToken()
    {
        self::start();
        
        if (!isset($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['_csrf_token'];
    }
    
    /**
     * Obtener token CSRF
     * 
     * @return string|null
     */
    public static function getCSRFToken()
    {
        self::start();
        return $_SESSION['_csrf_token'] ?? null;
    }
    
    /**
     * Validar token CSRF
     * 
     * @param string $token Token a validar
     * @return bool
     */
    public static function validateCSRFToken($token)
    {
        self::start();
        
        $sessionToken = self::getCSRFToken();
        
        if (!$sessionToken || !$token) {
            return false;
        }
        
        return hash_equals($sessionToken, $token);
    }
    
    /**
     * Obtener todos los datos de la sesión
     * 
     * @return array
     */
    public static function all()
    {
        self::start();
        return $_SESSION;
    }
    
    /**
     * Guardar URL anterior (útil para redirect back)
     * 
     * @param string $url URL
     */
    public static function setPreviousUrl($url)
    {
        self::set('_previous_url', $url);
    }
    
    /**
     * Obtener URL anterior
     * 
     * @param string $default URL por defecto
     * @return string
     */
    public static function getPreviousUrl($default = '/')
    {
        return self::get('_previous_url', $default);
    }
}
