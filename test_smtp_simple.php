<?php
/**
 * Test SMTP simple con el nuevo password
 */

require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

echo "===========================================\n";
echo "TEST SMTP CON NUEVO PASSWORD\n";
echo "===========================================\n\n";

$mail = new PHPMailer(true);

try {
    // Configuración SMTP
    $mail->SMTPDebug = 2; // Mostrar debug completo
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'aespinooza@oopart.cl';
    $mail->Password = 'rhxwzfsewubjkpby'; // Sin espacios
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    
    // Remitente y destinatario
    $mail->setFrom('aespinooza@oopart.cl', 'ChileChocados');
    $mail->addAddress('feasypuppy@gmail.com', 'Test');
    
    // Contenido
    $mail->isHTML(true);
    $mail->Subject = 'Test de Email - ChileChocados';
    $mail->Body = '<h1>Test exitoso</h1><p>Este es un email de prueba.</p>';
    
    // Enviar
    $mail->send();
    
    echo "\n\n✅ EMAIL ENVIADO EXITOSAMENTE!\n";
    
} catch (Exception $e) {
    echo "\n\n❌ ERROR: {$mail->ErrorInfo}\n";
    echo "Excepción: {$e->getMessage()}\n";
}
