<?php
require_once '../includes/config.php';
require_once 'admin_header.php';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM installer_reviews WHERE id = ?")->execute([$id]);
    // Тут можно добавить пересчет рейтинга, если нужно
    header("Location: installer_reviews.php");
    exit;
}

$stmt = $pdo->query("
    SELECT ir.*, u.username as user_name, i.first_name as installer_name, i.last_name as installer_surname
    FROM installer_reviews ir
    LEFT JOIN users u ON ir.user_id = u.id
    LEFT JOIN installers i ON ir.installer_id = i.id
    ORDER BY ir.created_at DESC
");
$reviews = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Отзывы о мастерах</h1>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Мастер</th>
                        <th>Клиент</th>
                        <th>Оценка</th>
                        <th>Отзыв</th>
                        <th>Дата</th>
                        <th class="text-end">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reviews as $rev): ?>
                    <tr>
                        <td><?= htmlspecialchars($rev['installer_name'] . ' ' . $rev['installer_surname']); ?></td>
                        <td><?= htmlspecialchars($rev['user_name']); ?></td>
                        <td><span class="text-warning fw-bold"><i class="bi bi-star-fill"></i> <?= $rev['rating']; ?></span></td>
                        <td>
                             <div class="text-wrap" style="min-width: 200px; max-width: 400px;">
                                <small class="text-muted fst-italic">
                                    <?= mb_strimwidth(htmlspecialchars($rev['comment']), 0, 100, "..."); ?>
                                </small>
                            </div>
                        </td>
                        <td class="text-nowrap"><?= date('d.m.Y', strtotime($rev['created_at'])); ?></td>
                        <td class="text-end">
                            <a href="installer_reviews.php?delete=<?= $rev['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить отзыв?');"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
