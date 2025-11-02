<?php
/**
 * ChileChocados - Front Controller
 * Enrutamiento principal de la aplicación
 * MODIFICADO: /admin/login redirige a /login
 */

// Cargar configuración y archivos esenciales (config.php maneja la sesión)
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';

// Cargar helpers
require_once APP_PATH . '/helpers/Session.php';
require_once APP_PATH . '/helpers/Auth.php';

// Log de debugging POST
$logFile = __DIR__ . '/../logs/post_debug.log';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    file_put_contents($logFile, "\n\n=== POST REQUEST ===\n", FILE_APPEND);
    file_put_contents($logFile, "Time: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    file_put_contents($logFile, "GET url param: " . ($_GET['url'] ?? 'NO URL') . "\n", FILE_APPEND);
}

// Obtener la URL solicitada
$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

// DEBUG: Log de la URL parseada
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    file_put_contents($logFile, "URL array: " . print_r($url, true) . "\n", FILE_APPEND);
}

// Determinar controlador y acción
$controllerName = !empty($url[0]) ? ucfirst($url[0]) . 'Controller' : 'HomeController';
$method = $url[1] ?? 'index';
$params = array_slice($url, 2);

// ====================================
// MAPA DE RUTAS ESPECIALES
// ====================================

$specialRoutes = [
    // Rutas de autenticación
    'registro' => ['controller' => 'AuthController', 'method' => 'register'],
    'login' => ['controller' => 'AuthController', 'method' => 'login'],
    'logout' => ['controller' => 'AuthController', 'method' => 'logout'],
    'recuperar-contrasena' => ['controller' => 'AuthController', 'method' => 'forgotPassword'],
    'reset-password' => ['controller' => 'AuthController', 'method' => 'resetPassword'],
    'verificar-email' => ['controller' => 'AuthController', 'method' => 'verifyEmail'],
    // Rutas legales
    'terminos' => ['controller' => 'LegalController', 'method' => 'terminos'],
    'privacidad' => ['controller' => 'LegalController', 'method' => 'privacidad'],
    'cookies' => ['controller' => 'LegalController', 'method' => 'cookies'],
    'denuncias' => ['controller' => 'LegalController', 'method' => 'denuncias'],
    // Ruta de contacto
    'contacto' => ['controller' => 'ContactoController', 'method' => 'index'],
    
    // Rutas API
    'api' => ['controller' => 'ApiController', 'method' => 'index'],
    
    // ====================================
    // NUEVAS RUTAS - PUBLICACIONES
    // ====================================
    'publicaciones' => ['controller' => 'PublicacionController', 'method' => 'index'],
    'publicacion' => ['controller' => 'PublicacionController', 'method' => 'show'],
    'detalle' => ['controller' => 'PublicacionController', 'method' => 'show'],
    'publicar' => ['controller' => 'PublicacionController', 'method' => 'create'],
    'vender' => ['controller' => 'PublicacionController', 'method' => 'sell'],
    'listado' => ['controller' => 'PublicacionController', 'method' => 'index'],
    // ====================================
    // NUEVAS RUTAS - CATEGORÍAS
    // ====================================
    'categorias' => ['controller' => 'CategoriaController', 'method' => 'index'],
    'categoria' => ['controller' => 'CategoriaController', 'method' => 'show'],
    
    // ====================================
    // RUTAS ADMIN
    // ====================================
    'admin' => ['controller' => 'AdminController', 'method' => 'index'],
    
    // ====================================
    // RUTAS DE PERFIL DE USUARIO
    // ====================================
    'perfil' => ['controller' => 'UsuarioController', 'method' => 'perfil'],
    'mis-publicaciones' => ['controller' => 'UsuarioController', 'method' => 'misPublicaciones'],
    'usuario' => ['controller' => 'UsuarioController', 'method' => 'verPerfil'],
    
    // ====================================
    // RUTAS DE FAVORITOS
    // ====================================
    'favoritos' => ['controller' => 'FavoritoController', 'method' => 'index'],
    
    // ====================================
    // RUTAS DE PAGOS CON FLOW
    // ====================================
    'pago' => ['controller' => 'PagoController', 'method' => 'index'],
];

// Aplicar rutas especiales si coincide
if (!empty($url[0]) && isset($specialRoutes[$url[0]])) {
    $controllerName = $specialRoutes[$url[0]]['controller'];
    $method = $specialRoutes[$url[0]]['method'];
    $params = array_slice($url, 1);  // Parámetros después de la ruta
}

