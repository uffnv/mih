<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$videos = [];
try {
    $videos = getVideos(20);
} catch (Exception $e) {
    $videos = [];
}

$page_title = 'Видеообзоры оборудования — Территория Тепла';
require_once __DIR__ . '/includes/header.php';
?>

<main style="background-color: var(--bg-page, #f8fafc);">
    <!-- Hero заголовок -->
    <div class="py-5 text-white" style="background: linear-gradient(135deg, #0d0f12 0%, #1c2229 100%); border-bottom: 3px solid #FFDF1E;">
        <div class="container text-center py-4">
            <span class="badge bg-warning text-dark text-uppercase px-3 py-2 fw-bold mb-3 shadow-sm">
                <i class="bi bi-play-circle-fill me-1"></i> Практические обзоры
            </span>
            <h1 class="display-5 fw-bold text-uppercase mb-2 text-white">Видеообзоры <span class="text-warning">оборудования</span></h1>
            <p class="lead text-white-50 mb-0 max-w-600 mx-auto">
                Тестирование банных печей, твердотопливных котлов и правильный монтаж сэндвич-дымоходов от наших мастеров
            </p>
        </div>
    </div>

    <div class="container py-5">
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Главная</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Видеообзоры</li>
            </ol>
        </nav>

        <?php if (!empty($videos)): ?>
            <div class="row g-4">
                <?php foreach ($videos as $video): 
                    $vUrl = $video['video_url'] ?? $video['url'] ?? '';
                ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                            <div class="ratio ratio-16x9 bg-black">
                                <iframe src="<?= htmlspecialchars($vUrl) ?>" title="<?= htmlspecialchars($video['title']) ?>" allowfullscreen loading="lazy"></iframe>
                            </div>
                            <div class="card-body p-4 d-flex flex-column">
                                <span class="badge bg-warning-subtle text-dark border align-self-start mb-2 small fw-semibold">
                                    <i class="bi bi-youtube text-danger me-1"></i> Видеоинструкция
                                </span>
                                <h5 class="card-title fw-bold text-dark mb-2"><?= htmlspecialchars($video['title']) ?></h5>
                                <?php if (!empty($video['description'])): ?>
                                    <p class="card-text text-muted small mb-0 flex-grow-1">
                                        <?= htmlspecialchars($video['description']) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center text-muted bg-white">
                <i class="bi bi-camera-video display-4 mb-3 text-warning"></i>
                <h4 class="fw-bold text-dark">Видеообзоры готовятся</h4>
                <p class="mb-0">В ближайшее время мы опубликуем новые видео по монтажу и растопке печей.</p>
            </div>
        <?php endif; ?>

        <!-- Баннер подбора оборудования -->
        <div class="mt-5 p-4 p-md-5 rounded-4 shadow-sm text-white text-center text-md-start d-flex flex-column flex-md-row align-items-center justify-content-between gap-4" style="background: linear-gradient(135deg, #0d0f12 0%, #1c2229 100%); border-left: 4px solid #FFDF1E;">
            <div>
                <h4 class="fw-bold text-white mb-2">Нужна консультация по видео или расчет дымохода?</h4>
                <p class="text-white-50 mb-0 small">
                    Специалисты магазина-склада в Майме ответят на все вопросы и подберут надежную систему под ваш дом.
                </p>
            </div>
            <a href="contacts.php" class="btn btn-warning fw-bold px-4 py-3 rounded-pill text-nowrap shadow-sm">
                Связаться с инженером <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
