<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';

// Обработка формы: Отзыв о МАГАЗИНЕ (site_reviews)
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_site_review'])) {
    if (!isset($_SESSION['user_id'])) {
        $msg = "<div class='alert alert-danger'>Войдите, чтобы оставить отзыв.</div>";
    } else {
        $rating = (int)$_POST['rating'];
        $comment = trim($_POST['comment']);
        // Проверка
        if ($rating < 1) $rating = 1; 
        if ($rating > 5) $rating = 5;
        
        if (empty($comment)) {
            $msg = "<div class='alert alert-warning'>Напишите текст отзыва.</div>";
        } else {
            // ИСПРАВЛЕНИЕ: Явное указание колонок. 
            // Убедитесь, что в базе таблица site_reviews имеет эти поля.
            // Если поля created_at нет в запросе, MySQL может ругаться, если оно не NULL.
            // Добавляем user_id, rating, comment, created_at.
            try {
                $stmt = $pdo->prepare("INSERT INTO site_reviews (user_id, rating, comment, created_at) VALUES (?, ?, ?, NOW())");
                if ($stmt->execute([$_SESSION['user_id'], $rating, $comment])) {
                    $msg = "<div class='alert alert-success'>Спасибо! Ваш отзыв о магазине добавлен.</div>";
                } else {
                    $msg = "<div class='alert alert-danger'>Не удалось сохранить отзыв.</div>";
                }
            } catch (PDOException $e) {
                $msg = "<div class='alert alert-danger'>Ошибка БД: " . $e->getMessage() . "</div>";
            }
        }
    }
}

// Загружаем отзывы о МАГАЗИНЕ (site_reviews)
// Используем LEFT JOIN на случай, если данных пользователя нет
$stmt = $pdo->query("
    SELECT r.*, u.username, u.first_name 
    FROM site_reviews r 
    LEFT JOIN users u ON r.user_id = u.id 
    ORDER BY r.created_at DESC 
    LIMIT 20
");
$site_reviews_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Отзывы о магазине — Михаил Потапыч';
require_once __DIR__ . '/includes/header.php';
?>

<main class="bg-light">
    <!-- Hero заголовок -->
    <div class="hero-gradient text-white py-5">
        <div class="container text-center py-3">
            <h1 class="display-5 fw-bold text-uppercase mb-2">Отзывы о <span class="text-warning">Магазине</span></h1>
            <p class="lead text-white-50 mb-0">Мнения и впечатления клиентов о нашей продукции и сервисе</p>
        </div>
    </div>

    <div class="container py-5">
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="index.php">Главная</a></li>
                <li class="breadcrumb-item active" aria-current="page">Отзывы</li>
            </ol>
        </nav>

        <div class="row g-4 g-lg-5">
            <!-- Левая колонка: Форма -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white sticky-lg-top" style="top: 85px; z-index: 10;">
                    <h5 class="fw-bold mb-2 text-dark border-bottom pb-2">
                        <i class="bi bi-pencil-square text-warning me-2"></i>Оставить отзыв
                    </h5>
                    <p class="small text-muted mb-3">Поделитесь впечатлениями о покупке или качестве обслуживания.</p>
                    
                    <?= $msg ?>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted text-uppercase">Ваша оценка:</label>
                                <select name="rating" class="form-select rounded-pill bg-light">
                                    <option value="5" selected>★★★★★ 5 - Отлично</option>
                                    <option value="4">★★★★☆ 4 - Хорошо</option>
                                    <option value="3">★★★☆☆ 3 - Нормально</option>
                                    <option value="2">★★☆☆☆ 2 - Плохо</option>
                                    <option value="1">★☆☆☆☆ 1 - Ужасно</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted text-uppercase">Комментарий:</label>
                                <textarea name="comment" class="form-control bg-light" rows="4" required placeholder="Расскажите о вашем опыте..."></textarea>
                            </div>
                            <button type="submit" name="submit_site_review" class="btn btn-warning w-100 fw-bold rounded-pill shadow-sm">
                                <i class="bi bi-send-fill me-1"></i> Отправить отзыв
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="p-3 bg-light rounded-3 text-center border">
                            <p class="mb-2 small text-muted">Хотите поделиться отзывом?</p>
                            <a href="login.php" class="btn btn-warning btn-sm fw-bold rounded-pill shadow-sm px-3">Войти на сайт</a>
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
                            $name = $review['first_name'] ?: ($review['username'] ?: 'Пользователь');
                            $initial = mb_substr($name, 0, 1);
                        ?>
                            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white card-hover">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle bg-dark text-warning fw-bold d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px; font-size: 1.1rem;">
                                            <?= $initial ?>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($name) ?></h6>
                                            <small class="text-muted"><?= date('d.m.Y', strtotime($review['created_at'])) ?></small>
                                        </div>
                                    </div>
                                    <div class="text-warning small">
                                        <?php 
                                            for ($i = 1; $i <= 5; $i++) {
                                                echo $i <= $review['rating'] ? '<i class="bi bi-star-fill"></i> ' : '<i class="bi bi-star"></i> ';
                                            }
                                        ?>
                                    </div>
                                </div>
                                <p class="mb-0 text-muted small lh-lg"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
