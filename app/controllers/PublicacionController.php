<?php
/**
 * PublicacionController
 * Maneja todas las operaciones relacionadas con publicaciones
 */

require_once APP_PATH . '/models/Publicacion.php';
require_once APP_PATH . '/models/Categoria.php';

class PublicacionController {
    
    private $publicacionModel;
    private $categoriaModel;
    
    public function __construct() {
        $this->publicacionModel = new Publicacion();
        $this->categoriaModel = new Categoria();
    }
    
    /**
     * Muestra el listado de publicaciones con filtros
     */
    public function index() {
        // Obtener parámetros de búsqueda/filtro
        $filtros = [
            'categoria_id' => $_GET['categoria'] ?? null,
            'subcategoria_id' => $_GET['subcategoria'] ?? null,
            'region_id' => $_GET['region'] ?? null,
            'comuna_id' => $_GET['comuna'] ?? null,
            'precio_min' => $_GET['precio_min'] ?? null,
            'precio_max' => $_GET['precio_max'] ?? null,
            'estado' => $_GET['estado'] ?? null, // 'siniestrado' o 'desarme'
            'buscar' => $_GET['q'] ?? null,
            'orden' => $_GET['orden'] ?? 'recientes', // recientes, precio_asc, precio_desc
            'page' => $_GET['page'] ?? 1
        ];
        
        // Obtener publicaciones aplicando filtros
        $resultado = $this->publicacionModel->listarConFiltros($filtros);
        
        // Obtener datos para los filtros
        $categorias = $this->categoriaModel->obtenerCategoriasActivas();
        $regiones = $this->obtenerRegiones();
        
        $data = [
            'title' => 'Listado de Vehículos Siniestrados - ChileChocados',
            'meta_description' => 'Encuentra vehículos siniestrados y en desarme en todo Chile',
            'publicaciones' => $resultado['publicaciones'],
            'total' => $resultado['total'],
            'pagina_actual' => $filtros['page'],
            'total_paginas' => $resultado['total_paginas'],
            'filtros_aplicados' => $filtros,
            'categorias' => $categorias,
            'regiones' => $regiones
        ];
        
        require_once APP_PATH . '/views/pages/publicaciones/listado.php';
    }
    
    /**
     * Muestra el detalle de una publicación
     * 
     * @param int $id ID de la publicación
     */
    public function detalle($id) {
        $publicacion = $this->publicacionModel->obtenerPorIdCompleto($id);
        
        if (!$publicacion || $publicacion['estado'] !== 'activa') {
            header('Location: ' . BASE_URL . '/listado');
            exit;
        }
        
        // Incrementar contador de vistas
        $this->publicacionModel->incrementarVistas($id);
        
        // Obtener publicaciones similares
        $similares = $this->publicacionModel->obtenerSimilares(
            $publicacion['categoria_id'],
            $publicacion['region_id'],
            $id,
            4
        );
        
        // Verificar si el usuario actual es el propietario
        $es_propietario = false;
        if (isset($_SESSION['usuario_id'])) {
            $es_propietario = ($publicacion['usuario_id'] == $_SESSION['usuario_id']);
        }
        
        $data = [
            'title' => $publicacion['titulo'] . ' - ChileChocados',
            'meta_description' => substr(strip_tags($publicacion['descripcion']), 0, 160),
            'publicacion' => $publicacion,
            'similares' => $similares,
            'es_propietario' => $es_propietario
        ];
        
        require_once APP_PATH . '/views/pages/publicaciones/detalle.php';
    }
    
    /**
     * Muestra el formulario para crear una nueva publicación
     * Requiere autenticación
     */
    public function crear() {
        // Verificar autenticación
        if (!isset($_SESSION['usuario_id'])) {
            $_SESSION['redirect_after_login'] = BASE_URL . '/publicaciones/crear';
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
        
        // Obtener datos para el formulario
        $categorias = $this->categoriaModel->obtenerCategoriasConSubcategorias();
        $regiones = $this->obtenerRegiones();
        
        $data = [
            'title' => 'Publicar Vehículo - ChileChocados',
            'meta_description' => 'Publica tu vehículo siniestrado o en desarme',
            'categorias' => $categorias,
            'regiones' => $regiones
        ];
        
        require_once APP_PATH . '/views/pages/publicaciones/crear.php';
    }
    
    /**
     * Procesa el guardado de una nueva publicación
     */
    public function guardar() {
        // Verificar autenticación
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
        
        // Verificar método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/publicaciones/crear');
            exit;
        }
        
        // Verificar token CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['error'] = 'Token de seguridad inválido';
            header('Location: ' . BASE_URL . '/publicaciones/crear');
            exit;
        }
        
