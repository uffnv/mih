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

$page_title = htmlspecialchars($product['name']);
require_once __DIR__ . '/includes/header.php';
?>

<main class="py-5 bg-white">
    <div class="container">
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Главная</a></li>
                <li class="breadcrumb-item"><a href="catalog.php" class="text-decoration-none text-muted">Каталог</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['name']); ?></li>
            </ol>
        </nav>

        <div class="row g-5">
            <!-- Галерея -->
            <div class="col-lg-6">
                <div class="card border-0 mb-3">
                    <a href="<?= htmlspecialchars($main_image); ?>" class="glightbox" data-gallery="product-gallery">
                        <img src="<?= htmlspecialchars($main_image); ?>" class="img-fluid rounded shadow-sm w-100 object-fit-cover" style="max-height: 500px;" alt="<?= htmlspecialchars($product['name']); ?>">
                    </a>
                </div>
                <?php if (count($images) > 1): ?>
                    <div class="d-flex gap-2 overflow-auto pb-2">
                        <?php foreach ($images as $img): 
                            $thumb = str_replace('../', '', $img['image_path']);
                        ?>
                            <a href="<?= htmlspecialchars($thumb); ?>" class="glightbox" data-gallery="product-gallery">
                                <img src="<?= htmlspecialchars($thumb); ?>" class="rounded border" style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;">
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Инфо -->
            <div class="col-lg-6">
                <h1 class="fw-bold mb-3"><?= htmlspecialchars($product['name']); ?></h1>
                
                <div class="d-flex align-items-center mb-4">
                    <div class="text-warning fs-5 me-2">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                    </div>
                    <span class="text-muted small">Артикул: <?= htmlspecialchars($product['sku']); ?></span>
                </div>

                <div class="mb-4">
                    <?php if ($product['old_price'] > $product['price']): ?>
                        <span class="text-muted text-decoration-line-through fs-5 me-2"><?= number_format($product['old_price'], 0, '.', ' '); ?> ₽</span>
                    <?php endif; ?>
                    <span class="text-warning fw-bold display-6"><?= number_format($product['price'], 0, '.', ' '); ?> ₽</span>
                </div>

                <p class="text-muted mb-4 lead fs-6">
                    <?= nl2br(htmlspecialchars($product['short_description'])); ?>
                </p>

                <!-- БЛОК ПОКУПКИ (БЕЗ ТЕГА FORM!) -->
                <div class="d-flex align-items-center gap-3 mb-5 p-4 bg-light rounded border">
                    <div style="width: 100px;">
                        <input type="number" class="form-control form-control-lg text-center fw-bold" name="quantity" value="1" min="1" data-product-id="<?= $product['id']; ?>">
                    </div>
                    <button class="btn btn-warning btn-lg px-5 fw-bold w-100 shadow-sm btn-add-cart" data-product-id="<?= $product['id']; ?>">
                        <i class="bi bi-cart-plus me-2"></i> В корзину
                    </button>
                </div>

                <div class="mb-5">
                    <h5 class="fw-bold border-bottom pb-2 mb-3">Описание</h5>
                    <div class="text-muted">
                        <?= nl2br(htmlspecialchars($product['description'])); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
