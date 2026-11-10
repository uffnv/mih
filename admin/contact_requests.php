<?php
require_once '../includes/config.php';

// ЛОГИКА ОБРАБОТКИ
if (isset($_GET['action']) && $_GET['action'] === 'process' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE contact_requests SET status = 'processed' WHERE id = ?");
        $stmt->execute([$id]);
    }
    header("Location: contact_requests.php");
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($id > 0) {
        $pdo->prepare("DELETE FROM contact_requests WHERE id = ?")->execute([$id]);
    }
    header("Location: contact_requests.php");
    exit;
}

$page_title = 'Обращения с сайта';
require_once 'admin_header.php';

$stmt = $pdo->query("SELECT * FROM contact_requests ORDER BY created_at DESC");
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h3 fw-bold text-dark mb-0">Обращения с сайта (<?= count($requests) ?>)</h1>
        <small class="text-muted">Заявки и вопросы, отправленные через форму обратной связи</small>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3" style="width: 50px;">ID</th>
                        <th class="py-3">Дата</th>
                        <th class="py-3">Имя клиента</th>
                        <th class="py-3">Контакты</th>
                        <th class="py-3">Сообщение</th>
                        <th class="py-3">Статус</th>
                        <th class="text-end pe-3 py-3">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($requests)): ?>
                        <?php foreach ($requests as $req): ?>
                        <tr>
                            <td class="ps-3 fw-bold text-dark">#<?= $req['id']; ?></td>
                            <td class="text-nowrap small text-muted"><?= date('d.m.Y H:i', strtotime($req['created_at'])); ?></td>
                            <td class="fw-bold text-dark"><?= htmlspecialchars($req['name']); ?></td>
                            <td>
                                <div><a href="mailto:<?= htmlspecialchars($req['email']); ?>" class="text-decoration-none text-dark small"><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($req['email']); ?></a></div>
                                <?php if (!empty($req['phone'])): ?>
                                    <div><a href="tel:<?= htmlspecialchars($req['phone']); ?>" class="text-decoration-none text-muted small"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($req['phone']); ?></a></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="text-wrap small text-muted lh-sm" style="min-width: 200px; max-width: 450px;">
                                    <?= nl2br(htmlspecialchars($req['message'])); ?>
                                </div>
                            </td>
                            <td>
                                <?php if ($req['status'] != 'processed'): ?>
                                    <span class="badge bg-danger rounded-pill px-2">Новое</span>
                                <?php else: ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2">Обработано</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-3 text-nowrap">
                                <?php if ($req['status'] != 'processed'): ?>
                                    <a href="contact_requests.php?id=<?= $req['id']; ?>&action=process" class="btn btn-sm btn-success rounded-pill px-3 me-1" title="Отметить как обработанное">
                                        <i class="bi bi-check-lg me-1"></i> Обработано
                                    </a>
                                <?php endif; ?>
                                <a href="contact_requests.php?id=<?= $req['id']; ?>&action=delete" class="btn btn-sm btn-outline-danger rounded-pill px-2" onclick="return confirm('Удалить обращение?');" title="Удалить">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox display-4 opacity-50 d-block mb-2"></i>
                                Обращений пока нет
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
