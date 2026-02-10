<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';

// Получаем список мастеров
$stmt = $pdo->query("
    SELECT 
        i.*,
        (SELECT AVG(rating) FROM installer_reviews WHERE installer_id = i.id) as avg_rating,
        (SELECT COUNT(*) FROM installer_reviews WHERE installer_id = i.id) as review_count
    FROM installers i 
    WHERE i.is_active = 1
");
$installers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold">Наши мастера</h2>
        <p class="text-muted">Профессиональный монтаж и обслуживание</p>
    </div>

    <div class="row g-4">
        <?php foreach ($installers as $inst): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <?php if (!empty($inst['photo'])): ?>
                                <img src="images/installers/<?= htmlspecialchars($inst['photo']) ?>" class="rounded-circle shadow-sm" style="width: 120px; height: 120px; object-fit: cover;">
                            <?php else: ?>
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto text-secondary fw-bold" style="width: 120px; height: 120px; font-size: 2rem;">
                                    <?= mb_substr($inst['first_name'], 0, 1) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- ИСПРАВЛЕНО: Вывод Имени и Фамилии -->
                        <h5 class="fw-bold mb-1">
                            <?= htmlspecialchars($inst['first_name'] . ' ' . $inst['last_name']) ?>
                        </h5>
                        
                        <p class="text-muted small mb-2"><?= htmlspecialchars($inst['specialization'] ?? 'Мастер') ?></p>
                        
                        <div class="mb-3 text-warning small">
                            <?php 
                                $rating = round($inst['avg_rating'] ?? 0); 
                                for($i=0; $i<5; $i++) echo $i < $rating ? '★' : '☆'; 
                            ?>
                            <span class="text-muted ms-1">(<?= $inst['review_count'] ?? 0 ?>)</span>
                        </div>

                        <a href="installer.php?id=<?= $inst['id'] ?>" class="btn btn-outline-warning w-100 fw-bold">Подробнее</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
