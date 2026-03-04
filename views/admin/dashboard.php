<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="panel-container">
    <div class="panel-header">
        <div>
            <h1>Panel de Administración</h1>
            <p class="panel-subtitle">Gestiona tu plataforma Nexin</p>
        </div>
    </div>

    <div class="panel-stats">
        <div class="stat-box">
            <h3>Reseñas</h3>
            <div class="stat-number"><?= $stats['posts'] ?></div>
            <a href="index.php?action=admin_posts" class="read-more" style="margin-top: 10px; display: inline-block; font-size: 0.85rem;">Ver todas →</a>
        </div>
        
        <div class="stat-box">
            <h3>Usuarios</h3>
            <div class="stat-number"><?= $stats['users'] ?></div>
            <a href="index.php?action=admin_users" class="read-more" style="margin-top: 10px; display: inline-block; font-size: 0.85rem;">Ver todos →</a>
        </div>
        
        <div class="stat-box">
            <h3>Comentarios</h3>
            <div class="stat-number"><?= $stats['comments'] ?></div>
            <a href="index.php?action=admin_comments" class="read-more" style="margin-top: 10px; display: inline-block; font-size: 0.85rem;">Ver todos →</a>
        </div>
        
        <div class="stat-box">
            <h3>Géneros</h3>
            <div class="stat-number"><?= $stats['categories'] ?></div>
            <a href="index.php?action=admin_categories" class="read-more" style="margin-top: 10px; display: inline-block; font-size: 0.85rem;">Ver todos →</a>
        </div>
    </div>

    <div class="reviews-table" style="margin-top: 30px;">
        <h2 style="font-size: 1.5rem; margin-bottom: 20px; font-weight: 700;">Accesos Rápidos</h2>
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
            <a href="index.php?action=admin_posts" class="sidebar-section" style="text-decoration: none; color: inherit; margin-bottom: 0;">
                <h3 style="font-size: 1.1rem;">Gestionar Reseñas</h3>
                <p style="color: #999; font-size: 0.9rem; margin-top: 5px;">Ver, editar y eliminar reseñas</p>
            </a>
            <a href="index.php?action=admin_users" class="sidebar-section" style="text-decoration: none; color: inherit; margin-bottom: 0;">
                <h3 style="font-size: 1.1rem;">Gestionar Usuarios</h3>
                <p style="color: #999; font-size: 0.9rem; margin-top: 5px;">Administrar usuarios y roles</p>
            </a>
            <a href="index.php?action=admin_comments" class="sidebar-section" style="text-decoration: none; color: inherit; margin-bottom: 0;">
                <h3 style="font-size: 1.1rem;">Gestionar Comentarios</h3>
                <p style="color: #999; font-size: 0.9rem; margin-top: 5px;">Moderar comentarios</p>
            </a>
            <a href="index.php?action=admin_categories" class="sidebar-section" style="text-decoration: none; color: inherit; margin-bottom: 0;">
                <h3 style="font-size: 1.1rem;">Gestionar Géneros</h3>
                <p style="color: #999; font-size: 0.9rem; margin-top: 5px;">Crear y editar géneros</p>
            </a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>