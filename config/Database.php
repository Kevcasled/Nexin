<?php
require_once __DIR__ . '/Environment.php';

/** Conector MySQL usando PDO */
class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $pdo;

    /** Carga credenciales (.env) */
    public function __construct() {
        $this->host     = Environment::get('DB_HOST', 'db');
        $this->db_name  = Environment::get('DB_NAME', 'blog_db');
        $this->username = Environment::get('DB_USER', 'blog_user');
        $this->password = Environment::get('DB_PASS', 'blog_pass');
    }

    /** Retorna conexión PDO o null */
    public function getConnection() {
        $this->pdo = null;
        try {
            $this->pdo = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            error_log("Error de conexión: " . $exception->getMessage());
            if (Environment::isDevelopment()) {
                echo "Error de conexión: " . $exception->getMessage();
            } else {
                echo "Error interno del servidor. Inténtalo más tarde.";
            }
        }
        return $this->pdo;
    }
}