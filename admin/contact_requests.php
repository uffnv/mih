<?php
require_once __DIR__ . '/admin_auth.php';
require_once '../includes/config.php';
require_once '../includes/functions.php';

// 0. Автоматическое исправление схемы и перенумерация id = 0
try {
    $pkCheck = $pdo->query("SHOW KEYS FROM contact_requests WHERE Key_name = 'PRIMARY'");
    if (!$pkCheck || $pkCheck->rowCount() === 0) {
        // Если primary key нет или все id = 0, безопасно перенумеруем записи
        $pdo->exec("SET @r := 0; UPDATE contact_requests SET id = (@r := @r + 1) ORDER BY created_at ASC;");
        $pdo->exec("ALTER TABLE contact_requests MODIFY id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY;");
    }
    // Проверяем наличие колонки admin_notes
    $notesCheck = $pdo->query("SHOW COLUMNS FROM contact_requests LIKE 'admin_notes'");
    if (!$notesCheck || $notesCheck->rowCount() === 0) {
        $pdo->exec("ALTER TABLE contact_requests ADD COLUMN admin_notes TEXT NULL;");
    }
} catch (Throwable $e) {
    error_log("Schema auto-fix contact_requests: " . $e->getMessage());
}

// 1. Обработка действий
$msg = null;
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $req_id = (int)($_GET['id'] ?? 0);

    if ($action === 'process' && $req_id > 0) {
        try {
            $stmt = $pdo->prepare("UPDATE contact_requests SET status = 'processed', processed_at = NOW() WHERE id = ?");
            $stmt->execute([$req_id]);
            header("Location: contact_requests.php?selected=" . $req_id . "&msg=processed");
            exit;
        } catch (Throwable $e) {}
    }

    if ($action === 'unprocess' && $req_id > 0) {
        try {
            $stmt = $pdo->prepare("UPDATE contact_requests SET status = 'new', processed_at = NULL WHERE id = ?");
            $stmt->execute([$req_id]);
            header("Location: contact_requests.php?selected=" . $req_id . "&msg=unprocessed");
            exit;
        } catch (Throwable $e) {}
    }

    if ($action === 'delete' && $req_id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM contact_requests WHERE id = ?");
            $stmt->execute([$req_id]);
            header("Location: contact_requests.php?msg=deleted");
            exit;
        } catch (Throwable $e) {}
    }

    if ($action === 'process_all') {
        try {
            $pdo->exec("UPDATE contact_requests SET status = 'processed', processed_at = NOW() WHERE status != 'processed'");
            header("Location: contact_requests.php?msg=all_processed");
            exit;
        } catch (Throwable $e) {}
    }
}

// 2. Сохранение заметок администратора
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_notes'])) {
    $req_id = (int)($_POST['id'] ?? 0);
    $notes = trim($_POST['admin_notes'] ?? '');
    if ($req_id > 0) {
        try {
            $stmt = $pdo->prepare("UPDATE contact_requests SET admin_notes = ? WHERE id = ?");
            $stmt->execute([$notes, $req_id]);
            header("Location: contact_requests.php?selected=" . $req_id . "&msg=notes_saved");
            exit;
        } catch (Throwable $e) {}
    }
}

$page_title = 'Почтовый центр: Обращения с сайта';
require_once 'admin_header.php';

// Фильтрация и поиск
$filter = $_GET['filter'] ?? 'all';
$search = trim($_GET['search'] ?? '');
$selected_id = (int)($_GET['selected'] ?? 0);

$where = [];
$params = [];

if ($filter === 'new') {
    $where[] = "status != 'processed'";
} elseif ($filter === 'processed') {
    $where[] = "status = 'processed'";
}

if (!empty($search)) {
    $where[] = "(name LIKE ? OR email LIKE ? OR phone LIKE ? OR message LIKE ?)";
    $sTerm = "%$search%";
    $params[] = $sTerm;
    $params[] = $sTerm;
    $params[] = $sTerm;
    $params[] = $sTerm;
}

$whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

$requests = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM contact_requests $whereSql ORDER BY created_at DESC");
    $stmt->execute($params);
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log("Error loading contact_requests: " . $e->getMessage());
}

