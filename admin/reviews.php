<?php
require_once '../includes/config.php';

// Одобрение / Удаление отзывов о товарах
if (isset($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    $pdo->prepare("UPDATE product_reviews SET is_approved = 1 WHERE id = ?")->execute([$id]);
    header("Location: reviews.php?msg=approved");
    exit;
}
if (isset($_GET['reject'])) {
    $id = (int)$_GET['reject'];
    $pdo->prepare("UPDATE product_reviews SET is_approved = 0 WHERE id = ?")->execute([$id]);
    header("Location: reviews.php?msg=rejected");
    exit;
}
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM product_reviews WHERE id = ?")->execute([$id]);
    header("Location: reviews.php?msg=deleted");
    exit;
}

$page_title = 'Отзывы о товарах';
require_once 'admin_header.php';

$filter = $_GET['filter'] ?? 'all';
$sql = "
    SELECT r.*, u.username as user_name, u.first_name, u.last_name, p.name as product_name 
    FROM product_reviews r 
    LEFT JOIN users u ON r.user_id = u.id 
    LEFT JOIN products p ON r.product_id = p.id 
";
if ($filter === 'pending') {
    $sql .= " WHERE r.is_approved = 0 ";
} elseif ($filter === 'approved') {
    $sql .= " WHERE r.is_approved = 1 ";
}
$sql .= " ORDER BY r.created_at DESC";

$stmt = $pdo->query($sql);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h3 fw-bold text-dark mb-0">Отзывы о товарах (<?= count($reviews) ?>)</h1>
        <small class="text-muted">Модерация оценок и отзывов покупателей на продукцию</small>
    </div>
</div>

<div class="d-flex gap-2 mb-3">
    <a href="reviews.php?filter=all" class="btn btn-sm rounded-pill <?= $filter === 'all' ? 'btn-dark fw-bold' : 'btn-outline-secondary' ?>">Все отзывы</a>
    <a href="reviews.php?filter=pending" class="btn btn-sm rounded-pill <?= $filter === 'pending' ? 'btn-warning text-dark fw-bold' : 'btn-outline-warning text-dark' ?>">На модерации</a>
    <a href="reviews.php?filter=approved" class="btn btn-sm rounded-pill <?= $filter === 'approved' ? 'btn-success fw-bold' : 'btn-outline-success' ?>">Одобренные</a>
</div>

<div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3">Клиент</th>
                        <th class="py-3">Товар</th>
                        <th class="py-3">Оценка</th>
                        <th class="py-3" style="max-width: 320px;">Текст отзыва</th>
                        <th class="py-3">Статус</th>
                        <th class="py-3">Дата</th>
                        <th class="text-end pe-3 py-3">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($reviews)): ?>
                        <?php foreach ($reviews as $r): 
                            $author = !empty($r['first_name']) ? ($r['first_name'] . ' ' . $r['last_name']) : ($r['user_name'] ?? 'Покупатель');
                        ?>
                        <tr>
                            <td class="ps-3 fw-bold text-dark"><?= htmlspecialchars($author); ?></td>
                            <td>
                                <a href="../product.php?id=<?= $r['product_id'] ?>" target="_blank" class="text-decoration-none fw-medium text-dark">
                                    <?= htmlspecialchars($r['product_name'] ?? 'Товар #' . $r['product_id']) ?>
                                    <i class="bi bi-box-arrow-up-right small text-muted ms-1"></i>
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-warning text-dark">
                                    <?= (int)$r['rating'] ?> <i class="bi bi-star-fill"></i>
                                </span>
                            </td>
                            <td class="small text-muted" style="max-width: 320px;">
                                <?= nl2br(htmlspecialchars($r['comment'] ?? $r['review_text'] ?? '')) ?>
                            </td>
                            <td>
                                <?php if ($r['is_approved']): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Одобрен</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">На модерации</span>
                                <?php endif; ?>
                            </td>
                            <td class="small text-muted">
                                <?= date('d.m.Y H:i', strtotime($r['created_at'])) ?>
                            </td>
                            <td class="text-end pe-3 text-nowrap">
                                <?php if (!$r['is_approved']): ?>
                                    <a href="reviews.php?approve=<?= $r['id'] ?>" class="btn btn-sm btn-success rounded-pill px-2 me-1" title="Одобрить">
                                        <i class="bi bi-check-lg"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="reviews.php?reject=<?= $r['id'] ?>" class="btn btn-sm btn-outline-warning rounded-pill px-2 me-1" title="Снять с публикации">
                                        <i class="bi bi-dash-circle"></i>
                                    </a>
                                <?php endif; ?>
                                <a href="reviews.php?delete=<?= $r['id'] ?>" class="btn btn-sm btn-outline-danger rounded-pill px-2" onclick="return confirm('Удалить отзыв безвозвратно?');" title="Удалить">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Отзывов не найдено</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
