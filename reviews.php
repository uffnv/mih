<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$msg = '';

// Обработка формы: Отзыв о магазине (site_reviews)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_site_review'])) {
    if (!isset($_SESSION['user_id'])) {
        $msg = "<div class='alert alert-danger rounded-4 shadow-sm'>Для отправки отзыва необходимо <a href='login.php' class='alert-link text-decoration-underline'>войти в личный кабинет</a>.</div>";
    } else {
        $rating = (int)($_POST['rating'] ?? 5);
        $comment = trim($_POST['comment'] ?? '');
        $consent = isset($_POST['consent_pd']);

        if ($rating < 1) $rating = 1; 
        if ($rating > 5) $rating = 5;
        
        if (empty($comment)) {
            $msg = "<div class='alert alert-warning rounded-4 shadow-sm'>Пожалуйста, напишите текст отзыва.</div>";
        } elseif (!$consent) {
            $msg = "<div class='alert alert-warning rounded-4 shadow-sm'>Необходимо согласие на обработку персональных данных (152-ФЗ).</div>";
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO site_reviews (user_id, rating, comment, created_at) VALUES (?, ?, ?, NOW())");
                if ($stmt->execute([$_SESSION['user_id'], $rating, $comment])) {
                    $msg = "<div class='alert alert-success rounded-4 shadow-sm'>Спасибо! Ваш отзыв о магазине успешно опубликован.</div>";
                } else {
                    $msg = "<div class='alert alert-danger rounded-4 shadow-sm'>Не удалось сохранить отзыв. Попробуйте еще раз.</div>";
                }
            } catch (PDOException $e) {
                $msg = "<div class='alert alert-danger rounded-4 shadow-sm'>Ошибка базы данных: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
    }
}