// Счетчики
$total_count = 0;
$new_count = 0;
$processed_count = 0;
try {
    $total_count = (int)$pdo->query("SELECT COUNT(*) FROM contact_requests")->fetchColumn();
    $new_count = (int)$pdo->query("SELECT COUNT(*) FROM contact_requests WHERE status != 'processed'")->fetchColumn();
    $processed_count = $total_count - $new_count;
} catch (Throwable $e) {}

// Определение активного обращения для панели чтения
$selected_request = null;
if ($selected_id > 0) {
    foreach ($requests as $r) {
        if ((int)$r['id'] === $selected_id) {
            $selected_request = $r;
            break;
        }
    }
}
if (!$selected_request && !empty($requests)) {
    $selected_request = $requests[0];
}
?>

<style>
.mail-container {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    min-height: 680px;
    overflow: hidden;
}
.mail-list-col {
    border-right: 1px solid #e2e8f0;
    max-height: 720px;
    overflow-y: auto;
}
.mail-item {
    border-bottom: 1px solid #f1f5f9;
    padding: 1rem;
    transition: all 0.15s ease;
    cursor: pointer;
    text-decoration: none !important;
    display: block;
    color: inherit;
}
.mail-item:hover {
    background-color: #f8fafc;
}
.mail-item.active {
    background-color: #FFFBEB;
    border-left: 4px solid #FFDF1E;
}
.mail-item.unread {
    font-weight: 600;
}
.mail-reader-col {
    padding: 1.5rem 2rem;
    background-color: #ffffff;
    max-height: 720px;
    overflow-y: auto;
}
@media (max-width: 767.98px) {
    .mail-reader-col {
        padding: 1rem;
        max-height: none;
    }
    .mail-list-col {
        max-height: 360px !important;
    }
}
.message-body-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 1.5rem;
    font-size: 0.95rem;
    line-height: 1.6;
    color: #1e293b;
    white-space: pre-wrap;
    word-break: break-word;
}
.channel-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.45rem 0.9rem;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
}
</style>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h3 fw-bold text-dark mb-0">Почтовый центр: Обращения с сайта</h1>
        <small class="text-muted">Удобный просмотр и мгновенная обработка входящих запросов по всем каналам связи</small>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0 gap-2">
        <?php if ($new_count > 0): ?>
            <a href="contact_requests.php?action=process_all" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-semibold" onclick="return confirm('Отметить все новые обращения как обработанные?');">
                <i class="bi bi-check2-all me-1"></i> Отметить все прочитанными
            </a>
        <?php endif; ?>
    </div>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        <?php
        switch ($_GET['msg']) {
            case 'processed': echo "Обращение успешно помечено как обработанное."; break;
            case 'unprocessed': echo "Обращение возвращено в статус нового."; break;
            case 'deleted': echo "Обращение удалено."; break;
            case 'notes_saved': echo "Заметка администратора сохранена."; break;
            case 'all_processed': echo "Все обращения отмечены как обработанные."; break;
            default: echo "Операция успешно выполнена.";
        }
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Панель фильтров и поиска -->
<div class="card border-0 shadow-sm rounded-4 p-3 bg-white mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <!-- Вкладки фильтров -->
        <div class="d-flex gap-2">
            <a href="contact_requests.php?filter=all" class="btn btn-sm rounded-pill <?= $filter === 'all' ? 'btn-dark fw-bold' : 'btn-outline-secondary' ?>">
                Все (<?= $total_count ?>)
            </a>
            <a href="contact_requests.php?filter=new" class="btn btn-sm rounded-pill <?= $filter === 'new' ? 'btn-danger fw-bold' : 'btn-outline-danger' ?>">
                <i class="bi bi-envelope-fill me-1"></i> Новые (<?= $new_count ?>)
            </a>
            <a href="contact_requests.php?filter=processed" class="btn btn-sm rounded-pill <?= $filter === 'processed' ? 'btn-success fw-bold' : 'btn-outline-success' ?>">
                <i class="bi bi-check2-circle me-1"></i> Обработанные (<?= $processed_count ?>)
            </a>
        </div>

        <!-- Поиск -->
        <form method="GET" action="contact_requests.php" class="d-flex gap-2" style="max-width: 380px; width: 100%;">
            <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
            <div class="input-group input-group-sm">
                <input type="text" name="search" class="form-control" placeholder="Поиск по имени, email, телефону..." value="<?= htmlspecialchars($search) ?>">
                <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
            </div>
            <?php if (!empty($search)): ?>
                <a href="contact_requests.php?filter=<?= htmlspecialchars($filter) ?>" class="btn btn-sm btn-outline-dark" title="Сбросить поиск"><i class="bi bi-x-lg"></i></a>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Почтовый интерфейс в 2 колонки -->
