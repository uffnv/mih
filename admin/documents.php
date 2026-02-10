<?php
require_once '../includes/config.php';
require_once '../includes/functions.php'; // Подключаем функции, если getDocuments там
require_once 'admin_header.php';

// Удаление
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // Получаем путь к файлу
    $stmt = $pdo->prepare("SELECT file_path FROM company_documents WHERE id = ?");
    $stmt->execute([$id]);
    $file = $stmt->fetchColumn();
    
    if ($file && file_exists(__DIR__ . '/..' . $file)) {
        unlink(__DIR__ . '/..' . $file);
    }
    
    $pdo->prepare("DELETE FROM company_documents WHERE id = ?")->execute([$id]);
    header("Location: documents.php?msg=deleted");
    exit;
}

// Используем таблицу company_documents или функцию getDocuments()
if (function_exists('getDocuments')) {
    $docs = getDocuments();
} else {
    // Фолбэк, если функции нет
    $stmt = $pdo->query("SELECT * FROM company_documents ORDER BY created_at DESC");
    $docs = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Документы и грамоты</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="document_edit.php" class="btn btn-success">
            <i class="bi bi-plus-lg"></i> Добавить документ
        </a>
    </div>
</div>

<?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
    <div class="alert alert-success alert-dismissible fade show">
        Документ удален.
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
                        <th>Файл</th>
                        <th>Дата</th>
                        <th class="text-end">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($docs)): ?>
                        <?php foreach ($docs as $doc): ?>
                        <tr>
                            <td><?= $doc['id']; ?></td>
                            <td class="fw-bold"><?= htmlspecialchars($doc['title']); ?></td>
                            <td>
                                <?php if (!empty($doc['file_path'])): ?>
                                    <a href="../<?= htmlspecialchars($doc['file_path']); ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-eye"></i> Просмотр
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">Нет файла</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-nowrap"><?= date('d.m.Y', strtotime($doc['created_at'])); ?></td>
                            <td class="text-end text-nowrap">
                                <a href="document_edit.php?id=<?= $doc['id']; ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                                <a href="documents.php?delete=<?= $doc['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить документ?');"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted">Документов пока нет</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
