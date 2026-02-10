<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/admin_header.php';

// Обработка удаления
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM promotions WHERE id = ?");
        $stmt->execute([$delete_id]);
        echo '<div class="alert alert-success">Акция успешно удалена!</div>';
    } catch (Exception $e) {
        echo '<div class="alert alert-danger">Ошибка удаления: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}

// Получение списка акций
try {
    $stmt = $pdo->query("SELECT * FROM promotions ORDER BY is_active DESC, sort_order ASC, created_at DESC");
    $promotions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $promotions = [];
    $error = "Ошибка загрузки списка: " . $e->getMessage();
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Акции и Новости</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="promotion_edit.php" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Добавить акцию
        </a>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th scope="col" width="50">#</th>
                <th scope="col" width="100">Фото</th>
                <th scope="col">Название</th>
                <th scope="col">Даты действия</th>
                <th scope="col">Скидка</th>
                <th scope="col">Статус</th>
                <th scope="col" width="150">Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($promotions)): ?>
                <?php foreach ($promotions as $promo): ?>
                    <tr>
                        <td><?= $promo['id'] ?></td>
                        <td>
                            <?php if (!empty($promo['image_path'])): ?>
                                <img src="<?= htmlspecialchars($promo['image_path']) ?>" alt="img" style="height: 50px; width: 50px; object-fit: cover;" class="rounded">
                            <?php else: ?>
                                <span class="text-muted small">Нет фото</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($promo['title']) ?></strong>
                            <div class="small text-muted text-truncate" style="max-width: 250px;">
                                <?= htmlspecialchars(mb_substr($promo['description'], 0, 50)) ?>...
                            </div>
                        </td>
                        <td>
                            <small class="d-block text-nowrap">С: <?= date('d.m.Y', strtotime($promo['start_date'])) ?></small>
                            <?php if ($promo['end_date']): ?>
                                <small class="d-block text-nowrap text-danger">До: <?= date('d.m.Y', strtotime($promo['end_date'])) ?></small>
                            <?php else: ?>
                                <small class="d-block text-success">Бессрочно</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($promo['discount_percent'])): ?>
                                <span class="badge bg-warning text-dark">-<?= $promo['discount_percent'] ?>%</span>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($promo['is_active']): ?>
                                <span class="badge bg-success">Активна</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Скрыта</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="promotion_edit.php?id=<?= $promo['id'] ?>" class="btn btn-outline-primary" title="Редактировать">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="?delete_id=<?= $promo['id'] ?>" class="btn btn-outline-danger" 
                                   onclick="return confirm('Вы уверены, что хотите удалить эту акцию?');" title="Удалить">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Акций пока нет. Нажмите "Добавить акцию".</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/admin_footer.php'; ?>
