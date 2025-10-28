<?php
/**
 * AdminController
 * Controlador para el panel de administración y moderación
 * 
 * @author ChileChocados
 * @date 2025-10-26
 * MODIFICADO: Login unificado - se eliminaron métodos login() y authenticate()
 */

namespace App\Controllers;

use PDO;
use App\Helpers\Auth;
use App\Helpers\Session;

class AdminController
{
    private $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    /**
     * Verificar que el usuario tiene permisos de administrador
     * MODIFICADO: Usa Auth helpers en lugar de $_SESSION directo
     */
    private function requireAdmin()
    {
        if (!Auth::check() || !Auth::isAdmin()) {
            Session::flash('error', 'Acceso denegado. Debes ser administrador.');
            header('Location: /login');
            exit;
        }
    }

    /**
     * Página principal del admin - Dashboard
     * Ruta: GET /admin
     */
    public function index()
    {
        $this->requireAdmin();
        
        // Manejar acciones AJAX
        $action = $_GET['action'] ?? null;
        $id = $_GET['id'] ?? null;
        
        if ($action === 'ver' && $id && isset($_GET['ajax'])) {
            $this->verPublicacionAjax($id);
            return;
        }
        
        if ($action === 'publicaciones') {
            $this->publicaciones();
            return;
        }
        
        // Mostrar dashboard principal
        $pageTitle = 'Panel de Administración';
        $currentPage = 'admin';
        require_once APP_PATH . '/views/pages/admin/index.php';
    }
    
