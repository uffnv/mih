<?php
require_once __DIR__ . '/admin_auth.php';
require_once '../includes/config.php';
require_once '../includes/functions.php';

// 0. Автоматическая проверка и расширение колонок при необходимости
try {
    $cols = $pdo->query("SHOW COLUMNS FROM installer_applications")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('admin_notes', $cols)) {
        $pdo->exec("ALTER TABLE installer_applications ADD COLUMN admin_notes TEXT NULL;");
    }
    if (!in_array('city', $cols)) {
        $pdo->exec("ALTER TABLE installer_applications ADD COLUMN city VARCHAR(100) NULL DEFAULT 'Республика Алтай';");
    }
    if (!in_array('telegram', $cols)) {
        $pdo->exec("ALTER TABLE installer_applications ADD COLUMN telegram VARCHAR(100) NULL;");
    }
} catch (Throwable $e) {}

// 1. Одобрение заявки
if (isset($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM installer_applications WHERE id = ?");
        $stmt->execute([$id]);
        $app = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($app) {
            $pdo->beginTransaction();
            // Помечаем одобренной
            $pdo->prepare("UPDATE installer_applications SET status = 'approved' WHERE id = ?")->execute([$id]);
            
            // Создаем карточку мастера в таблице installers (если еще нет)
            $check = $pdo->prepare("SELECT id FROM installers WHERE phone = ?");
            $check->execute([$app['phone']]);
            $existing = $check->fetch(PDO::FETCH_ASSOC);

            if (!$existing) {
                $insStmt = $pdo->prepare("
                    INSERT INTO installers (first_name, last_name, phone, specialization, experience_years, bio, is_active, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, 1, NOW())
                ");
                $insStmt->execute([
                    $app['first_name'],
                    $app['last_name'],
                    $app['phone'],
                    $app['specialization'],
                    $app['experience_years'] ?? 0,
                    $app['bio'] ?? ($app['skills'] ?? '')
                ]);
            }
            $pdo->commit();
            header("Location: installer_applications.php?msg=approved");
            exit;
        }
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        error_log("Error approving application: " . $e->getMessage());
    }
}

// 2. Отклонение заявки
if (isset($_GET['reject'])) {
    $id = (int)$_GET['reject'];
    try {
        $pdo->prepare("UPDATE installer_applications SET status = 'rejected' WHERE id = ?")->execute([$id]);
        header("Location: installer_applications.php?msg=rejected");
        exit;
    } catch (Throwable $e) {}
}

// 3. Возврат в новые
if (isset($_GET['reopen'])) {
    $id = (int)$_GET['reopen'];
    try {
        $pdo->prepare("UPDATE installer_applications SET status = 'new' WHERE id = ?")->execute([$id]);
        header("Location: installer_applications.php?msg=reopened");
        exit;
    } catch (Throwable $e) {}
}

// 4. Удаление заявки
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $pdo->prepare("DELETE FROM installer_applications WHERE id = ?")->execute([$id]);
        header("Location: installer_applications.php?msg=deleted");
        exit;
    } catch (Throwable $e) {}
}

// 5. Сохранение служебной заметки
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_notes'])) {
    $id = (int)($_POST['id'] ?? 0);
    $notes = trim($_POST['admin_notes'] ?? '');
    if ($id > 0) {
        try {
            $stmt = $pdo->prepare("UPDATE installer_applications SET admin_notes = ? WHERE id = ?");
            $stmt->execute([$notes, $id]);
            header("Location: installer_applications.php?msg=notes_saved");
            exit;
        } catch (Throwable $e) {}
    }
}

$page_title = 'Заявки мастеров на партнерство';
require_once 'admin_header.php';

$filter = $_GET['filter'] ?? 'new';
$search = trim($_GET['search'] ?? '');

$where = [];
$params = [];

if ($filter === 'new') {
    $where[] = "status = 'new'";
} elseif ($filter === 'approved') {
    $where[] = "status = 'approved'";
} elseif ($filter === 'rejected') {
    $where[] = "status = 'rejected'";
}

