<?php
/**
 * MensajeController
 * Controlador para el sistema de mensajería interna
 * 
 * NOTA: Implementación con datos MOCK (sin base de datos)
 * Para desarrollo y testing de UI/UX
 */

namespace App\Controllers;

class MensajeController
{
    /**
     * Vista principal de mensajería
     * Muestra conversaciones y chat activo
     */
    public function index()
    {
        // Verificar sesión de usuario (simulada)
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = 1; // Usuario mock para desarrollo
            $_SESSION['user_nombre'] = 'Juan Pérez';
            $_SESSION['user_role'] = 'vendedor'; // Por defecto vendedor
        }
        
        $userId = $_SESSION['user_id'];
        $userRole = $_SESSION['user_role'] ?? 'vendedor';
        
        // Obtener ID de publicación desde URL (si viene desde "Contactar vendedor")
        $publicacionId = isset($_GET['publicacion']) ? (int)$_GET['publicacion'] : null;
        $conversacionActiva = isset($_GET['conversacion']) ? (int)$_GET['conversacion'] : null;
        
        // DATOS MOCK: Conversaciones según el rol del usuario
        // Vendedor: solo sus publicaciones
        // Admin: todas las conversaciones del sistema (se maneja en admin)
        $conversaciones = $this->getMockConversaciones($userId, $userRole);
        
        // Si viene desde una publicación, buscar/crear conversación
        if ($publicacionId && !$conversacionActiva) {
            foreach ($conversaciones as $conv) {
                if ($conv['publicacion_id'] == $publicacionId) {
                    $conversacionActiva = $conv['id'];
                    break;
                }
            }
            
            // Si no existe conversación, crear una nueva (mock)
            if (!$conversacionActiva) {
                $conversacionActiva = count($conversaciones) + 1;
            }
        }
        
        // Seleccionar primera conversación si no hay una activa
        if (!$conversacionActiva && !empty($conversaciones)) {
            $conversacionActiva = $conversaciones[0]['id'];
        }
        
        // Obtener mensajes de la conversación activa
        $mensajes = $conversacionActiva ? $this->getMockMensajes($conversacionActiva, $userId) : [];
        
        // Obtener info de la conversación activa
        $conversacionInfo = null;
        if ($conversacionActiva) {
            foreach ($conversaciones as $conv) {
                if ($conv['id'] == $conversacionActiva) {
                    $conversacionInfo = $conv;
                    break;
                }
            }
        }
        
        // Datos para la vista
        $data = [
            'title' => 'Mensajes - ChileChocados',
            'conversaciones' => $conversaciones,
            'conversacion_activa_id' => $conversacionActiva,
            'conversacion_info' => $conversacionInfo,
            'mensajes' => $mensajes,
            'user_id' => $userId
        ];
        
