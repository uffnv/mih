<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$msg = '';

// Обработка отзыва о магазине
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_site_review'])) {
    if (!isset($_SESSION['user_id'])) {
        $msg = "<div class='alert alert-danger rounded-4 shadow-sm'>Для отправки отзыва войдите в личный кабинет.</div>";
    } else {
        $rating = (int)($_POST['rating'] ?? 5);
        $comment = trim($_POST['comment'] ?? '');
        $consent = isset($_POST['consent_pd']);

        if ($rating < 1) $rating = 1; 
        if ($rating > 5) $rating = 5;
        
        if (empty($comment)) {
            $msg = "<div class='alert alert-warning rounded-4 shadow-sm'>Пожалуйста, напишите текст отзыва.</div>";
        } elseif (!$consent) {
            $msg = "<div class='alert alert-warning rounded-4 shadow-sm'>Необходимо согласие на обработку персональных данных.</div>";
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO site_reviews (user_id, rating, comment, is_approved, created_at) VALUES (?, ?, ?, 1, NOW())");
                if ($stmt->execute([$_SESSION['user_id'], $rating, $comment])) {
                    $msg = "<div class='alert alert-success rounded-4 shadow-sm'>Спасибо! Ваш отзыв опубликован.</div>";
                } else {
                    $msg = "<div class='alert alert-danger rounded-4 shadow-sm'>Не удалось сохранить отзыв.</div>";
                }
            } catch (PDOException $e) {
                $msg = "<div class='alert alert-danger rounded-4 shadow-sm'>Ошибка базы данных: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
    }
}

// Загружаем отзывы о магазине
$stmt = $pdo->query("
    SELECT r.*, u.username, u.first_name, u.last_name 
    FROM site_reviews r 
    LEFT JOIN users u ON r.user_id = u.id 
    WHERE r.is_approved = 1
    ORDER BY r.created_at DESC 
    LIMIT 50
");
$site_reviews_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Расчет статистики рейтинга
$totalReviews = count($site_reviews_list);
$avgScore = 0;
$distribution = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];

if ($totalReviews > 0) {
    $sum = 0;
    foreach ($site_reviews_list as $sr) {
        $r = (int)$sr['rating'];
        $sum += $r;
        if (isset($distribution[$r])) {
            $distribution[$r]++;
        }
    }
    $avgScore = round($sum / $totalReviews, 1);
}

$page_title = 'Отзывы покупателей — Территория Тепла';
require_once __DIR__ . '/includes/header.php';
?>

