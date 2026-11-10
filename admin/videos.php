<?php
require_once __DIR__ . '/admin_auth.php';
require_once '../includes/config.php';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $pdo->prepare("DELETE FROM videos WHERE id = ?")->execute([$id]);
    } catch (Throwable $e) {}
    header("Location: videos.php?msg=deleted");
    exit;
}

$page_title = 'Управление видео';
require_once 'admin_header.php';

$videos = [];
try {
    $stmt = $pdo->query("SELECT * FROM videos ORDER BY created_at DESC");
    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log("Error loading videos: " . $e->getMessage());
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h3 fw-bold text-dark mb-0">Видеогалерея (<?= count($videos) ?>)</h1>
        <small class="text-muted">Видеоролики о монтаже, обзорах оборудования и отзывах</small>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="video_edit.php" class="btn btn-warning fw-bold rounded-pill shadow-sm px-3">
            <i class="bi bi-camera-video me-1"></i> Добавить видео
        </a>
    </div>
</div>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
        <i class="bi bi-check-circle me-2"></i> Видеоролик удален.
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
                        <th class="py-3">Название видео</th>
                        <th class="py-3">Ссылка на источник</th>
                        <th class="py-3">На главной</th>
                        <th class="py-3">Статус</th>
                        <th class="text-end pe-3 py-3">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($videos)): ?>
                        <?php foreach ($videos as $v): ?>
                        <tr>
                            <td class="ps-3 fw-bold text-dark">#<?= $v['id']; ?></td>
                            <td class="fw-bold text-dark"><?= htmlspecialchars($v['title']); ?></td>
                            <td>
                                <a href="<?= htmlspecialchars($v['video_url']); ?>" target="_blank" class="text-decoration-none text-dark small text-truncate d-inline-block" style="max-width: 250px;">
                                    <i class="bi bi-box-arrow-up-right me-1 text-primary"></i> <?= htmlspecialchars($v['video_url']); ?>
                                </a>
                            </td>
                            <td><?= $v['show_on_home'] ? '<span class="badge bg-warning text-dark rounded-pill px-2">Да</span>' : '<span class="badge bg-light text-muted border rounded-pill px-2">Нет</span>'; ?></td>
                            <td><?= $v['is_active'] ? '<span class="badge bg-success rounded-pill px-2">Активно</span>' : '<span class="badge bg-secondary rounded-pill px-2">Скрыто</span>'; ?></td>
                            <td class="text-end pe-3 text-nowrap">
                                <a href="video_edit.php?id=<?= $v['id']; ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3 me-1" title="Редактировать">
                                    <i class="bi bi-pencil me-1"></i> Изменить
                                </a>
                                <a href="videos.php?delete=<?= $v['id']; ?>" class="btn btn-sm btn-outline-danger rounded-pill px-2" onclick="return confirm('Удалить видеоролик?');" title="Удалить">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-camera-video display-4 opacity-50 d-block mb-2"></i>
                                Видеоролики пока не добавлены
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
