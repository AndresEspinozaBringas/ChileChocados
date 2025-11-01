<?php
/**
 * Test de sesión para pago
 */

require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/helpers/Session.php';
require_once __DIR__ . '/app/helpers/Auth.php';

use App\Helpers\Session;
use App\Helpers\Auth;

echo "<h1>Test de Sesión para Pago</h1>";
echo "<hr>";

// Iniciar sesión
Session::start();

echo "<h2>1. Estado de Sesión</h2>";
echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
echo "<p><strong>Authenticated:</strong> " . (Session::get('authenticated') ? 'Sí' : 'No') . "</p>";
echo "<p><strong>User ID:</strong> " . (Session::get('user_id') ?? 'No existe') . "</p>";
echo "<p><strong>Auth::check():</strong> " . (Auth::check() ? 'Sí' : 'No') . "</p>";

echo "<hr>";

echo "<h2>2. Datos de Sesión Completos</h2>";
echo "<pre>" . print_r($_SESSION, true) . "</pre>";

echo "<hr>";

echo "<h2>3. Simular Publicación Pendiente de Pago</h2>";

// Simular que hay una publicación pendiente
$_SESSION['publicacion_pendiente_pago'] = [
    'publicacion_id' => 999,
    'tipo_destacado' => 'destacada15'
];

echo "<p>✅ Publicación pendiente guardada en sesión</p>";
echo "<pre>" . print_r($_SESSION['publicacion_pendiente_pago'], true) . "</pre>";

echo "<hr>";

echo "<h2>4. Formulario de Prueba</h2>";

if (!Auth::check()) {
    echo "<p style='color: red;'><strong>⚠️ NO ESTÁS AUTENTICADO</strong></p>";
    echo "<p>Para probar el flujo de pago, primero debes iniciar sesión:</p>";
    echo "<p><a href='" . BASE_URL . "/login'>Ir a Login</a></p>";
} else {
    echo "<p style='color: green;'><strong>✅ ESTÁS AUTENTICADO</strong></p>";
    echo "<p>Puedes probar el formulario de pago:</p>";
    
    ?>
    <form method="POST" action="<?php echo BASE_URL; ?>/pago/iniciar" style="background: #f5f5f5; padding: 20px; border-radius: 8px;">
        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
        <input type="hidden" name="publicacion_id" value="999">
        <input type="hidden" name="tipo_destacado" value="destacada15">
        
        <p><strong>Publicación ID:</strong> 999</p>
        <p><strong>Tipo:</strong> Destacada 15 días</p>
        <p><strong>Monto:</strong> $15.000</p>
        
        <button type="submit" style="padding: 12px 24px; background: #E6332A; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 16px;">
            Ir a pagar con Flow
        </button>
    </form>
    <?php
}

echo "<hr>";

echo "<h2>5. Instrucciones</h2>";
echo "<ol>";
echo "<li>Si no estás autenticado, inicia sesión primero</li>";
echo "<li>Vuelve a esta página después de iniciar sesión</li>";
echo "<li>Haz click en el botón 'Ir a pagar con Flow'</li>";
echo "<li>Revisa los logs en <code>logs/php_errors.log</code></li>";
echo "</ol>";

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
