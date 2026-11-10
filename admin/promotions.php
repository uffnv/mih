<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Обработка удаления
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM promotions WHERE id = ?");
        $stmt->execute([$delete_id]);
        header("Location: promotions.php?msg=deleted");
        exit;
    } catch (Exception $e) {
        $error = "Ошибка удаления: " . $e->getMessage();
    }
}

$page_title = 'Управление акциями';
require_once __DIR__ . '/admin_header.php';

// Получение списка акций
try {
    $stmt = $pdo->query("SELECT * FROM promotions ORDER BY is_active DESC, sort_order ASC, created_at DESC");
    $promotions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $promotions = [];
    $error = "Ошибка загрузки списка: " . $e->getMessage();
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h3 fw-bold text-dark mb-0">Акции и скидки (<?= count($promotions) ?>)</h1>
        <small class="text-muted">Управление специальными предложениями и скидками</small>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="promotion_edit.php" class="btn btn-warning fw-bold rounded-pill shadow-sm px-3">
            <i class="bi bi-plus-lg me-1"></i> Добавить акцию
        </a>
    </div>
</div>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
        <i class="bi bi-check-circle me-2"></i> Акция успешно удалена.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i> <?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3" style="width: 50px;">#</th>
                        <th class="py-3" style="width: 70px;">Фото</th>
                        <th class="py-3">Название</th>
                        <th class="py-3">Период действия</th>
                        <th class="py-3">Скидка</th>
                        <th class="py-3">Статус</th>
                        <th class="text-end pe-3 py-3">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($promotions)): ?>
                        <?php foreach ($promotions as $promo): 
                            $img = $promo['image_path'];
                            if ($img) $img = str_replace('../', '', $img);
                            $img_src = (!empty($img) && file_exists(__DIR__ . '/../' . $img)) ? '../' . $img : '../images/hero-bg.jpg';
                        ?>
                            <tr>
                                <td class="ps-3 fw-bold text-dark">#<?= $promo['id'] ?></td>
                                <td>
                                    <img src="<?= htmlspecialchars($img_src) ?>" alt="" width="50" height="38" class="rounded-3 object-fit-cover border shadow-sm" onerror="this.src='../images/hero-bg.jpg'">
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($promo['title']) ?></div>
                                    <small class="text-muted text-truncate d-block" style="max-width: 280px;"><?= htmlspecialchars($promo['description'] ?? '') ?></small>
                                </td>
                                <td class="small">
                                    <div><i class="bi bi-play-circle text-success me-1"></i><?= date('d.m.Y', strtotime($promo['start_date'])) ?></div>
                                    <?php if (!empty($promo['end_date'])): ?>
                                        <div><i class="bi bi-stop-circle text-danger me-1"></i><?= date('d.m.Y', strtotime($promo['end_date'])) ?></div>
                                    <?php else: ?>
                                        <div class="text-muted">Бессрочно</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($promo['discount_percent'])): ?>
                                        <span class="badge bg-danger rounded-pill px-2 py-1">-<?= $promo['discount_percent'] ?>%</span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($promo['is_active']): ?>
                                        <span class="badge bg-success rounded-pill px-2">Активна</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary rounded-pill px-2">Скрыта</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-3 text-nowrap">
                                    <a href="promotion_edit.php?id=<?= $promo['id'] ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3 me-1" title="Редактировать">
                                        <i class="bi bi-pencil me-1"></i> Изменить
                                    </a>
                                    <a href="promotions.php?delete_id=<?= $promo['id'] ?>" class="btn btn-sm btn-outline-danger rounded-pill px-2" onclick="return confirm('Удалить эту акцию?');" title="Удалить">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-megaphone display-4 opacity-50 d-block mb-2"></i>
                                Акции пока не добавлены
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/admin_footer.php'; ?>
