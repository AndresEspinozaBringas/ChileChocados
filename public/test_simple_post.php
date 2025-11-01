<!DOCTYPE html>
<html>
<head>
    <title>Test Simple POST</title>
</head>
<body>
    <h1>Test Simple POST</h1>
    
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo '<div style="background: #c8e6c9; padding: 15px; margin: 10px 0;">';
        echo '<h2>✅ POST Recibido</h2>';
        echo '<pre>';
        print_r($_POST);
        echo '</pre>';
        echo '</div>';
    }
    ?>
    
    <form method="POST" action="">
        <input type="text" name="test" value="valor de prueba">
        <button type="submit">Enviar POST</button>
    </form>
    
    <hr>
    
    <h2>Test POST a /pago/iniciar</h2>
    <form method="POST" action="<?php echo 'http://chilechocados.local:8080'; ?>/pago/iniciar">
        <input type="hidden" name="csrf_token" value="test123">
        <input type="hidden" name="publicacion_id" value="1">
        <input type="hidden" name="tipo_destacado" value="destacada15">
        <button type="submit">POST a /pago/iniciar</button>
    </form>
</body>
</html>
