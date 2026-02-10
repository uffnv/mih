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

?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">Отзывы о нашем магазине</h1>
        <p class="text-muted">Мнение клиентов о нашей работе и сервисе</p>
    </div>

    <div class="row g-5">
        <!-- Левая колонка: Форма -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Оставить отзыв</h5>
                    <p class="small text-muted mb-4">Поделитесь впечатлениями о работе нашего интернет-магазина.</p>
                    
                    <?= $msg ?>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Ваша оценка</label>
                                <select name="rating" class="form-select">
                                    <option value="5" selected>★★★★★ (Отлично)</option>
                                    <option value="4">★★★★☆ (Хорошо)</option>
                                    <option value="3">★★★☆☆ (Нормально)</option>
                                    <option value="2">★★☆☆☆ (Плохо)</option>
                                    <option value="1">★☆☆☆☆ (Ужасно)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Комментарий</label>
                                <textarea name="comment" class="form-control" rows="4" required placeholder="Напишите пару слов..."></textarea>
                            </div>
                            <button type="submit" name="submit_site_review" class="btn btn-warning w-100 fw-bold">Отправить отзыв</button>
                        </form>
                    <?php else: ?>
                        <div class="text-center py-3 bg-light rounded">
                            <p class="mb-2 text-muted">Хотите написать отзыв?</p>
                            <a href="auth.php?redirect=reviews" class="btn btn-dark btn-sm">Войти на сайт</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Правая колонка: Список -->
        <div class="col-lg-8">
            <?php if (empty($site_reviews_list)): ?>
                <div class="text-center py-5 text-muted border rounded bg-light">
                    <i class="bi bi-chat-square-text display-4 mb-3"></i>
                    <p class="mb-0">Отзывов пока нет. Будьте первым!</p>
                </div>
            <?php else: ?>
                <?php foreach ($site_reviews_list as $review): ?>
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3 fw-bold text-secondary" style="width: 40px; height: 40px;">
                                        <?= mb_substr($review['first_name'] ?: ($review['username'] ?: 'U'), 0, 1) ?>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0"><?= htmlspecialchars($review['first_name'] ?: ($review['username'] ?: 'Пользователь')) ?></h6>
                                        <small class="text-muted"><?= date('d.m.Y', strtotime($review['created_at'])) ?></small>
                                    </div>
                                </div>
                                <div class="text-warning">
                                    <?php for($i=0; $i<5; $i++) echo $i < $review['rating'] ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>'; ?>
                                </div>
                            </div>
                            <p class="mb-0 text-dark opacity-75"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
