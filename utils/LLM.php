<?php
require_once __DIR__ . '/../config/Environment.php';
require_once __DIR__ . '/HttpClient.php';

/** Conecta con Ollama para respuestas inteligentes (RAG) */
class LLM {

    /** Genera respuesta con contexto usando Ollama */
    public static function generate($query, $context = '') {
        $ollamaUrl = Environment::get('OLLAMA_URL', 'http://host.docker.internal:11434');
        $model = Environment::get('OLLAMA_MODEL', 'llama3.2:3b');

        $prompt = self::buildPrompt($query, $context);

        $data = [
            'model'  => $model,
            'prompt' => $prompt,
            'stream' => false,
            'options' => [
                'temperature' => 0.7,
                'num_predict' => 500,
            ]
        ];

        $response = HttpClient::post($ollamaUrl . '/api/generate', $data, 120);

        if ($response === false) {
            return self::fallbackResponse($query, $context);
        }

        $result = json_decode($response, true);

        if (isset($result['response'])) {
            return trim($result['response']);
        }

        return self::fallbackResponse($query, $context);
    }

    /** Construye el prompt con contexto */
    private static function buildPrompt($query, $context) {
        return "Eres un asistente del blog MVC Blog. Tu tarea es responder preguntas basándote ÚNICAMENTE en el contenido de los artículos del blog que se te proporcionan como contexto.

REGLAS:
- Responde siempre en español
- Basa tu respuesta solo en el contexto proporcionado
- Si no hay información suficiente, indícalo claramente
- Sé conciso y útil
- Menciona los títulos de los artículos relevantes cuando sea posible

CONTEXTO DEL BLOG:
$context

PREGUNTA DEL USUARIO:
$query

RESPUESTA:";
    }

    /** Respuesta si Ollama falla */
    private static function fallbackResponse($query, $context) {
        if (strpos($context, 'No se encontraron') !== false) {
            return "No se encontraron artículos relevantes en el blog para tu consulta: \"$query\". Intenta usar otras palabras clave.";
        }
        
        return "⚠️ El servicio de IA (Ollama) no está disponible en este momento. Sin embargo, se encontraron artículos relacionados con tu búsqueda. Puedes consultarlos en la lista de abajo.\n\nPara activar las respuestas inteligentes, asegúrate de que Ollama está ejecutándose en tu sistema.";
    }

    /** Verifica disponibilidad de Ollama */
    public static function isAvailable() {
        $ollamaUrl = Environment::get('OLLAMA_URL', 'http://host.docker.internal:11434');
        
        $ch = curl_init($ollamaUrl . '/api/tags');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 3,
            CURLOPT_CONNECTTIMEOUT => 2,
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $httpCode === 200;
    }
}
