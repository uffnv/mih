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

$page_title = 'Акции и Новости — Михаил Потапыч';
require_once __DIR__ . '/includes/header.php';
?>

<style>
    /* Стили специально для страницы акций */
    .promo-header {
        background: #ffc107;
        color: #000;
    }
    .promo-date {
        font-size: 0.9rem;
        color: #666;
    }
    .discount-badge-lg {
        position: absolute;
        top: 20px;
        right: 20px;
        background: #dc3545;
        color: white;
        padding: 10px 15px;
        border-radius: 50%;
        font-weight: bold;
        font-size: 1.2rem;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        z-index: 2;
        transform: rotate(15deg);
    }
    .promo-card-item {
        transition: transform 0.2s;
        border: 1px solid #ffeeba;
    }
    .promo-card-item:hover {
        transform: scale(1.01);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        border-color: #ffc107;
    }
</style>

<main>
    <!-- Заголовок страницы -->
    <div class="promo-header py-5 mb-5">
        <div class="container text-center">
            <h1 class="display-4 fw-bold">Акции и Специальные предложения</h1>
            <p class="lead">Лучшие цены на тепло и уют в вашем доме</p>
        </div>
    </div>

    <div class="container mb-5">
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php" class="text-warning text-decoration-none">Главная</a></li>
                <li class="breadcrumb-item active" aria-current="page">Акции</li>
            </ol>
        </nav>

        <?php if (!empty($promotions)): ?>
            <div class="row g-4">
                <?php foreach ($promotions as $promo): ?>
                    <div class="col-lg-6" id="promo-<?= $promo['id']; ?>">
                        <div class="card h-100 shadow-sm promo-card-item overflow-hidden">
                            <div class="row g-0 h-100">
                                <!-- Изображение акции -->
                                <div class="col-md-5 position-relative">
                                    <?php if (!empty($promo['discount_percent'])): ?>
                                        <div class="discount-badge-lg">
                                            -<?= htmlspecialchars($promo['discount_percent']); ?>%
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($promo['image_path'])): ?>
                                        <img src="<?= htmlspecialchars($promo['image_path']); ?>" 
                                             class="img-fluid h-100 w-100" 
                                             alt="<?= htmlspecialchars($promo['title']); ?>"
                                             style="object-fit: cover; min-height: 250px;">
                                    <?php else: ?>
                                        <div class="bg-warning h-100 d-flex align-items-center justify-content-center" style="min-height: 250px;">
                                            <i class="bi bi-gift fs-1 text-white"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Контент акции -->
                                <div class="col-md-7">
                                    <div class="card-body d-flex flex-column h-100">
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="bi bi-calendar-event"></i> 
                                                Добавлено: <?= date('d.m.Y', strtotime($promo['created_at'])); ?>
                                            </small>
                                        </div>
                                        
                                        <h3 class="card-title h4 fw-bold mb-3">
                                            <?= htmlspecialchars($promo['title']); ?>
                                        </h3>
                                        
                                        <div class="card-text mb-4 flex-grow-1">
                                            <?= nl2br(htmlspecialchars($promo['description'])); ?>
                                        </div>

                                        <?php if (!empty($promo['end_date'])): ?>
                                            <div class="alert alert-warning py-2 mb-3">
                                                <i class="bi bi-stopwatch"></i> 
                                                Акция действует до: <strong><?= date('d.m.Y', strtotime($promo['end_date'])); ?></strong>
                                            </div>
                                        <?php endif; ?>

                                        <a href="catalog.php" class="btn btn-warning w-100 mt-auto fw-bold">
                                            Перейти к покупкам
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <div class="display-1 text-muted mb-3">😕</div>
                <h3>Акции пока отсутствуют</h3>
                <p class="text-muted">Загляните к нам позже, мы обязательно придумаем что-то выгодное!</p>
                <a href="index.php" class="btn btn-warning mt-3">Вернуться на главную</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
