<?php
require_once '../includes/config.php';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM installer_reviews WHERE id = ?")->execute([$id]);
    header("Location: installer_reviews.php?msg=deleted");
    exit;
}

$page_title = 'Отзывы о мастерах';
require_once 'admin_header.php';

$stmt = $pdo->query("
    SELECT ir.*, u.username as user_name, u.first_name, u.last_name, i.first_name as installer_name, i.last_name as installer_surname
    FROM installer_reviews ir
    LEFT JOIN users u ON ir.user_id = u.id
    LEFT JOIN installers i ON ir.installer_id = i.id
    ORDER BY ir.created_at DESC
");
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h3 fw-bold text-dark mb-0">Отзывы о мастерах (<?= count($reviews) ?>)</h1>
        <small class="text-muted">Отзывы заказчиков о выполненных монтажных работах</small>
    </div>
</div>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
        <i class="bi bi-check-circle me-2"></i> Отзыв о мастере удален.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3">Мастер</th>
                        <th class="py-3">Клиент</th>
                        <th class="py-3">Оценка</th>
                        <th class="py-3">Отзыв</th>
                        <th class="py-3">Дата</th>
                        <th class="text-end pe-3 py-3">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($reviews)): ?>
                        <?php foreach ($reviews as $rev): 
                            $client = !empty($rev['first_name']) ? ($rev['first_name'] . ' ' . $rev['last_name']) : ($rev['user_name'] ?? 'Клиент');
                        ?>
                        <tr>
                            <td class="ps-3 fw-bold text-dark">
                                <?= htmlspecialchars(($rev['installer_name'] ?? '') . ' ' . ($rev['installer_surname'] ?? '')); ?>
                            </td>
                            <td><?= htmlspecialchars($client); ?></td>
                            <td>
                                <span class="badge bg-warning text-dark rounded-pill px-2 py-1">
                                    <?= $rev['rating']; ?> <i class="bi bi-star-fill"></i>
                                </span>
                            </td>
                            <td>
                                <div class="text-wrap small text-muted lh-sm" style="min-width: 200px; max-width: 450px;">
                                    <?= nl2br(htmlspecialchars($rev['comment'])); ?>
                                </div>
                            </td>
                            <td class="text-muted small text-nowrap"><?= date('d.m.Y', strtotime($rev['created_at'])); ?></td>
                            <td class="text-end pe-3">
                                <a href="installer_reviews.php?delete=<?= $rev['id']; ?>" class="btn btn-sm btn-outline-danger rounded-pill px-2" onclick="return confirm('Удалить отзыв о мастере?');" title="Удалить">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-chat-square-text display-4 opacity-50 d-block mb-2"></i>
                                Отзывов о мастерах пока нет
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
