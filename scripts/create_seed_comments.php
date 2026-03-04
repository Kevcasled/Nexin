<?php
/**
 * Script de seed: Crear comentarios de prueba
 * Ejecutar dentro del contenedor Docker:
 *   docker exec -it mvc_app php scripts/create_seed_comments.php
 * Nota: Ejecutar DESPUÉS de create_seed_users.php y create_seed_posts.php
 */
require_once __DIR__ . '/../config/Environment.php';
Environment::load(__DIR__ . '/..');
require_once __DIR__ . '/../config/Database.php';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die("Error: No se pudo conectar a la base de datos\n");
}

// Obtener IDs de usuarios y posts publicados
$users = $db->query("SELECT id FROM users")->fetchAll(PDO::FETCH_COLUMN);
$posts = $db->query("SELECT id FROM posts WHERE status = 'published'")->fetchAll(PDO::FETCH_COLUMN);

if (empty($users) || empty($posts)) {
    die("Error: No hay usuarios o posts. Ejecuta primero los otros seeds.\n");
}

// Comentarios de ejemplo
$commentTexts = [
    'Totalmente de acuerdo con la puntuación. Una obra maestra.',
    '¡Gran reseña! Me han entrado ganas de volver a verla.',
    'Interesante análisis. ¿Qué opinas de la banda sonora?',
    'La mejor serie/película que he visto en años.',
    'Buen artículo, aunque yo le daría medio punto más.',
];

$count = 0;
$stmt = $db->prepare("INSERT INTO comments (post_id, user_id, text) VALUES (?, ?, ?)");

// Crear entre 2-5 comentarios por post
foreach ($posts as $postId) {
    $numComments = rand(2, 5);
    for ($i = 0; $i < $numComments; $i++) {
        $userId = $users[array_rand($users)];
        $text = $commentTexts[array_rand($commentTexts)];
        
        try {
            $stmt->execute([$postId, $userId, $text]);
            $count++;
        } catch (PDOException $e) {
            echo "✗ Error: " . $e->getMessage() . "\n";
        }
    }
}

echo "=== $count comentarios creados en " . count($posts) . " posts ===\n";