// ====================================
// MANEJO DE RUTAS API
// ====================================
if (!empty($url[0]) && $url[0] === 'api') {
    if (!empty($url[1]) && $url[1] === 'comunas') {
        $controllerName = 'PublicacionController';
        $method = 'getComunas';
        $params = [];
    }
    
    // API Admin - Notificaciones
    if (!empty($url[1]) && $url[1] === 'admin') {
        require_once APP_PATH . '/controllers/ApiController.php';
        $apiController = new App\Controllers\ApiController();
        
        if (!empty($url[2]) && $url[2] === 'notifications') {
            $apiController->getAdminNotifications();
            exit;
        }
        
        if (!empty($url[2]) && $url[2] === 'stats') {
            $apiController->getAdminStats();
            exit;
        }
    }
}

// ====================================
// RUTAS DE MENSAJERÍA
// ====================================

// Ruta: /mensajes
// Maneja el sistema de mensajería interna
if (!empty($url[0]) && $url[0] === 'mensajes') {
    $controllerName = 'MensajeController';
    
    // Subrutas de mensajería
    if (isset($url[1])) {
        switch ($url[1]) {
            case 'enviar':
                // POST /mensajes/enviar - Enviar un mensaje
                $method = 'enviar';
                $params = [];
                break;
                
            case 'marcar-leido':
                // POST /mensajes/marcar-leido - Marcar conversación como leída
                $method = 'marcarLeido';
                $params = [];
                break;
                
            case 'obtener-nuevos':
                // GET /mensajes/obtener-nuevos - Obtener nuevos mensajes (polling)
                $method = 'obtenerNuevos';
                $params = [];
                break;
                
            default:
                // GET /mensajes - Vista principal
                $method = 'index';
                $params = [];
        }
    } else {
        // GET /mensajes - Vista principal (default)
        $method = 'index';
        $params = [];
    }
}

// Rutas soportadas:
// - GET  /mensajes                          → Ver bandeja de mensajes
// - GET  /mensajes?publicacion=1            → Iniciar conversación desde publicación
// - GET  /mensajes?conversacion=5           → Ver conversación específica
// - POST /mensajes/enviar                   → Enviar mensaje (AJAX)
// - POST /mensajes/marcar-leido             → Marcar como leído (AJAX)


// ====================================
// MANEJO DE MÉTODOS POST ESPECÍFICOS
// ====================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($url[0])) {
    // Autenticación
    if ($url[0] === 'registro') {
        $controllerName = 'AuthController';
        $method = 'processRegister';
    } elseif ($url[0] === 'login') {
        $controllerName = 'AuthController';
        $method = 'authenticate';
    } elseif ($url[0] === 'recuperar-contrasena') {
        $controllerName = 'AuthController';
        $method = 'sendResetLink';
    } elseif ($url[0] === 'reset-password') {
        $controllerName = 'AuthController';
        $method = 'updatePassword';
    }
    // Publicaciones - POST
    elseif ($url[0] === 'publicar' && isset($url[1]) && $url[1] === 'procesar') {
        $controllerName = 'PublicacionController';
        $method = 'store';
        $params = [];
    }
    elseif ($url[0] === 'publicaciones' && isset($url[1])) {
        $controllerName = 'PublicacionController';
        if ($url[1] === 'store') {
            $method = 'store';
            $params = [];
        } elseif (isset($url[2]) && $url[2] === 'update') {
            $method = 'update';
            $params = [$url[1]];  // ID de la publicación
        } elseif (isset($url[2]) && $url[2] === 'eliminar') {
            $method = 'destroy';
            $params = [$url[1]];  // ID de la publicación
        }
    }
    // Perfil de usuario - POST
    elseif ($url[0] === 'perfil' && isset($url[1])) {
        $controllerName = 'UsuarioController';
        if ($url[1] === 'actualizar') {
            $method = 'actualizarPerfil';
            $params = [];
        } elseif ($url[1] === 'cambiar-password') {
            $method = 'cambiarPassword';
            $params = [];
        } elseif ($url[1] === 'subir-foto') {
            $method = 'subirFoto';
            $params = [];
        }
    }
}

// ====================================
// RUTAS ESPECIALES PARA PUBLICACIONES
// ====================================

// Ruta: /publicaciones/approval
if (!empty($url[0]) && $url[0] === 'publicaciones' && isset($url[1]) && $url[1] === 'approval') {
    $controllerName = 'PublicacionController';
    $method = 'approval';
    $params = [];
}

