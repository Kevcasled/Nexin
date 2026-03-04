<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="panel-container">
    <div class="panel-header">
        <div>
            <h1>Gestión de Usuarios</h1>
            <p class="panel-subtitle">Administra los usuarios de la plataforma</p>
        </div>
    </div>

    <div class="reviews-table">
        <table>
            <thead>
                <tr>
                    <th style="padding: 15px; text-align: left; color: #e5e5e5; font-weight: 700; border-bottom: 2px solid rgba(255,255,255,0.1);">ID</th>
                    <th style="padding: 15px; text-align: left; color: #e5e5e5; font-weight: 700; border-bottom: 2px solid rgba(255,255,255,0.1);">Usuario</th>
                    <th style="padding: 15px; text-align: left; color: #e5e5e5; font-weight: 700; border-bottom: 2px solid rgba(255,255,255,0.1);">Email</th>
                    <th style="padding: 15px; text-align: left; color: #e5e5e5; font-weight: 700; border-bottom: 2px solid rgba(255,255,255,0.1);">Rol</th>
                    <th style="padding: 15px; text-align: left; color: #e5e5e5; font-weight: 700; border-bottom: 2px solid rgba(255,255,255,0.1);">Registro</th>
                    <th style="padding: 15px; text-align: left; color: #e5e5e5; font-weight: 700; border-bottom: 2px solid rgba(255,255,255,0.1);">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr class="review-row">
                        <td style="padding: 20px 15px; color: #b3b3b3;"><?= $user['id'] ?></td>
                        <td style="padding: 20px 15px; font-weight: 600;"><?= htmlspecialchars($user['username']) ?></td>
                        <td style="padding: 20px 15px; color: #b3b3b3;"><?= htmlspecialchars($user['email']) ?></td>
                        <td style="padding: 20px 15px;">
                            <?php if ($user['role'] === 'admin'): ?>
                                <span class="badge" style="font-size: 0.75rem; padding: 4px 12px;">Admin</span>
                            <?php else: ?>
                                <span style="background: rgba(59,130,246,0.2); color: #60a5fa; padding: 4px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 600;">Writer</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 20px 15px; color: #666;"><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                        <td style="padding: 20px 15px;">
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <div class="action-buttons">
                                    <form action="index.php?action=admin_change_user_role&id=<?= $user['id'] ?>" method="POST" style="display: inline;">
                                        <?= Csrf::insertHiddenField() ?>
                                        <button type="submit" class="btn-action" title="Cambiar rol">
                                            <?= $user['role'] === 'admin' ? '⬇️' : '⬆️' ?>
                                        </button>
                                    </form>
                                    <form action="index.php?action=admin_delete_user&id=<?= $user['id'] ?>" method="POST" style="display: inline;"
                                          onsubmit="return confirm('¿Eliminar este usuario?')">
                                        <?= Csrf::insertHiddenField() ?>
                                        <button type="submit" class="btn-action delete" title="Eliminar">🗑️</button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <span style="color: #666; font-size: 0.8rem;">(Tu cuenta)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>