if (!empty($search)) {
    $where[] = "(first_name LIKE ? OR last_name LIKE ? OR phone LIKE ? OR specialization LIKE ? OR skills LIKE ?)";
    $s = "%$search%";
    $params = array_merge($params, [$s, $s, $s, $s, $s]);
}

$whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

$apps = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM installer_applications $whereSql ORDER BY created_at DESC");
    $stmt->execute($params);
    $apps = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log("Error loading applications: " . $e->getMessage());
}

// Счетчики
$c_total = 0; $c_new = 0; $c_approved = 0; $c_rejected = 0;
try {
    $c_total = (int)$pdo->query("SELECT COUNT(*) FROM installer_applications")->fetchColumn();
    $c_new = (int)$pdo->query("SELECT COUNT(*) FROM installer_applications WHERE status = 'new'")->fetchColumn();
    $c_approved = (int)$pdo->query("SELECT COUNT(*) FROM installer_applications WHERE status = 'approved'")->fetchColumn();
    $c_rejected = (int)$pdo->query("SELECT COUNT(*) FROM installer_applications WHERE status = 'rejected'")->fetchColumn();
} catch (Throwable $e) {}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h3 fw-bold text-dark mb-0">Заявки монтажников (<?= count($apps) ?>)</h1>
        <small class="text-muted">Процессинг анкет специалистов по установке отопления, котлов и дымоходов</small>
    </div>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        <?php
        switch ($_GET['msg']) {
            case 'approved': echo "Заявка одобрена! Мастер добавлен в реестр специалистов."; break;
            case 'rejected': echo "Заявка отклонена."; break;
            case 'reopened': echo "Заявка возвращена на рассмотрение."; break;
            case 'deleted': echo "Анкета удалена."; break;
            case 'notes_saved': echo "Служебная заметка успешно сохранена."; break;
            default: echo "Операция выполнена успешно.";
        }
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Панель фильтров и поиска -->
<div class="card border-0 shadow-sm rounded-4 p-3 bg-white mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div class="d-flex gap-2">
            <a href="installer_applications.php?filter=new" class="btn btn-sm rounded-pill <?= $filter === 'new' ? 'btn-warning text-dark fw-bold' : 'btn-outline-warning text-dark' ?>">
                <i class="bi bi-clock-history me-1"></i> Новые (<?= $c_new ?>)
            </a>
            <a href="installer_applications.php?filter=approved" class="btn btn-sm rounded-pill <?= $filter === 'approved' ? 'btn-success fw-bold' : 'btn-outline-success' ?>">
                <i class="bi bi-check-circle-fill me-1"></i> Одобренные (<?= $c_approved ?>)
            </a>
            <a href="installer_applications.php?filter=rejected" class="btn btn-sm rounded-pill <?= $filter === 'rejected' ? 'btn-secondary fw-bold' : 'btn-outline-secondary' ?>">
                <i class="bi bi-x-circle-fill me-1"></i> Отклоненные (<?= $c_rejected ?>)
            </a>
            <a href="installer_applications.php?filter=all" class="btn btn-sm rounded-pill <?= $filter === 'all' ? 'btn-dark fw-bold' : 'btn-outline-secondary' ?>">
                Все (<?= $c_total ?>)
            </a>
        </div>

        <form method="GET" action="installer_applications.php" class="d-flex gap-2" style="max-width: 350px; width: 100%;">
            <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
            <div class="input-group input-group-sm">
                <input type="text" name="search" class="form-control" placeholder="Поиск мастера..." value="<?= htmlspecialchars($search) ?>">
                <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
            </div>
            <?php if (!empty($search)): ?>
                <a href="installer_applications.php?filter=<?= htmlspecialchars($filter) ?>" class="btn btn-sm btn-outline-dark" title="Сбросить"><i class="bi bi-x-lg"></i></a>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Реестр анкет -->
