<?php
/** Recuperador de contexto para RAG (Keywords + FULLTEXT) */
class Retriever {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /** Búsqueda híbrida: FULLTEXT -> LIKE */
    public function searchByKeywords($query, $limit = 5) {
        try {
            $stmt = $this->db->prepare("
                SELECT p.id, p.title, p.content, p.image_path, p.created_at,
                       u.username, c.name as category_name,
                       MATCH(p.title, p.content) AGAINST(? IN NATURAL LANGUAGE MODE) as relevance
                FROM posts p
                LEFT JOIN users u ON p.user_id = u.id
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'published'
                AND MATCH(p.title, p.content) AGAINST(? IN NATURAL LANGUAGE MODE)
                ORDER BY relevance DESC
                LIMIT ?
            ");
            $stmt->bindValue(1, $query, PDO::PARAM_STR);
            $stmt->bindValue(2, $query, PDO::PARAM_STR);
            $stmt->bindValue(3, $limit, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($results)) {
                $results = $this->searchByLike($query, $limit);
            }

            return $results;
        } catch (PDOException $e) {
            error_log("FULLTEXT search error, using LIKE: " . $e->getMessage());
            return $this->searchByLike($query, $limit);
        }
    }

    /** Fallback por LIKE */
    private function searchByLike($query, $limit = 5) {
        $keywords = $this->extractKeywords($query);
        
        if (empty($keywords)) {
            return [];
        }

        $conditions = [];
        $params = [];
        foreach ($keywords as $keyword) {
            $conditions[] = "(p.title LIKE ? OR p.content LIKE ?)";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
        }

        $whereClause = implode(' OR ', $conditions);

        $sql = "
            SELECT p.id, p.title, p.content, p.image_path, p.created_at,
                   u.username, c.name as category_name
            FROM posts p
            LEFT JOIN users u ON p.user_id = u.id
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.status = 'published' AND ($whereClause)
            ORDER BY p.created_at DESC
            LIMIT $limit
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Extrae palabras clave significativas de una consulta
     * Elimina stopwords en español y palabras cortas
     * @param string $query Texto de la consulta
     * @return array Palabras clave filtradas
     */
    private function extractKeywords($query) {
        $stopwords = ['el', 'la', 'los', 'las', 'un', 'una', 'unos', 'unas', 'de', 'del',
                       'en', 'y', 'o', 'que', 'es', 'por', 'con', 'para', 'como', 'más',
                       'pero', 'su', 'al', 'se', 'no', 'hay', 'son', 'fue', 'ha', 'me',
                       'si', 'ya', 'le', 'lo', 'qué', 'cuál', 'cuáles', 'cómo', 'dónde',
                       'sobre', 'tiene', 'tienen', 'habla', 'hablan', 'posts', 'post',
                       'the', 'is', 'are', 'was', 'were', 'a', 'an', 'and', 'or', 'of'];
        
        $words = preg_split('/\s+/', mb_strtolower(trim($query)));
        $keywords = [];
        
        foreach ($words as $word) {
            $word = preg_replace('/[^\p{L}\p{N}]/u', '', $word);
            if (mb_strlen($word) >= 3 && !in_array($word, $stopwords)) {
                $keywords[] = $word;
            }
        }
        
        return $keywords;
    }

    /**
     * Prepara el contexto de los posts encontrados para el LLM
     * Formatea los posts como texto que el modelo pueda usar
     * @param array $posts Posts relevantes encontrados
     * @return string Texto con el contexto formateado
     */
    public function buildContext($posts) {
        if (empty($posts)) {
            return "No se encontraron publicaciones relevantes en el blog.";
        }

        $context = "Contenido relevante del blog:\n\n";
        
        foreach ($posts as $i => $post) {
            $num = $i + 1;
            $excerpt = mb_substr(strip_tags($post['content']), 0, 500);
            $context .= "--- Artículo $num ---\n";
            $context .= "Título: " . $post['title'] . "\n";
            $context .= "Autor: " . ($post['username'] ?? 'Desconocido') . "\n";
            $context .= "Categoría: " . ($post['category_name'] ?? 'Sin categoría') . "\n";
            $context .= "Fecha: " . date('d/m/Y', strtotime($post['created_at'])) . "\n";
            $context .= "Contenido: $excerpt\n\n";
        }

        return $context;
    }
}
