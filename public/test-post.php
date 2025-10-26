<?php
// Test simple para verificar que POST funciona
file_put_contents(__DIR__ . '/logs/test-post.txt', "POST recibido: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
file_put_contents(__DIR__ . '/logs/test-post.txt', "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n", FILE_APPEND);
file_put_contents(__DIR__ . '/logs/test-post.txt', "REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n", FILE_APPEND);
file_put_contents(__DIR__ . '/logs/test-post.txt', "POST data: " . print_r($_POST, true) . "\n", FILE_APPEND);

echo "Test POST ejecutado. Revisa public/logs/test-post.txt";
