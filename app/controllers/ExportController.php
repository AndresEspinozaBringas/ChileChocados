<?php
/**
 * ExportController
 * Controlador para exportación de datos (Excel, CSV, PDF)
 * 
 * @author ChileChocados
 * @date 2025-10-30
 */

namespace App\Controllers;

use PDO;
use App\Helpers\Auth;

class ExportController
{
    private $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    /**
     * Verificar que el usuario es administrador
     */
    private function requireAdmin()
    {
        if (!Auth::check() || !Auth::isAdmin()) {
            header('Location: /login');
            exit;
        }
    }

    /**
     * Exportar publicaciones a CSV
     * Ruta: GET /admin/export/publicaciones
     */
    public function exportarPublicaciones()
    {
        $this->requireAdmin();
        
        // Obtener filtros de la URL
        $estado = $_GET['estado'] ?? '';
        $categoria = $_GET['categoria'] ?? '';
        $busqueda = $_GET['q'] ?? '';
        
        // Construir query
        $sql = "SELECT p.id, p.titulo, p.marca, p.modelo, p.anio, p.precio,
                       p.estado, p.fecha_creacion, p.fecha_publicacion,
                       u.nombre as usuario_nombre, u.email as usuario_email,
                       cp.nombre as categoria, r.nombre as region, c.nombre as comuna
                FROM publicaciones p
                INNER JOIN usuarios u ON p.usuario_id = u.id
                LEFT JOIN categorias_padre cp ON p.categoria_padre_id = cp.id
                LEFT JOIN regiones r ON p.region_id = r.id
                LEFT JOIN comunas c ON p.comuna_id = c.id
                WHERE 1=1";
        
        $params = [];
        
        if ($estado) {
            $sql .= " AND p.estado = ?";
            $params[] = $estado;
        }
        
        if ($categoria) {
            $sql .= " AND p.categoria_padre_id = ?";
            $params[] = $categoria;
        }
        
        if ($busqueda) {
            $sql .= " AND (p.titulo LIKE ? OR p.marca LIKE ? OR p.modelo LIKE ?)";
            $search = "%{$busqueda}%";
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }
        
        $sql .= " ORDER BY p.fecha_creacion DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $publicaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Generar CSV
        $this->generateCSV($publicaciones, 'publicaciones_' . date('Y-m-d'));
    }

    /**
     * Exportar usuarios a CSV
     * Ruta: GET /admin/export/usuarios
     */
    public function exportarUsuarios()
    {
        $this->requireAdmin();
        
        // Obtener filtros
        $rol = $_GET['rol'] ?? '';
        $estado = $_GET['estado'] ?? '';
        $busqueda = $_GET['q'] ?? '';
        
        $sql = "SELECT u.id, u.nombre, u.apellido, u.email, u.telefono, u.rut,
                       u.rol, u.estado, u.fecha_registro, u.ultima_conexion,
                       COUNT(DISTINCT p.id) as total_publicaciones
                FROM usuarios u
                LEFT JOIN publicaciones p ON u.id = p.usuario_id
                WHERE 1=1";
        
        $params = [];
        
        if ($rol) {
            $sql .= " AND u.rol = ?";
            $params[] = $rol;
        }
        
        if ($estado) {
            $sql .= " AND u.estado = ?";
            $params[] = $estado;
        }
        
        if ($busqueda) {
            $sql .= " AND (u.nombre LIKE ? OR u.apellido LIKE ? OR u.email LIKE ?)";
            $search = "%{$busqueda}%";
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }
        
        $sql .= " GROUP BY u.id ORDER BY u.fecha_registro DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $this->generateCSV($usuarios, 'usuarios_' . date('Y-m-d'));
    }

    /**
     * Generar archivo CSV
     */
    private function generateCSV($data, $filename)
    {
        if (empty($data)) {
            echo "No hay datos para exportar";
            return;
        }
        
        // Headers para descarga
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Abrir output
        $output = fopen('php://output', 'w');
        
        // BOM para UTF-8 (Excel compatibility)
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Headers (nombres de columnas)
        fputcsv($output, array_keys($data[0]));
        
        // Datos
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        
        fclose($output);
        exit;
    }
}
