<?php
require_once '../includes/config.php';

// Одобрение заявки
if (isset($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    $stmt = $pdo->prepare("SELECT * FROM installer_applications WHERE id = ?");
    $stmt->execute([$id]);
    $app = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($app) {
        $pdo->beginTransaction();
        // 1. Помечаем одобренной
        $pdo->prepare("UPDATE installer_applications SET status = 'approved' WHERE id = ?")->execute([$id]);
        
        // 2. Создаем карточку мастера в таблице installers (если нет)
        $check = $pdo->prepare("SELECT id FROM installers WHERE phone = ?");
        $check->execute([$app['phone']]);
        if (!$check->fetch()) {
            $insStmt = $pdo->prepare("
                INSERT INTO installers (first_name, last_name, phone, specialization, experience_years, is_active)
                VALUES (?, ?, ?, ?, ?, 1)
            ");
            $insStmt->execute([
                $app['first_name'],
                $app['last_name'],
                $app['phone'],
                $app['specialization'],
                $app['experience_years'] ?? 0
            ]);
        }
        $pdo->commit();
        header("Location: installer_applications.php?msg=approved");
        exit;
    }
}

// Отклонение заявки
if (isset($_GET['reject'])) {
    $id = (int)$_GET['reject'];
    $pdo->prepare("UPDATE installer_applications SET status = 'rejected' WHERE id = ?")->execute([$id]);
    header("Location: installer_applications.php?msg=rejected");
    exit;
}

// Удаление заявки
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM installer_applications WHERE id = ?")->execute([$id]);
    header("Location: installer_applications.php?msg=deleted");
    exit;
}

$page_title = 'Заявки мастеров на партнерство';
require_once 'admin_header.php';

$filter = $_GET['filter'] ?? 'new';
$sql = "SELECT * FROM installer_applications ";
if ($filter === 'new') {
    $sql .= " WHERE status = 'new' ";
} elseif ($filter === 'approved') {
    $sql .= " WHERE status = 'approved' ";
} elseif ($filter === 'rejected') {
    $sql .= " WHERE status = 'rejected' ";
}
$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->query($sql);
$apps = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h3 fw-bold text-dark mb-0">Заявки монтажников (<?= count($apps) ?>)</h1>
        <small class="text-muted">Анкеты специалистов, желающих стать партнерами «Территории Тепла»</small>
    </div>
</div>

<div class="d-flex gap-2 mb-3">
    <a href="installer_applications.php?filter=new" class="btn btn-sm rounded-pill <?= $filter === 'new' ? 'btn-warning text-dark fw-bold' : 'btn-outline-warning text-dark' ?>">Новые заявки</a>
    <a href="installer_applications.php?filter=approved" class="btn btn-sm rounded-pill <?= $filter === 'approved' ? 'btn-success fw-bold' : 'btn-outline-success' ?>">Одобренные</a>
    <a href="installer_applications.php?filter=rejected" class="btn btn-sm rounded-pill <?= $filter === 'rejected' ? 'btn-secondary fw-bold' : 'btn-outline-secondary' ?>">Отклоненные</a>
    <a href="installer_applications.php?filter=all" class="btn btn-sm rounded-pill <?= $filter === 'all' ? 'btn-dark fw-bold' : 'btn-outline-secondary' ?>">Все</a>
</div>

<div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3">ФИО мастера</th>
                        <th class="py-3">Телефон</th>
                        <th class="py-3">Специализация</th>
                        <th class="py-3">Опыт</th>
                        <th class="py-3" style="max-width: 300px;">Навыки / О себе</th>
                        <th class="py-3">Статус</th>
                        <th class="py-3">Дата</th>
                        <th class="text-end pe-3 py-3">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($apps)): ?>
                        <?php foreach ($apps as $a): ?>
                        <tr>
                            <td class="ps-3 fw-bold text-dark">
                                <?= htmlspecialchars($a['first_name'] . ' ' . $a['last_name']) ?>
                            </td>
                            <td>
                                <a href="tel:<?= htmlspecialchars($a['phone']) ?>" class="text-decoration-none fw-semibold text-dark">
                                    <i class="bi bi-telephone text-warning me-1"></i><?= htmlspecialchars($a['phone']) ?>
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <?= htmlspecialchars($a['specialization']) ?>
                                </span>
                            </td>
                            <td class="small">
                                <?= (int)$a['experience_years'] ?> лет
                            </td>
                            <td class="small text-muted" style="max-width: 300px;">
                                <?php if (!empty($a['skills'])): ?>
                                    <div><strong>Навыки:</strong> <?= htmlspecialchars($a['skills']) ?></div>
                                <?php endif; ?>
                                <?php if (!empty($a['bio'])): ?>
                                    <div><?= nl2br(htmlspecialchars($a['bio'])) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($a['status'] === 'approved'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Одобрен</span>
                                <?php elseif ($a['status'] === 'rejected'): ?>
                                    <span class="badge bg-secondary-subtle text-secondary border rounded-pill">Отклонен</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">Новая</span>
                                <?php endif; ?>
                            </td>
                            <td class="small text-muted">
                                <?= date('d.m.Y H:i', strtotime($a['created_at'])) ?>
                            </td>
                            <td class="text-end pe-3 text-nowrap">
                                <?php if ($a['status'] !== 'approved'): ?>
                                    <a href="installer_applications.php?approve=<?= $a['id'] ?>" class="btn btn-sm btn-success rounded-pill px-2 me-1" title="Одобрить и добавить в мастера">
                                        <i class="bi bi-person-check-fill me-1"></i>Одобрить
                                    </a>
                                <?php endif; ?>
                                <?php if ($a['status'] === 'new'): ?>
                                    <a href="installer_applications.php?reject=<?= $a['id'] ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-2 me-1" title="Отклонить">
                                        <i class="bi bi-x-circle"></i>
                                    </a>
                                <?php endif; ?>
                                <a href="installer_applications.php?delete=<?= $a['id'] ?>" class="btn btn-sm btn-outline-danger rounded-pill px-2" onclick="return confirm('Удалить анкету?');" title="Удалить">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Заявок не найдено</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
