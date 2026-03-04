<?php include __DIR__ . '/../layout/header.php'; ?>

<section class="hero">
    <div class="hero-content">
        <span class="badge">Reseñas</span>
        <h1>Nexin</h1>
        <p class="description">Tu plataforma de reseñas y noticias sobre las mejores series y películas del momento. Descubre, comenta y comparte tu opinión.</p>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <div class="hero-buttons">
                <a href="index.php?action=register" class="btn-primary" style="text-decoration:none;">Suscribirse</a>
                <a href="index.php?action=login" class="btn-secondary" style="text-decoration:none;">Iniciar Sesión</a>
            </div>
        <?php else: ?>
            <div class="hero-buttons">
                <a href="index.php?action=create_post" class="btn-primary" style="text-decoration:none;">+ Nueva Reseña</a>
                <a href="index.php?action=rag_ask" class="btn-secondary" style="text-decoration:none;">🔍 Buscar con IA</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<div class="main-container">
    <main>
        <h2>Últimas Reseñas</h2>
        
        <?php if (empty($posts)): ?>
            <div style="background: linear-gradient(145deg, #1a1a1a, #242424); border-radius: 16px; padding: 40px; text-align: center;">
                <p style="color: #b3b3b3; font-size: 1.1rem;">No hay publicaciones disponibles todavía.</p>
            </div>
        <?php else: ?>
            <div class="movies-grid">
                <?php foreach ($posts as $post): ?>
                    <a href="index.php?action=show_post&id=<?= $post['id'] ?>" style="text-decoration: none; color: inherit;">
                        <article class="card">
                            <?php if (!empty($post['image_path'])): ?>
                                <div class="poster-placeholder" style="background: url('<?= htmlspecialchars($post['image_path']) ?>') center/cover no-repeat;">
                                </div>
                            <?php else: ?>
                                <div class="poster-placeholder">IMG</div>
                            <?php endif; ?>
                            <h3><?= htmlspecialchars($post['title']) ?></h3>
                            <?php if (!empty($post['category_name'])): ?>
                                <span class="rating"><?= htmlspecialchars($post['category_name']) ?></span>
                            <?php endif; ?>
                            <p class="card-description">
                                <?= htmlspecialchars(mb_substr(strip_tags($post['content']), 0, 100)) ?>...
                            </p>
                            <p style="padding: 8px 20px 18px; color: #555; font-size: 0.82rem; border-top: 1px solid rgba(255,255,255,0.05); margin-top: 8px; padding-top: 12px;">
                                Por <?= htmlspecialchars($post['username']) ?> · <?= date('d/m/Y', strtotime($post['created_at'])) ?>
                            </p>
                        </article>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <aside>
        <div class="sidebar-section">
            <h3>Géneros</h3>
            <ul>
                <?php
                // Show categories from the database if available via controller
                if (isset($categories) && !empty($categories)):
                    foreach ($categories as $cat): ?>
                        <li><a href="#"><?= htmlspecialchars($cat['name']) ?></a></li>
                    <?php endforeach;
                else: ?>
                    <li><a href="#">Ciencia Ficción</a></li>
                    <li><a href="#">Drama</a></li>
                    <li><a href="#">Thriller</a></li>
                    <li><a href="#">Comedia</a></li>
                    <li><a href="#">Terror</a></li>
                    <li><a href="#">Acción</a></li>
                <?php endif; ?>
            </ul>
        </div>
        
        <div class="sidebar-section">
            <h3>Top Valoradas</h3>
            <ol>
                <li>1. Breaking Bad</li>
                <li>2. The Wire</li>
                <li>3. Dark</li>
                <li>4. Arcane</li>
                <li>5. The Bear</li>
            </ol>
        </div>
    </aside>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>