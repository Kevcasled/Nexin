<?php
/** Modelo de comentarios */
class Comment {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /** Lista todos los comentarios */
    public function getAll() {
        try {
            $stmt = $this->db->query("
                SELECT c.*, u.username, p.title as post_title 
                FROM comments c
                LEFT JOIN users u ON c.user_id = u.id
                LEFT JOIN posts p ON c.post_id = p.id
                ORDER BY c.created_at DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting comments: " . $e->getMessage());
            return [];
        }
    }

    /** Comentarios por ID de post */
    public function getByPostId($postId) {
        try {
            $stmt = $this->db->prepare("
                SELECT c.*, u.username 
                FROM comments c
                LEFT JOIN users u ON c.user_id = u.id
                WHERE c.post_id = ?
                ORDER BY c.created_at DESC
            ");
            $stmt->execute([$postId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting comments by post: " . $e->getMessage());
            return [];
        }
    }

    /** Busca comentario por ID */
    public function findById($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT c.*, u.username, p.title as post_title 
                FROM comments c
                LEFT JOIN users u ON c.user_id = u.id
                LEFT JOIN posts p ON c.post_id = p.id
                WHERE c.id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error finding comment: " . $e->getMessage());
            return false;
        }
    }

    /** Inserta comentario nuevo */
    public function insert($postId, $userId, $text) {
        try {
            $stmt = $this->db->prepare("INSERT INTO comments (post_id, user_id, text) VALUES (?, ?, ?)");
            return $stmt->execute([$postId, $userId, $text]);
        } catch(PDOException $e) {
            error_log("Error inserting comment: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza el texto de un comentario
     * @param int $id ID del comentario
     * @param string $text Nuevo texto
     * @return bool True si se actualizó, false si falló
     */
    public function update($id, $text) {
        try {
            $stmt = $this->db->prepare("UPDATE comments SET text = ? WHERE id = ?");
            return $stmt->execute([$text, $id]);
        } catch(PDOException $e) {
            error_log("Error updating comment: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina un comentario
     * @param int $id ID del comentario
     * @return bool True si se eliminó, false si falló
     */
    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM comments WHERE id = ?");
            return $stmt->execute([$id]);
        } catch(PDOException $e) {
            error_log("Error deleting comment: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cuenta el total de comentarios
     * @return int Número total de comentarios
     */
    public function count() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM comments");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch(PDOException $e) {
            error_log("Error counting comments: " . $e->getMessage());
            return 0;
        }
    }
}