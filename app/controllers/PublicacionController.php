<?php

/**
 * PublicacionController
 * Controlador para gestionar todas las operaciones de publicaciones
 *
 * @author ToroDigital
 * @date 2025-10-25
 */

namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\Session;
use App\Models\Categoria;
use App\Models\Publicacion;
use PDO;

class PublicacionController
{
    private $publicacionModel;
    private $categoriaModel;

    public function __construct()
    {
        $this->publicacionModel = new Publicacion();
        $this->categoriaModel = new Categoria();
    }

    /**
     * Muestra el listado de publicaciones con filtros
     * Ruta: GET /publicaciones o /listado
     */
    public function index()
    {
        // Obtener parámetros de filtros desde URL
        $filtros = [
            'categoria_id' => $_GET['categoria'] ?? null,
            'subcategoria_id' => $_GET['subcategoria'] ?? null,
            'region_id' => $_GET['region'] ?? null,
            'comuna_id' => $_GET['comuna'] ?? null,
            'precio_min' => $_GET['precio_min'] ?? null,
            'precio_max' => $_GET['precio_max'] ?? null,
            'tipo_venta' => $_GET['tipo_venta'] ?? null,
            'buscar' => $_GET['q'] ?? null,
            'orden' => $_GET['orden'] ?? 'recientes',
            'page' => (int) ($_GET['page'] ?? 1)
        ];

        // Obtener publicaciones con filtros desde la BD
        $resultado = $this->publicacionModel->listarConFiltros($filtros);
        
        // Obtener categorías para el filtro
        $categoriaModel = new \App\Models\Categoria();
        $categorias = $categoriaModel->getConSubcategorias();
        
        // Obtener regiones para el filtro
        $sql = "SELECT * FROM regiones ORDER BY nombre ASC";
        $regiones = $this->publicacionModel->query($sql);
        
        // Preparar datos para la vista
        $data = [
            'title' => 'Listado de Vehículos Siniestrados - ChileChocados',
            'meta_description' => 'Encuentra vehículos siniestrados y en desarme en todo Chile',
            'publicaciones' => $resultado['publicaciones'] ?? [],
            'total' => $resultado['total'] ?? 0,
            'pagina_actual' => $filtros['page'],
            'total_paginas' => $resultado['total_paginas'] ?? 1,
            'filtros_aplicados' => $filtros,
            'categorias' => $categorias,
            'regiones' => $regiones
        ];

        // Cargar vista
        require_once __DIR__ . '/../views/pages/publicaciones/list.php';
    }

    /**
     * Muestra el detalle de una publicación
     * Ruta: GET /publicacion/{id}
     */
    public function show($id)
    {
        // Obtener publicación con relaciones desde la BD
        $publicacion = $this->publicacionModel->getConRelaciones($id);
        
        // Verificar que existe
        if (!$publicacion) {
            $_SESSION['error'] = 'Publicación no encontrada';
            header('Location: ' . BASE_URL . '/listado');
            exit;
        }
        
        // Verificar que esté aprobada (excepto si es el dueño o admin)
        $es_dueno = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $publicacion->usuario_id;
        $es_admin = isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === 'admin';
        
        if ($publicacion->estado !== 'aprobada' && !$es_dueno && !$es_admin) {
            $_SESSION['error'] = 'Esta publicación no está disponible';
            header('Location: ' . BASE_URL . '/listado');
            exit;
        }
        
        // Incrementar visitas (solo si no es el dueño)
        if (!$es_dueno) {
            $this->publicacionModel->incrementarVisitas($id);
        }
        
        // Obtener imágenes de la publicación
        $imagenes = $this->publicacionModel->getImagenes($id);
        
        // Obtener publicaciones similares
        $publicaciones_similares = $this->publicacionModel->getSimilares($id, $publicacion->categoria_padre_id, 4);

        // Preparar datos para la vista
        $data = [
            'title' => $publicacion->titulo . ' - ChileChocados',
            'publicacion' => $publicacion,
            'imagenes' => $imagenes,
            'similares' => $publicaciones_similares
        ];

        // Cargar vista
        require_once __DIR__ . '/../views/pages/publicaciones/detail.php';
    }

