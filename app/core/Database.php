<?php
/**
 * ChileChocados - Configuración de Base de Datos
 * Gestión de conexión PDO a MySQL
 */

namespace App\Core;

use PDO;
use PDOException;
use Exception;

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $host = getenv('DB_HOST');
            $port = getenv('DB_PORT') ?: 3306;
            $dbname = getenv('DB_NAME');
            $username = getenv('DB_USER');
            $password = getenv('DB_PASS');
            
            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];
            
            $this->connection = new PDO($dsn, $username, $password, $options);
            
        } catch (PDOException $e) {
            $this->logError($e);
            die('Error de conexión a la base de datos. Por favor, contacte al administrador.');
        }
    }
    
    /**
     * Obtener instancia única de Database (Singleton)
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Obtener conexión PDO
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Prevenir clonación
     */
    private function __clone() {}
    
    /**
     * Prevenir deserialización
     */
    public function __wakeup() {
        throw new Exception("No se puede deserializar la instancia de Database");
    }
    
    /**
     * Registrar errores en log
     */
    private function logError($error) {
        $logDir = dirname(__DIR__, 2) . '/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        $logFile = $logDir . '/database_errors.log';
        $message = date('Y-m-d H:i:s') . ' - ' . $error->getMessage() . PHP_EOL;
        error_log($message, 3, $logFile);
    }
    
    /**
     * Cerrar conexión
     */
    public function closeConnection() {
        $this->connection = null;
    }
}
