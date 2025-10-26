<?php
/**
 * PublicacionController
 * Controlador para gestionar todas las operaciones de publicaciones
 * 
 * @author ToroDigital
 * @date 2025-10-25
 */

namespace App\Controllers;

use App\Models\Publicacion;
use App\Models\Categoria;
use App\Helpers\Auth;
use App\Helpers\Session;
use PDO;

class PublicacionController {
    
    private $publicacionModel;
    private $categoriaModel;
    
    public function __construct() {
        $this->publicacionModel = new Publicacion();
        $this->categoriaModel = new Categoria();
    }
    
    /**
     * Muestra el listado de publicaciones con filtros
     * Ruta: GET /publicaciones
     */
    public function index() {
        // Obtener parámetros de filtros desde URL
        $filtros = [
            'categoria_id' => $_GET['categoria'] ?? null,
            'subcategoria_id' => $_GET['subcategoria'] ?? null,
            'region_id' => $_GET['region'] ?? null,
            'comuna_id' => $_GET['comuna'] ?? null,
            'precio_min' => $_GET['precio_min'] ?? null,
            'precio_max' => $_GET['precio_max'] ?? null,
            'tipo_venta' => $_GET['tipo'] ?? null, // 'completo' o 'desarme'
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
            'pagina_actual' => (int)$filtros['page'],
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
    public function show($id) {
        // Obtener publicación con toda su información
        $publicacion = $this->publicacionModel->getConRelaciones($id);
        
        if (!$publicacion || $publicacion->estado !== 'aprobada') {
            header('Location: ' . BASE_URL . '/404');
            exit;
        }
        
        // Incrementar contador de vistas
        $this->publicacionModel->incrementarVistas($id);
        
        // Obtener imágenes de la publicación
        $imagenes = $this->publicacionModel->getImagenes($id);
        
        // Obtener publicaciones similares
        $similares = $this->publicacionModel->getSimilares($id, $publicacion->categoria_padre_id, 4);
        
        $data = [
            'title' => $publicacion->titulo . ' - ChileChocados',
            'meta_description' => substr($publicacion->descripcion, 0, 160),
            'publicacion' => $publicacion,
            'imagenes' => $imagenes,
            'similares' => $similares
        ];
        
        // Cargar vista
        require_once __DIR__ . '/../views/pages/publicaciones/detail.php';
    }
    
    /**
     * Muestra el formulario para crear nueva publicación
     * Ruta: GET /publicar
     */
    public function create() {
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
    public function store() {
        // Verificar autenticación
        Auth::require();
        
        // Verificar token CSRF
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de seguridad inválido';
            header('Location: ' . BASE_URL . '/publicar');
            exit;
        }
        
        // Validar datos del formulario
        $errores = $this->validarDatosPublicacion($_POST);
        
        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['old'] = $_POST;
            header('Location: ' . BASE_URL . '/publicar');
            exit;
        }
        
        // Preparar datos para guardar
        $datos = [
            'usuario_id' => $_SESSION['user_id'],
            'categoria_padre_id' => sanitize($_POST['categoria_padre_id']),
            'subcategoria_id' => !empty($_POST['subcategoria_id']) ? sanitize($_POST['subcategoria_id']) : null,
            'titulo' => sanitize($_POST['titulo']),
            'marca' => sanitize($_POST['marca']),
            'modelo' => sanitize($_POST['modelo']),
            'anio' => (int)$_POST['anio'],
            'descripcion' => sanitize($_POST['descripcion']),
            'tipo_venta' => sanitize($_POST['tipo_venta']),
            'precio' => $_POST['tipo_venta'] === 'completo' ? (float)$_POST['precio'] : null,
            'region_id' => (int)$_POST['region_id'],
            'comuna_id' => !empty($_POST['comuna_id']) ? (int)$_POST['comuna_id'] : null,
            'estado' => 'pendiente', // Requiere aprobación del admin
            'fecha_publicacion' => date('Y-m-d H:i:s')
        ];
        
        // Guardar publicación en BD
        $publicacion_id = $this->publicacionModel->create($datos);
        
        if (!$publicacion_id) {
            $_SESSION['error'] = 'Error al crear la publicación. Intenta nuevamente.';
            header('Location: ' . BASE_URL . '/publicar');
            exit;
        }
        
        // Procesar y guardar imágenes
        if (!empty($_FILES['imagenes']['name'][0])) {
            $this->procesarImagenes($publicacion_id, $_FILES['imagenes']);
        }
        
        // Guardar ID de publicación en sesión para página de confirmación
        $_SESSION['publicacion_creada_id'] = $publicacion_id;
        
        // Redirigir a página de aprobación/confirmación
        header('Location: ' . BASE_URL . '/publicaciones/approval');
        exit;
    }
    
    /**
     * Muestra página de confirmación tras crear publicación
     * Ruta: GET /publicaciones/approval
     */
    public function approval() {
        // Verificar autenticación
        Auth::require();
        
        // Verificar que existe una publicación recién creada
        if (!isset($_SESSION['publicacion_creada_id'])) {
            header('Location: ' . BASE_URL);
            exit;
        }
        
        $publicacion_id = $_SESSION['publicacion_creada_id'];
        unset($_SESSION['publicacion_creada_id']); // Limpiar sesión
        
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
    public function edit($id) {
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
    public function update($id) {
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
            'anio' => (int)$_POST['anio'],
            'descripcion' => sanitize($_POST['descripcion']),
            'tipo_venta' => sanitize($_POST['tipo_venta']),
            'precio' => $_POST['tipo_venta'] === 'completo' ? (float)$_POST['precio'] : null,
            'region_id' => (int)$_POST['region_id'],
            'comuna_id' => !empty($_POST['comuna_id']) ? (int)$_POST['comuna_id'] : null,
            'estado' => 'pendiente' // Vuelve a revisión tras editar
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
    public function destroy($id) {
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
    public function sell() {
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
    private function validarDatosPublicacion($datos) {
        $errores = [];
        
        // Validar categoría
        if (empty($datos['categoria_padre_id'])) {
            $errores['categoria_padre_id'] = 'Debes seleccionar una categoría';
        }
        
        // Validar título
        if (empty($datos['titulo']) || strlen($datos['titulo']) < 10) {
            $errores['titulo'] = 'El título debe tener al menos 10 caracteres';
        } elseif (strlen($datos['titulo']) > 100) {
            $errores['titulo'] = 'El título no puede exceder 100 caracteres';
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
        $anio_actual = (int)date('Y');
        if (empty($datos['anio']) || $datos['anio'] < 1970 || $datos['anio'] > $anio_actual + 1) {
            $errores['anio'] = 'El año debe estar entre 1970 y ' . ($anio_actual + 1);
        }
        
        // Validar descripción
        if (empty($datos['descripcion']) || strlen($datos['descripcion']) < 50) {
            $errores['descripcion'] = 'La descripción debe tener al menos 50 caracteres';
        } elseif (strlen($datos['descripcion']) > 2000) {
            $errores['descripcion'] = 'La descripción no puede exceder 2000 caracteres';
        }
        
        // Validar tipo de venta
        if (empty($datos['tipo_venta']) || !in_array($datos['tipo_venta'], ['completo', 'desarme'])) {
            $errores['tipo_venta'] = 'Debes seleccionar un tipo de venta válido';
        }
        
        // Validar precio si es venta completa
        if ($datos['tipo_venta'] === 'completo') {
            if (empty($datos['precio']) || $datos['precio'] <= 0) {
                $errores['precio'] = 'El precio debe ser mayor a 0';
            } elseif ($datos['precio'] > 50000000) {
                $errores['precio'] = 'El precio no puede exceder $50.000.000';
            }
        }
        
        // Validar región
        if (empty($datos['region_id'])) {
            $errores['region_id'] = 'Debes seleccionar una región';
        }
        
        return $errores;
    }
    
    /**
     * Procesa y guarda las imágenes subidas
     */
    private function procesarImagenes($publicacion_id, $archivos) {
        $total_imagenes = count($archivos['name']);
        
        // Validar cantidad (máximo 6 imágenes)
        if ($total_imagenes > 6) {
            $_SESSION['warning'] = 'Solo se procesaron las primeras 6 imágenes';
            $total_imagenes = 6;
        }
        
        for ($i = 0; $i < $total_imagenes; $i++) {
            // Saltar si hay error en el archivo
            if ($archivos['error'][$i] !== UPLOAD_ERR_OK) {
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
                continue;
            }
            
            // Generar nombre único
            $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
            $nombre_archivo = uniqid('pub_' . $publicacion_id . '_') . '.' . $extension;
            
            // Crear directorio si no existe
            $directorio = __DIR__ . '/../../public/uploads/publicaciones/' . date('Y') . '/' . date('m');
            if (!is_dir($directorio)) {
                mkdir($directorio, 0777, true);
            }
            
            // Ruta completa del archivo
            $ruta_completa = $directorio . '/' . $nombre_archivo;
            
            // Mover archivo subido
            if (move_uploaded_file($archivo['tmp_name'], $ruta_completa)) {
                // Guardar en BD
                $url_relativa = 'publicaciones/' . date('Y') . '/' . date('m') . '/' . $nombre_archivo;
                
                $this->publicacionModel->agregarImagen($publicacion_id, [
                    'url' => $url_relativa,
                    'orden' => $i + 1,
                    'es_principal' => $i === 0 ? 1 : 0
                ]);
                
                // Actualizar foto principal en la publicación si es la primera
                if ($i === 0) {
                    $this->publicacionModel->update($publicacion_id, ['foto_principal' => $url_relativa]);
                }
            }
        }
    }
    
    /**
     * Valida que el archivo sea una imagen válida
     */
    private function validarImagen($archivo) {
        // Validar tamaño (máximo 5MB)
        $max_size = 5 * 1024 * 1024; // 5MB
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
    private function getRegiones() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM regiones ORDER BY nombre ASC");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}