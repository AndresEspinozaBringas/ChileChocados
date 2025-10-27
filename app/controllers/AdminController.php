<?php
/**
 * AdminController
 * Controlador para el panel de administración y moderación
 * 
 * @author ChileChocados
 * @date 2025-10-26
 */

namespace App\Controllers;

use PDO;

class AdminController
{
    private $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    /**
     * Verificar que el usuario tiene permisos de administrador
     */
    private function requireAdmin()
    {
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_rol'] ?? '') !== 'admin') {
            $_SESSION['error'] = 'Acceso denegado. Debes ser administrador.';
            header('Location: ' . BASE_URL . '/admin/login');
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
     * Login del administrador
     * Ruta: GET /admin/login
     */
    public function login()
    {
        // Si ya está autenticado como admin, redirigir al panel
        if (isset($_SESSION['user_id']) && ($_SESSION['user_rol'] ?? '') === 'admin') {
            header('Location: ' . BASE_URL . '/admin');
            exit;
        }

        $data = [
            'title' => 'Login Admin - ChileChocados',
            'csrf_token' => generateCsrfToken()
        ];

        require_once APP_PATH . '/views/pages/admin/login.php';
    }

    /**
     * Procesar login del administrador
     * Ruta: POST /admin/authenticate
     */
    public function authenticate()
    {
        // Validar CSRF
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de seguridad inválido';
            header('Location: ' . BASE_URL . '/admin/login');
            exit;
        }

        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Buscar usuario admin
        $stmt = $this->db->prepare("
            SELECT * FROM usuarios 
            WHERE email = ? AND rol = 'admin' AND estado = 'activo'
        ");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_OBJ);

        if (!$user || !password_verify($password, $user->password)) {
            $_SESSION['error'] = 'Credenciales incorrectas';
            header('Location: ' . BASE_URL . '/admin/login');
            exit;
        }

        // Establecer sesión
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_nombre'] = $user->nombre;
        $_SESSION['user_apellido'] = $user->apellido;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_rol'] = $user->rol;

        // Actualizar última conexión
        $stmt = $this->db->prepare("UPDATE usuarios SET ultima_conexion = NOW() WHERE id = ?");
        $stmt->execute([$user->id]);

        $_SESSION['success'] = '¡Bienvenido, ' . $user->nombre . '!';
        header('Location: ' . BASE_URL . '/admin');
        exit;
    }

    /**
     * Cerrar sesión del admin
     * Ruta: POST /admin/logout
     */
    public function logout()
    {
        session_destroy();
        header('Location: ' . BASE_URL . '/admin/login');
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

        // Contar total para paginación
        $count_sql = "SELECT COUNT(*) as total FROM publicaciones p WHERE 1=1";
        $count_params = [];

        if ($estado && $estado !== 'todas') {
            $count_sql .= " AND p.estado = ?";
            $count_params[] = $estado;
        }
        if ($categoria_id) {
            $count_sql .= " AND p.categoria_padre_id = ?";
            $count_params[] = $categoria_id;
        }
        if ($busqueda) {
            $count_sql .= " AND (p.titulo LIKE ? OR p.marca LIKE ? OR p.modelo LIKE ?)";
            $search_term = "%$busqueda%";
            $count_params = array_merge($count_params, [$search_term, $search_term, $search_term]);
        }

        $count_stmt = $this->db->prepare($count_sql);
        $count_stmt->execute($count_params);
        $total = $count_stmt->fetch(PDO::FETCH_OBJ)->total;

        // Obtener estadísticas
        $stats_sql = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                        SUM(CASE WHEN estado = 'aprobada' THEN 1 ELSE 0 END) as aprobadas,
                        SUM(CASE WHEN estado = 'rechazada' THEN 1 ELSE 0 END) as rechazadas
                      FROM publicaciones";
        $stats = $this->db->query($stats_sql)->fetch(PDO::FETCH_OBJ);

        // Obtener categorías para filtro
        $categorias = $this->db->query("SELECT id, nombre FROM categorias_padre WHERE activo = 1 ORDER BY nombre")->fetchAll(PDO::FETCH_OBJ);

        $data = [
            'title' => 'Gestión de Publicaciones - Admin',
            'publicaciones' => $publicaciones,
            'stats' => $stats,
            'categorias' => $categorias,
            'filtros' => [
                'estado' => $estado,
                'categoria' => $categoria_id,
                'q' => $busqueda
            ],
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($total / $per_page),
                'total_items' => $total,
                'per_page' => $per_page
            ]
        ];

        require_once APP_PATH . '/views/pages/admin/publicaciones.php';
    }

