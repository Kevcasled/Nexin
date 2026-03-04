<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="auth-container">
    <div class="auth-box" style="max-width: 700px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; background: rgba(229,9,20,0.15); border-radius: 50%; margin-bottom: 15px;">
                <span style="font-size: 32px;">🔍</span>
            </div>
            <h2>Búsqueda Inteligente</h2>
            <p style="color: #b3b3b3; margin-top: 8px;">
                Haz una pregunta y nuestro sistema buscará en las reseñas para darte una respuesta contextual.
            </p>
        </div>

        <?php if (isset($ollamaAvailable) && !$ollamaAvailable): ?>
            <div style="background: rgba(229,9,20,0.1); border: 1px solid rgba(229,9,20,0.3); color: #ff6b6b; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                <strong>Nota:</strong> El servicio de IA (Ollama) no está disponible. La búsqueda funcionará pero las respuestas serán limitadas.
            </div>
        <?php endif; ?>
        
        <form action="index.php?action=rag_answer" method="POST" class="auth-form">
            <?= Csrf::insertHiddenField() ?>
            
            <div class="form-group">
                <label>Tu pregunta</label>
                <textarea name="query" rows="4" 
                          placeholder="Ejemplo: ¿Qué series de ciencia ficción hay? / ¿Cuáles son las mejores películas? / Resúmeme las reseñas de terror"
                          style="width: 100%; padding: 12px 15px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: #fff; font-size: 14px; resize: vertical; outline: none; font-family: 'Segoe UI', sans-serif;"
                          onfocus="this.style.borderColor='#E50914'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'"
                          required minlength="3"><?= htmlspecialchars($_POST['query'] ?? '') ?></textarea>
            </div>
            
            <button type="submit" class="btn-submit" style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                🔍 Buscar con IA
            </button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
