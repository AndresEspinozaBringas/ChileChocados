<?php
/**
 * Test de conexión SMTP directa
 * Verifica que podemos conectarnos a Gmail SMTP
 */

echo "===========================================\n";
echo "TEST DE CONEXIÓN SMTP DIRECTA\n";
echo "===========================================\n\n";

// Configuración
$host = 'smtp.gmail.com';
$port = 587;
$username = 'aespinooza@oopart.cl';
$password = 'bkrt ykce fcqd lbts'; // App password de Gmail

echo "Host: {$host}\n";
echo "Port: {$port}\n";
echo "Username: {$username}\n";
echo "Password: " . str_repeat('*', strlen($password)) . "\n\n";

// Test 1: Verificar que el puerto esté abierto
echo "===========================================\n";
echo "TEST 1: Verificar puerto SMTP\n";
echo "===========================================\n";

$connection = @fsockopen($host, $port, $errno, $errstr, 10);

if ($connection) {
    echo "✅ Puerto {$port} está abierto y accesible\n";
    fclose($connection);
} else {
    echo "❌ No se puede conectar al puerto {$port}\n";
    echo "Error: {$errstr} ({$errno})\n";
    exit(1);
}

echo "\n";

// Test 2: Verificar extensión OpenSSL
echo "===========================================\n";
echo "TEST 2: Verificar OpenSSL\n";
echo "===========================================\n";

if (extension_loaded('openssl')) {
    echo "✅ Extensión OpenSSL está cargada\n";
} else {
    echo "❌ Extensión OpenSSL NO está disponible\n";
    echo "Se requiere OpenSSL para TLS/SSL\n";
    exit(1);
}

echo "\n";

// Test 3: Intentar conexión SMTP con autenticación
echo "===========================================\n";
echo "TEST 3: Conexión SMTP con autenticación\n";
echo "===========================================\n";

try {
    // Crear socket
    $socket = fsockopen($host, $port, $errno, $errstr, 30);
    
    if (!$socket) {
        throw new Exception("No se pudo conectar: {$errstr} ({$errno})");
    }
    
    echo "✅ Conexión establecida\n";
    
    // Leer respuesta inicial
    $response = fgets($socket, 515);
    echo "Servidor: " . trim($response) . "\n";
    
    // EHLO
    fputs($socket, "EHLO localhost\r\n");
    $response = fgets($socket, 515);
    echo "EHLO: " . trim($response) . "\n";
    
    // Leer todas las capacidades
    while ($line = fgets($socket, 515)) {
        if (substr($line, 3, 1) == ' ') {
            break;
        }
    }
    
    // STARTTLS
    fputs($socket, "STARTTLS\r\n");
    $response = fgets($socket, 515);
    echo "STARTTLS: " . trim($response) . "\n";
    
    // Activar TLS
    if (stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
        echo "✅ TLS activado correctamente\n";
    } else {
        throw new Exception("No se pudo activar TLS");
    }
    
    // EHLO después de TLS
    fputs($socket, "EHLO localhost\r\n");
    while ($line = fgets($socket, 515)) {
        if (substr($line, 3, 1) == ' ') {
            break;
        }
    }
    
    // AUTH LOGIN
    fputs($socket, "AUTH LOGIN\r\n");
    $response = fgets($socket, 515);
    echo "AUTH: " . trim($response) . "\n";
    
    // Enviar username
    fputs($socket, base64_encode($username) . "\r\n");
    $response = fgets($socket, 515);
    
    // Enviar password
    fputs($socket, base64_encode($password) . "\r\n");
    $response = fgets($socket, 515);
    
    if (strpos($response, '235') !== false) {
        echo "✅ Autenticación exitosa\n";
    } else {
        echo "❌ Autenticación fallida: " . trim($response) . "\n";
        throw new Exception("Autenticación fallida");
    }
    
    // QUIT
    fputs($socket, "QUIT\r\n");
    fclose($socket);
    
    echo "\n";
    echo "===========================================\n";
    echo "✅ TODAS LAS PRUEBAS PASARON\n";
    echo "===========================================\n";
    echo "La configuración SMTP está correcta y funcional.\n";
    echo "El sistema puede enviar emails sin problemas.\n\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n\n";
    exit(1);
}
