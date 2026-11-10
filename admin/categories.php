<?php
require_once '../includes/config.php';

// Удаление (с защитой CSRF)
if (isset($_GET['delete'])) {
    if (!verifyCsrfToken($_GET['csrf_token'] ?? '')) {
        die('Ошибка проверки CSRF токена');
    }
    $id = (int)$_GET['delete'];
    // Проверяем, есть ли товары
    $check = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
    $check->execute([$id]);
    if ($check->fetchColumn() > 0) {
        $error = "Нельзя удалить категорию, в которой есть привязанные товары.";
    } else {
        $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$id]);
        header("Location: categories.php?msg=deleted");
        exit;
    }
}

$page_title = 'Категории товаров';
require_once 'admin_header.php';

$stmt = $pdo->query("
    SELECT c.*, COUNT(p.id) as products_count 
    FROM categories c 
    LEFT JOIN products p ON p.category_id = c.id 
    GROUP BY c.id 
    ORDER BY c.name ASC
");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h3 fw-bold text-dark mb-0">Категории (<?= count($categories) ?>)</h1>
        <small class="text-muted">Управление разделами каталога продукции</small>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="category_edit.php" class="btn btn-warning fw-bold rounded-pill shadow-sm px-3">
            <i class="bi bi-plus-lg me-1"></i> Добавить категорию
        </a>
    </div>
</div>

<?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
        <i class="bi bi-check-circle me-2"></i> Категория успешно удалена.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i> <?= $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3" style="width: 60px;">ID</th>
                        <th class="py-3">Название категории</th>
                        <th class="py-3">Slug (URL)</th>
                        <th class="py-3">Товаров</th>
                        <th class="text-end pe-3 py-3">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td class="ps-3 fw-bold text-dark">#<?= $cat['id']; ?></td>
                            <td class="fw-bold text-dark"><?= htmlspecialchars($cat['name']); ?></td>
                            <td><span class="badge bg-light text-secondary font-monospace border"><?= htmlspecialchars($cat['slug']); ?></span></td>
                            <td>
                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle rounded-pill px-2">
                                    <?= (int)$cat['products_count'] ?> шт.
                                </span>
                            </td>
                            <td class="text-end pe-3 text-nowrap">
                                <a href="category_edit.php?id=<?= $cat['id']; ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3 me-1" title="Редактировать">
                                    <i class="bi bi-pencil me-1"></i> Изменить
                                </a>
                                <a href="categories.php?delete=<?= $cat['id']; ?>&csrf_token=<?= generateCsrfToken(); ?>" class="btn btn-sm btn-outline-danger rounded-pill px-2" onclick="return confirm('Удалить категорию?');" title="Удалить">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-folder display-4 opacity-50 d-block mb-2"></i>
                                Категорий пока нет
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
