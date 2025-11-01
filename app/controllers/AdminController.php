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
        
        // Obtener estadísticas reales de la base de datos
        
        // Estadísticas de publicaciones (excluyendo borradores)
        $stmt = $this->db->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN estado = 'aprobada' THEN 1 ELSE 0 END) as aprobadas,
                SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                SUM(CASE WHEN estado = 'rechazada' THEN 1 ELSE 0 END) as rechazadas
            FROM publicaciones
            WHERE estado != 'borrador'
        ");
        $statsPublicaciones = $stmt->fetch(\PDO::FETCH_OBJ);
        
        // Estadísticas de usuarios
        $stmt = $this->db->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN estado = 'activo' THEN 1 ELSE 0 END) as activos,
                SUM(CASE WHEN DATE(fecha_registro) >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as nuevos_semana
            FROM usuarios
        ");
        $statsUsuarios = $stmt->fetch(\PDO::FETCH_OBJ);
        
        // Estadísticas de mensajes sin leer (aproximado)
        $stmt = $this->db->query("
            SELECT COUNT(*) as total
            FROM mensajes
            WHERE leido = 0
        ");
        $statsMensajes = $stmt->fetch(\PDO::FETCH_OBJ);
        
        // Publicaciones destacadas
        $stmt = $this->db->query("
            SELECT COUNT(*) as total
            FROM publicaciones
            WHERE es_destacada = 1 
            AND fecha_destacada_fin >= NOW()
        ");
        $statsDestacadas = $stmt->fetch(\PDO::FETCH_OBJ);
        
        // Actividad de los últimos 7 días (excluyendo borradores)
        $stmt = $this->db->query("
            SELECT 
                DATE(fecha_creacion) as fecha,
                COUNT(*) as total
            FROM publicaciones
            WHERE fecha_creacion >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            AND estado != 'borrador'
            GROUP BY DATE(fecha_creacion)
            ORDER BY fecha ASC
        ");
        $actividadSemanal = $stmt->fetchAll(\PDO::FETCH_OBJ);
        
        // Publicaciones por categoría (top 5)
        $stmt = $this->db->query("
            SELECT 
                cp.nombre as categoria,
                COUNT(p.id) as total
            FROM publicaciones p
            INNER JOIN categorias_padre cp ON p.categoria_padre_id = cp.id
            WHERE p.estado = 'aprobada'
            GROUP BY cp.id, cp.nombre
            ORDER BY total DESC
            LIMIT 5
        ");
        $publicacionesPorCategoria = $stmt->fetchAll(\PDO::FETCH_OBJ);
        
        // Distribución de estados (para gráfico de dona)
        $distribucionEstados = [
            'aprobadas' => $statsPublicaciones->aprobadas ?? 0,
            'pendientes' => $statsPublicaciones->pendientes ?? 0,
            'rechazadas' => $statsPublicaciones->rechazadas ?? 0
        ];
        
        // Preparar datos para la vista
        $pageTitle = 'Panel de Administración';
        $currentPage = 'admin-dashboard';
        $adminNombre = $_SESSION['user_nombre'] ?? 'Admin';
        
        $metricas = [
            'publicaciones_activas' => $statsPublicaciones->aprobadas ?? 0,
            'publicaciones_pendientes' => $statsPublicaciones->pendientes ?? 0,
            'publicaciones_rechazadas' => $statsPublicaciones->rechazadas ?? 0,
            'usuarios_total' => $statsUsuarios->total ?? 0,
            'usuarios_activos' => $statsUsuarios->activos ?? 0,
            'usuarios_nuevos_semana' => $statsUsuarios->nuevos_semana ?? 0,
            'mensajes_sin_leer' => $statsMensajes->total ?? 0,
            'destacadas_activas' => $statsDestacadas->total ?? 0
        ];
        
        // Preparar datos para gráficos
        $chartData = [
            'actividad_semanal' => $actividadSemanal,
            'por_categoria' => $publicacionesPorCategoria,
            'distribucion_estados' => $distribucionEstados
        ];
        
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
                WHERE p.estado != 'borrador'";

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
        $sql_count = "SELECT COUNT(DISTINCT p.id) as total
                FROM publicaciones p
                INNER JOIN usuarios u ON p.usuario_id = u.id
                LEFT JOIN categorias_padre cp ON p.categoria_padre_id = cp.id
                LEFT JOIN subcategorias sc ON p.subcategoria_id = sc.id
                LEFT JOIN regiones r ON p.region_id = r.id
                LEFT JOIN comunas c ON p.comuna_id = c.id
                WHERE p.estado != 'borrador'";
        
        $params_count = [];
        
        // Aplicar los mismos filtros
        if ($estado && $estado !== 'todas') {
            $sql_count .= " AND p.estado = ?";
            $params_count[] = $estado;
        }
        
        if ($categoria_id) {
            $sql_count .= " AND p.categoria_padre_id = ?";
            $params_count[] = $categoria_id;
        }
        
        if ($busqueda) {
            $sql_count .= " AND (p.titulo LIKE ? OR p.marca LIKE ? OR p.modelo LIKE ? OR u.nombre LIKE ? OR u.email LIKE ?)";
            $search_term = "%$busqueda%";
            $params_count = array_merge($params_count, [$search_term, $search_term, $search_term, $search_term, $search_term]);
        }
        
        $stmt_count = $this->db->prepare($sql_count);
        $stmt_count->execute($params_count);
        $total = $stmt_count->fetch(PDO::FETCH_OBJ)->total;

        // Obtener categorías para filtros
        $stmt_cats = $this->db->query("SELECT * FROM categorias_padre ORDER BY nombre");
        $categorias = $stmt_cats->fetchAll(PDO::FETCH_OBJ);

        // Calcular paginación
        $total_pages = ceil($total / $per_page);

        // Obtener conteos por estado (excluyendo borradores)
        $stmt_conteo = $this->db->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                SUM(CASE WHEN estado = 'aprobada' THEN 1 ELSE 0 END) as aprobadas,
                SUM(CASE WHEN estado = 'rechazada' THEN 1 ELSE 0 END) as rechazadas
            FROM publicaciones
            WHERE estado != 'borrador'
        ");
        $conteo = $stmt_conteo->fetch(PDO::FETCH_ASSOC);

        // Preparar datos para la vista
        $filtros = [
            'estado' => $estado,
            'categoria' => $categoria_id,
            'busqueda' => $busqueda,
            'fecha_desde' => $_GET['fecha_desde'] ?? '',
            'fecha_hasta' => $_GET['fecha_hasta'] ?? ''
        ];

        $pagination = [
            'current_page' => $page,
            'total_pages' => $total_pages,
            'total' => $total
        ];

        // Vista
        $pageTitle = 'Gestión de Publicaciones - Admin';
        $currentPage = 'admin-publicaciones';
        require_once APP_PATH . '/views/pages/admin/publicaciones.php';
    }

    /**
     * Ver detalle completo de una publicación
     * Ruta: GET /admin/publicaciones/{id}
     * Soporta AJAX: GET /admin/publicaciones/{id}?ajax=1
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
            // Si es AJAX, devolver JSON
            if (isset($_GET['ajax'])) {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Publicación no encontrada']);
                exit;
            }
            
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

        // Si es petición AJAX, devolver JSON
        if (isset($_GET['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'publicacion' => $publicacion,
                'fotos' => $fotos
            ]);
            exit;
        }

        // Si no es AJAX, cargar vista normal
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

        // Actualizar estado a aprobada y activar destacado si corresponde
        $updateSql = "UPDATE publicaciones 
                      SET estado = 'aprobada',
                          fecha_publicacion = NOW(),
                          motivo_rechazo = NULL";
        
        // Si es destacada, activar las fechas de destacado AHORA
        if ($publicacion->es_destacada == 1) {
            $updateSql .= ", fecha_destacada_inicio = NOW()";
            
            // Calcular fecha_destacada_fin basado en si ya existe o usar 15 días por defecto
            if (!empty($publicacion->fecha_destacada_fin) && !empty($publicacion->fecha_destacada_inicio)) {
                // Mantener la duración original
                $diff_seconds = strtotime($publicacion->fecha_destacada_fin) - strtotime($publicacion->fecha_destacada_inicio);
                $dias = ceil($diff_seconds / (24 * 3600));
            } else {
                // Por defecto 15 días
                $dias = 15;
            }
            
            $updateSql .= ", fecha_destacada_fin = DATE_ADD(NOW(), INTERVAL $dias DAY)";
        }
        
        $updateSql .= " WHERE id = ?";
        
        $stmt = $this->db->prepare($updateSql);
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
        
        require_once APP_PATH . '/models/Mensaje.php';
        
        $mensajeModel = new \App\Models\Mensaje();
        
        // Obtener conversación activa desde URL (formato: publicacion_id-usuario1_id-usuario2_id)
        $conversacionKey = isset($_GET['conversacion']) ? $_GET['conversacion'] : null;
        
        // Obtener todas las conversaciones del sistema usando el nuevo método del modelo
        $conversacionesRaw = $mensajeModel->getTodasLasConversaciones();
        
        // Formatear conversaciones
        $conversaciones = [];
        foreach ($conversacionesRaw as $conv) {
            $convArray = (array) $conv;
            $convArray['ultimo_mensaje_fecha_relativa'] = $this->formatearFecha($conv->ultimo_mensaje_fecha);
            $convArray['conversacion_titulo'] = trim($conv->usuario1_nombre) . ' ↔ ' . trim($conv->usuario2_nombre);
            $conversaciones[] = $convArray;
        }
        
        // Determinar conversación activa
        $conversacionActiva = null;
        $mensajes = [];
        
        if ($conversacionKey) {
            foreach ($conversaciones as $conv) {
                if ($conv['conversacion_key'] === $conversacionKey) {
                    $conversacionActiva = $conv;
                    break;
                }
            }
        } elseif (!empty($conversaciones)) {
            $conversacionActiva = $conversaciones[0];
        }
        
        // Obtener mensajes de la conversación activa
        if ($conversacionActiva) {
            $mensajes = $mensajeModel->getConversacionPorClave($conversacionActiva['conversacion_key']);
            
            // Formatear fechas
            foreach ($mensajes as &$mensaje) {
                $mensaje->fecha_formateada = $this->formatearFecha($mensaje->fecha_envio);
            }
        }
        
        // Datos para la vista
        $data = [
            'title' => 'Mensajes - Panel Admin',
            'conversaciones' => $conversaciones,
            'conversacion_activa' => $conversacionActiva,
            'mensajes' => $mensajes,
            'user_id' => $_SESSION['user_id'] ?? 1,
            'is_admin' => true
        ];
        
        // Renderizar vista admin de mensajes
        require_once __DIR__ . '/../views/pages/admin/mensajes.php';
    }
    
    /**
     * Obtener todas las conversaciones del sistema (para admin)
     */
    private function obtenerTodasConversaciones()
    {
        $sql = "SELECT 
                    p.id as publicacion_id,
                    p.titulo as publicacion_titulo,
                    p.foto_principal,
                    m.remitente_id as usuario1_id,
                    m.destinatario_id as usuario2_id,
                    CONCAT(u1.nombre, ' ', u1.apellido) as usuario1_nombre,
                    CONCAT(u2.nombre, ' ', u2.apellido) as usuario2_nombre,
                    u1.rol as usuario1_rol,
                    u2.rol as usuario2_rol,
                    MAX(m.fecha_envio) as ultimo_mensaje_fecha,
                    (SELECT mensaje FROM mensajes 
                     WHERE publicacion_id = p.id 
                     AND ((remitente_id = m.remitente_id AND destinatario_id = m.destinatario_id)
                          OR (remitente_id = m.destinatario_id AND destinatario_id = m.remitente_id))
                     ORDER BY fecha_envio DESC LIMIT 1) as ultimo_mensaje,
                    (SELECT COUNT(*) FROM mensajes 
                     WHERE publicacion_id = p.id 
                     AND ((remitente_id = m.remitente_id AND destinatario_id = m.destinatario_id)
                          OR (remitente_id = m.destinatario_id AND destinatario_id = m.remitente_id))
                     AND leido = 0) as mensajes_no_leidos
                FROM mensajes m
                INNER JOIN publicaciones p ON m.publicacion_id = p.id
                INNER JOIN usuarios u1 ON m.remitente_id = u1.id
                INNER JOIN usuarios u2 ON m.destinatario_id = u2.id
                GROUP BY p.id, m.remitente_id, m.destinatario_id
                ORDER BY ultimo_mensaje_fecha DESC";
        
        $stmt = $this->db->query($sql);
        $conversaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Formatear datos
        foreach ($conversaciones as &$conv) {
            $conv['ultimo_mensaje_fecha_relativa'] = $this->formatearFecha($conv['ultimo_mensaje_fecha']);
            $conv['conversacion_key'] = $conv['publicacion_id'] . '-' . $conv['usuario1_id'] . '-' . $conv['usuario2_id'];
            $conv['conversacion_titulo'] = $conv['usuario1_nombre'] . ' ↔ ' . $conv['usuario2_nombre'];
        }
        
        return $conversaciones;
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
