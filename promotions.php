<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$promotions = [];
try {
    $stmt = $pdo->query("
        SELECT * FROM promotions 
        WHERE is_active = 1 
        AND (start_date IS NULL OR start_date <= NOW()) 
        AND (end_date IS NULL OR end_date >= NOW())
        ORDER BY sort_order ASC, created_at DESC
    ");
    $promotions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $promotions = [];
}

$page_title = 'Акции и спецпредложения — Территория Тепла';
require_once __DIR__ . '/includes/header.php';
?>

<main style="background-color: var(--bg-page, #f8fafc);">
    <!-- Hero заголовок -->
    <div class="py-5 text-white" style="background: linear-gradient(135deg, #0d0f12 0%, #1c2229 100%); border-bottom: 3px solid #FFDF1E;">
        <div class="container text-center py-4">
            <span class="badge bg-warning text-dark text-uppercase px-3 py-2 fw-bold mb-3 shadow-sm">
                <i class="bi bi-tag-fill me-1"></i> Выгодные покупки
            </span>
            <h1 class="display-5 fw-bold text-uppercase mb-2 text-white">Акции и <span class="text-warning">Спецпредложения</span></h1>
            <p class="lead text-white-50 mb-0 max-w-600 mx-auto">
                Сезонные скидки на печи, котлы, сэндвич-дымоходы и комплектующие для отопления
            </p>
        </div>
    </div>

    <div class="container py-5">
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Главная</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Акции</li>
            </ol>
        </nav>

        <?php if (!empty($promotions)): ?>
            <div class="row g-4">
                <?php foreach ($promotions as $promo): ?>
                    <div class="col-lg-6" id="promo-<?= $promo['id']; ?>">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                            <div class="row g-0 h-100">
                                <!-- Изображение акции -->
                                <div class="col-md-5 position-relative bg-light">
                                    <?php if (!empty($promo['discount_percent'])): ?>
                                        <span class="position-absolute top-0 start-0 m-3 badge bg-danger fs-6 px-3 py-2 rounded-pill shadow-sm" style="z-index: 5;">
                                            -<?= htmlspecialchars($promo['discount_percent']); ?>%
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($promo['image_path'])): ?>
                                        <img src="<?= htmlspecialchars($promo['image_path']); ?>" 
                                             class="img-fluid h-100 w-100 object-fit-cover" 
                                             alt="<?= htmlspecialchars($promo['title']); ?>"
                                             style="min-height: 240px;">
                                    <?php else: ?>
                                        <div class="d-flex align-items-center justify-content-center h-100 p-4 text-center text-muted" style="min-height: 240px; background: #e2e8f0;">
                                            <i class="bi bi-fire display-3 text-warning"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Содержимое -->
                                <div class="col-md-7 d-flex flex-column p-4">
                                    <div class="mb-2">
                                        <span class="badge bg-warning text-dark small px-2 py-1">Спецпредложение</span>
                                    </div>
                                    
                                    <h4 class="card-title fw-bold text-dark mb-2">
                                        <?= htmlspecialchars($promo['title']); ?>
                                    </h4>
                                    
                                    <div class="card-text text-muted small mb-3 flex-grow-1">
                                        <?= nl2br(htmlspecialchars($promo['description'] ?? '')); ?>
                                    </div>

                                    <?php if (!empty($promo['end_date'])): ?>
                                        <div class="small text-muted mb-3 d-flex align-items-center gap-1">
                                            <i class="bi bi-clock-history text-warning"></i>
                                            <span>Действует до: <strong><?= date('d.m.Y', strtotime($promo['end_date'])); ?></strong></span>
                                        </div>
                                    <?php endif; ?>

                                    <div>
                                        <a href="catalog.php" class="btn btn-warning fw-bold rounded-pill px-4 shadow-sm w-100">
                                            Перейти к товарам <i class="bi bi-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="card border-0 shadow-sm rounded-4 text-center py-5 px-3 bg-white">
                <i class="bi bi-tag display-3 text-muted opacity-50 mb-3"></i>
                <h4 class="fw-bold">Актуальные скидки в каталоге</h4>
                <p class="text-muted mb-4">В данный момент специальные промокоды не требуются — скидки уже применены к товарам на складе!</p>
                <div>
                    <a href="catalog.php" class="btn btn-warning fw-bold rounded-pill px-4 shadow-sm">
                        Смотреть каталог со скидками
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
