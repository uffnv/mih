<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// Фильтры
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'new';

// SQL с JOIN фото, чтобы избежать N+1 запросов
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

// Сортировка
switch ($sort) {
    case 'price_asc': $sql .= " ORDER BY p.price ASC"; break;
    case 'price_desc': $sql .= " ORDER BY p.price DESC"; break;
    default: $sql .= " ORDER BY p.id DESC"; break; 
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

$page_title = 'Каталог — ' . $current_category_name;
require_once __DIR__ . '/includes/header.php';
?>

<main class="py-4 py-lg-5 bg-light">
    <div class="container">
        
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="index.php">Главная</a></li>
                <li class="breadcrumb-item"><a href="catalog.php">Каталог</a></li>
                <?php if ($category_id): ?>
                    <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($current_category_name); ?></li>
                <?php endif; ?>
            </ol>
        </nav>

        <div class="row g-4">
            <!-- Сайдбар фильтров -->
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden sticky-lg-top" style="top: 85px; z-index: 10;">
                    <div class="card-header bg-dark text-white fw-bold py-3 d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-grid-fill text-warning me-2"></i>Категории</span>
                        <span class="badge bg-warning text-dark"><?= count($products); ?> тов.</span>
                    </div>
                    
                    <!-- Поиск -->
                    <div class="p-3 border-bottom bg-white">
                        <form method="GET" action="catalog.php" class="input-group input-group-sm">
                            <?php if ($category_id): ?>
                                <input type="hidden" name="category" value="<?= $category_id ?>">
                            <?php endif; ?>
                            <input type="text" name="search" class="form-control" placeholder="Поиск товара..." value="<?= htmlspecialchars($search); ?>">
                            <button class="btn btn-warning" type="submit" title="Найти">
                                <i class="bi bi-search"></i>
                            </button>
                            <?php if ($search): ?>
                                <a href="catalog.php<?= $category_id ? '?category='.$category_id : '' ?>" class="btn btn-outline-secondary" title="Сбросить">
                                    <i class="bi bi-x"></i>
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>

                    <div class="list-group list-group-flush">
                        <a href="catalog.php<?= $search ? '?search='.urlencode($search) : '' ?>" 
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-3 <?= !$category_id ? 'active bg-warning border-warning text-dark fw-bold' : '' ?>">
                            <span>Все категории</span>
                        </a>
                        <?php foreach ($categories as $cat): ?>
                            <a href="catalog.php?category=<?= $cat['id']; ?><?= $search ? '&search='.urlencode($search) : '' ?>" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-3 <?= $category_id == $cat['id'] ? 'active bg-warning border-warning text-dark fw-bold' : '' ?>">
                                <span><?= htmlspecialchars($cat['name']); ?></span>
                                <span class="badge rounded-pill <?= $category_id == $cat['id'] ? 'bg-dark text-white' : 'bg-light text-muted border' ?>">
                                    <?= $cat['product_count']; ?>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Товары -->
            <div class="col-lg-9">
                <!-- Панель управления каталогом -->
                <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                        <div>
                            <h1 class="h4 fw-bold mb-1 text-dark"><?= htmlspecialchars($current_category_name); ?></h1>
                            <p class="text-muted small mb-0">
                                <?php if ($search): ?>
                                    По запросу «<?= htmlspecialchars($search); ?>» найдено: <strong><?= count($products); ?></strong>
                                <?php else: ?>
                                    Всего товаров в разделе: <strong><?= count($products); ?></strong>
                                <?php endif; ?>
                            </p>
                        </div>
                        
                        <form class="d-flex align-items-center gap-2" method="GET">
                            <?php if ($category_id): ?><input type="hidden" name="category" value="<?= $category_id ?>"><?php endif; ?>
                            <?php if ($search): ?><input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>"><?php endif; ?>
                            <label class="small text-muted text-nowrap d-none d-md-inline">Сортировка:</label>
                            <select name="sort" class="form-select form-select-sm rounded-pill shadow-none" onchange="this.form.submit()" style="min-width: 170px;">
                                <option value="new" <?= $sort == 'new' ? 'selected' : '' ?>>Новинки</option>
                                <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Сначала дешевые</option>
                                <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Сначала дорогие</option>
                            </select>
                        </form>
                    </div>
                </div>

                <?php if (empty($products)): ?>
                    <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
                        <i class="bi bi-search display-3 text-muted mb-3"></i>
                        <h4 class="fw-bold">Товары не найдены</h4>
                        <p class="text-muted mb-4">Попробуйте изменить параметры поиска или выбрать другую категорию</p>
                        <div>
                            <a href="catalog.php" class="btn btn-warning fw-bold rounded-pill px-4">Сбросить фильтры</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                        <?php foreach ($products as $prod): ?>
                            <?php 
                                $img = !empty($prod['main_image']) ? $prod['main_image'] : 'images/no-photo.jpg';
                                $img = str_replace('../', '', $img);
                                if (!file_exists(__DIR__ . '/' . $img)) {
                                    $img = 'images/no-photo.jpg';
                                }
                                $hasDiscount = !empty($prod['old_price']) && $prod['old_price'] > $prod['price'];
                            ?>
                            <div class="col">
                                <div class="card h-100 border-0 shadow-sm product-card position-relative bg-white">
                                    <div class="d-flex justify-content-between position-absolute top-0 start-0 end-0 p-2" style="z-index: 5;">
                                        <span class="badge-stock">
                                            <i class="bi bi-check2 me-1"></i>В наличии
                                        </span>
                                        <?php if ($hasDiscount): ?>
                                            <span class="badge-discount">
                                                -<?= round(($prod['old_price'] - $prod['price']) / $prod['old_price'] * 100) ?>%
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="product-img-wrapper">
                                        <img src="<?= htmlspecialchars($img); ?>" alt="<?= htmlspecialchars($prod['name']); ?>" loading="lazy">
                                    </div>
                                    
                                    <div class="card-body d-flex flex-column pt-3">
                                        <?php if (!empty($prod['sku'])): ?>
                                            <div class="small text-muted mb-1 font-monospace" style="font-size: 0.8rem;">Арт. <?= htmlspecialchars($prod['sku']); ?></div>
                                        <?php endif; ?>

                                        <h5 class="card-title fs-6 fw-bold mb-2 lh-sm">
                                            <a href="product.php?id=<?= $prod['id']; ?>" class="text-decoration-none text-dark stretched-link">
                                                <?= htmlspecialchars($prod['name']); ?>
                                            </a>
                                        </h5>
                                        
                                        <div class="mt-auto pt-3 border-top">
                                            <div class="mb-2 d-flex align-items-baseline gap-2">
                                                <span class="text-dark fw-bold fs-5"><?= number_format($prod['price'], 0, '.', ' '); ?> ₽</span>
                                                <?php if ($hasDiscount): ?>
                                                    <small class="text-muted text-decoration-line-through" style="font-size: 0.85rem;"><?= number_format($prod['old_price'], 0, '.', ' '); ?> ₽</small>
                                                <?php endif; ?>
                                            </div>

                                            <button class="btn btn-warning w-100 fw-bold py-2 shadow-sm btn-add-cart position-relative z-2" 
                                                    data-product-id="<?= $prod['id']; ?>">
                                                <i class="bi bi-cart-plus me-1"></i> В корзину
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
