<?php
// admin/site_reviews.php
require_once __DIR__ . '/admin_header.php';
require_once __DIR__ . '/../includes/config.php';

// Удаление отзыва
if (isset($_POST['delete_review'])) {
    $id = (int)$_POST['review_id'];
    $stmt = $pdo->prepare("DELETE FROM site_reviews WHERE id = ?");
    if ($stmt->execute([$id])) {
        echo "<div class='alert alert-success'>Отзыв удален.</div>";
    } else {
        echo "<div class='alert alert-danger'>Ошибка удаления.</div>";
    }
}

// Получение списка отзывов
$stmt = $pdo->query("
    SELECT r.*, u.username, u.email 
    FROM site_reviews r 
    JOIN users u ON r.user_id = u.id 
    ORDER BY r.created_at DESC
");
$reviews = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Отзывы об организации</h1>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Пользователь</th>
                <th>Оценка</th>
                <th>Комментарий</th>
                <th>Дата</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reviews as $review): ?>
                <tr>
                    <td><?= $review['id'] ?></td>
                    <td>
                        <strong><?= htmlspecialchars($review['username']) ?></strong><br>
                        <small class="text-muted"><?= htmlspecialchars($review['email']) ?></small>
                    </td>
                    <td>
                        <span class="badge bg-warning text-dark">
                            <?= $review['rating'] ?> <i class="bi bi-star-fill"></i>
                        </span>
                    </td>
                    <td><?= nl2br(htmlspecialchars($review['comment'])) ?></td>
                    <td><?= date('d.m.Y H:i', strtotime($review['created_at'])) ?></td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Удалить этот отзыв?');">
                            <input type="hidden" name="review_id" value="<?= $review['id'] ?>">
                            <button type="submit" name="delete_review" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <?php if(empty($reviews)): ?>
        <p class="text-center text-muted my-5">Отзывов пока нет.</p>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/admin_footer.php'; ?>
