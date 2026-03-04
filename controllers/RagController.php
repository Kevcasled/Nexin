<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Retriever.php';
require_once __DIR__ . '/../utils/LLM.php';
require_once __DIR__ . '/../utils/Auth.php';
require_once __DIR__ . '/../utils/Flash.php';
require_once __DIR__ . '/../utils/Csrf.php';
require_once __DIR__ . '/../utils/Validator.php';

/** RAG: Búsqueda inteligente (Ollama) */
class RagController {
    private $retriever;

    public function __construct() {
        Auth::requireLogin();
        $database = new Database();
        $db = $database->getConnection();
        $this->retriever = new Retriever($db);
    }

    /** Formulario pregunta */
    public function ask() {
        $this->render('rag/ask.php', [
            'ollamaAvailable' => LLM::isAvailable()
        ]);
    }

    /** Procesa consulta RAG: Buscar -> Contexto -> IA */
    public function answer() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=rag_ask');
            exit();
        }

        Csrf::verify();

        $query = trim($_POST['query'] ?? '');
        
        $validator = new Validator();
        $validator->required('query', $query, 'La pregunta');
        $validator->minLength('query', $query, 3, 'La pregunta');

        if ($validator->hasErrors()) {
            Flash::error($validator->getFirstError());
            header('Location: index.php?action=rag_ask');
            exit();
        }

        // 1. RETRIEVAL: Buscar posts
        $relatedPosts = $this->retriever->searchByKeywords($query, 5);

        // 2. CONTEXTO para LLM
        $context = $this->retriever->buildContext($relatedPosts);

        // 3. GENERATION: Ollama
        $answer = LLM::generate($query, $context);

        // 4. Render
        $this->render('rag/answer.php', [
            'query'        => $query,
            'answer'       => $answer,
            'relatedPosts' => $relatedPosts,
            'ollamaAvailable' => LLM::isAvailable()
        ]);
    }

    /** Renderiza vista */
    protected function render($view, $data = []) {
        extract($data);
        include __DIR__ . '/../views/' . $view;
    }
}
