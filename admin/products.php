<?php
require_once '../includes/config.php';
$page_title = 'Управление товарами';
require_once 'admin_header.php';

// Удаление / Восстановление
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("UPDATE products SET is_active = 0 WHERE id = ?")->execute([$id]);
    header("Location: products.php?msg=deleted");
    exit;
}
if (isset($_GET['restore'])) {
    $id = (int)$_GET['restore'];
    $pdo->prepare("UPDATE products SET is_active = 1 WHERE id = ?")->execute([$id]);
    header("Location: products.php?msg=restored");
    exit;
}

// Категории для фильтра
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY sort_order ASC, name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Фильтры
$search = trim($_GET['search'] ?? '');
$cat_id = (int)($_GET['category_id'] ?? 0);
$stock_filter = $_GET['stock'] ?? 'all';

$sql = "
    SELECT p.*, c.name as category_name,
           COALESCE(
               (SELECT image_path FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1),
               (SELECT image_path FROM product_images WHERE product_id = p.id LIMIT 1),
               p.image_path
           ) as primary_image
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE 1=1
";
$params = [];

if (!empty($search)) {
    $sql .= " AND (p.name LIKE ? OR p.sku LIKE ? OR p.description LIKE ?) ";
    $sTerm = "%$search%";
    $params[] = $sTerm;
    $params[] = $sTerm;
    $params[] = $sTerm;
}

if ($cat_id > 0) {
    $sql .= " AND p.category_id = ? ";
    $params[] = $cat_id;
}

if ($stock_filter === 'in_stock') {
    $sql .= " AND p.stock_quantity > 0 ";
} elseif ($stock_filter === 'out_of_stock') {
    $sql .= " AND (p.stock_quantity = 0 OR p.stock_quantity IS NULL) ";
}

$sql .= " ORDER BY p.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h3 fw-bold text-dark mb-0">Товары (<?= count($products) ?>)</h1>
        <small class="text-muted">Каталог товаров интернет-магазина «Территория Тепла»</small>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="product_edit.php" class="btn btn-warning fw-bold rounded-pill shadow-sm px-3">
            <i class="bi bi-plus-lg me-1"></i> Добавить товар
        </a>
    </div>
</div>

<?php if (isset($_GET['msg'])): ?>
    <?php if ($_GET['msg'] == 'deleted'): ?>
        <div class="alert alert-warning alert-dismissible fade show rounded-4 mb-4" role="alert">
            <i class="bi bi-info-circle me-2"></i> Товар скрыт из каталога.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($_GET['msg'] == 'restored'): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
            <i class="bi bi-check-circle me-2"></i> Товар активирован.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Панель поиска и фильтрации -->
<div class="card border-0 shadow-sm rounded-4 bg-white p-3 mb-4">
    <form method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Поиск по названию или артикулу..." value="<?= htmlspecialchars($search) ?>">
            </div>
        </div>
        <div class="col-md-3">
            <select name="category_id" class="form-select">
                <option value="0">Все категории</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $cat_id == $cat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="stock" class="form-select">
                <option value="all" <?= $stock_filter === 'all' ? 'selected' : '' ?>>Любой остаток</option>
                <option value="in_stock" <?= $stock_filter === 'in_stock' ? 'selected' : '' ?>>В наличии (>0)</option>
                <option value="out_of_stock" <?= $stock_filter === 'out_of_stock' ? 'selected' : '' ?>>Нет на складе</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-dark fw-bold rounded-pill w-100">Найти</button>
            <?php if (!empty($search) || $cat_id > 0 || $stock_filter !== 'all'): ?>
                <a href="products.php" class="btn btn-outline-secondary rounded-pill px-3" title="Сбросить"><i class="bi bi-x-lg"></i></a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3" style="width: 60px;">ID</th>
                        <th class="py-3" style="width: 70px;">Фото</th>
                        <th class="py-3">Название и артикул</th>
                        <th class="py-3">Категория</th>
                        <th class="py-3">Цена</th>
                        <th class="py-3">Наличие</th>
                        <th class="py-3">Статус</th>
                        <th class="text-end pe-3 py-3">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $prod): 
                            $img = $prod['primary_image'];
                            if ($img) $img = str_replace('../', '', $img);
                            $img_src = (!empty($img) && file_exists(__DIR__ . '/../' . $img)) ? '../' . $img : '../images/hero-bg.jpg';
                        ?>
                        <tr>
                            <td class="ps-3 fw-bold text-dark">#<?= $prod['id']; ?></td>
                            <td>
                                <img src="<?= htmlspecialchars($img_src); ?>" alt="" width="48" height="48" class="rounded-3 object-fit-cover border shadow-sm" onerror="this.src='../images/hero-bg.jpg'">
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($prod['name']); ?></div>
                                <small class="text-muted">Арт: <strong><?= htmlspecialchars($prod['sku'] ?? '—'); ?></strong></small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border"><?= htmlspecialchars($prod['category_name'] ?? 'Без категории'); ?></span>
                            </td>
                            <td class="text-nowrap fw-bold text-dark">
                                <?= number_format($prod['price'], 0, '.', ' '); ?> ₽
                                <?php if (!empty($prod['old_price']) && $prod['old_price'] > $prod['price']): ?>
                                    <div class="small text-muted text-decoration-line-through"><?= number_format($prod['old_price'], 0, '.', ' '); ?> ₽</div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php $qty = (int)($prod['stock_quantity'] ?? 0); ?>
                                <?php if ($qty > 5): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill"><?= $qty ?> шт.</span>
                                <?php elseif ($qty > 0): ?>
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill"><?= $qty ?> шт.</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary border rounded-pill">0 шт.</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($prod['is_active']): ?>
                                    <span class="badge bg-success rounded-pill px-2">Активен</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary rounded-pill px-2">Скрыт</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-3 text-nowrap">
                                <a href="product_edit.php?id=<?= $prod['id']; ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3 me-1" title="Редактировать">
                                    <i class="bi bi-pencil me-1"></i> Изменить
                                </a>
                                <?php if ($prod['is_active']): ?>
                                    <a href="products.php?delete=<?= $prod['id']; ?>" class="btn btn-sm btn-outline-danger rounded-pill px-2" onclick="return confirm('Скрыть товар из каталога?');" title="Скрыть">
                                        <i class="bi bi-eye-slash"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="products.php?restore=<?= $prod['id']; ?>" class="btn btn-sm btn-outline-success rounded-pill px-2" title="Активировать">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-box display-4 opacity-50 d-block mb-2"></i>
                                Товары по заданным критериям не найдены
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
