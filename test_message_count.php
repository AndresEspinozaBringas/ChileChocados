<?php
/**
 * Script de prueba para verificar el contador de mensajes
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/helpers.php';

// Simular sesión de usuario (cambia el ID por uno que exista en tu BD)
$_SESSION['user_id'] = 2; // Cambia este ID por uno válido

echo "<h1>Test de Contador de Mensajes</h1>";

// Probar la función helper
echo "<h2>1. Función getMessageCount():</h2>";
$count = getMessageCount();
echo "Mensajes sin leer: <strong>$count</strong><br>";

// Probar directamente con el modelo
echo "<h2>2. Modelo Mensaje directamente:</h2>";
require_once APP_PATH . '/models/Mensaje.php';
$mensajeModel = new \App\Models\Mensaje();
$countDirect = $mensajeModel->contarNoLeidos($_SESSION['user_id']);
echo "Mensajes sin leer (directo): <strong>$countDirect</strong><br>";

// Verificar mensajes en la BD
echo "<h2>3. Consulta directa a la BD:</h2>";
$db = getDB();
$stmt = $db->prepare("SELECT COUNT(*) as total FROM mensajes WHERE destinatario_id = ? AND leido = 0");
$stmt->execute([$_SESSION['user_id']]);
$result = $stmt->fetch(PDO::FETCH_OBJ);
echo "Mensajes sin leer (BD): <strong>{$result->total}</strong><br>";

// Mostrar algunos mensajes
echo "<h2>4. Últimos mensajes sin leer:</h2>";
$stmt = $db->prepare("SELECT * FROM mensajes WHERE destinatario_id = ? AND leido = 0 ORDER BY fecha_envio DESC LIMIT 5");
$stmt->execute([$_SESSION['user_id']]);
$mensajes = $stmt->fetchAll(PDO::FETCH_OBJ);

if (empty($mensajes)) {
    echo "<p>No hay mensajes sin leer</p>";
} else {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>De</th><th>Mensaje</th><th>Fecha</th></tr>";
    foreach ($mensajes as $msg) {
        echo "<tr>";
        echo "<td>{$msg->id}</td>";
        echo "<td>{$msg->remitente_id}</td>";
        echo "<td>" . substr($msg->mensaje, 0, 50) . "...</td>";
        echo "<td>{$msg->fecha_envio}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<h2>5. Test del API:</h2>";
echo "<p>Abre en otra pestaña: <a href='" . BASE_URL . "/api/mensajes/contar' target='_blank'>" . BASE_URL . "/api/mensajes/contar</a></p>";
