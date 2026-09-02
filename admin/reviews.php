<?php
require_once '../includes/config.php';

// Одобрение / Удаление
if (isset($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    $pdo->prepare("UPDATE reviews SET is_approved = 1 WHERE id = ?")->execute([$id]);
    header("Location: reviews.php");
    exit;
}
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM reviews WHERE id = ?")->execute([$id]);
    header("Location: reviews.php");
    exit;
}

$page_title = 'Отзывы о товарах';
require_once 'admin_header.php';

$stmt = $pdo->query("
    SELECT r.*, u.username as user_name, u.first_name, u.last_name, p.name as product_name 
    FROM reviews r 
    LEFT JOIN users u ON r.user_id = u.id 
    LEFT JOIN products p ON r.product_id = p.id 
    ORDER BY r.created_at DESC
");
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h3 fw-bold text-dark mb-0">Отзывы о товарах (<?= count($reviews) ?>)</h1>
        <small class="text-muted">Модерация отзывов покупателей к товарам каталога</small>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3">Автор</th>
                        <th class="py-3">Товар</th>
                        <th class="py-3">Оценка</th>
                        <th class="py-3">Отзыв</th>
                        <th class="py-3">Дата</th>
                        <th class="py-3">Статус</th>
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
                                <div class="fw-bold text-dark"><?= htmlspecialchars($r['product_name'] ?? 'Товар #' . ($r['product_id'] ?? '')); ?></div>
                            </td>
                            <td class="text-nowrap text-warning small">
                                <?php 
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo $i <= $r['rating'] ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>';
                                    }
                                ?>
                            </td>
                            <td>
                                <div class="text-wrap small text-muted lh-sm" style="min-width: 200px; max-width: 400px;">
                                    <?= nl2br(htmlspecialchars($r['comment'])); ?>
                                </div>
                            </td>
                            <td class="text-muted small text-nowrap"><?= date('d.m.Y H:i', strtotime($r['created_at'])); ?></td>
                            <td>
                                <?= $r['is_approved'] ? 
                                    '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2">Опубликован</span>' : 
                                    '<span class="badge bg-warning text-dark rounded-pill px-2">На модерации</span>'; 
                                ?>
                            </td>
                            <td class="text-end pe-3 text-nowrap">
                                <?php if (!$r['is_approved']): ?>
                                    <a href="reviews.php?approve=<?= $r['id']; ?>" class="btn btn-sm btn-success rounded-pill px-3 me-1" title="Одобрить">
                                        <i class="bi bi-check-lg me-1"></i> Одобрить
                                    </a>
                                <?php endif; ?>
                                <a href="reviews.php?delete=<?= $r['id']; ?>" class="btn btn-sm btn-outline-danger rounded-pill px-2" onclick="return confirm('Удалить отзыв?');" title="Удалить">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-chat-square-text display-4 opacity-50 d-block mb-2"></i>
                                Отзывов о товарах пока нет
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
