<?php
// Cargar dependencias necesarias
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Comment.php';
require_once __DIR__ . '/../utils/Auth.php';
require_once __DIR__ . '/../utils/Validator.php';
require_once __DIR__ . '/../utils/Flash.php';
require_once __DIR__ . '/../utils/Csrf.php';
require_once __DIR__ . '/../utils/HttpClient.php';

/** CRUD de publicaciones */
class PostController {
    private $db;
    private $postModel;
    private $categoryModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->postModel = new Post($this->db);
        $this->categoryModel = new Category($this->db);
    }

    /** Lista posts públicos */
    public function index() {
        $posts = $this->postModel->getPublished();
        $categories = $this->categoryModel->getAll();
        return $this->render('posts/index.php', ['posts' => $posts, 'categories' => $categories]);
    }

    /** Detalle de un post */
    public function show($id) {
        $post = $this->postModel->findById($id);

        if (!$post) {
            Flash::error('Post no encontrado');
            header('Location: index.php?action=posts');
            exit();
        }

        if ($post['status'] !== 'published' && !Auth::canModify($post['user_id'])) {
            Flash::error('No tienes permisos para ver este post');
            header('Location: index.php?action=posts');
            exit();
        }

        $commentModel = new Comment($this->db);
        $comments = $commentModel->getByPostId($post['id']);

        return $this->render('posts/show.php', ['post' => $post, 'comments' => $comments]);
    }

    /** Formulario nuevo post (Login req) */
    public function create() {
        Auth::requireLogin();
        $categories = $this->categoryModel->getAll();
        return $this->render('posts/create.php', ['categories' => $categories]);
    }

    /** Guarda post y sube imagen */
    public function store() {
        Auth::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=posts');
            exit();
        }

        Csrf::verify();

        $validator = new Validator();
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $categoryId = $_POST['category_id'] ?? null;
        $status = $_POST['status'] ?? 'draft';
        if (!in_array($status, ['draft', 'published'])) {
            $status = 'draft';
        }

        $validator->required('title', $title, 'El título');
        $validator->maxLength('title', $title, 150, 'El título');
        $validator->required('content', $content, 'El contenido');
        $validator->image('image', $_FILES['image']);

        // Si no hay errores de validación
        if (!$validator->hasErrors()) {
            // Subir la imagen al servidor
            $imagePath = $this->uploadImage($_FILES['image']);

            if ($imagePath) {
                // Guardar post en la base de datos
                $result = $this->postModel->insert($_SESSION['user_id'], $categoryId, $title, $content, $imagePath, $status);

                if ($result) {
                    // Si el post se publica directamente, notificar via webhook
                    if ($status === 'published') {
                        $category = $categoryId ? $this->categoryModel->findById($categoryId) : null;
                        $newPost = [
                            'id'            => $this->postModel->getLastInsertId(),
                            'title'         => $title,
                            'content'       => $content,
                            'category_name' => $category['name'] ?? 'Sin categoría',
                            'image_path'    => $imagePath,
                        ];
                        HttpClient::notifyNewPost($newPost, $_SESSION['username']);
                    }

                    Flash::success('Post creado exitosamente');
                    header('Location: index.php?action=posts');
                    exit();
                } else {
                    Flash::error('Error al crear el post');
                }
            } else {
                Flash::error('Error al subir la imagen');
            }
        }

        $categories = $this->categoryModel->getAll();
        $this->render('posts/create.php', ['categories' => $categories, 'errors' => $validator->getErrors(), 'old' => $_POST]);
    }

    /** Formulario edición */
    public function edit($id) {
        Auth::requireLogin();
        $post = $this->postModel->findById($id);

        if (!$post) {
            Flash::error('Post no encontrado');
            header('Location: index.php?action=posts');
            exit();
        }

        if (!Auth::canModify($post['user_id'])) {
            Flash::error('No tienes permisos para editar este post');
            header('Location: index.php?action=posts');
            exit();
        }

        $categories = $this->categoryModel->getAll();
        return $this->render('posts/edit.php', ['post' => $post, 'categories' => $categories]);
    }

    /** Actualiza post e imagen */
    public function update($id) {
        Auth::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=posts');
            exit();
        }

        Csrf::verify();

        $post = $this->postModel->findById($id);

        if (!$post || !Auth::canModify($post['user_id'])) {
            Flash::error('No tienes permisos para editar este post');
            header('Location: index.php?action=posts');
            exit();
        }

        $validator = new Validator();
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $categoryId = $_POST['category_id'] ?? null;
        $status = $_POST['status'] ?? $post['status'];
        if (!in_array($status, ['draft', 'published'])) {
            $status = $post['status'];
        }

        $validator->required('title', $title, 'El título');
        $validator->maxLength('title', $title, 150, 'El título');
        $validator->required('content', $content, 'El contenido');

        if (isset($_FILES['image']) && $_FILES['image']['tmp_name']) {
            $validator->image('image', $_FILES['image']);
        }

        if (!$validator->hasErrors()) {
            $imagePath = null;

            if (isset($_FILES['image']) && $_FILES['image']['tmp_name']) {
                $imagePath = $this->uploadImage($_FILES['image']);
                if ($imagePath && !empty($post['image_path'])) {
                    $oldImagePath = __DIR__ . '/../' . $post['image_path'];
                    if (file_exists($oldImagePath)) unlink($oldImagePath);
                }
            }

            $result = $this->postModel->update($id, $categoryId, $title, $content, $imagePath, $status);

            if ($result) {
                Flash::success('Post actualizado exitosamente');
                header('Location: index.php?action=show_post&id=' . $id);
                exit();
            } else {
                Flash::error('Error al actualizar el post');
            }
        }

        $categories = $this->categoryModel->getAll();
        $this->render('posts/edit.php', ['post' => $post, 'categories' => $categories, 'errors' => $validator->getErrors(), 'old' => $_POST]);
    }

    /** Borra post e imagen */
    public function delete($id) {
        Auth::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=posts');
            exit();
        }

        Csrf::verify();
        $post = $this->postModel->findById($id);

        if (!$post || !Auth::canModify($post['user_id'])) {
            Flash::error('No tienes permisos para eliminar este post');
            header('Location: index.php?action=posts');
            exit();
        }

        if ($this->postModel->delete($id)) {
            Flash::success('Post eliminado exitosamente');
        } else {
            Flash::error('Error al eliminar the post');
        }

        header('Location: index.php?action=posts');
        exit();
    }

    /** Sube imagen a uploads/ con extensión forzada por MIME type */
    private function uploadImage($file) {
        $uploadDir = __DIR__ . '/../uploads/';
        $imageInfo = @getimagesize($file['tmp_name']);
        $ext = $this->mimeToExtension($imageInfo['mime'] ?? '');
        $fileName = uniqid() . '.' . $ext;
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return 'uploads/' . $fileName;
        }

        return false;
    }

    /** Mapea MIME type a extensión de archivo segura */
    private function mimeToExtension($mime) {
        $map = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'image/webp' => 'webp',
        ];
        return $map[$mime] ?? 'jpg';
    }

    /** Renderiza vista */
    protected function render($view, $data = []) {
        extract($data);
        include __DIR__ . '/../views/' . $view;
    }
}
