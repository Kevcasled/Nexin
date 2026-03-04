<?php
/**
 * Script de seed: Crear posts de prueba
 * Ejecutar dentro del contenedor Docker:
 *   docker exec -it mvc_app php scripts/create_seed_posts.php
 * Nota: Ejecutar DESPUÉS de create_seed_users.php
 */
require_once __DIR__ . '/../config/Environment.php';
Environment::load(__DIR__ . '/..');
require_once __DIR__ . '/../config/Database.php';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die("Error: No se pudo conectar a la base de datos\n");
}

// Obtener IDs de usuarios y categorías existentes
$users = $db->query("SELECT id FROM users")->fetchAll(PDO::FETCH_COLUMN);
$categories = $db->query("SELECT id FROM categories")->fetchAll(PDO::FETCH_COLUMN);

if (empty($users)) {
    die("Error: No hay usuarios. Ejecuta primero create_seed_users.php\n");
}

// Posts de ejemplo - Reseñas de series y películas
$posts = [
    ['title' => 'Breaking Bad: La Obra Maestra de la Televisión', 'content' => 'Breaking Bad sigue siendo una de las mejores series jamás creadas. La transformación de Walter White de profesor de química a señor de la droga es un estudio magistral del personaje. Bryan Cranston entrega una actuación legendaria, acompañado por un Aaron Paul que da vida a Jesse Pinkman de forma inolvidable. La fotografía del desierto de Nuevo México y la música de Dave Porter crean una atmósfera única. Cada temporada eleva la apuesta narrativa hasta un final perfecto. Puntuación: 10/10.', 'status' => 'published'],
    ['title' => 'Interstellar: Viaje al Infinito de Christopher Nolan', 'content' => 'Interstellar es una epopeya espacial que combina ciencia real con emoción pura. Matthew McConaughey interpreta a Cooper, un astronauta que debe dejar a su familia para salvar a la humanidad. La representación del agujero negro Gargantúa, asesorada por el físico Kip Thorne, es visualmente impactante. La escena de la ola gigante en el planeta de Miller y la secuencia del tesseract son momentos cinematográficos icónicos. Hans Zimmer compone una de sus mejores bandas sonoras. Puntuación: 9.5/10.', 'status' => 'published'],
    ['title' => 'Dark: La Joya Oculta de Netflix', 'content' => 'Dark es la serie alemana que redefinió los viajes en el tiempo en televisión. Su narrativa compleja conecta cuatro líneas temporales con una precisión matemática. Los personajes de Winden están entrelazados en un árbol genealógico que requiere un diagrama para seguir. La fotografía oscura y lluviosa crea una atmósfera opresiva perfecta. Jonas Kahnwald (Louis Hofmann) lleva el peso de la serie sobre sus hombros. El final de la tercera temporada cierra todos los hilos narrativos de forma satisfactoria. Puntuación: 9.5/10.', 'status' => 'published'],
];

$stmt = $db->prepare("INSERT INTO posts (user_id, category_id, title, content, image_path, status) VALUES (?, ?, ?, ?, ?, ?)");
$count = 0;

foreach ($posts as $post) {
    $userId = $users[array_rand($users)];
    $categoryId = !empty($categories) ? $categories[array_rand($categories)] : null;
    // Usar imagen placeholder
    $imagePath = 'uploads/placeholder_' . ($count + 1) . '.jpg';
    
    try {
        $stmt->execute([$userId, $categoryId, $post['title'], $post['content'], $imagePath, $post['status']]);
        echo "✓ Post creado: {$post['title']} [{$post['status']}]\n";
        $count++;
    } catch (PDOException $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
}

echo "\n=== $count posts creados ===\n";
