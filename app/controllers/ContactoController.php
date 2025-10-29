<?php
/**
 * ContactoController
 * Controlador para el formulario de contacto
 */

namespace App\Controllers;

class ContactoController
{
    /**
     * Mostrar formulario de contacto
     * Ruta: GET /contacto
     */
    public function index()
    {
        $pageTitle = 'Contacto - ChileChocados';
        
        // Cargar vista
        require_once __DIR__ . '/../views/pages/contacto.php';
    }
    
    /**
     * Enviar mensaje de contacto
     * Ruta: POST /contacto/enviar
     */
    public function enviar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/contacto');
            exit;
        }
        
        // Recoger datos del formulario
        $nombre = trim($_POST['nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $asunto = trim($_POST['asunto'] ?? '');
        $mensaje = trim($_POST['mensaje'] ?? '');
        
        // Validaciones básicas
        $errors = [];
        
        if (empty($nombre)) {
            $errors[] = 'El nombre es obligatorio';
        }
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email inválido';
        }
        
        if (empty($mensaje)) {
            $errors[] = 'El mensaje es obligatorio';
        }
        
        if (!empty($errors)) {
            setFlash('error', implode('<br>', $errors));
            header('Location: ' . BASE_URL . '/contacto');
            exit;
        }
        
        // TODO: Enviar email en futuras etapas
        // Por ahora solo guardamos en log
        $logMessage = "CONTACTO - Nombre: $nombre, Email: $email, Asunto: $asunto, Mensaje: $mensaje";
        error_log($logMessage);
        
        setFlash('success', '¡Mensaje enviado correctamente! Te contactaremos pronto.');
        header('Location: ' . BASE_URL . '/contacto');
        exit;
    }
}