    /**
     * Muestra el formulario para crear nueva publicación
     * Ruta: GET /publicar
     */
    public function create()
    {
        // Verificar que el usuario esté autenticado
        Auth::require();

        // Obtener categorías para el formulario
        $categorias = $this->categoriaModel->getConSubcategorias();

        // Obtener regiones para el selector
        $regiones = $this->getRegiones();

        $data = [
            'title' => 'Publicar Vehículo Siniestrado - ChileChocados',
            'categorias' => $categorias,
            'regiones' => $regiones,
            'csrf_token' => generateCsrfToken()
        ];

        // Cargar vista del formulario
        require_once __DIR__ . '/../views/pages/publicaciones/publish.php';
    }

    /**
     * Procesa y guarda una nueva publicación
     * Ruta: POST /publicaciones/store
     */
    public function store()
    {
        // Verificar autenticación
        Auth::require();

        // Verificar token CSRF
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de seguridad inválido. Por favor, intenta nuevamente.';
            header('Location: ' . BASE_URL . '/publicar');
            exit;
        }

        // Determinar si es borrador
        $es_borrador = isset($_POST['guardar_borrador']);

        // Validar datos del formulario (solo si no es borrador)
        if (!$es_borrador) {
            $errores = $this->validarDatosPublicacion($_POST);

            if (!empty($errores)) {
                $_SESSION['errores'] = $errores;
                $_SESSION['old'] = $_POST;
                header('Location: ' . BASE_URL . '/publicar');
                exit;
            }
        }

        // Generar título automático
        $titulo = trim(sanitize($_POST['marca'] ?? '') . ' ' . sanitize($_POST['modelo'] ?? '') . ' ' . ($_POST['anio'] ?? ''));
        if (empty(trim($titulo))) {
            $titulo = 'Borrador sin título';
        }

        // Determinar si es destacada
        $promocion = !empty($_POST['promocion']) ? sanitize($_POST['promocion']) : 'normal';
        $es_destacada = in_array($promocion, ['destacada15', 'destacada30']) ? 1 : 0;
        
        // Guardar la duración deseada en días
        $dias_destacado = 0;
        if ($es_destacada) {
            $dias_destacado = $promocion === 'destacada15' ? 15 : 30;
        }

        // IMPORTANTE: Si es destacada, guardar como borrador hasta que se pague
        $estado_inicial = $es_borrador ? 'borrador' : ($es_destacada ? 'borrador' : 'pendiente');

        // Preparar datos para guardar
        $datos = [
            'usuario_id' => $_SESSION['user_id'],
            'tipificacion' => !empty($_POST['tipificacion']) ? sanitize($_POST['tipificacion']) : null,
            'categoria_padre_id' => !empty($_POST['categoria_padre_id']) ? sanitize($_POST['categoria_padre_id']) : null,
            'subcategoria_id' => !empty($_POST['subcategoria_id']) ? sanitize($_POST['subcategoria_id']) : null,
            'titulo' => $titulo,
            'marca' => !empty($_POST['marca']) ? sanitize($_POST['marca']) : null,
            'modelo' => !empty($_POST['modelo']) ? sanitize($_POST['modelo']) : null,
            'anio' => !empty($_POST['anio']) ? (int) $_POST['anio'] : null,
            'descripcion' => !empty($_POST['descripcion']) ? sanitize($_POST['descripcion']) : null,
            'tipo_venta' => !empty($_POST['tipo_venta']) ? sanitize($_POST['tipo_venta']) : 'completo',
            'precio' => (!empty($_POST['tipo_venta']) && $_POST['tipo_venta'] === 'completo' && !empty($_POST['precio'])) ? (float) str_replace(['.', ','], ['', '.'], $_POST['precio']) : null,
            'region_id' => !empty($_POST['region_id']) ? (int) $_POST['region_id'] : null,
            'comuna_id' => !empty($_POST['comuna_id']) ? (int) $_POST['comuna_id'] : null,
            'estado' => $estado_inicial,
            'es_destacada' => 0, // Se activará después del pago
            'fecha_destacada_inicio' => null,
            'fecha_destacada_fin' => null,
            'fecha_publicacion' => $es_borrador ? null : date('Y-m-d H:i:s')
        ];

        // Guardar publicación en BD
        $publicacion_id = $this->publicacionModel->create($datos);

        if (!$publicacion_id) {
            $_SESSION['error'] = 'Error al crear la publicación. Intenta nuevamente.';
            header('Location: ' . BASE_URL . '/publicar');
            exit;
        }

