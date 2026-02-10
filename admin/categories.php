<?php
require_once '../includes/config.php';
require_once 'admin_header.php';

// Удаление
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // Проверяем, есть ли товары
    $check = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
    $check->execute([$id]);
    if ($check->fetchColumn() > 0) {
        $error = "Нельзя удалить категорию, в которой есть товары.";
    } else {
        $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$id]);
        header("Location: categories.php?msg=deleted");
        exit;
    }
}

$stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Категории</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="category_edit.php" class="btn btn-success">
            <i class="bi bi-plus-lg"></i> Добавить категорию
        </a>
    </div>
</div>

<?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
    <div class="alert alert-success alert-dismissible fade show">
        Категория удалена.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= $error; ?>
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
                        <th>Slug (URL)</th>
                        <th class="text-end">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?= $cat['id']; ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($cat['name']); ?></td>
                        <td class="text-muted font-monospace"><?= htmlspecialchars($cat['slug']); ?></td>
                        <td class="text-end text-nowrap">
                            <a href="category_edit.php?id=<?= $cat['id']; ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                            <a href="categories.php?delete=<?= $cat['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить?');"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