<div class="mail-container mb-4">
    <div class="row g-0">
        <!-- Левая колонка: Список сообщений (Inbox) -->
        <div class="col-lg-5 col-xl-4 mail-list-col">
            <?php if (!empty($requests)): ?>
                <?php foreach ($requests as $r): ?>
                    <?php 
                    $is_active = $selected_request && (int)$selected_request['id'] === (int)$r['id'];
                    $is_unread = $r['status'] !== 'processed';
                    ?>
                    <a href="contact_requests.php?filter=<?= urlencode($filter) ?>&search=<?= urlencode($search) ?>&selected=<?= $r['id'] ?>" 
                       class="mail-item <?= $is_active ? 'active' : '' ?> <?= $is_unread ? 'unread' : '' ?>">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <div class="d-flex align-items-center gap-2 text-truncate pe-2">
                                <?php if ($is_unread): ?>
                                    <span class="badge bg-danger rounded-circle p-1" style="width: 8px; height: 8px;" title="Новое сообщение"></span>
                                <?php else: ?>
                                    <i class="bi bi-check2-circle text-success small"></i>
                                <?php endif; ?>
                                <span class="fw-bold text-dark text-truncate">
                                    <?= htmlspecialchars($r['name'] ?: 'Клиент') ?>
                                </span>
                            </div>
                            <small class="text-muted text-nowrap" style="font-size: 0.75rem;">
                                <?= date('d.m H:i', strtotime($r['created_at'])) ?>
                            </small>
                        </div>

                        <!-- Сниппет сообщения -->
                        <div class="text-muted small text-truncate mb-1" style="max-width: 95%;">
                            <?= htmlspecialchars(mb_strimwidth($r['message'], 0, 80, "...")) ?>
                        </div>

                        <!-- Контакты и статус -->
                        <div class="d-flex justify-content-between align-items-center small text-muted" style="font-size: 0.75rem;">
                            <span>#<?= (int)$r['id'] ?> • <?= htmlspecialchars($r['phone'] ?: $r['email']) ?></span>
                            <?php if ($is_unread): ?>
                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2">Новое</span>
                            <?php else: ?>
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2">Обработано</span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1 text-muted opacity-50 d-block mb-2"></i>
                    Обращений не найдено
                </div>
            <?php endif; ?>
        </div>

        <!-- Правая колонка: Просмотр сообщения (Mail Reader) -->
        <div class="col-lg-7 col-xl-8 mail-reader-col">
            <?php if ($selected_request): ?>
                <?php 
                $clean_phone = preg_replace('/[^0-9]/', '', $selected_request['phone']);
                if (strlen($clean_phone) === 11 && $clean_phone[0] === '8') {
                    $clean_phone = '7' . substr($clean_phone, 1);
                }
                ?>
                <div class="d-flex justify-content-between align-items-start mb-3 border-bottom pb-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h2 class="h4 fw-bold text-dark mb-0"><?= htmlspecialchars($selected_request['name']) ?></h2>
                            <?php if ($selected_request['status'] !== 'processed'): ?>
                                <span class="badge bg-danger rounded-pill px-2">Новое</span>
                            <?php else: ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2">
                                    <i class="bi bi-check-lg"></i> Обработано
                                </span>
                            <?php endif; ?>
                            <span class="badge bg-light text-muted border">Обращение #<?= (int)$selected_request['id'] ?></span>
                        </div>
                        <small class="text-muted">
                            <i class="bi bi-clock me-1"></i> Получено: <?= date('d.m.Y в H:i', strtotime($selected_request['created_at'])) ?>
                            <?php if (!empty($selected_request['processed_at'])): ?>
                                • Обработано: <?= date('d.m.Y H:i', strtotime($selected_request['processed_at'])) ?>
                            <?php endif; ?>
                        </small>
                    </div>

                    <!-- Быстрые действия статуса -->
                    <div class="d-flex gap-2">
                        <?php if ($selected_request['status'] !== 'processed'): ?>
                            <a href="contact_requests.php?action=process&id=<?= $selected_request['id'] ?>&filter=<?= urlencode($filter) ?>&search=<?= urlencode($search) ?>" 
                               class="btn btn-sm btn-success rounded-pill px-3 fw-bold">
                                <i class="bi bi-check-lg me-1"></i> Отметить обработанным
                            </a>
                        <?php else: ?>
                            <a href="contact_requests.php?action=unprocess&id=<?= $selected_request['id'] ?>&filter=<?= urlencode($filter) ?>&search=<?= urlencode($search) ?>" 
                               class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Вернуть в новые
                            </a>
                        <?php endif; ?>
                        <a href="contact_requests.php?action=delete&id=<?= $selected_request['id'] ?>" 
                           class="btn btn-sm btn-outline-danger rounded-pill px-2" 
                           onclick="return confirm('Удалить обращение безвозвратно?');" title="Удалить">
                            <i class="bi bi-trash"></i>
                        </a>
                    </div>
                </div>

                <!-- Панель каналов связи клиента -->
                <div class="p-3 bg-light rounded-3 border mb-4">
                    <div class="small fw-bold text-muted text-uppercase mb-2">Каналы связи с клиентом:</div>
                    <div class="d-flex flex-wrap gap-2">
                        <?php if (!empty($selected_request['phone'])): ?>
                            <a href="tel:<?= htmlspecialchars($selected_request['phone']) ?>" class="channel-btn btn btn-sm btn-dark">
                                <i class="bi bi-telephone-fill text-warning"></i> <?= htmlspecialchars($selected_request['phone']) ?>
                            </a>
                            <a href="https://wa.me/<?= $clean_phone ?>" target="_blank" class="channel-btn btn btn-sm btn-success">
                                <i class="bi bi-whatsapp"></i> Написать в WhatsApp
                            </a>
                            <a href="https://t.me/+<?= $clean_phone ?>" target="_blank" class="channel-btn btn btn-sm btn-info text-white">
                                <i class="bi bi-telegram"></i> Поиск в Telegram
                            </a>
                        <?php endif; ?>

                        <?php if (!empty($selected_request['email'])): ?>
                            <a href="mailto:<?= htmlspecialchars($selected_request['email']) ?>?subject=Ответ на обращение в магазин «Территория Тепла»" class="channel-btn btn btn-sm btn-outline-dark">
                                <i class="bi bi-envelope-fill text-primary"></i> Ответить по Email (<?= htmlspecialchars($selected_request['email']) ?>)
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Тело сообщения -->
                <div class="mb-4">
                    <div class="small fw-bold text-muted text-uppercase mb-2">Текст обращения:</div>
                    <div class="message-body-box">
                        <?= nl2br(htmlspecialchars($selected_request['message'])) ?>
                    </div>
                </div>

                <!-- Заметки администратора / менеджера -->
                <div class="card border rounded-3 p-3 bg-light">
                    <form method="POST" action="contact_requests.php">
                        <input type="hidden" name="save_notes" value="1">
                        <input type="hidden" name="id" value="<?= (int)$selected_request['id'] ?>">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="small fw-bold text-dark text-uppercase mb-0">
                                <i class="bi bi-journal-text text-warning me-1"></i> Служебные заметки менеджера:
                            </label>
                            <button type="submit" class="btn btn-sm btn-dark rounded-pill px-3">
                                Сохранить заметку
                            </button>
                        </div>
                        <textarea name="admin_notes" class="form-control form-control-sm" rows="3" placeholder="Например: Созвонились 11.11, договорились о выезде замерщика на субботу..."><?= htmlspecialchars($selected_request['admin_notes'] ?? '') ?></textarea>
                    </form>
                </div>

            <?php else: ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-envelope-open fs-1 text-muted opacity-50 d-block mb-3"></i>
                    <h5>Выберите обращение слева</h5>
                    <p class="small text-muted mb-0">Кликните по любой заявке из списка, чтобы прочесть полный текст и связаться с клиентом.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
