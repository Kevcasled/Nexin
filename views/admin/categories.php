<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="panel-container">
    <div class="panel-header">
        <div>
            <h1>Gestión de Géneros</h1>
            <p class="panel-subtitle">Administra los géneros de contenido</p>
        </div>
    </div>

    <!-- Formulario nuevo género -->
    <div class="sidebar-section" style="margin-bottom: 30px;">
        <h3 style="margin-bottom: 15px;">Nuevo Género</h3>
        <form action="index.php?action=admin_create_category" method="POST" style="display: flex; gap: 15px; align-items: center;">
            <?= Csrf::insertHiddenField() ?>
            <input type="text" name="name" placeholder="Nombre del género" required
                   style="flex: 1; padding: 12px 15px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: #fff; font-size: 14px; outline: none;"
                   onfocus="this.style.borderColor='#E50914'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
            <button type="submit" class="btn-primary" style="white-space: nowrap;">+ Crear Género</button>
        </form>
    </div>

    <!-- Tabla de géneros -->
    <div class="reviews-table">
        <table>
            <thead>
                <tr>
                    <th style="padding: 15px; text-align: left; color: #e5e5e5; font-weight: 700; border-bottom: 2px solid rgba(255,255,255,0.1);">ID</th>
                    <th style="padding: 15px; text-align: left; color: #e5e5e5; font-weight: 700; border-bottom: 2px solid rgba(255,255,255,0.1);">Nombre</th>
                    <th style="padding: 15px; text-align: left; color: #e5e5e5; font-weight: 700; border-bottom: 2px solid rgba(255,255,255,0.1);">Slug</th>
                    <th style="padding: 15px; text-align: left; color: #e5e5e5; font-weight: 700; border-bottom: 2px solid rgba(255,255,255,0.1);">Fecha</th>
                    <th style="padding: 15px; text-align: left; color: #e5e5e5; font-weight: 700; border-bottom: 2px solid rgba(255,255,255,0.1);">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr class="review-row">
                        <td style="padding: 20px 15px; color: #b3b3b3;"><?= $category['id'] ?></td>
                        <td style="padding: 20px 15px; font-weight: 600;">
                            <span class="badge"><?= htmlspecialchars($category['name']) ?></span>
                        </td>
                        <td style="padding: 20px 15px; color: #666;"><?= htmlspecialchars($category['slug']) ?></td>
                        <td style="padding: 20px 15px; color: #666;"><?= date('d/m/Y', strtotime($category['created_at'])) ?></td>
                        <td style="padding: 20px 15px;">
                            <form action="index.php?action=admin_delete_category&id=<?= $category['id'] ?>" method="POST"
                                  onsubmit="return confirm('¿Eliminar este género?')">
                                <?= Csrf::insertHiddenField() ?>
                                <button type="submit" class="btn-action delete" title="Eliminar">🗑️</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php if (empty($categories)): ?>
            <p style="text-align: center; padding: 40px; color: #666;">No hay géneros</p>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