<main style="background-color: var(--bg-page, #f8fafc);">
    <!-- Hero заголовок -->
    <div class="py-5 text-white" style="background: linear-gradient(135deg, #181c20 0%, #2b3238 100%); border-bottom: 3px solid var(--bs-warning, #f59e0b);">
        <div class="container text-center py-4">
            <span class="badge bg-warning text-dark text-uppercase px-3 py-2 fw-bold mb-3 shadow-sm">
                <i class="bi bi-star-fill me-1"></i> Репутация и доверие
            </span>
            <h1 class="display-5 fw-bold text-uppercase mb-2">Отзывы о <span class="text-warning">Магазине</span></h1>
            <p class="lead text-white-50 mb-0 max-w-600 mx-auto">
                Впечатления наших клиентов о качестве печей, котлов, скорости отгрузки и монтаже в Майме
            </p>
        </div>
    </div>

    <div class="container py-5">
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Главная</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Отзывы</li>
            </ol>
        </nav>

        <?= $msg ?>

        <div class="row g-4 g-lg-5">
            <!-- Левая колонка: Статистика и Форма отзыва -->
            <div class="col-lg-4">
                <!-- Сводка рейтинга -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4 text-center">
                    <h5 class="fw-bold text-dark mb-1">Рейтинг магазина</h5>
                    <div class="display-3 fw-bold text-dark my-2">
                        <?= $avgScore > 0 ? $avgScore : '5.0' ?>
                    </div>
                    <div class="text-warning fs-5 mb-2">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <?= $i <= round($avgScore ?: 5) ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>' ?>
                        <?php endfor; ?>
                    </div>
                    <div class="small text-muted mb-3">
                        Всего оценок: <strong><?= $totalReviews ?></strong>
                    </div>

                    <!-- Полосы распределения -->
                    <div class="text-start small">
                        <?php for ($star = 5; $star >= 1; $star--): 
                            $cnt = $distribution[$star] ?? 0;
                            $pct = $totalReviews > 0 ? round(($cnt / $totalReviews) * 100) : 0;
                        ?>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="text-nowrap" style="width: 25px;"><?= $star ?>★</span>
                                <div class="progress flex-grow-1" style="height: 6px;">
                                    <div class="progress-bar bg-warning" style="width: <?= $pct ?>%;"></div>
                                </div>
                                <span class="text-muted text-end" style="width: 25px;"><?= $cnt ?></span>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- Форма отправки отзыва -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white sticky-lg-top" style="top: 85px; z-index: 10;">
                    <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                        <i class="bi bi-pencil-square text-warning me-2"></i> Оставить свой отзыв
                    </h5>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form method="POST">
                            <input type="hidden" name="submit_site_review" value="1">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Ваша оценка *</label>
                                <select name="rating" class="form-select" required>
                                    <option value="5" selected>★★★★★ (5 — Отлично)</option>
                                    <option value="4">★★★★☆ (4 — Хорошо)</option>
                                    <option value="3">★★★☆☆ (3 — Нормально)</option>
                                    <option value="2">★★☆☆☆ (2 — Плохо)</option>
                                    <option value="1">★☆☆☆☆ (1 — Ужасно)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Ваш отзыв *</label>
                                <textarea name="comment" class="form-control" rows="4" placeholder="Расскажите о купленном товаре, доставке или консультации менеджера..." required></textarea>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="consent_pd" id="rev_pd" required checked>
                                <label class="form-check-label small text-muted" for="rev_pd">
                                    Согласен на обработку персональных данных (152-ФЗ)
                                </label>
                            </div>
                            <button type="submit" class="btn btn-warning w-100 fw-bold rounded-pill shadow-sm py-2">
                                Опубликовать отзыв
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <i class="bi bi-person-lock fs-1 text-muted opacity-50 mb-2 d-block"></i>
                            <p class="small text-muted mb-3">Чтобы оставить отзыв, пожалуйста, авторизуйтесь на сайте.</p>
                            <a href="login.php" class="btn btn-warning fw-bold rounded-pill px-4 shadow-sm w-100">
                                Войти в аккаунт
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Правая колонка: Список отзывов -->
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="h4 fw-bold text-dark mb-0">Отзывы покупателей</h3>
                    <span class="badge bg-dark rounded-pill px-3 py-2"><?= $totalReviews ?> отзывов</span>
                </div>

                <?php if (!empty($site_reviews_list)): ?>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($site_reviews_list as $item): 
                            $name = !empty($item['first_name']) ? ($item['first_name'] . ' ' . ($item['last_name'] ?? '')) : ($item['username'] ?? 'Покупатель');
                        ?>
                            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-warning text-dark fw-bold d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                                            <?= mb_substr($name, 0, 1) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($name) ?></div>
                                            <div class="text-muted small" style="font-size: 0.75rem;">
                                                <?= date('d.m.Y H:i', strtotime($item['created_at'])) ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-warning">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <?= $i <= $item['rating'] ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>' ?>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <p class="text-muted mb-0 small lh-lg">
                                    <?= nl2br(htmlspecialchars($item['comment'])) ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
                        <i class="bi bi-chat-heart display-3 text-muted opacity-50 mb-3"></i>
                        <h4 class="fw-bold">Отзывов пока нет</h4>
                        <p class="text-muted mb-0">Будьте первым, кто поделится своим мнением о нашей работе!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
