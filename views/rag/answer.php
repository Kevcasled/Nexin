<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="auth-container" style="align-items: flex-start; padding-top: 140px;">
    <div class="auth-box" style="max-width: 800px;">
        
        <!-- Pregunta original -->
        <div style="display: flex; align-items: flex-start; gap: 15px; margin-bottom: 30px; padding-bottom: 25px; border-bottom: 1px solid rgba(255,255,255,0.08);">
            <div style="flex-shrink: 0; width: 44px; height: 44px; background: rgba(229,9,20,0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <span style="font-size: 20px;">👤</span>
            </div>
            <div>
                <p style="color: #666; font-size: 13px; margin-bottom: 5px;">Tu pregunta:</p>
                <p style="font-size: 18px; font-weight: 600; color: #e5e5e5;"><?= htmlspecialchars($query) ?></p>
            </div>
        </div>
        
        <!-- Respuesta de la IA -->
        <div style="display: flex; align-items: flex-start; gap: 15px; margin-bottom: 30px; padding: 25px; background: rgba(229,9,20,0.04); border-left: 3px solid #E50914; border-radius: 0 12px 12px 0;">
            <div style="flex-shrink: 0; width: 44px; height: 44px; background: rgba(229,9,20,0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <span style="font-size: 20px;">🤖</span>
            </div>
            <div style="flex: 1; min-width: 0;">
                <p style="color: #666; font-size: 13px; margin-bottom: 10px;">
                    Respuesta del asistente
                    <?php if (isset($ollamaAvailable) && !$ollamaAvailable): ?>
                        <span style="color: #E50914;">(sin IA — respuesta basada en búsqueda)</span>
                    <?php endif; ?>
                </p>
                <div style="color: #d4d4d4; line-height: 1.8; font-size: 15px;">
                    <?= nl2br(htmlspecialchars($answer)) ?>
                </div>
            </div>
        </div>
        
        <!-- Posts relacionados -->
        <?php if (!empty($relatedPosts)): ?>
            <div style="margin-bottom: 30px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.08);">
                <h3 style="margin-bottom: 20px; font-size: 1.1rem; font-weight: 700; display: flex; align-items: center; gap: 10px; color: #e5e5e5;">
                    🎬 Reseñas relacionadas (<?= count($relatedPosts) ?>)
                </h3>
                
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <?php foreach ($relatedPosts as $post): ?>
                        <a href="index.php?action=show_post&id=<?= $post['id'] ?>" 
                           style="text-decoration: none; display: flex; align-items: center; gap: 15px; padding: 14px 18px; background: rgba(255,255,255,0.03); border-radius: 12px; border: 1px solid rgba(255,255,255,0.06); transition: all 0.3s ease;"
                           onmouseover="this.style.background='rgba(229,9,20,0.06)'; this.style.borderColor='rgba(229,9,20,0.2)'"
                           onmouseout="this.style.background='rgba(255,255,255,0.03)'; this.style.borderColor='rgba(255,255,255,0.06)'">
                            <?php if (!empty($post['image_path'])): ?>
                                <img src="<?= htmlspecialchars($post['image_path']) ?>" 
                                     alt="<?= htmlspecialchars($post['title']) ?>"
                                     style="width: 60px; height: 60px; object-fit: cover; border-radius: 10px; flex-shrink: 0;">
                            <?php else: ?>
                                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #2d2d2d, #1a1a1a); border-radius: 10px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; color: #555; font-size: 12px;">IMG</div>
                            <?php endif; ?>
                            <div style="flex: 1; min-width: 0;">
                                <h4 style="font-weight: 600; color: #e5e5e5; font-size: 15px; margin-bottom: 4px;"><?= htmlspecialchars($post['title']) ?></h4>
                                <p style="font-size: 12px; color: #666;">
                                    <?= htmlspecialchars($post['username'] ?? '') ?> 
                                    · <?= date('d/m/Y', strtotime($post['created_at'])) ?>
                                    <?php if (!empty($post['category_name'])): ?>
                                        · <span style="color: #E50914;"><?= htmlspecialchars($post['category_name']) ?></span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <span style="color: #555; font-size: 18px; flex-shrink: 0;">→</span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Botones -->
        <div style="display: flex; gap: 15px; justify-content: center; padding-top: 10px;">
            <a href="index.php?action=rag_ask" class="btn-primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 0.95rem;">
                🔍 Nueva pregunta
            </a>
            <a href="index.php?action=posts" class="btn-secondary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 0.95rem;">
                Volver al inicio
            </a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
