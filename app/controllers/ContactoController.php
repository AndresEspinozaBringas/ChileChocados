<?php
/**
 * ContactoController
 * Maneja el formulario de contacto y envío de mensajes
 */

class ContactoController {
    
    /**
     * Muestra el formulario de contacto
     */
    public function index() {
        $data = [
            'title' => 'Contacto - ChileChocados',
            'meta_description' => 'Contáctanos para dudas, sugerencias o soporte técnico',
            'success' => $_SESSION['contact_success'] ?? null,
            'error' => $_SESSION['contact_error'] ?? null
        ];
        
        // Limpiar mensajes de sesión
        unset($_SESSION['contact_success'], $_SESSION['contact_error']);
        
        require_once APP_PATH . '/views/pages/contacto.php';
    }
    
    /**
     * Procesa el envío del formulario de contacto
     */
    public function enviar() {
        // Verificar método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/contacto');
            exit;
        }
        
        // Verificar token CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['contact_error'] = 'Token de seguridad inválido';
            header('Location: ' . BASE_URL . '/contacto');
            exit;
        }
        
        // Sanitizar y validar datos
        $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $asunto = filter_input(INPUT_POST, 'asunto', FILTER_SANITIZE_STRING);
        $mensaje = filter_input(INPUT_POST, 'mensaje', FILTER_SANITIZE_STRING);
        
        // Validaciones
        $errores = [];
        
        if (empty($nombre) || strlen($nombre) < 3) {
            $errores[] = 'El nombre debe tener al menos 3 caracteres';
        }
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El email no es válido';
        }
        
        if (empty($asunto) || strlen($asunto) < 5) {
            $errores[] = 'El asunto debe tener al menos 5 caracteres';
        }
        
        if (empty($mensaje) || strlen($mensaje) < 10) {
            $errores[] = 'El mensaje debe tener al menos 10 caracteres';
        }
        
        // Si hay errores, redirigir con mensaje
        if (!empty($errores)) {
            $_SESSION['contact_error'] = implode('. ', $errores);
            header('Location: ' . BASE_URL . '/contacto');
            exit;
        }
        
        // Enviar email
        try {
            $enviado = $this->enviarEmail([
                'nombre' => $nombre,
                'email' => $email,
                'asunto' => $asunto,
                'mensaje' => $mensaje
            ]);
            
            if ($enviado) {
                $_SESSION['contact_success'] = 'Mensaje enviado correctamente. Te responderemos pronto.';
            } else {
                $_SESSION['contact_error'] = 'Error al enviar el mensaje. Intenta nuevamente.';
            }
        } catch (Exception $e) {
            $_SESSION['contact_error'] = 'Error al procesar tu solicitud. Intenta más tarde.';
            error_log('Error en contacto: ' . $e->getMessage());
        }
        
        header('Location: ' . BASE_URL . '/contacto');
        exit;
    }
    
    /**
     * Envía el email de contacto
     * 
     * @param array $datos Datos del formulario
     * @return bool True si se envió correctamente
     */
    private function enviarEmail($datos) {
        $to = 'soporte@chilechocados.cl'; // Email de destino
        $subject = '[ChileChocados] ' . $datos['asunto'];
        
        // Construir cuerpo del mensaje
        $body = "Nuevo mensaje de contacto\n\n";
        $body .= "Nombre: {$datos['nombre']}\n";
        $body .= "Email: {$datos['email']}\n";
        $body .= "Asunto: {$datos['asunto']}\n\n";
        $body .= "Mensaje:\n{$datos['mensaje']}\n\n";
        $body .= "---\n";
        $body .= "Enviado desde: " . $_SERVER['REMOTE_ADDR'] . "\n";
        $body .= "Fecha: " . date('Y-m-d H:i:s') . "\n";
        
        // Headers
        $headers = [
            'From' => 'noreply@chilechocados.cl',
            'Reply-To' => $datos['email'],
            'X-Mailer' => 'PHP/' . phpversion(),
            'Content-Type' => 'text/plain; charset=UTF-8'
        ];
        
        $header_string = '';
        foreach ($headers as $key => $value) {
            $header_string .= "$key: $value\r\n";
        }
        
        // Enviar email
        return mail($to, $subject, $body, $header_string);
    }
}
