<?php
require_once __DIR__ . '/admin_auth.php';
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Удаление (с защитой CSRF и Path Traversal)
if (isset($_GET['delete'])) {
    if (!verifyCsrfToken($_GET['csrf_token'] ?? '')) {
        die('Ошибка проверки CSRF токена');
    }
    $id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("SELECT file_path FROM company_documents WHERE id = ?");
        $stmt->execute([$id]);
        $file = $stmt->fetchColumn();
        
        if ($file) {
            $realFile = realpath(__DIR__ . '/../' . ltrim($file, '/'));
            $allowedDir = realpath(__DIR__ . '/../images/documents');
            if ($realFile && $allowedDir && strpos($realFile, $allowedDir) === 0 && file_exists($realFile)) {
                @unlink($realFile);
            }
        }
        
        $pdo->prepare("DELETE FROM company_documents WHERE id = ?")->execute([$id]);
    } catch (Throwable $e) {}
    header("Location: documents.php?msg=deleted");
    exit;
}

$page_title = 'Управление документами и сертификатами';
require_once 'admin_header.php';

$cat_filter = $_GET['cat'] ?? 'all';
$search = trim($_GET['search'] ?? '');

$where = [];
$params = [];

if ($cat_filter !== 'all') {
    $where[] = "category = ?";
    $params[] = $cat_filter;
}
if (!empty($search)) {
    $where[] = "title LIKE ?";
    $params[] = "%$search%";
}

$whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

$docs = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM company_documents $whereSql ORDER BY created_at DESC");
    $stmt->execute($params);
    $docs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log("Error loading company_documents: " . $e->getMessage());
}

