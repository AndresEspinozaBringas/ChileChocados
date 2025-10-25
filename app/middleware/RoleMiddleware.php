<?php
/**
 * Role Middleware - ChileChocados
 * Verifica roles y permisos específicos del usuario
 */

namespace App\Middleware;

use App\Helpers\Auth;
use App\Helpers\Session;

class RoleMiddleware
{
    /**
     * Verificar que el usuario tenga un rol específico
     * 
     * @param string|array $allowedRoles Rol(es) permitido(s)
     * @param string $redirectOnFail URL de redirección si falla
     */
    public static function handle($allowedRoles, $redirectOnFail = null)
    {
        // Primero verificar que esté autenticado
        if (!Auth::check()) {
            Session::flash('error', 'Debes iniciar sesión para acceder');
            header('Location: /login');
            exit;
        }
        
        // Verificar rol
        if (!Auth::hasRole($allowedRoles)) {
            Session::flash('error', 'No tienes permisos para acceder a esta página');
            
            // Redirigir
            $redirectUrl = $redirectOnFail ?? Auth::getRedirectPath();
            header('Location: ' . $redirectUrl);
            exit;
        }
    }
    
    /**
     * Verificar permiso específico
     * 
     * @param string $permission Permiso requerido
     * @param string $redirectOnFail URL de redirección
     */
    public static function handlePermission($permission, $redirectOnFail = null)
    {
        // Verificar autenticación
        if (!Auth::check()) {
            Session::flash('error', 'Debes iniciar sesión');
            header('Location: /login');
            exit;
        }
        
        // Verificar permiso
        if (!Auth::can($permission)) {
            Session::flash('error', 'No tienes permisos para realizar esta acción');
            
            $redirectUrl = $redirectOnFail ?? Auth::getRedirectPath();
            header('Location: ' . $redirectUrl);
            exit;
        }
    }
    
    /**
     * Verificar que el usuario sea propietario del recurso
     * 
     * @param int $resourceUserId ID del usuario propietario
     * @param string $errorMessage Mensaje de error personalizado
     */
    public static function handleOwnership($resourceUserId, $errorMessage = null)
    {
        // Verificar autenticación
        if (!Auth::check()) {
            Session::flash('error', 'Debes iniciar sesión');
            header('Location: /login');
            exit;
        }
        
        $currentUserId = Auth::id();
        
        // Admin puede acceder a todo
        if (Auth::isAdmin()) {
            return;
        }
        
        // Verificar propiedad
        if ($currentUserId != $resourceUserId) {
            $message = $errorMessage ?? 'No tienes permisos para acceder a este recurso';
            Session::flash('error', $message);
            
            header('Location: ' . Auth::getRedirectPath());
            exit;
        }
    }
    
    /**
     * Solo administradores
     */
    public static function handleAdmin()
    {
        self::handle('admin', '/');
    }
    
    /**
     * Solo vendedores
     */
    public static function handleVendedor()
    {
        self::handle('vendedor', '/');
    }
    
    /**
     * Solo compradores
     */
    public static function handleComprador()
    {
        self::handle('comprador', '/');
    }
    
    /**
     * Vendedores y administradores
     */
    public static function handleVendedorOrAdmin()
    {
        self::handle(['vendedor', 'admin'], '/');
    }
    
    /**
     * Cualquier usuario autenticado excepto compradores
     */
    public static function handleNotComprador()
    {
        if (!Auth::check()) {
            Session::flash('error', 'Debes iniciar sesión');
            header('Location: /login');
            exit;
        }
        
        if (Auth::isComprador()) {
            Session::flash('error', 'Los compradores no pueden acceder a esta función');
            header('Location: '/');
            exit;
        }
    }
    
    /**
     * Verificar múltiples condiciones
     * 
     * @param array $conditions Condiciones a verificar
     * Ejemplo: ['authenticated' => true, 'role' => 'admin', 'verified' => true]
     */
    public static function handleMultiple($conditions)
    {
        // Verificar autenticación
        if (isset($conditions['authenticated']) && $conditions['authenticated']) {
            if (!Auth::check()) {
                Session::flash('error', 'Debes iniciar sesión');
                header('Location: /login');
                exit;
            }
        }
        
        // Verificar rol
        if (isset($conditions['role'])) {
            if (!Auth::hasRole($conditions['role'])) {
                Session::flash('error', 'No tienes el rol necesario');
                header('Location: ' . Auth::getRedirectPath());
                exit;
            }
        }
        
        // Verificar email verificado
        if (isset($conditions['verified']) && $conditions['verified']) {
            if (!Auth::isVerified()) {
                Session::flash('warning', 'Debes verificar tu email');
                header('Location: /perfil');
                exit;
            }
        }
        
        // Verificar permiso
        if (isset($conditions['permission'])) {
            if (!Auth::can($conditions['permission'])) {
                Session::flash('error', 'No tienes permisos para esta acción');
                header('Location: ' . Auth::getRedirectPath());
                exit;
            }
        }
    }
}
