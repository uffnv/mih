<?php
require_once '../includes/config.php';

// ЛОГИКА ОБРАБОТКИ (Выполняется строго до вывода)
if (isset($_GET['action']) && $_GET['action'] === 'process' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Валидация: обновляем только если ID > 0
    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE contact_requests SET status = 'processed' WHERE id = ?");
        $stmt->execute([$id]);
    }
    
    // Редирект на "чистый" URL, чтобы сбросить GET-параметры
    header("Location: contact_requests.php");
    exit;
}

// Удаление (опционально, если нужно)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($id > 0) {
        $pdo->prepare("DELETE FROM contact_requests WHERE id = ?")->execute([$id]);
    }
    header("Location: contact_requests.php");
    exit;
}

// ТЕПЕРЬ ПОДКЛЮЧАЕМ ШАПКУ
require_once 'admin_header.php';

$stmt = $pdo->query("SELECT * FROM contact_requests ORDER BY created_at DESC");
$requests = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Обращения</h1>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Дата</th>
                        <th>Имя</th>
                        <th>Email</th>
                        <th>Сообщение</th>
                        <th>Статус</th>
                        <th class="text-end">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($requests)): ?>
                        <?php foreach ($requests as $req): ?>
                        <tr>
                            <td><?= $req['id']; ?></td>
                            <td class="text-nowrap"><?= date('d.m.Y H:i', strtotime($req['created_at'])); ?></td>
                            <td class="fw-bold"><?= htmlspecialchars($req['name']); ?></td>
                            <td><a href="mailto:<?= htmlspecialchars($req['email']); ?>"><?= htmlspecialchars($req['email']); ?></a></td>
                            <td>
                                <div class="text-wrap" style="min-width: 200px; max-width: 400px;">
                                    <small><?= htmlspecialchars($req['message']); ?></small>
                                </div>
                            </td>
                            <td>
                                <?php if ($req['status'] == 'new'): ?>
                                    <span class="badge bg-danger">Новое</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Обработано</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <?php if ($req['status'] == 'new'): ?>
                                    <!-- Явно указываем action=process -->
                                    <a href="contact_requests.php?id=<?= $req['id']; ?>&action=process" class="btn btn-sm btn-success" title="Отметить как обработанное">
                                        <i class="bi bi-check-lg"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small"><i class="bi bi-check2-all"></i> OK</span>
                                <?php endif; ?>
                                
                                <!-- Кнопка удаления для проверки, что действия разделены -->
                                <a href="contact_requests.php?id=<?= $req['id']; ?>&action=delete" class="btn btn-sm btn-outline-danger ms-1" onclick="return confirm('Удалить запись?');" title="Удалить">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">Обращений нет</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
