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
}