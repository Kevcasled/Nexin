<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="auth-container">
    <div class="auth-box">
        <h1>Iniciar Sesión</h1>
        <p class="auth-subtitle">Accede a tu cuenta de Nexin</p>
        
        <form action="index.php?action=login" method="POST" class="auth-form">
            <?= Csrf::insertHiddenField() ?>
            
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" placeholder="tu@email.com" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>
            
            <button type="submit" class="btn-submit">Iniciar Sesión</button>
            
            <p class="auth-footer">
                ¿No tienes cuenta? <a href="index.php?action=register">Regístrate aquí</a>
            </p>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
