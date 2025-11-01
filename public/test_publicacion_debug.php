<?php
/**
 * Test para debuggear la publicación y ver qué datos se están cargando
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/models/Model.php';
require_once __DIR__ . '/../app/models/Publicacion.php';

session_start();

use App\Models\Publicacion;

$publicacionModel = new Publicacion();
$publicacion = $publicacionModel->getConRelaciones(10);

echo "<h1>Debug Publicación ID 10</h1>";

echo "<h2>Datos de la Publicación:</h2>";
echo "<pre>";
print_r($publicacion);
echo "</pre>";

echo "<h2>Datos de Sesión:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Verificación de Condiciones:</h2>";
echo "<ul>";
echo "<li>¿Sesión iniciada? " . (isset($_SESSION['user_id']) ? 'SÍ (ID: ' . $_SESSION['user_id'] . ')' : 'NO') . "</li>";
echo "<li>Usuario ID de la publicación: " . ($publicacion->usuario_id ?? 'NO DEFINIDO') . "</li>";
echo "<li>¿Es el dueño? " . (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $publicacion->usuario_id ? 'SÍ' : 'NO') . "</li>";
echo "<li>¿Debería ver el botón? " . (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $publicacion->usuario_id ? 'SÍ' : 'NO') . "</li>";
echo "</ul>";

echo "<h2>Campos del Vendedor:</h2>";
echo "<ul>";
echo "<li>vendedor_nombre: " . ($publicacion->vendedor_nombre ?? 'NO DEFINIDO') . "</li>";
echo "<li>vendedor_apellido: " . ($publicacion->vendedor_apellido ?? 'NO DEFINIDO') . "</li>";
echo "<li>vendedor_email: " . ($publicacion->vendedor_email ?? 'NO DEFINIDO') . "</li>";
echo "<li>vendedor_telefono: " . ($publicacion->vendedor_telefono ?? 'NO DEFINIDO') . "</li>";
echo "</ul>";
