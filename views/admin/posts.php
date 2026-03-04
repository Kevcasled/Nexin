<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="panel-container">
    <div class="panel-header">
        <div>
            <h1>Gestión de Reseñas</h1>
            <p class="panel-subtitle">Administra todas las reseñas del blog</p>
        </div>
        <a href="index.php?action=create_post" class="btn-new-review">+ Nueva Reseña</a>
    </div>

    <div class="reviews-table">
        <table>
            <thead>
                <tr>
                    <th style="padding: 15px; text-align: left; color: #e5e5e5; font-weight: 700; border-bottom: 2px solid rgba(255,255,255,0.1);">ID</th>
                    <th style="padding: 15px; text-align: left; color: #e5e5e5; font-weight: 700; border-bottom: 2px solid rgba(255,255,255,0.1);">Título</th>
                    <th style="padding: 15px; text-align: left; color: #e5e5e5; font-weight: 700; border-bottom: 2px solid rgba(255,255,255,0.1);">Autor</th>
                    <th style="padding: 15px; text-align: left; color: #e5e5e5; font-weight: 700; border-bottom: 2px solid rgba(255,255,255,0.1);">Género</th>
                    <th style="padding: 15px; text-align: left; color: #e5e5e5; font-weight: 700; border-bottom: 2px solid rgba(255,255,255,0.1);">Estado</th>
                    <th style="padding: 15px; text-align: left; color: #e5e5e5; font-weight: 700; border-bottom: 2px solid rgba(255,255,255,0.1);">Fecha</th>
                    <th style="padding: 15px; text-align: left; color: #e5e5e5; font-weight: 700; border-bottom: 2px solid rgba(255,255,255,0.1);">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post): ?>
                    <tr class="review-row">
                        <td style="padding: 20px 15px; color: #b3b3b3;"><?= $post['id'] ?></td>
                        <td style="padding: 20px 15px;">
                            <a href="index.php?action=show_post&id=<?= $post['id'] ?>" style="color: #E50914; text-decoration: none; font-weight: 600;">
                                <?= htmlspecialchars($post['title']) ?>
                            </a>
                        </td>
                        <td style="padding: 20px 15px; color: #b3b3b3;"><?= htmlspecialchars($post['username']) ?></td>
                        <td style="padding: 20px 15px; color: #b3b3b3;"><?= htmlspecialchars($post['category_name'] ?? 'Sin género') ?></td>
                        <td style="padding: 20px 15px;">
                            <?php if ($post['status'] === 'published'): ?>
                                <span class="status-badge published">Publicado</span>
                            <?php else: ?>
                                <span class="status-badge draft">Borrador</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 20px 15px; color: #666;"><?= date('d/m/Y', strtotime($post['created_at'])) ?></td>
                        <td style="padding: 20px 15px;">
                            <div class="action-buttons">
                                <a href="index.php?action=edit_post&id=<?= $post['id'] ?>" class="btn-action" title="Editar" style="text-decoration: none;">✏️</a>
                                <form action="index.php?action=admin_change_post_status&id=<?= $post['id'] ?>" method="POST" style="display: inline;">
                                    <?= Csrf::insertHiddenField() ?>
                                    <button type="submit" class="btn-action" title="<?= $post['status'] === 'published' ? 'Ocultar' : 'Publicar' ?>">
                                        <?= $post['status'] === 'published' ? '👁️' : '📢' ?>
                                    </button>
                                </form>
                                <form action="index.php?action=admin_delete_post&id=<?= $post['id'] ?>" method="POST" style="display: inline;"
                                      onsubmit="return confirm('¿Eliminar esta reseña?')">
                                    <?= Csrf::insertHiddenField() ?>
                                    <button type="submit" class="btn-action delete" title="Eliminar">🗑️</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php if (empty($posts)): ?>
            <p style="text-align: center; padding: 40px; color: #666;">No hay reseñas</p>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>