        // Validar y sanitizar datos
        $datos = [
            'usuario_id' => $_SESSION['usuario_id'],
            'categoria_id' => filter_input(INPUT_POST, 'categoria_id', FILTER_VALIDATE_INT),
            'subcategoria_id' => filter_input(INPUT_POST, 'subcategoria_id', FILTER_VALIDATE_INT),
            'titulo' => filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_STRING),
            'descripcion' => filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING),
            'precio' => filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT),
            'precio_negociable' => isset($_POST['precio_negociable']) ? 1 : 0,
            'region_id' => filter_input(INPUT_POST, 'region_id', FILTER_VALIDATE_INT),
            'comuna_id' => filter_input(INPUT_POST, 'comuna_id', FILTER_VALIDATE_INT),
            'marca' => filter_input(INPUT_POST, 'marca', FILTER_SANITIZE_STRING),
            'modelo' => filter_input(INPUT_POST, 'modelo', FILTER_SANITIZE_STRING),
            'ano' => filter_input(INPUT_POST, 'ano', FILTER_VALIDATE_INT),
            'kilometraje' => filter_input(INPUT_POST, 'kilometraje', FILTER_VALIDATE_INT),
            'tipo_vehiculo' => filter_input(INPUT_POST, 'tipo_vehiculo', FILTER_SANITIZE_STRING),
            'estado_vehiculo' => filter_input(INPUT_POST, 'estado_vehiculo', FILTER_SANITIZE_STRING)
        ];
        
        // Validaciones
        $errores = $this->validarDatosPublicacion($datos);
        
        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['datos_publicacion'] = $datos;
            header('Location: ' . BASE_URL . '/publicaciones/crear');
            exit;
        }
        
        // Guardar publicación
        try {
            $publicacion_id = $this->publicacionModel->crear($datos);
            
            // Procesar imágenes si hay
            if (!empty($_FILES['imagenes']['name'][0])) {
                $this->procesarImagenes($publicacion_id, $_FILES['imagenes']);
            }
            
            $_SESSION['success'] = 'Publicación creada exitosamente. Está en revisión.';
            header('Location: ' . BASE_URL . '/publicaciones/detalle/' . $publicacion_id);
            exit;
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al crear la publicación. Intenta nuevamente.';
            error_log('Error al crear publicación: ' . $e->getMessage());
            header('Location: ' . BASE_URL . '/publicaciones/crear');
            exit;
        }
    }
    
    /**
     * Valida los datos de una publicación
     * 
     * @param array $datos Datos a validar
     * @return array Array de errores (vacío si no hay errores)
     */
    private function validarDatosPublicacion($datos) {
        $errores = [];
        
        if (empty($datos['titulo']) || strlen($datos['titulo']) < 10) {
            $errores[] = 'El título debe tener al menos 10 caracteres';
        }
        
        if (empty($datos['descripcion']) || strlen($datos['descripcion']) < 50) {
            $errores[] = 'La descripción debe tener al menos 50 caracteres';
        }
        
        if (empty($datos['categoria_id'])) {
            $errores[] = 'Debes seleccionar una categoría';
        }
        
        if (empty($datos['region_id'])) {
            $errores[] = 'Debes seleccionar una región';
        }
        
        if (empty($datos['comuna_id'])) {
            $errores[] = 'Debes seleccionar una comuna';
        }
        
        if ($datos['precio'] !== null && $datos['precio'] < 0) {
            $errores[] = 'El precio no puede ser negativo';
        }
        
        return $errores;
    }
    
    /**
     * Procesa y guarda las imágenes de una publicación
     * 
     * @param int $publicacion_id ID de la publicación
     * @param array $files Array de archivos $_FILES
     */
    private function procesarImagenes($publicacion_id, $files) {
        $upload_dir = PUBLIC_PATH . '/uploads/publicaciones/' . $publicacion_id . '/';
        
        // Crear directorio si no existe
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $tipos_permitidos = ['image/jpeg', 'image/png', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        $orden = 1;
        foreach ($files['tmp_name'] as $key => $tmp_name) {
            if (empty($tmp_name)) continue;
            
            $file_type = $files['type'][$key];
            $file_size = $files['size'][$key];
            
            // Validar tipo y tamaño
            if (!in_array($file_type, $tipos_permitidos) || $file_size > $max_size) {
                continue;
            }
            
            // Generar nombre único
            $extension = pathinfo($files['name'][$key], PATHINFO_EXTENSION);
            $nombre_archivo = uniqid() . '.' . $extension;
            $ruta_completa = $upload_dir . $nombre_archivo;
            
            // Mover archivo
            if (move_uploaded_file($tmp_name, $ruta_completa)) {
                // Guardar en base de datos
                $this->publicacionModel->guardarFoto([
                    'publicacion_id' => $publicacion_id,
                    'ruta' => '/uploads/publicaciones/' . $publicacion_id . '/' . $nombre_archivo,
                    'orden' => $orden,
                    'es_principal' => ($orden === 1) ? 1 : 0
                ]);
                
                $orden++;
            }
        }
    }
    
    /**
     * Obtiene el listado de regiones
     * 
     * @return array
     */
    private function obtenerRegiones() {
        // Esto debería venir de un modelo Region
        // Por ahora retornamos un array simple
        return $this->publicacionModel->obtenerRegiones();
    }
}
