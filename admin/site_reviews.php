<?php
require_once __DIR__ . '/../includes/config.php';

// Удаление отзыва
if (isset($_POST['delete_review']) && isset($_POST['review_id'])) {
    $id = (int)$_POST['review_id'];
    $stmt = $pdo->prepare("DELETE FROM site_reviews WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: site_reviews.php?msg=deleted");
    exit;
}

$page_title = 'Отзывы о магазине';
require_once __DIR__ . '/admin_header.php';

// Получение списка отзывов
$stmt = $pdo->query("
    SELECT r.*, u.username, u.first_name, u.last_name, u.email 
    FROM site_reviews r 
    LEFT JOIN users u ON r.user_id = u.id 
    ORDER BY r.created_at DESC
");
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h3 fw-bold text-dark mb-0">Отзывы о магазине (<?= count($reviews) ?>)</h1>
        <small class="text-muted">Мнения клиентов о качестве обслуживания и сервисе магазина</small>
    </div>
</div>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
        <i class="bi bi-check-circle me-2"></i> Отзыв успешно удален.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3" style="width: 50px;">ID</th>
                        <th class="py-3">Пользователь</th>
                        <th class="py-3">Оценка</th>
                        <th class="py-3">Текст отзыва</th>
                        <th class="py-3">Дата</th>
                        <th class="text-end pe-3 py-3">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($reviews)): ?>
                        <?php foreach ($reviews as $review): 
                            $name = !empty($review['first_name']) ? ($review['first_name'] . ' ' . $review['last_name']) : ($review['username'] ?? 'Пользователь');
                        ?>
                            <tr>
                                <td class="ps-3 fw-bold text-dark">#<?= $review['id'] ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($name) ?></div>
                                    <small class="text-muted"><?= htmlspecialchars($review['email'] ?? '') ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark rounded-pill px-2 py-1">
                                        <?= $review['rating'] ?> <i class="bi bi-star-fill"></i>
                                    </span>
                                </td>
                                <td>
                                    <div class="text-wrap small text-muted lh-sm" style="min-width: 200px; max-width: 450px;">
                                        <?= nl2br(htmlspecialchars($review['comment'])) ?>
                                    </div>
                                </td>
                                <td class="text-muted small text-nowrap"><?= date('d.m.Y H:i', strtotime($review['created_at'])) ?></td>
                                <td class="text-end pe-3">
                                    <form method="POST" onsubmit="return confirm('Удалить этот отзыв?');" class="d-inline">
                                        <input type="hidden" name="review_id" value="<?= $review['id'] ?>">
                                        <button type="submit" name="delete_review" class="btn btn-sm btn-outline-danger rounded-pill px-2" title="Удалить">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-chat-square-text display-4 opacity-50 d-block mb-2"></i>
                                Отзывов о магазине пока нет
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/admin_footer.php'; ?>
