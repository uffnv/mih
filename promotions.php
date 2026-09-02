<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// ==========================================
// ПОЛУЧЕНИЕ СПИСКА АКЦИЙ
// ==========================================
$promotions = [];
try {
    // Выбираем все активные акции, сортируем по порядку или дате
    $stmt = $pdo->query("
        SELECT * FROM promotions 
        WHERE is_active = 1 
        AND start_date <= NOW() 
        AND (end_date IS NULL OR end_date >= NOW())
        ORDER BY sort_order ASC, created_at DESC
    ");
    $promotions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $promotions = [];
}

$page_title = 'Акции и скидки — Михаил Потапыч';
require_once __DIR__ . '/includes/header.php';
?>

<main class="bg-light">
    <!-- Заголовок страницы -->
    <div class="hero-gradient text-white py-5">
        <div class="container text-center py-3">
            <h1 class="display-5 fw-bold text-uppercase mb-2">Акции и <span class="text-warning">Скидки</span></h1>
            <p class="lead text-white-50 mb-0">Лучшие предложения на отопительное оборудование и монтаж</p>
        </div>
    </div>

    <div class="container py-5">
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="index.php">Главная</a></li>
                <li class="breadcrumb-item active" aria-current="page">Акции</li>
            </ol>
        </nav>

        <?php if (!empty($promotions)): ?>
            <div class="row g-4">
                <?php foreach ($promotions as $promo): ?>
                    <div class="col-lg-6" id="promo-<?= $promo['id']; ?>">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden card-hover bg-white">
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
                                             style="min-height: 240px;"
                                             onerror="this.src='images/hero-bg.jpg'">
                                    <?php else: ?>
                                        <div class="bg-gradient-primary h-100 d-flex align-items-center justify-content-center" style="min-height: 240px;">
                                            <i class="bi bi-gift-fill display-4 text-white opacity-75"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Контент акции -->
                                <div class="col-md-7">
                                    <div class="card-body d-flex flex-column p-4 h-100">
                                        <div class="mb-2">
                                            <small class="text-muted d-flex align-items-center gap-1">
                                                <i class="bi bi-calendar3 text-warning"></i> 
                                                <?= date('d.m.Y', strtotime($promo['created_at'])); ?>
                                            </small>
                                        </div>
                                        
                                        <h4 class="card-title fw-bold mb-3 text-dark">
                                            <?= htmlspecialchars($promo['title']); ?>
                                        </h4>
                                        
                                        <div class="card-text text-muted small mb-4 flex-grow-1 lh-base">
                                            <?= nl2br(htmlspecialchars($promo['description'])); ?>
                                        </div>

                                        <?php if (!empty($promo['end_date'])): ?>
                                            <div class="alert alert-warning py-2 px-3 mb-3 small rounded-3 d-flex align-items-center gap-2">
                                                <i class="bi bi-clock-history text-dark"></i> 
                                                <span>Действует до: <strong><?= date('d.m.Y', strtotime($promo['end_date'])); ?></strong></span>
                                            </div>
                                        <?php endif; ?>

                                        <a href="catalog.php" class="btn btn-warning w-100 mt-auto fw-bold rounded-pill shadow-sm">
                                            В каталог по акции <i class="bi bi-arrow-right ms-1"></i>
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
                <i class="bi bi-tag-fill display-3 text-muted opacity-50 mb-3"></i>
                <h3 class="fw-bold">Активных акций пока нет</h3>
                <p class="text-muted mb-4">Следите за обновлениями, скоро мы подготовим новые скидки!</p>
                <div>
                    <a href="catalog.php" class="btn btn-warning fw-bold px-4 py-2 rounded-pill">Перейти в каталог</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
