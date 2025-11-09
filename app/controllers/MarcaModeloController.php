<?php

namespace App\Controllers;

use App\Models\MarcaModelo;
use App\Helpers\Auth;

class MarcaModeloController
{
    private $marcaModeloModel;

    public function __construct()
    {
        $this->marcaModeloModel = new MarcaModelo();
    }

    /**
     * API: Buscar marcas
     * GET /api/marcas?q=toyota
     */
    public function buscarMarcas()
    {
        header('Content-Type: application/json');
        
        $query = $_GET['q'] ?? '';
        
        // Obtener marcas desde BD
        $marcas = $this->marcaModeloModel->getMarcas();
        
        // Filtrar si hay query
        if (!empty($query)) {
            $marcas = array_filter($marcas, function($marca) use ($query) {
                return stripos($marca->nombre, $query) !== false;
            });
            
            // Solo limitar a 10 si hay búsqueda
            $marcas = array_slice($marcas, 0, 10);
        }
        // Si no hay query, devolver todas las marcas (para carga inicial)
        
        // Formatear para compatibilidad con frontend
        $marcasFormateadas = array_map(function($marca) {
            return [
                'nombre' => $marca->nombre,
                'cantidadModelos' => $marca->cantidad_modelos
            ];
        }, $marcas);
        
        echo json_encode(['marcas' => array_values($marcasFormateadas)]);
    }

    /**
     * API: Obtener modelos de una marca
     * GET /api/modelos?marca=Toyota
     */
    public function obtenerModelos()
    {
        header('Content-Type: application/json');
        
        $marca = $_GET['marca'] ?? '';
        
        if (empty($marca)) {
            echo json_encode(['error' => 'Marca requerida', 'modelos' => []]);
            return;
        }
        
        // Obtener modelos desde BD
        $modelos = $this->marcaModeloModel->getModelosPorNombreMarca($marca);
        
        if ($modelos) {
            // Formatear para compatibilidad con frontend
            $modelosFormateados = array_map(function($modelo) {
                return [
                    'nombre' => $modelo->nombre
                ];
            }, $modelos);
            
            echo json_encode(['modelos' => $modelosFormateados]);
        } else {
            echo json_encode(['error' => 'Marca no encontrada', 'modelos' => []]);
        }
    }

    /**
     * Panel de admin: Listar marcas/modelos pendientes
     * GET /admin/marcas-modelos-pendientes
     */
    public function listarPendientes()
    {
        Auth::requireAdmin();
        
        $pendientes = $this->marcaModeloModel->getPendientes();
        $historial = $this->marcaModeloModel->getTodas(20);
        
        require_once __DIR__ . '/../views/pages/admin/marcas-modelos-pendientes.php';
    }

    /**
     * Aprobar marca/modelo personalizado
     * POST /admin/marcas-modelos/{id}/aprobar
     */
    public function aprobar($id)
    {
        Auth::requireAdmin();
        
        // Validar CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['error'] = 'Token inválido';
            header('Location: ' . BASE_URL . '/admin/marcas-modelos-pendientes');
            exit;
        }
        
        $marcaSugerida = !empty($_POST['marca_sugerida']) ? $_POST['marca_sugerida'] : null;
        $modeloSugerido = !empty($_POST['modelo_sugerido']) ? $_POST['modelo_sugerido'] : null;
        $notas = $_POST['notas'] ?? null;
        
        // Obtener datos de la solicitud antes de aprobar
        $solicitud = $this->marcaModeloModel->find($id);
        
        // Aprobar
        $resultado = $this->marcaModeloModel->aprobar($id, $marcaSugerida, $modeloSugerido, $notas, $_SESSION['user_id']);
        
        if ($resultado && $solicitud) {
            // Obtener datos de la publicación para notificar al usuario
            $publicacionModel = new \App\Models\Publicacion();
            $publicacion = $publicacionModel->find($solicitud->publicacion_id);
            
            if ($publicacion) {
                // Crear notificación para el usuario
                $notificacionModel = new \App\Models\Notificacion();
                $marcaFinal = $marcaSugerida ?? $solicitud->marca_ingresada;
                $modeloFinal = $modeloSugerido ?? $solicitud->modelo_ingresado;
                
                $mensaje = "Tu publicación '{$publicacion->titulo}' ha sido aprobada. ";
                $mensaje .= "Marca/Modelo: {$marcaFinal} {$modeloFinal}";
                
                $notificacionModel->create([
                    'usuario_id' => $publicacion->usuario_id,
                    'tipo' => 'publicacion_aprobada',
                    'titulo' => 'Marca/Modelo Aprobado',
                    'mensaje' => $mensaje,
                    'enlace' => '/publicacion/' . $publicacion->id,
                    'leida' => 0
                ]);
            }
            
            $_SESSION['success'] = 'Marca/modelo aprobado exitosamente';
        } else {
            $_SESSION['error'] = 'Error al aprobar marca/modelo';
        }
        
        header('Location: ' . BASE_URL . '/admin/marcas-modelos-pendientes');
        exit;
    }

    /**
     * Rechazar marca/modelo personalizado
     * POST /admin/marcas-modelos/{id}/rechazar
     */
    public function rechazar($id)
    {
        Auth::requireAdmin();
        
        // Validar CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['error'] = 'Token inválido';
            header('Location: ' . BASE_URL . '/admin/marcas-modelos-pendientes');
            exit;
        }
        
        $motivo = $_POST['motivo'] ?? 'No especificado';
        
        // Obtener datos de la solicitud antes de rechazar
        $solicitud = $this->marcaModeloModel->find($id);
        
        // Rechazar
        $resultado = $this->marcaModeloModel->rechazar($id, $motivo, $_SESSION['user_id']);
        
        if ($resultado && $solicitud) {
            // Obtener datos de la publicación para notificar al usuario
            $publicacionModel = new \App\Models\Publicacion();
            $publicacion = $publicacionModel->find($solicitud->publicacion_id);
            
            if ($publicacion) {
                // Crear notificación para el usuario
                $notificacionModel = new \App\Models\Notificacion();
                
                $mensaje = "Tu publicación '{$publicacion->titulo}' requiere corrección. ";
                $mensaje .= "Marca/Modelo rechazado: {$solicitud->marca_ingresada} {$solicitud->modelo_ingresado}. ";
                $mensaje .= "Motivo: {$motivo}";
                
                $notificacionModel->create([
                    'usuario_id' => $publicacion->usuario_id,
                    'tipo' => 'publicacion_rechazada',
                    'titulo' => 'Marca/Modelo Rechazado',
                    'mensaje' => $mensaje,
                    'enlace' => '/publicaciones/' . $publicacion->id . '/editar',
                    'leida' => 0
                ]);
            }
            
            $_SESSION['success'] = 'Marca/modelo rechazado';
        } else {
            $_SESSION['error'] = 'Error al rechazar marca/modelo';
        }
        
        header('Location: ' . BASE_URL . '/admin/marcas-modelos-pendientes');
        exit;
    }
}
