<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="panel-container">
    <div class="panel-header">
        <div>
            <h1>Gestión de Comentarios</h1>
            <p class="panel-subtitle">Modera los comentarios de la plataforma</p>
        </div>
    </div>

    <div class="reviews-table">
        <table>
            <thead>
                <tr>
                    <th style="padding: 15px; text-align: left; color: #e5e5e5; font-weight: 700; border-bottom: 2px solid rgba(255,255,255,0.1);">ID</th>
                    <th style="padding: 15px; text-align: left; color: #e5e5e5; font-weight: 700; border-bottom: 2px solid rgba(255,255,255,0.1);">Usuario</th>
                    <th style="padding: 15px; text-align: left; color: #e5e5e5; font-weight: 700; border-bottom: 2px solid rgba(255,255,255,0.1);">Reseña</th>
                    <th style="padding: 15px; text-align: left; color: #e5e5e5; font-weight: 700; border-bottom: 2px solid rgba(255,255,255,0.1);">Comentario</th>
                    <th style="padding: 15px; text-align: left; color: #e5e5e5; font-weight: 700; border-bottom: 2px solid rgba(255,255,255,0.1);">Fecha</th>
                    <th style="padding: 15px; text-align: left; color: #e5e5e5; font-weight: 700; border-bottom: 2px solid rgba(255,255,255,0.1);">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comments as $comment): ?>
                    <tr class="review-row">
                        <td style="padding: 20px 15px; color: #b3b3b3;"><?= $comment['id'] ?></td>
                        <td style="padding: 20px 15px; font-weight: 600;"><?= htmlspecialchars($comment['username']) ?></td>
                        <td style="padding: 20px 15px;">
                            <a href="index.php?action=show_post&id=<?= $comment['post_id'] ?>" style="color: #E50914; text-decoration: none; font-weight: 600;">
                                <?= htmlspecialchars($comment['post_title']) ?>
                            </a>
                        </td>
                        <td style="padding: 20px 15px; color: #b3b3b3; max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            <?= htmlspecialchars($comment['text']) ?>
                        </td>
                        <td style="padding: 20px 15px; color: #666;"><?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?></td>
                        <td style="padding: 20px 15px;">
                            <form action="index.php?action=admin_delete_comment&id=<?= $comment['id'] ?>" method="POST"
                                  onsubmit="return confirm('¿Eliminar este comentario?')">
                                <?= Csrf::insertHiddenField() ?>
                                <button type="submit" class="btn-action delete" title="Eliminar">🗑️</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php if (empty($comments)): ?>
            <p style="text-align: center; padding: 40px; color: #666;">No hay comentarios</p>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>