<?php
require_once __DIR__ . '/../config/Environment.php';

/** Cliente HTTP para servicios externos (n8n, Ollama) */
class HttpClient {

    /** Envía petición POST JSON a webhook */
    public static function sendWebhook($url, $data, $timeout = 10) {
        if (empty($url)) {
            error_log("HttpClient: URL vacía");
            return ['success' => false, 'response' => 'URL vacía', 'http_code' => 0];
        }

        $jsonData = json_encode($data, JSON_UNESCAPED_UNICODE);
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $jsonData,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Content-Length: ' . strlen($jsonData)],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            error_log("HttpClient error: $error");
            return ['success' => false, 'response' => $error, 'http_code' => 0];
        }

        return ['success' => ($httpCode >= 200 && $httpCode < 300), 'response' => $response, 'http_code' => $httpCode];
    }

    /** Notifica nuevo post a n8n */
    public static function notifyNewPost($post, $authorName) {
        $webhookUrl = Environment::get('N8N_WEBHOOK_URL', '');
        if (empty($webhookUrl)) return;

        $data = [
            'event'        => 'post_published',
            'post_id'      => $post['id'] ?? null,
            'title'        => $post['title'] ?? '',
            'author'       => $authorName,
            'category'     => $post['category_name'] ?? 'Sin categoría',
            'content'      => strip_tags($post['content'] ?? ''),
            'excerpt'      => mb_substr(strip_tags($post['content'] ?? ''), 0, 200) . '...',
            'image_url'    => Environment::get('APP_URL', 'http://localhost:8080') . '/' . ($post['image_path'] ?? ''),
            'post_url'     => Environment::get('APP_URL', 'http://localhost:8080') . '/index.php?action=show_post&id=' . ($post['id'] ?? ''),
            'published_at' => date('Y-m-d H:i:s'),
        ];
        self::sendWebhook($webhookUrl, $data);
    }

    /** POST genérico (Ollama, etc) */
    public static function post($url, $data, $timeout = 60) {
        $jsonData = json_encode($data, JSON_UNESCAPED_UNICODE);
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $jsonData,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Content-Length: ' . strlen($jsonData)],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            error_log("HttpClient POST error: $error");
            return false;
        }
        return $response;
    }
}