        // Procesar y guardar imágenes
        if (!empty($_FILES['fotos']['name'][0])) {
            $foto_principal_index = isset($_POST['foto_principal']) ? (int) $_POST['foto_principal'] : 1;
            $this->procesarImagenes($publicacion_id, $_FILES['fotos'], $foto_principal_index);
        }

        // Redirigir según el tipo de guardado
        error_log("=== REDIRECCION DESPUES DE CREAR PUBLICACION ===");
        error_log("Es borrador: " . ($es_borrador ? 'SI' : 'NO'));
        error_log("Es destacada: " . ($es_destacada ? 'SI' : 'NO'));
        error_log("Promocion: $promocion");
        error_log("Publicacion ID: $publicacion_id");
        error_log("Estado guardado: $estado_inicial");
        
        if ($es_borrador) {
            error_log("Redirigiendo a: mis-publicaciones (borrador)");
            $_SESSION['success'] = 'Borrador guardado exitosamente. Puedes continuar editándolo más tarde.';
            header('Location: ' . BASE_URL . '/mis-publicaciones');
        } else {
            // Si es destacada, redirigir a pago (publicación guardada como borrador)
            if ($es_destacada && !$es_borrador) {
                error_log("Redirigiendo a: /pago/preparar (destacada - guardada como borrador)");
                $_SESSION['publicacion_pendiente_pago'] = [
                    'publicacion_id' => $publicacion_id,
                    'tipo_destacado' => $promocion
                ];
                error_log("Sesion guardada: " . print_r($_SESSION['publicacion_pendiente_pago'], true));
                header('Location: ' . BASE_URL . '/pago/preparar');
            } else {
                error_log("Redirigiendo a: /publicaciones/approval (normal)");
                // Guardar ID de publicación en sesión para página de confirmación
                $_SESSION['publicacion_creada_id'] = $publicacion_id;
                header('Location: ' . BASE_URL . '/publicaciones/approval');
            }
        }
        exit;
    }

    /**
     * Muestra página de confirmación tras crear publicación
     * Ruta: GET /publicaciones/approval
     */
    public function approval()
    {
        // Verificar autenticación
        Auth::require();

        // Verificar que existe una publicación recién creada
        if (!isset($_SESSION['publicacion_creada_id'])) {
            header('Location: ' . BASE_URL);
            exit;
        }

        $publicacion_id = $_SESSION['publicacion_creada_id'];
        unset($_SESSION['publicacion_creada_id']);  // Limpiar sesión

        // Obtener datos de la publicación
        $publicacion = $this->publicacionModel->find($publicacion_id);

        $data = [
            'title' => 'Publicación Creada - ChileChocados',
            'publicacion' => $publicacion
        ];

        // Cargar vista de confirmación
        require_once __DIR__ . '/../views/pages/publicaciones/approval.php';
    }

    /**
     * Muestra formulario para editar publicación
     * Ruta: GET /publicaciones/{id}/editar
     */
    public function edit($id)
    {
        Auth::require();

        // Obtener publicación
        $publicacion = $this->publicacionModel->find($id);

        // Verificar que la publicación existe y pertenece al usuario
        if (!$publicacion || $publicacion->usuario_id != $_SESSION['user_id']) {
            $_SESSION['error'] = 'No tienes permiso para editar esta publicación';
            header('Location: ' . BASE_URL . '/mis-publicaciones');
            exit;
        }

        // Obtener categorías y regiones
        $categorias = $this->categoriaModel->getConSubcategorias();
        $regiones = $this->getRegiones();
        
        // Obtener imágenes de la publicación
        $imagenes = $this->publicacionModel->getImagenes($id);
        
        // Debug
        error_log("Editando publicación ID: $id");
        error_log("Imágenes encontradas: " . count($imagenes));
        error_log("Datos de imágenes: " . print_r($imagenes, true));

        // Pasar datos a la vista (reutilizando publish.php)
        $pageTitle = 'Editar Publicación - ChileChocados';
        $modoEdicion = true;
        $publicacionId = $id;

        require_once __DIR__ . '/../views/pages/publicaciones/publish.php';
    }

    /**
     * Actualiza una publicación existente
     * Ruta: POST /publicaciones/{id}/update
     */
    public function update($id)
    {
        Auth::require();

        // Log temporal para debugging
        error_log("=== UPDATE PUBLICACION ID: $id ===");
        error_log("POST data: " . print_r($_POST, true));
        error_log("guardar_borrador isset: " . (isset($_POST['guardar_borrador']) ? 'SI' : 'NO'));

        // Verificar CSRF
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de seguridad inválido';
            header('Location: ' . BASE_URL . '/publicaciones/' . $id . '/editar');
            exit;
        }

        // Obtener publicación
        $publicacion = $this->publicacionModel->find($id);
        error_log("Estado anterior de publicacion: " . ($publicacion ? $publicacion->estado : 'NO ENCONTRADA'));

        // Verificar permisos
        if (!$publicacion || $publicacion->usuario_id != $_SESSION['user_id']) {
            $_SESSION['error'] = 'No tienes permiso para editar esta publicación';
            header('Location: ' . BASE_URL . '/mis-publicaciones');
            exit;
        }

        // Determinar si es borrador PRIMERO
        $es_borrador = isset($_POST['guardar_borrador']);
        
        error_log("es_borrador: " . ($es_borrador ? 'SI' : 'NO'));

        // Validar datos solo si NO es borrador
        if (!$es_borrador) {
            $errores = $this->validarDatosPublicacion($_POST, true);

            if (!empty($errores)) {
                error_log("Errores de validación: " . print_r($errores, true));
                $_SESSION['errores'] = $errores;
                $_SESSION['old'] = $_POST;
                header('Location: ' . BASE_URL . '/publicaciones/' . $id . '/editar');
                exit;
            }
        }
        
        // Determinar si es destacada
        $promocion = !empty($_POST['promocion']) ? sanitize($_POST['promocion']) : 'normal';
        $es_destacada = in_array($promocion, ['destacada15', 'destacada30']) ? 1 : 0;

        // IMPORTANTE: Si es destacada, mantener como borrador hasta que se pague
        $estado_actualizado = $es_borrador ? 'borrador' : ($es_destacada ? 'borrador' : 'pendiente');

        // Generar título automático
        $titulo = trim(sanitize($_POST['marca'] ?? '') . ' ' . sanitize($_POST['modelo'] ?? '') . ' ' . ($_POST['anio'] ?? ''));
        if (empty(trim($titulo))) {
            $titulo = 'Sin título';
        }

        // Preparar datos actualizados
        $datos = [
            'tipificacion' => !empty($_POST['tipificacion']) ? sanitize($_POST['tipificacion']) : null,
            'categoria_padre_id' => !empty($_POST['categoria_padre_id']) ? sanitize($_POST['categoria_padre_id']) : null,
            'subcategoria_id' => !empty($_POST['subcategoria_id']) ? sanitize($_POST['subcategoria_id']) : null,
            'titulo' => $titulo,
            'marca' => !empty($_POST['marca']) ? sanitize($_POST['marca']) : null,
            'modelo' => !empty($_POST['modelo']) ? sanitize($_POST['modelo']) : null,
            'anio' => !empty($_POST['anio']) ? (int) $_POST['anio'] : null,
            'descripcion' => !empty($_POST['descripcion']) ? sanitize($_POST['descripcion']) : null,
            'tipo_venta' => !empty($_POST['tipo_venta']) ? sanitize($_POST['tipo_venta']) : 'completo',
            'precio' => (!empty($_POST['tipo_venta']) && $_POST['tipo_venta'] === 'completo' && !empty($_POST['precio'])) ? (float) str_replace(['.', ','], ['', '.'], $_POST['precio']) : null,
            'region_id' => !empty($_POST['region_id']) ? (int) $_POST['region_id'] : null,
            'comuna_id' => !empty($_POST['comuna_id']) ? (int) $_POST['comuna_id'] : null,
            'estado' => $estado_actualizado,
            'es_destacada' => 0, // Se activará después del pago
            'fecha_destacada_inicio' => null,
            'fecha_destacada_fin' => null,
            'fecha_publicacion' => $es_borrador ? null : date('Y-m-d H:i:s')
        ];

        // Verificar si el estado anterior era borrador y ahora se está enviando para aprobación
        $estado_anterior = $publicacion->estado;
        $cambio_de_borrador_a_pendiente = ($estado_anterior === 'borrador' && !$es_borrador);
        
        error_log("Estado anterior: $estado_anterior");
        error_log("Nuevo estado: " . $datos['estado']);
        error_log("Cambio de borrador a pendiente: " . ($cambio_de_borrador_a_pendiente ? 'SI' : 'NO'));

        // Actualizar en BD
        $this->publicacionModel->update($id, $datos);

        // Debug de archivos
        error_log("UPDATE - FILES recibidos: " . print_r($_FILES, true));
        
        // Procesar nuevas imágenes si existen
        if (!empty($_FILES['fotos']['name'][0])) {
            error_log("UPDATE - Procesando imágenes...");
            $foto_principal_index = isset($_POST['foto_principal']) ? (int) $_POST['foto_principal'] : 1;
            $this->procesarImagenes($id, $_FILES['fotos'], $foto_principal_index);
        } else {
            error_log("UPDATE - No se recibieron imágenes nuevas");
        }

        // Redirigir según el tipo de guardado
        error_log("=== REDIRECCION DESPUES DE ACTUALIZAR ===");
        error_log("Es borrador: " . ($es_borrador ? 'SI' : 'NO'));
        error_log("Es destacada: " . ($es_destacada ? 'SI' : 'NO'));
        error_log("Estado actualizado: $estado_actualizado");
        
        if ($es_borrador) {
            $_SESSION['success'] = 'Borrador actualizado exitosamente.';
            header('Location: ' . BASE_URL . '/mis-publicaciones');
        } elseif ($cambio_de_borrador_a_pendiente) {
            // Si cambió de borrador a pendiente y es destacada, redirigir a pago
            if ($es_destacada) {
                error_log("Redirigiendo a pago (destacada - guardada como borrador)");
                $_SESSION['publicacion_pendiente_pago'] = [
                    'publicacion_id' => $id,
                    'tipo_destacado' => $promocion
                ];
                header('Location: ' . BASE_URL . '/pago/preparar');
            } else {
                // Si no es destacada, mostrar pantalla de éxito
                $_SESSION['publicacion_creada_id'] = $id;
                header('Location: ' . BASE_URL . '/publicaciones/approval');
            }
        } else {
            $_SESSION['success'] = 'Publicación actualizada exitosamente. Está pendiente de revisión.';
            header('Location: ' . BASE_URL . '/mis-publicaciones');
        }
        exit;
    }

    /**
     * Elimina una publicación
     * Ruta: POST /publicaciones/{id}/eliminar
     */
    public function destroy($id)
    {
        Auth::require();

        // Verificar CSRF
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de seguridad inválido';
            header('Location: ' . BASE_URL . '/mis-publicaciones');
            exit;
        }

        // Obtener publicación
        $publicacion = $this->publicacionModel->find($id);

        // Verificar permisos
        if (!$publicacion || $publicacion->usuario_id != $_SESSION['user_id']) {
            $_SESSION['error'] = 'No tienes permiso para eliminar esta publicación';
            header('Location: ' . BASE_URL . '/mis-publicaciones');
            exit;
        }

        // Eliminar imágenes físicas del servidor
        $imagenes = $this->publicacionModel->getImagenes($id);
        foreach ($imagenes as $imagen) {
            $ruta = __DIR__ . '/../../public/uploads/' . $imagen->url;
            if (file_exists($ruta)) {
                unlink($ruta);
            }
        }

        // Eliminar publicación de BD (soft delete - cambiar estado)
        $this->publicacionModel->update($id, ['estado' => 'archivada']);

        $_SESSION['success'] = 'Publicación eliminada exitosamente';
        header('Location: ' . BASE_URL . '/mis-publicaciones');
        exit;
    }

    /**
     * Muestra landing page "Vende tu vehículo"
     * Ruta: GET /vender
     */
    public function sell()
    {
        $data = [
            'title' => 'Vende tu Vehículo Siniestrado - ChileChocados',
            'meta_description' => 'Publica gratis tu vehículo siniestrado en el marketplace líder de Chile'
        ];

        require_once __DIR__ . '/../views/pages/publicaciones/sell.php';
    }

    // ==================== MÉTODOS PRIVADOS ====================

    /**
     * Valida los datos del formulario de publicación
     * @param array $datos Datos a validar
     * @param bool $esEdicion Si es true, no valida fotos obligatorias
     */
    private function validarDatosPublicacion($datos, $esEdicion = false)
    {
        $errores = [];

        // Validar tipificación
        if (empty($datos['tipificacion'])) {
            $errores['tipificacion'] = 'Debes seleccionar el tipo de vehículo';
        }

        // Validar tipo de venta
        if (empty($datos['tipo_venta'])) {
            $errores['tipo_venta'] = 'Debes seleccionar un tipo de venta';
        }

        // Validar marca
        if (empty($datos['marca'])) {
            $errores['marca'] = 'La marca es obligatoria';
        }

        // Validar modelo
        if (empty($datos['modelo'])) {
            $errores['modelo'] = 'El modelo es obligatorio';
        }

        // Validar año
        $anio_actual = (int) date('Y');
        if (empty($datos['anio']) || $datos['anio'] < 1900 || $datos['anio'] > 2025) {
            $errores['anio'] = 'El año debe estar entre 1900 y 2025';
        }

        // Validar categoría
        if (empty($datos['categoria_padre_id'])) {
            $errores['categoria_padre_id'] = 'Debes seleccionar una categoría';
        }

        // Validar región
        if (empty($datos['region_id'])) {
            $errores['region_id'] = 'Debes seleccionar una región';
        }

        // Validar comuna
        if (empty($datos['comuna_id'])) {
            $errores['comuna_id'] = 'Debes seleccionar una comuna';
        }

        // Validar precio si es venta completa
        if (!empty($datos['tipo_venta']) && $datos['tipo_venta'] === 'completo') {
            if (empty($datos['precio'])) {
                $errores['precio'] = 'El precio es obligatorio para venta completa';
            } else {
                // Limpiar el precio de puntos y convertir a número
                $precio_limpio = str_replace(['.', ','], ['', '.'], $datos['precio']);
                $precio_numero = (float) $precio_limpio;
                
                if ($precio_numero <= 0) {
                    $errores['precio'] = 'El precio debe ser mayor a 0';
                } elseif ($precio_numero > 50000000) {
                    $errores['precio'] = 'El precio no puede exceder $50.000.000';
                }
            }
        }

        // Validar descripción
        if (empty($datos['descripcion']) || strlen($datos['descripcion']) < 20) {
            $errores['descripcion'] = 'La descripción debe tener al menos 20 caracteres';
        } elseif (strlen($datos['descripcion']) > 2000) {
            $errores['descripcion'] = 'La descripción no puede exceder 2000 caracteres';
        }

        // Validar que haya al menos una foto (solo en creación, no en edición)
        if (!$esEdicion && empty($_FILES['fotos']['name'][0])) {
            $errores['fotos'] = 'Debes subir al menos una foto del vehículo';
        }

        return $errores;
    }

    /**
     * Procesa y guarda las imágenes subidas
     */
    private function procesarImagenes($publicacion_id, $archivos, $foto_principal_index = 1)
    {
        $logFile = __DIR__ . '/../../public/logs/debug.txt';
        file_put_contents($logFile, "\n=== PROCESANDO IMÁGENES ===\n", FILE_APPEND);
        file_put_contents($logFile, "Publicación ID: $publicacion_id\n", FILE_APPEND);
        file_put_contents($logFile, "Foto principal index: $foto_principal_index\n", FILE_APPEND);
        file_put_contents($logFile, "Archivos completos: " . print_r($archivos, true) . "\n", FILE_APPEND);

        $total_imagenes = count($archivos['name']);
        $foto_principal_url = null;

        file_put_contents($logFile, "Total de archivos recibidos: $total_imagenes\n", FILE_APPEND);
        
        // Verificar si realmente hay archivos
        if (empty($archivos['name'][0])) {
            file_put_contents($logFile, "ERROR: No hay archivos para procesar (name[0] está vacío)\n", FILE_APPEND);
            return;
        }

        // Validar cantidad (máximo 6 imágenes)
        if ($total_imagenes > 6) {
            $_SESSION['warning'] = 'Solo se procesaron las primeras 6 imágenes';
            $total_imagenes = 6;
        }

        for ($i = 0; $i < $total_imagenes; $i++) {
            file_put_contents($logFile, "\nProcesando archivo $i:\n", FILE_APPEND);
            file_put_contents($logFile, "  - Error code: {$archivos['error'][$i]}\n", FILE_APPEND);
            file_put_contents($logFile, "  - Name: {$archivos['name'][$i]}\n", FILE_APPEND);

            // Saltar si hay error en el archivo o no se subió archivo
            if ($archivos['error'][$i] !== UPLOAD_ERR_OK || empty($archivos['name'][$i])) {
                file_put_contents($logFile, "  - SALTADO (error o vacío)\n", FILE_APPEND);
                continue;
            }

            $archivo = [
                'name' => $archivos['name'][$i],
                'type' => $archivos['type'][$i],
                'tmp_name' => $archivos['tmp_name'][$i],
                'error' => $archivos['error'][$i],
                'size' => $archivos['size'][$i]
            ];

            // Validar imagen
            if (!$this->validarImagen($archivo)) {
                file_put_contents($logFile, "  - RECHAZADO (validación falló)\n", FILE_APPEND);
                continue;
            }

            // Generar nombre único
            $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
            $nombre_archivo = uniqid('pub_' . $publicacion_id . '_') . '.' . $extension;

            // Crear directorio si no existe (usando constante UPLOAD_PATH)
            $directorio = UPLOAD_PATH . '/publicaciones/' . date('Y') . '/' . date('m');
            if (!is_dir($directorio)) {
                mkdir($directorio, 0777, true);
                file_put_contents($logFile, "  - Directorio creado: $directorio\n", FILE_APPEND);
            }

            // Ruta completa del archivo
            $ruta_completa = $directorio . '/' . $nombre_archivo;

            file_put_contents($logFile, "  - Ruta completa: $ruta_completa\n", FILE_APPEND);
            file_put_contents($logFile, "  - Directorio existe: " . (is_dir($directorio) ? 'SÍ' : 'NO') . "\n", FILE_APPEND);
            file_put_contents($logFile, "  - Directorio escribible: " . (is_writable($directorio) ? 'SÍ' : 'NO') . "\n", FILE_APPEND);
            file_put_contents($logFile, "  - Archivo temporal existe: " . (file_exists($archivo['tmp_name']) ? 'SÍ' : 'NO') . "\n", FILE_APPEND);

            // Mover archivo subido
            if (move_uploaded_file($archivo['tmp_name'], $ruta_completa)) {
                file_put_contents($logFile, "  - ✓ Archivo movido exitosamente\n", FILE_APPEND);

                // Guardar en BD - ruta relativa desde /uploads/publicaciones/
                $ruta_relativa = date('Y') . '/' . date('m') . '/' . $nombre_archivo;

                // Determinar si es la foto principal (índice comienza en 1)
                $es_principal = ($i + 1) === $foto_principal_index ? 1 : 0;

                file_put_contents($logFile, "  - Ruta relativa: $ruta_relativa\n", FILE_APPEND);
                file_put_contents($logFile, "  - Es principal: $es_principal\n", FILE_APPEND);

                $resultado = $this->publicacionModel->agregarImagen($publicacion_id, [
                    'ruta' => $ruta_relativa,
                    'orden' => $i + 1,
                    'es_principal' => $es_principal
                ]);

                file_put_contents($logFile, '  - Guardado en BD: ' . ($resultado ? 'SÍ' : 'NO') . "\n", FILE_APPEND);

                // Guardar ruta de la foto principal
                if ($es_principal) {
                    $foto_principal_url = $ruta_relativa;
                }
            } else {
                $error = error_get_last();
                file_put_contents($logFile, "  - ✗ ERROR al mover archivo\n", FILE_APPEND);
                file_put_contents($logFile, "  - Error PHP: " . print_r($error, true) . "\n", FILE_APPEND);
                file_put_contents($logFile, "  - Permisos directorio: " . substr(sprintf('%o', fileperms($directorio)), -4) . "\n", FILE_APPEND);
            }
        }

        // Actualizar foto principal en la tabla publicaciones
        if ($foto_principal_url) {
            file_put_contents($logFile, "\nActualizando foto principal: $foto_principal_url\n", FILE_APPEND);
            $this->publicacionModel->update($publicacion_id, ['foto_principal' => $foto_principal_url]);
        } else {
            file_put_contents($logFile, "\nNo se encontró foto principal\n", FILE_APPEND);
        }

        file_put_contents($logFile, "=== FIN PROCESAMIENTO IMÁGENES ===\n\n", FILE_APPEND);
    }

    /**
     * Valida que el archivo sea una imagen válida
     */
    private function validarImagen($archivo)
    {
        // Validar tamaño (máximo 5MB)
        $max_size = 5 * 1024 * 1024;  // 5MB
        if ($archivo['size'] > $max_size) {
            return false;
        }

        // Validar extensión
        $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'webp'];
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $extensiones_permitidas)) {
            return false;
        }

        // Validar MIME type
        $tipos_permitidos = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($archivo['type'], $tipos_permitidos)) {
            return false;
        }

        return true;
    }

    /**
     * Obtiene las regiones de Chile
     */
    private function getRegiones()
    {
        $db = getDB();
        $stmt = $db->query('SELECT * FROM regiones ORDER BY nombre ASC');
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * API: Obtiene las comunas de una región
     * Ruta: GET /api/comunas?region_id=X
     */
    public function getComunas()
    {
        header('Content-Type: application/json');

        $regionId = $_GET['region_id'] ?? null;

        if (!$regionId) {
            echo json_encode(['error' => 'region_id es requerido', 'comunas' => []]);
            return;
        }

        try {
            $db = getDB();
            $stmt = $db->prepare('SELECT id, nombre FROM comunas WHERE region_id = ? ORDER BY nombre ASC');
            $stmt->execute([$regionId]);
            $comunas = $stmt->fetchAll(PDO::FETCH_OBJ);

            echo json_encode(['comunas' => $comunas]);
        } catch (\Exception $e) {
            echo json_encode(['error' => 'Error al obtener comunas', 'comunas' => []]);
        }
    }
    
    /**
     * Marcar publicación como vendida
     * Ruta: POST /publicaciones/{id}/marcar-vendido
     */
    public function marcarVendido($id)
    {
        Auth::require();
        
        // Validar CSRF
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de seguridad inválido';
            header('Location: ' . BASE_URL . '/mis-publicaciones');
            exit;
        }
        
        // Obtener publicación
        $publicacion = $this->publicacionModel->find($id);
        
        // Verificar que la publicación existe y pertenece al usuario
        if (!$publicacion || $publicacion->usuario_id != $_SESSION['user_id']) {
            $_SESSION['error'] = 'No tienes permiso para modificar esta publicación';
            header('Location: ' . BASE_URL . '/mis-publicaciones');
            exit;
        }
        
        // Verificar que está aprobada
        if ($publicacion->estado !== 'aprobada') {
            $_SESSION['error'] = 'Solo puedes marcar como vendidas las publicaciones aprobadas';
            header('Location: ' . BASE_URL . '/mis-publicaciones');
            exit;
        }
        
        // Actualizar estado a vendida
        $db = getDB();
        $stmt = $db->prepare("UPDATE publicaciones SET estado = 'vendida' WHERE id = ?");
        $stmt->execute([$id]);
        
        // Registrar en auditoría
        $stmt = $db->prepare("
            INSERT INTO auditoria (usuario_id, tabla, registro_id, accion, datos_nuevos, ip)
            VALUES (?, 'publicaciones', ?, 'actualizar', ?, ?)
        ");
        $stmt->execute([
            $_SESSION['user_id'],
            $id,
            json_encode(['estado' => 'vendida']),
            $_SERVER['REMOTE_ADDR'] ?? null
        ]);
        
        $_SESSION['success'] = '¡Felicitaciones! Tu publicación ha sido marcada como vendida';
        header('Location: ' . BASE_URL . '/mis-publicaciones');
        exit;
    }
    
    /**
     * Eliminar publicación
     * Ruta: POST /publicaciones/{id}/eliminar
     */
    public function eliminar($id)
    {
        Auth::require();
        
        // Validar CSRF
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de seguridad inválido';
            header('Location: ' . BASE_URL . '/mis-publicaciones');
            exit;
        }
        
        // Obtener publicación
        $publicacion = $this->publicacionModel->find($id);
        
        // Verificar que la publicación existe y pertenece al usuario
        if (!$publicacion || $publicacion->usuario_id != $_SESSION['user_id']) {
            $_SESSION['error'] = 'No tienes permiso para eliminar esta publicación';
            header('Location: ' . BASE_URL . '/mis-publicaciones');
            exit;
        }
        
        // Eliminar publicación
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM publicaciones WHERE id = ?");
        $stmt->execute([$id]);
        
        // Eliminar fotos asociadas
        $stmt = $db->prepare("DELETE FROM publicacion_fotos WHERE publicacion_id = ?");
        $stmt->execute([$id]);
        
        $_SESSION['success'] = 'Publicación eliminada exitosamente';
        header('Location: ' . BASE_URL . '/mis-publicaciones');
        exit;
    }
}
