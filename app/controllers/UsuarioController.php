<?php

/**
 * UsuarioController
 * 
 * Controlador para gestión de usuarios y perfiles
 * Maneja visualización y edición de perfiles de usuario
 * 
 * @author ToroDigital
 * @version 1.0
 */

namespace App\Controllers;

use PDO;
use App\Helpers\Auth;
use App\Helpers\Session;

class UsuarioController
{
    private $usuarioModel;
    private $publicacionModel;
    private $db;

    public function __construct()
    {
        $this->db = getDB();
        $this->usuarioModel = new \App\Models\Usuario();
        $this->publicacionModel = new \App\Models\Publicacion();
    }

    /**
     * Mostrar perfil del usuario autenticado
     */
    public function perfil()
    {
        // Verificar autenticación
        Auth::require();

        $userId = $_SESSION['user_id'];

        // Obtener datos del usuario desde la BD
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$userId]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            $_SESSION['error'] = 'Usuario no encontrado';
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        // Obtener estadísticas reales del usuario
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_publicaciones,
                SUM(CASE WHEN estado = 'aprobada' THEN 1 ELSE 0 END) as publicaciones_activas,
                SUM(CASE WHEN estado = 'vendida' THEN 1 ELSE 0 END) as ventas_realizadas,
                SUM(COALESCE(visitas, 0)) as total_visitas
            FROM publicaciones 
            WHERE usuario_id = ?
        ");
        $stmt->execute([$userId]);
        $estadisticas = $stmt->fetch(PDO::FETCH_ASSOC);