$total_docs = 0;
try {
    $total_docs = (int)$pdo->query("SELECT COUNT(*) FROM company_documents")->fetchColumn();
} catch (Throwable $e) {}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h3 fw-bold text-dark mb-0">Сертификаты и документы (<?= count($docs) ?>)</h1>
        <small class="text-muted">Реестр паспортов изделий, сертификатов качества ГОСТ и партнерских соглашений</small>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0 gap-2">
        <a href="document_edit.php" class="btn btn-warning fw-bold rounded-pill shadow-sm px-3">
            <i class="bi bi-cloud-arrow-up-fill me-1"></i> Пакетная загрузка / Добавить
        </a>
    </div>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        <?php
        switch ($_GET['msg']) {
            case 'batch_success': 
                $cnt = (int)($_GET['count'] ?? 0);
                echo "Успешно загружен пакет из $cnt документов!"; 
                break;
            case 'saved': echo "Документ сохранен."; break;
            case 'deleted': echo "Документ удален."; break;
            default: echo "Операция выполнена успешно.";
        }
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Панель категорий и поиска -->
<div class="card border-0 shadow-sm rounded-4 p-3 bg-white mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div class="d-flex flex-wrap gap-2">
            <a href="documents.php?cat=all" class="btn btn-sm rounded-pill <?= $cat_filter === 'all' ? 'btn-dark fw-bold' : 'btn-outline-secondary' ?>">
                Все (<?= $total_docs ?>)
            </a>
            <a href="documents.php?cat=<?= urlencode('Сертификаты соответствия') ?>" class="btn btn-sm rounded-pill <?= $cat_filter === 'Сертификаты соответствия' ? 'btn-warning text-dark fw-bold' : 'btn-outline-warning text-dark' ?>">
                Сертификаты ГОСТ
            </a>
            <a href="documents.php?cat=<?= urlencode('Паспорта и инструкции') ?>" class="btn btn-sm rounded-pill <?= $cat_filter === 'Паспорта и инструкции' ? 'btn-primary fw-bold' : 'btn-outline-primary' ?>">
                Паспорта котлов/печей
            </a>
            <a href="documents.php?cat=<?= urlencode('Дилерские соглашения') ?>" class="btn btn-sm rounded-pill <?= $cat_filter === 'Дилерские соглашения' ? 'btn-success fw-bold' : 'btn-outline-success' ?>">
                Дилерские
            </a>
            <a href="documents.php?cat=<?= urlencode('Лицензии и допуски') ?>" class="btn btn-sm rounded-pill <?= $cat_filter === 'Лицензии и допуски' ? 'btn-info text-white fw-bold' : 'btn-outline-info' ?>">
                Лицензии
            </a>
        </div>

        <form method="GET" action="documents.php" class="d-flex gap-2" style="max-width: 320px; width: 100%;">
            <input type="hidden" name="cat" value="<?= htmlspecialchars($cat_filter) ?>">
            <div class="input-group input-group-sm">
                <input type="text" name="search" class="form-control" placeholder="Поиск документа..." value="<?= htmlspecialchars($search) ?>">
                <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
            </div>
            <?php if (!empty($search)): ?>
                <a href="documents.php?cat=<?= htmlspecialchars($cat_filter) ?>" class="btn btn-sm btn-outline-dark" title="Сбросить"><i class="bi bi-x-lg"></i></a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3" style="width: 50px;">ID</th>
                        <th class="py-3">Тип</th>
                        <th class="py-3">Название документа</th>
                        <th class="py-3">Категория</th>
                        <th class="py-3">Размер / Файл</th>
                        <th class="py-3">Дата загрузки</th>
                        <th class="text-end pe-3 py-3">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($docs)): ?>
                        <?php foreach ($docs as $doc): 
                            $file_url = !empty($doc['file_path']) ? ('../' . ltrim($doc['file_path'], '/')) : '';
                            $ext = strtolower(pathinfo($doc['file_path'] ?? '', PATHINFO_EXTENSION));
                            
                            $icon = 'bi-file-earmark text-muted';
                            $type_badge = 'bg-secondary';
                            if ($ext === 'pdf') {
                                $icon = 'bi-file-earmark-pdf-fill text-danger';
                                $type_badge = 'bg-danger';
                            } elseif (in_array($ext, ['doc', 'docx'])) {
                                $icon = 'bi-file-earmark-word-fill text-primary';
                                $type_badge = 'bg-primary';
                            } elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                                $icon = 'bi-file-earmark-image-fill text-success';
                                $type_badge = 'bg-success';
                            }

                            $sizeText = '';
                            if (!empty($doc['file_size'])) {
                                $kb = round($doc['file_size'] / 1024, 1);
                                if ($kb > 1024) {
                                    $sizeText = round($kb / 1024, 2) . ' МБ';
                                } else {
                                    $sizeText = $kb . ' КБ';
                                }
                            }
                        ?>
                        <tr>
                            <td class="ps-3 fw-bold text-dark">#<?= $doc['id']; ?></td>
                            <td>
                                <i class="bi <?= $icon ?> fs-4"></i>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($doc['title']); ?></div>
                                <small class="text-muted"><?= htmlspecialchars(basename($doc['file_path'] ?? '')) ?></small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <?= htmlspecialchars($doc['category'] ?? 'Сертификаты') ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($file_url)): ?>
                                    <a href="<?= htmlspecialchars($file_url); ?>" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                                        <i class="bi bi-download me-1"></i> Скачать <?= $sizeText ? "($sizeText)" : "" ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">Нет файла</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-nowrap small text-muted"><?= date('d.m.Y H:i', strtotime($doc['created_at'])); ?></td>
                            <td class="text-end pe-3 text-nowrap">
                                <a href="document_edit.php?id=<?= $doc['id']; ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3 me-1" title="Редактировать">
                                    <i class="bi bi-pencil me-1"></i> Изменить
                                </a>
                                <a href="documents.php?delete=<?= $doc['id']; ?>&csrf_token=<?= generateCsrfToken(); ?>" class="btn btn-sm btn-outline-danger rounded-pill px-2" onclick="return confirm('Удалить документ?');" title="Удалить">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-file-earmark-text display-4 opacity-50 d-block mb-2"></i>
                                Документы не найдены
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
