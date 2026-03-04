<?php
/**
 * Script de seed: Crear usuarios de prueba
 * Ejecutar dentro del contenedor Docker:
 *   docker exec -it mvc_app php scripts/create_seed_users.php
 */
require_once __DIR__ . '/../config/Environment.php';
Environment::load(__DIR__ . '/..');
require_once __DIR__ . '/../config/Database.php';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die("Error: No se pudo conectar a la base de datos\n");
}

$users = [
    ['username' => 'Admin',  'email' => 'admin@blog.com',  'password' => 'Admin123!',  'role' => 'admin'],
    ['username' => 'Maria',  'email' => 'maria@blog.com',  'password' => 'Password1!', 'role' => 'writer'],
    ['username' => 'Carlos', 'email' => 'carlos@blog.com', 'password' => 'Password1!', 'role' => 'writer'],
];

$stmt = $db->prepare("INSERT IGNORE INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)");
$count = 0;

foreach ($users as $user) {
    $hash = password_hash($user['password'], PASSWORD_DEFAULT);
    try {
        $stmt->execute([$user['username'], $user['email'], $hash, $user['role']]);
        if ($stmt->rowCount() > 0) {
            echo "✓ Usuario creado: {$user['username']} ({$user['role']})\n";
            $count++;
        } else {
            echo "- Usuario ya existe: {$user['username']}\n";
        }
    } catch (PDOException $e) {
        echo "✗ Error con {$user['username']}: " . $e->getMessage() . "\n";
    }
}

echo "\n=== $count usuarios nuevos creados ===\n";
echo "Credenciales admin: admin@blog.com / Admin123!\n";
echo "Credenciales writer: maria@blog.com / Password1!\n";
