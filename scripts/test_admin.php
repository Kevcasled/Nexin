<?php
require_once __DIR__ . '/../config/Environment.php';
Environment::load(__DIR__ . '/..');
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/User.php';

$db = (new Database())->getConnection();
if (!$db) { die("DB connection failed\n"); }

$user = new User($db);
$found = $user->findByEmail('admin@blog.com');

if ($found) {
    echo "User: " . $found['username'] . "\n";
    echo "Role: " . $found['role'] . "\n";
    echo "Password OK: " . ($user->verifyPassword('Admin123!', $found['password_hash']) ? 'YES' : 'NO') . "\n";
} else {
    echo "USER NOT FOUND\n";
}

// Check if there's a render method issue
echo "\n--- Testing AdminController load ---\n";
require_once __DIR__ . '/../utils/Auth.php';
require_once __DIR__ . '/../utils/Flash.php';
require_once __DIR__ . '/../utils/Csrf.php';
require_once __DIR__ . '/../utils/HttpClient.php';
require_once __DIR__ . '/../utils/Validator.php';
require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../models/Comment.php';
require_once __DIR__ . '/../models/Category.php';

// Check if render method exists
$rc = new ReflectionClass('AdminController');
echo "Methods: ";
foreach ($rc->getMethods() as $m) {
    echo $m->getName() . ", ";
}
echo "\n";

// Check if render method exists
if ($rc->hasMethod('render')) {
    echo "render() method EXISTS\n";
} else {
    echo "render() method MISSING!\n";
}
