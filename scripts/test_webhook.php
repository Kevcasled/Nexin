<?php
require_once __DIR__ . '/../config/Environment.php';
Environment::load(__DIR__ . '/..');
require_once __DIR__ . '/../utils/HttpClient.php';

// Test webhook - usa la URL de PRODUCCIÓN de n8n
$testUrl = 'http://n8n:5678/webhook/nuevo-post';

$data = [
    'event'        => 'post_published',
    'post_id'      => 1,
    'title'        => 'Test desde el Blog MVC',
    'author'       => 'Admin',
    'excerpt'      => 'Este es un post de prueba para verificar que el webhook de n8n funciona correctamente con Telegram.',
    'post_url'     => 'http://localhost:8080/?action=show_post&id=1',
    'published_at' => date('Y-m-d H:i:s'),
];

echo "Enviando webhook de prueba a: $testUrl\n";
$result = HttpClient::sendWebhook($testUrl, $data);
echo "Success: " . ($result['success'] ? 'YES' : 'NO') . "\n";
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Response: " . $result['response'] . "\n";
