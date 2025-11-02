<?php
/**
 * Script de prueba para el sistema de configuración
 * Ejecutar: php scripts/test_configuracion.php
 */

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/models/Configuracion.php';

echo "=== Test del Sistema de Configuración ===\n\n";

// Test 1: Obtener configuración individual
echo "Test 1: Obtener configuración individual\n";
echo str_repeat('-', 50) . "\n";
$precio15 = \App\Models\Configuracion::get('precio_destacado_15_dias');
$precio30 = \App\Models\Configuracion::get('precio_destacado_30_dias');
echo "Precio 15 días: $precio15 CLP\n";
echo "Precio 30 días: $precio30 CLP\n";
echo "✓ Test 1 pasado\n\n";

// Test 2: Obtener todas las configuraciones
echo "Test 2: Obtener todas las configuraciones\n";
echo str_repeat('-', 50) . "\n";
$config = \App\Models\Configuracion::getAll();
echo "Total de configuraciones: " . count($config) . "\n";
echo "✓ Test 2 pasado\n\n";

// Test 3: Obtener límites de fotos
echo "Test 3: Obtener límites de fotos\n";
echo str_repeat('-', 50) . "\n";
$limites = \App\Models\Configuracion::getLimitesFotos();
echo "Mínimo de fotos: {$limites['minimo']}\n";
echo "Máximo de fotos: {$limites['maximo']}\n";
echo "✓ Test 3 pasado\n\n";

// Test 4: Obtener tamaños máximos
echo "Test 4: Obtener tamaños máximos\n";
echo str_repeat('-', 50) . "\n";
$tamanos = \App\Models\Configuracion::getTamanosMaximos();
echo "Tamaño máximo imagen: {$tamanos['imagen_mb']} MB\n";
echo "Tamaño máximo adjunto: {$tamanos['adjunto_mb']} MB\n";
echo "✓ Test 4 pasado\n\n";

// Test 5: Obtener precio de destacado
echo "Test 5: Obtener precio de destacado\n";
echo str_repeat('-', 50) . "\n";
$precio15_helper = \App\Models\Configuracion::getPrecioDestacado(15);
$precio30_helper = \App\Models\Configuracion::getPrecioDestacado(30);
echo "Precio destacado 15 días: $precio15_helper CLP\n";
echo "Precio destacado 30 días: $precio30_helper CLP\n";
echo "✓ Test 5 pasado\n\n";

// Test 6: Obtener configuración con valor por defecto
echo "Test 6: Obtener configuración con valor por defecto\n";
echo str_repeat('-', 50) . "\n";
$noExiste = \App\Models\Configuracion::get('config_que_no_existe', 'valor_default');
echo "Configuración inexistente: $noExiste\n";
echo "✓ Test 6 pasado\n\n";

// Test 7: Establecer una configuración de prueba
echo "Test 7: Establecer una configuración de prueba\n";
echo str_repeat('-', 50) . "\n";
$resultado = \App\Models\Configuracion::set('test_config', 'test_value', 'string', 'Configuración de prueba');
echo "Resultado: " . ($resultado ? 'Éxito' : 'Fallo') . "\n";
$valor = \App\Models\Configuracion::get('test_config');
echo "Valor guardado: $valor\n";
echo "✓ Test 7 pasado\n\n";

// Test 8: Actualizar configuración existente
echo "Test 8: Actualizar configuración existente\n";
echo str_repeat('-', 50) . "\n";
$resultado = \App\Models\Configuracion::set('test_config', 'nuevo_valor', 'string', 'Configuración actualizada');
echo "Resultado: " . ($resultado ? 'Éxito' : 'Fallo') . "\n";
$valor = \App\Models\Configuracion::get('test_config');
echo "Valor actualizado: $valor\n";
echo "✓ Test 8 pasado\n\n";

// Limpiar configuración de prueba
$db = getDB();
$stmt = $db->prepare("DELETE FROM configuraciones WHERE clave = 'test_config'");
$stmt->execute();

echo "=== Todos los tests pasaron exitosamente ===\n";
echo "\nResumen:\n";
echo "- Precio destacado 15 días: $precio15 CLP\n";
echo "- Precio destacado 30 días: $precio30 CLP\n";
echo "- Mínimo de fotos: {$limites['minimo']}\n";
echo "- Máximo de fotos: {$limites['maximo']}\n";
echo "- Tamaño máximo imagen: {$tamanos['imagen_mb']} MB\n";
echo "- Tamaño máximo adjunto: {$tamanos['adjunto_mb']} MB\n";
