<?php
/** Sistema de autenticación y autorización */
class Auth {
    
    /** Requiere login o redirige */
    public static function requireLogin() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Debes iniciar sesión para acceder a esta página';
            header('Location: index.php?action=login');
            exit();
        }
    }
    
    /** Requiere admin o redirige */
    public static function requireAdmin() {
        self::requireLogin();
        
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'No tienes permisos para acceder a esta página';
            header('Location: index.php?action=posts');
            exit();
        }
    }
    
    /** ¿Usuario logueado? */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    /** ¿Usuario admin? */
    public static function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
    
    /** ¿Puede modificar el recurso? */
    public static function canModify($resourceUserId) {
        if (!self::isLoggedIn()) {
            return false;
        }
        
        return $_SESSION['user_id'] == $resourceUserId || self::isAdmin();
    }
}
