<?php
/** Modelo de categorías */
class Category {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /** Lista alfabetica */
    public function getAll() {
        try {
            $stmt = $this->db->query("SELECT * FROM categories ORDER BY name ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting categories: " . $e->getMessage());
            return [];
        }
    }

    /** Busca por ID */
    public function findById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error finding category: " . $e->getMessage());
            return false;
        }
    }

    /** Busca por slug */
    public function findBySlug($slug) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM categories WHERE slug = ?");
            $stmt->execute([$slug]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error finding category by slug: " . $e->getMessage());
            return false;
        }
    }

    /** Inserta categoría */
    public function insert($name, $slug) {
        try {
            $stmt = $this->db->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
            return $stmt->execute([$name, $slug]);
        } catch(PDOException $e) {
            error_log("Error inserting category: " . $e->getMessage());
            return false;
        }
    }

    /** Actualiza categoría */
    public function update($id, $name, $slug) {
        try {
            $stmt = $this->db->prepare("UPDATE categories SET name = ?, slug = ? WHERE id = ?");
            return $stmt->execute([$name, $slug, $id]);
        } catch(PDOException $e) {
            error_log("Error updating category: " . $e->getMessage());
            return false;
        }
    }

    /** Borra categoría */
    public function delete($id) {
        try {
            $stmt = $this->db->prepare("UPDATE posts SET category_id = NULL WHERE category_id = ?");
            $stmt->execute([$id]);
            $stmt = $this->db->prepare("DELETE FROM categories WHERE id = ?");
            return $stmt->execute([$id]);
        } catch(PDOException $e) {
            error_log("Error deleting category: " . $e->getMessage());
            return false;
        }
    }

    /** Total categorías */
    public function count() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM categories");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch(PDOException $e) {
            error_log("Error counting categories: " . $e->getMessage());
            return 0;
        }
    }
}
