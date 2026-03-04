<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="auth-container">
    <div class="auth-box" style="max-width: 700px;">
        <h1>Editar Reseña</h1>
        <p class="auth-subtitle">Modifica tu reseña</p>
        
        <?php if (isset($errors) && !empty($errors)): ?>
            <div style="background: rgba(229,9,20,0.15); border: 1px solid rgba(229,9,20,0.3); color: #ff6b6b; padding: 12px 20px; border-radius: 12px; margin-bottom: 20px;">
                <ul style="list-style: disc; padding-left: 20px;">
                    <?php foreach ($errors as $error): ?>
                        <li style="margin-bottom: 5px;"><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form action="index.php?action=update_post&id=<?= $post['id'] ?>" method="POST" enctype="multipart/form-data" class="auth-form">
            <?= Csrf::insertHiddenField() ?>
            
            <div class="form-group">
                <label for="title">Título *</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($old['title'] ?? $post['title']) ?>" required maxlength="150">
            </div>
            
            <div class="form-group">
                <label for="category_id">Género</label>
                <select name="category_id" id="category_id"
                    style="padding: 14px 18px; border: 2px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); border-radius: 12px; color: white; font-size: 1rem; width: 100%;">
                    <option value="" style="background: #1a1a1a;">Sin categoría</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" style="background: #1a1a1a;"
                            <?= (isset($old['category_id']) ? $old['category_id'] : $post['category_id']) == $category['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="content">Contenido *</label>
                <textarea name="content" id="content" rows="10" required
                    style="padding: 14px 18px; border: 2px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); border-radius: 12px; color: white; font-size: 1rem; resize: vertical; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; width: 100%; box-sizing: border-box;"
                    onfocus="this.style.borderColor='#E50914'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'"><?= htmlspecialchars($old['content'] ?? $post['content']) ?></textarea>
            </div>
            
            <div class="form-group">
                <?php if (!empty($post['image_path'])): ?>
                    <label>Imagen actual</label>
                    <img src="<?= htmlspecialchars($post['image_path']) ?>" alt="Imagen actual" 
                         style="width: 100%; max-height: 200px; object-fit: cover; border-radius: 12px; margin-bottom: 15px; border: 2px solid rgba(255,255,255,0.1);">
                <?php endif; ?>
                <label for="image">Cambiar imagen (opcional)</label>
                <input type="file" id="image" name="image" accept="image/*"
                    style="padding: 14px 18px; border: 2px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); border-radius: 12px; color: white; font-size: 0.95rem; width: 100%; box-sizing: border-box;">
                <p style="color: #666; font-size: 0.85rem; margin-top: 8px;">Deja vacío para mantener la imagen actual</p>
            </div>
            
            <div class="form-group">
                <label for="status">Estado</label>
                <select name="status" id="status"
                    style="padding: 14px 18px; border: 2px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); border-radius: 12px; color: white; font-size: 1rem; width: 100%;">
                    <option value="draft" style="background: #1a1a1a;" <?= (isset($old['status']) ? $old['status'] : $post['status']) == 'draft' ? 'selected' : '' ?>>Borrador</option>
                    <option value="published" style="background: #1a1a1a;" <?= (isset($old['status']) ? $old['status'] : $post['status']) == 'published' ? 'selected' : '' ?>>Publicado</option>
                </select>
            </div>
            
            <div style="display: flex; gap: 15px; margin-top: 10px;">
                <button type="submit" class="btn-submit" style="flex: 1;">Actualizar Reseña</button>
                <a href="index.php?action=show_post&id=<?= $post['id'] ?>" class="btn-secondary" style="text-decoration: none; text-align: center; padding: 16px; flex: 0.5;">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>