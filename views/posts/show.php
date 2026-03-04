<?php 
include __DIR__ . '/../layout/header.php'; 
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/Comment.php';
require_once __DIR__ . '/../../utils/Auth.php';

$database = new Database();
$db = $database->getConnection();
$commentModel = new Comment($db);
$comments = $commentModel->getByPostId($post['id']);
?>

<!-- Mini hero for the post -->
<section class="hero" style="height: 50vh; <?= !empty($post['image_path']) ? "background-image: linear-gradient(to bottom, rgba(15,15,15,0.3) 0%, rgba(15,15,15,0.5) 50%, rgba(15,15,15,0.9) 85%, #0f0f0f 100%), url('" . htmlspecialchars($post['image_path']) . "'); background-size: cover; background-position: center;" : '' ?>">
    <div class="hero-content">
        <?php if (!empty($post['category_name'])): ?>
            <span class="badge"><?= htmlspecialchars($post['category_name']) ?></span>
        <?php endif; ?>
        <h1 style="font-size: 3rem;"><?= htmlspecialchars($post['title']) ?></h1>
        <p class="description">
            Por <strong><?= htmlspecialchars($post['username']) ?></strong> 
            · <?= date('d/m/Y', strtotime($post['created_at'])) ?>
            <?php if ($post['status'] === 'draft'): ?>
                · <span style="color: #ffa500; font-weight: 600;">BORRADOR</span>
            <?php endif; ?>
        </p>
    </div>
</section>

<div class="main-container" style="flex-direction: column; max-width: 900px;">
    <!-- Post content -->
    <article class="news-article" style="flex-direction: column; padding: 30px 40px;">
        <div class="news-content" style="width: 100%;">
            <div style="color: #b3b3b3; line-height: 1.8; font-size: 1.05rem; white-space: pre-wrap;">
<?= nl2br(htmlspecialchars($post['content'])) ?>
            </div>
            
            <?php if (Auth::canModify($post['user_id'])): ?>
                <div style="display: flex; gap: 15px; margin-top: 30px; padding-top: 25px; border-top: 1px solid rgba(255,255,255,0.1);">
                    <a href="index.php?action=edit_post&id=<?= $post['id'] ?>" class="btn-primary" style="text-decoration: none; padding: 12px 28px; font-size: 0.95rem;">
                        ✏️ Editar
                    </a>
                    <form action="index.php?action=delete_post&id=<?= $post['id'] ?>" method="POST" 
                          onsubmit="return confirm('¿Estás seguro de eliminar esta reseña?')">
                        <?= Csrf::insertHiddenField() ?>
                        <button type="submit" class="btn-secondary" style="cursor: pointer; color: #ff6b6b; border-color: rgba(229,9,20,0.3);">
                            🗑️ Eliminar
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </article>
    
    <!-- Comments section -->
    <div class="sidebar-section" style="border-radius: 20px; padding: 30px; margin-top: 30px;">
        <h3>Comentarios (<?= count($comments) ?>)</h3>
        
        <?php if (Auth::isLoggedIn()): ?>
            <form action="index.php?action=store_comment" method="POST" style="margin-bottom: 25px;">
                <?= Csrf::insertHiddenField() ?>
                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                <textarea name="text" rows="4" placeholder="Escribe tu comentario..." required minlength="5"
                    style="width: 100%; padding: 14px 18px; border: 2px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); border-radius: 12px; color: white; font-size: 1rem; resize: vertical; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin-bottom: 15px; box-sizing: border-box;"
                    onfocus="this.style.borderColor='#E50914'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'"></textarea>
                <button type="submit" class="btn-submit" style="width: auto; padding: 12px 28px;">
                    Publicar Comentario
                </button>
            </form>
        <?php else: ?>
            <div style="background: rgba(255,255,255,0.05); padding: 20px; border-radius: 12px; text-align: center; margin-bottom: 25px;">
                <p style="color: #b3b3b3;">
                    <a href="index.php?action=login" style="color: #E50914; text-decoration: none; font-weight: 600;">Inicia sesión</a> 
                    para dejar un comentario
                </p>
            </div>
        <?php endif; ?>
        
        <div style="display: flex; flex-direction: column; gap: 15px;">
            <?php foreach ($comments as $comment): ?>
                <div style="border-left: 4px solid #E50914; padding: 15px 20px; background: rgba(255,255,255,0.03); border-radius: 0 12px 12px 0;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <div>
                            <strong style="color: #e5e5e5;"><?= htmlspecialchars($comment['username']) ?></strong>
                            <span style="color: #666; font-size: 0.85rem; margin-left: 10px;">
                                <?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?>
                            </span>
                        </div>
                        <?php if (Auth::canModify($comment['user_id'])): ?>
                            <form action="index.php?action=delete_comment&id=<?= $comment['id'] ?>" method="POST"
                                  onsubmit="return confirm('¿Eliminar este comentario?')">
                                <?= Csrf::insertHiddenField() ?>
                                <button type="submit" style="background:none; border:none; color:#E50914; cursor:pointer; font-size:0.85rem; font-weight:600;">Eliminar</button>
                            </form>
                        <?php endif; ?>
                    </div>
                    <p style="color: #b3b3b3; line-height: 1.6;"><?= nl2br(htmlspecialchars($comment['text'])) ?></p>
                </div>
            <?php endforeach; ?>
            
            <?php if (empty($comments)): ?>
                <p style="text-align: center; color: #666; padding: 30px;">No hay comentarios aún. ¡Sé el primero en comentar!</p>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Back button -->
    <div style="text-align: center; margin-top: 20px;">
        <a href="index.php?action=posts" class="read-more" style="font-size: 1rem;">← Volver al Inicio</a>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>