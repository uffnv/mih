<?php
require_once '../includes/config.php';
require_once 'admin_header.php';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("UPDATE installers SET is_active = 0 WHERE id = ?")->execute([$id]);
    header("Location: installers.php?msg=deleted");
    exit;
}

$stmt = $pdo->query("SELECT * FROM installers ORDER BY id DESC");
$installers = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Монтажники</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="installer_edit.php" class="btn btn-success">
            <i class="bi bi-person-plus-fill"></i> Добавить мастера
        </a>
    </div>
</div>

<?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
    <div class="alert alert-success alert-dismissible fade show">
        Мастер удален (скрыт).
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th style="width: 60px;">Фото</th>
                        <th>Имя</th>
                        <th>Телефон</th>
                        <th>Рейтинг</th>
                        <th>Статус</th>
                        <th class="text-end">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($installers as $inst): ?>
                    <tr>
                        <td><?= $inst['id']; ?></td>
                        <td>
                            <?php if (!empty($inst['photo_path'])): ?>
                                <img src="<?= htmlspecialchars($inst['photo_path']); ?>" class="rounded-circle object-fit-cover" width="40" height="40" alt="">
                            <?php else: ?>
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-muted" style="width: 40px; height: 40px;"><i class="bi bi-person"></i></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="fw-bold"><?= htmlspecialchars($inst['first_name'] . ' ' . $inst['last_name']); ?></div>
                            <small class="text-muted"><?= htmlspecialchars($inst['specialization'] ?? ''); ?></small>
                        </td>
                        <td class="text-nowrap"><?= htmlspecialchars($inst['phone']); ?></td>
                        <td>
                            <span class="text-warning fw-bold"><i class="bi bi-star-fill"></i> <?= number_format($inst['average_rating'] ?? 0, 1); ?></span>
                            <small class="text-muted">(<?= $inst['review_count'] ?? 0; ?>)</small>
                        </td>
                        <td>
                            <?= $inst['is_active'] ? '<span class="badge bg-success">Активен</span>' : '<span class="badge bg-secondary">Скрыт</span>'; ?>
                        </td>
                        <td class="text-end text-nowrap">
                            <a href="installer_edit.php?id=<?= $inst['id']; ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                            <a href="installers.php?delete=<?= $inst['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить?');"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
