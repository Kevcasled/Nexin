<?php
// Cargar dependencias necesarias
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Comment.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../utils/Auth.php';
require_once __DIR__ . '/../utils/Flash.php';
require_once __DIR__ . '/../utils/Validator.php';
require_once __DIR__ . '/../utils/Csrf.php';
require_once __DIR__ . '/../utils/HttpClient.php';

/** Admin Panel: Gestión total del blog */
class AdminController {
    private $postModel;
    private $userModel;
    private $commentModel;
    private $categoryModel;

    public function __construct() {
        Auth::requireAdmin();
        
        $database = new Database();
        $db = $database->getConnection();
        $this->postModel = new Post($db);
        $this->userModel = new User($db);
        $this->commentModel = new Comment($db);
        $this->categoryModel = new Category($db);
    }

    /** Dashboard con stats */
    public function dashboard() {
        $stats = [
            'posts' => $this->postModel->count(),
            'users' => $this->userModel->count(),
            'comments' => $this->commentModel->count(),
            'categories' => $this->categoryModel->count()
        ];
        
        $this->render('admin/dashboard.php', ['stats' => $stats]);
    }

    // --- Gestión de Posts ---
    
    /** Lista todo (admin) */
    public function managePosts() {
        $posts = $this->postModel->getAll();
        $this->render('admin/posts.php', ['posts' => $posts]);
    }

    /** Cambia estado (pub/borr) y notifica */
    public function changePostStatus($id) {
        Csrf::verify();
        $post = $this->postModel->findById($id);
        
        if (!$post) {
            Flash::error('Post no encontrado');
            header('Location: index.php?action=admin_posts');
            exit();
        }
        
        $newStatus = $post['status'] === 'published' ? 'draft' : 'published';
        
        if ($this->postModel->changeStatus($id, $newStatus)) {
            if ($newStatus === 'published') {
                HttpClient::notifyNewPost($post, $post['username'] ?? 'Admin');
            }
            Flash::success('Estado del post actualizado');
        } else {
            Flash::error('Error al actualizar el estado');
        }
        
        header('Location: index.php?action=admin_posts');
        exit();
    }

    /** Borra post y su imagen */
    public function deletePost($id) {
        Csrf::verify();
        if ($this->postModel->delete($id)) {
            Flash::success('Post eliminado exitosamente');
        } else {
            Flash::error('Error al eliminar el post');
        }
        
        header('Location: index.php?action=admin_posts');
        exit();
    }

    // --- Gestión Usuarios ---
    
    /** Lista usuarios registrados */
    public function manageUsers() {
        $users = $this->userModel->getAll();
        $this->render('admin/users.php', ['users' => $users]);
    }

    /** Borra un usuario */
    public function deleteUser($id) {
        Csrf::verify();
        if ($id == $_SESSION['user_id']) {
            Flash::error('No puedes eliminar tu propia cuenta');
            header('Location: index.php?action=admin_users');
            exit();
        }
        
        if ($this->userModel->delete($id)) {
            Flash::success('Usuario eliminado exitosamente');
        } else {
            Flash::error('Error al eliminar el usuario');
        }
        
        header('Location: index.php?action=admin_users');
        exit();
    }

    /** Cambia rol usuario (admin/writer) */
    public function changeUserRole($id) {
        Csrf::verify();
        $user = $this->userModel->findById($id);
        
        if (!$user) {
            Flash::error('Usuario no encontrado');
            header('Location: index.php?action=admin_users');
            exit();
        }
        
        if ($id == $_SESSION['user_id']) {
            Flash::error('No puedes cambiar tu propio rol');
            header('Location: index.php?action=admin_users');
            exit();
        }
        
        $newRole = $user['role'] === 'admin' ? 'writer' : 'admin';
        
        if ($this->userModel->updateRole($id, $newRole)) {
            Flash::success('Rol actualizado exitosamente');
        } else {
            Flash::error('Error al actualizar el rol');
        }
        
        header('Location: index.php?action=admin_users');
        exit();
    }

    // --- Gestión Comentarios ---
    
    /** Lista todos los comentarios */
    public function manageComments() {
        $comments = $this->commentModel->getAll();
        $this->render('admin/comments.php', ['comments' => $comments]);
    }

    /** Borra comentario */
    public function deleteComment($id) {
        Csrf::verify();
        if ($this->commentModel->delete($id)) {
            Flash::success('Comentario eliminado exitosamente');
        } else {
            Flash::error('Error al eliminar el comentario');
        }
        
        header('Location: index.php?action=admin_comments');
        exit();
    }

    // --- Gestión Categorías ---
    
    /** Lista categorías */
    public function manageCategories() {
        $categories = $this->categoryModel->getAll();
        $this->render('admin/categories.php', ['categories' => $categories]);
    }

    /** Crea categoría */
    public function createCategory() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=admin_categories');
            exit();
        }
        
        Csrf::verify();
        
        $validator = new Validator();
        $name = $_POST['name'] ?? '';
        $slug = $this->generateSlug($name);
        
        $validator->required('name', $name, 'El nombre');
        
        if (!$validator->hasErrors()) {
            if ($this->categoryModel->insert($name, $slug)) {
                Flash::success('Categoría creada exitosamente');
            } else {
                Flash::error('Error al crear la categoría');
            }
        } else {
            Flash::error($validator->getFirstError());
        }
        
        header('Location: index.php?action=admin_categories');
        exit();
    }

    /** Borra categoría */
    public function deleteCategory($id) {
        Csrf::verify();
        if ($this->categoryModel->delete($id)) {
            Flash::success('Categoría eliminada exitosamente');
        } else {
            Flash::error('Error al eliminar la categoría');
        }
        
        header('Location: index.php?action=admin_categories');
        exit();
    }

    /** Genera slug URL-friendly */
    private function generateSlug($text) {
        $text = strtolower($text);
        $text = str_replace(' ', '-', $text);
        $text = preg_replace('/[^a-z0-9\-]/', '', $text);
        return $text;
    }

    /** Renderiza vista */
    protected function render($view, $data = []) {
        extract($data);
        include __DIR__ . '/../views/' . $view;
    }
}
