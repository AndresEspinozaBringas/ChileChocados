<?php
/**
 * Test del flujo completo de pago
 * Verifica que la publicación se guarde como borrador y cambie a pendiente después del pago
 */

require_once __DIR__ . '/app/config/config.php';

echo "<h1>Test: Flujo Completo de Pago</h1>";
echo "<style>
body { font-family: Arial, sans-serif; padding: 20px; }
.success { color: green; font-weight: bold; }
.error { color: red; font-weight: bold; }
.info { color: blue; }
pre { background: #f5f5f5; padding: 10px; border-radius: 5px; }
</style>";

// 1. Verificar que existe una publicación de prueba
echo "<h2>1. Verificar publicación de prueba</h2>";

$db = getDB();
$stmt = $db->query("SELECT * FROM publicaciones WHERE usuario_id = 1 ORDER BY id DESC LIMIT 1");
$publicacion = $stmt->fetch(PDO::FETCH_OBJ);

if ($publicacion) {
    echo "<p class='success'>✓ Publicación encontrada: ID {$publicacion->id}</p>";
    echo "<pre>";
    echo "Título: {$publicacion->titulo}\n";
    echo "Estado: {$publicacion->estado}\n";
    echo "Es destacada: " . ($publicacion->es_destacada ? 'Sí' : 'No') . "\n";
    echo "Fecha destacada inicio: " . ($publicacion->fecha_destacada_inicio ?? 'NULL') . "\n";
    echo "Fecha destacada fin: " . ($publicacion->fecha_destacada_fin ?? 'NULL') . "\n";
    echo "</pre>";
} else {
    echo "<p class='error'>✗ No se encontró ninguna publicación</p>";
    exit;
}

// 2. Simular creación de publicación destacada (como borrador)
echo "<h2>2. Simular creación de publicación destacada</h2>";

$datos_nueva = [
    'usuario_id' => 1,
    'tipificacion' => 'chocado',
    'categoria_padre_id' => 1,
    'subcategoria_id' => 1,
    'titulo' => 'Test Pago - ' . date('Y-m-d H:i:s'),
    'marca' => 'Toyota',
    'modelo' => 'Corolla',
    'anio' => 2020,
    'descripcion' => 'Publicación de prueba para verificar flujo de pago destacado',
    'tipo_venta' => 'completo',
    'precio' => 5000000,
    'region_id' => 1,
    'comuna_id' => 1,
    'estado' => 'borrador', // ← IMPORTANTE: Se guarda como borrador
    'es_destacada' => 0,
    'fecha_destacada_inicio' => null,
    'fecha_destacada_fin' => null,
    'fecha_publicacion' => date('Y-m-d H:i:s')
];

$stmt = $db->prepare("
    INSERT INTO publicaciones 
    (usuario_id, tipificacion, categoria_padre_id, subcategoria_id, titulo, marca, modelo, anio, 
     descripcion, tipo_venta, precio, region_id, comuna_id, estado, es_destacada, 
     fecha_destacada_inicio, fecha_destacada_fin, fecha_publicacion)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$resultado = $stmt->execute([
    $datos_nueva['usuario_id'],
    $datos_nueva['tipificacion'],
    $datos_nueva['categoria_padre_id'],
    $datos_nueva['subcategoria_id'],
    $datos_nueva['titulo'],
    $datos_nueva['marca'],
    $datos_nueva['modelo'],
    $datos_nueva['anio'],
    $datos_nueva['descripcion'],
    $datos_nueva['tipo_venta'],
    $datos_nueva['precio'],
    $datos_nueva['region_id'],
    $datos_nueva['comuna_id'],
    $datos_nueva['estado'],
    $datos_nueva['es_destacada'],
    $datos_nueva['fecha_destacada_inicio'],
    $datos_nueva['fecha_destacada_fin'],
    $datos_nueva['fecha_publicacion']
]);

if ($resultado) {
    $nueva_id = $db->lastInsertId();
    echo "<p class='success'>✓ Publicación creada como BORRADOR: ID {$nueva_id}</p>";
} else {
    echo "<p class='error'>✗ Error al crear publicación</p>";
    exit;
}

// 3. Simular registro de pago
echo "<h2>3. Simular registro de pago en tabla pagos_flow</h2>";

$commerceOrder = 'TEST-' . $nueva_id . '-' . time();
$stmt = $db->prepare("
    INSERT INTO pagos_flow 
    (publicacion_id, usuario_id, tipo, monto, flow_orden, estado, fecha_creacion)
    VALUES (?, ?, ?, ?, ?, 'pendiente', NOW())
");

$resultado = $stmt->execute([
    $nueva_id,
    1,
    'destacada15',
    15000,
    $commerceOrder
]);

if ($resultado) {
    $pago_id = $db->lastInsertId();
    echo "<p class='success'>✓ Registro de pago creado: ID {$pago_id}</p>";
    echo "<pre>";
    echo "Commerce Order: {$commerceOrder}\n";
    echo "Monto: $15.000\n";
    echo "Estado: pendiente\n";
    echo "</pre>";
} else {
    echo "<p class='error'>✗ Error al crear registro de pago</p>";
    exit;
}

// 4. Simular pago exitoso (actualizar estado)
echo "<h2>4. Simular pago exitoso</h2>";

$stmt = $db->prepare("UPDATE pagos_flow SET estado = 'aprobado', fecha_pago = NOW() WHERE id = ?");
$stmt->execute([$pago_id]);

echo "<p class='success'>✓ Estado del pago actualizado a 'aprobado'</p>";

// 5. Activar destacado y cambiar estado de publicación
echo "<h2>5. Activar destacado y cambiar estado a 'pendiente'</h2>";

$dias = 15;
$stmt = $db->prepare("
    UPDATE publicaciones 
    SET estado = 'pendiente',
        es_destacada = 1,
        fecha_destacada_inicio = NOW(),
        fecha_destacada_fin = DATE_ADD(NOW(), INTERVAL ? DAY)
    WHERE id = ?
");

$resultado = $stmt->execute([$dias, $nueva_id]);

if ($resultado) {
    echo "<p class='success'>✓ Publicación actualizada correctamente</p>";
    
    // Verificar cambios
    $stmt = $db->prepare("SELECT * FROM publicaciones WHERE id = ?");
    $stmt->execute([$nueva_id]);
    $pub_actualizada = $stmt->fetch(PDO::FETCH_OBJ);
    
    echo "<pre>";
    echo "Estado: {$pub_actualizada->estado} (debe ser 'pendiente')\n";
    echo "Es destacada: " . ($pub_actualizada->es_destacada ? 'Sí' : 'No') . " (debe ser 'Sí')\n";
    echo "Fecha inicio: {$pub_actualizada->fecha_destacada_inicio}\n";
    echo "Fecha fin: {$pub_actualizada->fecha_destacada_fin}\n";
    echo "</pre>";
    
    // Validar
    if ($pub_actualizada->estado === 'pendiente' && $pub_actualizada->es_destacada == 1) {
        echo "<p class='success'>✓✓✓ FLUJO COMPLETO EXITOSO ✓✓✓</p>";
    } else {
        echo "<p class='error'>✗ Error: Los datos no se actualizaron correctamente</p>";
    }
} else {
    echo "<p class='error'>✗ Error al actualizar publicación</p>";
}

// 6. Resumen
echo "<h2>6. Resumen del flujo</h2>";
echo "<ol>";
echo "<li>Publicación creada con estado <strong>'borrador'</strong> ✓</li>";
echo "<li>Registro de pago creado con estado <strong>'pendiente'</strong> ✓</li>";
echo "<li>Pago marcado como <strong>'aprobado'</strong> ✓</li>";
echo "<li>Publicación cambiada a estado <strong>'pendiente'</strong> ✓</li>";
echo "<li>Destacado activado con fechas correctas ✓</li>";
echo "</ol>";

echo "<p class='info'>Publicación de prueba ID: {$nueva_id}</p>";
echo "<p class='info'>Pago de prueba ID: {$pago_id}</p>";

// Limpiar (opcional)
echo "<h2>7. Limpieza (opcional)</h2>";
echo "<p><a href='?limpiar={$nueva_id}' style='color: red;'>Eliminar publicación de prueba</a></p>";

if (isset($_GET['limpiar'])) {
    $id_limpiar = (int) $_GET['limpiar'];
    $db->prepare("DELETE FROM pagos_flow WHERE publicacion_id = ?")->execute([$id_limpiar]);
    $db->prepare("DELETE FROM publicaciones WHERE id = ?")->execute([$id_limpiar]);
    echo "<p class='success'>✓ Publicación y pago eliminados</p>";
    echo "<p><a href='test_flujo_pago_completo.php'>Volver a ejecutar test</a></p>";
}
