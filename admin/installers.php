<?php
require_once __DIR__ . '/admin_auth.php';
require_once '../includes/config.php';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $pdo->prepare("UPDATE installers SET is_active = 0 WHERE id = ?")->execute([$id]);
    } catch (Throwable $e) {}
    header("Location: installers.php?msg=deleted");
    exit;
}
if (isset($_GET['restore'])) {
    $id = (int)$_GET['restore'];
    try {
        $pdo->prepare("UPDATE installers SET is_active = 1 WHERE id = ?")->execute([$id]);
    } catch (Throwable $e) {}
    header("Location: installers.php?msg=restored");
    exit;
}

$page_title = 'Управление мастерами';
require_once 'admin_header.php';

$installers = [];
try {
    $stmt = $pdo->query("SELECT * FROM installers ORDER BY id DESC");
    $installers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log("Error loading installers: " . $e->getMessage());
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h3 fw-bold text-dark mb-0">Монтажники (<?= count($installers) ?>)</h1>
        <small class="text-muted">База сертифицированных специалистов по монтажу отопления</small>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="installer_edit.php" class="btn btn-warning fw-bold rounded-pill shadow-sm px-3">
            <i class="bi bi-person-plus-fill me-1"></i> Добавить мастера
        </a>
    </div>
</div>

<?php if (isset($_GET['msg'])): ?>
    <?php if ($_GET['msg'] === 'deleted'): ?>
        <div class="alert alert-warning alert-dismissible fade show rounded-4 mb-4" role="alert">
            <i class="bi bi-info-circle me-2"></i> Мастер скрыт из публичного каталога.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($_GET['msg'] === 'restored'): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
            <i class="bi bi-check-circle me-2"></i> Мастер активирован.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
<?php endif; ?>

<div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3" style="width: 50px;">ID</th>
                        <th class="py-3" style="width: 60px;">Фото</th>
                        <th class="py-3">Специалист</th>
                        <th class="py-3">Телефон</th>
                        <th class="py-3">Рейтинг</th>
                        <th class="py-3">Статус</th>
                        <th class="text-end pe-3 py-3">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($installers)): ?>
                        <?php foreach ($installers as $inst): 
                            $photo = $inst['photo_path'];
                            if ($photo) $photo = str_replace('../', '', $photo);
                            $photo_src = (!empty($photo) && file_exists(__DIR__ . '/../' . $photo)) ? '../' . $photo : '';
                        ?>
                        <tr>
                            <td class="ps-3 fw-bold text-dark">#<?= $inst['id']; ?></td>
                            <td>
                                <?php if (!empty($photo_src)): ?>
                                    <img src="<?= htmlspecialchars($photo_src); ?>" class="rounded-circle object-fit-cover border shadow-sm" width="44" height="44" alt="">
                                <?php else: ?>
                                    <div class="bg-warning bg-opacity-10 text-dark rounded-circle d-flex align-items-center justify-content-center fw-bold border" style="width: 44px; height: 44px;">
                                        <?= mb_substr($inst['first_name'] ?? 'М', 0, 1) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($inst['first_name'] . ' ' . $inst['last_name']); ?></div>
                                <small class="text-muted"><?= htmlspecialchars($inst['specialization'] ?? 'Монтаж систем отопления'); ?></small>
                            </td>
                            <td class="text-nowrap">
                                <a href="tel:<?= htmlspecialchars($inst['phone']); ?>" class="text-decoration-none text-dark fw-bold small">
                                    <?= htmlspecialchars($inst['phone']); ?>
                                </a>
                            </td>
                            <td>
                                <span class="text-warning fw-bold"><i class="bi bi-star-fill"></i> <?= number_format($inst['average_rating'] ?? 5, 1); ?></span>
                                <small class="text-muted">(<?= $inst['review_count'] ?? 0; ?>)</small>
                            </td>
                            <td>
                                <?= $inst['is_active'] ? 
                                    '<span class="badge bg-success rounded-pill px-2">Активен</span>' : 
                                    '<span class="badge bg-secondary rounded-pill px-2">Скрыт</span>'; 
                                ?>
                            </td>
                            <td class="text-end pe-3 text-nowrap">
                                <a href="installer_edit.php?id=<?= $inst['id']; ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3 me-1" title="Редактировать">
                                    <i class="bi bi-pencil me-1"></i> Изменить
                                </a>
                                <?php if ($inst['is_active']): ?>
                                    <a href="installers.php?delete=<?= $inst['id']; ?>" class="btn btn-sm btn-outline-danger rounded-pill px-2" onclick="return confirm('Скрыть мастера?');" title="Скрыть">
                                        <i class="bi bi-eye-slash"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="installers.php?restore=<?= $inst['id']; ?>" class="btn btn-sm btn-outline-success rounded-pill px-2" title="Активировать">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-people display-4 opacity-50 d-block mb-2"></i>
                                Мастера пока не добавлены
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
