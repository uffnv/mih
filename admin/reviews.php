<?php
require_once '../includes/config.php';
require_once 'admin_header.php';

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

$stmt = $pdo->query("
    SELECT r.*, u.username as user_name, p.name as product_name 
    FROM reviews r 
    LEFT JOIN users u ON r.user_id = u.id 
    LEFT JOIN products p ON r.product_id = p.id 
    ORDER BY r.created_at DESC
");
$reviews = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Отзывы о товарах</h1>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Автор</th>
                        <th>Товар</th>
                        <th>Оценка</th>
                        <th>Текст</th>
                        <th>Статус</th>
                        <th class="text-end">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reviews as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['user_name']); ?></td>
                        <td><?= htmlspecialchars($r['product_name']); ?></td>
                        <td class="text-nowrap text-warning">
                            <?= str_repeat('<i class="bi bi-star-fill"></i>', $r['rating']); ?>
                        </td>
                        <td>
                            <div class="text-wrap" style="min-width: 200px; max-width: 400px;">
                                <small class="text-muted fst-italic">
                                    <?= mb_strimwidth(htmlspecialchars($r['comment']), 0, 100, "..."); ?>
                                </small>
                            </div>
                        </td>
                        <td>
                            <?= $r['is_approved'] ? 
                                '<span class="badge bg-success">Опубликован</span>' : 
                                '<span class="badge bg-warning text-dark">На модерации</span>'; 
                            ?>
                        </td>
                        <td class="text-end text-nowrap">
                            <?php if (!$r['is_approved']): ?>
                                <a href="reviews.php?approve=<?= $r['id']; ?>" class="btn btn-sm btn-success me-1" title="Одобрить"><i class="bi bi-check-lg"></i></a>
                            <?php endif; ?>
                            <a href="reviews.php?delete=<?= $r['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить?');"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
