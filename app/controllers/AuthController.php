<?php
/**
 * AuthController - ChileChocados
 * Controlador de autenticación y gestión de usuarios
 */

namespace App\Controllers;

use App\Models\Usuario;
use App\Helpers\Session;
use App\Helpers\Auth;
use App\Helpers\Email;

class AuthController
{
    private $usuarioModel;
    
    public function __construct()
    {
        $this->usuarioModel = new Usuario();
    }
    
    /**
     * GET /registro
     * Mostrar formulario de registro
     */
    public function register()
    {
        // Si ya está autenticado, redirigir
        if (Auth::check()) {
            header('Location: /');
            exit;
        }
        
        require_once __DIR__ . '/../views/pages/auth/registro.php';
    }
    
    /**
     * POST /registro
     * Procesar registro de usuario
     */
    public function processRegister()
    {
        // Verificar método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /registro');
            exit;
        }
        
        // Recoger datos del formulario
        $data = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'apellido' => trim($_POST['apellido'] ?? ''),
            'rut' => trim($_POST['rut'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? '',
            'rol' => $_POST['rol'] ?? 'comprador', // Por defecto comprador
            'terminos' => isset($_POST['terminos'])
        ];
        
        // Validaciones
        $errors = [];
        
        if (empty($data['nombre']) || empty($data['apellido'])) {
            $errors[] = 'Nombre y apellido son obligatorios';
        }
        
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email inválido';
        }
        
        if (empty($data['password']) || strlen($data['password']) < 8) {
            $errors[] = 'La contraseña debe tener al menos 8 caracteres';
        }
        
        if ($data['password'] !== $data['password_confirm']) {
            $errors[] = 'Las contraseñas no coinciden';
        }
        
        if (!$data['terminos']) {
            $errors[] = 'Debes aceptar los términos y condiciones';
        }
        
        // Verificar si el email ya existe
        if ($this->usuarioModel->findByEmail($data['email'])) {
            $errors[] = 'El email ya está registrado';
        }
        
        // Si hay errores, volver al formulario
        if (!empty($errors)) {
            Session::flash('error', implode('<br>', $errors));
            header('Location: /registro');
            exit;
        }
        
        // Hash de contraseña
        $data['password'] = password_hash($data['password'], PASSWORD_ARGON2ID);
        
        // Generar token de verificación
        $verificationToken = bin2hex(random_bytes(32));
        $data['token_recuperacion'] = $verificationToken;
        $data['token_expira'] = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        // Crear usuario
        $userId = $this->usuarioModel->create($data);
        
        if (!$userId) {
            Session::flash('error', 'Error al crear usuario. Inténtalo nuevamente');
            header('Location: /registro');
            exit;
        }
        
        // Enviar email de verificación
        $verifyLink = getenv('APP_URL') . '/verificar-email/' . $verificationToken;
        
        Email::send(
            $data['email'],
            'Verifica tu cuenta en ChileChocados',
            'verify-email',
            [
                'nombre' => $data['nombre'],
                'verify_link' => $verifyLink
            ]
        );
        
