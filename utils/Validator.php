<?php
/** Sistema de validación de datos */
class Validator {
    
    private $errors = [];
    
    /** Campo obligatorio */
    public function required($field, $value, $fieldName = null) {
        if (empty(trim($value))) {
            $this->errors[$field] = ($fieldName ?? $field) . ' es obligatorio';
            return false;
        }
        return true;
    }
    
    /** Formato email */
    public function email($field, $value) {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = 'El email no tiene un formato válido';
            return false;
        }
        return true;
    }
    
    /** Longitud mínima */
    public function minLength($field, $value, $min, $fieldName = null) {
        if (strlen($value) < $min) {
            $this->errors[$field] = ($fieldName ?? $field) . ' debe tener al menos ' . $min . ' caracteres';
            return false;
        }
        return true;
    }
    
    /** Longitud máxima */
    public function maxLength($field, $value, $max, $fieldName = null) {
        if (strlen($value) > $max) {
            $this->errors[$field] = ($fieldName ?? $field) . ' no puede exceder ' . $max . ' caracteres';
            return false;
        }
        return true;
    }
    
    /** Imagen válida */
    public function image($field, $file) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 2 * 1024 * 1024;
        
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            $this->errors[$field] = 'Debes seleccionar una imagen';
            return false;
        }
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[$field] = 'Error al subir la imagen';
            return false;
        }
        
        $imageInfo = @getimagesize($file['tmp_name']);
        if ($imageInfo === false || !in_array($imageInfo['mime'], $allowedTypes)) {
            $this->errors[$field] = 'La imagen debe ser JPG, PNG, GIF o WEBP';
            return false;
        }
        
        if ($file['size'] > $maxSize) {
            $this->errors[$field] = 'La imagen no puede superar los 2MB';
            return false;
        }
        return true;
    }
    
    /** Imagen opcional */
    public function imageOptional($field, $file) {
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) return true;
        return $this->image($field, $file);
    }
    
    /** Sanitizar cadena (XSS) */
    public static function sanitize($value) {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }
    
    /** Sanitizar array */
    public static function sanitizeArray($data) {
        $sanitized = [];
        foreach ($data as $key => $value) {
            $sanitized[$key] = is_array($value) ? self::sanitizeArray($value) : self::sanitize($value);
        }
        return $sanitized;
    }
    
    public function getErrors() { return $this->errors; }
    public function hasErrors() { return !empty($this->errors); }
    public function getFirstError() { return !empty($this->errors) ? reset($this->errors) : null; }
}
