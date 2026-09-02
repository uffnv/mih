<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Удаление
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT file_path FROM company_documents WHERE id = ?");
    $stmt->execute([$id]);
    $file = $stmt->fetchColumn();
    
    if ($file && file_exists(__DIR__ . '/../' . ltrim($file, '/'))) {
        unlink(__DIR__ . '/../' . ltrim($file, '/'));
    }
    
    $pdo->prepare("DELETE FROM company_documents WHERE id = ?")->execute([$id]);
    header("Location: documents.php?msg=deleted");
    exit;
}

$page_title = 'Управление документами';
require_once 'admin_header.php';

$stmt = $pdo->query("SELECT * FROM company_documents ORDER BY created_at DESC");
$docs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h3 fw-bold text-dark mb-0">Сертификаты и документы (<?= count($docs) ?>)</h1>
        <small class="text-muted">Лицензии, свидетельства, дипломы и сертификаты соответствия</small>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="document_edit.php" class="btn btn-warning fw-bold rounded-pill shadow-sm px-3">
            <i class="bi bi-file-earmark-plus me-1"></i> Добавить документ
        </a>
    </div>
</div>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
        <i class="bi bi-check-circle me-2"></i> Документ успешно удален.
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
                        <th class="py-3">Название документа</th>
                        <th class="py-3">Файл / Скан</th>
                        <th class="py-3">Дата загрузки</th>
                        <th class="text-end pe-3 py-3">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($docs)): ?>
                        <?php foreach ($docs as $doc): 
                            $file_url = !empty($doc['file_path']) ? ('../' . ltrim($doc['file_path'], '/')) : '';
                        ?>
                        <tr>
                            <td class="ps-3 fw-bold text-dark">#<?= $doc['id']; ?></td>
                            <td class="fw-bold text-dark">
                                <i class="bi bi-file-earmark-pdf text-danger me-2"></i><?= htmlspecialchars($doc['title']); ?>
                            </td>
                            <td>
                                <?php if (!empty($file_url)): ?>
                                    <a href="<?= htmlspecialchars($file_url); ?>" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                                        <i class="bi bi-eye me-1"></i> Открыть
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">Нет прикрепленного файла</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-nowrap small text-muted"><?= date('d.m.Y', strtotime($doc['created_at'])); ?></td>
                            <td class="text-end pe-3 text-nowrap">
                                <a href="document_edit.php?id=<?= $doc['id']; ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3 me-1" title="Редактировать">
                                    <i class="bi bi-pencil me-1"></i> Изменить
                                </a>
                                <a href="documents.php?delete=<?= $doc['id']; ?>" class="btn btn-sm btn-outline-danger rounded-pill px-2" onclick="return confirm('Удалить документ?');" title="Удалить">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-file-earmark-text display-4 opacity-50 d-block mb-2"></i>
                                Документы пока не загружены
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
