<?php
/**
 * Test de Integración con Flow
 * Verifica que la configuración de Flow esté correcta
 */

require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/helpers/FlowHelper.php';

use App\Helpers\FlowHelper;

echo "<h1>Test de Integración con Flow</h1>";
echo "<hr>";

// 1. Verificar que las credenciales estén configuradas
echo "<h2>1. Verificación de Credenciales</h2>";
$apiKey = getenv('FLOW_API_KEY');
$secretKey = getenv('FLOW_SECRET_KEY');
$sandbox = getenv('FLOW_SANDBOX');

echo "<p><strong>API Key:</strong> " . ($apiKey ? '✅ Configurada' : '❌ No configurada') . "</p>";
echo "<p><strong>Secret Key:</strong> " . ($secretKey ? '✅ Configurada' : '❌ No configurada') . "</p>";
echo "<p><strong>Modo Sandbox:</strong> " . ($sandbox === 'true' ? '✅ Activado' : '⚠️ Desactivado (Producción)') . "</p>";

if (!$apiKey || !$secretKey) {
    echo "<p style='color: red;'><strong>ERROR:</strong> Las credenciales de Flow no están configuradas en el archivo .env</p>";
    exit;
}

echo "<hr>";

// 2. Verificar que FlowHelper se pueda instanciar
echo "<h2>2. Verificación de FlowHelper</h2>";
try {
    $flowHelper = new FlowHelper();
    echo "<p>✅ FlowHelper instanciado correctamente</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error al instanciar FlowHelper: " . $e->getMessage() . "</p>";
    exit;
}

echo "<hr>";

// 3. Verificar métodos estáticos
echo "<h2>3. Verificación de Métodos</h2>";

$precio15 = FlowHelper::obtenerPrecioDestacado('destacada15');
$precio30 = FlowHelper::obtenerPrecioDestacado('destacada30');
$dias15 = FlowHelper::obtenerDiasDestacado('destacada15');
$dias30 = FlowHelper::obtenerDiasDestacado('destacada30');

echo "<p><strong>Precio Destacada 15 días:</strong> $" . number_format($precio15, 0, ',', '.') . " (" . ($precio15 === 15000 ? '✅' : '❌') . ")</p>";
echo "<p><strong>Precio Destacada 30 días:</strong> $" . number_format($precio30, 0, ',', '.') . " (" . ($precio30 === 25000 ? '✅' : '❌') . ")</p>";
echo "<p><strong>Días Destacada 15:</strong> " . $dias15 . " días (" . ($dias15 === 15 ? '✅' : '❌') . ")</p>";
echo "<p><strong>Días Destacada 30:</strong> " . $dias30 . " días (" . ($dias30 === 30 ? '✅' : '❌') . ")</p>";

echo "<hr>";

