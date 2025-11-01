<?php

/**
 * PagoController
 * Controlador para gestionar pagos con Flow
 */

namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\FlowHelper;
use App\Models\Publicacion;
use PDO;

class PagoController
{
    private $flowHelper;
    private $publicacionModel;

    public function __construct()
    {
        $this->flowHelper = new FlowHelper();
        $this->publicacionModel = new Publicacion();
    }

    /**
     * Preparar pago - Pantalla de confirmación antes de ir a Flow
     * Ruta: GET /pago/preparar
     */
    public function preparar()
    {
        Auth::require();

        // Verificar que hay una publicación pendiente de pago
        if (!isset($_SESSION['publicacion_pendiente_pago'])) {
            $_SESSION['error'] = 'No hay publicación pendiente de pago';
            header('Location: ' . BASE_URL . '/mis-publicaciones');
            exit;
        }

        $datos = $_SESSION['publicacion_pendiente_pago'];
        $publicacionId = $datos['publicacion_id'];
        $tipoDestacado = $datos['tipo_destacado'];

        // Obtener publicación
        $publicacion = $this->publicacionModel->find($publicacionId);

        if (!$publicacion || $publicacion->usuario_id != $_SESSION['user_id']) {
            unset($_SESSION['publicacion_pendiente_pago']);
            $_SESSION['error'] = 'Publicación no encontrada';
            header('Location: ' . BASE_URL . '/mis-publicaciones');
            exit;
        }

        // Obtener precio y días
        $monto = FlowHelper::obtenerPrecioDestacado($tipoDestacado);
        $dias = FlowHelper::obtenerDiasDestacado($tipoDestacado);

        // Pasar variables a la vista
        $title = 'Confirmar Pago - ChileChocados';
        $tipo_destacado = $tipoDestacado;
        
        require_once __DIR__ . '/../views/pages/pagos/preparar.php';
    }

