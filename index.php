<?php
/** Front Controller - Punto de entrada único */

// Cargar entorno
require_once 'config/Environment.php';
Environment::load(__DIR__);

// Configuración de errores
if (Environment::isProduction()) {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/logs/error.log');
} else {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// Sesión segura
session_start([
    'cookie_httponly' => true,
    'cookie_secure'   => isset($_SERVER['HTTPS']),
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true
]);

require_once 'controllers/PostController.php';
require_once 'controllers/UserController.php';
require_once 'controllers/CommentController.php';
require_once 'controllers/AdminController.php';
require_once 'controllers/RagController.php';
require_once 'utils/Csrf.php';

$action = $_GET['action'] ?? 'posts';
$id = $_GET['id'] ?? null;

// Router
switch ($action) {
    // Posts
    case 'posts':
        $controller = new PostController();
        $controller->index();
        break;
    
    case 'show_post':
        if ($id) {
            $controller = new PostController();
            $controller->show($id);
        }
        break;
    
    case 'create_post':
        $controller = new PostController();
        $controller->create();
        break;
    
    case 'store_post':
        $controller = new PostController();
        $controller->store();
        break;
    
    case 'edit_post':
        if ($id) {
            $controller = new PostController();
            $controller->edit($id);
        }
        break;
    
    case 'update_post':
        if ($id) {
            $controller = new PostController();
            $controller->update($id);
        }
        break;
    
    case 'delete_post':
        if ($id) {
            $controller = new PostController();
            $controller->delete($id);
        }
        break;

    // Users
    case 'register':
        $controller = new UserController();
        $controller->register();
        break;

    case 'login':
        $controller = new UserController();
        $controller->login();
        break;

    case 'logout':
        $controller = new UserController();
        $controller->logout();
        break;
    
    // Comments
    case 'store_comment':
        $controller = new CommentController();
        $controller->store();
        break;
    
    case 'delete_comment':
        if ($id) {
            $controller = new CommentController();
            $controller->delete($id);
        }
        break;
    
    // Admin
    case 'admin_dashboard':
        $controller = new AdminController();
        $controller->dashboard();
        break;
    
    case 'admin_posts':
        $controller = new AdminController();
        $controller->managePosts();
        break;
    
    case 'admin_change_post_status':
        if ($id) {
            $controller = new AdminController();
            $controller->changePostStatus($id);
        }
        break;
    
    // Eliminar post desde admin
    case 'admin_delete_post':
        if ($id) {
            $controller = new AdminController();
            $controller->deletePost($id);
        }
        break;
    
    // Gestionar usuarios (admin)
    case 'admin_users':
        $controller = new AdminController();
        $controller->manageUsers();
        break;
    
    // Cambiar rol de usuario (writer/admin)
    case 'admin_change_user_role':
        if ($id) {
            $controller = new AdminController();
            $controller->changeUserRole($id);
        }
        break;
    
    case 'admin_delete_user':
        if ($id) {
            $controller = new AdminController();
            $controller->deleteUser($id);
        }
        break;
    
    case 'admin_comments':
        $controller = new AdminController();
        $controller->manageComments();
        break;
    
    case 'admin_delete_comment':
        if ($id) {
            $controller = new AdminController();
            $controller->deleteComment($id);
        }
        break;
    
    case 'admin_categories':
        $controller = new AdminController();
        $controller->manageCategories();
        break;
    
    case 'admin_create_category':
        $controller = new AdminController();
        $controller->createCategory();
        break;
    
    case 'admin_delete_category':
        if ($id) {
            $controller = new AdminController();
            $controller->deleteCategory($id);
        }
        break;

    // --- RAG ---
    case 'rag_ask':
        $controller = new RagController();
        $controller->ask();
        break;

    case 'rag_answer':
        $controller = new RagController();
        $controller->answer();
        break;
        
    default:
        $controller = new PostController();
        $controller->index();
        break;
}