<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// ==========================================
// 1. ТОВАРЫ (Хиты + Добор до 4)
// ==========================================
$featured_products = [];
if (function_exists('getFeaturedProducts')) {
    $featured_products = getFeaturedProducts(4);
}
// Добор
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

// ==========================================
// 2. АКЦИИ (НОВАЯ ЛОГИКА)
// ==========================================
$promotions = [];
try {
    $stmt = $pdo->query("
        SELECT * FROM promotions 
        WHERE is_active = 1 AND start_date <= NOW() 
        AND (end_date IS NULL OR end_date >= NOW())
        ORDER BY sort_order ASC, created_at DESC 
        LIMIT 3
    ");
    $promotions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $promotions = [];
}

// ==========================================
// 3. ВИДЕО
// ==========================================
$homepage_videos = function_exists('getVideos') ? getVideos(5) : [];

// ==========================================
// 4. МОНТАЖНИКИ
// ==========================================
$installers = [];
try {
    $stmt = $pdo->query("
        SELECT * FROM installers 
        WHERE is_active = 1 
        ORDER BY average_rating DESC, review_count DESC 
        LIMIT 4
    ");
    $installers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $installers = [];
}

// ==========================================
// 5. ОТЗЫВЫ О МАГАЗИНЕ
// ==========================================
$shop_reviews = function_exists('getLatestReviews') ? getLatestReviews(3) : [];


$page_title = 'Главная — Михаил Потапыч';
require_once __DIR__ . '/includes/header.php';
?>

<main>

    <!-- ГЕРОЙ (Градиент) -->
    <section class="bg-gradient-primary py-5 text-center text-lg-start position-relative">
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: url('images/hero-bg.jpg') center/cover; opacity: 0.1;"></div>
        <div class="container py-5 position-relative" style="z-index: 2;">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-3 fw-bold mb-3 text-uppercase text-dark">Тепло и Уют <br><span class="text-white" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">в вашем доме</span></h1>
                    <p class="lead mb-4 text-dark fw-bold">Профессиональное отопительное оборудование, качественная сантехника и надежный монтаж с 1995 года.</p>
                    <div class="d-grid gap-3 d-sm-flex justify-content-sm-center justify-content-lg-start">
                        <a href="catalog.php" class="btn btn-dark btn-lg px-5 fw-bold">В каталог</a>
                        <a href="installers.php" class="btn btn-outline-light btn-lg px-5 fw-bold text-dark border-dark">Найти мастера</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- АКЦИИ И НОВОСТИ (НОВЫЙ БЛОК) -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="section-title-wrapper">
                <h2 class="section-title text-dark">Акции и Новости</h2>
                <a href="promotions.php" class="btn btn-gradient btn-sm px-4 rounded-pill">Все акции &rarr;</a>
            </div>

            <?php if (!empty($promotions)): ?>
                <div class="row g-4">
                    <?php foreach ($promotions as $promo): ?>
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm advantage-card overflow-hidden">
                                <?php if (!empty($promo['image_path'])): ?>
                                    <div style="height: 200px; overflow: hidden;">
                                        <img src="<?= htmlspecialchars($promo['image_path']); ?>" class="card-img-top h-100 w-100 object-fit-cover" alt="<?= htmlspecialchars($promo['title']); ?>">
                                    </div>
                                <?php else: ?>
                                    <div class="bg-gradient-primary text-center d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <i class="bi bi-gift fs-1 text-white"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title fw-bold"><?= htmlspecialchars($promo['title']); ?></h5>
                                    <p class="card-text text-muted small flex-grow-1">
                                        <?= htmlspecialchars(mb_substr($promo['description'], 0, 100)) . '...'; ?>
                                    </p>
                                    <a href="promotions.php#promo-<?= $promo['id']; ?>" class="text-gradient-accent text-decoration-none mt-3">Подробнее &rarr;</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-light border text-center text-muted">
                    <i class="bi bi-info-circle me-2"></i> Активных акций пока нет.
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- ТОВАРЫ -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="section-title-wrapper mb-4 d-flex justify-content-between align-items-center">
                <h2 class="section-title text-dark m-0">Популярные товары</h2>
                <a href="catalog.php" class="text-dark fw-bold text-decoration-none small text-uppercase">Весь каталог &rarr;</a>
            </div>

            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                <?php if (!empty($featured_products)): ?>
                    <?php foreach ($featured_products as $prod): ?>
                        <?php 
                            $discount_percent = 0;
                            if (!empty($prod['old_price']) && $prod['old_price'] > $prod['price']) {
                                $discount_percent = round((($prod['old_price'] - $prod['price']) / $prod['old_price']) * 100);
                            }
                            
                            $img = !empty($prod['image_path']) ? $prod['image_path'] : '';
                            // Убираем ../ если путь был относительным для админки
                            $img = str_replace('../', '', $img);
                            
                            // Если картинки нет, пытаемся найти через функцию (если она есть)
                            if (empty($img) && function_exists('getProductMainImage')) {
                                 $img = getProductMainImage($prod['id']);
                                 $img = str_replace('../', '', $img);
                            }
                            
                            // Заглушка
                            if (empty($img) || !file_exists(__DIR__ . '/' . $img)) {
                                $img = 'images/no-photo.jpg'; 
                            }
                        ?>
                        <div class="col">
                            <div class="card h-100 border-0 shadow-sm text-center product-card position-relative overflow-hidden advantage-card">
                                <?php if ($discount_percent > 0): ?>
                                    <span class="position-absolute top-0 end-0 m-2 badge bg-warning text-dark rounded-pill px-3 py-2 shadow" style="z-index: 5;">
                                        -<?= $discount_percent ?>%
                                    </span>
                                <?php endif; ?>
                                
                                <div class="p-4 d-flex align-items-center justify-content-center bg-white" style="height: 220px;">
                                    <img src="<?= htmlspecialchars($img); ?>" alt="<?= htmlspecialchars($prod['name']); ?>" class="img-fluid" style="max-height: 180px;">
                                </div>
                                
                                <div class="card-body d-flex flex-column pt-3">
                                    <h5 class="card-title fs-6 text-uppercase fw-bold mb-2">
                                        <a href="product.php?id=<?= $prod['id']; ?>" class="text-decoration-none text-dark stretched-link">
                                            <?= htmlspecialchars($prod['name']); ?>
                                        </a>
                                    </h5>
                                    
                                    <div class="mt-auto">
                                        <div class="mb-3">
                                            <?php if ($discount_percent > 0): ?>
                                                <small class="text-muted text-decoration-line-through me-2">
                                                    <?= number_format($prod['old_price'], 0, '.', ' '); ?> ₽
                                                </small>
                                            <?php endif; ?>
                                            <span class="text-warning fw-bold fs-5">
                                                <?= number_format($prod['price'], 0, '.', ' '); ?> ₽
                                            </span>
                                        </div>
                                        
                                        <!-- ВАЖНО: Добавлен класс btn-add-cart для JS -->
                                        <button class="btn btn-dark w-100 position-relative rounded-pill fw-bold btn-add-cart" 
                                                style="z-index: 2;" 
                                                data-product-id="<?= $prod['id']; ?>">
                                            <i class="bi bi-cart-plus me-2"></i> В корзину
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center text-muted py-5">
                        <p>Товары пока не добавлены.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ВИДЕО -->
    <?php if (!empty($homepage_videos)): ?>
    <section class="py-5 bg-dark text-white">
        <div class="container-fluid px-0">
             <div class="container section-title-wrapper" style="border-bottom-color: #333;">
                <h2 class="section-title text-white">Видеообзоры</h2>
                <a href="videos.php" class="text-gradient-accent text-decoration-none">Все видео &rarr;</a>
            </div>

            <div id="videoCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php foreach ($homepage_videos as $index => $video): ?>
                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                            <div class="row justify-content-center mx-0">
                                <div class="col-lg-10 col-xl-9">
                                    <div class="card h-100 border-0 shadow-lg overflow-hidden bg-black rounded-3">
                                        <div class="ratio ratio-16x9">
                                            <iframe src="<?= htmlspecialchars($video['video_url'] ?? $video['url']); ?>" title="<?= htmlspecialchars($video['title']); ?>" allowfullscreen></iframe>
                                        </div>
                                        <div class="card-body text-white p-4 text-center bg-dark border-top border-warning border-2">
                                            <h4 class="card-title fw-bold mb-2 text-gradient-accent"><?= htmlspecialchars($video['title']); ?></h4>
                                            <p class="card-text text-white-50"><?= htmlspecialchars($video['description'] ?? ''); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#videoCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#videoCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </section>
    <?php endif; ?>

     <!-- МОНТАЖНИКИ -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="section-title-wrapper">
                <h2 class="section-title text-dark">Наши Мастера</h2>
                <a href="installers.php" class="text-dark fw-bold text-decoration-none">Все мастера &rarr;</a>
            </div>

            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                <?php if (!empty($installers)): ?>
                    <?php foreach ($installers as $inst): ?>
                        <?php 
                            $photo = !empty($inst['photo_path']) ? $inst['photo_path'] : 'images/default-avatar.png';
                            $reviewsCount = (int)($inst['review_count'] ?? 0);
                            $avgRating = (float)($inst['average_rating'] ?? 0);
                            $displayRating = ($reviewsCount > 0) ? $avgRating : 0;
                        ?>
                        <div class="col">
                            <div class="card h-100 border-0 shadow-sm text-center p-3 advantage-card">
                                <div class="mx-auto mb-3" style="width: 120px; height: 120px;">
                                    <div class="rounded-circle overflow-hidden w-100 h-100 border border-3 border-warning bg-white">
                                        <img src="<?= htmlspecialchars($photo); ?>" 
                                             alt="<?= htmlspecialchars($inst['first_name']); ?>" 
                                             class="w-100 h-100 object-fit-cover">
                                    </div>
                                </div>
                                <h5 class="fw-bold mb-1 text-truncate"><?= htmlspecialchars($inst['first_name'] . ' ' . $inst['last_name']); ?></h5>
                                <p class="text-muted small mb-2 text-truncate"><?= htmlspecialchars($inst['specialization'] ?? 'Мастер'); ?></p>
                                <div class="text-warning small mb-3">
                                    <?php 
                                        $r_round = round($displayRating);
                                        echo str_repeat('<i class="bi bi-star-fill"></i>', $r_round);
                                        echo str_repeat('<i class="bi bi-star"></i>', 5 - $r_round);
                                    ?>
                                </div>
                                <a href="installer.php?id=<?= $inst['id']; ?>" class="btn btn-outline-dark btn-sm w-100 rounded-pill">Профиль</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center text-muted">Мастера не найдены.</div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ПРЕИМУЩЕСТВА (ИСПРАВЛЕННЫЙ БЛОК) -->
    <section class="py-5 bg-white">
        <div class="container">
            <!-- Заголовок секции ("Подпись к нему") -->
            <div class="text-center mb-5">
                <h2 class="fw-bold text-uppercase">Почему выбирают нас</h2>
                <div class="mx-auto mt-2" style="width: 60px; height: 3px; background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);"></div>
            </div>

            <div class="row g-4">
                <div class="col-md-3 d-flex">
                    <div class="card w-100 advantage-card text-center p-4 h-100 border-warning">
                        <div class="card-body d-flex flex-column align-items-center justify-content-center">
                            <i class="bi bi-shield-check advantage-icon text-gradient-accent"></i>
                            <h5 class="fw-bold mb-3">Гарантия качества</h5>
                            <p class="text-muted small mb-0">Мы работаем только с проверенными поставщиками и предоставляем гарантию на всё оборудование.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 d-flex">
                    <div class="card w-100 advantage-card text-center p-4 h-100 border-warning">
                        <div class="card-body d-flex flex-column align-items-center justify-content-center">
                            <i class="bi bi-truck advantage-icon text-gradient-accent"></i>
                            <h5 class="fw-bold mb-3">Быстрая доставка</h5>
                            <p class="text-muted small mb-0">Оперативная доставка по городу и области. Возможен самовывоз со склада.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 d-flex">
                    <div class="card w-100 advantage-card text-center p-4 h-100 border-warning">
                        <div class="card-body d-flex flex-column align-items-center justify-content-center">
                            <i class="bi bi-tools advantage-icon text-gradient-accent"></i>
                            <h5 class="fw-bold mb-3">Профессиональный монтаж</h5>
                            <p class="text-muted small mb-0">Наши сертифицированные специалисты выполнят монтаж любой сложности под ключ.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 d-flex">
                    <div class="card w-100 advantage-card text-center p-4 h-100 border-warning">
                        <div class="card-body d-flex flex-column align-items-center justify-content-center">
                            <i class="bi bi-headset advantage-icon text-gradient-accent"></i>
                            <h5 class="fw-bold mb-3">Поддержка 24/7</h5>
                            <p class="text-muted small mb-0">Квалифицированные консультации и помощь в подборе оборудования в любое время.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
