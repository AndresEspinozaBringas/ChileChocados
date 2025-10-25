<?php
/**
 * AuthController - ChileChocados
 * Controlador de autenticación completo
 * 
 * @author Andrés Espinoza
 * @version 1.0
 */

namespace App\Controllers;

use App\Helpers\Session;
use App\Helpers\Email;
use App\Helpers\Auth;
use App\Helpers\Validator;
use App\Models\Usuario;

class AuthController
{
    private $usuarioModel;
    
    public function __construct()
    {
        $this->usuarioModel = new Usuario();
        Session::start();
    }
    
    /**
     * GET /registro
     * Mostrar formulario de registro
     */
    public function register()
    {
        // Solo invitados pueden registrarse
        if (Auth::check()) {
            Session::flash('info', 'Ya tienes una sesión activa');
            header('Location: /');
            exit;
        }
        
        require_once __DIR__ . '/../views/pages/auth/register.php';
    }
    
    /**
     * POST /registro
     * Procesar registro de nuevo usuario
     */
    public function processRegister()
    {
        // Verificar método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /registro');
            exit;
        }
        
        // Obtener datos del formulario
        $data = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'apellido' => trim($_POST['apellido'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? '',
            'telefono' => trim($_POST['telefono'] ?? ''),
            'rol' => $_POST['rol'] ?? 'comprador'
        ];
        
        // Validaciones
        $validator = new Validator($data);
        
        $validator->required('nombre', 'El nombre es obligatorio')
                  ->min('nombre', 2, 'El nombre debe tener al menos 2 caracteres')
                  ->max('nombre', 100, 'El nombre no puede exceder 100 caracteres');
        
        $validator->required('apellido', 'El apellido es obligatorio')
                  ->min('apellido', 2, 'El apellido debe tener al menos 2 caracteres');
        
        $validator->required('email', 'El email es obligatorio')
                  ->email('email', 'El email no es válido');
        
        $validator->required('password', 'La contraseña es obligatoria')
                  ->min('password', 8, 'La contraseña debe tener al menos 8 caracteres')
                  ->passwordStrength('password', 'La contraseña debe contener mayúsculas, minúsculas y números');
        
        // Verificar coincidencia de contraseñas
        if ($data['password'] !== $data['password_confirm']) {
            $validator->addError('password_confirm', 'Las contraseñas no coinciden');
        }
        
        // Validar teléfono chileno si se proporciona
        if (!empty($data['telefono'])) {
            $validator->phoneChile('telefono', 'El formato del teléfono no es válido');
        }
        
        // Si hay errores, volver al formulario
        if ($validator->fails()) {
            Session::flash('errors', $validator->getErrors());
            Session::flash('old', $data);
            header('Location: /registro');
            exit;
        }
        
        // Verificar si el email ya existe
        if ($this->usuarioModel->emailExists($data['email'])) {
            Session::flash('error', 'Este email ya está registrado');
            Session::flash('old', $data);
            header('Location: /registro');
            exit;
        }
        
        // Generar token de verificación
        $verificationToken = bin2hex(random_bytes(32));
        
        // Crear usuario
        $userData = [
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_ARGON2ID),
            'telefono' => $data['telefono'],
            'rol' => $data['rol'],
            'estado' => 'activo',
            'verificado' => 0,
            'token_recuperacion' => $verificationToken,
            'token_expira' => date('Y-m-d H:i:s', strtotime('+24 hours'))
        ];
        
        $userId = $this->usuarioModel->create($userData);
        
        if (!$userId) {
            Session::flash('error', 'Error al crear el usuario. Inténtalo nuevamente');
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
        
        if (empty($email)) {
            Session::flash('error', 'El email es obligatorio');
            header('Location: /recuperar-contrasena');
            exit;
        }
        
        $usuario = $this->usuarioModel->findByEmail($email);
        
        // Por seguridad, siempre mostrar mensaje de éxito
        // (no revelar si el email existe o no)
        if ($usuario) {
            // Generar token de recuperación
            $token = $this->usuarioModel->generateRecoveryToken($usuario->id);
            
            // Enviar email
            $resetLink = getenv('APP_URL') . '/reset-password/' . $token;
            
            Email::send(
                $email,
                'Recuperación de contraseña - ChileChocados',
                'reset-password',
                [
                    'nombre' => $usuario->nombre,
                    'reset_link' => $resetLink
                ]
            );
        }
        
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
        $usuario = $this->usuarioModel->verifyRecoveryToken($token);
        
        if (!$usuario) {
            Session::flash('error', 'Token de recuperación inválido o expirado');
            header('Location: /recuperar-contrasena');
            exit;
        }
        
        require_once __DIR__ . '/../views/pages/auth/reset-password.php';
    }
    
    /**
     * POST /reset-password
     * Actualizar contraseña
     */
    public function updatePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /recuperar-contrasena');
            exit;
        }
        
        $token = $_POST['token'] ?? '';
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
     */
    private function redirectByRole()
    {
        $user = Auth::user();
        
        switch ($user['rol']) {
            case 'admin':
                header('Location: /admin');
                break;
            case 'vendedor':
                header('Location: /panel/vendedor');
                break;
            case 'comprador':
            default:
                header('Location: /');
                break;
        }
        exit;
    }
}
