<?php
require_once __DIR__ . '/../config/Environment.php';
Environment::load(__DIR__ . '/..');
require_once __DIR__ . '/../config/Database.php';

$db = (new Database())->getConnection();
$newHash = password_hash('Admin123!', PASSWORD_DEFAULT);
$stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
$stmt->execute([$newHash, 'admin@blog.com']);
echo "Admin password updated to Admin123!\n";
echo "Hash: $newHash\n";

// Also update kevin if exists
$stmt->execute([$newHash, 'kevincasled@gmail.com']);
echo "Kevin password also set to Admin123!\n";
