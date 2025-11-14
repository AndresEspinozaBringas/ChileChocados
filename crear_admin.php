<?php
/**
 * Script para crear usuario administrador
 * ChileChocados
 * 
 * Uso: php crear_admin.php
 */

require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/config/database.php';

echo "===========================================\n";
echo "CREAR USUARIO ADMINISTRADOR - ChileChocados\n";
echo "===========================================\n\n";

// Datos del administrador
$nombre = 'Administrador';
$apellido = 'Sistema';
$email = 'admin@chilechocados.cl';
$password = 'Admin123!'; // Contraseña por defecto

// Generar hash de contraseña
$passwordHash = password_hash($password, PASSWORD_ARGON2ID);

echo "Generando hash de contraseña...\n";
echo "Hash generado: " . substr($passwordHash, 0, 50) . "...\n\n";

try {
    // Conectar a la base de datos
    $db = Database::getInstance()->getConnection();
    
    // Verificar si ya existe un admin
    $stmt = $db->prepare("SELECT id, email FROM usuarios WHERE email = ? OR rol = 'admin'");
    $stmt->execute([$email]);
    $existingAdmin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingAdmin) {
        echo "⚠️  Ya existe un usuario administrador:\n";
        echo "   ID: {$existingAdmin['id']}\n";
        echo "   Email: {$existingAdmin['email']}\n\n";
        
        echo "¿Deseas actualizar la contraseña? (s/n): ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        fclose($handle);
        
        if (trim(strtolower($line)) !== 's') {
            echo "\n❌ Operación cancelada.\n";
            exit(0);
        }
        
        // Actualizar contraseña
        $stmt = $db->prepare("
            UPDATE usuarios 
            SET password = ?,
                rol = 'admin',
                estado = 'activo',
                verificado = 1,
                updated_at = NOW()
            WHERE email = ?
        ");
        
        $stmt->execute([$passwordHash, $email]);
        
        echo "\n✅ Contraseña del administrador actualizada exitosamente!\n";
        
    } else {
        // Crear nuevo administrador
        $stmt = $db->prepare("
            INSERT INTO usuarios (
                nombre,
                apellido,
                email,
                password,
                rol,
                estado,
                verificado,
                created_at,
                updated_at
            ) VALUES (?, ?, ?, ?, 'admin', 'activo', 1, NOW(), NOW())
        ");
        
        $stmt->execute([
            $nombre,
            $apellido,
            $email,
            $passwordHash
        ]);
        
        echo "✅ Usuario administrador creado exitosamente!\n";
    }
    
    // Mostrar información del admin
    $stmt = $db->prepare("
        SELECT id, nombre, apellido, email, rol, estado, verificado, created_at
        FROM usuarios
        WHERE email = ?
    ");
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "\n===========================================\n";
    echo "INFORMACIÓN DEL ADMINISTRADOR\n";
    echo "===========================================\n";
    echo "ID:         {$admin['id']}\n";
    echo "Nombre:     {$admin['nombre']} {$admin['apellido']}\n";
    echo "Email:      {$admin['email']}\n";
    echo "Contraseña: {$password}\n";
    echo "Rol:        {$admin['rol']}\n";
    echo "Estado:     {$admin['estado']}\n";
    echo "Verificado: " . ($admin['verificado'] ? 'Sí' : 'No') . "\n";
    echo "Creado:     {$admin['created_at']}\n";
    echo "===========================================\n\n";
    
    echo "⚠️  IMPORTANTE: Cambia la contraseña después del primer login!\n";
    echo "🔗 URL de login: " . BASE_URL . "/login\n\n";
    
} catch (PDOException $e) {
    echo "\n❌ Error al crear administrador:\n";
    echo "   " . $e->getMessage() . "\n\n";
    exit(1);
}
