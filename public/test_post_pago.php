<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Test POST a /pago/iniciar</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .info { background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .success { background: #c8e6c9; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #ffcdd2; padding: 15px; border-radius: 5px; margin: 10px 0; }
        button { padding: 12px 24px; background: #E6332A; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        button:hover { background: #c72a22; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Test: POST a /pago/iniciar</h1>
    
    <?php
    require_once __DIR__ . '/app/config/config.php';
    
    // Verificar si el usuario está logueado
    if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
        echo '<div class="error">❌ No estás autenticado. <a href="' . BASE_URL . '/login">Iniciar sesión</a></div>';
        exit;
    }
    
    echo '<div class="success">✅ Usuario autenticado: ' . $_SESSION['user_email'] . '</div>';
    
    // Verificar si hay publicación pendiente
    if (isset($_SESSION['publicacion_pendiente_pago'])) {
        $pendiente = $_SESSION['publicacion_pendiente_pago'];
        echo '<div class="info">';
        echo '<strong>Publicación pendiente de pago:</strong><br>';
        echo 'ID: ' . $pendiente['publicacion_id'] . '<br>';
        echo 'Tipo: ' . $pendiente['tipo_destacado'];
        echo '</div>';
        
        $publicacionId = $pendiente['publicacion_id'];
        $tipoDestacado = $pendiente['tipo_destacado'];
    } else {
        // Buscar última publicación del usuario
        $db = getDB();
        $stmt = $db->prepare("SELECT id FROM publicaciones WHERE usuario_id = ? ORDER BY id DESC LIMIT 1");
        $stmt->execute([$_SESSION['user_id']]);
        $pub = $stmt->fetch(PDO::FETCH_OBJ);
        
        if ($pub) {
            $publicacionId = $pub->id;
            $tipoDestacado = 'destacada15';
            echo '<div class="info">';
            echo '<strong>Usando última publicación:</strong><br>';
            echo 'ID: ' . $publicacionId . '<br>';
            echo 'Tipo: destacada15 (por defecto)';
            echo '</div>';
        } else {
            echo '<div class="error">❌ No hay publicaciones disponibles</div>';
            exit;
        }
    }
    ?>
    
    <h2>Formulario de prueba</h2>
    <form method="POST" action="<?php echo BASE_URL; ?>/pago/iniciar" id="testForm">
        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
        <input type="hidden" name="publicacion_id" value="<?php echo $publicacionId; ?>">
        <input type="hidden" name="tipo_destacado" value="<?php echo $tipoDestacado; ?>">
        
        <div style="margin: 20px 0;">
            <strong>Datos que se enviarán:</strong>
            <pre><?php
echo "csrf_token: " . generateCsrfToken() . "\n";
echo "publicacion_id: $publicacionId\n";
echo "tipo_destacado: $tipoDestacado\n";
            ?></pre>
        </div>
        
        <button type="submit">Enviar POST a /pago/iniciar</button>
    </form>
    
    <h2>Información de debugging</h2>
    <pre><?php
echo "Session ID: " . session_id() . "\n";
echo "User ID: " . $_SESSION['user_id'] . "\n";
echo "Authenticated: " . ($_SESSION['authenticated'] ? 'true' : 'false') . "\n";
echo "BASE_URL: " . BASE_URL . "\n";
echo "Action URL: " . BASE_URL . "/pago/iniciar\n";
    ?></pre>
    
    <script>
    document.getElementById('testForm').addEventListener('submit', function(e) {
        console.log('=== ENVIANDO FORMULARIO ===');
        console.log('Action:', this.action);
        console.log('Method:', this.method);
        
        const formData = new FormData(this);
        console.log('Datos:');
        for (let [key, value] of formData.entries()) {
            console.log(`  ${key}: ${value}`);
        }
        
        console.log('Abre la consola del navegador (F12) y la pestaña Network para ver la petición');
    });
    </script>
    
    <p style="margin-top: 30px; color: #666;">
        <strong>Instrucciones:</strong><br>
        1. Abre las DevTools del navegador (F12)<br>
        2. Ve a la pestaña "Network"<br>
        3. Click en el botón "Enviar POST"<br>
        4. Observa la petición HTTP y la respuesta<br>
        5. Revisa los logs en <code>logs/php_errors.log</code>
    </p>
</body>
</html>
