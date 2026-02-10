<?php
require_once '../includes/config.php';
require_once 'admin_header.php';

// Удаление
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("UPDATE products SET is_active = 0 WHERE id = ?")->execute([$id]);
    header("Location: products.php?msg=deleted");
    exit;
}

$stmt = $pdo->query("
    SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    ORDER BY p.id DESC
");
$products = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Товары</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="product_edit.php" class="btn btn-success">
            <i class="bi bi-plus-lg"></i> Добавить товар
        </a>
    </div>
</div>

<?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        Товар удален (скрыт).
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
                        <th style="width: 80px;">Фото</th>
                        <th>Название</th>
                        <th>Цена</th>
                        <th>Категория</th>
                        <th>Статус</th>
                        <th class="text-end">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $prod): ?>
                    <tr>
                        <td><?= $prod['id']; ?></td>
                        <td>
                            <?php if (!empty($prod['image_path'])): ?>
                                <img src="<?= htmlspecialchars($prod['image_path']); ?>" alt="" width="50" height="50" class="rounded object-fit-cover">
                            <?php else: ?>
                                <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted" style="width: 50px; height: 50px;">No</div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="fw-bold text-wrap" style="min-width: 150px;"><?= htmlspecialchars($prod['name']); ?></div>
                            <small class="text-muted">SKU: <?= htmlspecialchars($prod['sku'] ?? ''); ?></small>
                        </td>
                        <td class="text-nowrap"><?= number_format($prod['price'], 0, ',', ' '); ?> ₽</td>
                        <td><?= htmlspecialchars($prod['category_name'] ?? '-'); ?></td>
                        <td>
                            <?php if ($prod['is_active']): ?>
                                <span class="badge bg-success">Активен</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Скрыт</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end text-nowrap">
                            <a href="product_edit.php?id=<?= $prod['id']; ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                            <a href="products.php?delete=<?= $prod['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить?');"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
