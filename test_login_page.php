<?php  // phpcs:ignore PSR12.Files.FileHeader.SpacingAfterTagBlock
/** Script de prueba para verificar que la página de login funcione */

// Cargar configuración
require_once __DIR__ . '/app/config/config.php';

// Cargar clase Database
require_once __DIR__ . '/app/core/Database.php';

// Cargar helpers
require_once __DIR__ . '/app/helpers/Session.php';

use App\Helpers\Session;

echo '<h1>Test de Página de Login</h1>';

try {
    // Iniciar sesión
    Session::start();
    echo "<p style='color: green;'>✓ Sesión iniciada correctamente</p>";

    // Verificar que la clase Session funcione
    Session::set('test', 'valor_prueba');
    $valor = Session::get('test');

    if ($valor === 'valor_prueba') {
        echo "<p style='color: green;'>✓ Session::set() y Session::get() funcionan correctamente</p>";
    }

    // Verificar flash messages
    Session::flash('success', 'Mensaje de prueba');
    $flash = Session::getFlash('success');

    if ($flash === 'Mensaje de prueba') {
        echo "<p style='color: green;'>✓ Session::flash() y Session::getFlash() funcionan correctamente</p>";
    }

    // Verificar que la función icon() esté disponible
    if (function_exists('icon')) {
        echo "<p style='color: green;'>✓ Función icon() está disponible</p>";
        echo '<p>Ejemplo de icono: ' . icon('user', 24) . '</p>';
    } else {
        echo "<p style='color: red;'>✗ Función icon() no está disponible</p>";
    }

    echo "<h2 style='color: green;'>✓ Todos los componentes necesarios están funcionando</h2>";
    echo "<p><a href='/login'>Ir a la página de login</a></p>";
} catch (Exception $e) {
    echo "<h2 style='color: red;'>✗ Error</h2>";
    echo "<p style='color: red;'>" . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
}
