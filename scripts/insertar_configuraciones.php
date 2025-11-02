<?php
/**
 * Script para insertar configuraciones por defecto
 * Ejecutar: php scripts/insertar_configuraciones.php
 */

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';

echo "=== Insertando Configuraciones por Defecto ===\n\n";

$db = getDB();

$configuraciones = [
    [
        'clave' => 'precio_destacado_15_dias',
        'valor' => '15000',
        'tipo' => 'int',
        'descripcion' => 'Precio en CLP para destacar una publicación por 15 días'
    ],
    [
        'clave' => 'precio_destacado_30_dias',
        'valor' => '25000',
        'tipo' => 'int',
        'descripcion' => 'Precio en CLP para destacar una publicación por 30 días'
    ],
    [
        'clave' => 'minimo_fotos',
        'valor' => '1',
        'tipo' => 'int',
        'descripcion' => 'Cantidad mínima de fotos requeridas por publicación'
    ],
    [
        'clave' => 'maximo_fotos',
        'valor' => '6',
        'tipo' => 'int',
        'descripcion' => 'Cantidad máxima de fotos permitidas por publicación'
    ],
    [
        'clave' => 'tamano_maximo_imagen_mb',
        'valor' => '5',
        'tipo' => 'float',
        'descripcion' => 'Tamaño máximo en MB para cada imagen subida'
    ],
    [
        'clave' => 'tamano_maximo_adjunto_mb',
        'valor' => '10',
        'tipo' => 'float',
        'descripcion' => 'Tamaño máximo en MB para archivos adjuntos en mensajes'
    ]
];

foreach ($configuraciones as $config) {
    // Verificar si existe
    $stmt = $db->prepare("SELECT id FROM configuraciones WHERE clave = ?");
    $stmt->execute([$config['clave']]);
    $existe = $stmt->fetch(PDO::FETCH_OBJ);
    
    if ($existe) {
        // Actualizar
        $stmt = $db->prepare("
            UPDATE configuraciones 
            SET valor = ?, tipo = ?, descripcion = ?, fecha_actualizacion = NOW() 
            WHERE clave = ?
        ");
        $stmt->execute([
            $config['valor'],
            $config['tipo'],
            $config['descripcion'],
            $config['clave']
        ]);
        echo "✓ Actualizada: {$config['clave']} = {$config['valor']}\n";
    } else {
        // Insertar
        $stmt = $db->prepare("
            INSERT INTO configuraciones (clave, valor, tipo, descripcion) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $config['clave'],
            $config['valor'],
            $config['tipo'],
            $config['descripcion']
        ]);
        echo "✓ Insertada: {$config['clave']} = {$config['valor']}\n";
    }
}

echo "\n=== Configuraciones Insertadas Exitosamente ===\n\n";

// Mostrar configuraciones actuales
echo "Configuraciones actuales:\n";
echo str_repeat('-', 80) . "\n";

$stmt = $db->query("SELECT * FROM configuraciones ORDER BY clave");
$configs = $stmt->fetchAll(PDO::FETCH_OBJ);

foreach ($configs as $config) {
    echo sprintf(
        "%-35s | %-15s | %s\n",
        $config->clave,
        $config->valor,
        $config->descripcion
    );
}

echo str_repeat('-', 80) . "\n";
echo "\nTotal: " . count($configs) . " configuraciones\n";