        // Renderizar vista
        require_once __DIR__ . '/../views/pages/mensajes/index.php';
    }
    
    /**
     * Enviar mensaje (AJAX endpoint simulado)
     */
    public function enviar()
    {
        // Verificar método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }
        
        // Simulación de envío exitoso
        $mensaje = $_POST['mensaje'] ?? '';
        $conversacionId = $_POST['conversacion_id'] ?? 0;
        
        if (empty($mensaje)) {
            http_response_code(400);
            echo json_encode(['error' => 'El mensaje no puede estar vacío']);
            return;
        }
        
        // Respuesta mock exitosa
        $response = [
            'success' => true,
            'mensaje' => [
                'id' => rand(1000, 9999),
                'texto' => $mensaje,
                'remitente_id' => $_SESSION['user_id'] ?? 1,
                'fecha' => date('Y-m-d H:i:s'),
                'fecha_formateada' => 'Justo ahora'
            ]
        ];
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }
    
    /**
     * Marcar conversación como leída
     */
    public function marcarLeido()
    {
        $conversacionId = $_POST['conversacion_id'] ?? 0;
        
        // Simulación: siempre exitoso
        echo json_encode(['success' => true]);
    }
    
    /**
     * DATOS MOCK: Obtener conversaciones del usuario
     * @param int $userId ID del usuario
     * @param string $userRole Rol del usuario (vendedor, comprador, admin)
     */
    private function getMockConversaciones($userId, $userRole = 'vendedor')
    {
        // Conversaciones de ejemplo para un vendedor (usuario_id = 1)
        // Muestra compradores interesados en sus publicaciones
        $conversacionesVendedor = [
            [
                'id' => 1,
                'publicacion_id' => 1,
                'publicacion_titulo' => 'Ford Territory 2022 - Chocado Frontal',
                'publicacion_foto' => 'ford-territory.jpg',
                'otro_usuario_id' => 10,
                'otro_usuario_nombre' => 'Pedro Sánchez',
                'otro_usuario_tipo' => 'comprador',
                'ultimo_mensaje' => '¿Puedo ir a verlo mañana?',
                'ultimo_mensaje_fecha' => date('Y-m-d H:i:s', strtotime('-1 hour')),
                'ultimo_mensaje_fecha_relativa' => 'Hace 1 hora',
                'mensajes_no_leidos' => 1,
                'esta_activa' => true
            ],
            [
                'id' => 2,
                'publicacion_id' => 1,
                'publicacion_titulo' => 'Ford Territory 2022 - Chocado Frontal',
                'publicacion_foto' => 'ford-territory.jpg',
                'otro_usuario_id' => 11,
                'otro_usuario_nombre' => 'Laura Díaz',
                'otro_usuario_tipo' => 'comprador',
                'ultimo_mensaje' => '¿Acepta permuta?',
                'ultimo_mensaje_fecha' => date('Y-m-d H:i:s', strtotime('-3 hours')),
                'ultimo_mensaje_fecha_relativa' => 'Hace 3 horas',
                'mensajes_no_leidos' => 0,
                'esta_activa' => false
            ],
            [
                'id' => 3,
                'publicacion_id' => 2,
                'publicacion_titulo' => 'Kia Cerato 2020 - Choque Lateral',
                'publicacion_foto' => 'kia-cerato.jpg',
                'otro_usuario_id' => 12,
                'otro_usuario_nombre' => 'Roberto Muñoz',
                'otro_usuario_tipo' => 'comprador',
                'ultimo_mensaje' => 'Perfecto, quedamos en contacto',
                'ultimo_mensaje_fecha' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'ultimo_mensaje_fecha_relativa' => 'Hace 1 día',
                'mensajes_no_leidos' => 0,
                'esta_activa' => false
            ]
        ];
        
        // Si es vendedor, retornar solo sus conversaciones
        return $conversacionesVendedor;
    }
    
    /**
     * DATOS MOCK: Obtener mensajes de una conversación
     */
    private function getMockMensajes($conversacionId, $userId)
    {
        $mensajesPorConversacion = [
            1 => [ // Conversación con María González
                [
                    'id' => 1,
                    'remitente_id' => 1, // Usuario actual
                    'remitente_nombre' => 'Juan Pérez',
                    'destinatario_id' => 2,
                    'mensaje' => 'Hola, ¿el vehículo aún está disponible?',
                    'fecha' => date('Y-m-d H:i:s', strtotime('-3 hours')),
                    'fecha_formateada' => 'Hace 3 horas',
                    'leido' => true
                ],
                [
                    'id' => 2,
                    'remitente_id' => 2, // Vendedor
                    'remitente_nombre' => 'María González',
                    'destinatario_id' => 1,
                    'mensaje' => 'Sí, todavía está disponible. ¿Te interesa verlo?',
                    'fecha' => date('Y-m-d H:i:s', strtotime('-2 hours 45 minutes')),
                    'fecha_formateada' => 'Hace 2 horas',
                    'leido' => true
                ],
                [
                    'id' => 3,
                    'remitente_id' => 1,
                    'remitente_nombre' => 'Juan Pérez',
                    'destinatario_id' => 2,
                    'mensaje' => 'Sí, me gustaría saber más detalles. ¿Cuál es el estado exacto del motor?',
                    'fecha' => date('Y-m-d H:i:s', strtotime('-2 hours 30 minutes')),
                    'fecha_formateada' => 'Hace 2 horas',
                    'leido' => true
                ],
                [
                    'id' => 4,
                    'remitente_id' => 2,
                    'remitente_nombre' => 'María González',
                    'destinatario_id' => 1,
                    'mensaje' => 'El motor está en perfectas condiciones, el choque fue solo frontal en la carrocería. Puedo enviarte más fotos si quieres.',
                    'fecha' => date('Y-m-d H:i:s', strtotime('-2 hours')),
                    'fecha_formateada' => 'Hace 2 horas',
                    'leido' => false
                ]
            ],
            2 => [ // Conversación con Carlos Ramírez
                [
                    'id' => 5,
                    'remitente_id' => 1,
                    'remitente_nombre' => 'Juan Pérez',
                    'destinatario_id' => 3,
                    'mensaje' => '¿Aceptas ofertas?',
                    'fecha' => date('Y-m-d H:i:s', strtotime('-2 days')),
                    'fecha_formateada' => 'Hace 2 días',
                    'leido' => true
                ],
                [
                    'id' => 6,
                    'remitente_id' => 3,
                    'remitente_nombre' => 'Carlos Ramírez',
                    'destinatario_id' => 1,
                    'mensaje' => 'El precio es negociable según el estado',
                    'fecha' => date('Y-m-d H:i:s', strtotime('-1 day')),
                    'fecha_formateada' => 'Hace 1 día',
                    'leido' => true
                ]
            ],
            3 => [ // Conversación con Ana Martínez
                [
                    'id' => 7,
                    'remitente_id' => 1,
                    'remitente_nombre' => 'Juan Pérez',
                    'destinatario_id' => 4,
                    'mensaje' => 'Me interesan las piezas del motor',
                    'fecha' => date('Y-m-d H:i:s', strtotime('-4 days')),
                    'fecha_formateada' => 'Hace 4 días',
                    'leido' => true
                ],
                [
                    'id' => 8,
                    'remitente_id' => 4,
                    'remitente_nombre' => 'Ana Martínez',
                    'destinatario_id' => 1,
                    'mensaje' => 'Perfecto, quedamos en contacto',
                    'fecha' => date('Y-m-d H:i:s', strtotime('-3 days')),
                    'fecha_formateada' => 'Hace 3 días',
                    'leido' => true
                ]
            ]
        ];
        
        return $mensajesPorConversacion[$conversacionId] ?? [];
    }
}
