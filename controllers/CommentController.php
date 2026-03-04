<?php
// Carga dependencias
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Comment.php';
require_once __DIR__ . '/../utils/Auth.php';
require_once __DIR__ . '/../utils/Validator.php';
require_once __DIR__ . '/../utils/Flash.php';
require_once __DIR__ . '/../utils/Csrf.php';

/** Gestiona creación y borrado de comentarios */
class CommentController {
    private $commentModel;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->commentModel = new Comment($db);
    }

    /** Guarda comentario (POST) */
    public function store() {
        Auth::requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=posts');
            exit();
        }
        
        Csrf::verify();
        
        $validator = new Validator();
        $postId = $_POST['post_id'] ?? null;
        $text = $_POST['text'] ?? '';
        
        $validator->required('text', $text, 'El comentario');
        $validator->minLength('text', $text, 5, 'El comentario');
        
        if (!$validator->hasErrors() && $postId) {
            $result = $this->commentModel->insert($postId, $_SESSION['user_id'], $text);
            
            if ($result) {
                Flash::success('Comentario publicado exitosamente');
            } else {
                Flash::error('Error al publicar el comentario');
            }
        } else {
            Flash::error($validator->getFirstError() ?? 'Datos inválidos');
        }
        
        header('Location: index.php?action=show_post&id=' . $postId);
        exit();
    }

    /** Elimina comentario por ID */
    public function delete($id) {
        Auth::requireLogin();
        Csrf::verify();
        
        $comment = $this->commentModel->findById($id);
        
        if (!$comment) {
            Flash::error('Comentario no encontrado');
            header('Location: index.php?action=posts');
            exit();
        }
        
        if (!Auth::canModify($comment['user_id'])) {
            Flash::error('No tienes permisos para eliminar este comentario');
            header('Location: index.php?action=show_post&id=' . $comment['post_id']);
            exit();
        }
        
        if ($this->commentModel->delete($id)) {
            Flash::success('Comentario eliminado exitosamente');
        } else {
            Flash::error('Error al eliminar el comentario');
        }
        
        header('Location: index.php?action=show_post&id=' . $comment['post_id']);
        exit();
    }
}
