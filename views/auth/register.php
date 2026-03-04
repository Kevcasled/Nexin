<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="auth-container">
    <div class="auth-box">
        <h1>Crear Cuenta</h1>
        <p class="auth-subtitle">Únete a la comunidad de Nexin</p>
        
        <?php if (isset($errors) && !empty($errors)): ?>
            <div style="background: rgba(229,9,20,0.15); border: 1px solid rgba(229,9,20,0.3); color: #ff6b6b; padding: 12px 20px; border-radius: 12px; margin-bottom: 20px;">
                <?php foreach ($errors as $error): ?>
                    <p style="margin-bottom: 5px; font-size: 0.9rem;"><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form action="index.php?action=register" method="POST" class="auth-form">
            <?= Csrf::insertHiddenField() ?>
            
            <div class="form-group">
                <label for="username">Nombre de Usuario</label>
                <input type="text" id="username" name="username" 
                       value="<?= isset($old['username']) ? htmlspecialchars($old['username']) : '' ?>" 
                       placeholder="Usuario123" required>
            </div>
            
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" 
                       value="<?= isset($old['email']) ? htmlspecialchars($old['email']) : '' ?>" 
                       placeholder="tu@email.com" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="Mínimo 8 caracteres" required>
                <p style="color: #666; font-size: 0.85rem; margin-top: 5px;">Debe tener al menos 8 caracteres</p>
            </div>
            
            <button type="submit" class="btn-submit">Crear Cuenta</button>
            
            <p class="auth-footer">
                ¿Ya tienes cuenta? <a href="index.php?action=login">Inicia sesión aquí</a>
            </p>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>