<?php
/** Modelo de Usuario */
class User {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /** Crear nuevo usuario */
    public function insert($username, $email, $password, $role = 'writer') {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)");
            return $stmt->execute([$username, $email, $hash, $role]);
        } catch (PDOException $e) {
            error_log("Error al insertar usuario: " . $e->getMessage());
            return false;
        }
    }

    /** Buscar por email */
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /** Buscar por ID */
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /** Obtener todos los usuarios */
    public function getAll() {
        $stmt = $this->db->query("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Verificar contraseña */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /** Actualizar rol */
    public function updateRole($id, $role) {
        $stmt = $this->db->prepare("UPDATE users SET role = ? WHERE id = ?");
        return $stmt->execute([$role, $id]);
    }

    /** Eliminar usuario */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /** Contar total usuarios */
    public function count() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM users");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
}