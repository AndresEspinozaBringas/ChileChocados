<?php
/**
 * ChileChocados - Funciones Auxiliares
 * Funciones helper reutilizables en toda la aplicación
 */

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
 * Generar icono SVG usando Lucide
 */
function icon($name, $size = 24, $class = '') {
    $classAttr = $class ? ' class="' . $class . '"' : '';
    return '<i data-lucide="' . $name . '" width="' . $size . '" height="' . $size . '"' . $classAttr . '></i>';
}

/**
 * Cargar layout/vista parcial
 */
function layout($name) {
    $layoutPath = APP_PATH . '/views/layouts/' . $name . '.php';
    if (file_exists($layoutPath)) {
        require $layoutPath;
    } else {
        error_log("Layout no encontrado: $layoutPath");
    }
}

/**
 * Función helper para escapar HTML (alias de sanitize)
 */
function e($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}
