<?php
/**
 * Script de prueba: Incrementar visitas de publicaciones
 */

require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/config/database.php';

$db = getDB();

echo "=== Incrementando visitas de prueba ===\n\n";

// Obtener publicaciones aprobadas
$stmt = $db->query("SELECT id, titulo, visitas FROM publicaciones WHERE estado = 'aprobada' ORDER BY id DESC LIMIT 5");
$publicaciones = $stmt->fetchAll(PDO::FETCH_OBJ);

if (empty($publicaciones)) {
    echo "❌ No hay publicaciones aprobadas\n";
    exit;
}

foreach ($publicaciones as $pub) {
    // Incrementar visitas aleatoriamente entre 5 y 50
    $visitas_aleatorias = rand(5, 50);
    
    $stmt = $db->prepare("UPDATE publicaciones SET visitas = visitas + ? WHERE id = ?");
    $stmt->execute([$visitas_aleatorias, $pub->id]);
    
    $visitas_nuevas = $pub->visitas + $visitas_aleatorias;
    
    echo "✅ Publicación #{$pub->id}: {$pub->titulo}\n";
    echo "   Visitas anteriores: {$pub->visitas}\n";
    echo "   Visitas agregadas: +{$visitas_aleatorias}\n";
    echo "   Visitas totales: {$visitas_nuevas}\n\n";
}

echo "=== Verificando resultados ===\n\n";

$stmt = $db->query("SELECT id, titulo, visitas FROM publicaciones WHERE estado = 'aprobada' ORDER BY visitas DESC LIMIT 5");
$top = $stmt->fetchAll(PDO::FETCH_OBJ);

echo "Top 5 publicaciones más vistas:\n";
foreach ($top as $i => $pub) {
    echo ($i + 1) . ". {$pub->titulo}: " . number_format($pub->visitas) . " visitas\n";
}

echo "\n✅ Ahora puedes verificar en http://chilechocados.local:8080/mis-publicaciones\n";