// Ruta: /publicaciones/{id}/editar
if (!empty($url[0]) && $url[0] === 'publicaciones' && isset($url[1]) && is_numeric($url[1]) && isset($url[2]) && $url[2] === 'editar') {
    $controllerName = 'PublicacionController';
    $method = 'edit';
    $params = [$url[1]];  // ID de la publicación
}

// Ruta: /publicaciones/{id}/marcar-vendido
if (!empty($url[0]) && $url[0] === 'publicaciones' && isset($url[1]) && is_numeric($url[1]) && isset($url[2]) && $url[2] === 'marcar-vendido' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controllerName = 'PublicacionController';
    $method = 'marcarVendido';
    $params = [$url[1]];
}

// Ruta: /publicaciones/{id}/eliminar
if (!empty($url[0]) && $url[0] === 'publicaciones' && isset($url[1]) && is_numeric($url[1]) && isset($url[2]) && $url[2] === 'eliminar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controllerName = 'PublicacionController';
    $method = 'eliminar';
    $params = [$url[1]];
}

// ====================================
// RUTAS ADMIN - SISTEMA DE MODERACIÓN
// MODIFICADO: /admin/login redirige a /login
// ====================================

// Rutas específicas para administración que deben manejarse antes del routing genérico
if (!empty($url[0]) && $url[0] === 'admin') {
    
    // CRÍTICO: Redirigir /admin/login a /login (login unificado)
    if (!empty($url[1]) && $url[1] === 'login') {
        header('Location: /login');
        exit;
    }
    
    require_once APP_PATH . '/controllers/AdminController.php';
    $controller = new App\Controllers\AdminController();
    
    // /admin - Dashboard principal (redirige a publicaciones)
    if (count($url) === 1) {
        $controller->index();
        exit;
    }
    
    // /admin/logout - Cerrar sesión
    if ($url[1] === 'logout') {
        $controller->logout();
        exit;
    }
    
    // /admin/mensajes - Sistema de mensajería (vista admin)
    if ($url[1] === 'mensajes') {
        $controller->mensajes();
        exit;
    }
    
    // /admin/reportes - Reportes y estadísticas
    if ($url[1] === 'reportes') {
        $controller->reportes();
        exit;
    }
    
    // /admin/configuracion - Configuración del sistema
    if ($url[1] === 'configuracion') {
        if (count($url) === 2) {
            // GET /admin/configuracion - Ver configuración
            $controller->configuracion();
            exit;
        } elseif (count($url) === 3 && $url[2] === 'guardar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            // POST /admin/configuracion/guardar - Guardar configuración
            $controller->guardarConfiguracion();
            exit;
        }
    }
    
    // /admin/export - Exportación de datos
    if ($url[1] === 'export') {
        require_once APP_PATH . '/controllers/ExportController.php';
        $exportController = new App\Controllers\ExportController();
        
        if (!empty($url[2]) && $url[2] === 'publicaciones') {
            $exportController->exportarPublicaciones();
            exit;
        }
        
        if (!empty($url[2]) && $url[2] === 'usuarios') {
            $exportController->exportarUsuarios();
            exit;
        }
    }
    
    // /admin/usuarios - Gestión de usuarios
    if ($url[1] === 'usuarios') {
        require_once APP_PATH . '/controllers/UsuarioController.php';
        $usuarioController = new App\Controllers\UsuarioController();
        
        // /admin/usuarios - Lista de usuarios
        if (count($url) === 2) {
            $usuarioController->adminListar();
            exit;
        }
        
        // /admin/usuarios/{id} - Ver detalle de usuario
        if (count($url) === 3 && is_numeric($url[2])) {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $usuarioController->adminDetalle($url[2]);
                exit;
            }
        }
        
        // /admin/usuarios/{id}/actualizar - Actualizar usuario
        if (count($url) === 4 && is_numeric($url[2]) && $url[3] === 'actualizar') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $usuarioController->adminActualizar($url[2]);
                exit;
            }
        }
        
        // /admin/usuarios/{id}/cambiar-estado - Cambiar estado de usuario
        if (count($url) === 4 && is_numeric($url[2]) && $url[3] === 'cambiar-estado') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $usuarioController->adminCambiarEstado($url[2]);
                exit;
            }
        }
        
        // /admin/usuarios/{id}/eliminar - Eliminar usuario
        if (count($url) === 4 && is_numeric($url[2]) && $url[3] === 'eliminar') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $usuarioController->adminEliminar($url[2]);
                exit;
            }
        }
        
        // /admin/usuarios/{id}/historial - Ver historial de usuario
        if (count($url) === 4 && is_numeric($url[2]) && $url[3] === 'historial') {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $usuarioController->adminHistorial($url[2]);
                exit;
            }
        }
    }
    
    // /admin/publicaciones - Lista de publicaciones
    if ($url[1] === 'publicaciones' && count($url) === 2) {
        $controller->publicaciones();
        exit;
    }
    
    // /admin/publicaciones/{id} - Ver detalle de publicación
    if ($url[1] === 'publicaciones' && count($url) === 3 && is_numeric($url[2])) {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $controller->verPublicacion($url[2]);
            exit;
        }
    }
    
    // /admin/publicaciones/{id}/aprobar
    if ($url[1] === 'publicaciones' && count($url) === 4 && is_numeric($url[2]) && $url[3] === 'aprobar') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->aprobarPublicacion($url[2]);
            exit;
        }
    }
    
    // /admin/publicaciones/{id}/rechazar
    if ($url[1] === 'publicaciones' && count($url) === 4 && is_numeric($url[2]) && $url[3] === 'rechazar') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->rechazarPublicacion($url[2]);
            exit;
        }
    }
    
    // /admin/mensajes - Sistema de mensajería
    if ($url[1] === 'mensajes') {
        $controller->mensajes();
        exit;
    }
}

