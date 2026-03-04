<?php
/** Sistema de mensajes flash */
class Flash {
    
    /** Establece mensaje */
    public static function set($type, $message) {
        $_SESSION['flash'][$type] = $message;
    }
    
    /** Obtiene y elimina mensaje */
    public static function get($type) {
        if (isset($_SESSION['flash'][$type])) {
            $message = $_SESSION['flash'][$type];
            unset($_SESSION['flash'][$type]);
            return $message;
        }
        return null;
    }
    
    /** ¿Tiene mensaje? */
    public static function has($type) {
        return isset($_SESSION['flash'][$type]);
    }
    
    /** Éxito */
    public static function success($message) {
        self::set('success', $message);
    }
    
    /** Error */
    public static function error($message) {
        self::set('error', $message);
    }
    
    /** Info */
    public static function info($message) {
        self::set('info', $message);
    }
    
    /** Alerta */
    public static function warning($message) {
        self::set('warning', $message);
    }
    
    /** Obtiene todos y limpia */
    public static function getAll() {
        $messages = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $messages;
    }
}