// Загружаем отзывы о магазине (site_reviews)
$site_reviews_list = [];
try {
    $stmt = $pdo->query("
        SELECT r.*, u.username, u.first_name, u.last_name 
        FROM site_reviews r 
        LEFT JOIN users u ON r.user_id = u.id 
        ORDER BY r.created_at DESC 
        LIMIT 50
    ");
    $site_reviews_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $site_reviews_list = [];
}

// Защита от пустой базы: если отзывов еще нет, наполняем базовыми
if (empty($site_reviews_list)) {
    try {
        $seed_file = __DIR__ . '/database_seed.sql';
        if (file_exists($seed_file)) {
            $seed_sql = file_get_contents($seed_file);
            $pdo->exec($seed_sql);
            $stmt = $pdo->query("
                SELECT r.*, u.username, u.first_name, u.last_name 
                FROM site_reviews r 
                LEFT JOIN users u ON r.user_id = u.id 
                ORDER BY r.created_at DESC 
                LIMIT 50
            ");
            $site_reviews_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (Exception $e) {}
}

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

<main class="py-4 py-lg-5" style="background-color: var(--bg-page, #f8fafc);">
    <div class="container">

        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Главная</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Отзывы</li>
            </ol>
        </nav>

        <div class="text-center mb-5">
            <span class="badge bg-warning text-dark text-uppercase px-3 py-2 fw-bold mb-2 shadow-sm">
                <i class="bi bi-star-fill me-1"></i> Реальный опыт покупателей
            </span>
            <h1 class="display-6 fw-bold text-dark text-uppercase mb-2">Отзывы о магазине <span class="text-warning">«Территория Тепла»</span></h1>
            <p class="text-muted max-w-600 mx-auto" style="max-width: 650px;">
                Мнения наших клиентов об отопительном оборудовании, печах, котлах и работе сертифицированных специалистов в Республике Алтай.
            </p>
        </div>

        <?php if ($msg): ?>
            <div class="mb-4">
                <?= $msg ?>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Левая колонка: Рейтинг и форма отправки -->
            <div class="col-lg-4">
                <!-- Карточка рейтинга -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                    <h5 class="fw-bold mb-3 text-dark">Общий рейтинг</h5>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="display-4 fw-bold text-warning lh-1"><?= $avgScore > 0 ? $avgScore : '5.0' ?></div>
                        <div>
                            <div class="text-warning small mb-1">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="bi bi-star-fill"></i>
                                <?php endfor; ?>
                            </div>
                            <small class="text-muted">На основе <?= $totalReviews ?> отзывов</small>
                        </div>
                    </div>

                    <!-- Шкала распределения -->
                    <div class="d-flex flex-column gap-2 small">
                        <?php for ($star = 5; $star >= 1; $star--): 
                            $count = $distribution[$star] ?? 0;
                            $percent = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : ($star === 5 ? 100 : 0);
                        ?>
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-nowrap" style="width: 25px;"><?= $star ?> <i class="bi bi-star-fill text-warning"></i></span>
                                <div class="progress flex-grow-1" style="height: 6px;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $percent ?>%"></div>
                                </div>
                                <span class="text-muted small" style="width: 35px; text-align: right;"><?= $count ?></span>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- Карточка добавления отзыва -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h5 class="fw-bold mb-3 text-dark">Оставить отзыв</h5>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form method="POST" action="reviews.php">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Ваша оценка *</label>
                                <select name="rating" class="form-select" required>
                                    <option value="5" selected>⭐⭐⭐⭐⭐ — Отлично (5)</option>
                                    <option value="4">⭐⭐⭐⭐ — Хорошо (4)</option>
                                    <option value="3">⭐⭐⭐ — Нормально (3)</option>
                                    <option value="2">⭐⭐ — Плохо (2)</option>
                                    <option value="1">⭐ — Ужасно (1)</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Ваш отзыв *</label>
                                <textarea name="comment" class="form-control" rows="4" required placeholder="Расскажите о покупке, монтаже или работе оборудования..."></textarea>
                            </div>

                            <div class="form-check small text-muted mb-3">
                                <input class="form-check-input" type="checkbox" name="consent_pd" id="consent_rev" required checked>
                                <label class="form-check-label" for="consent_rev">
                                    Согласие на публикацию и обработку данных (<a href="privacy.php" target="_blank" class="text-warning">152-ФЗ</a>)
                                </label>
                            </div>

                            <button type="submit" name="submit_site_review" class="btn btn-warning w-100 fw-bold rounded-pill text-dark shadow-sm">
                                <i class="bi bi-send-fill me-1"></i> Опубликовать отзыв
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="p-3 bg-light rounded-3 text-center border">
                            <i class="bi bi-person-lock display-6 text-warning mb-2 d-block"></i>
                            <p class="mb-2 small text-muted">Чтобы оставить отзыв, пожалуйста, авторизуйтесь в личном кабинете.</p>
                            <a href="login.php" class="btn btn-warning btn-sm fw-bold rounded-pill shadow-sm px-4 text-dark">
                                Войти на сайт
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Правая колонка: Список отзывов -->
            <div class="col-lg-8">
                <?php if (empty($site_reviews_list)): ?>
                    <div class="card border-0 shadow-sm rounded-4 text-center py-5 px-3 bg-white">
                        <i class="bi bi-chat-square-text-fill display-3 text-muted opacity-50 mb-3"></i>
                        <h4 class="fw-bold">Отзывов пока нет</h4>
                        <p class="text-muted mb-0">Будьте первым, кто оставит отзыв о нашем магазине!</p>
                    </div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($site_reviews_list as $review): 
                            $name = trim(($review['first_name'] ?? '') . ' ' . ($review['last_name'] ?? ''));
                            if (empty($name)) {
                                $name = !empty($review['username']) ? $review['username'] : 'Покупатель';
                            }
                            $initial = mb_substr($name, 0, 1);
                            $revDate = !empty($review['created_at']) ? date('d.m.Y', strtotime($review['created_at'])) : date('d.m.Y');
                        ?>
                            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white" style="transition: transform 0.2s ease;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle bg-dark text-warning fw-bold d-flex align-items-center justify-content-center border border-warning shadow-sm" style="width: 46px; height: 46px; font-size: 1.15rem;">
                                            <?= htmlspecialchars($initial) ?>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($name) ?></h6>
                                            <small class="text-muted"><?= $revDate ?></small>
                                        </div>
                                    </div>
                                    <div class="text-warning small">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <?= $i <= (int)$review['rating'] ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>' ?>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <p class="mb-0 text-muted small lh-lg">
                                    <?= nl2br(htmlspecialchars($review['comment'] ?? '')) ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
