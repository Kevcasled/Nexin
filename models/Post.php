<?php
/** Modelo de posts */
class Post {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /** Obtiene todos (opcional por estado) */
    public function getAll($status = null) {
        $sql = "SELECT p.*, u.username, c.name as category_name 
                FROM posts p
                LEFT JOIN users u ON p.user_id = u.id
                LEFT JOIN categories c ON p.category_id = c.id";
        
        if ($status) {
            $sql .= " WHERE p.status = ? ORDER BY p.created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$status]);
        } else {
            $sql .= " ORDER BY p.created_at DESC";
            $stmt = $this->db->query($sql);
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Solo publicados */
    public function getPublished() {
        return $this->getAll('published');
    }

    /** Busca por ID con autor y categoría */
    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT p.*, u.username, c.name as category_name 
            FROM posts p
            LEFT JOIN users u ON p.user_id = u.id
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /** Posts de un autor */
    public function getByUserId($userId) {
        $stmt = $this->db->prepare("
            SELECT p.*, u.username, c.name as category_name 
            FROM posts p
            LEFT JOIN users u ON p.user_id = u.id
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.user_id = ?
            ORDER BY p.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Inserta post nuevo */
    public function insert($userId, $categoryId, $title, $content, $imagePath, $status = 'draft') {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO posts (user_id, category_id, title, content, image_path, status) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            return $stmt->execute([$userId, $categoryId, $title, $content, $imagePath, $status]);
        } catch(PDOException $e) {
            error_log("Error inserting post: " . $e->getMessage());
            return false;
        }
    }

    /** Actualiza post existente */
    public function update($id, $categoryId, $title, $content, $imagePath = null, $status = null) {
        try {
            $fields = ['category_id = ?', 'title = ?', 'content = ?'];
            $params = [$categoryId, $title, $content];

            if ($imagePath !== null) {
                $fields[] = 'image_path = ?';
                $params[] = $imagePath;
            }

            if ($status !== null) {
                $fields[] = 'status = ?';
                $params[] = $status;
            }

            $params[] = $id;
            $sql = 'UPDATE posts SET ' . implode(', ', $fields) . ' WHERE id = ?';
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch(PDOException $e) {
            error_log("Error updating post: " . $e->getMessage());
            return false;
        }
    }

    /** Borra post e imagen */
    public function delete($id) {
        try {
            $post = $this->findById($id);
            $stmt = $this->db->prepare("DELETE FROM posts WHERE id = ?");
            $result = $stmt->execute([$id]);
            if ($result && $post && !empty($post['image_path'])) {
                $imagePath = __DIR__ . '/../' . $post['image_path'];
                if (file_exists($imagePath)) unlink($imagePath);
            }
            return $result;
        } catch(PDOException $e) {
            error_log("Error deleting post: " . $e->getMessage());
            return false;
        }
    }

    /** Cambia estado post */
    public function changeStatus($id, $status) {
        try {
            $stmt = $this->db->prepare("UPDATE posts SET status = ? WHERE id = ?");
            return $stmt->execute([$status, $id]);
        } catch(PDOException $e) {
            error_log("Error changing post status: " . $e->getMessage());
            return false;
        }
    }

    /** ID de última inserción */
    public function getLastInsertId() {
        return $this->db->lastInsertId();
    }

    /** Total de posts */
    public function count() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM posts");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch(PDOException $e) {
            error_log("Error counting posts: " . $e->getMessage());
            return 0;
        }
    }
}