    /**
     * Ver detalle de publicación vía AJAX
     */
    private function verPublicacionAjax($id)
    {
        header('Content-Type: application/json');
        
        // Obtener publicación
        $stmt = $this->db->prepare("
            SELECT p.*, 
                   u.nombre as usuario_nombre, 
                   u.apellido as usuario_apellido,
                   u.email as usuario_email,
                   u.telefono as usuario_telefono,
                   cp.nombre as categoria_nombre,
                   sc.nombre as subcategoria_nombre,
                   r.nombre as region_nombre,
                   c.nombre as comuna_nombre
            FROM publicaciones p
            INNER JOIN usuarios u ON p.usuario_id = u.id
            LEFT JOIN categorias_padre cp ON p.categoria_padre_id = cp.id
            LEFT JOIN subcategorias sc ON p.subcategoria_id = sc.id
            LEFT JOIN regiones r ON p.region_id = r.id
            LEFT JOIN comunas c ON p.comuna_id = c.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        $publicacion = $stmt->fetch(PDO::FETCH_OBJ);
        
        if (!$publicacion) {
            echo json_encode(['error' => 'Publicación no encontrada']);
            exit;
        }
        
        // Obtener fotos
        $stmt = $this->db->prepare("SELECT * FROM publicacion_fotos WHERE publicacion_id = ? ORDER BY orden");
        $stmt->execute([$id]);
        $fotos = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        // Obtener historial (simplificado por ahora)
        $historial = [];
        
        echo json_encode([
            'publicacion' => $publicacion,
            'fotos' => $fotos,
            'historial' => $historial
        ]);
        exit;
    }

    /**
     * Cerrar sesión del admin
     * Ruta: GET /admin/logout
     * MODIFICADO: Usa Auth::logout() helper
     */
    public function logout()
    {
        Auth::logout();
        Session::flash('success', 'Sesión cerrada exitosamente');
        header('Location: /');
        exit;
    }

    /**
     * Panel de gestión de publicaciones
     * Ruta: GET /admin/publicaciones
     */
    public function publicaciones()
    {
        $this->requireAdmin();

        // Obtener filtros
        $estado = $_GET['estado'] ?? '';
        $categoria_id = $_GET['categoria'] ?? '';
        $busqueda = $_GET['q'] ?? '';
        $page = max(1, (int)($_GET['page'] ?? 1));
        $per_page = 20;
        $offset = ($page - 1) * $per_page;

        // Construir query base
        $sql = "SELECT p.*, 
                       u.nombre as usuario_nombre, 
                       u.apellido as usuario_apellido,
                       u.email as usuario_email,
                       u.telefono as usuario_telefono,
                       cp.nombre as categoria_nombre,
                       sc.nombre as subcategoria_nombre,
                       r.nombre as region_nombre,
                       c.nombre as comuna_nombre
                FROM publicaciones p
                INNER JOIN usuarios u ON p.usuario_id = u.id
                LEFT JOIN categorias_padre cp ON p.categoria_padre_id = cp.id
                LEFT JOIN subcategorias sc ON p.subcategoria_id = sc.id
                LEFT JOIN regiones r ON p.region_id = r.id
                LEFT JOIN comunas c ON p.comuna_id = c.id
                WHERE 1=1";

        $params = [];

        // Filtro por estado
        if ($estado && $estado !== 'todas') {
            $sql .= " AND p.estado = ?";
            $params[] = $estado;
        }

        // Filtro por categoría
        if ($categoria_id) {
            $sql .= " AND p.categoria_padre_id = ?";
            $params[] = $categoria_id;
        }

        // Búsqueda
        if ($busqueda) {
            $sql .= " AND (p.titulo LIKE ? OR p.marca LIKE ? OR p.modelo LIKE ? OR u.nombre LIKE ? OR u.email LIKE ?)";
            $search_term = "%$busqueda%";
            $params = array_merge($params, [$search_term, $search_term, $search_term, $search_term, $search_term]);
        }

        $sql .= " ORDER BY 
                  CASE 
                    WHEN p.estado = 'pendiente' THEN 1
                    WHEN p.estado = 'aprobada' THEN 2
                    WHEN p.estado = 'rechazada' THEN 3
                    ELSE 4
                  END,
                  p.fecha_creacion DESC
                  LIMIT ? OFFSET ?";

        $params[] = $per_page;
        $params[] = $offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $publicaciones = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Obtener total de registros para paginación
        $sql_count = str_replace("SELECT p.*", "SELECT COUNT(*) as total", $sql);
        $sql_count = preg_replace('/ORDER BY.*$/s', '', $sql_count);
        $sql_count = preg_replace('/LIMIT.*$/s', '', $sql_count);
        
        $params_count = array_slice($params, 0, -2); // Remover LIMIT y OFFSET
        $stmt_count = $this->db->prepare($sql_count);
        $stmt_count->execute($params_count);
        $total = $stmt_count->fetch(PDO::FETCH_OBJ)->total;

        // Obtener categorías para filtros
        $stmt_cats = $this->db->query("SELECT * FROM categorias_padre ORDER BY nombre");
        $categorias = $stmt_cats->fetchAll(PDO::FETCH_OBJ);

        // Calcular paginación
        $total_pages = ceil($total / $per_page);

        // Vista
        $pageTitle = 'Gestión de Publicaciones';
        $currentPage = 'admin-publicaciones';
        require_once APP_PATH . '/views/pages/admin/publicaciones.php';
    }

    /**
     * Ver detalle completo de una publicación
     * Ruta: GET /admin/publicaciones/{id}
     */
    public function verPublicacion($id)
    {
        $this->requireAdmin();

        // Obtener publicación completa con datos relacionados
        $stmt = $this->db->prepare("
            SELECT p.*, 
                   u.nombre as usuario_nombre, 
                   u.apellido as usuario_apellido,
                   u.email as usuario_email,
                   u.telefono as usuario_telefono,
                   u.rut as usuario_rut,
                   u.fecha_registro as usuario_fecha_registro,
                   cp.nombre as categoria_nombre,
                   sc.nombre as subcategoria_nombre,
                   r.nombre as region_nombre,
                   c.nombre as comuna_nombre
            FROM publicaciones p
            INNER JOIN usuarios u ON p.usuario_id = u.id
            LEFT JOIN categorias_padre cp ON p.categoria_padre_id = cp.id
            LEFT JOIN subcategorias sc ON p.subcategoria_id = sc.id
            LEFT JOIN regiones r ON p.region_id = r.id
            LEFT JOIN comunas c ON p.comuna_id = c.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        $publicacion = $stmt->fetch(PDO::FETCH_OBJ);

        if (!$publicacion) {
            Session::flash('error', 'Publicación no encontrada');
            header('Location: /admin/publicaciones');
            exit;
        }

        // Obtener fotos de la publicación
        $stmt = $this->db->prepare("
            SELECT * FROM publicacion_fotos 
            WHERE publicacion_id = ? 
            ORDER BY orden ASC
        ");
        $stmt->execute([$id]);
        $fotos = $stmt->fetchAll(PDO::FETCH_OBJ);

        $data = [
            'title' => 'Revisar Publicación - Admin',
            'publicacion' => $publicacion,
            'fotos' => $fotos,
            'csrf_token' => generateCsrfToken()
        ];

        require_once APP_PATH . '/views/pages/admin/detalle-publicacion.php';
    }

    /**
     * Aprobar una publicación
     * Ruta: POST /admin/publicaciones/{id}/aprobar
     */
    public function aprobarPublicacion($id)
    {
        $this->requireAdmin();

        // Validar CSRF
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'Token de seguridad inválido');
            header('Location: /admin/publicaciones/' . $id);
            exit;
        }

        // Obtener publicación
        $stmt = $this->db->prepare("
            SELECT p.*, u.email, u.nombre 
            FROM publicaciones p 
            INNER JOIN usuarios u ON p.usuario_id = u.id 
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        $publicacion = $stmt->fetch(PDO::FETCH_OBJ);

        if (!$publicacion) {
            Session::flash('error', 'Publicación no encontrada');
            header('Location: /admin/publicaciones');
            exit;
        }

        // Actualizar estado a aprobada
        $stmt = $this->db->prepare("
            UPDATE publicaciones 
            SET estado = 'aprobada',
                fecha_publicacion = NOW(),
                motivo_rechazo = NULL
            WHERE id = ?
        ");
        $stmt->execute([$id]);

        // Registrar en auditoría
        $stmt = $this->db->prepare("
            INSERT INTO auditoria (usuario_id, tabla, registro_id, accion, datos_nuevos, ip)
            VALUES (?, 'publicaciones', ?, 'actualizar', ?, ?)
        ");
        $stmt->execute([
            Auth::id(),
            $id,
            json_encode(['estado' => 'aprobada', 'aprobado_por' => Auth::id()]),
            $_SERVER['REMOTE_ADDR'] ?? null
        ]);

        // Enviar notificación por email (si está configurado)
        $this->enviarNotificacionAprobacion($publicacion);

        Session::flash('success', 'Publicación aprobada exitosamente. El usuario ha sido notificado.');
        
        // Verificar si se debe enviar notificación adicional
        if (isset($_POST['enviar_notificacion']) && $_POST['enviar_notificacion'] === '1') {
            // Aquí se puede agregar lógica adicional para notificación especial
        }

        header('Location: /admin/publicaciones');
        exit;
    }

    /**
     * Rechazar una publicación
     * Ruta: POST /admin/publicaciones/{id}/rechazar
     */
    public function rechazarPublicacion($id)
    {
        $this->requireAdmin();

        // Validar CSRF
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'Token de seguridad inválido');
            header('Location: /admin/publicaciones/' . $id);
            exit;
        }

        $motivo = sanitize($_POST['motivo_rechazo'] ?? '');

        if (empty($motivo)) {
            Session::flash('error', 'Debes especificar un motivo de rechazo');
            header('Location: /admin/publicaciones/' . $id);
            exit;
        }

        // Obtener publicación
        $stmt = $this->db->prepare("
            SELECT p.*, u.email, u.nombre 
            FROM publicaciones p 
            INNER JOIN usuarios u ON p.usuario_id = u.id 
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        $publicacion = $stmt->fetch(PDO::FETCH_OBJ);

        if (!$publicacion) {
            Session::flash('error', 'Publicación no encontrada');
            header('Location: /admin/publicaciones');
            exit;
        }

        // Actualizar estado a rechazada
        $stmt = $this->db->prepare("
            UPDATE publicaciones 
            SET estado = 'rechazada',
                motivo_rechazo = ?,
                fecha_publicacion = NULL
            WHERE id = ?
        ");
        $stmt->execute([$motivo, $id]);

        // Registrar en auditoría
        $stmt = $this->db->prepare("
            INSERT INTO auditoria (usuario_id, tabla, registro_id, accion, datos_nuevos, ip)
            VALUES (?, 'publicaciones', ?, 'actualizar', ?, ?)
        ");
        $stmt->execute([
            Auth::id(),
            $id,
            json_encode(['estado' => 'rechazada', 'motivo' => $motivo, 'rechazado_por' => Auth::id()]),
            $_SERVER['REMOTE_ADDR'] ?? null
        ]);

        // Enviar notificación por email
        $this->enviarNotificacionRechazo($publicacion, $motivo);

        Session::flash('success', 'Publicación rechazada. El usuario ha sido notificado del motivo.');
        header('Location: /admin/publicaciones');
        exit;
    }

    /**
     * Destacar publicación (asignar como destacada)
     * Ruta: POST /admin/publicaciones/{id}/destacar
     */
    public function destacarPublicacion($id)
    {
        $this->requireAdmin();

        // Validar CSRF
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'Token de seguridad inválido');
            header('Location: /admin/publicaciones');
            exit;
        }

        $dias = (int)($_POST['dias'] ?? 15);
        
        if (!in_array($dias, [15, 30])) {
            Session::flash('error', 'Días de destacado inválidos');
            header('Location: /admin/publicaciones');
            exit;
        }

        // Actualizar publicación
        $stmt = $this->db->prepare("
            UPDATE publicaciones 
            SET es_destacada = 1,
                fecha_destacada_inicio = NOW(),
                fecha_destacada_fin = DATE_ADD(NOW(), INTERVAL ? DAY)
            WHERE id = ?
        ");
        $stmt->execute([$dias, $id]);

        Session::flash('success', "Publicación destacada por $dias días");
        header('Location: /admin/publicaciones');
        exit;
    }

    /**
     * Contactar al usuario de una publicación
     * Ruta: POST /admin/publicaciones/{id}/contactar
     */
    public function contactarUsuario($id)
    {
        $this->requireAdmin();

        // Obtener publicación y usuario
        $stmt = $this->db->prepare("
            SELECT p.*, u.email, u.nombre, u.telefono 
            FROM publicaciones p 
            INNER JOIN usuarios u ON p.usuario_id = u.id 
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        $publicacion = $stmt->fetch(PDO::FETCH_OBJ);

        if (!$publicacion) {
            Session::flash('error', 'Publicación no encontrada');
            header('Location: /admin/publicaciones');
            exit;
        }

        $mensaje = sanitize($_POST['mensaje'] ?? '');

        if (empty($mensaje)) {
            Session::flash('error', 'Debes escribir un mensaje');
            header('Location: /admin/publicaciones/' . $id);
            exit;
        }

        // Enviar email al usuario
        $this->enviarMensajeContacto($publicacion, $mensaje);

        Session::flash('success', 'Mensaje enviado al usuario');
        header('Location: /admin/publicaciones/' . $id);
        exit;
    }

    /**
     * Enviar notificación de aprobación por email
     */
    private function enviarNotificacionAprobacion($publicacion)
    {
        // TODO: Implementar envío real de email usando PHPMailer o similar
        // Por ahora solo registramos en log
        
        $asunto = 'Tu publicación ha sido aprobada - ChileChocados';
        $mensaje = "
            Hola {$publicacion->nombre},
            
            ¡Buenas noticias! Tu publicación '{$publicacion->titulo}' ha sido aprobada 
            y ahora es visible públicamente en ChileChocados.
            
            Puedes ver tu publicación en: " . BASE_URL . "/detalle/{$publicacion->id}
            
            ¡Gracias por confiar en ChileChocados!
            
            --
            Equipo ChileChocados
        ";

        // Log temporal
        error_log("EMAIL APROBACIÓN: Para {$publicacion->email} - Publicación #{$publicacion->id}");
        
        // Aquí iría la lógica real de envío de email
        // sendEmail($publicacion->email, $asunto, $mensaje);
    }

    /**
     * Enviar notificación de rechazo por email
     */
    private function enviarNotificacionRechazo($publicacion, $motivo)
    {
        $asunto = 'Tu publicación requiere modificaciones - ChileChocados';
        $mensaje = "
            Hola {$publicacion->nombre},
            
            Tu publicación '{$publicacion->titulo}' no pudo ser aprobada por el siguiente motivo:
            
            {$motivo}
            
            Por favor, edita tu publicación y vuelve a enviarla para revisión.
            
            Si tienes dudas, no dudes en contactarnos.
            
            --
            Equipo ChileChocados
        ";

        error_log("EMAIL RECHAZO: Para {$publicacion->email} - Publicación #{$publicacion->id}");
        
        // Aquí iría la lógica real de envío de email
        // sendEmail($publicacion->email, $asunto, $mensaje);
    }

    /**
     * Enviar mensaje de contacto al usuario
     */
    private function enviarMensajeContacto($publicacion, $mensaje)
    {
        $asunto = 'Mensaje del equipo de ChileChocados';
        $contenido = "
            Hola {$publicacion->nombre},
            
            El equipo de ChileChocados te ha enviado el siguiente mensaje 
            respecto a tu publicación '{$publicacion->titulo}':
            
            {$mensaje}
            
            --
            Equipo ChileChocados
        ";

        error_log("EMAIL CONTACTO: Para {$publicacion->email}");
        
        // Aquí iría la lógica real de envío de email
        // sendEmail($publicacion->email, $asunto, $contenido);
    }
    
    /**
     * Vista de mensajería para administrador
     * Muestra TODAS las conversaciones del sistema
     * Ruta: GET /admin/mensajes
     */
    public function mensajes()
    {
        $this->requireAdmin();
        
        // Obtener conversación activa desde URL
        $conversacionActiva = isset($_GET['conversacion']) ? (int)$_GET['conversacion'] : null;
        
        // DATOS MOCK: Todas las conversaciones del sistema (admin ve todo)
        $conversaciones = $this->getMockTodasConversaciones();
        
        // Seleccionar primera conversación si no hay una activa
        if (!$conversacionActiva && !empty($conversaciones)) {
            $conversacionActiva = $conversaciones[0]['id'];
        }
        
        // Obtener mensajes de la conversación activa
        $mensajes = $conversacionActiva ? $this->getMockMensajesAdmin($conversacionActiva) : [];
        
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
            'title' => 'Mensajes - Panel Admin',
            'conversaciones' => $conversaciones,
            'conversacion_activa_id' => $conversacionActiva,
            'conversacion_info' => $conversacionInfo,
            'mensajes' => $mensajes,
            'user_id' => $_SESSION['user_id'] ?? 1,
            'is_admin' => true
        ];
        
        // Renderizar vista de mensajes (misma vista, diferente data)
        require_once __DIR__ . '/../views/pages/mensajes/index.php';
    }
    
    /**
     * MOCK: Obtener todas las conversaciones del sistema (vista admin)
     */
    private function getMockTodasConversaciones()
    {
        return [
            [
                'id' => 1,
                'publicacion_id' => 1,
                'publicacion_titulo' => 'Ford Territory 2022 - Chocado Frontal',
                'publicacion_foto' => 'ford-territory.jpg',
                'vendedor_id' => 5,
                'vendedor_nombre' => 'Juan Pérez',
                'comprador_id' => 10,
                'comprador_nombre' => 'Pedro Sánchez',
                'otro_usuario_id' => 10,
                'otro_usuario_nombre' => 'Pedro Sánchez (Comprador) → Juan Pérez (Vendedor)',
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
                'vendedor_id' => 5,
                'vendedor_nombre' => 'Juan Pérez',
                'comprador_id' => 11,
                'comprador_nombre' => 'Laura Díaz',
                'otro_usuario_id' => 11,
                'otro_usuario_nombre' => 'Laura Díaz (Comprador) → Juan Pérez (Vendedor)',
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
                'vendedor_id' => 6,
                'vendedor_nombre' => 'María González',
                'comprador_id' => 12,
                'comprador_nombre' => 'Roberto Muñoz',
                'otro_usuario_id' => 12,
                'otro_usuario_nombre' => 'Roberto Muñoz (Comprador) → María González (Vendedor)',
                'otro_usuario_tipo' => 'comprador',
                'ultimo_mensaje' => 'Perfecto, quedamos en contacto',
                'ultimo_mensaje_fecha' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'ultimo_mensaje_fecha_relativa' => 'Hace 1 día',
                'mensajes_no_leidos' => 0,
                'esta_activa' => false
            ],
            [
                'id' => 4,
                'publicacion_id' => 3,
                'publicacion_titulo' => 'Kia Rio 5 2019 - Choque Trasero',
                'publicacion_foto' => 'kia-rio-5.jpg',
                'vendedor_id' => 7,
                'vendedor_nombre' => 'Carlos Muñoz',
                'comprador_id' => 13,
                'comprador_nombre' => 'Ana Torres',
                'otro_usuario_id' => 13,
                'otro_usuario_nombre' => 'Ana Torres (Comprador) → Carlos Muñoz (Vendedor)',
                'otro_usuario_tipo' => 'comprador',
                'ultimo_mensaje' => '¿Tiene más fotos del motor?',
                'ultimo_mensaje_fecha' => date('Y-m-d H:i:s', strtotime('-2 days')),
                'ultimo_mensaje_fecha_relativa' => 'Hace 2 días',
                'mensajes_no_leidos' => 0,
                'esta_activa' => false
            ]
        ];
    }
    
    /**
     * MOCK: Obtener mensajes de una conversación (vista admin)
     */
    private function getMockMensajesAdmin($conversacionId)
    {
        // Retornar mensajes mock según la conversación
        $mensajesPorConversacion = [
            1 => [
                [
                    'id' => 1,
                    'remitente_id' => 10,
                    'remitente_nombre' => 'Pedro Sánchez',
                    'destinatario_id' => 5,
                    'mensaje' => 'Hola, me interesa el Ford Territory',
                    'fecha' => date('Y-m-d H:i:s', strtotime('-2 hours')),
                    'fecha_relativa' => 'Hace 2 horas',
                    'leido' => true
                ],
                [
                    'id' => 2,
                    'remitente_id' => 5,
                    'remitente_nombre' => 'Juan Pérez',
                    'destinatario_id' => 10,
                    'mensaje' => 'Hola Pedro, sí está disponible',
                    'fecha' => date('Y-m-d H:i:s', strtotime('-1.5 hours')),
                    'fecha_relativa' => 'Hace 1.5 horas',
                    'leido' => true
                ],
                [
                    'id' => 3,
                    'remitente_id' => 10,
                    'remitente_nombre' => 'Pedro Sánchez',
                    'destinatario_id' => 5,
                    'mensaje' => '¿Puedo ir a verlo mañana?',
                    'fecha' => date('Y-m-d H:i:s', strtotime('-1 hour')),
                    'fecha_relativa' => 'Hace 1 hora',
                    'leido' => false
                ]
            ]
        ];
        
        return $mensajesPorConversacion[$conversacionId] ?? [];
    }

}
