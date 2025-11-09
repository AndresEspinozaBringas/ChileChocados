<?php
/**
 * ChileChocados - Funciones Auxiliares
 * Funciones helper reutilizables en toda la aplicación
 */
require_once APP_PATH . '/helpers/Icon.php';
/**
 * Sanitizar entrada de usuario
 */
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validar email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Generar URL completa
 */
function url($path = '') {
    $baseUrl = rtrim(APP_URL, '/');
    $path = ltrim($path, '/');
    return $baseUrl . '/' . $path;
}

/**
 * Redireccionar a una URL
 */
function redirect($path = '') {
    header('Location: ' . url($path));
    exit;
}

/**
 * Obtener valor de sesión
 */
function session($key = null, $default = null) {
    if ($key === null) {
        return $_SESSION;
    }
    return $_SESSION[$key] ?? $default;
}

/**
 * Establecer valor de sesión
 */
function setSession($key, $value) {
    $_SESSION[$key] = $value;
}

/**
 * Eliminar valor de sesión
 */
function removeSession($key) {
    if (isset($_SESSION[$key])) {
        unset($_SESSION[$key]);
    }
}

/**
 * Verificar si usuario está autenticado
 */
function isAuthenticated() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Verificar si usuario es administrador
 */
function isAdmin() {
    return isAuthenticated() && ($_SESSION['user_role'] ?? '') === 'admin';
}

/**
 * Obtener usuario actual
 */
function currentUser() {
    if (!isAuthenticated()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'nombre' => $_SESSION['user_nombre'] ?? '',
        'email' => $_SESSION['user_email'] ?? '',
        'role' => $_SESSION['user_role'] ?? 'user'
    ];
}

/**
 * Mostrar mensaje flash
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type, // success, error, warning, info
        'message' => $message
    ];
}

/**
 * Obtener y limpiar mensaje flash
 */
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Formatear precio chileno
 */
function formatPrice($price) {
    if ($price === null || $price === '') {
        return 'A convenir';
    }
    return '$ ' . number_format($price, 0, ',', '.');
}

/**
 * Formatear fecha
 */
function formatDate($date, $format = 'd/m/Y') {
    if (empty($date)) return '';
    return date($format, strtotime($date));
}

/**
 * Formatear fecha en español (localización completa)
 * Retorna fecha formateada en español chileno
 * 
 * @param string|int $date Fecha en formato string o timestamp
 * @param string $format Formato: 'full' (completo), 'short' (corto), 'relative' (relativo)
 * @return string Fecha formateada en español
 */
function formatDateSpanish($date, $format = 'full') {
    if (empty($date)) return '';
    
    $timestamp = is_numeric($date) ? $date : strtotime($date);
    
    // Arrays de traducción
    $dias = [
        'Monday' => 'Lunes',
        'Tuesday' => 'Martes',
        'Wednesday' => 'Miércoles',
        'Thursday' => 'Jueves',
        'Friday' => 'Viernes',
        'Saturday' => 'Sábado',
        'Sunday' => 'Domingo'
    ];
    
    $meses = [
        'January' => 'enero',
        'February' => 'febrero',
        'March' => 'marzo',
        'April' => 'abril',
        'May' => 'mayo',
        'June' => 'junio',
        'July' => 'julio',
        'August' => 'agosto',
        'September' => 'septiembre',
        'October' => 'octubre',
        'November' => 'noviembre',
        'December' => 'diciembre'
    ];
    
    switch ($format) {
        case 'full':
            // Formato: Jueves, 30 de octubre de 2025
            $dia = $dias[date('l', $timestamp)];
            $numero = date('j', $timestamp);
            $mes = $meses[date('F', $timestamp)];
            $anio = date('Y', $timestamp);
            return "{$dia}, {$numero} de {$mes} de {$anio}";
            
        case 'short':
            // Formato: 30 de octubre de 2025
            $numero = date('j', $timestamp);
            $mes = $meses[date('F', $timestamp)];
            $anio = date('Y', $timestamp);
            return "{$numero} de {$mes} de {$anio}";
            
        case 'relative':
            // Formato relativo: Hace 2 horas, Ayer, etc.
            $diff = time() - $timestamp;
            
            if ($diff < 60) {
                return 'Hace un momento';
            } elseif ($diff < 3600) {
                $minutos = floor($diff / 60);
                return "Hace {$minutos} " . ($minutos == 1 ? 'minuto' : 'minutos');
            } elseif ($diff < 86400) {
                $horas = floor($diff / 3600);
                return "Hace {$horas} " . ($horas == 1 ? 'hora' : 'horas');
            } elseif ($diff < 172800) {
                return 'Ayer';
            } elseif ($diff < 604800) {
                $dias = floor($diff / 86400);
                return "Hace {$dias} días";
            } else {
                return formatDateSpanish($date, 'short');
            }
            
        default:
            return date('d/m/Y', $timestamp);
    }
}

/**
 * Subir archivo
 */
function uploadFile($file, $destination = 'uploads/') {
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Error al subir el archivo'];
    }
    
    // Validar tamaño
    if ($file['size'] > UPLOAD_MAX_SIZE) {
        return ['success' => false, 'message' => 'El archivo es demasiado grande'];
    }
    
    // Validar tipo
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, UPLOAD_ALLOWED_TYPES)) {
        return ['success' => false, 'message' => 'Tipo de archivo no permitido'];
    }
    
    // Generar nombre único
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $targetPath = UPLOAD_PATH . '/' . $destination . $filename;
    
    // Crear directorio si no existe
    $directory = dirname($targetPath);
    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }
    
    // Mover archivo
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => true, 'filename' => $filename, 'path' => $destination . $filename];
    }
    
    return ['success' => false, 'message' => 'Error al guardar el archivo'];
}

