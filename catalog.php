<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// Фильтры
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'new';
$in_stock_only = isset($_GET['in_stock']) ? (int)$_GET['in_stock'] : 0;

// SQL с JOIN фото
$sql = "SELECT p.*, pi.image_path as main_image 
        FROM products p
        LEFT JOIN (
            SELECT product_id, MIN(image_path) as image_path 
            FROM product_images 
            WHERE is_main = 1 OR is_main IS NULL
            GROUP BY product_id
        ) pi ON pi.product_id = p.id
        WHERE p.is_active = 1";
$params = [];

if ($category_id) {
    $sql .= " AND p.category_id = ?";
    $params[] = $category_id;
}
if ($search) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ? OR p.sku LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($in_stock_only) {
    $sql .= " AND p.stock_quantity > 0";
}

// Сортировка
switch ($sort) {
    case 'price_asc': $sql .= " ORDER BY p.price ASC"; break;
    case 'price_desc': $sql .= " ORDER BY p.price DESC"; break;
    default: $sql .= " ORDER BY p.id DESC"; break;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Автоматическое наполнение базы, если товаров в каталоге нет
if (empty($products) && empty($search) && empty($category_id)) {
    try {
        $seed_file = __DIR__ . '/database_seed.sql';
        if (file_exists($seed_file)) {
            $seed_sql = file_get_contents($seed_file);
            $pdo->exec($seed_sql);
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (Exception $e) {}
}

// Категории для сайдбара
$cats_stmt = $pdo->query("SELECT c.*, COUNT(p.id) as product_count
                         FROM categories c
                         LEFT JOIN products p ON p.category_id = c.id AND p.is_active = 1
                         WHERE c.is_active = 1
                         GROUP BY c.id
                         ORDER BY c.sort_order ASC");
$categories = $cats_stmt->fetchAll(PDO::FETCH_ASSOC);

// Текущая категория
$current_category_name = 'Все товары';
if ($category_id) {
    foreach ($categories as $cat) {
        if ($cat['id'] == $category_id) {
            $current_category_name = $cat['name'];
            break;
        }
    }
}

$page_title = 'Каталог — ' . $current_category_name . ' | Территория Тепла';
require_once __DIR__ . '/includes/header.php';
?>

<main class="py-4 py-lg-5" style="background-color: var(--bg-page, #f8fafc);">
    <div class="container">

        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Главная</a></li>
                <li class="breadcrumb-item"><a href="catalog.php" class="text-decoration-none text-muted">Каталог</a></li>
                <?php if ($category_id): ?>
                    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page"><?= htmlspecialchars($current_category_name); ?></li>
                <?php endif; ?>
            </ol>
        </nav>

        <div class="row g-4">
            <!-- Сайдбар фильтров -->
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden sticky-lg-top" style="top: 85px; z-index: 10;">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: var(--dark-main, #181c20); color: #fff;">
                        <span class="fw-bold"><i class="bi bi-grid-fill text-warning me-2"></i> Категории</span>
                        <span class="badge bg-warning text-dark"><?= count($products); ?> тов.</span>
                    </div>

                    <!-- Поиск -->
                    <div class="p-3 border-bottom bg-white">
                        <form method="GET" action="catalog.php" class="input-group input-group-sm">
                            <?php if ($category_id): ?>
                                <input type="hidden" name="category" value="<?= $category_id ?>">
                            <?php endif; ?>
                            <input type="text" name="search" class="form-control" placeholder="Поиск по артикулу..." value="<?= htmlspecialchars($search); ?>">
                            <button class="btn btn-warning" type="submit" title="Найти">
                                <i class="bi bi-search"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Список категорий -->
                    <div class="list-group list-group-flush small">
                        <a href="catalog.php<?= $search ? '?search=' . urlencode($search) : '' ?>"
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-3 fw-semibold <?= !$category_id ? 'bg-light text-warning fw-bold' : 'text-dark' ?>">
                            <span>Все категории</span>
                        </a>
                        <?php foreach ($categories as $cat): ?>
                            <a href="catalog.php?category=<?= $cat['id']; ?><?= $search ? '&search=' . urlencode($search) : '' ?>"
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-3 <?= $category_id == $cat['id'] ? 'bg-light text-warning fw-bold' : 'text-dark' ?>">
                                <span><?= htmlspecialchars($cat['name']); ?></span>
                                <span class="badge rounded-pill <?= $category_id == $cat['id'] ? 'bg-warning text-dark' : 'bg-light text-muted border' ?>">
                                    <?= $cat['product_count']; ?>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <!-- Фильтр по наличию -->
                    <div class="p-3 bg-light border-top">
                        <form method="GET" action="catalog.php" id="stockFilterForm">
                            <?php if ($category_id): ?>
                                <input type="hidden" name="category" value="<?= $category_id ?>">
                            <?php endif; ?>
                            <?php if ($search): ?>
                                <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                            <?php endif; ?>
                            <div class="form-check small">
                                <input class="form-check-input" type="checkbox" name="in_stock" value="1" id="inStockCheck" <?= $in_stock_only ? 'checked' : '' ?> onchange="document.getElementById('stockFilterForm').submit()">
                                <label class="form-check-label text-dark fw-medium" for="inStockCheck">
                                    Только в наличии
                                </label>
                            </div>
                        </form>
                    </div>

                    <!-- Инфо-баннер склада -->
                    <div class="p-3 bg-white border-top small text-muted">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="bi bi-geo-alt-fill text-warning"></i>
                            <span><strong>Склад:</strong> с. Майма, ул. Энергетиков, 24</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-telephone-fill text-warning"></i>
                            <span>+7 (913) 691-44-43</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Список товаров -->
            <div class="col-lg-9">
                <!-- Панель управления и сортировка -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <h1 class="h4 fw-bold text-dark mb-0"><?= htmlspecialchars($current_category_name); ?></h1>
                            <small class="text-muted">Найдено позиций: <?= count($products); ?></small>
                        </div>

                        <!-- Сортировка -->
                        <div class="d-flex align-items-center gap-2">
                            <span class="small text-muted text-nowrap">Сортировка:</span>
                            <select class="form-select form-select-sm" style="width: 175px;" onchange="location = this.value;">
                                <option value="catalog.php?<?= http_build_query(array_merge($_GET, ['sort' => 'new'])) ?>" <?= $sort == 'new' ? 'selected' : '' ?>>Новинки</option>
                                <option value="catalog.php?<?= http_build_query(array_merge($_GET, ['sort' => 'price_asc'])) ?>" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Сначала дешевле</option>
                                <option value="catalog.php?<?= http_build_query(array_merge($_GET, ['sort' => 'price_desc'])) ?>" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Сначала дороже</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Сетка товаров -->
                <?php if (!empty($products)): ?>
                    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                        <?php foreach ($products as $prod): 
                            $img = $prod['main_image'];
                            if ($img) $img = str_replace('../', '', $img);
                            $img_src = (!empty($img) && file_exists(__DIR__ . '/' . $img)) ? $img : 'images/products/no-image.png';

                            $is_in_stock = isset($prod['stock_quantity']) && $prod['stock_quantity'] > 0;
                            $has_discount = !empty($prod['old_price']) && $prod['old_price'] > $prod['price'];
                        ?>
                            <div class="col">
                                <div class="product-card h-100 d-flex flex-column bg-white rounded-4 shadow-sm border-0 overflow-hidden" style="transition: transform 0.25s ease, box-shadow 0.25s ease;">
                                    <div class="product-img-box position-relative" style="height: 200px; background: #fff; padding: 1rem; border-bottom: 1px solid #f1f5f9;">
                                        <?php if ($has_discount): 
                                            $pct = round((($prod['old_price'] - $prod['price']) / $prod['old_price']) * 100);
                                        ?>
                                            <span class="position-absolute top-0 start-0 m-3 badge badge-discount rounded-pill">
                                                -<?= $pct ?>%
                                            </span>
                                        <?php endif; ?>

                                        <a href="product.php?id=<?= $prod['id']; ?>" class="w-100 h-100 d-flex align-items-center justify-content-center">
                                            <img src="<?= htmlspecialchars($img_src); ?>" alt="<?= htmlspecialchars($prod['name']); ?>" style="max-height: 100%; max-width: 100%; object-fit: contain;" loading="lazy">
                                        </a>
                                    </div>

                                    <div class="p-3 d-flex flex-column flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <?php if ($is_in_stock): ?>
                                                <span class="badge badge-stock rounded-pill px-2 py-1">
                                                    <i class="bi bi-check-circle-fill me-1"></i> В наличии
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-out-of-stock rounded-pill px-2 py-1">
                                                    Под заказ
                                                </span>
                                            <?php endif; ?>

                                            <?php if (!empty($prod['sku'])): ?>
                                                <small class="text-muted" style="font-size: 0.72rem;">Арт: <?= htmlspecialchars($prod['sku']) ?></small>
                                            <?php endif; ?>
                                        </div>

                                        <h6 class="fw-bold text-dark mb-2" style="min-height: 2.6rem; line-height: 1.35;">
                                            <a href="product.php?id=<?= $prod['id']; ?>" class="text-dark text-decoration-none">
                                                <?= htmlspecialchars($prod['name']); ?>
                                            </a>
                                        </h6>

                                        <div class="mt-auto pt-2 border-top">
                                            <div class="d-flex justify-content-between align-items-baseline mb-3">
                                                <div>
                                                    <span class="fs-5 fw-bold text-dark">
                                                        <?= number_format($prod['price'], 0, '.', ' '); ?> ₽
                                                    </span>
                                                    <?php if ($has_discount): ?>
                                                        <span class="small text-muted text-decoration-line-through ms-1">
                                                            <?= number_format($prod['old_price'], 0, '.', ' '); ?> ₽
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <div class="d-flex gap-2">
                                                <a href="product.php?id=<?= $prod['id']; ?>" class="btn btn-outline-dark btn-sm flex-fill fw-semibold rounded-pill">
                                                    Обзор
                                                </a>
                                                <button class="btn btn-warning btn-sm fw-bold rounded-pill px-3 shadow-sm add-to-cart-btn" data-product-id="<?= $prod['id']; ?>" title="В корзину">
                                                    <i class="bi bi-cart-plus-fill"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="card border-0 shadow-sm rounded-4 text-center py-5 px-3 bg-white">
                        <i class="bi bi-search display-3 text-muted opacity-50 mb-3"></i>
                        <h4 class="fw-bold">Товары не найдены</h4>
                        <p class="text-muted mb-4">Попробуйте изменить параметры поиска или выбрать другую категорию</p>
                        <div>
                            <a href="catalog.php" class="btn btn-warning fw-bold rounded-pill px-4 shadow-sm">
                                Сбросить все фильтры
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const prodId = this.dataset.productId;
            const originalHtml = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';

            fetch('ajax/cart_add.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'product_id=' + prodId + '&quantity=1'
            })
            .then(res => res.json())
            .then(data => {
                this.disabled = false;
                this.innerHTML = originalHtml;
                if (data.status === 'success') {
                    const badge = document.getElementById('cart-count-badge');
                    if (badge) {
                        badge.innerText = data.cart_count;
                        badge.style.display = 'inline-block';
                    }
                    showToast('Товар успешно добавлен в корзину!', 'success');
                } else {
                    showToast(data.message || 'Ошибка добавления', 'danger');
                }
            })
            .catch(err => {
                this.disabled = false;
                this.innerHTML = originalHtml;
                showToast('Ошибка связи с сервером', 'danger');
            });
        });
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