<div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3">ФИО мастера</th>
                        <th class="py-3">Связь и каналы</th>
                        <th class="py-3">Специализация</th>
                        <th class="py-3">Опыт</th>
                        <th class="py-3" style="max-width: 250px;">Анкета / Навыки</th>
                        <th class="py-3">Статус</th>
                        <th class="py-3">Дата</th>
                        <th class="text-end pe-3 py-3">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($apps)): ?>
                        <?php foreach ($apps as $a): ?>
                            <?php 
                            $clean_phone = preg_replace('/[^0-9]/', '', $a['phone']);
                            if (strlen($clean_phone) === 11 && $clean_phone[0] === '8') {
                                $clean_phone = '7' . substr($clean_phone, 1);
                            }
                            $modal_id = 'appModal' . $a['id'];
                            ?>
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-bold text-dark fs-6">
                                        <?= htmlspecialchars($a['first_name'] . ' ' . $a['last_name']) ?>
                                    </div>
                                    <small class="text-muted"><?= htmlspecialchars($a['city'] ?? 'Республика Алтай') ?></small>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <a href="tel:<?= htmlspecialchars($a['phone']) ?>" class="text-decoration-none fw-semibold text-dark small">
                                            <i class="bi bi-telephone text-warning me-1"></i><?= htmlspecialchars($a['phone']) ?>
                                        </a>
                                        <div class="d-flex gap-1 mt-1">
                                            <?php if (!empty($clean_phone)): ?>
                                                <a href="https://wa.me/<?= $clean_phone ?>" target="_blank" class="badge bg-success text-decoration-none" title="WhatsApp">
                                                    <i class="bi bi-whatsapp"></i> WA
                                                </a>
                                                <a href="https://t.me/+<?= $clean_phone ?>" target="_blank" class="badge bg-info text-white text-decoration-none" title="Telegram">
                                                    <i class="bi bi-telegram"></i> TG
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25 px-2 py-1">
                                        <?= htmlspecialchars($a['specialization']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark"><?= (int)$a['experience_years'] ?> лет</span>
                                </td>
                                <td style="max-width: 250px;">
                                    <div class="small text-muted text-truncate mb-1" style="max-width: 240px;">
                                        <?= htmlspecialchars(mb_strimwidth($a['skills'] ?: $a['bio'], 0, 70, '...')) ?>
                                    </div>
                                    <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2 rounded-pill" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#<?= $modal_id ?>">
                                        <i class="bi bi-card-text me-1"></i> Читать анкету
                                    </button>
                                </td>
                                <td>
                                    <?php if ($a['status'] === 'approved'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2">Одобрен</span>
                                    <?php elseif ($a['status'] === 'rejected'): ?>
                                        <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-2">Отклонен</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark rounded-pill px-2">Новая</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-muted text-nowrap">
                                    <?= date('d.m.Y H:i', strtotime($a['created_at'])) ?>
                                </td>
                                <td class="text-end pe-3 text-nowrap">
                                    <?php if ($a['status'] !== 'approved'): ?>
                                        <a href="installer_applications.php?approve=<?= $a['id'] ?>" class="btn btn-sm btn-success rounded-pill px-3 me-1 fw-semibold" title="Одобрить и добавить мастера в каталог">
                                            <i class="bi bi-check-lg me-1"></i> Одобрить
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($a['status'] === 'new'): ?>
                                        <a href="installer_applications.php?reject=<?= $a['id'] ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-2 me-1" title="Отклонить">
                                            <i class="bi bi-x-circle"></i>
                                        </a>
                                    <?php elseif ($a['status'] === 'rejected'): ?>
                                        <a href="installer_applications.php?reopen=<?= $a['id'] ?>" class="btn btn-sm btn-outline-warning rounded-pill px-2 me-1" title="Вернуть на рассмотрение">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="installer_applications.php?delete=<?= $a['id'] ?>" class="btn btn-sm btn-outline-danger rounded-pill px-2" onclick="return confirm('Удалить анкету?');" title="Удалить">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>

                            <!-- Модальное окно полного просмотра анкеты -->
                            <div class="modal fade" id="<?= $modal_id ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                        <div class="modal-header bg-dark text-white py-3">
                                            <h5 class="modal-title fw-bold">
                                                <i class="bi bi-person-badge text-warning me-2"></i> Анкета мастера: <?= htmlspecialchars($a['first_name'] . ' ' . $a['last_name']) ?>
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="row g-3 mb-4">
                                                <div class="col-sm-6">
                                                    <div class="text-muted small">Телефон:</div>
                                                    <div class="fw-bold fs-6">
                                                        <a href="tel:<?= htmlspecialchars($a['phone']) ?>" class="text-dark text-decoration-none">
                                                            <?= htmlspecialchars($a['phone']) ?>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="text-muted small">Каналы связи:</div>
                                                    <div class="d-flex gap-2 mt-1">
                                                        <a href="https://wa.me/<?= $clean_phone ?>" target="_blank" class="btn btn-sm btn-success rounded-pill px-3">
                                                            <i class="bi bi-whatsapp me-1"></i> WhatsApp
                                                        </a>
                                                        <a href="https://t.me/+<?= $clean_phone ?>" target="_blank" class="btn btn-sm btn-info text-white rounded-pill px-3">
                                                            <i class="bi bi-telegram me-1"></i> Telegram
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="text-muted small">Специализация:</div>
                                                    <span class="badge bg-warning text-dark px-2 py-1"><?= htmlspecialchars($a['specialization']) ?></span>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="text-muted small">Стаж работы:</div>
                                                    <div class="fw-bold"><?= (int)$a['experience_years'] ?> лет</div>
                                                </div>
                                            </div>

                                            <div class="mb-4">
                                                <h6 class="fw-bold text-dark border-bottom pb-2">Навыки и опыт работы:</h6>
                                                <div class="p-3 bg-light rounded-3 small text-dark lh-base" style="white-space: pre-wrap; word-break: break-word;">
                                                    <?= nl2br(htmlspecialchars($a['skills'] ?: 'Не указаны')) ?>
                                                </div>
                                            </div>

                                            <?php if (!empty($a['bio'])): ?>
                                                <div class="mb-4">
                                                    <h6 class="fw-bold text-dark border-bottom pb-2">О мастере (биография):</h6>
                                                    <div class="p-3 bg-light rounded-3 small text-dark lh-base" style="white-space: pre-wrap; word-break: break-word;">
                                                        <?= nl2br(htmlspecialchars($a['bio'])) ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <!-- Заметки менеджера -->
                                            <form method="POST" action="installer_applications.php" class="card border rounded-3 p-3 bg-light">
                                                <input type="hidden" name="save_notes" value="1">
                                                <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <label class="small fw-bold text-dark text-uppercase mb-0">
                                                        <i class="bi bi-pencil-square text-warning me-1"></i> Служебная заметка о мастере:
                                                    </label>
                                                    <button type="submit" class="btn btn-sm btn-dark rounded-pill px-3">
                                                        Сохранить
                                                    </button>
                                                </div>
                                                <textarea name="admin_notes" class="form-control form-control-sm" rows="2" placeholder="Например: Проверили дипломы, рекомендован прорабом ZOTA..."><?= htmlspecialchars($a['admin_notes'] ?? '') ?></textarea>
                                            </form>
                                        </div>
                                        <div class="modal-footer bg-light py-2 px-4 d-flex justify-content-between">
                                            <div class="small text-muted">Дата подачи заявки: <?= date('d.m.Y H:i', strtotime($a['created_at'])) ?></div>
                                            <div>
                                                <?php if ($a['status'] !== 'approved'): ?>
                                                    <a href="installer_applications.php?approve=<?= $a['id'] ?>" class="btn btn-success rounded-pill px-3 fw-bold">
                                                        <i class="bi bi-check-lg me-1"></i> Одобрить мастера
                                                    </a>
                                                <?php endif; ?>
                                                <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Закрыть</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 text-muted opacity-50 d-block mb-2"></i>
                                Заявок в этом разделе не найдено
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
