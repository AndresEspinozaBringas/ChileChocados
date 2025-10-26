<?php
require_once __DIR__ . '/../app/config/config.php';

echo "<h1>Configuración</h1>";
echo "<p><strong>BASE_URL:</strong> " . (defined('BASE_URL') ? BASE_URL : 'NO DEFINIDO') . "</p>";
echo "<p><strong>APP_URL:</strong> " . (defined('APP_URL') ? APP_URL : 'NO DEFINIDO') . "</p>";
echo "<p><strong>APP_PATH:</strong> " . (defined('APP_PATH') ? APP_PATH : 'NO DEFINIDO') . "</p>";
echo "<p><strong>ROOT_PATH:</strong> " . (defined('ROOT_PATH') ? ROOT_PATH : 'NO DEFINIDO') . "</p>";

echo "<h2>Variables de entorno</h2>";
echo "<p><strong>APP_URL (env):</strong> " . getenv('APP_URL') . "</p>";

echo "<h2>Servidor</h2>";
echo "<p><strong>DOCUMENT_ROOT:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>REQUEST_URI:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p><strong>HTTP_HOST:</strong> " . $_SERVER['HTTP_HOST'] . "</p>";
