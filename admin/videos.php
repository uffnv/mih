<?php
require_once '../includes/config.php';
require_once 'admin_header.php';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM videos WHERE id = ?")->execute([$id]);
    header("Location: videos.php?msg=deleted");
    exit;
}

$stmt = $pdo->query("SELECT * FROM videos ORDER BY created_at DESC");
$videos = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Видео</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="video_edit.php" class="btn btn-success">
            <i class="bi bi-camera-video"></i> Добавить видео
        </a>
    </div>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        Видео удалено.
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
                        <th>Название</th>
                        <th>Ссылка</th>
                        <th>На главной</th>
                        <th>Статус</th>
                        <th class="text-end">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($videos as $v): ?>
                    <tr>
                        <td><?= $v['id']; ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($v['title']); ?></td>
                        <td>
                            <a href="<?= htmlspecialchars($v['video_url']); ?>" target="_blank" class="text-decoration-none text-truncate d-inline-block" style="max-width: 150px;">
                                <?= htmlspecialchars($v['video_url']); ?>
                            </a>
                        </td>
                        <td><?= $v['show_on_home'] ? '<span class="badge bg-primary">Да</span>' : '<span class="badge bg-secondary">Нет</span>'; ?></td>
                        <td><?= $v['is_active'] ? '<span class="badge bg-success">Активен</span>' : '<span class="badge bg-danger">Скрыт</span>'; ?></td>
                        <td class="text-end text-nowrap">
                            <a href="video_edit.php?id=<?= $v['id']; ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                            <a href="videos.php?delete=<?= $v['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить?');"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
