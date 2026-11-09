<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// 1. Популярные товары (до 4 штук)
$featured_products = [];
if (function_exists('getFeaturedProducts')) {
    $featured_products = getFeaturedProducts(4);
}
if (count($featured_products) < 4) {
    $needed = 4 - count($featured_products);
    $exclude_ids = array_column($featured_products, 'id');
    if (empty($exclude_ids)) $exclude_ids = [0];
    $placeholders = implode(',', $exclude_ids);
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE is_active = 1 AND id NOT IN ($placeholders) ORDER BY id DESC LIMIT ?");
        $stmt->bindValue(1, (int)$needed, PDO::PARAM_INT);
        $stmt->execute();
        $more_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $featured_products = array_merge($featured_products, $more_products);
    } catch (Exception $e) {}
}
$featured_products = array_slice($featured_products, 0, 4);

// 2. Акции
$promotions = [];
try {
    $stmt = $pdo->query("
        SELECT * FROM promotions 
        WHERE is_active = 1 
        AND (start_date IS NULL OR start_date <= NOW()) 
        AND (end_date IS NULL OR end_date >= NOW())
        ORDER BY sort_order ASC, created_at DESC 
        LIMIT 3
    ");
    $promotions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $promotions = [];
}

// 3. Монтажники
$installers = [];
try {
    $stmt = $pdo->query("
        SELECT i.*, 
               (SELECT AVG(rating) FROM installer_reviews WHERE installer_id = i.id) as avg_rating,
               (SELECT COUNT(*) FROM installer_reviews WHERE installer_id = i.id) as review_count
        FROM installers i 
        WHERE i.is_active = 1 
        ORDER BY avg_rating DESC, id ASC 
        LIMIT 4
    ");
    $installers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $installers = [];
}

// 4. Отзывы о магазине
$shop_reviews = function_exists('getLatestReviews') ? getLatestReviews(3) : [];

$page_title = 'Территория Тепла — Отопительные системы, котлы и печи на Алтае';
require_once __DIR__ . '/includes/header.php';
?>

<main style="background-color: var(--bg-page, #f8fafc);">

    <!-- 1. HERO: ИНЖЕНЕРНЫЙ ТЕМНЫЙ БАННЕР -->
    <section class="hero-engineering text-white py-5 position-relative">
        <div class="container py-4 py-lg-5 position-relative" style="z-index: 2;">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <span class="badge bg-warning text-dark text-uppercase px-3 py-2 fw-bold mb-3 shadow-sm d-inline-flex align-items-center gap-1">
                        <i class="bi bi-fire fs-6"></i> Официальный склад-магазин завода
                    </span>
                    <h1 class="display-4 fw-bold mb-3 text-uppercase text-white" style="letter-spacing: -0.02em;">
                        Отопительные системы <br><span class="text-warning">и печи на Алтае</span>
                    </h1>
                    <p class="lead mb-4 text-white-50" style="max-width: 580px;">
                        Банные печи, твердотопливные котлы длительного горения и нержавеющие дымоходы. Официальная гарантия завода и профессиональный монтаж «под ключ».
                    </p>
                    
                    <div class="d-flex flex-wrap gap-3 mb-4">
                        <a href="catalog.php" class="btn btn-warning btn-lg px-4 py-3 fw-bold rounded-pill shadow-sm">
                            <i class="bi bi-grid-fill me-1"></i> Каталог товаров
                        </a>
                        <a href="installers.php" class="btn btn-outline-light btn-lg px-4 py-3 fw-semibold rounded-pill">
                            <i class="bi bi-person-badge me-1"></i> Выбрать монтажника
                        </a>
                    </div>

                    <!-- Доверительные метрики -->
                    <div class="row g-3 pt-3 border-top border-secondary border-opacity-25 small text-white-50">
                        <div class="col-auto d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-warning fs-5"></i>
                            <span>Склад в с. Майма</span>
                        </div>
                        <div class="col-auto d-flex align-items-center gap-2">
                            <i class="bi bi-shield-check text-warning fs-5"></i>
                            <span>Гарантия до 5 лет</span>
                        </div>
                        <div class="col-auto d-flex align-items-center gap-2">
                            <i class="bi bi-tools text-warning fs-5"></i>
                            <span>Монтаж по СНиП</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 text-center d-none d-lg-block">
                    <div class="p-4 rounded-4 shadow-lg" style="background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.1); backdrop-filter: blur(8px);">
                        <div class="d-flex align-items-center justify-content-between mb-3 text-start">
                            <div>
                                <span class="badge bg-warning text-dark text-uppercase small">Спецпредложение</span>
                                <h5 class="fw-bold text-white mb-0 mt-1">Подбор котельной за 15 минут</h5>
                            </div>
                            <i class="bi bi-calculator fs-2 text-warning"></i>
                        </div>
                        <p class="small text-white-50 text-start mb-4">
                            Рассчитаем необходимую мощность котла, метраж теплого пола и комплектацию дымохода под ваш объект.
                        </p>
                        <a href="contacts.php" class="btn btn-warning w-100 fw-bold py-2 rounded-pill">
                            Получить бесплатную консультацию
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. ИНЖЕНЕРНЫЕ ПРЕИМУЩЕСТВА -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-building-check"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Склад в с. Майма</h5>
                        <p class="text-muted small mb-0">
                            Большинство ходовых моделей печей, котлов и сэндвич-дымоходов всегда в наличии на складе (ул. Энергетиков, 24).
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Гарантия завода</h5>
                        <p class="text-muted small mb-0">
                            Прямые поставки от производителей с официальными гарантийными талонами и сертификатами соответствия ГОСТ.
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-tools"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Проверенный монтаж</h5>
                        <p class="text-muted small mb-0">
                            Аттестованные мастера-партнеры с опытом от 3 лет. Соблюдение всех пожарных отступок и норм безопасности.
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-truck"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Доставка по Алтаю</h5>
                        <p class="text-muted small mb-0">
                            Оперативная отгрузка и доставка по Республике Алтай и Алтайскому краю собственным транспортом и ТК.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. ПОПУЛЯРНЫЕ КАТЕГОРИИ -->
    <section class="py-4">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 fw-bold text-dark mb-0">Основные направления</h2>
                    <small class="text-muted">Оборудование для дома, бани и коммерческих котельных</small>
                </div>
                <a href="catalog.php" class="btn btn-outline-dark btn-sm rounded-pill fw-semibold">
                    Весь каталог <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>

            <div class="row g-4">
                <div class="col-6 col-md-3">
                    <a href="catalog.php?category=1" class="category-card">
                        <div class="category-icon">
                            <i class="bi bi-fire"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">Банные печи</h5>
                        <small class="text-muted">Дровяные и чугунные</small>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="catalog.php?category=2" class="category-card">
                        <div class="category-icon">
                            <i class="bi bi-box-seam-fill"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">Котлы отопления</h5>
                        <small class="text-muted">Твердотопливные и комби</small>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="catalog.php?category=3" class="category-card">
                        <div class="category-icon">
                            <i class="bi bi-layers-fill"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">Сэндвич-дымоходы</h5>
                        <small class="text-muted">Нержавеющая сталь</small>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="catalog.php?category=4" class="category-card">
                        <div class="category-icon">
                            <i class="bi bi-droplet-half"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">Баки и контуры</h5>
                        <small class="text-muted">Теплообменники и краны</small>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. ПОПУЛЯРНЫЕ ТОВАРЫ -->
    <section class="py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 fw-bold text-dark mb-0">Хиты продаж</h2>
                    <small class="text-muted">Оборудование, проверенное алтайскими зимами</small>
                </div>
                <a href="catalog.php" class="btn btn-warning btn-sm rounded-pill fw-bold">
                    Смотреть все товары <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>

            <?php if (!empty($featured_products)): ?>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                    <?php foreach ($featured_products as $prod): 
                        $img = !empty($prod['image_path']) ? $prod['image_path'] : '';
                        $img = str_replace('../', '', $img);
                        if (empty($img) && function_exists('getProductMainImage')) {
                            $img = getProductMainImage($prod['id']);
                            $img = str_replace('../', '', $img);
                        }
                        if (empty($img) || !file_exists(__DIR__ . '/' . $img)) {
                            $img = 'images/products/no-image.png';
                        }
                        $is_in_stock = isset($prod['stock_quantity']) && $prod['stock_quantity'] > 0;
                        $has_discount = !empty($prod['old_price']) && $prod['old_price'] > $prod['price'];
                    ?>
                        <div class="col">
                            <div class="product-card">
                                <!-- Контейнер фото -->
                                <div class="product-img-box">
                                    <?php if ($has_discount): 
                                        $pct = round((($prod['old_price'] - $prod['price']) / $prod['old_price']) * 100);
                                    ?>
                                        <span class="position-absolute top-0 start-0 m-3 badge badge-discount rounded-pill">
                                            -<?= $pct ?>%
                                        </span>
                                    <?php endif; ?>

                                    <a href="product.php?id=<?= $prod['id'] ?>" class="w-100 h-100 d-flex align-items-center justify-content-center">
                                        <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($prod['name']) ?>" loading="lazy">
                                    </a>
                                </div>

                                <!-- Тело карточки -->
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

                                    <h6 class="fw-bold text-dark mb-2 line-clamp-2" style="min-height: 2.6rem;">
                                        <a href="product.php?id=<?= $prod['id'] ?>" class="text-dark text-decoration-none">
                                            <?= htmlspecialchars($prod['name']) ?>
                                        </a>
                                    </h6>

                                    <div class="mt-auto pt-2 border-top">
                                        <div class="d-flex justify-content-between align-items-baseline mb-3">
                                            <div>
                                                <span class="fs-5 fw-bold text-dark">
                                                    <?= number_format($prod['price'], 0, '.', ' ') ?> ₽
                                                </span>
                                                <?php if ($has_discount): ?>
                                                    <span class="small text-muted text-decoration-line-through ms-1">
                                                        <?= number_format($prod['old_price'], 0, '.', ' ') ?> ₽
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="d-flex gap-2">
                                            <a href="product.php?id=<?= $prod['id'] ?>" class="btn btn-outline-dark btn-sm flex-fill fw-semibold rounded-pill">
                                                Обзор
                                            </a>
                                            <button class="btn btn-warning btn-sm fw-bold rounded-pill px-3 shadow-sm add-to-cart-btn" data-product-id="<?= $prod['id'] ?>" title="В корзину">
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
                <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-white">
                    <p class="text-muted mb-0">Каталог товаров наполняется. Актуальные остатки уточняйте по телефону склада.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- 5. МОНТАЖНИКИ СЕТИ -->
    <?php if (!empty($installers)): ?>
    <section class="py-5 bg-white border-top border-bottom">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 fw-bold text-dark mb-0">Сертифицированные монтажники</h2>
                    <small class="text-muted">Профессиональная сборка дымоходов и установка печей с гарантией</small>
                </div>
                <a href="installers.php" class="btn btn-outline-dark btn-sm rounded-pill fw-semibold">
                    Все мастера <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>

            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                <?php foreach ($installers as $inst): 
                    $photo = !empty($inst['photo_path']) ? $inst['photo_path'] : (!empty($inst['photo']) ? 'images/installers/' . $inst['photo'] : '');
                    $photo = str_replace('../', '', $photo);
                    $hasPhoto = !empty($photo) && file_exists(__DIR__ . '/' . $photo);
                    $avgRating = round($inst['avg_rating'] ?? 5);
                ?>
                    <div class="col">
                        <div class="card border-0 shadow-sm rounded-4 h-100 p-4 text-center bg-light">
                            <div class="mb-3 mx-auto" style="width: 88px; height: 88px;">
                                <?php if ($hasPhoto): ?>
                                    <img src="<?= htmlspecialchars($photo) ?>" class="rounded-circle w-100 h-100 object-fit-cover border border-2 border-warning shadow-sm" alt="Мастер">
                                <?php else: ?>
                                    <div class="rounded-circle bg-dark text-warning d-flex align-items-center justify-content-center w-100 h-100 fw-bold fs-4 border border-2 border-warning shadow-sm">
                                        <?= mb_substr($inst['first_name'], 0, 1) . mb_substr($inst['last_name'], 0, 1) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($inst['first_name'] . ' ' . $inst['last_name']) ?></h6>
                            <p class="text-muted small mb-2"><?= htmlspecialchars($inst['specialization'] ?? 'Монтаж отопления') ?></p>
                            <div class="text-warning small mb-3">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?= $i <= $avgRating ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>' ?>
                                <?php endfor; ?>
                            </div>
                            <a href="installer.php?id=<?= $inst['id'] ?>" class="btn btn-outline-dark btn-sm fw-semibold rounded-pill mt-auto">
                                Профиль мастера
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- 6. ОТЗЫВЫ КЛИЕНТОВ -->
    <?php if (!empty($shop_reviews)): ?>
    <section class="py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 fw-bold text-dark mb-0">Отзывы о магазине</h2>
                    <small class="text-muted">Что говорят клиенты о качестве нашего сервиса</small>
                </div>
                <a href="reviews.php" class="btn btn-outline-dark btn-sm rounded-pill fw-semibold">
                    Все отзывы <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>

            <div class="row g-4">
                <?php foreach ($shop_reviews as $rev): 
                    $author = !empty($rev['first_name']) ? ($rev['first_name'] . ' ' . ($rev['last_name'] ?? '')) : ($rev['username'] ?? 'Покупатель');
                ?>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="fw-bold text-dark"><?= htmlspecialchars($author) ?></div>
                                <div class="text-warning small">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <?= $i <= $rev['rating'] ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>' ?>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <p class="text-muted small mb-0 lh-lg flex-grow-1">
                                <?= htmlspecialchars(mb_substr($rev['comment'], 0, 150)) . (mb_strlen($rev['comment']) > 150 ? '...' : '') ?>
                            </p>
                            <div class="text-muted pt-3 border-top mt-3" style="font-size: 0.75rem;">
                                <?= date('d.m.Y', strtotime($rev['created_at'])) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Быстрое добавление в корзину
    document.querySelectorAll('.add-to-cart-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const pid = this.dataset.productId;
            const fd = new FormData();
            fd.append('product_id', pid);
            fd.append('quantity', 1);

            fetch('ajax/add_to_cart.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    const badge = document.getElementById('cart-count-badge');
                    if (badge) {
                        badge.innerText = res.cart_count;
                        badge.style.display = 'inline-block';
                    }
                    btn.classList.remove('btn-warning');
                    btn.classList.add('btn-success');
                    btn.innerHTML = '<i class="bi bi-check-lg"></i>';
                    setTimeout(() => {
                        btn.classList.remove('btn-success');
                        btn.classList.add('btn-warning');
                        btn.innerHTML = '<i class="bi bi-cart-plus-fill"></i>';
                    }, 1500);
                } else {
                    alert(res.message || 'Ошибка добавления');
                }
            })
            .catch(err => console.error(err));
        });
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