    /**
     * Ver detalle de una publicación para revisar
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
            $_SESSION['error'] = 'Publicación no encontrada';
            header('Location: ' . BASE_URL . '/admin/publicaciones');
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
            $_SESSION['error'] = 'Token de seguridad inválido';
            header('Location: ' . BASE_URL . '/admin/publicaciones/' . $id);
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
            $_SESSION['error'] = 'Publicación no encontrada';
            header('Location: ' . BASE_URL . '/admin/publicaciones');
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
            $_SESSION['user_id'],
            $id,
            json_encode(['estado' => 'aprobada', 'aprobado_por' => $_SESSION['user_id']]),
            $_SERVER['REMOTE_ADDR'] ?? null
        ]);

        // Enviar notificación por email (si está configurado)
        $this->enviarNotificacionAprobacion($publicacion);

        $_SESSION['success'] = 'Publicación aprobada exitosamente. El usuario ha sido notificado.';
        
        // Verificar si se debe enviar notificación adicional
        if (isset($_POST['enviar_notificacion']) && $_POST['enviar_notificacion'] === '1') {
            // Aquí se puede agregar lógica adicional para notificación especial
        }

        header('Location: ' . BASE_URL . '/admin/publicaciones');
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
            $_SESSION['error'] = 'Token de seguridad inválido';
            header('Location: ' . BASE_URL . '/admin/publicaciones/' . $id);
            exit;
        }

        $motivo = sanitize($_POST['motivo_rechazo'] ?? '');

        if (empty($motivo)) {
            $_SESSION['error'] = 'Debes especificar un motivo de rechazo';
            header('Location: ' . BASE_URL . '/admin/publicaciones/' . $id);
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
            $_SESSION['error'] = 'Publicación no encontrada';
            header('Location: ' . BASE_URL . '/admin/publicaciones');
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
            $_SESSION['user_id'],
            $id,
            json_encode(['estado' => 'rechazada', 'motivo' => $motivo, 'rechazado_por' => $_SESSION['user_id']]),
            $_SERVER['REMOTE_ADDR'] ?? null
        ]);

        // Enviar notificación por email
        $this->enviarNotificacionRechazo($publicacion, $motivo);

        $_SESSION['success'] = 'Publicación rechazada. El usuario ha sido notificado del motivo.';
        header('Location: ' . BASE_URL . '/admin/publicaciones');
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
            $_SESSION['error'] = 'Token de seguridad inválido';
            header('Location: ' . BASE_URL . '/admin/publicaciones');
            exit;
        }

        $dias = (int)($_POST['dias'] ?? 15);
        
        if (!in_array($dias, [15, 30])) {
            $_SESSION['error'] = 'Días de destacado inválidos';
            header('Location: ' . BASE_URL . '/admin/publicaciones');
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

        $_SESSION['success'] = "Publicación destacada por $dias días";
        header('Location: ' . BASE_URL . '/admin/publicaciones');
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
            $_SESSION['error'] = 'Publicación no encontrada';
            header('Location: ' . BASE_URL . '/admin/publicaciones');
            exit;
        }

        $mensaje = sanitize($_POST['mensaje'] ?? '');

        if (empty($mensaje)) {
            $_SESSION['error'] = 'Debes escribir un mensaje';
            header('Location: ' . BASE_URL . '/admin/publicaciones/' . $id);
            exit;
        }

        // Enviar email al usuario
        $this->enviarMensajeContacto($publicacion, $mensaje);

        $_SESSION['success'] = 'Mensaje enviado al usuario';
        header('Location: ' . BASE_URL . '/admin/publicaciones/' . $id);
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