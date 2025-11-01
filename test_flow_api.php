<?php
/**
 * Test directo de la API de Flow
 */

require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/helpers/FlowHelper.php';

use App\Helpers\FlowHelper;

echo "<h1>Test de API Flow</h1>";
echo "<hr>";

// Instanciar FlowHelper
$flowHelper = new FlowHelper();

echo "<h2>1. Configuración</h2>";
echo "<p><strong>API Key:</strong> " . getenv('FLOW_API_KEY') . "</p>";
echo "<p><strong>Secret Key:</strong> " . substr(getenv('FLOW_SECRET_KEY'), 0, 10) . "...</p>";
echo "<p><strong>Sandbox:</strong> " . (getenv('FLOW_SANDBOX') === 'true' ? 'Sí' : 'No') . "</p>";
echo "<hr>";

// Preparar datos de prueba
$testParams = [
    'commerceOrder' => 'TEST-' . time(),
    'subject' => 'Test de Pago',
    'amount' => 15000,
    'email' => 'test@chilechocados.cl',
    'urlConfirmation' => BASE_URL . '/pago/confirmar',
    'urlReturn' => BASE_URL . '/pago/retorno?test=1',
    'optional' => json_encode(['test' => true])
];

echo "<h2>2. Parámetros de Prueba</h2>";
echo "<pre>" . print_r($testParams, true) . "</pre>";
echo "<hr>";

// Intentar crear orden
echo "<h2>3. Crear Orden en Flow</h2>";
echo "<p>Intentando crear orden...</p>";

try {
    $response = $flowHelper->crearOrden($testParams);
    
    if ($response) {
        echo "<p style='color: green;'><strong>✅ Orden creada exitosamente</strong></p>";
        echo "<pre>" . print_r($response, true) . "</pre>";
        
        if (isset($response['token'])) {
            $urlPago = $flowHelper->obtenerUrlPago($response['token']);
            echo "<p><strong>URL de Pago:</strong></p>";
            echo "<p><a href='$urlPago' target='_blank'>$urlPago</a></p>";
        }
    } else {
        echo "<p style='color: red;'><strong>❌ Error al crear orden</strong></p>";
        echo "<p>Revisa los logs en logs/php_errors.log para más detalles</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>❌ Excepción:</strong> " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h2>4. Logs</h2>";
echo "<p>Revisa el archivo <code>logs/php_errors.log</code> para ver los detalles de la petición a Flow</p>";

?>

<style>
body {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    max-width: 900px;
    margin: 40px auto;
    padding: 20px;
    background: #f5f5f5;
}
h1 { color: #E6332A; }
h2 { color: #333; border-bottom: 2px solid #E6332A; padding-bottom: 8px; }
pre {
    background: #f0f0f0;
    padding: 15px;
    border-radius: 8px;
    overflow-x: auto;
    font-size: 12px;
}
code {
    background: #f0f0f0;
    padding: 2px 6px;
    border-radius: 4px;
}
hr {
    border: none;
    border-top: 1px solid #ddd;
    margin: 30px 0;
}
</style>
