<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// 1. ПОПУЛЯРНЫЕ ТОВАРЫ (Хиты + Добор до 4)
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

// 2. АКЦИИ И СПЕЦПРЕДЛОЖЕНИЯ
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

// 3. ВИДЕООБЗОРЫ ОБОРУДОВАНИЯ
$homepage_videos = function_exists('getVideos') ? getVideos(5) : [];

// 4. ПОПУЛЯРНЫЕ КАТЕГОРИИ ИЗ БД
$categories = [];
try {
    $stmt = $pdo->query("
        SELECT * FROM categories 
        WHERE is_active = 1 AND (parent_id IS NULL OR parent_id = 0)
        ORDER BY sort_order ASC, id ASC 
        LIMIT 8
    ");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $categories = [];
}

// 5. МОНТАЖНИКИ СЕТИ
$installers = [];
try {
    $stmt = $pdo->query("
        SELECT i.*, 
               (SELECT AVG(rating) FROM installer_reviews WHERE installer_id = i.id) as calc_avg_rating,
               (SELECT COUNT(*) FROM installer_reviews WHERE installer_id = i.id) as calc_review_count
        FROM installers i 
        WHERE i.is_active = 1 
        ORDER BY calc_avg_rating DESC, i.average_rating DESC, i.id ASC 
        LIMIT 4
    ");
    $installers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $installers = [];
}

// 6. ОТЗЫВЫ О МАГАЗИНЕ
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
                        Банные печи, твердотопливные котлы длительного горения и нержавеющие сэндвич-дымоходы. Официальная гарантия завода и монтаж сертифицированными мастерами с 1995 года.
                    </p>
                    
                    <div class="d-flex flex-wrap gap-3 mb-4">
                        <a href="catalog.php" class="btn btn-warning btn-lg px-4 py-3 fw-bold rounded-pill shadow-sm">
                            <i class="bi bi-grid-fill me-1"></i> Перейти в каталог
                        </a>
                        <a href="installers.php" class="btn btn-outline-light btn-lg px-4 py-3 fw-semibold rounded-pill">
                            <i class="bi bi-person-badge me-1"></i> Выбрать монтажника
                        </a>
                    </div>

                    <!-- Доверительные метрики -->
                    <div class="row g-3 pt-3 border-top border-secondary border-opacity-25 small text-white-50">
                        <div class="col-auto d-flex align-items-center gap-2">
                            <i class="bi bi-building-check text-warning fs-5"></i>
                            <span>Склад в с. Майма (ул. Энергетиков 24)</span>
                        </div>
                        <div class="col-auto d-flex align-items-center gap-2">
                            <i class="bi bi-shield-check text-warning fs-5"></i>
                            <span>Гарантия завода до 5 лет</span>
                        </div>
                        <div class="col-auto d-flex align-items-center gap-2">
                            <i class="bi bi-tools text-warning fs-5"></i>
                            <span>Монтаж по СНиП и ГОСТ</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 text-center d-none d-lg-block">
                    <div class="p-4 rounded-4 shadow-lg text-start" style="background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.12); backdrop-filter: blur(8px);">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <span class="badge bg-warning text-dark text-uppercase small fw-bold">Инженерный расчет</span>
                                <h5 class="fw-bold text-white mb-0 mt-1">Подбор котельной за 15 минут</h5>
                            </div>
                            <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="bi bi-calculator-fill fs-5"></i>
                            </div>
                        </div>
                        <p class="small text-white-50 mb-4">
                            Рассчитаем необходимую мощность котла под площадь дома, метраж теплого пола и безопасную конфигурацию дымохода с учетом сибирских морозов.
                        </p>
                        <a href="contacts.php" class="btn btn-warning w-100 fw-bold py-3 rounded-pill shadow-sm">
                            <i class="bi bi-telephone-outbound me-1"></i> Получить расчет инженера
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. АКЦИИ И СПЕЦПРЕДЛОЖЕНИЯ (ИЗ ОРИГИНАЛА) -->
    <?php if (!empty($promotions)): ?>
    <section class="py-5 bg-white border-bottom">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span class="badge bg-warning-subtle text-dark border small fw-bold mb-1">Специальные условия</span>
                    <h2 class="h3 fw-bold text-dark mb-0">Акции и спецпредложения</h2>
                </div>
                <a href="promotions.php" class="btn btn-outline-dark btn-sm rounded-pill fw-semibold">
                    Все акции <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>

            <div class="row g-4">
                <?php foreach ($promotions as $promo): 
                    $discount = (int)($promo['discount_percent'] ?? 0);
                    $promoImg = !empty($promo['image_path']) ? $promo['image_path'] : 'images/categories/cat_stoves.svg';
                    $promoImg = str_replace('../', '', $promoImg);
                ?>
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative" style="background: var(--bg-card, #fff); transition: transform 0.2s ease;">
                            <?php if ($discount > 0): ?>
                                <span class="position-absolute top-0 end-0 m-3 badge bg-danger text-white rounded-pill px-3 py-2 fw-bold shadow-sm" style="z-index: 5;">
                                    -<?= $discount ?>%
                                </span>
                            <?php endif; ?>

                            <div class="p-4 d-flex align-items-center justify-content-center bg-light" style="height: 180px;">
                                <img src="<?= htmlspecialchars($promoImg) ?>" alt="<?= htmlspecialchars($promo['title']) ?>" class="img-fluid" style="max-height: 120px; object-fit: contain;">
                            </div>

                            <div class="card-body p-4 d-flex flex-column">
                                <h5 class="fw-bold text-dark mb-2"><?= htmlspecialchars($promo['title']) ?></h5>
                                <p class="text-muted small mb-4 flex-grow-1 lh-base">
                                    <?= htmlspecialchars(mb_substr($promo['description'], 0, 120)) . (mb_strlen($promo['description']) > 120 ? '...' : '') ?>
                                </p>
                                <a href="promotions.php#promo-<?= $promo['id'] ?>" class="btn btn-outline-warning text-dark border-warning btn-sm fw-bold rounded-pill w-100">
                                    Подробнее об акции <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- 3. ПОПУЛЯРНЫЕ КАТЕГОРИИ -->
    <section class="py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 fw-bold text-dark mb-0">Категории оборудования</h2>
                    <small class="text-muted">Все для отопления частного дома, бани и производственных помещений</small>
                </div>
                <a href="catalog.php" class="btn btn-outline-dark btn-sm rounded-pill fw-semibold">
                    Весь каталог <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>

            <div class="row row-cols-2 row-cols-md-4 g-3 g-md-4">
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $cat): 
                        $catImg = !empty($cat['image_path']) ? $cat['image_path'] : 'images/categories/cat_boilers.svg';
                        $catImg = str_replace('../', '', $catImg);
                    ?>
                        <div class="col">
                            <a href="catalog.php?category=<?= $cat['id'] ?>" class="category-card d-block text-decoration-none">
                                <div class="category-icon mb-3">
                                    <img src="<?= htmlspecialchars($catImg) ?>" alt="<?= htmlspecialchars($cat['name']) ?>" style="width: 44px; height: 44px; object-fit: contain;" onerror="this.onerror=null; this.src='images/categories/cat_boilers.svg'">
                                </div>
                                <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($cat['name']) ?></h6>
                                <small class="text-muted d-block text-truncate"><?= htmlspecialchars($cat['description'] ?? 'В наличии на складе') ?></small>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-6 col-md-3">
                        <a href="catalog.php?category=1" class="category-card d-block text-decoration-none">
                            <div class="category-icon mb-3"><i class="bi bi-fire fs-3 text-warning"></i></div>
                            <h6 class="fw-bold text-dark mb-1">Банные печи</h6>
                            <small class="text-muted">Дровяные и чугунные</small>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="catalog.php?category=2" class="category-card d-block text-decoration-none">
                            <div class="category-icon mb-3"><i class="bi bi-box-seam-fill fs-3 text-warning"></i></div>
                            <h6 class="fw-bold text-dark mb-1">Котлы отопления</h6>
                            <small class="text-muted">Твердотопливные и комби</small>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="catalog.php?category=3" class="category-card d-block text-decoration-none">
                            <div class="category-icon mb-3"><i class="bi bi-layers-fill fs-3 text-warning"></i></div>
                            <h6 class="fw-bold text-dark mb-1">Сэндвич-дымоходы</h6>
                            <small class="text-muted">AISI 430 / AISI 304</small>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="catalog.php?category=4" class="category-card d-block text-decoration-none">
                            <div class="category-icon mb-3"><i class="bi bi-droplet-half fs-3 text-warning"></i></div>
                            <h6 class="fw-bold text-dark mb-1">Теплый пол и радиаторы</h6>
                            <small class="text-muted">Комплектующие и трубы</small>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- 4. ПОПУЛЯРНЫЕ ТОВАРЫ (ХИТЫ ПРОДАЖ) -->
    <section class="py-5 bg-white border-top border-bottom">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span class="badge bg-warning text-dark text-uppercase small fw-bold mb-1">Лидеры продаж</span>
                    <h2 class="h3 fw-bold text-dark mb-0">Популярное оборудование</h2>
                    <small class="text-muted">Оборудование, проверенное алтайскими зимами и десятками отзывов</small>
                </div>
                <a href="catalog.php" class="btn btn-warning btn-sm rounded-pill fw-bold">
                    Весь каталог <i class="bi bi-arrow-right ms-1"></i>
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

                                    <a href="product.php?id=<?= $prod['id'] ?>" class="w-100 h-100 d-flex align-items-center justify-content-center p-3">
                                        <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($prod['name']) ?>" loading="lazy" style="max-height: 160px; object-fit: contain;">
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

    <!-- 5. ВИДЕООБЗОРЫ ОБОРУДОВАНИЯ (ВОССТАНОВЛЕНО ИЗ ОРИГИНАЛА) -->
    <?php if (!empty($homepage_videos)): ?>
    <section class="py-5 text-white" style="background: linear-gradient(135deg, #181c20 0%, #22272c 100%);">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-secondary border-opacity-25 pb-3">
                <div>
                    <span class="badge bg-warning text-dark text-uppercase small fw-bold mb-1">Видео с объектов</span>
                    <h2 class="h3 fw-bold text-white mb-0">Видеообзоры оборудования</h2>
                </div>
                <a href="videos.php" class="btn btn-outline-warning text-warning btn-sm rounded-pill fw-semibold">
                    Все видео &rarr;
                </a>
            </div>

            <div id="videoCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php foreach ($homepage_videos as $index => $video): 
                        $vUrl = $video['video_url'] ?? $video['url'] ?? '';
                    ?>
                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                            <div class="row justify-content-center mx-0">
                                <div class="col-lg-10 col-xl-9">
                                    <div class="card h-100 border-0 shadow-lg overflow-hidden bg-black rounded-4">
                                        <div class="ratio ratio-16x9">
                                            <iframe src="<?= htmlspecialchars($vUrl) ?>" title="<?= htmlspecialchars($video['title']) ?>" allowfullscreen loading="lazy"></iframe>
                                        </div>
                                        <div class="card-body text-white p-4 text-center" style="background: #111417; border-top: 3px solid #FFDF1E;">
                                            <h4 class="card-title fw-bold mb-2 text-warning"><?= htmlspecialchars($video['title']) ?></h4>
                                            <?php if (!empty($video['description'])): ?>
                                                <p class="card-text text-white-50 mb-0"><?= htmlspecialchars($video['description']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (count($homepage_videos) > 1): ?>
                    <button class="carousel-control-prev" type="button" data-bs-target="#videoCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Предыдущее</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#videoCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Следующее</span>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- 6. ИНЖЕНЕРНЫЕ ПРЕИМУЩЕСТВА («ПОЧЕМУ ВЫБИРАЮТ НАС») -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <span class="badge bg-warning text-dark text-uppercase px-3 py-1 fw-bold mb-2">Стандарты надежности</span>
                <h2 class="h3 fw-bold text-dark mb-2">Почему выбирают «Территорию Тепла»</h2>
                <div class="mx-auto" style="width: 60px; height: 3px; background: #FFDF1E;"></div>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card h-100">
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
                    <div class="feature-card h-100">
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
                    <div class="feature-card h-100">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-tools"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Проверенный монтаж</h5>
                        <p class="text-muted small mb-0">
                            Аттестованные мастера-партнеры с опытом от 3 лет. Соблюдение всех пожарных отступок и норм безопасности СНиП.
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card h-100">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-truck"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Доставка по Алтаю</h5>
                        <p class="text-muted small mb-0">
                            Оперативная отгрузка и доставка по Республике Алтай и Алтайскому краю собственным транспортом и надежными ТК.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 7. МОНТАЖНИКИ СЕТИ (ВОССТАНОВЛЕНО ИЗ ОРИГИНАЛА) -->
    <?php if (!empty($installers)): ?>
    <section class="py-5 bg-white border-top border-bottom">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span class="badge bg-warning text-dark text-uppercase small fw-bold mb-1">Проверенные специалисты</span>
                    <h2 class="h3 fw-bold text-dark mb-0">Наши монтажники</h2>
                    <small class="text-muted">Сертифицированные мастера по установке печей, котлов и монтажу дымоходов в Республике Алтай</small>
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
                    $avgRating = round($inst['calc_avg_rating'] ?? $inst['average_rating'] ?? 5);
                ?>
                    <div class="col">
                        <div class="card border-0 shadow-sm rounded-4 h-100 p-4 text-center bg-light" style="transition: transform 0.2s ease;">
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
                            <p class="text-muted small mb-2 text-truncate"><?= htmlspecialchars($inst['specialization'] ?? 'Монтаж отопления') ?></p>
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

    <!-- 8. ОТЗЫВЫ О МАГАЗИНЕ -->
    <?php if (!empty($shop_reviews)): ?>
    <section class="py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span class="badge bg-warning-subtle text-dark border small fw-bold mb-1">Реальный опыт</span>
                    <h2 class="h3 fw-bold text-dark mb-0">Отзывы о магазине</h2>
                    <small class="text-muted">Что говорят покупатели об оборудовании и сервисе</small>
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
                                <?= htmlspecialchars(mb_substr($rev['comment'], 0, 160)) . (mb_strlen($rev['comment']) > 160 ? '...' : '') ?>
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

    <!-- 9. БЫСТРАЯ ЗАЯВКА ИНЖЕНЕРУ КОТЕЛЬНОЙ -->
    <section class="py-5 text-white" style="background: linear-gradient(135deg, #0d0f12 0%, #1c2229 100%); border-top: 3px solid #FFDF1E;">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <span class="badge bg-warning text-dark text-uppercase px-3 py-1 fw-bold mb-3 shadow-sm">
                        Бесплатная консультация
                    </span>
                    <h2 class="display-6 fw-bold text-white mb-2">Остались вопросы по подбору оборудования?</h2>
                    <p class="text-white-50 mb-0">
                        Позвоните на наш склад в с. Майма или оставьте заявку — инженер перезвонит вам в течение 15 минут, уточнит параметры дома и вышлет готовую спецификацию.
                    </p>
                </div>
                <div class="col-lg-5 text-lg-end">
                    <div class="d-flex flex-column flex-sm-row justify-content-lg-end gap-3">
                        <a href="tel:+79136914443" class="btn btn-outline-light btn-lg px-4 py-3 fw-bold rounded-pill">
                            <i class="bi bi-telephone-fill text-warning me-2"></i> +7 (913) 691-44-43
                        </a>
                        <a href="contacts.php" class="btn btn-warning btn-lg px-4 py-3 fw-bold rounded-pill shadow-sm">
                            Написать нам &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
