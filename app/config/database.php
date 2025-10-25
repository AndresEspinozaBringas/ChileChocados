<?php
/**
 * ChileChocados - Configuración de Base de Datos
 * Carga la clase Database desde app/core
 */

require_once __DIR__ . '/../core/Database.php';

use App\Core\Database;

/**
 * Función helper para obtener la conexión PDO
 */
function getDB() {
    return Database::getInstance()->getConnection();
}
