<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// Фильтры
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'new';

// SQL
$sql = "SELECT * FROM products WHERE is_active = 1";
$params = [];

if ($category_id) {
    $sql .= " AND category_id = ?";
    $params[] = $category_id;
}
if ($search) {
    $sql .= " AND (name LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Сортировка
switch ($sort) {
    case 'price_asc': $sql .= " ORDER BY price ASC"; break;
    case 'price_desc': $sql .= " ORDER BY price DESC"; break;
    default: $sql .= " ORDER BY id DESC"; break; 
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Категории для сайдбара
$cats_stmt = $pdo->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order ASC");
$categories = $cats_stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Каталог товаров';
require_once __DIR__ . '/includes/header.php';
?>

<main class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <!-- Сайдбар -->
            <div class="col-lg-3 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold py-3">Категории</div>
                    <div class="list-group list-group-flush">
                        <a href="catalog.php" class="list-group-item list-group-item-action <?= !$category_id ? 'active bg-warning border-warning text-dark' : '' ?>">
                            Все товары
                        </a>
                        <?php foreach ($categories as $cat): ?>
                            <a href="catalog.php?category=<?= $cat['id']; ?>" 
                               class="list-group-item list-group-item-action <?= $category_id == $cat['id'] ? 'active bg-warning border-warning text-dark' : '' ?>">
                                <?= htmlspecialchars($cat['name']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Товары -->
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 fw-bold mb-0">Каталог</h1>
                    
                    <form class="d-flex gap-2" method="GET">
                        <?php if ($category_id): ?><input type="hidden" name="category" value="<?= $category_id ?>"><?php endif; ?>
                        <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="new" <?= $sort == 'new' ? 'selected' : '' ?>>Новинки</option>
                            <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Сначала дешевые</option>
                            <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Сначала дорогие</option>
                        </select>
                    </form>
                </div>

                <?php if (empty($products)): ?>
                    <div class="alert alert-warning text-center">Товары не найдены.</div>
                <?php else: ?>
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                        <?php foreach ($products as $prod): ?>
                            <?php 
                                // Фото
                                $stmt = $pdo->prepare("SELECT image_path FROM product_images WHERE product_id = ? AND is_main = 1 LIMIT 1");
                                $stmt->execute([$prod['id']]);
                                $img = $stmt->fetchColumn() ?: 'images/no-photo.jpg';
                            ?>
                            <div class="col">
                                <div class="card h-100 border-0 shadow-sm product-card position-relative overflow-hidden">
                                    <?php if ($prod['old_price'] > $prod['price']): ?>
                                        <span class="position-absolute top-0 end-0 m-2 badge bg-warning text-dark shadow">-<?= round(($prod['old_price']-$prod['price'])/$prod['old_price']*100) ?>%</span>
                                    <?php endif; ?>
                                    
                                    <div class="p-4 d-flex align-items-center justify-content-center bg-white" style="height: 220px;">
                                        <img src="<?= htmlspecialchars($img); ?>" alt="<?= htmlspecialchars($prod['name']); ?>" class="img-fluid" style="max-height: 180px;">
                                    </div>
                                    
                                    <div class="card-body d-flex flex-column pt-3">
                                        <h5 class="card-title fs-6 fw-bold mb-2">
                                            <a href="product.php?id=<?= $prod['id']; ?>" class="text-decoration-none text-dark stretched-link">
                                                <?= htmlspecialchars($prod['name']); ?>
                                            </a>
                                        </h5>
                                        
                                        <div class="mt-auto">
                                            <div class="mb-3">
                                                <?php if ($prod['old_price'] > $prod['price']): ?>
                                                    <small class="text-muted text-decoration-line-through me-2"><?= number_format($prod['old_price'], 0, '.', ' '); ?> ₽</small>
                                                <?php endif; ?>
                                                <span class="text-warning fw-bold fs-5"><?= number_format($prod['price'], 0, '.', ' '); ?> ₽</span>
                                            </div>
                                            <!-- Кнопка с классом btn-add-cart и атрибутом data-product-id -->
                                            <button class="btn btn-warning w-100 fw-bold position-relative z-index-2 btn-add-cart" 
                                                    style="z-index: 2;" 
                                                    data-product-id="<?= $prod['id']; ?>">
                                                В корзину
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
