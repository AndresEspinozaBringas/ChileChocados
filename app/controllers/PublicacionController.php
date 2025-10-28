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
     * Ruta: GET /publicaciones
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
            'tipo_venta' => $_GET['tipo'] ?? null,  // 'completo' o 'desarme'
            'buscar' => $_GET['q'] ?? null,
            'orden' => $_GET['orden'] ?? 'recientes',
            'page' => $_GET['page'] ?? 1
        ];

        // Obtener publicaciones con filtros aplicados
        $resultado = $this->publicacionModel->listarConFiltros($filtros);

        // Obtener datos para los selectores de filtros
        $categorias = $this->categoriaModel->getActivas();

        $data = [
            'title' => 'Listado de Vehículos Siniestrados - ChileChocados',
            'meta_description' => 'Encuentra vehículos siniestrados y en desarme en todo Chile',
            'publicaciones' => $resultado['publicaciones'] ?? [],
            'total' => $resultado['total'] ?? 0,
            'pagina_actual' => (int) $filtros['page'],
            'total_paginas' => $resultado['total_paginas'] ?? 1,
            'filtros_aplicados' => $filtros,
            'categorias' => $categorias
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
        // TODO: Conectar con base de datos en futuras etapas
        // Por ahora usamos datos estáticos para mostrar la página

        // Datos estáticos de publicaciones
        $publicaciones_estaticas = [
            1 => [
                'id' => 1,
                'titulo' => 'Ford Territory 2022 - Chocado Frontal',
                'descripcion' => 'Ford Territory 2022 en excelente estado mecánico. Choque frontal leve, motor y transmisión funcionando perfectamente. Ideal para reparar o desarme. Documentación al día, sin multas. Vehículo ubicado en Santiago Centro.',
                'precio' => 8500000,
                'categoria_nombre' => 'SUV',
                'marca' => 'Ford',
                'modelo' => 'Territory',
                'anio' => 2022,
                'kilometraje' => 35000,
                'tipo_combustible' => 'Gasolina',
                'transmision' => 'Automática',
                'estado_vehiculo' => 'Chocado',
                'tipo_venta' => 'completo',
                'region_nombre' => 'Región Metropolitana',
                'comuna_nombre' => 'Santiago',
                'foto_principal' => 'ford-territory.jpg',
                'usuario_nombre' => 'Juan Pérez',
                'usuario_telefono' => '+56 9 1234 5678',
                'usuario_email' => 'juan.perez@example.com',
                'fecha_publicacion' => '2024-10-20',
                'vistas' => 245
            ],
            2 => [
                'id' => 2,
                'titulo' => 'Kia Cerato 2020 - Choque Lateral',
                'descripcion' => 'Kia Cerato 2020, choque lateral derecho. Motor en perfecto estado, transmisión automática funcionando. Ideal para reparación. Papeles al día.',
                'precio' => 6200000,
                'categoria_nombre' => 'Sedán',
                'marca' => 'Kia',
                'modelo' => 'Cerato',
                'anio' => 2020,
                'kilometraje' => 48000,
                'tipo_combustible' => 'Gasolina',
                'transmision' => 'Automática',
                'estado_vehiculo' => 'Chocado',
                'tipo_venta' => 'completo',
                'region_nombre' => 'Región de Valparaíso',
                'comuna_nombre' => 'Valparaíso',
                'foto_principal' => 'kia-cerato.jpg',
                'usuario_nombre' => 'María González',
                'usuario_telefono' => '+56 9 8765 4321',
                'usuario_email' => 'maria.gonzalez@example.com',
                'fecha_publicacion' => '2024-10-18',
                'vistas' => 189
            ],
            3 => [
                'id' => 3,
                'titulo' => 'Kia Rio 5 2019 - Choque Trasero',
                'descripcion' => 'Kia Rio 5 2019, choque trasero. Mecánica en excelente estado. Ideal para reparar y usar. Sin deudas.',
                'precio' => 4800000,
                'categoria_nombre' => 'Hatchback',
                'marca' => 'Kia',
                'modelo' => 'Rio 5',
                'anio' => 2019,
                'kilometraje' => 62000,
                'tipo_combustible' => 'Gasolina',
                'transmision' => 'Manual',
                'estado_vehiculo' => 'Chocado',
                'tipo_venta' => 'completo',
                'region_nombre' => 'Región del Biobío',
                'comuna_nombre' => 'Concepción',
                'foto_principal' => 'kia-rio-5.jpg',
                'usuario_nombre' => 'Carlos Muñoz',
                'usuario_telefono' => '+56 9 5555 6666',
                'usuario_email' => 'carlos.munoz@example.com',
                'fecha_publicacion' => '2024-10-15',
                'vistas' => 156
            ],
            4 => [
                'id' => 4,
                'titulo' => 'Dodge Journey 2018 - En Desarme',
                'descripcion' => 'Dodge Journey 2018 disponible para desarme. Todas las piezas disponibles. Motor, transmisión, puertas, vidrios, etc.',
                'precio' => null,
                'categoria_nombre' => 'SUV',
                'marca' => 'Dodge',
                'modelo' => 'Journey',
                'anio' => 2018,
                'kilometraje' => 95000,
                'tipo_combustible' => 'Gasolina',
                'transmision' => 'Automática',
                'estado_vehiculo' => 'En Desarme',
                'tipo_venta' => 'desarme',
                'region_nombre' => 'Región de Coquimbo',
                'comuna_nombre' => 'La Serena',
                'foto_principal' => 'dodge.jpg',
                'usuario_nombre' => 'Taller AutoPartes',
                'usuario_telefono' => '+56 9 7777 8888',
                'usuario_email' => 'contacto@autopartes.cl',
                'fecha_publicacion' => '2024-10-12',
                'vistas' => 98
            ]
        ];

        // Verificar si existe la publicación
        if (!isset($publicaciones_estaticas[$id])) {
            http_response_code(404);
            echo '404 - Publicación no encontrada';
            exit;
        }

        // Convertir array a objeto para compatibilidad con la vista
        $publicacion = (object) $publicaciones_estaticas[$id];

        // Imágenes para la galería (incluye la principal + adicionales)
        $imagenes = [];
        if ($id == 1) {
            // Imágenes para Ford Territory (4 imágenes en total)
            $imagenes = [
                (object) ['ruta_archivo' => 'ford-territory.jpg'],  // Imagen principal
                (object) ['ruta_archivo' => 'ford-territory-1.jpg'],
                (object) ['ruta_archivo' => 'ford-territory-2.jpg'],
                (object) ['ruta_archivo' => 'ford-territory-3.jpg']
            ];
        }

        // Publicaciones similares (otras publicaciones)
        $similares = [];
        foreach ($publicaciones_estaticas as $pub_id => $pub) {
            if ($pub_id != $id && count($similares) < 3) {
                $similares[] = (object) $pub;
            }
        }

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
        // DEBUG: Verificar que el método se está ejecutando
        file_put_contents(__DIR__ . '/../../public/logs/debug.txt', "STORE METHOD CALLED\n", FILE_APPEND);
        file_put_contents(__DIR__ . '/../../public/logs/debug.txt', 'POST data: ' . print_r($_POST, true) . "\n", FILE_APPEND);
        file_put_contents(__DIR__ . '/../../public/logs/debug.txt', 'FILES data: ' . print_r($_FILES, true) . "\n", FILE_APPEND);

        // Verificar autenticación
        Auth::require();

        // Verificar token CSRF
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            file_put_contents(__DIR__ . '/../../public/logs/debug.txt', "CSRF TOKEN INVALID\n", FILE_APPEND);
            file_put_contents(__DIR__ . '/../../public/logs/debug.txt', 'Session token: ' . ($_SESSION['csrf_token'] ?? 'NO SESSION TOKEN') . "\n", FILE_APPEND);
            file_put_contents(__DIR__ . '/../../public/logs/debug.txt', 'POST token: ' . ($_POST['csrf_token'] ?? 'NO POST TOKEN') . "\n", FILE_APPEND);
            $_SESSION['error'] = 'Token de seguridad inválido. Por favor, intenta nuevamente.';
            header('Location: ' . BASE_URL . '/publicar');
            exit;
        }

        file_put_contents(__DIR__ . '/../../public/logs/debug.txt', "CSRF TOKEN VALID - Continuando...\n", FILE_APPEND);

        // Determinar si es borrador
        $es_borrador = isset($_POST['guardar_borrador']);

        // Validar datos del formulario (solo si no es borrador)
        if (!$es_borrador) {
            $errores = $this->validarDatosPublicacion($_POST);

            if (!empty($errores)) {
                file_put_contents(__DIR__ . '/../../public/logs/debug.txt', 'ERRORES DE VALIDACIÓN: ' . print_r($errores, true) . "\n", FILE_APPEND);
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
            'precio' => (!empty($_POST['tipo_venta']) && $_POST['tipo_venta'] === 'completo' && !empty($_POST['precio'])) ? (float) $_POST['precio'] : null,
            'region_id' => !empty($_POST['region_id']) ? (int) $_POST['region_id'] : null,
            'comuna_id' => !empty($_POST['comuna_id']) ? (int) $_POST['comuna_id'] : null,
            'estado' => $es_borrador ? 'borrador' : 'pendiente',
            'fecha_publicacion' => $es_borrador ? null : date('Y-m-d H:i:s')
        ];

        // Guardar publicación en BD
        $publicacion_id = $this->publicacionModel->create($datos);

        if (!$publicacion_id) {
            file_put_contents(__DIR__ . '/../../public/logs/debug.txt', "ERROR: No se pudo crear la publicación\n", FILE_APPEND);
            $_SESSION['error'] = 'Error al crear la publicación. Intenta nuevamente.';
            header('Location: ' . BASE_URL . '/publicar');
            exit;
        }

        file_put_contents(__DIR__ . '/../../public/logs/debug.txt', "✓ Publicación creada con ID: $publicacion_id\n", FILE_APPEND);

        // Procesar y guardar imágenes
        if (!empty($_FILES['fotos']['name'][0])) {
            $foto_principal_index = isset($_POST['foto_principal']) ? (int) $_POST['foto_principal'] : 1;
            $this->procesarImagenes($publicacion_id, $_FILES['fotos'], $foto_principal_index);
        }

        // Redirigir según el tipo de guardado
        if ($es_borrador) {
            $_SESSION['success'] = 'Borrador guardado exitosamente. Puedes continuar editándolo más tarde.';
            header('Location: ' . BASE_URL . '/mis-publicaciones');
        } else {
            // Guardar ID de publicación en sesión para página de confirmación
            $_SESSION['publicacion_creada_id'] = $publicacion_id;
            header('Location: ' . BASE_URL . '/publicaciones/approval');
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
        $imagenes = $this->publicacionModel->getImagenes($id);

        $data = [
            'title' => 'Editar Publicación - ChileChocados',
            'publicacion' => $publicacion,
            'categorias' => $categorias,
            'regiones' => $regiones,
            'imagenes' => $imagenes,
            'csrf_token' => generateCsrfToken()
        ];

        require_once __DIR__ . '/../views/pages/publicaciones/edit.php';
    }

    /**
     * Actualiza una publicación existente
     * Ruta: POST /publicaciones/{id}/update
     */
    public function update($id)
    {
        Auth::require();

        // Verificar CSRF
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de seguridad inválido';
            header('Location: ' . BASE_URL . '/publicaciones/' . $id . '/editar');
            exit;
        }

        // Obtener publicación
        $publicacion = $this->publicacionModel->find($id);

        // Verificar permisos
        if (!$publicacion || $publicacion->usuario_id != $_SESSION['user_id']) {
            $_SESSION['error'] = 'No tienes permiso para editar esta publicación';
            header('Location: ' . BASE_URL . '/mis-publicaciones');
            exit;
        }

        // Validar datos
        $errores = $this->validarDatosPublicacion($_POST);

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['old'] = $_POST;
            header('Location: ' . BASE_URL . '/publicaciones/' . $id . '/editar');
            exit;
        }

        // Preparar datos actualizados
        $datos = [
            'categoria_padre_id' => sanitize($_POST['categoria_padre_id']),
            'subcategoria_id' => !empty($_POST['subcategoria_id']) ? sanitize($_POST['subcategoria_id']) : null,
            'titulo' => sanitize($_POST['titulo']),
            'marca' => sanitize($_POST['marca']),
            'modelo' => sanitize($_POST['modelo']),
            'anio' => (int) $_POST['anio'],
            'descripcion' => sanitize($_POST['descripcion']),
            'tipo_venta' => sanitize($_POST['tipo_venta']),
            'precio' => $_POST['tipo_venta'] === 'completo' ? (float) $_POST['precio'] : null,
            'region_id' => (int) $_POST['region_id'],
            'comuna_id' => !empty($_POST['comuna_id']) ? (int) $_POST['comuna_id'] : null,
            'estado' => 'pendiente'  // Vuelve a revisión tras editar
        ];

        // Actualizar en BD
        $this->publicacionModel->update($id, $datos);

        // Procesar nuevas imágenes si existen
        if (!empty($_FILES['imagenes']['name'][0])) {
            $this->procesarImagenes($id, $_FILES['imagenes']);
        }

        $_SESSION['success'] = 'Publicación actualizada exitosamente. Está pendiente de revisión.';
        header('Location: ' . BASE_URL . '/mis-publicaciones');
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
     */
    private function validarDatosPublicacion($datos)
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
            if (empty($datos['precio']) || $datos['precio'] <= 0) {
                $errores['precio'] = 'El precio debe ser mayor a 0';
            } elseif ($datos['precio'] > 50000000) {
                $errores['precio'] = 'El precio no puede exceder $50.000.000';
            }
        }

        // Validar descripción
        if (empty($datos['descripcion']) || strlen($datos['descripcion']) < 20) {
            $errores['descripcion'] = 'La descripción debe tener al menos 20 caracteres';
        } elseif (strlen($datos['descripcion']) > 2000) {
            $errores['descripcion'] = 'La descripción no puede exceder 2000 caracteres';
        }

        // Validar que haya al menos una foto
        if (empty($_FILES['fotos']['name'][0])) {
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

        $total_imagenes = count($archivos['name']);
        $foto_principal_url = null;

        file_put_contents($logFile, "Total de archivos recibidos: $total_imagenes\n", FILE_APPEND);

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

            // Crear directorio si no existe
            $directorio = __DIR__ . '/../../public/uploads/publicaciones/' . date('Y') . '/' . date('m');
            if (!is_dir($directorio)) {
                mkdir($directorio, 0777, true);
                file_put_contents($logFile, "  - Directorio creado: $directorio\n", FILE_APPEND);
            }

            // Ruta completa del archivo
            $ruta_completa = $directorio . '/' . $nombre_archivo;

            file_put_contents($logFile, "  - Ruta completa: $ruta_completa\n", FILE_APPEND);

            // Mover archivo subido
            if (move_uploaded_file($archivo['tmp_name'], $ruta_completa)) {
                file_put_contents($logFile, "  - ✓ Archivo movido exitosamente\n", FILE_APPEND);

                // Guardar en BD
                $ruta_relativa = 'publicaciones/' . date('Y') . '/' . date('m') . '/' . $nombre_archivo;

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
                file_put_contents($logFile, "  - ✗ ERROR al mover archivo\n", FILE_APPEND);
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
}
