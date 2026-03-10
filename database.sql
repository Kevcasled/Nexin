-- Charset
SET NAMES utf8mb4;

-- Usuarios
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(80) NOT NULL UNIQUE,
    email VARCHAR(120) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'writer') DEFAULT 'writer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categorías 
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de publicaciones 
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NULL,
    title VARCHAR(150) NOT NULL,
    content TEXT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    status ENUM('draft', 'published') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Tabla de comentarios 
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Índice FULLTEXT para búsqueda RAG (Retrieval-Augmented Generation)
-- Permite búsqueda semántica rápida en títulos y contenido de posts
ALTER TABLE posts ADD FULLTEXT INDEX idx_fulltext_posts (title, content);

-- Usuario administrador (password: Admin123!)
INSERT INTO users (username, email, password_hash, role) 
VALUES ('Admin', 'admin@blog.com', '$2y$10$3wl7WIwUN8jgtdtn1WIQ6uHg9ZmPjgV3OFWilc.eOmfjQI6V/clNq', 'admin')
ON DUPLICATE KEY UPDATE password_hash='$2y$10$3wl7WIwUN8jgtdtn1WIQ6uHg9ZmPjgV3OFWilc.eOmfjQI6V/clNq';

-- Géneros de series y películas
INSERT INTO categories (name, slug) VALUES 
('Ciencia Ficción', 'ciencia-ficcion'),
('Drama', 'drama'),
('Thriller', 'thriller'),
('Comedia', 'comedia'),
('Terror', 'terror'),
('Acción', 'accion')
ON DUPLICATE KEY UPDATE name=name;