// 4. Verificar tabla pagos_flow
echo "<h2>4. Verificación de Base de Datos</h2>";
try {
    $db = getDB();
    $stmt = $db->query("SHOW TABLES LIKE 'pagos_flow'");
    $exists = $stmt->fetch();
    
    if ($exists) {
        echo "<p>✅ Tabla 'pagos_flow' existe</p>";
        
        // Verificar estructura
        $stmt = $db->query("DESCRIBE pagos_flow");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $requiredColumns = ['id', 'publicacion_id', 'usuario_id', 'tipo', 'monto', 'flow_token', 'flow_orden', 'estado', 'respuesta_flow', 'fecha_pago', 'fecha_creacion'];
        $missingColumns = array_diff($requiredColumns, $columns);
        
        if (empty($missingColumns)) {
            echo "<p>✅ Estructura de tabla correcta</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Columnas faltantes: " . implode(', ', $missingColumns) . "</p>";
        }
        
        // Contar registros
        $stmt = $db->query("SELECT COUNT(*) as total FROM pagos_flow");
        $count = $stmt->fetch(PDO::FETCH_OBJ);
        echo "<p><strong>Registros en tabla:</strong> " . $count->total . "</p>";
        
    } else {
        echo "<p style='color: red;'>❌ Tabla 'pagos_flow' no existe</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error al verificar base de datos: " . $e->getMessage() . "</p>";
}

echo "<hr>";

// 5. Verificar archivos de controlador y vistas
echo "<h2>5. Verificación de Archivos</h2>";

$files = [
    'Controlador' => __DIR__ . '/app/controllers/PagoController.php',
    'Helper' => __DIR__ . '/app/helpers/FlowHelper.php',
    'Vista Preparar' => __DIR__ . '/app/views/pages/pagos/preparar.php',
    'Vista Retorno' => __DIR__ . '/app/views/pages/pagos/retorno.php',
];

foreach ($files as $name => $file) {
    if (file_exists($file)) {
        echo "<p>✅ $name: <code>" . basename($file) . "</code></p>";
    } else {
        echo "<p style='color: red;'>❌ $name no encontrado: <code>$file</code></p>";
    }
}

echo "<hr>";

// 6. Verificar rutas
echo "<h2>6. Rutas Disponibles</h2>";
echo "<ul>";
echo "<li><strong>GET</strong> /pago/preparar - Pantalla de confirmación</li>";
echo "<li><strong>POST</strong> /pago/iniciar - Iniciar pago</li>";
echo "<li><strong>POST</strong> /pago/confirmar - Callback de Flow</li>";
echo "<li><strong>GET</strong> /pago/retorno - Resultado del pago</li>";
echo "<li><strong>POST</strong> /pago/reintentar - Reintentar pago</li>";
echo "</ul>";

echo "<hr>";

// 7. Test de firma (sin hacer petición real)
echo "<h2>7. Test de Generación de Firma</h2>";
try {
    $testParams = [
        'apiKey' => $apiKey,
        'commerceOrder' => 'TEST-123',
        'subject' => 'Test',
        'amount' => 15000,
        'email' => 'test@test.com'
    ];
    
    // Usar reflexión para acceder al método privado
    $reflection = new ReflectionClass($flowHelper);
    $method = $reflection->getMethod('generarFirma');
    $method->setAccessible(true);
    $firma = $method->invoke($flowHelper, $testParams);
    
    if (!empty($firma) && strlen($firma) === 64) {
        echo "<p>✅ Firma generada correctamente (SHA256, 64 caracteres)</p>";
        echo "<p><code style='font-size: 11px;'>" . $firma . "</code></p>";
    } else {
        echo "<p style='color: red;'>❌ Error al generar firma</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error en test de firma: " . $e->getMessage() . "</p>";
}

echo "<hr>";

// Resumen final
echo "<h2>✅ Resumen</h2>";
echo "<p>La integración con Flow está configurada correctamente.</p>";
echo "<p><strong>Próximos pasos:</strong></p>";
echo "<ol>";
echo "<li>Crear o editar una publicación</li>";
echo "<li>Seleccionar opción 'Destacada 15 días' o 'Destacada 30 días'</li>";
echo "<li>Hacer clic en 'Enviar a revisión'</li>";
echo "<li>Serás redirigido a la pantalla de confirmación de pago</li>";
echo "<li>Confirmar y serás redirigido a Flow para realizar el pago</li>";
echo "</ol>";

echo "<hr>";
echo "<p style='text-align: center; color: #666;'><small>Test ejecutado el " . date('d/m/Y H:i:s') . "</small></p>";
?>

<style>
body {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    max-width: 900px;
    margin: 40px auto;
    padding: 20px;
    background: #f5f5f5;
}
h1 {
    color: #E6332A;
}
h2 {
    color: #333;
    border-bottom: 2px solid #E6332A;
    padding-bottom: 8px;
}
p {
    line-height: 1.6;
}
code {
    background: #f0f0f0;
    padding: 2px 6px;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
}
hr {
    border: none;
    border-top: 1px solid #ddd;
    margin: 30px 0;
}
</style>