// ====================================
// RUTAS DE CONTACTO
// ====================================
if (!empty($url[0]) && $url[0] === 'contacto') {
    $controllerName = 'ContactoController';
    
    if (empty($url[1])) {
        // GET /contacto - Ver formulario
        $method = 'index';
        $params = [];
    } elseif ($url[1] === 'enviar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        // POST /contacto/enviar - Enviar mensaje
        $method = 'enviar';
        $params = [];
    }
}

// ====================================
// RUTAS DE FAVORITOS
// ====================================
if (!empty($url[0]) && $url[0] === 'favoritos') {
    $controllerName = 'FavoritoController';
    
    if (empty($url[1])) {
        // GET /favoritos - Ver lista de favoritos
        $method = 'index';
        $params = [];
    } elseif ($url[1] === 'agregar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        // POST /favoritos/agregar - Agregar a favoritos (AJAX)
        $method = 'agregar';
        $params = [];
    } elseif ($url[1] === 'eliminar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        // POST /favoritos/eliminar - Eliminar de favoritos (AJAX)
        $method = 'eliminar';
        $params = [];
    } elseif ($url[1] === 'verificar' && !empty($url[2])) {
        // GET /favoritos/verificar/{id} - Verificar si está en favoritos (AJAX)
        $method = 'verificar';
        $params = [$url[2]];
    } elseif ($url[1] === 'total') {
        // GET /favoritos/total - Obtener total de favoritos (AJAX)
        $method = 'total';
        $params = [];
    }
}

