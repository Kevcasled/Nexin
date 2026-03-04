<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexin - Reseñas de Series y Películas</title>
    <link rel="stylesheet" href="public/css/nexin.css">
    <style>
        /* Flash messages */
        .flash-container { max-width: 1200px; margin: 100px auto 0; padding: 0 5%; }
        .flash-msg { padding: 12px 20px; border-radius: 12px; margin-bottom: 15px; font-size: 0.95rem; }
        .flash-success { background: rgba(70,211,105,0.15); color: #46d369; border: 1px solid rgba(70,211,105,0.3); }
        .flash-error { background: rgba(229,9,20,0.15); color: #ff6b6b; border: 1px solid rgba(229,9,20,0.3); }
        .flash-info { background: rgba(59,130,246,0.15); color: #60a5fa; border: 1px solid rgba(59,130,246,0.3); }
        .flash-warning { background: rgba(255,165,0,0.15); color: #ffa500; border: 1px solid rgba(255,165,0,0.3); }
        /* Utility */
        .text-center { text-align: center; }
        .mt-20 { margin-top: 20px; }
        .mb-20 { margin-bottom: 20px; }
        .gap-15 { gap: 15px; }
        .inline-flex { display: inline-flex; align-items: center; gap: 8px; }
    </style>
</head>
<body>

    <header>
        <div class="logo">
            <a href="index.php?action=posts" style="display: flex; align-items: center; height: 100%;">
                <img src="public/img/nexin_logo.png" alt="Nexin" style="height: 40px; width: auto; object-fit: contain;">
            </a>
        </div>
        <nav>
            <ul>
                <li><a href="index.php?action=posts" class="<?= (!isset($_GET['action']) || $_GET['action'] === 'posts') ? 'active' : '' ?>">Inicio</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="index.php?action=create_post" class="<?= (isset($_GET['action']) && $_GET['action'] === 'create_post') ? 'active' : '' ?>">Nueva Reseña</a></li>
                    <li><a href="index.php?action=rag_ask" class="<?= (isset($_GET['action']) && in_array($_GET['action'], ['rag_ask','rag_answer'])) ? 'active' : '' ?>">🔍 IA</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="user-actions">
            <?php if (isset($_SESSION['user_id'])): ?>
                <span style="color: #b3b3b3; font-size: 0.9rem;">
                    Hola, <strong style="color: #fff;"><?= htmlspecialchars($_SESSION['username']) ?></strong>
                </span>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="index.php?action=admin_dashboard" style="background: rgba(139,92,246,0.2); color: #a78bfa;">Admin</a>
                <?php endif; ?>
                <a href="index.php?action=logout">Salir</a>
            <?php else: ?>
                <a href="index.php?action=login">Login</a>
                <a href="index.php?action=register" class="btn-register">Suscribirse</a>
            <?php endif; ?>
        </div>
    </header>

    <?php
    require_once __DIR__ . '/../../utils/Flash.php';
    $flashMessages = Flash::getAll();
    if (!empty($flashMessages)): ?>
    <div class="flash-container">
        <?php foreach ($flashMessages as $type => $message):
            $flashClass = 'flash-' . ($type === 'success' ? 'success' : ($type === 'error' ? 'error' : ($type === 'warning' ? 'warning' : 'info')));
        ?>
            <div class="flash-msg <?= $flashClass ?>" role="alert">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>