/**
 * Eliminar archivo
 */
function deleteFile($path) {
    $fullPath = UPLOAD_PATH . '/' . $path;
    if (file_exists($fullPath)) {
        return unlink($fullPath);
    }
    return false;
}

/**
 * Generar token CSRF
 */
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validar token CSRF
 */
function validateCsrfToken($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    return true;
}

/**
 * Logging personalizado
 */
function logMessage($message, $level = 'INFO') {
    $logFile = LOGS_PATH . '/app.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;
    error_log($logMessage, 3, $logFile);
}

/**
 * Validar RUT chileno
 */
function validateRut($rut) {
    $rut = preg_replace('/[^k0-9]/i', '', $rut);
    $dv = substr($rut, -1);
    $numero = substr($rut, 0, strlen($rut) - 1);
    
    $i = 2;
    $suma = 0;
    foreach (array_reverse(str_split($numero)) as $v) {
        if ($i == 8) $i = 2;
        $suma += $v * $i;
        ++$i;
    }
    
    $dvr = 11 - ($suma % 11);
    if ($dvr == 11) $dvr = 0;
    if ($dvr == 10) $dvr = 'K';
    
    return strtoupper($dv) == $dvr;
}

/**
 * Generar slug desde texto
 */
function generateSlug($text) {
    $text = mb_strtolower($text, 'UTF-8');
    $text = str_replace(
        ['á', 'é', 'í', 'ó', 'ú', 'ñ'],
        ['a', 'e', 'i', 'o', 'u', 'n'],
        $text
    );
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

/**
 * Paginación
 */
function paginate($total, $perPage = 12, $currentPage = 1) {
    $totalPages = ceil($total / $perPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $perPage;
    
    return [
        'total' => $total,
        'per_page' => $perPage,
        'current_page' => $currentPage,
        'total_pages' => $totalPages,
        'offset' => $offset,
        'has_prev' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages
    ];
}

/**
 * Cargar layout/vista parcial
 */
function layout($name) {
    $layoutPath = __DIR__ . '/../app/views/layouts/' . $name . '.php';
    if (file_exists($layoutPath)) {
        require $layoutPath;
    }
}

/**
 * Definir constante BASE_URL si no existe
 */
if (!defined('BASE_URL')) {
    define('BASE_URL', APP_URL ?? 'http://localhost');
}

/**
 * Generar URL de asset (CSS, JS, imágenes)
 */
function asset($path) {
    $path = ltrim($path, '/');
    return BASE_URL . '/assets/' . $path;
}

/**
 * Función helper para escapar HTML (alias de sanitize)
 */
function e($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Obtener contadores de notificaciones para el admin
 * Retorna array con contadores de publicaciones pendientes y mensajes sin leer
 */
function getAdminNotifications() {
    // Solo para admins
    if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] !== 'admin') {
        return [
            'publicaciones_pendientes' => 0,
            'mensajes_sin_leer' => 0
        ];
    }
    
    try {
        $db = getDB();
        
        // Contar publicaciones pendientes
        $stmt = $db->query("SELECT COUNT(*) as total FROM publicaciones WHERE estado = 'pendiente'");
        $pendientes = $stmt->fetch(PDO::FETCH_OBJ);
        
        // Contar mensajes sin leer
        $stmt = $db->query("SELECT COUNT(*) as total FROM mensajes WHERE leido = 0");
        $mensajes = $stmt->fetch(PDO::FETCH_OBJ);
        
        return [
            'publicaciones_pendientes' => $pendientes->total ?? 0,
            'mensajes_sin_leer' => $mensajes->total ?? 0
        ];
    } catch (Exception $e) {
        error_log("Error obteniendo notificaciones admin: " . $e->getMessage());
        return [
            'publicaciones_pendientes' => 0,
            'mensajes_sin_leer' => 0
        ];
    }
}

/**
 * Formatear fecha de forma relativa (hace X tiempo)
 * 
 * @param string $fecha Fecha en formato MySQL (Y-m-d H:i:s)
 * @return string Fecha formateada de forma relativa
 */
function timeAgo($fecha) {
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




/**
 * Obtener contador de notificaciones no leídas
 */
function getNotificationCount() {
    if (!isset($_SESSION['user_id'])) {
        return 0;
    }
    
    require_once APP_PATH . '/models/Notificacion.php';
    $notificacionModel = new \App\Models\Notificacion();
    
    // Si es admin, mostrar publicaciones pendientes como "notificaciones"
    if (isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === 'admin') {
        return $notificacionModel->contarPendientesAdmin();
    }
    
    return $notificacionModel->contarNoLeidas($_SESSION['user_id']);
}

/**
 * Obtener contador de mensajes no leídos
 */
function getMessageCount() {
    if (!isset($_SESSION['user_id'])) {
        return 0;
    }
    
    try {
        require_once APP_PATH . '/models/Mensaje.php';
        $mensajeModel = new \App\Models\Mensaje();
        
        // Si es admin, contar TODOS los mensajes sin leer del sistema
        if (isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === 'admin') {
            return $mensajeModel->contarTodosNoLeidos();
        }
        
        return $mensajeModel->contarNoLeidos($_SESSION['user_id']);
    } catch (\Exception $e) {
        error_log("Error al contar mensajes: " . $e->getMessage());
        return 0;
    }
}

/**
 * Obtener notificaciones de un usuario
 */
function getUserNotifications($limit = 10) {
    if (!isset($_SESSION['user_id'])) {
        return [];
    }
    
    require_once APP_PATH . '/models/Notificacion.php';
    $notificacionModel = new \App\Models\Notificacion();
    return $notificacionModel->getByUsuario($_SESSION['user_id'], $limit);
}
