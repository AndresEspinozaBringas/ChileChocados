<?php
/**
 * Guest Middleware - ChileChocados
 * Verifica que el usuario NO esté autenticado (solo invitados)
 * Útil para páginas de login, registro, etc.
 */

namespace App\Middleware;

use App\Helpers\Auth;
use App\Helpers\Session;

class GuestMiddleware
{
    /**
     * Verificar que el usuario sea invitado (no autenticado)
     * Si está autenticado, redirige a su dashboard
     */
    public static function handle()
    {
        if (Auth::check()) {
            // Usuario ya está autenticado, redirigir según rol
            $redirectPath = Auth::getRedirectPath();
            
            // Mensaje flash opcional
            Session::flash('info', 'Ya has iniciado sesión');
            
            header('Location: ' . $redirectPath);
            exit;
        }
    }
    
    /**
     * Verificar guest y redirigir a URL personalizada si está autenticado
     * 
     * @param string $redirectTo URL de redirección
     */
    public static function handleWithRedirect($redirectTo = '/')
    {
        if (Auth::check()) {
            header('Location: ' . $redirectTo);
            exit;
        }
    }
}
