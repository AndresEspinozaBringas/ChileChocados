<?php
/**
 * Auth Middleware - ChileChocados
 * Verifica que el usuario esté autenticado
 */

namespace App\Middleware;

use App\Helpers\Auth;
use App\Helpers\Session;

class AuthMiddleware
{
    /**
     * Verificar que el usuario esté autenticado
     * Si no lo está, redirige a login
     */
    public static function handle()
    {
        if (!Auth::check()) {
            // Guardar URL intentada para redirigir después del login
            $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
            Session::set('intended_url', $requestUri);
            
            // Mensaje flash
            Session::flash('error', 'Debes iniciar sesión para acceder a esta página');
            
            // Redirigir a login
            header('Location: /login');
            exit;
        }
    }
    
    /**
     * Verificar autenticación y email verificado
     */
    public static function handleVerified()
    {
        // Primero verificar autenticación
        self::handle();
        
        // Luego verificar email
        if (!Auth::isVerified()) {
            Session::flash('warning', 'Debes verificar tu email para acceder a esta función');
            header('Location: /perfil');
            exit;
        }
    }
    
    /**
     * Verificar autenticación y rol específico
     * 
     * @param string|array $roles Rol(es) permitido(s)
     */
    public static function handleRole($roles)
    {
        // Primero verificar autenticación
        self::handle();
        
        // Verificar rol
        if (!Auth::hasRole($roles)) {
            Session::flash('error', 'No tienes permisos para acceder a esta página');
            
            // Redirigir según rol del usuario
            $redirectPath = Auth::getRedirectPath();
            header('Location: ' . $redirectPath);
            exit;
        }
    }
    
    /**
     * Verificar que sea admin
     */
    public static function handleAdmin()
    {
        self::handleRole('admin');
    }
    
    /**
     * Verificar que sea vendedor
     */
    public static function handleVendedor()
    {
        self::handleRole('vendedor');
    }
    
    /**
     * Verificar que sea comprador
     */
    public static function handleComprador()
    {
        self::handleRole('comprador');
    }
    
    /**
     * Verificar que sea vendedor o admin
     */
    public static function handleVendedorOrAdmin()
    {
        self::handleRole(['vendedor', 'admin']);
    }
}
