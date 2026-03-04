<?php
/** Protege formularios contra ataques CSRF */
class Csrf {

    /** Genera o reutiliza token CSRF */
    public static function generateToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /** Valida el token contra la sesión */
    public static function validateToken($token) {
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /** Inserta campo oculto CSRF */
    public static function insertHiddenField() {
        $token = self::generateToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }

    /** Verifica el token en peticiones POST */
    public static function verify() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            if (!self::validateToken($token)) {
                require_once __DIR__ . '/Flash.php';
                Flash::error('Token de seguridad inválido. Inténtalo de nuevo.');
                header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php?action=posts'));
                exit();
            }
        }
    }

    /** Regenera el token CSRF */
    public static function regenerateToken() {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}