        Session::flash('success', '¡Registro exitoso! Te hemos enviado un email de verificación');
        header('Location: /login');
        exit;
    }
    
    /**
     * GET /verificar-email/{token}
     * Verificar email del usuario
     */
    public function verifyEmail($token)
    {
        $usuario = $this->usuarioModel->verifyRecoveryToken($token);
        
        if (!$usuario) {
            Session::flash('error', 'Token de verificación inválido o expirado');
            header('Location: /login');
            exit;
        }
        
        // Actualizar usuario como verificado
        $this->usuarioModel->update($usuario->id, [
            'verificado' => 1,
            'token_recuperacion' => null,
            'token_expira' => null
        ]);
        
        Session::flash('success', '¡Email verificado exitosamente! Ya puedes iniciar sesión');
        header('Location: /login');
        exit;
    }
    
    /**
     * GET /login
     * Mostrar formulario de login
     */
    public function login()
    {
        // Si ya está autenticado, redirigir
        if (Auth::check()) {
            $this->redirectByRole();
        }
        
        require_once __DIR__ . '/../views/pages/auth/login.php';
    }
    
    /**
     * POST /login
     * Procesar autenticación
     */
    public function authenticate()
    {
        // Verificar método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /login');
            exit;
        }
        
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        
        // Validaciones básicas
        if (empty($email) || empty($password)) {
            Session::flash('error', 'Email y contraseña son obligatorios');
            header('Location: /login');
            exit;
        }
        
        // Rate limiting - Verificar intentos fallidos
        $attempts = Session::get('login_attempts', 0);
        $lastAttempt = Session::get('last_attempt_time', 0);
        
        if ($attempts >= 5) {
            $timeElapsed = time() - $lastAttempt;
            if ($timeElapsed < 900) { // 15 minutos
                $minutesLeft = ceil((900 - $timeElapsed) / 60);
                Session::flash('error', "Demasiados intentos fallidos. Intenta de nuevo en {$minutesLeft} minutos");
                header('Location: /login');
                exit;
            } else {
                // Resetear intentos después de 15 minutos
                Session::remove('login_attempts');
                Session::remove('last_attempt_time');
            }
        }
        
        // Buscar usuario por email
        $usuario = $this->usuarioModel->findByEmail($email);
        
        if (!$usuario) {
            $this->handleFailedLogin();
            header('Location: /login');
            exit;
        }
        
        // Verificar contraseña
        if (!password_verify($password, $usuario->password)) {
            $this->handleFailedLogin();
            header('Location: /login');
            exit;
        }
        
        // Verificar estado de la cuenta
        if ($usuario->estado === 'suspendido') {
            Session::flash('error', 'Tu cuenta ha sido suspendida. Contacta al administrador');
            header('Location: /login');
            exit;
        }
        
        // Login exitoso - Limpiar intentos
        Session::remove('login_attempts');
        Session::remove('last_attempt_time');
        
        // Crear sesión
        Auth::login([
            'id' => $usuario->id,
            'nombre' => $usuario->nombre,
            'apellido' => $usuario->apellido,
            'email' => $usuario->email,
            'rol' => $usuario->rol,
            'verificado' => $usuario->verificado,
            'foto_perfil' => $usuario->foto_perfil
        ]);
        
        // Actualizar última conexión
        $this->usuarioModel->updateLastConnection($usuario->id);
        
        // Configurar "Recordarme"
        if ($remember) {
            setcookie('remember_token', hash('sha256', $usuario->email . $usuario->id), time() + (86400 * 7), '/'); // 7 días
        }
        
        Session::flash('success', '¡Bienvenido de vuelta, ' . $usuario->nombre . '!');
        
        // Redirigir según rol
        $this->redirectByRole();
    }
    
    /**
     * POST /logout
     * Cerrar sesión
     */
    public function logout()
    {
        Auth::logout();
        Session::flash('success', 'Sesión cerrada exitosamente');
        header('Location: /');
        exit;
    }
    
    /**
     * GET /recuperar-contrasena
     * Mostrar formulario de recuperación
     */
    public function forgotPassword()
    {
        if (Auth::check()) {
            header('Location: /');
            exit;
        }
        
        require_once __DIR__ . '/../views/pages/auth/forgot-password.php';
    }
    
    /**
     * POST /recuperar-contrasena
     * Enviar enlace de recuperación
     */
    public function sendResetLink()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /recuperar-contrasena');
            exit;
        }
        
        $email = trim($_POST['email'] ?? '');
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Email inválido');
            header('Location: /recuperar-contrasena');
            exit;
        }
        
        // Buscar usuario
        $usuario = $this->usuarioModel->findByEmail($email);
        
        if ($usuario) {
            // Generar token de recuperación
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $this->usuarioModel->update($usuario->id, [
                'token_recuperacion' => $token,
                'token_expira' => $expiry
            ]);
            
            // Enviar email
            $resetLink = getenv('APP_URL') . '/reset-password/' . $token;
            
            Email::send(
                $email,
                'Recupera tu contraseña - ChileChocados',
                'reset-password',
                [
                    'nombre' => $usuario->nombre,
                    'reset_link' => $resetLink
                ]
            );
        }
        
        // Siempre mostrar el mismo mensaje por seguridad
        Session::flash('success', 'Si el email existe, recibirás un enlace de recuperación');
        header('Location: /login');
        exit;
    }
    
    /**
     * GET /reset-password/{token}
     * Mostrar formulario de nueva contraseña
     */
    public function resetPassword($token)
    {
        if (Auth::check()) {
            header('Location: /');
            exit;
        }
        
        // Verificar token
        $usuario = $this->usuarioModel->verifyRecoveryToken($token);
        
        if (!$usuario) {
            Session::flash('error', 'Token de recuperación inválido o expirado');
            header('Location: /recuperar-contrasena');
            exit;
        }
        
        require_once __DIR__ . '/../views/pages/auth/reset-password.php';
    }
    
    /**
     * POST /reset-password/{token}
     * Actualizar contraseña
     */
    public function updatePassword($token)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /reset-password/' . $token);
            exit;
        }
        
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        
        // Validaciones
        if (empty($password) || empty($passwordConfirm)) {
            Session::flash('error', 'Todos los campos son obligatorios');
            header('Location: /reset-password/' . $token);
            exit;
        }
        
        if ($password !== $passwordConfirm) {
            Session::flash('error', 'Las contraseñas no coinciden');
            header('Location: /reset-password/' . $token);
            exit;
        }
        
        if (strlen($password) < 8) {
            Session::flash('error', 'La contraseña debe tener al menos 8 caracteres');
            header('Location: /reset-password/' . $token);
            exit;
        }
        
        // Verificar token
        $usuario = $this->usuarioModel->verifyRecoveryToken($token);
        
        if (!$usuario) {
            Session::flash('error', 'Token de recuperación inválido o expirado');
            header('Location: /recuperar-contrasena');
            exit;
        }
        
        // Actualizar contraseña
        $this->usuarioModel->updatePassword($usuario->id, $password);
        $this->usuarioModel->clearRecoveryToken($usuario->id);
        
        Session::flash('success', 'Contraseña actualizada exitosamente. Ya puedes iniciar sesión');
        header('Location: /login');
        exit;
    }
    
    /**
     * Manejar intento de login fallido
     */
    private function handleFailedLogin()
    {
        $attempts = Session::get('login_attempts', 0);
        $attempts++;
        
        Session::set('login_attempts', $attempts);
        Session::set('last_attempt_time', time());
        
        $remaining = 5 - $attempts;
        
        if ($remaining > 0) {
            Session::flash('error', "Credenciales incorrectas. Te quedan {$remaining} intentos");
        } else {
            Session::flash('error', 'Demasiados intentos fallidos. Tu cuenta ha sido bloqueada temporalmente por 15 minutos');
        }
    }
    
    /**
     * Redirigir según rol del usuario
     * MODIFICADO: Vendedores ahora van al home (/)
     */
    private function redirectByRole()
    {
        $user = Auth::user();
        
        switch ($user['rol']) {
            case 'admin':
                header('Location: /admin');
                break;
            case 'vendedor':
                header('Location: /'); // CAMBIO: ahora redirige al home
                break;
            case 'comprador':
            default:
                header('Location: /');
                break;
        }
        exit;
    }
}