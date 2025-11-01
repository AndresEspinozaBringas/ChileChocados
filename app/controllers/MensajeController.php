<?php
/**
 * MensajeController
 * Controlador para el sistema de mensajería interna
 */

namespace App\Controllers;

use App\Models\Mensaje;
use App\Models\Publicacion;
use App\Models\Usuario;

class MensajeController
{
    private $mensajeModel;
    private $publicacionModel;
    private $usuarioModel;
    
    public function __construct()
    {
        $this->mensajeModel = new Mensaje();
        $this->publicacionModel = new Publicacion();
        $this->usuarioModel = new Usuario();
    }
    
    /**
     * Vista principal de mensajería
     * Muestra conversaciones y chat activo
     */
    public function index()
    {
        // Verificar sesión de usuario
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        
        // Si es admin, redirigir a la vista de admin
        if (isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === 'admin') {
            header('Location: ' . BASE_URL . '/admin/mensajes');
            exit;
        }
        
        // Obtener parámetros de URL
        $publicacionId = isset($_GET['publicacion']) ? (int)$_GET['publicacion'] : null;
        $otroUsuarioId = isset($_GET['usuario']) ? (int)$_GET['usuario'] : null;
        $conversacionKey = isset($_GET['conversacion']) ? $_GET['conversacion'] : null;
        
        // Obtener conversaciones del usuario
        $conversaciones = $this->obtenerConversaciones($userId);
        
        // Determinar conversación activa
        $conversacionActiva = null;
        $mensajes = [];
        
        // Si viene desde una publicación con un usuario específico
        if ($publicacionId && $otroUsuarioId) {
            // Buscar si ya existe conversación
            foreach ($conversaciones as $conv) {
                if ($conv['publicacion_id'] == $publicacionId && $conv['otro_usuario_id'] == $otroUsuarioId) {
                    $conversacionActiva = $conv;
                    break;
                }
            }
            
            // Si no existe, crear info de nueva conversación
            if (!$conversacionActiva) {
                $publicacion = $this->publicacionModel->find($publicacionId);
                $otroUsuario = $this->usuarioModel->find($otroUsuarioId);
                
                if ($publicacion && $otroUsuario) {
                    $conversacionActiva = [
                        'publicacion_id' => $publicacionId,
                        'publicacion_titulo' => $publicacion->titulo,
                        'publicacion_foto' => $publicacion->foto_principal,
                        'otro_usuario_id' => $otroUsuarioId,
                        'otro_usuario_nombre' => $otroUsuario->nombre . ' ' . $otroUsuario->apellido,
                        'otro_usuario_foto' => $otroUsuario->foto_perfil,
                        'otro_usuario_tipo' => $otroUsuario->rol,
                        'ultimo_mensaje' => null,
                        'ultimo_mensaje_fecha' => null,
                        'mensajes_no_leidos' => 0,
                        'es_nueva' => true
                    ];
                }
            }
        }
        // Si viene con clave de conversación (publicacion_id-otro_usuario_id)
        elseif ($conversacionKey) {
            foreach ($conversaciones as $conv) {
                $key = $conv['publicacion_id'] . '-' . $conv['otro_usuario_id'];
                if ($key === $conversacionKey) {
                    $conversacionActiva = $conv;
                    break;
                }
            }
        }
        // Seleccionar primera conversación por defecto
        elseif (!empty($conversaciones)) {
            $conversacionActiva = $conversaciones[0];
        }
        
        // Obtener mensajes de la conversación activa
        if ($conversacionActiva && !isset($conversacionActiva['es_nueva'])) {
            $mensajes = $this->mensajeModel->getConversacion(
                $conversacionActiva['publicacion_id'],
                $userId,
                $conversacionActiva['otro_usuario_id']
            );
            
            // Marcar mensajes como leídos
            $this->mensajeModel->marcarConversacionLeida(
                $conversacionActiva['publicacion_id'],
                $userId
            );
            
            // Formatear fechas
            foreach ($mensajes as &$mensaje) {
                $mensaje->fecha_formateada = $this->formatearFecha($mensaje->fecha_envio);
            }
        }
        
        // Datos para la vista
        $data = [
            'title' => 'Mensajes - ChileChocados',
            'conversaciones' => $conversaciones,
            'conversacion_activa' => $conversacionActiva,
            'mensajes' => $mensajes,
            'user_id' => $userId
        ];
        
        // Renderizar vista
        require_once __DIR__ . '/../views/pages/mensajes/index.php';
    }
    
    /**
     * Enviar mensaje (AJAX endpoint)
     */
    public function enviar()
    {
        header('Content-Type: application/json');
        
        // Verificar método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }
        
        // Verificar sesión
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            return;
        }
        
        $userId = $_SESSION['user_id'];
        
        // Obtener datos del POST
        $publicacionId = isset($_POST['publicacion_id']) ? (int)$_POST['publicacion_id'] : 0;
        $destinatarioId = isset($_POST['destinatario_id']) ? (int)$_POST['destinatario_id'] : 0;
        $mensaje = isset($_POST['mensaje']) ? trim($_POST['mensaje']) : '';
        
        // Validaciones
        if (empty($mensaje)) {
            http_response_code(400);
            echo json_encode(['error' => 'El mensaje no puede estar vacío']);
            return;
        }
        
