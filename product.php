<?php
// product.php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Получаем товар
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header("HTTP/1.0 404 Not Found");
    echo "Товар не найден";
    exit;
}

// Картинки
$stmt = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY is_main DESC, sort_order ASC");
$stmt->execute([$id]);
$images = $stmt->fetchAll();
$main_image = $images[0]['image_path'] ?? 'images/no-photo.jpg';

// Очистка путей от ../
$main_image = str_replace('../', '', $main_image);
if (!file_exists(__DIR__ . '/' . $main_image)) {
    $main_image = 'images/no-photo.jpg';
}

$page_title = htmlspecialchars($product['name']) . ' — Михаил Потапыч';
require_once __DIR__ . '/includes/header.php';
?>

<main class="py-4 py-lg-5 bg-light">
    <div class="container">
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="index.php">Главная</a></li>
                <li class="breadcrumb-item"><a href="catalog.php">Каталог</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['name']); ?></li>
            </ol>
        </nav>

        <div class="row g-4 g-lg-5">
            <!-- Галерея -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white p-3 p-md-4 mb-3">
                    <a href="<?= htmlspecialchars($main_image); ?>" class="glightbox d-flex align-items-center justify-content-center" data-gallery="product-gallery" style="min-height: 320px; max-height: 460px;">
                        <img id="main-product-image" src="<?= htmlspecialchars($main_image); ?>" class="img-fluid rounded" style="max-height: 420px; object-fit: contain;" alt="<?= htmlspecialchars($product['name']); ?>">
                    </a>
                </div>
                
                <?php if (count($images) > 1): ?>
                    <div class="d-flex gap-2 overflow-auto pb-2">
                        <?php foreach ($images as $index => $img): 
                            $thumb = str_replace('../', '', $img['image_path']);
                            if (!file_exists(__DIR__ . '/' . $thumb)) $thumb = 'images/no-photo.jpg';
                        ?>
                            <img src="<?= htmlspecialchars($thumb); ?>" 
                                 class="gallery-thumb rounded border bg-white p-1 <?= $index === 0 ? 'border-warning active' : '' ?>" 
                                 style="width: 75px; height: 75px; object-fit: contain; cursor: pointer;" 
                                 data-full-img="<?= htmlspecialchars($thumb); ?>"
                                 alt="Превью">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Инфо о товаре -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                        <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-2 rounded-pill">
                            <i class="bi bi-check-circle-fill me-1"></i> В наличии
                        </span>
                        <?php if (!empty($product['sku'])): ?>
                            <span class="text-muted small">Арт: <strong><?= htmlspecialchars($product['sku']); ?></strong></span>
                        <?php endif; ?>
                    </div>

                    <h1 class="h3 fw-bold mb-3 text-dark"><?= htmlspecialchars($product['name']); ?></h1>

                    <!-- Рейтинг -->
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="text-warning small">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <span class="small text-muted">(5.0)</span>
                    </div>

                    <!-- Цена -->
                    <div class="mb-4 pb-3 border-bottom">
                        <?php if (!empty($product['old_price']) && $product['old_price'] > $product['price']): ?>
                            <div class="text-muted text-decoration-line-through small mb-1">
                                <?= number_format($product['old_price'], 0, '.', ' '); ?> ₽
                            </div>
                        <?php endif; ?>
                        <div class="d-flex align-items-baseline gap-3">
                            <span class="display-6 fw-bold text-dark"><?= number_format($product['price'], 0, '.', ' '); ?> ₽</span>
                            <?php if (!empty($product['old_price']) && $product['old_price'] > $product['price']): ?>
                                <span class="badge bg-danger rounded-pill">
                                    -<?= round(($product['old_price'] - $product['price']) / $product['old_price'] * 100) ?>%
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (!empty($product['short_description'])): ?>
                        <p class="text-muted mb-4 small">
                            <?= nl2br(htmlspecialchars($product['short_description'])); ?>
                        </p>
                    <?php endif; ?>

                    <!-- Блок покупки -->
                    <div class="product-action-wrapper p-3 bg-light rounded-4 border mb-4">
                        <div class="row g-2 align-items-center">
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <button class="btn btn-outline-secondary" type="button" onclick="const i=this.nextElementSibling; if(i.value>1) i.value--;">-</button>
                                    <input type="number" class="form-control text-center fw-bold bg-white" name="quantity" value="1" min="1">
                                    <button class="btn btn-outline-secondary" type="button" onclick="const i=this.previousElementSibling; i.value++;">+</button>
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <button class="btn btn-warning w-100 fw-bold py-2 rounded-pill shadow-sm btn-add-cart" data-product-id="<?= $product['id']; ?>">
                                    <i class="bi bi-cart-plus-fill me-1"></i> Добавить в корзину
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Преимущества сервиса -->
                    <div class="row g-2 text-muted small mt-auto pt-2 border-top">
                        <div class="col-6 d-flex align-items-center gap-2">
                            <i class="bi bi-shield-check text-warning fs-5"></i>
                            <span>Гарантия производителя</span>
                        </div>
                        <div class="col-6 d-flex align-items-center gap-2">
                            <i class="bi bi-tools text-warning fs-5"></i>
                            <span>Монтаж под ключ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Подробное описание -->
        <?php if (!empty($product['description'])): ?>
            <div class="card border-0 shadow-sm rounded-4 p-4 mt-4 bg-white">
                <h4 class="fw-bold mb-3 text-dark border-bottom pb-2">Описание товара</h4>
                <div class="text-muted lh-lg">
                    <?= nl2br(htmlspecialchars($product['description'])); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