        // Obtener publicaciones activas (aprobadas)
        $stmt = $this->db->prepare("
            SELECT p.*, 
                   COALESCE(
                       (SELECT ruta FROM publicacion_fotos WHERE publicacion_id = p.id AND es_principal = 1 LIMIT 1),
                       p.foto_principal
                   ) as foto_principal
            FROM publicaciones p
            WHERE p.usuario_id = ? AND p.estado = 'aprobada'
            ORDER BY p.fecha_publicacion DESC
            LIMIT 10
        ");
        $stmt->execute([$userId]);
        $publicacionesActivas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Obtener publicaciones vendidas
        $stmt = $this->db->prepare("
            SELECT p.*,
                   COALESCE(
                       (SELECT ruta FROM publicacion_fotos WHERE publicacion_id = p.id AND es_principal = 1 LIMIT 1),
                       p.foto_principal
                   ) as foto_principal
            FROM publicaciones p
            WHERE p.usuario_id = ? AND p.estado = 'vendida'
            ORDER BY p.fecha_actualizacion DESC
            LIMIT 10
        ");
        $stmt->execute([$userId]);
        $publicacionesVendidas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Cargar vista
        require_once __DIR__ . '/../views/pages/usuarios/profile.php';
    }

    /**
     * Actualizar datos del perfil
     */
    public function actualizarPerfil()
    {
        // Verificar autenticación
        Auth::require();

        // Verificar método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Método no permitido';
            header('Location: ' . BASE_URL . '/perfil');
            exit;
        }

        // Verificar token CSRF
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de seguridad inválido';
            header('Location: ' . BASE_URL . '/perfil');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $errors = [];

        // Validar datos
        $nombre = trim($_POST['nombre'] ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $rut = trim($_POST['rut'] ?? '');

        // Validaciones
        if (empty($nombre)) {
            $errors[] = 'El nombre es obligatorio';
        } elseif (strlen($nombre) < 2 || strlen($nombre) > 100) {
            $errors[] = 'El nombre debe tener entre 2 y 100 caracteres';
        }

        if (empty($apellido)) {
            $errors[] = 'El apellido es obligatorio';
        } elseif (strlen($apellido) < 2 || strlen($apellido) > 100) {
            $errors[] = 'El apellido debe tener entre 2 y 100 caracteres';
        }

        // Validar email
        if (empty($email)) {
            $errors[] = 'El email es obligatorio';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El email no es válido';
        } else {
            // Verificar que el email no esté en uso por otro usuario
            $stmt = $this->db->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
            $stmt->execute([$email, $userId]);
            if ($stmt->fetch()) {
                $errors[] = 'El email ya está registrado por otro usuario';
            }
        }

        // Validar teléfono chileno
        if (!empty($telefono)) {
            $telefonoLimpio = preg_replace('/[^0-9]/', '', $telefono);
            if (strlen($telefonoLimpio) < 8 || strlen($telefonoLimpio) > 11) {
                $errors[] = 'El teléfono debe tener entre 8 y 11 dígitos';
            } elseif (!preg_match('/^(\+?56)?9\d{8}$/', $telefonoLimpio)) {
                $errors[] = 'El teléfono debe ser un número chileno válido (ej: +56912345678 o 912345678)';
            }
        }

        // Validar RUT chileno
        if (!empty($rut)) {
            if (!validateRut($rut)) {
                $errors[] = 'El RUT no es válido';
            }
        }

        // Si hay errores, regresar
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            header('Location: ' . BASE_URL . '/perfil');
            exit;
        }

        // Actualizar en base de datos
        try {
            $stmt = $this->db->prepare("
                UPDATE usuarios 
                SET nombre = ?, 
                    apellido = ?, 
                    email = ?,
                    telefono = ?, 
                    rut = ?,
                    fecha_actualizacion = NOW()
                WHERE id = ?
            ");
            $resultado = $stmt->execute([
                $nombre,
                $apellido,
                $email,
                $telefono ?: null,
                $rut ?: null,
                $userId
            ]);
            
            if ($resultado) {
                // Actualizar datos en sesión
                $_SESSION['user_nombre'] = $nombre;
                $_SESSION['user_apellido'] = $apellido;
                $_SESSION['user_email'] = $email;
                
                $_SESSION['success'] = 'Perfil actualizado correctamente';
            } else {
                $_SESSION['error'] = 'No se pudo actualizar el perfil';
            }
        } catch (\Exception $e) {
            error_log("Error al actualizar perfil: " . $e->getMessage());
            $_SESSION['error'] = 'Error al actualizar el perfil. Por favor intenta nuevamente.';
        }

        header('Location: ' . BASE_URL . '/perfil');
        exit;
    }

    /**
     * Actualizar avatar del usuario
     */
    public function actualizarAvatar()
    {
        // Verificar autenticación
        Auth::require();

        // Verificar método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        // Verificar token CSRF
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Token de seguridad inválido']);
            exit;
        }

        $userId = $_SESSION['user_id'];

        // Verificar que se subió un archivo
        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'No se recibió ningún archivo']);
            exit;
        }

        $file = $_FILES['avatar'];

        // Validar tipo de archivo
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Tipo de archivo no permitido. Solo JPG, PNG o WebP']);
            exit;
        }

        // Validar tamaño (máximo 2MB)
        if ($file['size'] > 2 * 1024 * 1024) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'El archivo no debe superar 2MB']);
            exit;
        }

        try {
            // Obtener avatar anterior para eliminarlo
            $stmt = $this->db->prepare("SELECT avatar FROM usuarios WHERE id = ?");
            $stmt->execute([$userId]);
            $avatarAnterior = $stmt->fetchColumn();

            // Crear imagen desde el archivo subido
            switch ($mimeType) {
                case 'image/jpeg':
                case 'image/jpg':
                    $imagen = imagecreatefromjpeg($file['tmp_name']);
                    break;
                case 'image/png':
                    $imagen = imagecreatefrompng($file['tmp_name']);
                    break;
                case 'image/webp':
                    $imagen = imagecreatefromwebp($file['tmp_name']);
                    break;
                default:
                    throw new \Exception('Tipo de imagen no soportado');
            }

            if (!$imagen) {
                throw new \Exception('No se pudo procesar la imagen');
            }

            // Obtener dimensiones originales
            $anchoOriginal = imagesx($imagen);
            $altoOriginal = imagesy($imagen);

            // Crear thumbnail cuadrado de 200x200
            $tamanoThumb = 200;
            $thumbnail = imagecreatetruecolor($tamanoThumb, $tamanoThumb);

            // Preservar transparencia para PNG
            if ($mimeType === 'image/png') {
                imagealphablending($thumbnail, false);
                imagesavealpha($thumbnail, true);
                $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
                imagefilledrectangle($thumbnail, 0, 0, $tamanoThumb, $tamanoThumb, $transparent);
            }

            // Calcular dimensiones para crop centrado
            $lado = min($anchoOriginal, $altoOriginal);
            $x = ($anchoOriginal - $lado) / 2;
            $y = ($altoOriginal - $lado) / 2;

            // Redimensionar y hacer crop
            imagecopyresampled(
                $thumbnail, $imagen,
                0, 0, $x, $y,
                $tamanoThumb, $tamanoThumb,
                $lado, $lado
            );

            // Generar nombre único para el archivo
            $extension = $mimeType === 'image/png' ? 'png' : 'jpg';
            $nombreArchivo = 'avatar_' . $userId . '_' . time() . '.' . $extension;
            $rutaDestino = __DIR__ . '/../../public/uploads/avatars/' . $nombreArchivo;

            // Guardar thumbnail
            if ($extension === 'png') {
                imagepng($thumbnail, $rutaDestino, 9);
            } else {
                imagejpeg($thumbnail, $rutaDestino, 90);
            }

            // Liberar memoria
            imagedestroy($imagen);
            imagedestroy($thumbnail);

            // Actualizar base de datos
            $stmt = $this->db->prepare("UPDATE usuarios SET avatar = ?, fecha_actualizacion = NOW() WHERE id = ?");
            $stmt->execute([$nombreArchivo, $userId]);

            // Actualizar sesión
            $_SESSION['user_avatar'] = $nombreArchivo;

            // Eliminar avatar anterior si existe
            if ($avatarAnterior && file_exists(__DIR__ . '/../../public/uploads/avatars/' . $avatarAnterior)) {
                unlink(__DIR__ . '/../../public/uploads/avatars/' . $avatarAnterior);
            }

            echo json_encode([
                'success' => true,
                'message' => 'Avatar actualizado correctamente',
                'avatar' => $nombreArchivo
            ]);

        } catch (\Exception $e) {
            error_log("Error al actualizar avatar: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al procesar la imagen: ' . $e->getMessage()
            ]);
        }

        exit;
    }

    /**
     * Cambiar contraseña del usuario
     */
    public function cambiarPassword()
    {
        // Verificar autenticación
        if (!isAuthenticated()) {
            echo json_encode(['success' => false, 'message' => 'No autenticado']);
            exit;
        }

        // Verificar método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            setFlash('error', 'Método no permitido');
            redirect('perfil');
        }

        // Verificar token CSRF
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlash('error', 'Token de seguridad inválido');
            redirect('perfil');
        }

        $userId = $_SESSION['user_id'];
        $errors = [];

        // Obtener datos del formulario
        $passwordActual = $_POST['password_actual'] ?? '';
        $passwordNueva = $_POST['password_nueva'] ?? '';
        $passwordConfirm = $_POST['password_confirmar'] ?? '';

        // Validaciones
        if (empty($passwordActual)) {
            $errors[] = 'Debes ingresar tu contraseña actual';
        }

        if (empty($passwordNueva)) {
            $errors[] = 'Debes ingresar una nueva contraseña';
        } elseif (strlen($passwordNueva) < 8) {
            $errors[] = 'La nueva contraseña debe tener al menos 8 caracteres';
        } elseif (!preg_match('/[A-Z]/', $passwordNueva)) {
            $errors[] = 'La nueva contraseña debe contener al menos una mayúscula';
        } elseif (!preg_match('/[a-z]/', $passwordNueva)) {
            $errors[] = 'La nueva contraseña debe contener al menos una minúscula';
        } elseif (!preg_match('/[0-9]/', $passwordNueva)) {
            $errors[] = 'La nueva contraseña debe contener al menos un número';
        }

        if ($passwordNueva !== $passwordConfirm) {
            $errors[] = 'Las contraseñas no coinciden';
        }

        // Si hay errores, regresar
        if (!empty($errors)) {
            setFlashMessage(implode('<br>', $errors), 'error');
            redirect('perfil');
        }

        // Verificar contraseña actual
        $usuario = $this->usuarioModel->find($userId);
        
        if (!$usuario || !password_verify($passwordActual, $usuario['password'])) {
            setFlash('error', 'La contraseña actual es incorrecta');
            redirect('perfil');
        }

        // Hash de la nueva contraseña
        $passwordHash = password_hash($passwordNueva, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3
        ]);

        // Actualizar contraseña
        try {
            $resultado = $this->usuarioModel->update($userId, [
                'password' => $passwordHash,
                'fecha_actualizacion' => date('Y-m-d H:i:s')
            ]);

            if ($resultado) {
                setFlash('success', 'Contraseña actualizada correctamente');
                
                // Opcional: Enviar email de notificación
                // $this->enviarEmailCambioPassword($usuario['email']);
            } else {
                setFlash('error', 'No se pudo actualizar la contraseña');
            }
        } catch (Exception $e) {
            error_log("Error al cambiar contraseña: " . $e->getMessage());
            setFlash('error', 'Error al cambiar la contraseña. Por favor intenta nuevamente.');
        }

        redirect('perfil');
    }

    /**
     * Subir foto de perfil
     */
    public function subirFoto()
    {
        // Verificar autenticación
        if (!isAuthenticated()) {
            echo json_encode(['success' => false, 'message' => 'No autenticado']);
            exit;
        }

        // Verificar método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        // Verificar que se subió un archivo
        if (!isset($_FILES['foto_perfil']) || $_FILES['foto_perfil']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'No se subió ninguna imagen']);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $archivo = $_FILES['foto_perfil'];

        // Validar tipo de archivo
        $tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($archivo['type'], $tiposPermitidos)) {
            echo json_encode(['success' => false, 'message' => 'Tipo de archivo no permitido. Solo JPG, PNG, GIF o WEBP']);
            exit;
        }

        // Validar tamaño (máximo 5MB)
        if ($archivo['size'] > 5242880) {
            echo json_encode(['success' => false, 'message' => 'La imagen es muy grande. Máximo 5MB']);
            exit;
        }

        // Crear directorio si no existe
        $directorioBase = $_SERVER['DOCUMENT_ROOT'] . '/uploads/avatars';
        if (!is_dir($directorioBase)) {
            mkdir($directorioBase, 0755, true);
        }

        // Generar nombre único para el archivo
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombreArchivo = 'avatar_' . $userId . '_' . uniqid() . '.' . $extension;
        $rutaCompleta = $directorioBase . '/' . $nombreArchivo;

        // Mover archivo
        if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            
            // Eliminar foto anterior si existe
            $usuario = $this->usuarioModel->find($userId);
            if ($usuario && $usuario['foto_perfil']) {
                $fotoAnterior = $_SERVER['DOCUMENT_ROOT'] . '/uploads/avatars/' . $usuario['foto_perfil'];
                if (file_exists($fotoAnterior)) {
                    unlink($fotoAnterior);
                }
            }

            // Actualizar en base de datos
            $resultado = $this->usuarioModel->update($userId, [
                'foto_perfil' => $nombreArchivo,
                'fecha_actualizacion' => date('Y-m-d H:i:s')
            ]);

            if ($resultado) {
                $_SESSION['user_avatar'] = $nombreArchivo;
                echo json_encode([
                    'success' => true, 
                    'message' => 'Foto actualizada correctamente',
                    'url' => BASE_URL . '/uploads/avatars/' . $nombreArchivo
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al guardar en base de datos']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al subir la imagen']);
        }
        exit;
    }

    /**
     * Ver perfil público de otro usuario
     */
    public function verPerfil($userId)
    {
        // Obtener datos del usuario
        $usuario = $this->usuarioModel->find($userId);
        
        if (!$usuario || $usuario['estado'] !== 'activo') {
            setFlash('error', 'Usuario no encontrado');
            redirect('/');
        }

        // Obtener solo publicaciones aprobadas y activas del usuario
        $publicaciones = $this->publicacionModel->query(
            "SELECT id, titulo, precio, foto_principal, visitas, fecha_publicacion
             FROM publicaciones 
             WHERE usuario_id = ? 
             AND estado = 'aprobada'
             ORDER BY fecha_publicacion DESC
             LIMIT 20",
            [$userId]
        );

        // Estadísticas públicas
        $estadisticas = $this->usuarioModel->query(
            "SELECT 
                COUNT(*) as total_publicaciones,
                SUM(visitas) as total_visitas
             FROM publicaciones 
             WHERE usuario_id = ? AND estado = 'aprobada'",
            [$userId]
        );

        $stats = $estadisticas[0] ?? [
            'total_publicaciones' => 0,
            'total_visitas' => 0
        ];

        // Cargar vista
        $this->view('pages/usuarios/perfil-publico', [
            'usuario' => $usuario,
            'publicaciones' => $publicaciones,
            'estadisticas' => $stats
        ]);
    }

    /**
     * Mostrar mis publicaciones
     * Ruta: GET /mis-publicaciones
     */
    public function misPublicaciones()
    {
        // Verificar autenticación
        if (!isAuthenticated()) {
            setFlash('error', 'Debes iniciar sesión para ver tus publicaciones');
            redirect('login');
        }

        $userId = $_SESSION['user_id'];

        // Obtener publicaciones del usuario desde la BD (incluyendo borradores y motivo de rechazo)
        $stmt = $this->db->prepare("
            SELECT p.*,
                   p.visitas,
                   p.fecha_creacion,
                   p.fecha_publicacion,
                   p.motivo_rechazo,
                   COALESCE(
                       (SELECT ruta FROM publicacion_fotos WHERE publicacion_id = p.id AND es_principal = 1 LIMIT 1),
                       (SELECT ruta FROM publicacion_fotos WHERE publicacion_id = p.id ORDER BY orden LIMIT 1),
                       p.foto_principal
                   ) as foto_principal
            FROM publicaciones p
            WHERE p.usuario_id = ?
            ORDER BY 
                CASE 
                    WHEN p.estado = 'borrador' THEN 1
                    WHEN p.estado = 'pendiente' THEN 2
                    WHEN p.estado = 'aprobada' THEN 3
                    WHEN p.estado = 'rechazada' THEN 4
                    ELSE 5
                END,
                p.fecha_creacion DESC
        ");
        $stmt->execute([$userId]);
        $publicaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Cargar vista
        require_once __DIR__ . '/../views/pages/usuarios/mis-publicaciones.php';
    }

     public function usuarios()
    {
        $this->requireAdmin();

        // Obtener filtros
        $rol = $_GET['rol'] ?? '';
        $estado = $_GET['estado'] ?? '';
        $busqueda = $_GET['q'] ?? '';
        $page = max(1, (int)($_GET['page'] ?? 1));
        $per_page = 20;
        $offset = ($page - 1) * $per_page;

        // Construir query base
        $sql = "SELECT u.*,
                       COUNT(DISTINCT p.id) as total_publicaciones,
                       COUNT(DISTINCT CASE WHEN p.estado = 'aprobada' THEN p.id END) as publicaciones_aprobadas,
                       COUNT(DISTINCT m.id) as total_mensajes
                FROM usuarios u
                LEFT JOIN publicaciones p ON u.id = p.usuario_id
                LEFT JOIN mensajes m ON u.id = m.remitente_id OR u.id = m.destinatario_id
                WHERE 1=1";

        $params = [];

        // Filtro por rol
        if ($rol && $rol !== 'todos') {
            $sql .= " AND u.rol = ?";
            $params[] = $rol;
        }

        // Filtro por estado
        if ($estado && $estado !== 'todos') {
            $sql .= " AND u.estado = ?";
            $params[] = $estado;
        }

        // Búsqueda
        if ($busqueda) {
            $sql .= " AND (u.nombre LIKE ? OR u.apellido LIKE ? OR u.email LIKE ? OR u.rut LIKE ?)";
            $search = "%{$busqueda}%";
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        $sql .= " GROUP BY u.id ORDER BY u.fecha_registro DESC LIMIT ? OFFSET ?";
        $params[] = $per_page;
        $params[] = $offset;

        // Ejecutar query
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $usuarios = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Contar total para paginación
        $sqlCount = "SELECT COUNT(DISTINCT u.id) as total
                     FROM usuarios u
                     WHERE 1=1";
        $paramsCount = [];

        if ($rol && $rol !== 'todos') {
            $sqlCount .= " AND u.rol = ?";
            $paramsCount[] = $rol;
        }

        if ($estado && $estado !== 'todos') {
            $sqlCount .= " AND u.estado = ?";
            $paramsCount[] = $estado;
        }

        if ($busqueda) {
            $sqlCount .= " AND (u.nombre LIKE ? OR u.apellido LIKE ? OR u.email LIKE ? OR u.rut LIKE ?)";
            $paramsCount[] = "%{$busqueda}%";
            $paramsCount[] = "%{$busqueda}%";
            $paramsCount[] = "%{$busqueda}%";
            $paramsCount[] = "%{$busqueda}%";
        }

        $stmt = $this->db->prepare($sqlCount);
        $stmt->execute($paramsCount);
        $totalUsuarios = $stmt->fetch(PDO::FETCH_OBJ)->total;
        $totalPages = ceil($totalUsuarios / $per_page);

        // Obtener estadísticas generales
        $stats = $this->db->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN rol = 'admin' THEN 1 ELSE 0 END) as admins,
                SUM(CASE WHEN rol = 'vendedor' THEN 1 ELSE 0 END) as vendedores,
                SUM(CASE WHEN rol = 'comprador' THEN 1 ELSE 0 END) as compradores,
                SUM(CASE WHEN estado = 'activo' THEN 1 ELSE 0 END) as activos,
                SUM(CASE WHEN estado = 'suspendido' THEN 1 ELSE 0 END) as suspendidos,
                SUM(CASE WHEN estado = 'eliminado' THEN 1 ELSE 0 END) as eliminados
            FROM usuarios
        ")->fetch(PDO::FETCH_OBJ);

        // Preparar datos para la vista
        $pageTitle = 'Gestión de Usuarios - Admin';
        $currentPage = 'admin-usuarios';
        $filtros = [
            'rol' => $rol,
            'estado' => $estado,
            'busqueda' => $busqueda
        ];
        $pagination = [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_items' => $totalUsuarios,
            'per_page' => $per_page
        ];
        $csrf_token = generateCsrfToken();

        require_once APP_PATH . '/views/pages/admin/usuarios.php';
    }

    /**
     * Ver detalle completo de un usuario
     * Ruta: GET /admin/usuarios/{id}
     */
    public function verUsuario($id)
    {
        $this->requireAdmin();

        // Obtener usuario
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        $usuario = $stmt->fetch(PDO::FETCH_OBJ);

        if (!$usuario) {
            Session::flash('error', 'Usuario no encontrado');
            header('Location: ' . BASE_URL . '/admin/usuarios');
            exit;
        }

        // Obtener publicaciones del usuario
        $stmt = $this->db->prepare("
            SELECT p.*, 
                   cp.nombre as categoria_nombre,
                   r.nombre as region_nombre
            FROM publicaciones p
            LEFT JOIN categorias_padre cp ON p.categoria_padre_id = cp.id
            LEFT JOIN regiones r ON p.region_id = r.id
            WHERE p.usuario_id = ?
            ORDER BY p.fecha_creacion DESC
            LIMIT 10
        ");
        $stmt->execute([$id]);
        $publicaciones = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Obtener historial de auditoría
        $stmt = $this->db->prepare("
            SELECT a.*, 
                   u.nombre as admin_nombre,
                   u.apellido as admin_apellido
            FROM auditoria a
            LEFT JOIN usuarios u ON a.usuario_id = u.id
            WHERE a.tabla = 'usuarios' AND a.registro_id = ?
            ORDER BY a.fecha DESC
            LIMIT 20
        ");
        $stmt->execute([$id]);
        $historial = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Obtener estadísticas del usuario
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(DISTINCT p.id) as total_publicaciones,
                COUNT(DISTINCT CASE WHEN p.estado = 'aprobada' THEN p.id END) as publicaciones_aprobadas,
                COUNT(DISTINCT CASE WHEN p.estado = 'pendiente' THEN p.id END) as publicaciones_pendientes,
                COUNT(DISTINCT CASE WHEN p.estado = 'rechazada' THEN p.id END) as publicaciones_rechazadas,
                COUNT(DISTINCT m.id) as total_mensajes,
                COUNT(DISTINCT f.id) as total_favoritos
            FROM usuarios u
            LEFT JOIN publicaciones p ON u.id = p.usuario_id
            LEFT JOIN mensajes m ON u.id = m.remitente_id OR u.id = m.destinatario_id
            LEFT JOIN favoritos f ON u.id = f.usuario_id
            WHERE u.id = ?
        ");
        $stmt->execute([$id]);
        $estadisticas = $stmt->fetch(PDO::FETCH_OBJ);

        $pageTitle = "Usuario: {$usuario->nombre} {$usuario->apellido}";
        $currentPage = 'admin-usuarios';
        $csrf_token = generateCsrfToken();

        require_once APP_PATH . '/views/pages/admin/usuario-detalle.php';
    }

    /**
     * Actualizar datos de un usuario
     * Ruta: POST /admin/usuarios/{id}/actualizar
     */
    public function actualizarUsuario($id)
    {
        $this->requireAdmin();

        // Validar CSRF
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'Token de seguridad inválido');
            header('Location: ' . BASE_URL . '/admin/usuarios/' . $id);
            exit;
        }

        // Obtener datos actuales del usuario
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        $usuarioAnterior = $stmt->fetch(PDO::FETCH_OBJ);

        if (!$usuarioAnterior) {
            Session::flash('error', 'Usuario no encontrado');
            header('Location: ' . BASE_URL . '/admin/usuarios');
            exit;
        }

        // Validar y sanitizar datos
        $nombre = sanitize($_POST['nombre'] ?? '');
        $apellido = sanitize($_POST['apellido'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $telefono = sanitize($_POST['telefono'] ?? '');
        $rut = sanitize($_POST['rut'] ?? '');
        $rol = sanitize($_POST['rol'] ?? '');

        // Validaciones
        $errores = [];

        if (empty($nombre)) $errores[] = 'El nombre es obligatorio';
        if (empty($apellido)) $errores[] = 'El apellido es obligatorio';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'Email inválido';
        }
        if (!in_array($rol, ['admin', 'vendedor', 'comprador'])) {
            $errores[] = 'Rol inválido';
        }

        // Verificar email único (si cambió)
        if ($email !== $usuarioAnterior->email) {
            $stmt = $this->db->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
            $stmt->execute([$email, $id]);
            if ($stmt->fetch()) {
                $errores[] = 'El email ya está registrado';
            }
        }

        if (!empty($errores)) {
            Session::flash('error', implode('<br>', $errores));
            header('Location: ' . BASE_URL . '/admin/usuarios/' . $id);
            exit;
        }

        // Actualizar usuario
        $stmt = $this->db->prepare("
            UPDATE usuarios 
            SET nombre = ?,
                apellido = ?,
                email = ?,
                telefono = ?,
                rut = ?,
                rol = ?,
                fecha_actualizacion = NOW()
            WHERE id = ?
        ");
        $stmt->execute([
            $nombre,
            $apellido,
            $email,
            $telefono,
            $rut,
            $rol,
            $id
        ]);

        // Registrar en auditoría
        $stmt = $this->db->prepare("
            INSERT INTO auditoria (usuario_id, tabla, registro_id, accion, datos_anteriores, datos_nuevos, ip)
            VALUES (?, 'usuarios', ?, 'actualizar', ?, ?, ?)
        ");
        $stmt->execute([
            Auth::id(),
            $id,
            json_encode([
                'nombre' => $usuarioAnterior->nombre,
                'apellido' => $usuarioAnterior->apellido,
                'email' => $usuarioAnterior->email,
                'telefono' => $usuarioAnterior->telefono,
                'rut' => $usuarioAnterior->rut,
                'rol' => $usuarioAnterior->rol
            ]),
            json_encode([
                'nombre' => $nombre,
                'apellido' => $apellido,
                'email' => $email,
                'telefono' => $telefono,
                'rut' => $rut,
                'rol' => $rol,
                'actualizado_por' => Auth::id()
            ]),
            $_SERVER['REMOTE_ADDR'] ?? null
        ]);

        Session::flash('success', 'Usuario actualizado exitosamente');
        header('Location: ' . BASE_URL . '/admin/usuarios/' . $id);
        exit;
    }

    /**
     * Cambiar estado de un usuario (activar/suspender)
     * Ruta: POST /admin/usuarios/{id}/cambiar-estado
     */
    public function cambiarEstadoUsuario($id)
    {
        $this->requireAdmin();

        // Validar CSRF
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'Token de seguridad inválido');
            header('Location: ' . BASE_URL . '/admin/usuarios/' . $id);
            exit;
        }

        $nuevoEstado = sanitize($_POST['estado'] ?? '');
        $motivo = sanitize($_POST['motivo'] ?? '');

        if (!in_array($nuevoEstado, ['activo', 'suspendido'])) {
            Session::flash('error', 'Estado inválido');
            header('Location: ' . BASE_URL . '/admin/usuarios/' . $id);
            exit;
        }

        // Obtener usuario
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        $usuario = $stmt->fetch(PDO::FETCH_OBJ);

        if (!$usuario) {
            Session::flash('error', 'Usuario no encontrado');
            header('Location: ' . BASE_URL . '/admin/usuarios');
            exit;
        }

        // No permitir suspender el propio usuario admin
        if ($id == Auth::id()) {
            Session::flash('error', 'No puedes cambiar tu propio estado');
            header('Location: ' . BASE_URL . '/admin/usuarios/' . $id);
            exit;
        }

        $estadoAnterior = $usuario->estado;

        // Actualizar estado
        $stmt = $this->db->prepare("
            UPDATE usuarios 
            SET estado = ?,
                fecha_actualizacion = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$nuevoEstado, $id]);

        // Registrar en auditoría
        $stmt = $this->db->prepare("
            INSERT INTO auditoria (usuario_id, tabla, registro_id, accion, datos_anteriores, datos_nuevos, ip)
            VALUES (?, 'usuarios', ?, 'actualizar', ?, ?, ?)
        ");
        $stmt->execute([
            Auth::id(),
            $id,
            json_encode(['estado' => $estadoAnterior]),
            json_encode([
                'estado' => $nuevoEstado,
                'motivo' => $motivo,
                'cambiado_por' => Auth::id()
            ]),
            $_SERVER['REMOTE_ADDR'] ?? null
        ]);

        // Enviar notificación al usuario
        $this->enviarNotificacionCambioEstado($usuario, $nuevoEstado, $motivo);

        $mensaje = $nuevoEstado === 'suspendido' 
            ? 'Usuario suspendido exitosamente' 
            : 'Usuario activado exitosamente';

        Session::flash('success', $mensaje);
        header('Location: ' . BASE_URL . '/admin/usuarios/' . $id);
        exit;
    }

    /**
     * Eliminar un usuario (soft delete)
     * Ruta: POST /admin/usuarios/{id}/eliminar
     */
    public function eliminarUsuario($id)
    {
        $this->requireAdmin();

        // Validar CSRF
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'Token de seguridad inválido');
            header('Location: ' . BASE_URL . '/admin/usuarios/' . $id);
            exit;
        }

        // No permitir eliminar el propio usuario admin
        if ($id == Auth::id()) {
            Session::flash('error', 'No puedes eliminar tu propio usuario');
            header('Location: ' . BASE_URL . '/admin/usuarios/' . $id);
            exit;
        }

        // Obtener usuario
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        $usuario = $stmt->fetch(PDO::FETCH_OBJ);

        if (!$usuario) {
            Session::flash('error', 'Usuario no encontrado');
            header('Location: ' . BASE_URL . '/admin/usuarios');
            exit;
        }

        // Soft delete: cambiar estado a 'eliminado'
        $stmt = $this->db->prepare("
            UPDATE usuarios 
            SET estado = 'eliminado',
                fecha_actualizacion = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$id]);

        // Registrar en auditoría
        $stmt = $this->db->prepare("
            INSERT INTO auditoria (usuario_id, tabla, registro_id, accion, datos_anteriores, ip)
            VALUES (?, 'usuarios', ?, 'eliminar', ?, ?)
        ");
        $stmt->execute([
            Auth::id(),
            $id,
            json_encode([
                'usuario' => "{$usuario->nombre} {$usuario->apellido}",
                'email' => $usuario->email,
                'eliminado_por' => Auth::id()
            ]),
            $_SERVER['REMOTE_ADDR'] ?? null
        ]);

        Session::flash('success', 'Usuario eliminado exitosamente');
        header('Location: ' . BASE_URL . '/admin/usuarios');
        exit;
    }

    /**
     * Ver historial de actividad de un usuario
     * Ruta: GET /admin/usuarios/{id}/historial
     */
    public function historialUsuario($id)
    {
        $this->requireAdmin();

        // Obtener usuario
        $stmt = $this->db->prepare("SELECT nombre, apellido, email FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        $usuario = $stmt->fetch(PDO::FETCH_OBJ);

        if (!$usuario) {
            Session::flash('error', 'Usuario no encontrado');
            header('Location: ' . BASE_URL . '/admin/usuarios');
            exit;
        }

        // Obtener historial completo
        $stmt = $this->db->prepare("
            SELECT a.*, 
                   u.nombre as admin_nombre,
                   u.apellido as admin_apellido
            FROM auditoria a
            LEFT JOIN usuarios u ON a.usuario_id = u.id
            WHERE (a.tabla = 'usuarios' AND a.registro_id = ?)
               OR (a.tabla = 'publicaciones' AND a.registro_id IN (
                   SELECT id FROM publicaciones WHERE usuario_id = ?
               ))
            ORDER BY a.fecha DESC
            LIMIT 100
        ");
        $stmt->execute([$id, $id]);
        $historial = $stmt->fetchAll(PDO::FETCH_OBJ);

        $pageTitle = "Historial de Actividad - {$usuario->nombre} {$usuario->apellido}";
        $currentPage = 'admin-usuarios';
        $usuario_id = $id;

        require_once APP_PATH . '/views/pages/admin/usuario-historial.php';
    }

    /**
     * Enviar notificación de cambio de estado
     */
    private function enviarNotificacionCambioEstado($usuario, $nuevoEstado, $motivo)
    {
        if ($nuevoEstado === 'suspendido') {
            $asunto = 'Tu cuenta ha sido suspendida - ChileChocados';
            $mensaje = "
                Hola {$usuario->nombre},
                
                Tu cuenta en ChileChocados ha sido suspendida por el siguiente motivo:
                
                {$motivo}
                
                Si consideras que esto es un error o deseas apelar esta decisión,
                por favor contacta a nuestro equipo de soporte.
                
                --
                Equipo ChileChocados
            ";
        } else {
            $asunto = 'Tu cuenta ha sido reactivada - ChileChocados';
            $mensaje = "
                Hola {$usuario->nombre},
                
                Nos complace informarte que tu cuenta en ChileChocados 
                ha sido reactivada y ya puedes volver a utilizar todos los servicios.
                
                ¡Bienvenido de vuelta!
                
                --
                Equipo ChileChocados
            ";
        }

        error_log("EMAIL CAMBIO ESTADO: Para {$usuario->email} - Estado: {$nuevoEstado}");
        
        // Aquí iría la lógica real de envío de email
        // sendEmail($usuario->email, $asunto, $mensaje);
    }

    // ====================================
    // ALIAS DE MÉTODOS PARA RUTAS ADMIN
    // ====================================

    /**
     * Alias para usuarios() - Listado de usuarios (admin)
     */
    public function adminListar()
    {
        return $this->usuarios();
    }

    /**
     * Alias para verUsuario() - Ver detalle de usuario (admin)
     */
    public function adminDetalle($id)
    {
        return $this->verUsuario($id);
    }

    /**
     * Alias para actualizarUsuario() - Actualizar usuario (admin)
     */
    public function adminActualizar($id)
    {
        return $this->actualizarUsuario($id);
    }

    /**
     * Alias para cambiarEstadoUsuario() - Cambiar estado (admin)
     */
    public function adminCambiarEstado($id)
    {
        return $this->cambiarEstadoUsuario($id);
    }

    /**
     * Alias para eliminarUsuario() - Eliminar usuario (admin)
     */
    public function adminEliminar($id)
    {
        return $this->eliminarUsuario($id);
    }

    /**
     * Alias para historialUsuario() - Ver historial (admin)
     */
    public function adminHistorial($id)
    {
        return $this->historialUsuario($id);
    }

    /**
     * Verificar que el usuario tiene permisos de administrador
     */
    private function requireAdmin()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'admin') {
            setFlash('error', 'Acceso denegado. Debes ser administrador.');
            redirect('login');
        }
    }

}