        if (!$publicacionId || !$destinatarioId) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos incompletos']);
            return;
        }
        
        // Verificar que la publicación existe
        $publicacion = $this->publicacionModel->find($publicacionId);
        if (!$publicacion) {
            http_response_code(404);
            echo json_encode(['error' => 'Publicación no encontrada']);
            return;
        }
        
        // Verificar que el usuario puede enviar mensajes sobre esta publicación
        // (debe ser el dueño o un interesado)
        if ($publicacion->usuario_id != $userId && $destinatarioId != $publicacion->usuario_id) {
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para enviar mensajes en esta publicación']);
            return;
        }
        
        // Enviar mensaje
        $mensajeId = $this->mensajeModel->enviar(
            $publicacionId,
            $userId,
            $destinatarioId,
            $mensaje
        );
        
        if ($mensajeId) {
            $response = [
                'success' => true,
                'mensaje' => [
                    'id' => $mensajeId,
                    'texto' => $mensaje,
                    'remitente_id' => $userId,
                    'fecha' => date('Y-m-d H:i:s'),
                    'fecha_formateada' => 'Justo ahora'
                ]
            ];
            echo json_encode($response);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al enviar el mensaje']);
        }
    }
    
    /**
     * Marcar conversación como leída
     */
    public function marcarLeido()
    {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            return;
        }
        
        $publicacionId = isset($_POST['publicacion_id']) ? (int)$_POST['publicacion_id'] : 0;
        
        if ($publicacionId) {
            $this->mensajeModel->marcarConversacionLeida($publicacionId, $_SESSION['user_id']);
            echo json_encode(['success' => true]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Datos incompletos']);
        }
    }
    
    /**
     * Obtener nuevos mensajes de una conversación (AJAX endpoint para polling)
     */
    public function obtenerNuevos()
    {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            return;
        }
        
        $userId = $_SESSION['user_id'];
        $publicacionId = isset($_GET['publicacion_id']) ? (int)$_GET['publicacion_id'] : 0;
        $otroUsuarioId = isset($_GET['otro_usuario_id']) ? (int)$_GET['otro_usuario_id'] : 0;
        $ultimoMensajeId = isset($_GET['ultimo_mensaje_id']) ? (int)$_GET['ultimo_mensaje_id'] : 0;
        
        if (!$publicacionId || !$otroUsuarioId) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos incompletos']);
            return;
        }
        
        // Obtener mensajes de la conversación
        $mensajes = $this->mensajeModel->getConversacion($publicacionId, $userId, $otroUsuarioId);
        
        // Filtrar solo mensajes nuevos (después del último ID conocido)
        $mensajesNuevos = array_filter($mensajes, function($msg) use ($ultimoMensajeId) {
            return $msg->id > $ultimoMensajeId;
        });
        
        // Marcar como leídos los mensajes recibidos
        if (!empty($mensajesNuevos)) {
            $this->mensajeModel->marcarConversacionLeida($publicacionId, $userId);
        }
        
        // Formatear mensajes
        $mensajesFormateados = array_map(function($msg) use ($userId) {
            return [
                'id' => $msg->id,
                'mensaje' => $msg->mensaje,
                'remitente_id' => $msg->remitente_id,
                'es_propio' => $msg->remitente_id == $userId,
                'fecha_formateada' => $this->formatearFecha($msg->fecha_envio)
            ];
        }, array_values($mensajesNuevos));
        
        echo json_encode([
            'success' => true,
            'mensajes' => $mensajesFormateados
        ]);
    }
    
    /**
     * Obtener conversaciones del usuario agrupadas por publicación y otro usuario
     */
    private function obtenerConversaciones($usuarioId)
    {
        $conversaciones = $this->mensajeModel->getConversacionesUsuario($usuarioId);
        
        // Convertir objetos a arrays y formatear
        $resultado = [];
        foreach ($conversaciones as $conv) {
            $convArray = (array) $conv;
            $convArray['ultimo_mensaje_fecha_relativa'] = $this->formatearFecha($conv->ultimo_mensaje_fecha);
            $convArray['conversacion_key'] = $conv->publicacion_id . '-' . $conv->otro_usuario_id;
            $resultado[] = $convArray;
        }
        
        return $resultado;
    }
    
    /**
     * Formatear fecha de forma relativa (hace X tiempo)
     */
    private function formatearFecha($fecha)
    {
        if (!$fecha) return '';
        
        $timestamp = strtotime($fecha);
        $diff = time() - $timestamp;
        
        if ($diff < 60) {
            return 'Justo ahora';
        } elseif ($diff < 3600) {
            $mins = floor($diff / 60);
            return 'Hace ' . $mins . ' ' . ($mins == 1 ? 'minuto' : 'minutos');
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return 'Hace ' . $hours . ' ' . ($hours == 1 ? 'hora' : 'horas');
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return 'Hace ' . $days . ' ' . ($days == 1 ? 'día' : 'días');
        } elseif ($diff < 2592000) {
            $weeks = floor($diff / 604800);
            return 'Hace ' . $weeks . ' ' . ($weeks == 1 ? 'semana' : 'semanas');
        } else {
            return date('d/m/Y', $timestamp);
        }
    }
}