    /**
     * Iniciar proceso de pago para publicación destacada
     * Ruta: POST /pago/iniciar
     */
    public function iniciar()
    {
        // Log ANTES de Auth::require() - Escribir directamente en archivo
        $logFile = __DIR__ . '/../../logs/pago_debug.log';
        $logMsg = "\n=== INICIAR PAGO - " . date('Y-m-d H:i:s') . " ===\n";
        $logMsg .= "Request Method: " . $_SERVER['REQUEST_METHOD'] . "\n";
        $logMsg .= "Request URI: " . $_SERVER['REQUEST_URI'] . "\n";
        $logMsg .= "Session ID: " . session_id() . "\n";
        $logMsg .= "User ID: " . ($_SESSION['user_id'] ?? 'NO EXISTE') . "\n";
        $logMsg .= "Authenticated: " . ($_SESSION['authenticated'] ?? 'NO EXISTE') . "\n";
        $logMsg .= "POST data: " . print_r($_POST, true) . "\n";
        $logMsg .= "Session pendiente_pago: " . print_r($_SESSION['publicacion_pendiente_pago'] ?? 'NO EXISTE', true) . "\n";
        file_put_contents($logFile, $logMsg, FILE_APPEND);
        
        error_log("=== INICIAR PAGO - INICIO ===");
        error_log("Timestamp: " . date('Y-m-d H:i:s'));
        error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
        error_log("Request URI: " . $_SERVER['REQUEST_URI']);
        error_log("Session ID: " . session_id());
        error_log("User ID en sesión: " . ($_SESSION['user_id'] ?? 'NO EXISTE'));
        error_log("Authenticated: " . ($_SESSION['authenticated'] ?? 'NO EXISTE'));
        error_log("POST data: " . print_r($_POST, true));
        error_log("Session publicacion_pendiente_pago: " . print_r($_SESSION['publicacion_pendiente_pago'] ?? 'NO EXISTE', true));
        
        // Verificar autenticación manualmente primero
        if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
            error_log("❌ ERROR: Usuario no autenticado - Redirigiendo a login");
            $_SESSION['error'] = 'Debes iniciar sesión para continuar';
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
        
        error_log("✅ Usuario autenticado correctamente");

        // Validar CSRF token (solo log, no bloquear)
        $csrfToken = $_POST['csrf_token'] ?? null;
        error_log("CSRF Token recibido: " . ($csrfToken ? 'SI' : 'NO'));
        if ($csrfToken && function_exists('validateCsrfToken')) {
            $csrfValido = validateCsrfToken($csrfToken);
            error_log("CSRF Token válido: " . ($csrfValido ? 'SI' : 'NO'));
        }

        // Validar datos
        $publicacionId = $_POST['publicacion_id'] ?? null;
        $tipoDestacado = $_POST['tipo_destacado'] ?? null;

        error_log("Publicacion ID recibido: " . ($publicacionId ?? 'NULL'));
        error_log("Tipo Destacado recibido: " . ($tipoDestacado ?? 'NULL'));

        if (!$publicacionId || !$tipoDestacado) {
            error_log("❌ ERROR: Datos de pago incompletos");
            $_SESSION['error'] = 'Datos de pago incompletos';
            header('Location: ' . BASE_URL . '/publicar');
            exit;
        }

        // Validar que la publicación existe y pertenece al usuario
        $publicacion = $this->publicacionModel->find($publicacionId);
        
        if (!$publicacion || $publicacion->usuario_id != $_SESSION['user_id']) {
            error_log("ERROR: Publicación no encontrada o no pertenece al usuario");
            $_SESSION['error'] = 'Publicación no encontrada o no tienes permisos';
            header('Location: ' . BASE_URL . '/mis-publicaciones');
            exit;
        }
        
        error_log("✅ Publicación validada correctamente");

        // Obtener precio y días según tipo
        $monto = FlowHelper::obtenerPrecioDestacado($tipoDestacado);
        $dias = FlowHelper::obtenerDiasDestacado($tipoDestacado);

        if ($monto === 0) {
            $_SESSION['error'] = 'Tipo de destacado inválido';
            header('Location: ' . BASE_URL . '/publicaciones/' . $publicacionId . '/editar');
            exit;
        }

        // Generar número de orden único
        $commerceOrder = 'PUB-' . $publicacionId . '-' . time();

        // Convertir tipo destacado al formato de la BD
        $tipoBD = FlowHelper::convertirTipoParaBD($tipoDestacado);
        
        error_log("Tipo destacado original: $tipoDestacado");
        error_log("Tipo para BD: $tipoBD");

        // Crear registro en la tabla pagos_flow
        $db = getDB();
        $stmt = $db->prepare("
            INSERT INTO pagos_flow 
            (publicacion_id, usuario_id, tipo, monto, flow_orden, estado, fecha_creacion) 
            VALUES (?, ?, ?, ?, ?, 'pendiente', NOW())
        ");
        $stmt->execute([
            $publicacionId,
            $_SESSION['user_id'],
            $tipoBD,
            $monto,
            $commerceOrder
        ]);
        $pagoId = $db->lastInsertId();

        // Preparar datos para Flow
        $params = [
            'commerceOrder' => $commerceOrder,
            'subject' => "Publicación Destacada - {$dias} días",
            'amount' => $monto,
            'email' => $_SESSION['user_email'] ?? 'usuario@chilechocados.cl',
            'urlConfirmation' => BASE_URL . '/pago/confirmar',
            'urlReturn' => BASE_URL . '/pago/retorno?pago_id=' . $pagoId,
            'optional' => json_encode([
                'pago_id' => $pagoId,
                'publicacion_id' => $publicacionId,
                'tipo_destacado' => $tipoDestacado
            ])
        ];

        // Crear orden en Flow
        error_log("Creando orden en Flow con params: " . print_r($params, true));
        $response = $this->flowHelper->crearOrden($params);
        error_log("Respuesta de Flow: " . print_r($response, true));

        if (!$response || !isset($response['token'])) {
            // Error al crear orden
            error_log("ERROR: No se recibió token de Flow");
            $stmt = $db->prepare("UPDATE pagos_flow SET estado = 'error' WHERE id = ?");
            $stmt->execute([$pagoId]);

            $_SESSION['error'] = 'Error al iniciar el pago. Por favor, intenta nuevamente.';
            header('Location: ' . BASE_URL . '/publicaciones/' . $publicacionId . '/editar');
            exit;
        }

        // Guardar token de Flow
        $stmt = $db->prepare("
            UPDATE pagos_flow 
            SET flow_token = ?, respuesta_flow = ? 
            WHERE id = ?
        ");
        $stmt->execute([
            $response['token'],
            json_encode($response),
            $pagoId
        ]);

        // Redirigir a Flow para realizar el pago
        $urlPago = $this->flowHelper->obtenerUrlPago($response['token']);
        error_log("Redirigiendo a Flow: $urlPago");
        header('Location: ' . $urlPago);
        exit;
    }

    /**
     * Confirmar pago (callback de Flow)
     * Ruta: POST /pago/confirmar
     */
    public function confirmar()
    {
        // Obtener token de Flow
        $token = $_POST['token'] ?? $_GET['token'] ?? null;

        if (!$token) {
            error_log('Pago Flow: Token no recibido en confirmación');
            http_response_code(400);
            exit;
        }

        // Obtener estado del pago desde Flow
        $response = $this->flowHelper->obtenerEstadoOrden($token);

        if (!$response) {
            error_log('Pago Flow: Error al obtener estado de la orden');
            http_response_code(500);
            exit;
        }

        // Validar firma
        $firma = $_POST['s'] ?? $_GET['s'] ?? null;
        if (!$this->flowHelper->validarFirma($response, $firma)) {
            error_log('Pago Flow: Firma inválida');
            http_response_code(400);
            exit;
        }

        // Obtener datos del pago
        $commerceOrder = $response['commerceOrder'] ?? null;
        $status = $response['status'] ?? null;

        if (!$commerceOrder) {
            error_log('Pago Flow: commerceOrder no encontrado');
            http_response_code(400);
            exit;
        }

        // Buscar el pago en la base de datos
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM pagos_flow WHERE flow_orden = ?");
        $stmt->execute([$commerceOrder]);
        $pago = $stmt->fetch(PDO::FETCH_OBJ);

        if (!$pago) {
            error_log("Pago Flow: Pago no encontrado para orden $commerceOrder");
            http_response_code(404);
            exit;
        }

        // Actualizar estado del pago según respuesta de Flow
        $estadoPago = 'pendiente';
        if ($status == 2) {
            $estadoPago = 'aprobado';
        } elseif ($status == 3) {
            $estadoPago = 'rechazado';
        }

        $stmt = $db->prepare("
            UPDATE pagos_flow 
            SET estado = ?, 
                respuesta_flow = ?, 
                fecha_pago = NOW() 
            WHERE id = ?
        ");
        $stmt->execute([
            $estadoPago,
            json_encode($response),
            $pago->id
        ]);

        // Si el pago fue aprobado, cambiar estado a pendiente y activar el destacado
        if ($estadoPago === 'aprobado') {
            $dias = FlowHelper::obtenerDiasDestacado($pago->tipo);
            
            $stmt = $db->prepare("
                UPDATE publicaciones 
                SET estado = 'pendiente',
                    es_destacada = 1,
                    fecha_destacada_inicio = NOW(),
                    fecha_destacada_fin = DATE_ADD(NOW(), INTERVAL ? DAY)
                WHERE id = ?
            ");
            $stmt->execute([$dias, $pago->publicacion_id]);

            error_log("Pago Flow: Publicación {$pago->publicacion_id} cambiada a 'pendiente' y destacado activado por $dias días");
        }

        // Responder a Flow
        http_response_code(200);
        exit;
    }

    /**
     * Página de retorno después del pago
     * Ruta: GET /pago/retorno
     */
    public function retorno()
    {
        Auth::require();

        $pagoId = $_GET['pago_id'] ?? null;
        $token = $_GET['token'] ?? null;

        if (!$pagoId) {
            $_SESSION['error'] = 'Información de pago no encontrada';
            header('Location: ' . BASE_URL . '/mis-publicaciones');
            exit;
        }

        // Obtener información del pago
        $db = getDB();
        $stmt = $db->prepare("
            SELECT p.*, pub.titulo, pub.id as publicacion_id
            FROM pagos_flow p
            INNER JOIN publicaciones pub ON p.publicacion_id = pub.id
            WHERE p.id = ? AND p.usuario_id = ?
        ");
        $stmt->execute([$pagoId, $_SESSION['user_id']]);
        $pago = $stmt->fetch(PDO::FETCH_OBJ);

        if (!$pago) {
            $_SESSION['error'] = 'Pago no encontrado';
            header('Location: ' . BASE_URL . '/mis-publicaciones');
            exit;
        }

        // Si hay token, obtener estado actualizado de Flow
        if ($token) {
            $response = $this->flowHelper->obtenerEstadoOrden($token);
            if ($response && isset($response['status'])) {
                $estadoPago = 'pendiente';
                if ($response['status'] == 2) {
                    $estadoPago = 'aprobado';
                } elseif ($response['status'] == 3) {
                    $estadoPago = 'rechazado';
                }

                // Actualizar estado si cambió
                if ($estadoPago !== $pago->estado) {
                    $stmt = $db->prepare("UPDATE pagos_flow SET estado = ? WHERE id = ?");
                    $stmt->execute([$estadoPago, $pagoId]);
                    $pago->estado = $estadoPago;
                }
            }
        }

        // Cargar vista según el estado
        $data = [
            'pago' => $pago,
            'title' => 'Resultado del Pago - ChileChocados'
        ];

        require_once __DIR__ . '/../views/pages/pagos/retorno.php';
    }

    /**
     * Reintentar pago
     * Ruta: POST /pago/reintentar
     */
    public function reintentar()
    {
        Auth::require();

        $pagoId = $_POST['pago_id'] ?? null;

        if (!$pagoId) {
            $_SESSION['error'] = 'Información de pago no encontrada';
            header('Location: ' . BASE_URL . '/mis-publicaciones');
            exit;
        }

        // Obtener información del pago
        $db = getDB();
        $stmt = $db->prepare("
            SELECT * FROM pagos_flow 
            WHERE id = ? AND usuario_id = ? AND estado IN ('rechazado', 'expirado')
        ");
        $stmt->execute([$pagoId, $_SESSION['user_id']]);
        $pago = $stmt->fetch(PDO::FETCH_OBJ);

        if (!$pago) {
            $_SESSION['error'] = 'Pago no encontrado o no se puede reintentar';
            header('Location: ' . BASE_URL . '/mis-publicaciones');
            exit;
        }

        // Redirigir al proceso de pago nuevamente
        $_POST['publicacion_id'] = $pago->publicacion_id;
        $_POST['tipo_destacado'] = $pago->tipo;
        
        $this->iniciar();
    }

    /**
     * Mostrar simulador de Flow (modo local)
     * Ruta: GET /pago/simulador
     */
    public function simulador()
    {
        $data = [
            'title' => 'Simulador de Flow - ChileChocados'
        ];

        require_once __DIR__ . '/../views/pages/pagos/simulador.php';
    }

    /**
     * Procesar resultado del simulador
     * Ruta: POST /pago/simulador/procesar
     */
    public function simularProcesar()
    {
        $token = $_POST['token'] ?? null;
        $resultado = $_POST['resultado'] ?? 'exitoso';

        if (!$token) {
            $_SESSION['error'] = 'Token no proporcionado';
            header('Location: ' . BASE_URL . '/mis-publicaciones');
            exit;
        }

        // Buscar el pago por token
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM pagos_flow WHERE flow_token = ?");
        $stmt->execute([$token]);
        $pago = $stmt->fetch(PDO::FETCH_OBJ);

        if (!$pago) {
            $_SESSION['error'] = 'Pago no encontrado';
            header('Location: ' . BASE_URL . '/mis-publicaciones');
            exit;
        }

        // Actualizar estado según resultado
        $estadoPago = $resultado === 'exitoso' ? 'aprobado' : 'rechazado';
        
        $stmt = $db->prepare("
            UPDATE pagos_flow 
            SET estado = ?, fecha_pago = NOW() 
            WHERE id = ?
        ");
        $stmt->execute([$estadoPago, $pago->id]);

        // Si fue exitoso, cambiar estado a pendiente y activar destacado
        if ($estadoPago === 'aprobado') {
            $dias = FlowHelper::obtenerDiasDestacado($pago->tipo);
            
            $stmt = $db->prepare("
                UPDATE publicaciones 
                SET estado = 'pendiente',
                    es_destacada = 1,
                    fecha_destacada_inicio = NOW(),
                    fecha_destacada_fin = DATE_ADD(NOW(), INTERVAL ? DAY)
                WHERE id = ?
            ");
            $stmt->execute([$dias, $pago->publicacion_id]);
        }

        // Redirigir a página de retorno
        header('Location: ' . BASE_URL . '/pago/retorno?pago_id=' . $pago->id);
        exit;
    }
}