// ====================================
// RUTAS DE PAGOS CON FLOW
// ====================================
if (!empty($url[0]) && $url[0] === 'pago') {
    error_log("=== ROUTING PAGO ===");
    error_log("URL: " . print_r($url, true));
    error_log("Method: " . $_SERVER['REQUEST_METHOD']);
    
    $controllerName = 'PagoController';
    
    if (empty($url[1])) {
        // GET /pago - Redirigir a mis publicaciones
        header('Location: ' . BASE_URL . '/mis-publicaciones');
        exit;
    } elseif ($url[1] === 'preparar') {
        // GET /pago/preparar - Pantalla de confirmación antes de pagar
        error_log("Ruta: /pago/preparar");
        $method = 'preparar';
        $params = [];
    } elseif ($url[1] === 'iniciar') {
        // POST /pago/iniciar - Iniciar proceso de pago con Flow
        error_log("=== RUTA /pago/iniciar DETECTADA ===");
        error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
        error_log("Expected: POST");
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            error_log("✅ Método POST correcto - Ejecutando PagoController::iniciar()");
            $method = 'iniciar';
            $params = [];
        } else {
            error_log("❌ Método incorrecto: " . $_SERVER['REQUEST_METHOD']);
            error_log("Redirigiendo a /pago/preparar");
            header('Location: ' . BASE_URL . '/pago/preparar');
            exit;
        }
    } elseif ($url[1] === 'confirmar') {
        // POST /pago/confirmar - Callback de Flow (confirmación del pago)
        error_log("Ruta: /pago/confirmar");
        $method = 'confirmar';
        $params = [];
    } elseif ($url[1] === 'retorno') {
        // GET /pago/retorno - Página de retorno después del pago
        error_log("Ruta: /pago/retorno");
        $method = 'retorno';
        $params = [];
    } elseif ($url[1] === 'reintentar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        // POST /pago/reintentar - Reintentar un pago rechazado
        error_log("Ruta: POST /pago/reintentar");
        $method = 'reintentar';
        $params = [];
    } elseif ($url[1] === 'retomar' && !empty($url[2])) {
        // GET /pago/retomar/{id} - Retomar un pago pendiente
        error_log("Ruta: /pago/retomar/{$url[2]}");
        $method = 'retomar';
        $params = [$url[2]];
    } elseif ($url[1] === 'cancelar' && !empty($url[2]) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        // POST /pago/cancelar/{id} - Cancelar un pago pendiente
        error_log("Ruta: POST /pago/cancelar/{$url[2]}");
        $method = 'cancelar';
        $params = [$url[2]];
    } elseif ($url[1] === 'simulador') {
        if (empty($url[2])) {
            // GET /pago/simulador - Mostrar simulador de Flow
            error_log("Ruta: /pago/simulador");
            $method = 'simulador';
            $params = [];
        } elseif ($url[2] === 'procesar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            // POST /pago/simulador/procesar - Procesar resultado del simulador
            error_log("Ruta: POST /pago/simulador/procesar");
            $method = 'simularProcesar';
            $params = [];
        }
    }
    
    error_log("Controller: $controllerName, Method: $method");
}

// ====================================
// RUTA: MIS PAGOS PENDIENTES
// ====================================
if (!empty($url[0]) && $url[0] === 'mis-pagos-pendientes') {
    $controllerName = 'PagoController';
    $method = 'pendientes';
    $params = [];
}

// ====================================
// RUTAS ADMIN - GESTIÓN DE USUARIOS
// ====================================

if (!empty($url[0]) && $url[0] === 'admin' && !empty($url[1]) && $url[1] === 'usuarios') {
    require_once APP_PATH . '/controllers/AdminController.php';
    $controller = new App\Controllers\AdminController();
    
    // /admin/usuarios - Listado de usuarios
    if (count($url) === 2) {
        $controller->usuarios();
        exit;
    }
    
    // /admin/usuarios/{id} - Ver detalle de usuario
    if (count($url) === 3 && is_numeric($url[2])) {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $controller->verUsuario($url[2]);
            exit;
        }
    }
    
    // /admin/usuarios/{id}/actualizar - Actualizar datos de usuario
    if (count($url) === 4 && is_numeric($url[2]) && $url[3] === 'actualizar') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->actualizarUsuario($url[2]);
            exit;
        }
    }
    
    // /admin/usuarios/{id}/cambiar-estado - Cambiar estado (activar/suspender)
    if (count($url) === 4 && is_numeric($url[2]) && $url[3] === 'cambiar-estado') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->cambiarEstadoUsuario($url[2]);
            exit;
        }
    }
    
    // /admin/usuarios/{id}/eliminar - Eliminar usuario (soft delete)
    if (count($url) === 4 && is_numeric($url[2]) && $url[3] === 'eliminar') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->eliminarUsuario($url[2]);
            exit;
        }
    }
    
    // /admin/usuarios/{id}/historial - Ver historial completo
    if (count($url) === 4 && is_numeric($url[2]) && $url[3] === 'historial') {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $controller->historialUsuario($url[2]);
            exit;
        }
    }
}


// ====================================
// CARGA ESTÁNDAR DE CONTROLADOR
// ====================================

// Cargar controlador
$controllerFile = APP_PATH . '/controllers/' . $controllerName . '.php';

if (!file_exists($controllerFile)) {
    http_response_code(404);
    echo "404 - Página no encontrada";
    exit;
}

require_once $controllerFile;

// Instanciar controlador
$controllerClass = 'App\\Controllers\\' . $controllerName;

if (!class_exists($controllerClass)) {
    http_response_code(500);
    echo "Error: Controlador no encontrado";
    exit;
}

$controller = new $controllerClass();

// Ejecutar método
if (!method_exists($controller, $method)) {
    http_response_code(404);
    echo "404 - Método no encontrado";
    exit;
}

// Llamar al método con los parámetros
call_user_func_array([$controller, $method], $params);