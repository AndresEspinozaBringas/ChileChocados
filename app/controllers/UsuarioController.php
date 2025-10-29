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

class UsuarioController
{
    private $usuarioModel;
    private $publicacionModel;

    public function __construct()
    {
        // TODO: Inicializar modelos cuando estén conectados a BD
        // $this->usuarioModel = new \App\Models\Usuario();
        // $this->publicacionModel = new \App\Models\Publicacion();
    }

    /**
     * Mostrar perfil del usuario autenticado
     */
    public function perfil()
    {
        // Verificar autenticación
        if (!isAuthenticated()) {
            setFlash('error', 'Debes iniciar sesión para ver tu perfil');
            redirect('login');
        }

        $userId = $_SESSION['user_id'];

        // TODO: Conectar con BD en futuras etapas
        // Datos mock del usuario (como array)
        $usuario = [
            'id' => $userId,
            'nombre' => $_SESSION['user_nombre'] ?? 'Usuario',
            'apellido' => $_SESSION['user_apellido'] ?? 'Demo',
            'email' => $_SESSION['user_email'] ?? 'usuario@example.com',
            'telefono' => '+56 9 1234 5678',
            'rut' => '12.345.678-9',
            'rol' => $_SESSION['user_role'] ?? 'vendedor',
            'foto_perfil' => null,
            'fecha_registro' => '2024-10-01',
            'verificado' => 1
        ];


        // Publicaciones activas mock (como arrays)
        $publicacionesActivas = [
            [
                'id' => 1,
                'titulo' => 'Ford Territory 2022 - Chocado Frontal',
                'precio' => 8500000,
                'foto_principal' => 'ford-territory.jpg',
                'estado' => 'aprobada',
                'visitas' => 245,
                'fecha_publicacion' => '2024-10-20'
            ],
            [
                'id' => 2,
                'titulo' => 'Kia Cerato 2020 - Choque Lateral',
                'precio' => 6200000,
                'foto_principal' => 'kia-cerato.jpg',
                'estado' => 'aprobada',
                'visitas' => 189,
                'fecha_publicacion' => '2024-10-18'
            ]
        ];

        // Publicaciones archivadas mock
        $publicacionesArchivadas = [];

        // Estadísticas mock
        $estadisticas = [
            'total_publicaciones' => 2,
            'publicaciones_activas' => 2,
            'publicaciones_pendientes' => 0,
            'ventas_realizadas' => 0,
            'total_visitas' => 434
        ];

        // Cargar vista
        require_once __DIR__ . '/../views/pages/usuarios/profile.php';
    }

    /**
     * Actualizar datos del perfil
     */
    public function actualizarPerfil()
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

        // Validar datos
        $nombre = trim($_POST['nombre'] ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $redes_sociales = trim($_POST['redes_sociales'] ?? '');

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

        if (!empty($telefono)) {
            // Validar formato teléfono chileno
            $telefonoLimpio = preg_replace('/[^0-9]/', '', $telefono);
            if (strlen($telefonoLimpio) < 8 || strlen($telefonoLimpio) > 15) {
                $errors[] = 'El teléfono debe tener entre 8 y 15 dígitos';
            }
        }

        // Si hay errores, regresar
        if (!empty($errors)) {
            setFlashMessage(implode('<br>', $errors), 'error');
            redirect('perfil');
        }

        // Preparar datos para actualizar
        $datosActualizar = [
            'nombre' => $nombre,
            'apellido' => $apellido,
            'telefono' => $telefono ?: null,
            'fecha_actualizacion' => date('Y-m-d H:i:s')
        ];

        // Procesar redes sociales (JSON)
        if (!empty($redes_sociales)) {
            // Si es una URL completa, extraer el usuario
            $redesArray = [];
            $lineas = explode("\n", $redes_sociales);
            
            foreach ($lineas as $linea) {
                $linea = trim($linea);
                if (empty($linea)) continue;
                
                // Detectar tipo de red social
                if (strpos($linea, 'facebook.com') !== false || strpos($linea, 'fb.com') !== false) {
                    $redesArray['facebook'] = $linea;
                } elseif (strpos($linea, 'instagram.com') !== false) {
                    $redesArray['instagram'] = $linea;
                } elseif (strpos($linea, 'twitter.com') !== false || strpos($linea, 'x.com') !== false) {
                    $redesArray['twitter'] = $linea;
                } elseif (strpos($linea, 'whatsapp') !== false || strpos($linea, 'wa.me') !== false) {
                    $redesArray['whatsapp'] = $linea;
                } else {
                    // Si no se detecta, guardar como "otro"
                    $redesArray['otro'] = $linea;
                }
            }
            
            $datosActualizar['redes_sociales'] = !empty($redesArray) ? json_encode($redesArray) : null;
        } else {
            $datosActualizar['redes_sociales'] = null;
        }

        // Actualizar en base de datos
        try {
            $resultado = $this->usuarioModel->update($userId, $datosActualizar);
            
            if ($resultado) {
                // Actualizar datos en sesión
                $_SESSION['user_nombre'] = $nombre;
                $_SESSION['user_apellido'] = $apellido;
                
                setFlash('success', 'Perfil actualizado correctamente');
            } else {
                setFlash('error', 'No se pudo actualizar el perfil');
            }
        } catch (Exception $e) {
            error_log("Error al actualizar perfil: " . $e->getMessage());
            setFlash('error', 'Error al actualizar el perfil. Por favor intenta nuevamente.');
        }

        redirect('perfil');
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

        // TODO: Conectar con BD en futuras etapas
        // Todas las publicaciones del usuario (activas, pendientes, vendidas)
        $publicaciones = [
            [
                'id' => 1,
                'titulo' => 'Ford Territory 2022 - Chocado Frontal',
                'precio' => 8500000,
                'foto_principal' => 'ford-territory.jpg',
                'estado' => 'aprobada',
                'visitas' => 245,
                'fecha_publicacion' => '2024-10-20'
            ],
            [
                'id' => 2,
                'titulo' => 'Kia Cerato 2020 - Choque Lateral',
                'precio' => 6200000,
                'foto_principal' => 'kia-cerato.jpg',
                'estado' => 'aprobada',
                'visitas' => 189,
                'fecha_publicacion' => '2024-10-18'
            ]
        ];

        // Cargar vista
        require_once __DIR__ . '/../views/pages/usuarios/mis-publicaciones.php';
    }

}
